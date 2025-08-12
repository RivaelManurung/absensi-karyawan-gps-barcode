<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $reportType = $request->get('report_type', 'overview');
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $specificDate = $request->get('specific_date', Carbon::now()->format('Y-m-d'));
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $divisionId = $request->get('division_id');
        $userId = $request->get('user_id');
        
        // Get filter data
        $divisions = Division::all();
        $users = User::where('role', 'user')->orderBy('name')->get();
        
        // Calculate statistics
        $totalEmployees = User::where('role', 'user')->count();
        
        $today = Carbon::now()->format('Y-m-d');
        $presentToday = Attendance::whereDate('date', $today)
            ->where('status', 'present')
            ->distinct('user_id')
            ->count();
            
        $onLeaveToday = Attendance::whereDate('date', $today)
            ->whereIn('status', ['sick', 'leave', 'excused'])
            ->distinct('user_id')
            ->count();
        
        $statistics = [
            'total_employees' => $totalEmployees,
            'present_today' => $presentToday,
            'on_leave_today' => $onLeaveToday,
            'absent_today' => $totalEmployees - $presentToday - $onLeaveToday
        ];
        
        // Determine date range based on report type
        switch ($reportType) {
            case 'daily':
                $startDate = $specificDate;
                $endDate = $specificDate;
                break;
            case 'monthly':
                $startDate = Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');
                break;
        }
        
        // Build base query
        $baseQuery = Attendance::with(['user.division', 'user.jobTitle'])
            ->whereBetween('date', [$startDate, $endDate]);
            
        if ($divisionId) {
            $baseQuery->whereHas('user', function($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }
        
        if ($userId) {
            $baseQuery->where('user_id', $userId);
        }
        
        // Initialize variables
        $attendances = collect();
        $daily_reports = [];
        $monthly_reports = [];
        $employee_report = null;
        $division_reports = [];
        $leave_requests = collect();
        $leave_summary = [];
        
        // Get data based on report type
        switch ($reportType) {
            case 'overview':
                $attendances = $baseQuery->orderBy('date', 'desc')
                    ->orderBy('user_id', 'asc')
                    ->paginate(20);
                break;
                
            case 'daily':
                // Group by date for daily reports
                $daily_data = $baseQuery->selectRaw('
                    date,
                    COUNT(DISTINCT user_id) as total_employees,
                    COUNT(CASE WHEN status = "present" THEN 1 END) as present,
                    COUNT(CASE WHEN status = "late" THEN 1 END) as late,
                    COUNT(CASE WHEN status = "absent" THEN 1 END) as absent,
                    COUNT(CASE WHEN status IN ("sick", "leave", "excused") THEN 1 END) as on_leave
                ')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
                
                $daily_reports = $daily_data->toArray();
                break;
                
            case 'monthly':
                // Group by month for monthly reports
                $monthly_data = $baseQuery->selectRaw('
                    DATE_FORMAT(date, "%Y-%m") as month,
                    COUNT(DISTINCT DATE(date)) as working_days,
                    COUNT(CASE WHEN status = "present" THEN 1 END) as present,
                    COUNT(CASE WHEN status = "late" THEN 1 END) as late,
                    COUNT(CASE WHEN status = "absent" THEN 1 END) as absent,
                    COUNT(CASE WHEN status IN ("sick", "leave", "excused") THEN 1 END) as on_leave
                ')
                ->groupBy(DB::raw('DATE_FORMAT(date, "%Y-%m")'))
                ->orderBy('month', 'desc')
                ->get();
                
                $monthly_reports = $monthly_data->toArray();
                break;
                
            case 'employee':
                if ($userId) {
                    $user = User::find($userId);
                    if ($user) {
                        $user_attendances = $baseQuery->where('user_id', $userId)
                            ->orderBy('date', 'desc')
                            ->paginate(20);
                            
                        $user_stats = $baseQuery->where('user_id', $userId)
                            ->selectRaw('
                                COUNT(CASE WHEN status = "present" THEN 1 END) as present,
                                COUNT(CASE WHEN status = "late" THEN 1 END) as late,
                                COUNT(CASE WHEN status = "absent" THEN 1 END) as absent,
                                COUNT(CASE WHEN status IN ("sick", "leave", "excused") THEN 1 END) as on_leave
                            ')
                            ->first();
                            
                        $employee_report = [
                            'user' => $user,
                            'attendances' => $user_attendances,
                            'statistics' => [
                                'present' => $user_stats->present ?? 0,
                                'late' => $user_stats->late ?? 0,
                                'absent' => $user_stats->absent ?? 0,
                                'on_leave' => $user_stats->on_leave ?? 0
                            ]
                        ];
                    }
                }
                break;
                
            case 'division':
                $divisions_with_data = Division::with('users')->get();
                
                foreach ($divisions_with_data as $division) {
                    $division_attendances = Attendance::whereHas('user', function($q) use ($division) {
                        $q->where('division_id', $division->id);
                    })
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get();
                    
                    $division_stats = [
                        'present' => $division_attendances->where('status', 'present')->count(),
                        'late' => $division_attendances->where('status', 'late')->count(),
                        'absent' => $division_attendances->where('status', 'absent')->count(),
                        'on_leave' => $division_attendances->whereIn('status', ['sick', 'leave', 'excused'])->count()
                    ];
                    
                    $employees = [];
                    foreach ($division->users->where('role', 'user') as $user) {
                        $user_attendances = $division_attendances->where('user_id', $user->id);
                        $employees[] = [
                            'user' => $user,
                            'statistics' => [
                                'present' => $user_attendances->where('status', 'present')->count(),
                                'late' => $user_attendances->where('status', 'late')->count(),
                                'absent' => $user_attendances->where('status', 'absent')->count(),
                                'on_leave' => $user_attendances->whereIn('status', ['sick', 'leave', 'excused'])->count()
                            ]
                        ];
                    }
                    
                    $division_reports[] = [
                        'division' => $division,
                        'total_employees' => $division->users->where('role', 'user')->count(),
                        'statistics' => $division_stats,
                        'employees' => $employees
                    ];
                }
                break;
                
            case 'leave-requests':
                $leave_requests = Attendance::with(['user.division', 'user.jobTitle'])
                    ->whereIn('status', ['sick', 'leave', 'excused'])
                    ->whereBetween('date', [$startDate, $endDate]);
                    
                if ($divisionId) {
                    $leave_requests->whereHas('user', function($q) use ($divisionId) {
                        $q->where('division_id', $divisionId);
                    });
                }
                
                if ($userId) {
                    $leave_requests->where('user_id', $userId);
                }
                
                $leave_requests = $leave_requests->orderBy('date', 'desc')->paginate(20);
                
                // Leave summary
                $leave_summary = [
                    'total' => $leave_requests->total(),
                    'pending' => 0, // Implementasi sesuai kebutuhan
                    'approved' => 0, // Implementasi sesuai kebutuhan
                    'rejected' => 0 // Implementasi sesuai kebutuhan
                ];
                break;
        }
        
        return view('Admin.reports.index', compact(
            'statistics', 'attendances', 'daily_reports', 'monthly_reports', 
            'employee_report', 'division_reports', 'leave_requests', 'leave_summary',
            'divisions', 'users', 'reportType', 'startDate', 'endDate'
        ));
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $reportType = $request->get('report_type', 'overview');
        $divisionId = $request->get('division_id');
        $userId = $request->get('user_id');
        
        // Determine date range based on type
        switch ($reportType) {
            case 'daily':
                $specificDate = $request->get('specific_date', Carbon::now()->format('Y-m-d'));
                $startDate = $specificDate;
                $endDate = $specificDate;
                $filename = "attendance_daily_{$specificDate}";
                break;
            case 'monthly':
                $month = $request->get('month', Carbon::now()->format('Y-m'));
                $startDate = Carbon::parse($month . '-01')->startOfMonth()->format('Y-m-d');
                $endDate = Carbon::parse($month . '-01')->endOfMonth()->format('Y-m-d');
                $filename = "attendance_monthly_{$month}";
                break;
            case 'leave-requests':
                $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
                $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
                $filename = "leave_requests_{$startDate}_to_{$endDate}";
                break;
            default:
                $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
                $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
                $filename = "attendance_report_{$startDate}_to_{$endDate}";
                break;
        }
        
        // Add division info to filename if filtered
        if ($divisionId) {
            $division = Division::find($divisionId);
            if ($division) {
                $filename .= "_" . str_replace(' ', '_', strtolower($division->name));
            }
        }
        
        // Build query based on report type
        if ($reportType === 'leave-requests') {
            $query = Attendance::with(['user.division', 'user.jobTitle'])
                ->whereIn('status', ['sick', 'leave', 'excused'])
                ->whereBetween('date', [$startDate, $endDate]);
        } else {
            $query = Attendance::with(['user.division', 'user.jobTitle'])
                ->whereBetween('date', [$startDate, $endDate]);
        }
            
        if ($divisionId) {
            $query->whereHas('user', function($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('user_id', 'asc')
            ->get();
        
        if ($format == 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
            ];
            
            $callback = function() use ($attendances, $reportType) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for proper UTF-8 encoding in Excel
                fputs($file, "\xEF\xBB\xBF");
                
                // Add header based on report type
                if ($reportType === 'leave-requests') {
                    fputcsv($file, [
                        'Tanggal',
                        'Nama Karyawan',
                        'Divisi',
                        'Jabatan',
                        'Status',
                        'Keterangan'
                    ]);
                } else {
                    fputcsv($file, [
                        'Tanggal',
                        'Nama Karyawan',
                        'Divisi',
                        'Jabatan',
                        'Status',
                        'Jam Masuk',
                        'Jam Keluar',
                        'Keterangan'
                    ]);
                }
                
                foreach ($attendances as $attendance) {
                    if ($reportType === 'leave-requests') {
                        fputcsv($file, [
                            Carbon::parse($attendance->date)->format('d/m/Y'),
                            $attendance->user->name ?? '',
                            $attendance->user->division->name ?? '',
                            $attendance->user->jobTitle->name ?? '',
                            ucfirst($attendance->status),
                            $attendance->notes ?? ''
                        ]);
                    } else {
                        fputcsv($file, [
                            Carbon::parse($attendance->date)->format('d/m/Y'),
                            $attendance->user->name ?? '',
                            $attendance->user->division->name ?? '',
                            $attendance->user->jobTitle->name ?? '',
                            ucfirst($attendance->status),
                            $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : '',
                            $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '',
                            $attendance->notes ?? ''
                        ]);
                    }
                }
                
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }
        
        // For Excel format (placeholder)
        return redirect()->back()->with('error', 'Format Excel belum tersedia');
    }

    public function getUsersByDivision($divisionId)
    {
        $users = User::where('division_id', $divisionId)
            ->where('role', 'user')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
            
        return response()->json($users);
    }
}
