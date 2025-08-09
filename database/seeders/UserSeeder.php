<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Superadmin
        User::create([
            'nip' => 'SA001',
            'name' => 'Super Administrator',
            'email' => 'superadmin@example.com',
            'phone' => '081234567890',
            'gender' => 'male',
            'address' => 'Jl. Merdeka No. 1',
            'city' => 'Pematangsiantar',
            'education_id' => 3, // S1
            'division_id' => 1, // TI
            'job_title_id' => 4, // Direktur
            'password' => Hash::make('password'), // password default: password
            'group' => 'superadmin',
            'email_verified_at' => Carbon::now(), // ✅ email terverifikasi
        ]);

        // 2. Admin
        User::create([
            'nip' => 'A001',
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'phone' => '081234567891',
            'gender' => 'female',
            'address' => 'Jl. Sutomo No. 10',
            'city' => 'Pematangsiantar',
            'education_id' => 3, // S1
            'division_id' => 2, // HRD
            'job_title_id' => 3, // Manajer
            'password' => Hash::make('password'), // password default: password
            'group' => 'admin',
            'email_verified_at' => Carbon::now(), // ✅ email terverifikasi
        ]);

        // 3. Karyawan Biasa
        User::create([
            'nip' => 'K001',
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@example.com',
            'phone' => '081234567892',
            'gender' => 'male',
            'address' => 'Jl. Viyata Yudha No. 5',
            'city' => 'Pematangsiantar',
            'education_id' => 1, // SMA
            'division_id' => 1, // TI
            'job_title_id' => 1, // Staf
            'password' => Hash::make('password'), // password default: password
            'group' => 'user',
            'email_verified_at' => Carbon::now(), // ✅ email terverifikasi
        ]);

    }
}
