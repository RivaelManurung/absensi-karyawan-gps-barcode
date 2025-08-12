<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Barcode;
use App\Models\Status;
use App\Models\AttendanceLog;
use App\Models\LocationViolation;
use Carbon\Carbon;
use App\Http\Controllers\Traits\CalculatesDistance;
use App\Http\Controllers\Controller;
use App\Services\GeofencingService;
use Illuminate\Support\Facades\Log;

class AttendanceController extends Controller
{
    use CalculatesDistance;

    protected $geofencingService;

    public function __construct(GeofencingService $geofencingService)
    {
        $this->geofencingService = $geofencingService;
    }

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
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $accuracy = $request->accuracy ?? 0;

        // Log attempt
        $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy);

        // Check if already checked in today
        if (Attendance::where('user_id', $user->id)->whereDate('date', Carbon::today())->exists()) {
            $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy, false, 'Sudah absen hari ini');
            return back()->with('error', 'Anda sudah tercatat absensi untuk hari ini.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy));
        }

        // Validate barcode
        $barcode = Barcode::where('value', $request->barcode_value)->first();
        if (!$barcode) {
            $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy, false, 'Barcode tidak valid');
            return back()->with('error', 'Lokasi absensi (barcode) tidak valid.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy))
                ->with('barcode_error', 'Barcode: ' . $request->barcode_value);
        }

        // Check if location is active
        if (!$barcode->is_active) {
            $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy, false, 'Lokasi tidak aktif');
            return back()->with('error', 'Lokasi absensi ini sedang tidak aktif.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy))
                ->with('barcode_info', 'Lokasi: ' . $barcode->name);
        }

        // Validate GPS accuracy (lebih toleran untuk kondisi Indonesia)
        if ($accuracy > 200) {
            $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy, false, 'Akurasi GPS sangat rendah');
            $this->createLocationViolation($user->id, 'accuracy_low', $latitude, $longitude, null, $accuracy, 'high', 
                'Akurasi GPS sangat rendah: ' . $accuracy . ' meter');
            
            return back()->with('error', 'Akurasi GPS Anda terlalu rendah (' . round($accuracy) . ' meter). Harap coba lagi di tempat terbuka.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy))
                ->with('barcode_info', 'Lokasi Target: ' . $barcode->name);
        }

        // Warning untuk akurasi sedang (50-200 meter)
        if ($accuracy > 50 && $accuracy <= 200) {
            $warningMsg = 'Peringatan: Akurasi GPS Anda ' . round($accuracy) . ' meter. Untuk hasil terbaik, pastikan Anda di area terbuka.';
            session()->flash('warning', $warningMsg);
        }

        // Calculate distance using enhanced geofencing service
        try {
            $validation = $this->geofencingService->validateGeofence(
                $latitude, 
                $longitude, 
                $barcode->id, 
                $user->id
            );

            if (!$validation['valid']) {
                $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy, false, $validation['message']);
                
                // Create violation record
                $this->createLocationViolation(
                    $user->id, 
                    'distance_exceeded', 
                    $latitude, 
                    $longitude, 
                    $validation['distance'], 
                    $accuracy, 
                    $validation['distance'] > 100 ? 'high' : 'medium',
                    $validation['message']
                );

                $errorMsg = 'Anda berada di luar jangkauan lokasi absensi. ';
                $errorMsg .= 'Jarak Anda: ' . round($validation['distance']) . ' meter dari lokasi target. ';
                $errorMsg .= 'Maksimal jarak: ' . $validation['max_distance'] . ' meter.';

                return back()->with('error', $errorMsg)
                    ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy))
                    ->with('barcode_info', 'Lokasi Target: ' . $barcode->name)
                    ->with('distance_info', 'Jarak: ' . round($validation['distance']) . 'm (Max: ' . $validation['max_distance'] . 'm)');
            }

            $distance = $validation['distance'];

        } catch (\Exception $e) {
            Log::error('Geofencing validation error', [
                'user_id' => $user->id,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'barcode_id' => $barcode->id,
                'error' => $e->getMessage()
            ]);

            $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy, false, 'Error validasi lokasi');
            
            return back()->with('error', 'Terjadi kesalahan saat memvalidasi lokasi. Silakan coba lagi.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy))
                ->with('technical_error', 'Error: ' . $e->getMessage());
        }

        // Check shift
        $shift = $user->shift;
        if (!$shift) {
            $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy, false, 'Shift tidak ditemukan');
            return back()->with('error', 'Jadwal shift Anda tidak ditemukan. Hubungi admin.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy));
        }

        // Determine status
        $currentTime = Carbon::now();
        $status = ($currentTime->format('H:i:s') > $shift->start_time) ? 'late' : 'present';
        
        // Get status record
        $statusRecord = Status::where('name', $status)->first();
        if (!$statusRecord) {
            $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy, false, 'Status tidak ditemukan');
            return back()->with('error', 'Status absensi tidak ditemukan. Hubungi admin.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy));
        }

        // Create attendance record
        try {
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'barcode_id' => $barcode->id,
                'shift_id' => $shift->id,
                'date' => Carbon::today(),
                'time_in' => $currentTime,
                'status_id' => $statusRecord->id,
                'check_in_latitude' => $latitude,
                'check_in_longitude' => $longitude,
                'check_in_accuracy' => $accuracy,
                'check_in_distance' => $distance,
            ]);

            // Log successful attempt
            $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy, true, 'Berhasil check-in', $attendance->id);

            $successMsg = 'Absensi masuk berhasil direkam! ';
            $successMsg .= 'Status: ' . ucfirst($status) . '. ';
            $successMsg .= 'Waktu: ' . $currentTime->format('H:i:s') . '. ';
            $successMsg .= 'Lokasi terverifikasi dengan jarak ' . round($distance) . ' meter dari titik absensi.';

            return redirect()->route('attendances.index')
                ->with('success', $successMsg)
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy))
                ->with('attendance_details', [
                    'location' => $barcode->name,
                    'distance' => round($distance) . ' meter',
                    'accuracy' => round($accuracy) . ' meter',
                    'status' => ucfirst($status),
                    'time' => $currentTime->format('H:i:s')
                ]);

        } catch (\Exception $e) {
            Log::error('Failed to create attendance record', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);

            $this->logAttendanceAttempt($user->id, 'checkin_attempt', $latitude, $longitude, $accuracy, false, 'Gagal menyimpan data');
            
            return back()->with('error', 'Gagal menyimpan data absensi. Silakan coba lagi.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy))
                ->with('technical_error', 'Database Error: ' . $e->getMessage());
        }
    }

    /**
     * Mengupdate data untuk absensi pulang (clock-out).
     */
    public function storeClockOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $accuracy = $request->accuracy ?? 0;

        // Log checkout attempt
        $this->logAttendanceAttempt($user->id, 'checkout_attempt', $latitude, $longitude, $accuracy);

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->whereNotNull('time_in')
            ->whereNull('time_out')
            ->first();

        if (!$attendance) {
            $this->logAttendanceAttempt($user->id, 'checkout_attempt', $latitude, $longitude, $accuracy, false, 'Tidak ada data check-in');
            return back()->with('error', 'Tidak ada data absensi masuk untuk di-update.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy));
        }

        // Validate GPS accuracy for checkout (lebih toleran)
        if ($accuracy > 200) {
            $this->logAttendanceAttempt($user->id, 'checkout_attempt', $latitude, $longitude, $accuracy, false, 'Akurasi GPS sangat rendah saat checkout');
            $this->createLocationViolation($user->id, 'accuracy_low', $latitude, $longitude, null, $accuracy, 'medium', 
                'Akurasi GPS sangat rendah saat checkout: ' . $accuracy . ' meter');
            
            return back()->with('warning', 'Akurasi GPS Anda sangat rendah (' . round($accuracy) . ' meter), namun checkout tetap diproses.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy));
        }

        // Warning untuk akurasi sedang saat checkout
        if ($accuracy > 50 && $accuracy <= 200) {
            session()->flash('info', 'Checkout dengan akurasi GPS ' . round($accuracy) . ' meter');
        }

        // Validasi lokasi saat clock-out
        $barcode = $attendance->barcode;
        $distance = null;
        
        if ($barcode) {
            try {
                $validation = $this->geofencingService->validateGeofence(
                    $latitude, 
                    $longitude, 
                    $barcode->id, 
                    $user->id
                );

                $distance = $validation['distance'];

                if (!$validation['valid']) {
                    $this->logAttendanceAttempt($user->id, 'checkout_attempt', $latitude, $longitude, $accuracy, false, $validation['message']);
                    
                    // Create violation but still allow checkout
                    $this->createLocationViolation(
                        $user->id, 
                        'distance_exceeded', 
                        $latitude, 
                        $longitude, 
                        $distance, 
                        $accuracy, 
                        'medium',
                        'Checkout di luar radius: ' . $validation['message']
                    );

                    $warningMsg = 'Anda berada di luar jangkauan lokasi saat checkout (' . round($distance) . ' meter), ';
                    $warningMsg .= 'namun absensi pulang tetap diproses untuk menghindari lembur tidak perlu.';

                    // Still process checkout but with warning
                } else {
                    // Valid location
                }

            } catch (\Exception $e) {
                Log::error('Checkout location validation error', [
                    'user_id' => $user->id,
                    'attendance_id' => $attendance->id,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'error' => $e->getMessage()
                ]);

                $this->logAttendanceAttempt($user->id, 'checkout_attempt', $latitude, $longitude, $accuracy, false, 'Error validasi lokasi checkout');
                
                // Continue with checkout despite error
                $warningMsg = 'Terjadi kesalahan saat memvalidasi lokasi checkout, namun absensi pulang tetap diproses.';
            }
        }

        // Update attendance record
        try {
            $currentTime = Carbon::now();
            $timeIn = Carbon::parse($attendance->time_in);
            $workDuration = $currentTime->diffInMinutes($timeIn);

            $attendance->update([
                'time_out' => $currentTime,
                'check_out_latitude' => $latitude,
                'check_out_longitude' => $longitude,
                'check_out_accuracy' => $accuracy,
                'check_out_distance' => $distance,
                'work_duration' => $workDuration,
            ]);

            // Log successful checkout
            $this->logAttendanceAttempt($user->id, 'checkout_attempt', $latitude, $longitude, $accuracy, true, 'Berhasil checkout', $attendance->id);

            $successMsg = 'Absensi pulang berhasil direkam! ';
            $successMsg .= 'Waktu checkout: ' . $currentTime->format('H:i:s') . '. ';
            $successMsg .= 'Durasi kerja: ' . floor($workDuration / 60) . ' jam ' . ($workDuration % 60) . ' menit.';
            
            if ($distance) {
                $successMsg .= ' Jarak dari titik absensi: ' . round($distance) . ' meter.';
            }

            $response = redirect()->route('attendances.index')->with('success', $successMsg)
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy))
                ->with('checkout_details', [
                    'time' => $currentTime->format('H:i:s'),
                    'duration' => floor($workDuration / 60) . 'j ' . ($workDuration % 60) . 'm',
                    'distance' => $distance ? round($distance) . ' meter' : 'Tidak diketahui',
                    'accuracy' => round($accuracy) . ' meter'
                ]);

            // Add warning if there was a location issue
            if (isset($warningMsg)) {
                $response = $response->with('warning', $warningMsg);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Failed to update checkout', [
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'error' => $e->getMessage()
            ]);

            $this->logAttendanceAttempt($user->id, 'checkout_attempt', $latitude, $longitude, $accuracy, false, 'Gagal update checkout');
            
            return back()->with('error', 'Gagal menyimpan data checkout. Silakan coba lagi.')
                ->with('location_info', $this->getLocationInfo($latitude, $longitude, $accuracy))
                ->with('technical_error', 'Database Error: ' . $e->getMessage());
        }
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

    /**
     * Log attendance attempt for tracking and debugging
     */
    private function logAttendanceAttempt($userId, $action, $latitude, $longitude, $accuracy, $isSuccessful = true, $failureReason = null, $attendanceId = null)
    {
        try {
            AttendanceLog::create([
                'user_id' => $userId,
                'attendance_id' => $attendanceId,
                'action' => $action,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'accuracy' => $accuracy,
                'is_successful' => $isSuccessful,
                'failure_reason' => $failureReason,
                'device_info' => [
                    'user_agent' => request()->header('User-Agent'),
                    'ip_address' => request()->ip(),
                    'timestamp' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log attendance attempt', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create location violation record
     */
    private function createLocationViolation($userId, $violationType, $latitude, $longitude, $distance, $accuracy, $severity, $description)
    {
        try {
            LocationViolation::create([
                'user_id' => $userId,
                'violation_type' => $violationType,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'distance_from_location' => $distance,
                'accuracy' => $accuracy,
                'severity' => $severity,
                'description' => $description,
                'metadata' => [
                    'timestamp' => now()->toISOString(),
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->header('User-Agent')
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create location violation', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get formatted location information
     */
    private function getLocationInfo($latitude, $longitude, $accuracy)
    {
        return [
            'coordinates' => round($latitude, 6) . ', ' . round($longitude, 6),
            'accuracy' => round($accuracy) . ' meter',
            'timestamp' => now()->format('H:i:s'),
            'quality' => $this->getGPSQuality($accuracy)
        ];
    }

    /**
     * Determine GPS quality based on accuracy (Disesuaikan untuk kondisi Indonesia)
     */
    private function getGPSQuality($accuracy)
    {
        if ($accuracy <= 10) return 'Sangat Baik';
        if ($accuracy <= 30) return 'Baik';
        if ($accuracy <= 100) return 'Cukup';
        if ($accuracy <= 200) return 'Kurang Baik';
        return 'Buruk';
    }
}
