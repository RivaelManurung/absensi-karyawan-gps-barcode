<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Barcode;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Menampilkan halaman utama absensi untuk karyawan.
     * Halaman ini akan menampilkan form clock-in/clock-out sesuai kondisi.
     */
    public function index()
    {
        $user = Auth::user();

        // Data untuk status absensi hari ini
        $todayAttendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->first();

        // Data untuk tabel riwayat (kita ambil 5 terakhir)
        $history = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(5); // Ubah angka 5 jika ingin menampilkan lebih banyak

        return view('user.attendances.index', [
            'todayAttendance' => $todayAttendance,
            'history'         => $history
        ]);
    }

    /**
     * Menyimpan data absensi masuk (clock-in) dari form.
     */
    public function storeClockIn(Request $request)
    {
        $request->validate([
            'barcode_value' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();

        // 1. Cek apakah sudah ada absensi untuk hari ini
        if (Attendance::where('user_id', $user->id)->whereDate('date', Carbon::today())->exists()) {
            return back()->with('error', 'Anda sudah tercatat absensi untuk hari ini.');
        }

        // 2. Validasi Barcode
        $barcode = Barcode::where('value', $request->barcode_value)->first();
        if (!$barcode) {
            return back()->with('error', 'Lokasi absensi (barcode) tidak valid.');
        }

        // 3. Validasi Jarak (Geolocation)
        $distance = $this->calculateDistance($request->latitude, $request->longitude, $barcode->latitude, $barcode->longitude);
        if ($distance > $barcode->radius) {
            return back()->with('error', 'Anda berada di luar jangkauan lokasi yang diizinkan (' . round($distance) . ' meter).');
        }

        // 4. Tentukan Shift dan Status
        // Pastikan Anda sudah mengatur relasi 'shift' pada model User
        // Contoh: $user->shift_id diisi saat admin membuat data karyawan
        $shift = $user->shift;
        if (!$shift) {
            // Jika user tidak punya shift, gunakan shift default atau beri error
            // Untuk contoh ini, kita anggap user wajib punya shift
            return back()->with('error', 'Jadwal shift Anda tidak ditemukan. Hubungi admin.');
        }

        $status = (Carbon::now()->format('H:i:s') > $shift->start_time) ? 'late' : 'present';

        // 5. Membuat record absensi
        Attendance::create([
            'user_id' => $user->id,
            'barcode_id' => $barcode->id,
            'shift_id' => $shift->id,
            'date' => Carbon::today(),
            'time_in' => Carbon::now(),
            'status' => $status,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return redirect()->route('user.attendances.index')->with('success', 'Absensi masuk berhasil direkam!');
    }

    /**
     * Mengupdate data untuk absensi pulang (clock-out).
     */
    public function storeClockOut(Request $request)
    {
        $user = Auth::user();

        // Cari data absensi hari ini yang sudah clock-in tapi belum clock-out
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Tidak ada data absensi masuk untuk di-update.');
        }

        $attendance->update([
            'time_out' => Carbon::now(),
        ]);

        return redirect()->route('attendances.index')->with('success', 'Absensi pulang berhasil direkam!');
    }

    /**
     * Menampilkan halaman riwayat absensi milik karyawan yang login.
     */
    public function history()
    {
        $history = Attendance::where('user_id', Auth::id())
            ->with('shift') // Eager load untuk performa
            ->orderBy('date', 'desc')
            ->paginate(15); // Paginasi untuk data yang banyak

        return view('user.attendances.history', ['history' => $history]);
    }

    /**
     * Menampilkan form untuk mengajukan izin.
     */
    public function createRequest()
    {
        return view('user.attendances.request_create');
    }

    /**
     * Menyimpan pengajuan izin dari form.
     */
    public function storeRequest(Request $request)
    {
        $request->validate([
            'status' => 'required|in:excused,sick',
            'note' => 'required|string|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $user = Auth::user();

        if (Attendance::where('user_id', $user->id)->whereDate('date', Carbon::today())->exists()) {
            return back()->with('error', 'Anda sudah tercatat absensi untuk hari ini, tidak bisa mengajukan izin.');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            // Simpan file ke storage/app/public/attachments
            $attachmentPath = $request->file('attachment')->store('attachments', 'public');
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'status' => $request->status,
            'note' => $request->note,
            'attachment' => $attachmentPath,
        ]);

        return redirect()->route('attendances.index')->with('success', 'Pengajuan izin Anda telah berhasil dikirim.');
    }

    /**
     * Fungsi private untuk menghitung jarak.
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371000; // dalam meter
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }
}
