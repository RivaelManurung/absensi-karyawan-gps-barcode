<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMovement extends Model
{
    protected $fillable = [
        'user_id',
        'latitude',
        'longitude',
        'accuracy',
        'activity_type',
        'recorded_at',
        'additional_data'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'recorded_at' => 'datetime',
        'additional_data' => 'array'
    ];

    /**
     * Get the user that owns the movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get movements for a specific activity type.
     */
    public function scopeActivityType($query, $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope to get movements within date range.
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('recorded_at', [$start, $end]);
    }

    /**
     * Scope to get movements for specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get movement distance from a specific point.
     */
    public function getDistanceFromPoint($latitude, $longitude)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $latFrom = deg2rad((float) $this->latitude);
        $lonFrom = deg2rad((float) $this->longitude);
        $latTo = deg2rad((float) $latitude);
        $lonTo = deg2rad((float) $longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
