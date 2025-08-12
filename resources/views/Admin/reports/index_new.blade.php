@extends('admin.layout.main')
@section('title', 'Laporan Absensi')

@push('styles')
<style>
.report-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    color: white;
    padding: 2rem 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.filter-card {
    background: #ffffff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: none;
    margin-bottom: 2rem;
}

.stats-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 15px;
    color: white;
    text-align: center;
    padding: 1.5rem;
    margin-bottom: 1rem;
    border: none;
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-card.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stats-card.warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stats-card.success {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stats-card.danger {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.report-table {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: none;
}

.table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: 600;
    padding: 1rem;
    text-align: center;
}

.table tbody td {
    padding: 0.8rem;
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.division-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.action-btn {
    border-radius: 25px;
    padding: 0.5rem 1rem;
    border: none;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 0;
}

.avatar {
    width: 2rem;
    height: 2rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.avatar-initial {
    font-size: 0.75rem;
    font-weight: 600;
}

.empty-state {
    padding: 2rem;
}

@media (max-width: 768px) {
    .report-header {
        padding: 1.5rem 1rem;
        text-align: center;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .stats-card {
        margin-bottom: 1rem;
    }
}
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bx bx-check-circle me-2"></i>
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Modern Header -->
    <div class="report-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="bx bx-bar-chart-alt-2 me-3"></i>Laporan Absensi
                </h1>
                <p class="page-subtitle">Analisis komprehensif kehadiran karyawan & manajemen pengajuan izin</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-light action-btn" onclick="printReport()">
                        <i class="bx bx-printer"></i>Cetak
                    </button>
                    <a class="btn btn-success action-btn" href="{{ route('admin.reports.export', request()->query() + ['format' => 'csv']) }}">
                        <i class="bx bx-download"></i>Export
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filter Section -->
    <div class="card filter-card">
        <div class="card-header bg-transparent border-0 pb-0">
            <h5 class="mb-0">
                <i class="bx bx-filter-alt me-2"></i>Filter & Pengaturan Laporan
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.index') }}" class="row g-3">
                <!-- Report Type Selection -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">
                        <i class="bx bx-category me-1"></i>Jenis Laporan
                    </label>
                    <select class="form-select" name="report_type" id="reportType">
                        <option value="overview" {{ $reportType == 'overview' ? 'selected' : '' }}>📊 Overview Umum</option>
                        <option value="daily" {{ $reportType == 'daily' ? 'selected' : '' }}>📅 Laporan Harian</option>
                        <option value="monthly" {{ $reportType == 'monthly' ? 'selected' : '' }}>📆 Laporan Bulanan</option>
                        <option value="employee" {{ $reportType == 'employee' ? 'selected' : '' }}>👤 Per Karyawan</option>
                        <option value="division" {{ $reportType == 'division' ? 'selected' : '' }}>🏢 Per Divisi</option>
                        <option value="leave-requests" {{ $reportType == 'leave-requests' ? 'selected' : '' }}>📝 Pengajuan Izin</option>
                    </select>
                </div>
                
                <!-- Division Filter -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">
                        <i class="bx bx-buildings me-1"></i>Divisi
                    </label>
                    <select class="form-select" name="division_id" id="division_id">
                        <option value="">🌐 Semua Divisi</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}" {{ $divisionId == $division->id ? 'selected' : '' }}>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- User Filter -->
                <div class="col-md-6 col-lg-3">
                    <label class="form-label fw-semibold">
                        <i class="bx bx-user me-1"></i>Karyawan
                    </label>
                    <select class="form-select" name="user_id" id="user_id">
                        <option value="">👥 Semua Karyawan</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ ($userId ?? '') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Date Filters (Dynamic based on report type) -->
                <div class="col-md-6 col-lg-3" id="dateRangeFilter">
                    <label class="form-label fw-semibold">
                        <i class="bx bx-calendar me-1"></i>Tanggal Mulai
                    </label>
                    <input type="date" class="form-control" name="start_date" value="{{ $startDate }}">
                </div>
                
                <div class="col-md-6 col-lg-3" id="endDateFilter">
                    <label class="form-label fw-semibold">
                        <i class="bx bx-calendar-check me-1"></i>Tanggal Akhir
                    </label>
                    <input type="date" class="form-control" name="end_date" value="{{ $endDate }}">
                </div>

                <div class="col-md-6 col-lg-3" id="specificDateFilter" style="display: none;">
                    <label class="form-label fw-semibold">
                        <i class="bx bx-calendar-alt me-1"></i>Tanggal Spesifik
                    </label>
                    <input type="date" class="form-control" name="specific_date" value="{{ $specificDate }}">
                </div>

                <div class="col-md-6 col-lg-3" id="monthFilter" style="display: none;">
                    <label class="form-label fw-semibold">
                        <i class="bx bx-calendar-event me-1"></i>Pilih Bulan
                    </label>
                    <input type="month" class="form-control" name="month" value="{{ $month }}">
                </div>
                
                <!-- Status Filters -->
                <div class="col-md-6 col-lg-3" id="statusFilter" style="{{ $reportType == 'leave-requests' ? 'display: none;' : '' }}">
                    <label class="form-label fw-semibold">
                        <i class="bx bx-check-circle me-1"></i>Status Absensi
                    </label>
                    <select class="form-select" name="status_id">
                        <option value="">🎯 Semua Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ $statusId == $status->id ? 'selected' : '' }}>
                                @if($status->name === 'present') ✅ Hadir
                                @elseif($status->name === 'late') ⏰ Terlambat
                                @elseif($status->name === 'absent') ❌ Tidak Hadir
                                @elseif($status->name === 'sick') 🤒 Sakit
                                @elseif($status->name === 'excused') 📝 Izin
                                @elseif($status->name === 'leave') 🏖️ Cuti
                                @else {{ ucfirst($status->name) }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-6 col-lg-3" id="approvalFilter" style="{{ $reportType == 'leave-requests' ? '' : 'display: none;' }}">
                    <label class="form-label fw-semibold">
                        <i class="bx bx-task me-1"></i>Status Approval
                    </label>
                    <select class="form-select" name="approval_filter">
                        <option value="">📋 Semua Status</option>
                        <option value="pending" {{ request('approval_filter') == 'pending' ? 'selected' : '' }}>⏳ Menunggu</option>
                        <option value="approved" {{ request('approval_filter') == 'approved' ? 'selected' : '' }}>✅ Disetujui</option>
                        <option value="rejected" {{ request('approval_filter') == 'rejected' ? 'selected' : '' }}>❌ Ditolak</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="col-12">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-2"></i>Terapkan Filter
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-refresh me-2"></i>Reset Filter
                        </a>
                        <button type="button" class="btn btn-outline-info" onclick="toggleAdvancedFilters()">
                            <i class="bx bx-cog me-2"></i>Pengaturan Lanjutan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    @if($reportType === 'leave-requests')
    <div class="row mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stats-card primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $leaveStats['total_requests'] ?? 0 }}</h3>
                        <small class="opacity-75">Total Pengajuan</small>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bx bx-file-blank fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stats-card warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $leaveStats['pending_requests'] ?? 0 }}</h3>
                        <small class="opacity-75">Menunggu Approval</small>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bx bx-time fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stats-card success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $leaveStats['approved_requests'] ?? 0 }}</h3>
                        <small class="opacity-75">Disetujui</small>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bx bx-check-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stats-card danger">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ $leaveStats['rejected_requests'] ?? 0 }}</h3>
                        <small class="opacity-75">Ditolak</small>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bx bx-x-circle fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <!-- General Attendance Statistics -->
    <div class="row mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="stats-card success">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ isset($data) ? $data->where('status.name', 'present')->count() : 0 }}</h3>
                        <small class="opacity-75">Hadir</small>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bx bx-check fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stats-card warning">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ isset($data) ? $data->where('status.name', 'late')->count() : 0 }}</h3>
                        <small class="opacity-75">Terlambat</small>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bx bx-time fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stats-card danger">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ isset($data) ? $data->where('status.name', 'absent')->count() : 0 }}</h3>
                        <small class="opacity-75">Tidak Hadir</small>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bx bx-x fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xl-3">
            <div class="stats-card primary">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="mb-0">{{ isset($data) ? $data->whereIn('status.name', ['sick', 'excused', 'leave'])->count() : 0 }}</h3>
                        <small class="opacity-75">Izin/Sakit/Cuti</small>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bx bx-user-plus fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modern Data Table Section -->
    <div class="card report-table">
        <div class="card-header bg-transparent border-0 pb-0">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-1 fw-bold">
                        @switch($reportType)
                            @case('daily')
                                <i class="bx bx-calendar-alt me-2"></i>📅 Laporan Harian
                                @break
                            @case('monthly')
                                <i class="bx bx-calendar-event me-2"></i>📊 Laporan Bulanan
                                @break
                            @case('employee')
                                <i class="bx bx-user me-2"></i>👥 Laporan Per Karyawan
                                @break
                            @case('division')
                                <i class="bx bx-buildings me-2"></i>🏢 Laporan Per Divisi
                                @break
                            @case('leave-requests')
                                <i class="bx bx-file-blank me-2"></i>📝 Laporan Pengajuan Izin
                                @break
                            @default
                                <i class="bx bx-bar-chart-alt me-2"></i>📋 Overview Laporan Absensi
                        @endswitch
                    </h5>
                    <p class="text-muted small mb-0">
                        Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Semua' }} 
                        @if($endDate) - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }} @endif
                        @if($divisionId) | Divisi: {{ $divisions->where('id', $divisionId)->first()?->name ?? 'N/A' }} @endif
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <span class="badge bg-primary-gradient px-3 py-2">
                            Total: {{ isset($data) && method_exists($data, 'total') ? $data->total() : (isset($data) ? count($data) : 0) }} records
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            @if($reportType === 'division')
                                <th class="text-center" width="5%">#</th>
                                <th><i class="bx bx-buildings me-1"></i>Nama Divisi</th>
                                <th class="text-center"><i class="bx bx-user me-1"></i>Total Karyawan</th>
                                <th class="text-center">✅ Hadir</th>
                                <th class="text-center">⏰ Terlambat</th>
                                <th class="text-center">❌ Tidak Hadir</th>
                                <th class="text-center">🤒 Sakit</th>
                                <th class="text-center">📝 Izin</th>
                                <th class="text-center">🏖️ Cuti</th>
                                <th class="text-center" width="10%">Aksi</th>
                            @elseif($reportType === 'leave-requests')
                                <th class="text-center" width="5%">#</th>
                                <th><i class="bx bx-calendar me-1"></i>Tanggal</th>
                                <th><i class="bx bx-user me-1"></i>Karyawan</th>
                                <th><i class="bx bx-buildings me-1"></i>Divisi</th>
                                <th><i class="bx bx-check-circle me-1"></i>Status</th>
                                <th><i class="bx bx-note me-1"></i>Keterangan</th>
                                <th><i class="bx bx-file me-1"></i>Lampiran</th>
                                <th class="text-center"><i class="bx bx-task me-1"></i>Status Approval</th>
                                <th class="text-center"><i class="bx bx-time me-1"></i>Waktu Approval</th>
                                <th class="text-center" width="10%">Aksi</th>
                            @else
                                <th class="text-center" width="5%">#</th>
                                <th><i class="bx bx-calendar me-1"></i>Tanggal</th>
                                <th><i class="bx bx-user me-1"></i>Karyawan</th>
                                <th><i class="bx bx-buildings me-1"></i>Divisi</th>
                                <th class="text-center"><i class="bx bx-check-circle me-1"></i>Status</th>
                                <th class="text-center"><i class="bx bx-time me-1"></i>Masuk</th>
                                <th class="text-center"><i class="bx bx-time me-1"></i>Keluar</th>
                                <th class="text-center"><i class="bx bx-timer me-1"></i>Durasi</th>
                                @if($reportType === 'employee')
                                <th class="text-center"><i class="bx bx-note me-1"></i>Keterangan</th>
                                @endif
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @if($reportType === 'division')
                            @forelse($data as $index => $division)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial bg-primary rounded-circle">
                                                <i class="bx bx-buildings"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $division->name }}</h6>
                                            <small class="text-muted">Kode: {{ $division->code ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $division->total_employees ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">{{ $division->present ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning">{{ $division->late ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger">{{ $division->absent ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $division->sick ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $division->excused ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-dark">{{ $division->leave ?? 0 }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.users.per-division') }}?division_id={{ $division->id }}" 
                                       class="btn btn-sm btn-outline-primary action-btn" title="Lihat Detail">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bx bx-folder-open display-4 text-muted"></i>
                                        <h6 class="mt-2 text-muted">Tidak ada data divisi</h6>
                                        <p class="text-muted small">Silakan ubah filter untuk melihat data</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse

                        @elseif($reportType === 'leave-requests')
                            @forelse($data as $index => $request)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial bg-primary rounded-circle">
                                                {{ substr($request->user?->name ?? 'N', 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $request->user?->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $request->user?->employee_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="division-badge">{{ $request->user?->division?->name ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($request->status?->name === 'sick')
                                        <span class="status-badge bg-info text-white">🤒 Sakit</span>
                                    @elseif($request->status?->name === 'excused')
                                        <span class="status-badge bg-secondary text-white">📝 Izin</span>
                                    @elseif($request->status?->name === 'leave')
                                        <span class="status-badge bg-dark text-white">🏖️ Cuti</span>
                                    @else
                                        <span class="status-badge bg-light text-dark">{{ $request->status?->name ?? '-' }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 150px;" title="{{ $request->note }}">
                                        {{ $request->note }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    @if($request->attachment)
                                        <a href="{{ route('admin.leave-requests.view-attachment', $request->id) }}" 
                                           class="btn btn-sm btn-outline-info action-btn" target="_blank" title="Lihat Lampiran">
                                            <i class="bx bx-file"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($request->approved_at)
                                        <span class="status-badge bg-success text-white">✅ Disetujui</span>
                                        <br><small class="text-muted">{{ $request->approvedBy?->name }}</small>
                                    @elseif($request->rejected_at)
                                        <span class="status-badge bg-danger text-white">❌ Ditolak</span>
                                        <br><small class="text-muted">{{ $request->rejectedBy?->name }}</small>
                                    @else
                                        <span class="status-badge bg-warning text-white">⏳ Pending</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <small class="text-muted">
                                        @if($request->approved_at)
                                            {{ \Carbon\Carbon::parse($request->approved_at)->format('d/m/Y H:i') }}
                                        @elseif($request->rejected_at)
                                            {{ \Carbon\Carbon::parse($request->rejected_at)->format('d/m/Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </small>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.leave-requests.show', $request->id) }}" 
                                       class="btn btn-sm btn-outline-primary action-btn" title="Lihat Detail">
                                        <i class="bx bx-show"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bx bx-file-blank display-4 text-muted"></i>
                                        <h6 class="mt-2 text-muted">Tidak ada data pengajuan izin</h6>
                                        <p class="text-muted small">Silakan ubah filter untuk melihat data</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse

                        @else
                            @forelse($data as $index => $attendance)
                            <tr>
                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                <td>
                                    <span class="fw-semibold">{{ $attendance->date ? \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') : '-' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-2">
                                            <span class="avatar-initial bg-primary rounded-circle">
                                                {{ substr($attendance->user?->name ?? 'N', 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $attendance->user?->name ?? 'N/A' }}</h6>
                                            <small class="text-muted">{{ $attendance->user?->employee_id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="division-badge">{{ $attendance->user?->division?->name ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($attendance->status?->name === 'present')
                                        <span class="status-badge bg-success text-white">✅ Hadir</span>
                                    @elseif($attendance->status?->name === 'late')
                                        <span class="status-badge bg-warning text-white">⏰ Terlambat</span>
                                    @elseif($attendance->status?->name === 'absent')
                                        <span class="status-badge bg-danger text-white">❌ Tidak Hadir</span>
                                    @elseif($attendance->status?->name === 'sick')
                                        <span class="status-badge bg-info text-white">🤒 Sakit</span>
                                    @elseif($attendance->status?->name === 'excused')
                                        <span class="status-badge bg-secondary text-white">📝 Izin</span>
                                    @elseif($attendance->status?->name === 'leave')
                                        <span class="status-badge bg-dark text-white">🏖️ Cuti</span>
                                    @else
                                        <span class="status-badge bg-light text-dark">{{ $attendance->status?->name ?? '-' }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold">{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold">{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($attendance->time_in && $attendance->time_out)
                                        @php
                                            $timeIn = \Carbon\Carbon::parse($attendance->time_in);
                                            $timeOut = \Carbon\Carbon::parse($attendance->time_out);
                                            $duration = $timeIn->diff($timeOut);
                                        @endphp
                                        <span class="badge bg-primary">{{ $duration->format('%h:%i') }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                @if($reportType === 'employee')
                                <td>
                                    <div class="text-truncate" style="max-width: 150px;" title="{{ $attendance->note }}">
                                        {{ $attendance->note ?: '-' }}
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $reportType === 'employee' ? '9' : '8' }}" class="text-center py-4">
                                    <div class="empty-state">
                                        <i class="bx bx-calendar-x display-4 text-muted"></i>
                                        <h6 class="mt-2 text-muted">Tidak ada data absensi</h6>
                                        <p class="text-muted small">Silakan ubah filter untuk melihat data</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if(isset($data) && method_exists($data, 'links'))
        <div class="card-footer bg-transparent border-0">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $data->firstItem() ?? 0 }} - {{ $data->lastItem() ?? 0 }} dari {{ $data->total() ?? 0 }} data
                </small>
                {{ $data->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle report type change
    function updateFiltersDisplay() {
        const reportType = document.getElementById('reportType').value;
        const dateRangeFilter = document.getElementById('dateRangeFilter');
        const endDateFilter = document.getElementById('endDateFilter');
        const specificDateFilter = document.getElementById('specificDateFilter');
        const monthFilter = document.getElementById('monthFilter');
        const statusFilter = document.getElementById('statusFilter');
        const approvalFilter = document.getElementById('approvalFilter');

        // Hide all filters first
        dateRangeFilter.style.display = 'none';
        endDateFilter.style.display = 'none';
        specificDateFilter.style.display = 'none';
        monthFilter.style.display = 'none';
        statusFilter.style.display = 'none';
        approvalFilter.style.display = 'none';

        // Show appropriate filters based on report type
        if (reportType === 'daily') {
            specificDateFilter.style.display = 'block';
            statusFilter.style.display = 'block';
        } else if (reportType === 'monthly') {
            monthFilter.style.display = 'block';
            statusFilter.style.display = 'block';
        } else if (reportType === 'leave-requests') {
            dateRangeFilter.style.display = 'block';
            endDateFilter.style.display = 'block';
            approvalFilter.style.display = 'block';
        } else {
            dateRangeFilter.style.display = 'block';
            endDateFilter.style.display = 'block';
            statusFilter.style.display = 'block';
        }
    }

    // Handle division change to load users
    function loadUsersByDivision() {
        const divisionId = document.getElementById('division_id').value;
        const userSelect = document.getElementById('user_id');
        
        // Clear existing options except the first one
        while (userSelect.children.length > 1) {
            userSelect.removeChild(userSelect.lastChild);
        }

        if (divisionId) {
            // Show loading state
            userSelect.disabled = true;
            const loadingOption = document.createElement('option');
            loadingOption.textContent = 'Memuat...';
            userSelect.appendChild(loadingOption);

            fetch(`/admin/divisions/${divisionId}/users`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Remove loading option
                userSelect.removeChild(userSelect.lastChild);
                userSelect.disabled = false;
                
                data.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = `${user.name} (${user.employee_id})`;
                    userSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error loading users:', error);
                userSelect.removeChild(userSelect.lastChild);
                userSelect.disabled = false;
                
                const errorOption = document.createElement('option');
                errorOption.textContent = 'Error memuat data';
                userSelect.appendChild(errorOption);
            });
        }
    }

    // Toggle advanced filters (future enhancement)
    window.toggleAdvancedFilters = function() {
        alert('Fitur pengaturan lanjutan akan segera tersedia');
    }

    // Print function
    window.printReport = function() {
        // Hide non-printable elements
        const elements = document.querySelectorAll('.btn, .pagination, .card-header .btn-group');
        elements.forEach(el => el.style.display = 'none');
        
        window.print();
        
        // Restore elements after print
        setTimeout(() => {
            elements.forEach(el => el.style.display = '');
        }, 1000);
    }

    // Event listeners
    document.getElementById('reportType').addEventListener('change', updateFiltersDisplay);
    document.getElementById('division_id').addEventListener('change', loadUsersByDivision);

    // Initialize on page load
    updateFiltersDisplay();
});
</script>
@endpush

@endsection
