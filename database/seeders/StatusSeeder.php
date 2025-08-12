<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'present',
                'description' => 'Karyawan hadir tepat waktu'
            ],
            [
                'name' => 'late',
                'description' => 'Karyawan hadir tetapi terlambat'
            ],
            [
                'name' => 'pending',
                'description' => 'Pengajuan izin/sakit menunggu persetujuan admin'
            ],
            [
                'name' => 'approved',
                'description' => 'Pengajuan disetujui admin'
            ],
            [
                'name' => 'rejected',
                'description' => 'Pengajuan izin/sakit ditolak admin'
            ],
            [
                'name' => 'absent',
                'description' => 'Karyawan tidak hadir tanpa keterangan'
            ],
            // Status untuk jenis pengajuan
            [
                'name' => 'sick',
                'description' => 'Sakit dengan surat dokter'
            ],
            [
                'name' => 'excused',
                'description' => 'Izin keperluan pribadi'
            ],
            [
                'name' => 'leave',
                'description' => 'Cuti tahunan'
            ],
            [
                'name' => 'permit',
                'description' => 'Izin khusus/darurat'
            ],
            [
                'name' => 'official',
                'description' => 'Dinas luar kantor'
            ]
        ];

        foreach ($statuses as $status) {
            Status::updateOrCreate(
                ['name' => $status['name']], 
                $status
            );
        }
    }
}
