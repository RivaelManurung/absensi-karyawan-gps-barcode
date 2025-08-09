<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUlids;

    /** WAJIB ADA: Tipe primary key adalah string karena menggunakan ULID. */
    protected $keyType = 'string';

    /** WAJIB ADA: Primary key tidak auto-incrementing. */
    public $incrementing = false;

    protected $fillable = [
        'nip',
        'name',
        'email',
        'phone',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'city',
        'education_id',
        'division_id',
        'job_title_id',
        'shift_id',
        'password',
        'raw_password',
        'group',
        'profile_photo_path',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- ACCESSOR UNTUK CEK GRUP ---
    protected function isAdmin(): Attribute
    {
        return Attribute::make(get: fn() => in_array($this->group, ['admin', 'superadmin']));
    }

    protected function isUser(): Attribute
    {
        return Attribute::make(get: fn() => $this->group === 'user');
    }

    // --- RELASI ---
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }
    public function education(): BelongsTo
    {
        return $this->belongsTo(Education::class);
    }
    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
