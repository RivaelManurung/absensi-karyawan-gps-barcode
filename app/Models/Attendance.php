<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendances'; // tambahkan ini

    protected $guarded = ['id'];

    /**
     * Setiap absensi dimiliki oleh satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Setiap absensi terhubung dengan satu Barcode.
     */
    public function barcode(): BelongsTo
    {
        return $this->belongsTo(Barcode::class);
    }

    /**
     * Setiap absensi memiliki satu Shift.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Setiap absensi memiliki satu Status.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * User yang meng-approve attendance ini.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * User yang me-reject attendance ini.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Cek apakah sudah di-approve
     */
    public function isApproved(): bool
    {
        return !is_null($this->approved_at);
    }

    /**
     * Cek apakah sudah di-reject
     */
    public function isRejected(): bool
    {
        return !is_null($this->rejected_at);
    }

    /**
     * Cek apakah masih pending
     */
    public function isPending(): bool
    {
        return is_null($this->approved_at) && is_null($this->rejected_at);
    }

    /**
     * Get status approval
     */
    public function getApprovalStatusAttribute(): string
    {
        if ($this->isApproved()) {
            return 'approved';
        } elseif ($this->isRejected()) {
            return 'rejected';
        } else {
            return 'pending';
        }
    }
}