@extends('admin.layout.main')
@section('title', 'Karyawan per Divisi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Data Karyawan Berdasarkan Divisi</h4>
            <p class="text-muted mb-0">Kelola dan lihat karyawan berdasarkan divisi organisasi</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-expanded="false">
                <i class="bx bx-collapse-vertical me-2"></i>Toggle Semua
            </button>
            <button class="btn btn-primary">
                <i class="bx bx-plus me-2"></i>Tambah Karyawan
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-primary rounded">
                                <i class="bx bx-buildings text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Total Divisi</h6>
                            <span class="text-primary fw-bold h4 mb-0">{{ count($divisionsWithUsers) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-success rounded">
                                <i class="bx bx-user text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Total Karyawan</h6>
                            <span class="text-success fw-bold h4 mb-0">
                                {{ $divisionsWithUsers->sum(function($division) { return $division->users->count(); }) + $usersWithoutDivision->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-info rounded">
                                <i class="bx bx-group text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Karyawan Terbanyak</h6>
                            <span class="text-info fw-bold h4 mb-0">
                                {{ $divisionsWithUsers->max(function($division) { return $division->users->count(); }) ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-md me-3">
                            <div class="avatar-initial bg-warning rounded">
                                <i class="bx bx-user-x text-white"></i>
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">Tanpa Divisi</h6>
                            <span class="text-warning fw-bold h4 mb-0">{{ $usersWithoutDivision->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Division Cards -->
    @foreach ($divisionsWithUsers as $index => $division)
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                        <div class="avatar-initial bg-label-primary rounded">
                            <i class="bx bx-building"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold">{{ $division->name }}</h5>
                        <small class="text-muted">{{ $division->users->count() }} karyawan terdaftar</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        {{ $division->users->count() }} Karyawan
                    </span>
                    <button class="btn btn-sm btn-outline-secondary" type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#division{{ $index }}" 
                            aria-expanded="true">
                        <i class="bx bx-chevron-down"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="collapse show multi-collapse" id="division{{ $index }}">
            <div class="card-body pt-2">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-semibold">
                                    <i class="bx bx-user me-2 text-muted"></i>Nama
                                </th>
                                <th class="border-0 fw-semibold">
                                    <i class="bx bx-envelope me-2 text-muted"></i>Email
                                </th>
                                <th class="border-0 fw-semibold">
                                    <i class="bx bx-briefcase me-2 text-muted"></i>Jabatan
                                </th>
                                <th class="border-0 fw-semibold">
                                    <i class="bx bx-time me-2 text-muted"></i>Shift
                                </th>
                                <th class="border-0 fw-semibold">
                                    <i class="bx bx-group me-2 text-muted"></i>Grup
                                </th>
                                <th class="border-0 fw-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($division->users as $user)
                            <tr class="border-bottom">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="avatar-initial bg-label-secondary rounded-circle">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $user->email }}</span>
                                </td>
                                <td>
                                    @if($user->jobTitle?->name)
                                        <span class="badge bg-label-info rounded-pill">{{ $user->jobTitle->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->shift?->name)
                                        <span class="badge bg-label-success rounded-pill">{{ $user->shift->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary rounded-pill">{{ ucfirst($user->group) }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle hide-arrow" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#"><i class="bx bx-show me-2"></i>Lihat</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="bx bx-edit me-2"></i>Edit</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="bx bx-trash me-2"></i>Hapus</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bx bx-user-x text-muted mb-2" style="font-size: 2rem;"></i>
                                        <p class="text-muted mb-0">Tidak ada karyawan di divisi ini</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Users Without Division -->
    @if($usersWithoutDivision->isNotEmpty())
    <div class="card mb-4 border-0 shadow-sm border-start border-warning border-4">
        <div class="card-header bg-transparent border-0 pb-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-sm me-3">
                        <div class="avatar-initial bg-label-warning rounded">
                            <i class="bx bx-user-x"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="mb-1 fw-bold text-warning">
                            <i class="bx bx-error me-2"></i>Karyawan Tanpa Divisi
                        </h5>
                        <small class="text-muted">Karyawan yang belum ditempatkan di divisi tertentu</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-warning rounded-pill px-3 py-2">
                        {{ $usersWithoutDivision->count() }} Karyawan
                    </span>
                    <button class="btn btn-sm btn-outline-secondary" type="button" 
                            data-bs-toggle="collapse" 
                            data-bs-target="#withoutDivision" 
                            aria-expanded="true">
                        <i class="bx bx-chevron-down"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="collapse show multi-collapse" id="withoutDivision">
            <div class="card-body pt-2">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 fw-semibold">
                                    <i class="bx bx-user me-2 text-muted"></i>Nama
                                </th>
                                <th class="border-0 fw-semibold">
                                    <i class="bx bx-envelope me-2 text-muted"></i>Email
                                </th>
                                <th class="border-0 fw-semibold">
                                    <i class="bx bx-briefcase me-2 text-muted"></i>Jabatan
                                </th>
                                <th class="border-0 fw-semibold">
                                    <i class="bx bx-time me-2 text-muted"></i>Shift
                                </th>
                                <th class="border-0 fw-semibold">
                                    <i class="bx bx-group me-2 text-muted"></i>Grup
                                </th>
                                <th class="border-0 fw-semibold text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($usersWithoutDivision as $user)
                            <tr class="border-bottom">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <div class="avatar-initial bg-label-warning rounded-circle">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $user->email }}</span>
                                </td>
                                <td>
                                    @if($user->jobTitle?->name)
                                        <span class="badge bg-label-info rounded-pill">{{ $user->jobTitle->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->shift?->name)
                                        <span class="badge bg-label-success rounded-pill">{{ $user->shift->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-label-secondary rounded-pill">{{ ucfirst($user->group) }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle hide-arrow" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#"><i class="bx bx-show me-2"></i>Lihat</a></li>
                                            <li><a class="dropdown-item" href="#"><i class="bx bx-edit me-2"></i>Edit</a></li>
                                            <li><a class="dropdown-item text-primary" href="#"><i class="bx bx-building me-2"></i>Pindah ke Divisi</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#"><i class="bx bx-trash me-2"></i>Hapus</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.table tbody tr:hover {
    background-color: rgba(0,0,0,0.02);
}

.avatar-initial {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.border-start.border-warning {
    border-left-width: 4px !important;
}
</style>
@endsection