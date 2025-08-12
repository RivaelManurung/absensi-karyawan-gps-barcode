<?php

namespace App\Http\Controllers;

use App\Models\Barcode;
use App\Models\Attendance;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BarcodeValidationController extends Controller
{
    /**
     * Validasi akurasi lokasi dan barcode untuk absensi
     */
    public function validateLocation(Request $request)
    {
        $request->validate([
            'barcode_data' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric',
            'type' => 'required|in:check_in,check_out'
        ]);

        $barcode = Barcode::where('data', $request->barcode_data)->first();
        
        if (!$barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak valid atau tidak terdaftar'
            ], 400);
        }

        // Validasi jarak dari lokasi barcode
        $distance = $this->calculateDistance(
            $request->latitude, 
            $request->longitude,
            $barcode->latitude,
            $barcode->longitude
        );

        // Toleransi jarak dalam meter (dapat dikonfigurasi)
        $maxDistance = $barcode->radius ?? 50; // default 50 meter
        
        if ($distance > $maxDistance) {
            return response()->json([
                'success' => false,
                'message' => "Anda berada {$distance}m dari lokasi absensi. Maksimal jarak: {$maxDistance}m",
                'distance' => $distance,
                'max_distance' => $maxDistance
            ], 400);
        }

        // Validasi akurasi GPS
        $accuracy = $request->accuracy ?? 100;
        if ($accuracy > 20) { // Akurasi GPS lebih dari 20 meter dianggap tidak akurat
            return response()->json([
                'success' => false,
                'message' => "Akurasi GPS tidak mencukupi ({$accuracy}m). Pastikan GPS aktif dan sinyal kuat.",
                'accuracy' => $accuracy
            ], 400);
        }

        // Validasi waktu absensi
        $timeValidation = $this->validateAttendanceTime($barcode, $request->type);
        if (!$timeValidation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $timeValidation['message']
            ], 400);
        }

        // Proses absensi
        return $this->processAttendance($barcode, $request, $distance, $accuracy);
    }

    /**
     * Proses absensi setelah validasi berhasil
     */
    private function processAttendance($barcode, $request, $distance, $accuracy)
    {
        $user = Auth::user();
        $today = Carbon::today();
        
        // Cek apakah sudah ada attendance hari ini
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($request->type === 'check_in') {
            if ($attendance && $attendance->time_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check-in hari ini'
                ], 400);
            }

            // Tentukan status berdasarkan waktu
            $status = $this->determineStatus($barcode);
            
            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->user_id = $user->id;
                $attendance->date = $today;
                $attendance->status_id = $status->id;
            }

            $attendance->time_in = now();
            $attendance->barcode_id = $barcode->id;
            $attendance->check_in_latitude = $request->latitude;
            $attendance->check_in_longitude = $request->longitude;
            $attendance->check_in_accuracy = $accuracy;
            $attendance->check_in_distance = $distance;
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Check-in berhasil',
                'data' => [
                    'time' => $attendance->time_in->format('H:i:s'),
                    'status' => $status->name,
                    'location' => $barcode->name,
                    'distance' => $distance,
                    'accuracy' => $accuracy
                ]
            ]);

        } else { // check_out
            if (!$attendance || !$attendance->time_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan check-in hari ini'
                ], 400);
            }

            if ($attendance->time_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan check-out hari ini'
                ], 400);
            }

            $attendance->time_out = now();
            $attendance->check_out_latitude = $request->latitude;
            $attendance->check_out_longitude = $request->longitude;
            $attendance->check_out_accuracy = $accuracy;
            $attendance->check_out_distance = $distance;
            
            // Hitung durasi kerja
            $workDuration = Carbon::parse($attendance->time_in)->diffInMinutes($attendance->time_out);
            $attendance->work_duration = $workDuration;
            
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Check-out berhasil',
                'data' => [
                    'time' => $attendance->time_out->format('H:i:s'),
                    'work_duration' => $this->formatDuration($workDuration),
                    'location' => $barcode->name,
                    'distance' => $distance,
                    'accuracy' => $accuracy
                ]
            ]);
        }
    }

    /**
     * Hitung jarak antara dua koordinat (dalam meter)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meter

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Validasi waktu absensi
     */
    private function validateAttendanceTime($barcode, $type)
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');

        if ($type === 'check_in') {
            // Validasi tidak terlalu pagi (sebelum jam 5 pagi)
            if ($now->hour < 5) {
                return [
                    'valid' => false,
                    'message' => 'Check-in tidak diizinkan sebelum jam 05:00'
                ];
            }

            // Validasi tidak terlalu malam untuk check-in (setelah jam 12 siang kecuali shift malam)
            if ($now->hour > 12 && !$this->isNightShift($barcode)) {
                return [
                    'valid' => false,
                    'message' => 'Check-in terlalu siang. Hubungi admin jika Anda memiliki shift khusus.'
                ];
            }
        } else { // check_out
            // Validasi minimal bekerja 1 jam
            $user = Auth::user();
            $attendance = Attendance::where('user_id', $user->id)
                ->whereDate('date', Carbon::today())
                ->first();

            if ($attendance && $attendance->time_in) {
                $workDuration = Carbon::parse($attendance->time_in)->diffInMinutes($now);
                if ($workDuration < 60) { // kurang dari 1 jam
                    return [
                        'valid' => false,
                        'message' => 'Minimal bekerja 1 jam sebelum check-out'
                    ];
                }
            }
        }

        return ['valid' => true];
    }

    /**
     * Tentukan status berdasarkan waktu check-in
     */
    private function determineStatus($barcode)
    {
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');

        // Logika penentuan status bisa disesuaikan dengan shift
        // Untuk sementara, kita gunakan logika sederhana:
        // - Sebelum jam 9:00 = present
        // - Setelah jam 9:00 = late

        if ($currentTime <= '09:00:00') {
            return Status::where('name', 'present')->first();
        } else {
            return Status::where('name', 'late')->first();
        }
    }

    /**
     * Cek apakah ini shift malam
     */
    private function isNightShift($barcode)
    {
        // Implementasi logika shift malam
        // Untuk sementara return false
        return false;
    }

    /**
     * Format durasi kerja
     */
    private function formatDuration($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        return sprintf('%d jam %d menit', $hours, $mins);
    }

    /**
     * Get location info untuk mobile app
     */
    public function getLocationInfo(Request $request)
    {
        $request->validate([
            'barcode_data' => 'required|string'
        ]);

        $barcode = Barcode::where('data', $request->barcode_data)->first();
        
        if (!$barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak valid'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $barcode->name,
                'description' => $barcode->description,
                'latitude' => $barcode->latitude,
                'longitude' => $barcode->longitude,
                'radius' => $barcode->radius ?? 50,
                'address' => $barcode->address,
                'is_active' => $barcode->is_active
            ]
        ]);
    }

    /**
     * Validasi multiple barcode untuk area besar
     */
    public function validateMultipleLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'accuracy' => 'nullable|numeric'
        ]);

        // Cari semua barcode aktif dalam radius tertentu
        $barcodes = Barcode::where('is_active', true)->get();
        $validBarcodes = [];

        foreach ($barcodes as $barcode) {
            $distance = $this->calculateDistance(
                $request->latitude,
                $request->longitude,
                $barcode->latitude,
                $barcode->longitude
            );

            $maxDistance = $barcode->radius ?? 50;
            if ($distance <= $maxDistance) {
                $validBarcodes[] = [
                    'id' => $barcode->id,
                    'name' => $barcode->name,
                    'data' => $barcode->data,
                    'distance' => $distance,
                    'max_distance' => $maxDistance
                ];
            }
        }

        if (empty($validBarcodes)) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berada di lokasi absensi yang valid'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lokasi valid ditemukan',
            'valid_locations' => $validBarcodes
        ]);
    }
}
