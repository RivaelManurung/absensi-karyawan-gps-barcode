<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder untuk data master terlebih dahulu
        // Urutan ini tidak terlalu berpengaruh satu sama lain
        $this->call([
            DivisionSeeder::class,
            EducationSeeder::class,
            JobTitleSeeder::class,
            ShiftSeeder::class,
            BarcodeSeeder::class,
            StatusSeeder::class, // Tambah StatusSeeder
        ]);

        // Panggil UserSeeder SETELAH data master terisi
        // karena UserSeeder membutuhkan data dari seeder di atas
        $this->call(UserSeeder::class);
    }
}