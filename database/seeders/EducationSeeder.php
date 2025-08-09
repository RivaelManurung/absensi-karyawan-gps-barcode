<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Education;

class EducationSeeder extends Seeder
{
    public function run(): void
    {
        Education::create(['name' => 'SMA/SMK Sederajat']);
        Education::create(['name' => 'Diploma (D3)']);
        Education::create(['name' => 'Sarjana (S1)']);
        Education::create(['name' => 'Magister (S2)']);
    }
}