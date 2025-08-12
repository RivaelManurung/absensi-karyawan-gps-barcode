<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceLog extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_id',
        'action',
        'latitude',
        'longitude',
        'accuracy',
        'is_successful',
        'failure_reason',
        'device_info'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'is_successful' => 'boolean',
        'device_info' => 'array'
    ];

    /**
     * Get the user that owns the log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attendance record associated with this log.
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * Scope to get successful actions.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('is_successful', true);
    }

    /**
     * Scope to get failed actions.
     */
    public function scopeFailed($query)
    {
        return $query->where('is_successful', false);
    }

    /**
     * Scope to get logs by action type.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to get logs within date range.
     */
    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('created_at', [$start, $end]);
    }

    /**
     * Scope to get logs for specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get action label for display.
     */
    public function getActionLabelAttribute()
    {
        return match($this->action) {
            'checkin_attempt' => 'Percobaan Check-in',
            'checkout_attempt' => 'Percobaan Check-out',
            'validation_failed' => 'Validasi Gagal',
            'location_verified' => 'Lokasi Terverifikasi',
            'barcode_scanned' => 'Barcode Dipindai',
            default => 'Aktivitas Tidak Diketahui'
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute()
    {
        return $this->is_successful ? 'success' : 'danger';
    }

    /**
     * Get device type from device info.
     */
    public function getDeviceTypeAttribute()
    {
        if (!$this->device_info) {
            return 'Unknown';
        }

        $userAgent = $this->device_info['user_agent'] ?? '';
        
        if (str_contains($userAgent, 'Mobile') || str_contains($userAgent, 'Android') || str_contains($userAgent, 'iPhone')) {
            return 'Mobile';
        }
        
        return 'Desktop';
    }
}
