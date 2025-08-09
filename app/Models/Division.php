<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;
    protected $table = 'divisions'; // tambahkan ini

    protected $guarded = ['id'];

    /**
     * Satu divisi bisa memiliki banyak user.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}