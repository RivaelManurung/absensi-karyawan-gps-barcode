<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        Division::create(['name' => 'Teknologi Informasi']);
        Division::create(['name' => 'Sumber Daya Manusia']);
        Division::create(['name' => 'Keuangan']);
        Division::create(['name' => 'Pemasaran']);
        Division::create(['name' => 'Operasional']);
    }
}