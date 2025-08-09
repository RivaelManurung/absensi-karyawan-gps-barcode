<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barcode extends Model
{
    use HasFactory;
    protected $table = 'barcodes'; // tambahkan ini

    protected $guarded = ['id'];

    /**
     * Satu barcode bisa digunakan untuk banyak absensi.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}