<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUlids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nip', 'name', 'email', 'phone', 'gender', 'birth_date',
        'birth_place', 'address', 'city', 'education_id', 'division_id',
        'job_title_id', 'password', 'raw_password', 'group',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // RELASI-RELASI PENTING

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

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}