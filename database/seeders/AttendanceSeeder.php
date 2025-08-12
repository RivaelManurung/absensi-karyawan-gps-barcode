<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Status;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        $users = User::where('group', 'employee')->get();
        $statuses = Status::all();
        
        $this->command->info('Users found: ' . $users->count());
        $this->command->info('Statuses found: ' . $statuses->count());
        
        if ($users->isEmpty()) {
            // Try to get any users
            $users = User::all();
            $this->command->info('All users found: ' . $users->count());
        }
        
        if ($users->isEmpty() || $statuses->isEmpty()) {
            $this->command->info('No users or statuses found. Please run UserSeeder and create statuses first.');
            return;
        }
        
        // Create attendance records for last 30 days
        $startDate = Carbon::now()->subDays(30);
        $endDate = Carbon::now();
        
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            foreach ($users as $user) {
                // Skip weekends (optional)
                if ($date->isWeekend()) {
                    continue;
                }
                
                // Random chance to have attendance (90% chance)
                if (rand(1, 100) <= 90) {
                    $randomStatus = $statuses->random();
                    
                    $timeIn = null;
                    $timeOut = null;
                    
                    if (in_array($randomStatus->name, ['present', 'late'])) {
                        // Generate realistic time in/out times
                        $baseTimeIn = '08:00:00'; // 8 AM base time
                        
                        if ($randomStatus->name === 'late') {
                            // Late: 8:15 - 9:30 AM
                            $timeIn = date('H:i:s', strtotime($baseTimeIn . ' +' . rand(15, 90) . ' minutes'));
                        } else {
                            // On time: 7:45 - 8:15 AM
                            $minutes = rand(-15, 15);
                            $timeIn = date('H:i:s', strtotime($baseTimeIn . ' ' . ($minutes >= 0 ? '+' : '') . $minutes . ' minutes'));
                        }
                        
                        // Time out: 5:00 - 6:30 PM
                        $timeOut = date('H:i:s', strtotime('17:00:00 +' . rand(0, 90) . ' minutes'));
                    }
                    
                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => $date->format('Y-m-d'),
                        'status_id' => $randomStatus->id,
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                        'note' => $this->getRandomNote($randomStatus->name),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        
        $this->command->info('Attendance records created successfully!');
    }
    
    private function getRandomNote($statusName)
    {
        $notes = [
            'present' => ['Hadir tepat waktu', 'Bekerja dengan baik', null],
            'late' => ['Terlambat karena macet', 'Bangun kesiangan', 'Kendala transportasi'],
            'absent' => ['Sakit', 'Keperluan keluarga', 'Tidak ada kabar'],
            'sick' => ['Demam', 'Flu', 'Sakit perut', 'Pusing'],
            'excused' => ['Izin keperluan keluarga', 'Urusan penting', 'Acara keluarga'],
        ];
        
        $statusNotes = $notes[$statusName] ?? [null];
        return $statusNotes[array_rand($statusNotes)];
    }
}
