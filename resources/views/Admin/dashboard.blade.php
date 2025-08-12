@extends('admin.layout.main') 

@section('title', 'Dashboard Admin - Analytics')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Welcome Card --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Selamat Datang, {{ Auth::user()->name }}! 👋</h5>
                            <p class="mb-4">Dashboard Analytics untuk sistem absensi karyawan dengan geolocation tracking dan monitoring real-time.</p>
                            
                            {{-- Alert Summary --}}
                            @if($alerts['total'] > 0)
                            <div class="d-flex gap-2 mb-3">
                                @if($alerts['high_priority'] > 0)
                                <span class="badge bg-danger">{{ $alerts['high_priority'] }} Urgent</span>
                                @endif
                                @if($alerts['medium_priority'] > 0)
                                <span class="badge bg-warning">{{ $alerts['medium_priority'] }} Pending</span>
                                @endif
                                @if($alerts['low_priority'] > 0)
                                <span class="badge bg-info">{{ $alerts['low_priority'] }} Info</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop.png') }}" height="140"
                                alt="Analytics Dashboard" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Statistic Cards --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-group"></i></span>
                        </div>
                        <div class="dropdown">
                            <small class="text-success fw-semibold">+{{ number_format($stats['total_employees']) }}</small>
                        </div>
                    </div>
                    <span>Total Karyawan</span>
                    <h4 class="card-title mb-1">{{ number_format($stats['total_employees']) }}</h4>
                    <small class="text-muted">Karyawan Aktif</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-map-pin"></i></span>
                        </div>
                        <div class="dropdown">
                            <small class="text-info fw-semibold">{{ $stats['active_locations'] }}/{{ $stats['total_locations'] }}</small>
                        </div>
                    </div>
                    <span>Lokasi Absensi</span>
                    <h4 class="card-title mb-1">{{ $stats['total_locations'] }}</h4>
                    <small class="text-muted">{{ $stats['active_locations'] }} Aktif</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-building"></i></span>
                        </div>
                    </div>
                    <span>Total Divisi</span>
                    <h4 class="card-title mb-1">{{ $stats['total_divisions'] }}</h4>
                    <small class="text-muted">Departemen</small>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-time"></i></span>
                        </div>
                    </div>
                    <span>Shift Kerja</span>
                    <h4 class="card-title text-primary mb-1">{{ $stats['total_shifts'] }}</h4>
                    <small class="text-muted">Jam Kerja</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Real-time Today's Attendance --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Absensi Hari Ini - Real Time</h5>
                    <small class="text-muted">{{ \Carbon\Carbon::now()->format('d F Y, H:i') }}</small>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-success"><i class="bx bx-check"></i></span>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $todayAttendance['present'] }}</h4>
                                    <small class="text-muted">Hadir</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-warning"><i class="bx bx-time"></i></span>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $todayAttendance['late'] }}</h4>
                                    <small class="text-muted">Terlambat</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-danger"><i class="bx bx-x"></i></span>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $todayAttendance['absent'] }}</h4>
                                    <small class="text-muted">Absent</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-info"><i class="bx bx-calendar"></i></span>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $todayAttendance['on_leave'] }}</h4>
                                    <small class="text-muted">Izin/Sakit</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-secondary"><i class="bx bx-hourglass"></i></span>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $todayAttendance['pending_requests'] }}</h4>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-4 col-6 mb-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="avatar me-3">
                                    <span class="avatar-initial rounded bg-light"><i class="bx bx-user-minus"></i></span>
                                </div>
                                <div>
                                    <h4 class="mb-0">{{ $todayAttendance['not_recorded'] }}</h4>
                                    <small class="text-muted">Belum Absen</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Monthly Trends Chart --}}
        <div class="col-lg-8 col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Tren Absensi Bulanan</h5>
                    <div class="dropdown">
                        <form method="GET" action="{{ route('admin.dashboard') }}" class="d-flex">
                            <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                                @for($i = 1; $i <= 12; $i++)
                                    @php $monthVal = \Carbon\Carbon::now()->year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT) @endphp
                                    <option value="{{ $monthVal }}" {{ $selectedMonth == $monthVal ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($monthVal . '-01')->format('F Y') }}
                                    </option>
                                @endfor
                            </select>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div id="monthlyTrendsChart"></div>
                </div>
            </div>
        </div>

        {{-- Attendance Patterns --}}
        <div class="col-lg-4 col-12 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title m-0">Pola Check-in Harian</h5>
                    <small class="text-muted">Distribusi jam check-in</small>
                </div>
                <div class="card-body">
                    <div id="hourlyPatternChart"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Division Performance --}}
        <div class="col-lg-6 col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Performa Divisi</h5>
                    <small class="text-muted">Tingkat kehadiran per divisi</small>
                </div>
                <div class="card-body">
                    @forelse($divisionPerformance as $division)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-primary">
                                    {{ strtoupper(substr($division['division'], 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $division['division'] }}</h6>
                                <small class="text-muted">{{ $division['total_employees'] }} karyawan</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">{{ $division['attendance_rate'] }}%</span>
                            <br>
                            <small class="text-muted">{{ $division['present'] }}/{{ $division['total_attendances'] }}</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">Tidak ada data divisi.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Location Analytics --}}
        <div class="col-lg-6 col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title m-0">Analitik Lokasi</h5>
                    <small class="text-muted">Penggunaan lokasi absensi</small>
                </div>
                <div class="card-body">
                    @forelse($locationAnalytics as $location)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded {{ $location['is_active'] ? 'bg-success' : 'bg-secondary' }}">
                                    <i class="bx bx-map-pin"></i>
                                </span>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $location['name'] }}</h6>
                                <small class="text-muted">{{ $location['unique_users'] }} pengguna unik</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="text-primary">{{ $location['total_checkins'] }}</span>
                            <small class="text-muted">check-ins</small>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">Tidak ada data lokasi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title m-0">Aktivitas Terkini</h5>
                    <small class="text-muted">7 hari terakhir</small>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Karyawan</th>
                                    <th>Aktivitas</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $activity)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar me-3">
                                                <span class="avatar-initial rounded bg-label-info">
                                                    {{ strtoupper(substr($activity['user_name'], 0, 2)) }}
                                                </span>
                                            </div>
                                            <span>{{ $activity['user_name'] }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $activity['action'] }}</td>
                                    <td>
                                        <span class="badge bg-{{ $activity['status'] == 'present' ? 'success' : ($activity['status'] == 'late' ? 'warning' : 'info') }}">
                                            {{ ucfirst($activity['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($activity['date'])->format('d/m/Y') }}</td>
                                    <td>{{ $activity['time_formatted'] }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada aktivitas terkini</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Library ApexCharts --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Monthly Trends Chart
    const monthlyData = @json($monthlyTrends);
    const monthlyOptions = {
        series: [{
            name: 'Hadir',
            data: monthlyData.map(d => d.present)
        }, {
            name: 'Terlambat', 
            data: monthlyData.map(d => d.late)
        }, {
            name: 'Absent',
            data: monthlyData.map(d => d.absent)
        }, {
            name: 'Izin/Sakit',
            data: monthlyData.map(d => (d.sick + d.excused))
        }],
        chart: {
            type: 'line',
            height: 350,
            toolbar: { show: false }
        },
        colors: ['#28a745', '#ffc107', '#dc3545', '#17a2b8'],
        xaxis: {
            categories: monthlyData.map(d => d.day),
            title: { text: 'Tanggal' }
        },
        yaxis: {
            title: { text: 'Jumlah Karyawan' }
        },
        legend: { position: 'top' },
        stroke: { curve: 'smooth', width: 2 }
    };

    const monthlyChart = new ApexCharts(document.querySelector("#monthlyTrendsChart"), monthlyOptions);
    monthlyChart.render();

    // Hourly Pattern Chart
    const hourlyData = @json($attendancePatterns['hourly']);
    const hourlyOptions = {
        series: [{
            name: 'Check-ins',
            data: Object.values(hourlyData)
        }],
        chart: {
            type: 'bar',
            height: 300,
            toolbar: { show: false }
        },
        colors: ['#696cff'],
        xaxis: {
            categories: Object.keys(hourlyData).map(h => h + ':00'),
            title: { text: 'Jam' }
        },
        yaxis: {
            title: { text: 'Jumlah Check-in' }
        },
        plotOptions: {
            bar: { borderRadius: 4 }
        }
    };

    const hourlyChart = new ApexCharts(document.querySelector("#hourlyPatternChart"), hourlyOptions);
    hourlyChart.render();

    // Auto-refresh every 5 minutes for real-time data
    setInterval(function() {
        location.reload();
    }, 300000); // 5 minutes
});
</script>
@endpush