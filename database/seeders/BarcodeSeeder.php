<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Barcode;

class BarcodeSeeder extends Seeder
{
    public function run(): void
    {
        Barcode::create([
            'name' => 'Kantor Pusat Pematangsiantar',
            'value' => 'KANTOR_PUSAT_SIANTAR_01',
            'latitude' => 2.9698, // Koordinat Pematangsiantar
            'longitude' => 99.0645,
            'radius' => 50, // dalam meter
        ]);
        Barcode::create([
            'name' => 'Gudang Cabang',
            'value' => 'GUDANG_CABANG_01',
            'latitude' => 2.9750,
            'longitude' => 99.0700,
            'radius' => 100,
        ]);
    }
}