<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barcode extends Model
{
    use HasFactory;
    protected $table = 'barcodes';

    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'radius' => 'float',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    /**
     * Satu barcode bisa digunakan untuk banyak absensi.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $lat1Rad = deg2rad($lat1);
        $lat2Rad = deg2rad($lat2);
        $deltaLatRad = deg2rad($lat2 - $lat1);
        $deltaLonRad = deg2rad($lon2 - $lon1);

        $a = sin($deltaLatRad / 2) * sin($deltaLatRad / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLonRad / 2) * sin($deltaLonRad / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in meters
    }

    /**
     * Check if user location is within allowed radius
     */
    public function isWithinRadius($userLatitude, $userLongitude)
    {
        if (!$this->latitude || !$this->longitude) {
            return false;
        }

        $distance = $this->calculateDistance(
            $this->latitude,
            $this->longitude,
            $userLatitude,
            $userLongitude
        );

        return $distance <= ($this->radius ?? 100); // Default radius 100 meters
    }

    /**
     * Get accuracy percentage based on distance
     */
    public function getAccuracyPercentage($userLatitude, $userLongitude)
    {
        if (!$this->latitude || !$this->longitude) {
            return 0;
        }

        $distance = $this->calculateDistance(
            $this->latitude,
            $this->longitude,
            $userLatitude,
            $userLongitude
        );

        $maxRadius = $this->radius ?? 100;
        
        if ($distance > $maxRadius) {
            return 0;
        }

        // Calculate accuracy percentage (closer = higher accuracy)
        $accuracy = max(0, 100 - (($distance / $maxRadius) * 100));
        return round($accuracy, 2);
    }

    /**
     * Scope for active barcodes only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}