<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Status extends Model
{
    protected $fillable = [
        'name',
        'description'
    ];

    protected $casts = [
        // Hapus cast yang tidak perlu
    ];

    /**
     * Mendapatkan semua attendance dengan status ini
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
