<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LocationViolation extends Model
{
    protected $fillable = [
        'user_id',
        'violation_type',
        'latitude',
        'longitude',
        'distance_from_location',
        'accuracy',
        'severity',
        'description',
        'metadata',
        'is_resolved',
        'resolved_at'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'distance_from_location' => 'decimal:2',
        'accuracy' => 'decimal:2',
        'metadata' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime'
    ];

    /**
     * Get the user that owns the violation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get unresolved violations.
     */
    public function scopeUnresolved($query)
    {
        return $query->where('is_resolved', false);
    }

    /**
     * Scope to get violations by severity.
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope to get violations by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('violation_type', $type);
    }

    /**
     * Scope to get violations within date range.
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Mark violation as resolved.
     */
    public function markAsResolved()
    {
        $this->update([
            'is_resolved' => true,
            'resolved_at' => now()
        ]);
    }

    /**
     * Get severity color for UI display.
     */
    public function getSeverityColorAttribute()
    {
        return match($this->severity) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'dark',
            default => 'secondary'
        };
    }

    /**
     * Get violation type label.
     */
    public function getTypeLabel()
    {
        return match($this->violation_type) {
            'distance_exceeded' => 'Jarak Terlampaui',
            'suspicious_movement' => 'Pergerakan Mencurigakan',
            'accuracy_low' => 'Akurasi GPS Rendah',
            'time_violation' => 'Pelanggaran Waktu',
            default => 'Tidak Diketahui'
        };
    }
}
