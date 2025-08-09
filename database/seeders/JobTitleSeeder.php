<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobTitle;

class JobTitleSeeder extends Seeder
{
    public function run(): void
    {
        JobTitle::create(['name' => 'Staf']);
        JobTitle::create(['name' => 'Supervisor']);
        JobTitle::create(['name' => 'Manajer']);
        JobTitle::create(['name' => 'Direktur']);
    }
}