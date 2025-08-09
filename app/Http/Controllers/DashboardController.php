<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Division;
use App\Models\JobTitle;
use App\Models\Shift;
use App\Models\Attendance;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data ringkasan.
     */
    public function index()
    {
        // 1. Data untuk Kartu Statistik
        $employeeCount = User::where('group', 'user')->count();
        $divisionCount = Division::count();
        $jobTitleCount = JobTitle::count();
        $shiftCount = Shift::count();

        // 2. Data untuk Donut Chart Absensi Hari Ini
        $attendanceCounts = Attendance::whereDate('date', Carbon::today())
            ->groupBy('status')
            ->select('status', DB::raw('count(*) as total'))
            ->pluck('total', 'status');

        $attendanceChartData = [
            'labels' => ['Hadir', 'Terlambat', 'Izin', 'Sakit'],
            'series' => [
                $attendanceCounts->get('present', 0),
                $attendanceCounts->get('late', 0),
                $attendanceCounts->get('excused', 0),
                $attendanceCounts->get('sick', 0),
            ]
        ];

        // 3. Data untuk Daftar Pengajuan Izin Terbaru
        $recentLeaveRequests = Attendance::with('user.division') 
            ->whereIn('status', ['excused', 'sick'])
            ->latest()
            ->take(5)
            ->get();
        
        // dd() sudah dihapus dari sini

        // 4. Mengirim semua data ke view
        return view('admin.dashboard', compact( 
            'employeeCount',
            'divisionCount',
            'jobTitleCount',
            'shiftCount',
            'attendanceChartData',
            'recentLeaveRequests'
        ));
    }
}