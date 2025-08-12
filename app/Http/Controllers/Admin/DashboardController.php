<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Division;
use App\Models\JobTitle;
use App\Models\Shift;
use App\Models\Attendance;
use App\Models\Status;
use App\Models\Barcode;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data ringkasan.
     */
    public function index(Request $request)
    {
        $currentMonth = Carbon::now()->format('Y-m');
        $selectedMonth = $request->get('month', $currentMonth);
        
        // Enhanced Statistics
        $stats = [
            'total_employees' => User::where('group', 'user')->count(),
            'total_locations' => Barcode::count(),
            'active_locations' => Barcode::where('is_active', true)->count(),
            'total_divisions' => Division::count(),
            'total_job_titles' => JobTitle::count(),
            'total_shifts' => Shift::count(),
        ];
        
        // Today's Real-time Attendance
        $todayAttendance = $this->getTodayAttendanceStats();
        
        // Monthly Attendance Trends
        $monthlyTrends = $this->getMonthlyAttendanceTrends($selectedMonth);
        
        // Division Performance
        $divisionPerformance = $this->getDivisionPerformance($selectedMonth);
        
        // Recent Activities
        $recentActivities = $this->getRecentActivities();
        
        // Location Usage Analytics
        $locationAnalytics = $this->getLocationAnalytics($selectedMonth);
        
        // Attendance Patterns
        $attendancePatterns = $this->getAttendancePatterns($selectedMonth);
        
        // Alert Summary
        $alerts = $this->getAlertSummary();
        
        // Legacy data for compatibility
        $employeeCount = $stats['total_employees'];
        $divisionCount = $stats['total_divisions'];
        $jobTitleCount = $stats['total_job_titles'];
        $shiftCount = $stats['total_shifts'];
        
        $attendanceChartData = [
            'labels' => ['Hadir', 'Terlambat', 'Tidak Hadir', 'Sakit/Izin'],
            'series' => [
                $todayAttendance['present'],
                $todayAttendance['late'],
                $todayAttendance['absent'],
                $todayAttendance['on_leave']
            ]
        ];
        
        $recentLeaveRequests = Attendance::with(['user.division', 'status']) 
            ->whereHas('status', function($q) {
                $q->whereIn('name', ['pending', 'excused', 'sick']);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact( 
            'employeeCount',
            'divisionCount',
            'jobTitleCount',
            'shiftCount',
            'attendanceChartData',
            'recentLeaveRequests',
            'stats',
            'todayAttendance',
            'monthlyTrends',
            'divisionPerformance',
            'recentActivities',
            'locationAnalytics',
            'attendancePatterns',
            'alerts',
            'selectedMonth'
        ));
    }
    
    private function getTodayAttendanceStats()
    {
        $today = Carbon::today();
        
        $stats = [
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'on_leave' => 0,
            'pending_requests' => 0
        ];
        
        $attendances = Attendance::whereDate('date', $today)
            ->with('status')
            ->get();
            
        foreach ($attendances as $attendance) {
            $statusName = $attendance->status->name ?? 'unknown';
            
            switch ($statusName) {
                case 'present':
                    $stats['present']++;
                    break;
                case 'late':
                    $stats['late']++;
                    break;
                case 'absent':
                    $stats['absent']++;
                    break;
                case 'sick':
                case 'excused':
                    $stats['on_leave']++;
                    break;
                case 'pending':
                    $stats['pending_requests']++;
                    break;
            }
        }
        
        $totalEmployees = User::where('group', 'user')->count();
        $stats['not_recorded'] = $totalEmployees - array_sum(array_slice($stats, 0, 4));
        
        return $stats;
    }
    
    private function getMonthlyAttendanceTrends($month)
    {
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();
        
        $dailyStats = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dateStr = $current->format('Y-m-d');
            
            $dayStats = Attendance::whereDate('date', $current)
                ->join('statuses', 'attendances.status_id', '=', 'statuses.id')
                ->select('statuses.name', DB::raw('count(*) as count'))
                ->groupBy('statuses.name')
                ->pluck('count', 'name')
                ->toArray();
                
            $dailyStats[] = [
                'date' => $dateStr,
                'day' => $current->format('d'),
                'day_name' => $current->format('D'),
                'present' => $dayStats['present'] ?? 0,
                'late' => $dayStats['late'] ?? 0,
                'absent' => $dayStats['absent'] ?? 0,
                'sick' => $dayStats['sick'] ?? 0,
                'excused' => $dayStats['excused'] ?? 0,
            ];
            
            $current->addDay();
        }
        
        return $dailyStats;
    }
    
    private function getDivisionPerformance($month)
    {
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();
        
        $divisions = Division::with(['users' => function($query) {
            $query->where('group', 'user');
        }])->get();
        
        $performance = [];
        
        foreach ($divisions as $division) {
            $employeeIds = $division->users->pluck('id');
            
            if ($employeeIds->isEmpty()) {
                continue;
            }
            
            $attendances = Attendance::whereIn('user_id', $employeeIds)
                ->whereBetween('date', [$startDate, $endDate])
                ->with('status')
                ->get();
                
            $stats = [
                'division' => $division->name,
                'total_employees' => $employeeIds->count(),
                'present' => 0,
                'late' => 0,
                'absent' => 0,
                'on_leave' => 0,
                'total_attendances' => $attendances->count()
            ];
            
            foreach ($attendances as $attendance) {
                $statusName = $attendance->status->name ?? 'unknown';
                
                switch ($statusName) {
                    case 'present':
                        $stats['present']++;
                        break;
                    case 'late':
                        $stats['late']++;
                        break;
                    case 'absent':
                        $stats['absent']++;
                        break;
                    case 'sick':
                    case 'excused':
                        $stats['on_leave']++;
                        break;
                }
            }
            
            $stats['attendance_rate'] = $stats['total_attendances'] > 0 
                ? round(($stats['present'] / $stats['total_attendances']) * 100, 1)
                : 0;
                
            $performance[] = $stats;
        }
        
        // Sort by attendance rate
        usort($performance, function($a, $b) {
            return $b['attendance_rate'] <=> $a['attendance_rate'];
        });
        
        return $performance;
    }
    
    private function getRecentActivities()
    {
        return Attendance::with(['user', 'status'])
            ->whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($attendance) {
                return [
                    'id' => $attendance->id,
                    'user_name' => $attendance->user->name,
                    'action' => $this->getActionDescription($attendance),
                    'status' => $attendance->status->name ?? 'unknown',
                    'date' => $attendance->date,
                    'time' => $attendance->created_at,
                    'time_formatted' => $attendance->created_at->diffForHumans()
                ];
            });
    }
    
    private function getActionDescription($attendance)
    {
        if ($attendance->time_in && $attendance->time_out) {
            return 'Selesai absensi';
        } elseif ($attendance->time_in) {
            return 'Check in';
        } elseif ($attendance->note) {
            return 'Pengajuan izin';
        }
        return 'Aktivitas tidak diketahui';
    }
    
    private function getLocationAnalytics($month)
    {
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();
        
        $locations = Barcode::withCount(['attendances' => function($query) use ($startDate, $endDate) {
            $query->whereBetween('date', [$startDate, $endDate]);
        }])->get();
        
        $analytics = [];
        
        foreach ($locations as $location) {
            $checkins = Attendance::where('barcode_id', $location->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('time_in')
                ->count();
                
            $checkouts = Attendance::where('barcode_id', $location->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('time_out')
                ->count();
                
            $uniqueUsers = Attendance::where('barcode_id', $location->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->distinct('user_id')
                ->count();
            
            $stats = [
                'name' => $location->name,
                'total_checkins' => $checkins,
                'total_checkouts' => $checkouts,
                'unique_users' => $uniqueUsers,
                'is_active' => $location->is_active ?? true
            ];
            
            $analytics[] = $stats;
        }
        
        return $analytics;
    }
    
    private function getAttendancePatterns($month)
    {
        $startDate = Carbon::parse($month . '-01')->startOfMonth();
        $endDate = Carbon::parse($month . '-01')->endOfMonth();
        
        // Hourly check-in pattern
        $hourlyPattern = Attendance::whereBetween('date', [$startDate, $endDate])
            ->whereNotNull('time_in')
            ->selectRaw('HOUR(time_in) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour')
            ->map(function($item) {
                return $item->count;
            });
            
        // Fill missing hours with 0
        $hourlyData = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyData[$i] = $hourlyPattern[$i] ?? 0;
        }
        
        return [
            'hourly' => $hourlyData
        ];
    }
    
    private function getAlertSummary()
    {
        $alerts = [
            'high_priority' => 0,
            'medium_priority' => 0,
            'low_priority' => 0,
            'total' => 0
        ];
        
        // Check for pending leave requests
        $pendingRequests = Attendance::whereHas('status', function($query) {
            $query->where('name', 'pending');
        })->count();
        
        if ($pendingRequests > 0) {
            $alerts['medium_priority'] += $pendingRequests;
        }
        
        // Check for inactive locations
        $inactiveLocations = Barcode::where('is_active', false)->count();
        if ($inactiveLocations > 0) {
            $alerts['low_priority'] += $inactiveLocations;
        }
        
        $alerts['total'] = array_sum(array_slice($alerts, 0, 3));
        
        return $alerts;
    }
}