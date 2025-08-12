<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Status;
use Carbon\Carbon;

class LeaveRequestSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        $pendingStatus = Status::where('name', 'pending')->first();
        $sickStatus = Status::where('name', 'sick')->first();
        $excusedStatus = Status::where('name', 'excused')->first();

        // Create some leave requests
        $leaveRequests = [
            [
                'user_id' => $users[0]->id,
                'date' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'status_id' => $pendingStatus->id,
                'request_type' => 'sick',
                'note' => 'Demam tinggi, tidak bisa masuk kerja',
                'time_in' => null,
                'time_out' => null,
            ],
            [
                'user_id' => $users[1]->id,
                'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
                'status_id' => $pendingStatus->id,
                'request_type' => 'excused',
                'note' => 'Ada keperluan keluarga mendesak',
                'time_in' => null,
                'time_out' => null,
            ],
            [
                'user_id' => $users[2]->id,
                'date' => Carbon::now()->format('Y-m-d'),
                'status_id' => $sickStatus->id,
                'request_type' => 'sick',
                'note' => 'Sakit maag, sudah ada surat dokter',
                'approved_at' => Carbon::now()->subHours(2),
                'approved_by' => $users[0]->id,
                'time_in' => null,
                'time_out' => null,
            ],
        ];

        foreach ($leaveRequests as $request) {
            Attendance::create($request);
        }

        echo 'Leave requests created successfully!' . PHP_EOL;
    }
}
