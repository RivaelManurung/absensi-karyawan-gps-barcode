<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Education extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    /**
     * Satu jenjang pendidikan bisa dimiliki oleh banyak user.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}