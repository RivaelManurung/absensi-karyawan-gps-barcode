<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Barcode;
use App\Models\Status;
use Carbon\Carbon;
use App\Http\Controllers\Traits\CalculatesDistance;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    use CalculatesDistance;

    /**
     * Menampilkan halaman utama absensi dengan semua data untuk tab.
     */
    public function index()
    {
        $user = Auth::user();

        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->with('status')
            ->first();

        $history = Attendance::where('user_id', $user->id)
            ->with(['shift', 'status'])
            ->orderBy('date', 'desc')
            ->paginate(10); // Menampilkan 10 riwayat per halaman

        // Ambil semua status yang bisa dipilih untuk pengajuan
        $availableStatuses = Status::whereIn('name', ['sick', 'excused', 'leave', 'permit', 'official'])->get();

        return view('user.attendances.index', [ // Pastikan path view ini benar
            'todayAttendance' => $todayAttendance,
            'history'         => $history,
            'availableStatuses' => $availableStatuses
        ]);
    }

    /**
     * Menyimpan data absensi masuk (clock-in) dari form.
     */
    public function storeClockIn(Request $request)
    {
        $request->validate([
            'barcode_value' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $user = Auth::user();

        if (Attendance::where('user_id', $user->id)->whereDate('date', Carbon::today())->exists()) {
            return back()->with('error', 'Anda sudah tercatat absensi untuk hari ini.');
        }

        $barcode = Barcode::where('value', $request->barcode_value)->first();
        if (!$barcode) {
            return back()->with('error', 'Lokasi absensi (barcode) tidak valid.');
        }

        try {
            $distance = $this->calculateOptimalDistance(
                $request->latitude,
                $request->longitude,
                $barcode->latitude,
                $barcode->longitude
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($distance > $barcode->radius) {
            return back()->with('error', 'Anda berada di luar jangkauan lokasi (' . round($distance) . ' meter).');
        }

        $shift = $user->shift;
        if (!$shift) {
            return back()->with('error', 'Jadwal shift Anda tidak ditemukan. Hubungi admin.');
        }

        $status = (Carbon::now()->format('H:i:s') > $shift->start_time) ? 'late' : 'present';
        
        // Dapatkan status_id berdasarkan nama status
        $statusRecord = Status::where('name', $status)->first();

        Attendance::create([
            'user_id' => $user->id,
            'barcode_id' => $barcode->id,
            'shift_id' => $shift->id,
            'date' => Carbon::today(),
            'time_in' => Carbon::now(),
            'status_id' => $statusRecord->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('attendances.index')->with('success', 'Absensi masuk berhasil direkam! Jarak: ' . round($distance) . ' meter');
    }

    /**
     * Mengupdate data untuk absensi pulang (clock-out).
     */
    public function storeClockOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $user = Auth::user();

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Tidak ada data absensi masuk untuk di-update.');
        }

        // Validasi ulang lokasi saat clock-out
        $barcode = $attendance->barcode;
        if ($barcode) {
            try {
                $distance = $this->calculateOptimalDistance(
                    $request->latitude,
                    $request->longitude,
                    $barcode->latitude,
                    $barcode->longitude
                );

                if ($distance > $barcode->radius) {
                    return back()->with('error', 'Anda berada di luar jangkauan untuk clock-out (' . round($distance) . ' meter).');
                }
            } catch (\InvalidArgumentException $e) {
                return back()->with('error', 'Koordinat tidak valid saat clock-out.');
            }
        }

        $attendance->update(['time_out' => Carbon::now()]);

        return redirect()->route('attendances.index')->with('success', 'Absensi pulang berhasil direkam!');
    }

    /**
     * Menyimpan pengajuan izin dari form.
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'status_id' => 'required|exists:statuses,id',
            'note' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $user = Auth::user();

        if (Attendance::where('user_id', $user->id)->whereDate('date', Carbon::today())->exists()) {
            return back()->with('error', 'Anda sudah tercatat absensi untuk hari ini, tidak bisa mengajukan izin.');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        // Dapatkan status pending untuk pengajuan baru
        $pendingStatus = Status::where('name', 'pending')->first();
        
        // Jika tidak ada status pending, buat error
        if (!$pendingStatus) {
            return back()->with('error', 'Status pending tidak ditemukan. Hubungi administrator.');
        }

        // Simpan status yang diminta sebagai request_type 
        $requestedStatus = Status::find($request->status_id);

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status_id' => $pendingStatus->id, // Status awal pending
            'request_type' => $requestedStatus->name, // Simpan jenis pengajuan yang diminta
            'note' => $request->note,
            'attachment' => $attachmentPath,
        ]);
        
        return redirect()->route('attendances.index')
            ->with('success', 'Pengajuan izin ' . $requestedStatus->name . ' Anda telah berhasil dikirim dan menunggu persetujuan admin.')
            ->with('active_tab', 'request');
    }
}
