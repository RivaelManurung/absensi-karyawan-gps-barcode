<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Division;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display attendance reports index
     */
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $divisionId = $request->get('division_id');
        $statusId = $request->get('status_id');

        // Query untuk laporan
        $query = Attendance::with(['user.division', 'status'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($divisionId) {
            $query->whereHas('user', function($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        if ($statusId) {
            $query->where('status_id', $statusId);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('user_id')
            ->paginate(20);

        // Data untuk filter
        $divisions = Division::all();
        $statuses = Status::all();

        // Summary statistics
        $totalRecords = $query->count();
        $statusSummary = $query->select('status_id', DB::raw('count(*) as total'))
            ->groupBy('status_id')
            ->with('status')
            ->get();

        return view('admin.reports.index', compact(
            'attendances', 'divisions', 'statuses', 'startDate', 'endDate', 
            'divisionId', 'statusId', 'totalRecords', 'statusSummary'
        ));
    }

    /**
     * Generate daily attendance report
     */
    public function daily(Request $request)
    {
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        $divisionId = $request->get('division_id');

        $query = Attendance::with(['user.division', 'status'])
            ->whereDate('date', $date);

        if ($divisionId) {
            $query->whereHas('user', function($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        $attendances = $query->orderBy('user_id')->get();
        $divisions = Division::all();

        // Get all users for the selected division to show who's absent
        $usersQuery = User::with('division')->where('is_active', true);
        if ($divisionId) {
            $usersQuery->where('division_id', $divisionId);
        }
        $allUsers = $usersQuery->get();

        // Find users who didn't have attendance record
        $attendanceUserIds = $attendances->pluck('user_id')->toArray();
        $absentUsers = $allUsers->whereNotIn('id', $attendanceUserIds);

        return view('admin.reports.daily', compact(
            'attendances', 'divisions', 'date', 'divisionId', 'allUsers', 'absentUsers'
        ));
    }

    /**
     * Generate monthly summary report
     */
    public function monthly(Request $request)
    {
        $month = $request->get('month', Carbon::now()->format('Y-m'));
        $divisionId = $request->get('division_id');

        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        // Get attendance summary by user
        $query = User::with(['division'])
            ->withCount([
                'attendances as present_count' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->whereHas('status', function($sq) {
                          $sq->where('name', 'present');
                      });
                },
                'attendances as late_count' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->whereHas('status', function($sq) {
                          $sq->where('name', 'late');
                      });
                },
                'attendances as sick_count' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->whereHas('status', function($sq) {
                          $sq->whereIn('name', ['sick', 'approved']);
                      });
                },
                'attendances as excused_count' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->whereHas('status', function($sq) {
                          $sq->where('name', 'excused');
                      });
                },
                'attendances as absent_count' => function($q) use ($startDate, $endDate) {
                    $q->whereBetween('date', [$startDate, $endDate])
                      ->whereHas('status', function($sq) {
                          $sq->where('name', 'absent');
                      });
                }
            ])
            ->where('is_active', true);

        if ($divisionId) {
            $query->where('division_id', $divisionId);
        }

        $userSummaries = $query->paginate(20);
        $divisions = Division::all();

        // Working days in month (excluding weekends)
        $workingDays = 0;
        $current = $startDate->copy();
        while ($current <= $endDate) {
            if (!$current->isWeekend()) {
                $workingDays++;
            }
            $current->addDay();
        }

        return view('admin.reports.monthly', compact(
            'userSummaries', 'divisions', 'month', 'divisionId', 'workingDays', 'startDate', 'endDate'
        ));
    }

    /**
     * Export attendance report to Excel/CSV
     */
    public function export(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $divisionId = $request->get('division_id');
        $format = $request->get('format', 'csv'); // csv or excel

        $query = Attendance::with(['user.division', 'status'])
            ->whereBetween('date', [$startDate, $endDate]);

        if ($divisionId) {
            $query->whereHas('user', function($q) use ($divisionId) {
                $q->where('division_id', $divisionId);
            });
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        if ($format === 'csv') {
            return $this->exportToCsv($attendances, $startDate, $endDate);
        }

        // TODO: Implement Excel export if needed
        return response()->json(['error' => 'Format not supported yet'], 400);
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($attendances, $startDate, $endDate)
    {
        $filename = "laporan_absensi_{$startDate}_to_{$endDate}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, [
                'Tanggal',
                'Nama Karyawan',
                'NIK',
                'Divisi',
                'Status',
                'Jam Masuk',
                'Jam Keluar',
                'Keterangan'
            ]);

            // Data
            foreach ($attendances as $attendance) {
                fputcsv($file, [
                    Carbon::parse($attendance->date)->format('d/m/Y'),
                    $attendance->user->name,
                    $attendance->user->employee_id,
                    $attendance->user->division->name ?? '-',
                    $attendance->status->name ?? '-',
                    $attendance->time_in ? Carbon::parse($attendance->time_in)->format('H:i') : '-',
                    $attendance->time_out ? Carbon::parse($attendance->time_out)->format('H:i') : '-',
                    $attendance->note ?? '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
