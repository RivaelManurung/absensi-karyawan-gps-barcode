<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        Shift::create([
            'name' => 'Shift Pagi',
            'start_time' => '08:00:00',
            'end_time' => '17:00:00',
        ]);
        Shift::create([
            'name' => 'Shift Malam',
            'start_time' => '20:00:00',
            'end_time' => '04:00:00',
        ]);
    }
}