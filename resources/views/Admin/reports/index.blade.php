@extends('admin.layout.main')
@section('title', 'Laporan Absensi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal!</strong> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="me-1">
                            <p class="text-heading mb-2">Total Karyawan</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ $statistics['total_employees'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-user fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="me-1">
                            <p class="text-heading mb-2">Hadir Hari Ini</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ $statistics['present_today'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-check fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="me-1">
                            <p class="text-heading mb-2">Izin Hari Ini</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ $statistics['on_leave_today'] ?? 0 }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-time fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="me-1">
                            <p class="text-heading mb-2">Absen Hari Ini</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ ($statistics['total_employees'] ?? 0) - ($statistics['present_today'] ?? 0) - ($statistics['on_leave_today'] ?? 0) }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="bx bx-x fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Export Card -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Filter Laporan & Export Data</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.index') }}" id="filterForm">
                <div class="row">
                    <!-- Report Type -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Jenis Laporan</label>
                        <select name="report_type" class="form-select" onchange="toggleFilters()">
                            <option value="overview" {{ request('report_type', 'overview') == 'overview' ? 'selected' : '' }}>
                                Ringkasan
                            </option>
                            <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>
                                Harian
                            </option>
                            <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>
                                Bulanan
                            </option>
                            <option value="employee" {{ request('report_type') == 'employee' ? 'selected' : '' }}>
                                Per Karyawan
                            </option>
                            <option value="division" {{ request('report_type') == 'division' ? 'selected' : '' }}>
                                Per Divisi
                            </option>
                            <option value="leave-requests" {{ request('report_type') == 'leave-requests' ? 'selected' : '' }}>
                                Pengajuan Izin
                            </option>
                        </select>
                    </div>

                    <!-- Division Filter -->
                    <div class="col-md-3 mb-3" id="division-filter">
                        <label class="form-label">Divisi</label>
                        <select name="division_id" class="form-select" onchange="loadUsers()">
                            <option value="">Semua Divisi</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ request('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- User Filter -->
                    <div class="col-md-3 mb-3" id="user-filter" style="{{ request('report_type') == 'employee' ? 'display: block;' : 'display: none;' }}">
                        <label class="form-label">Karyawan</label>
                        <select name="user_id" class="form-select" id="user-select">
                            <option value="">Pilih Karyawan</option>
                            @if(request('division_id'))
                                @foreach($users->where('division_id', request('division_id')) as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ request('start_date', date('Y-m-01')) }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Akhir</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ request('end_date', date('Y-m-d')) }}">
                    </div>
                    <div class="col-md-9 mb-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-search me-1"></i>Terapkan Filter
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
                            <i class="bx bx-refresh me-1"></i>Reset
                        </a>
                        
                        <!-- Export Buttons -->
                        <button type="button" onclick="exportData('excel')" class="btn btn-success">
                            <i class="bx bx-file me-1"></i>Export Excel
                        </button>
                        
                        <button type="button" onclick="exportData('pdf')" class="btn btn-danger">
                            <i class="bx bx-file-blank me-1"></i>Export PDF
                        </button>
                        
                        <button type="button" onclick="exportData('csv')" class="btn btn-outline-info">
                            <i class="bx bx-file me-1"></i>Export CSV
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results Section -->
    @if(request('report_type', 'overview') == 'overview')
        @include('Admin.reports.partials.overview')
    @elseif(request('report_type') == 'daily')
        @include('Admin.reports.partials.daily')
    @elseif(request('report_type') == 'monthly')
        @include('Admin.reports.partials.monthly')
    @elseif(request('report_type') == 'employee')
        @include('Admin.reports.partials.employee')
    @elseif(request('report_type') == 'division')
        @include('Admin.reports.partials.division')
    @elseif(request('report_type') == 'leave-requests')
        @include('Admin.reports.partials.leave-requests')
    @endif


</div>

<script>
function toggleFilters() {
    const reportType = document.querySelector('select[name="report_type"]').value;
    const userFilter = document.getElementById('user-filter');
    
    if (reportType === 'employee') {
        userFilter.style.display = 'block';
    } else {
        userFilter.style.display = 'none';
        document.getElementById('user-select').value = '';
    }
}

function loadUsers() {
    const divisionId = document.querySelector('select[name="division_id"]').value;
    const userSelect = document.getElementById('user-select');
    
    // Reset user select
    userSelect.innerHTML = '<option value="">Pilih Karyawan</option>';
    
    if (divisionId) {
        fetch(`/admin/divisions/${divisionId}/users`)
            .then(response => response.json())
            .then(users => {
                users.forEach(user => {
                    const option = document.createElement('option');
                    option.value = user.id;
                    option.textContent = user.name;
                    userSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading users:', error));
    }
}

function exportData(format) {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    
    // Create export URL with current filters
    const params = new URLSearchParams();
    for (let [key, value] of formData.entries()) {
        if (value) params.append(key, value);
    }
    params.append('format', format);
    
    // Open export in new window
    window.open(`{{ route('admin.reports.export') }}?${params.toString()}`, '_blank');
}

// Initialize filters on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleFilters();
});
</script>
@endsection
