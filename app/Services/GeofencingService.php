<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Barcode;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GeofencingService
{
    /**
     * Validate if user is within allowed geofence
     */
    public function validateGeofence($latitude, $longitude, $barcodeId, $userId)
    {
        $barcode = Barcode::findOrFail($barcodeId);
        $user = User::findOrFail($userId);
        
        $distance = $this->calculateDistance(
            $latitude, 
            $longitude, 
            $barcode->latitude, 
            $barcode->longitude
        );
        
        $result = [
            'valid' => false,
            'distance' => $distance,
            'max_distance' => $barcode->radius,
            'message' => '',
            'barcode' => $barcode,
            'user' => $user
        ];
        
        if ($distance > $barcode->radius) {
            $result['message'] = "Anda berada {$distance}m dari lokasi. Maksimal: {$barcode->radius}m";
            return $result;
        }
        
        // Check time validity
        if ($barcode->valid_from && $barcode->valid_until) {
            $currentTime = Carbon::now()->format('H:i:s');
            if ($currentTime < $barcode->valid_from || $currentTime > $barcode->valid_until) {
                $result['message'] = "Lokasi tidak aktif pada waktu ini ({$barcode->valid_from} - {$barcode->valid_until})";
                return $result;
            }
        }
        
        // Check if barcode is active
        if (!$barcode->is_active) {
            $result['message'] = "Lokasi sedang tidak aktif";
            return $result;
        }
        
        $result['valid'] = true;
        $result['message'] = "Lokasi valid";
        return $result;
    }
    
    /**
     * Calculate distance between two points using Haversine formula
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters
        
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
     * Track user movement and detect suspicious activity
     */
    public function trackMovement($userId, $latitude, $longitude, $accuracy, $timestamp = null)
    {
        $timestamp = $timestamp ?? Carbon::now();
        
        // Store movement data
        DB::table('user_movements')->insert([
            'user_id' => $userId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
            'recorded_at' => $timestamp,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Detect suspicious patterns
        $suspiciousActivity = $this->detectSuspiciousActivity($userId, $latitude, $longitude);
        
        if ($suspiciousActivity) {
            Log::warning('Suspicious movement detected', [
                'user_id' => $userId,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'activity' => $suspiciousActivity
            ]);
        }
        
        return $suspiciousActivity;
    }
    
    /**
     * Detect suspicious movement patterns
     */
    private function detectSuspiciousActivity($userId, $latitude, $longitude)
    {
        $recentMovements = DB::table('user_movements')
            ->where('user_id', $userId)
            ->where('recorded_at', '>=', Carbon::now()->subMinutes(10))
            ->orderBy('recorded_at', 'desc')
            ->limit(5)
            ->get();
            
        if ($recentMovements->count() < 2) {
            return null;
        }
        
        $activities = [];
        
        // Check for rapid movement (more than 100km/h)
        $lastMovement = $recentMovements->first();
        $distance = $this->calculateDistance(
            $lastMovement->latitude,
            $lastMovement->longitude,
            $latitude,
            $longitude
        );
        
        $timeDiff = Carbon::parse($lastMovement->recorded_at)->diffInMinutes(Carbon::now());
        if ($timeDiff > 0) {
            $speed = ($distance / 1000) / ($timeDiff / 60); // km/h
            if ($speed > 100) {
                $activities[] = [
                    'type' => 'rapid_movement',
                    'speed' => $speed,
                    'message' => "Pergerakan terlalu cepat: {$speed} km/h"
                ];
            }
        }
        
        // Check for location jumping (teleportation)
        if ($distance > 5000 && $timeDiff < 5) { // 5km in less than 5 minutes
            $activities[] = [
                'type' => 'location_jump',
                'distance' => $distance,
                'time' => $timeDiff,
                'message' => "Perpindahan lokasi mencurigakan: {$distance}m dalam {$timeDiff} menit"
            ];
        }
        
        return empty($activities) ? null : $activities;
    }
    
    /**
     * Find nearest valid locations
     */
    public function findNearestLocations($latitude, $longitude, $maxRadius = 5000)
    {
        $barcodes = Barcode::where('is_active', true)->get();
        $nearestLocations = [];
        
        foreach ($barcodes as $barcode) {
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                $barcode->latitude,
                $barcode->longitude
            );
            
            if ($distance <= $maxRadius) {
                $nearestLocations[] = [
                    'barcode' => $barcode,
                    'distance' => $distance,
                    'can_checkin' => $distance <= $barcode->radius
                ];
            }
        }
        
        // Sort by distance
        usort($nearestLocations, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        return $nearestLocations;
    }
    
    /**
     * Generate attendance analytics
     */
    public function generateLocationAnalytics($barcodeId, $startDate, $endDate)
    {
        $attendances = Attendance::where('barcode_id', $barcodeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with('user')
            ->get();
            
        $analytics = [
            'total_checkins' => $attendances->whereNotNull('check_in_latitude')->count(),
            'total_checkouts' => $attendances->whereNotNull('check_out_latitude')->count(),
            'average_distance' => $attendances->whereNotNull('check_in_distance')->avg('check_in_distance'),
            'max_distance' => $attendances->whereNotNull('check_in_distance')->max('check_in_distance'),
            'accuracy_stats' => [
                'avg_checkin_accuracy' => $attendances->whereNotNull('check_in_accuracy')->avg('check_in_accuracy'),
                'avg_checkout_accuracy' => $attendances->whereNotNull('check_out_accuracy')->avg('check_out_accuracy'),
            ],
            'hourly_distribution' => $this->getHourlyDistribution($attendances),
            'user_frequency' => $this->getUserFrequency($attendances)
        ];
        
        return $analytics;
    }
    
    private function getHourlyDistribution($attendances)
    {
        $hourly = [];
        for ($i = 0; $i < 24; $i++) {
            $hourly[$i] = 0;
        }
        
        foreach ($attendances as $attendance) {
            if ($attendance->time_in) {
                $hour = Carbon::parse($attendance->time_in)->hour;
                $hourly[$hour]++;
            }
        }
        
        return $hourly;
    }
    
    private function getUserFrequency($attendances)
    {
        $frequency = [];
        foreach ($attendances as $attendance) {
            $userId = $attendance->user_id;
            if (!isset($frequency[$userId])) {
                $frequency[$userId] = [
                    'user' => $attendance->user,
                    'count' => 0
                ];
            }
            $frequency[$userId]['count']++;
        }
        
        return array_values($frequency);
    }
}
