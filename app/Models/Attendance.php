<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;
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
}