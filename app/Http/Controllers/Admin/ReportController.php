<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\User;
use App\Exports\AttendanceReportExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $users = User::where('group', 'user')->orderBy('name')->get();
        
        // Calculate statistics
        $totalEmployees = User::where('group', 'user')->count();
        
        $today = Carbon::now()->format('Y-m-d');
        
        // Get present today (status dengan nama 'present')
        $presentToday = Attendance::whereDate('date', $today)
            ->whereHas('status', function($q) {
                $q->where('name', 'present');
            })
            ->distinct('user_id')
            ->count();
            
        // Get on leave today (status dengan nama 'sick', 'leave', 'excused')
        $onLeaveToday = Attendance::whereDate('date', $today)
            ->whereHas('status', function($q) {
                $q->whereIn('name', ['sick', 'leave', 'excused']);
            })
            ->distinct('user_id')
            ->count();
        
        $statistics = [
            'total_employees' => $totalEmployees,
            'present_today' => $presentToday,
            'on_leave_today' => $onLeaveToday,
            'absent_today' => max(0, $totalEmployees - $presentToday - $onLeaveToday)
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
        $baseQuery = Attendance::with(['user.division', 'user.jobTitle', 'status'])
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
                $daily_data = $baseQuery->join('statuses', 'attendances.status_id', '=', 'statuses.id')
                    ->selectRaw('
                        date,
                        COUNT(DISTINCT user_id) as total_employees,
                        COUNT(CASE WHEN statuses.name = "present" THEN 1 END) as present,
                        COUNT(CASE WHEN statuses.name = "late" THEN 1 END) as late,
                        COUNT(CASE WHEN statuses.name = "absent" THEN 1 END) as absent,
                        COUNT(CASE WHEN statuses.name IN ("sick", "leave", "excused") THEN 1 END) as on_leave
                    ')
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get();
                
                $daily_reports = $daily_data->toArray();
                break;
                
            case 'monthly':
                // Group by month for monthly reports
                $monthly_data = $baseQuery->join('statuses', 'attendances.status_id', '=', 'statuses.id')
                    ->selectRaw('
                        DATE_FORMAT(date, "%Y-%m") as month,
                        COUNT(DISTINCT DATE(date)) as working_days,
                        COUNT(CASE WHEN statuses.name = "present" THEN 1 END) as present,
                        COUNT(CASE WHEN statuses.name = "late" THEN 1 END) as late,
                        COUNT(CASE WHEN statuses.name = "absent" THEN 1 END) as absent,
                        COUNT(CASE WHEN statuses.name IN ("sick", "leave", "excused") THEN 1 END) as on_leave
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
                            ->join('statuses', 'attendances.status_id', '=', 'statuses.id')
                            ->selectRaw('
                                COUNT(CASE WHEN statuses.name = "present" THEN 1 END) as present,
                                COUNT(CASE WHEN statuses.name = "late" THEN 1 END) as late,
                                COUNT(CASE WHEN statuses.name = "absent" THEN 1 END) as absent,
                                COUNT(CASE WHEN statuses.name IN ("sick", "leave", "excused") THEN 1 END) as on_leave
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
                    $division_attendances = Attendance::with('status')
                        ->whereHas('user', function($q) use ($division) {
                            $q->where('division_id', $division->id);
                        })
                        ->whereBetween('date', [$startDate, $endDate])
                        ->get();
                    
                    $division_stats = [
                        'present' => $division_attendances->filter(function($att) {
                            return $att->status && $att->status->name == 'present';
                        })->count(),
                        'late' => $division_attendances->filter(function($att) {
                            return $att->status && $att->status->name == 'late';
                        })->count(),
                        'absent' => $division_attendances->filter(function($att) {
                            return $att->status && $att->status->name == 'absent';
                        })->count(),
                        'on_leave' => $division_attendances->filter(function($att) {
                            return $att->status && in_array($att->status->name, ['sick', 'leave', 'excused']);
                        })->count()
                    ];
                    
                    $employees = [];
                    foreach ($division->users->where('group', 'user') as $user) {
                        $user_attendances = $division_attendances->where('user_id', $user->id);
                        $employees[] = [
                            'user' => $user,
                            'statistics' => [
                                'present' => $user_attendances->filter(function($att) {
                                    return $att->status && $att->status->name == 'present';
                                })->count(),
                                'late' => $user_attendances->filter(function($att) {
                                    return $att->status && $att->status->name == 'late';
                                })->count(),
                                'absent' => $user_attendances->filter(function($att) {
                                    return $att->status && $att->status->name == 'absent';
                                })->count(),
                                'on_leave' => $user_attendances->filter(function($att) {
                                    return $att->status && in_array($att->status->name, ['sick', 'leave', 'excused']);
                                })->count()
                            ]
                        ];
                    }
                    
                    $division_reports[] = [
                        'division' => $division,
                        'total_employees' => $division->users->where('group', 'user')->count(),
                        'statistics' => $division_stats,
                        'employees' => $employees
                    ];
                }
                break;
                
            case 'leave-requests':
                $leave_requests = Attendance::with(['user.division', 'user.jobTitle', 'status'])
                    ->whereHas('status', function($q) {
                        $q->whereIn('name', ['sick', 'leave', 'excused']);
                    })
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
        $format = $request->get('format', 'excel');
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
            $query = Attendance::with(['user.division', 'user.jobTitle', 'status'])
                ->whereHas('status', function($q) {
                    $q->whereIn('name', ['sick', 'leave', 'excused']);
                })
                ->whereBetween('date', [$startDate, $endDate]);
        } else {
            $query = Attendance::with(['user.division', 'user.jobTitle', 'status'])
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
        
        // Handle Excel export
        if ($format == 'excel') {
            return Excel::download(
                new AttendanceReportExport($attendances, $reportType), 
                $filename . '.xlsx'
            );
        }
        
        // Handle PDF export
        if ($format == 'pdf') {
            // Get statistics
            $statistics = $this->getStatistics();
            
            // Prepare data for PDF
            $data = [
                'attendances' => $attendances,
                'reportType' => $reportType,
                'reportTypeLabel' => $this->getReportTypeLabel($reportType),
                'title' => 'Laporan Absensi Karyawan',
                'period' => Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y'),
                'selectedDivision' => $divisionId ? Division::find($divisionId)->name : null,
                'selectedEmployee' => $userId ? User::find($userId)->name : null,
                'statistics' => $statistics
            ];

            $pdf = Pdf::loadView('Admin.reports.pdf.attendance-report', $data);
            $pdf->setPaper('A4', 'landscape');
            
            return $pdf->download($filename . '.pdf');
        }
        
        return redirect()->back()->with('error', 'Format tidak valid');
    }

    private function getStatistics()
    {
        $totalEmployees = User::where('group', 'user')->count();
        
        $today = Carbon::now()->format('Y-m-d');
        
        // Get present today
        $presentToday = Attendance::whereDate('date', $today)
            ->whereHas('status', function($q) {
                $q->where('name', 'present');
            })
            ->distinct('user_id')
            ->count();
            
        // Get on leave today
        $onLeaveToday = Attendance::whereDate('date', $today)
            ->whereHas('status', function($q) {
                $q->whereIn('name', ['sick', 'leave', 'excused']);
            })
            ->distinct('user_id')
            ->count();
        
        return [
            'total_employees' => $totalEmployees,
            'present_today' => $presentToday,
            'on_leave_today' => $onLeaveToday,
            'absent_today' => max(0, $totalEmployees - $presentToday - $onLeaveToday)
        ];
    }

    private function getReportTypeLabel($reportType)
    {
        $labels = [
            'overview' => 'Ringkasan Keseluruhan',
            'daily' => 'Laporan Harian',
            'monthly' => 'Laporan Bulanan',
            'employee' => 'Laporan Per Karyawan',
            'division' => 'Laporan Per Divisi',
            'leave-requests' => 'Laporan Izin'
        ];

        return $labels[$reportType] ?? 'Laporan Absensi';
    }

    public function getUsersByDivision($divisionId)
    {
        $users = User::where('division_id', $divisionId)
            ->where('group', 'user')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
            
        return response()->json($users);
    }
}
