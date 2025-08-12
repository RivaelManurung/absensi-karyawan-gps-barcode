@extends('admin.layout.main')
@section('title', 'Karyawan per Divisi')

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
                            <p class="text-heading mb-2">Total Divisi</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ count($divisionsWithUsers) }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="bx bx-buildings fs-4"></i>
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
                            <p class="text-heading mb-2">Total Karyawan</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ $divisionsWithUsers->sum(function($division) { return $division->users->count(); }) + $usersWithoutDivision->count() }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="bx bx-group fs-4"></i>
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
                            <p class="text-heading mb-2">Divisi Terbesar</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ $divisionsWithUsers->max(function($division) { return $division->users->count(); }) ?? 0 }}</h4>
                                <small class="text-muted">karyawan</small>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-trending-up fs-4"></i>
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
                            <p class="text-heading mb-2">Tanpa Divisi</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ $usersWithoutDivision->count() }}</h4>
                                <small class="text-muted">karyawan</small>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="bx bx-user-x fs-4"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Karyawan per Divisi</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" onclick="toggleAll()">
                    <i class="bx bx-expand me-1"></i>Toggle Semua
                </button>
                <a href="{{ route('admin.users.create') }}" class="btn btn-sm btn-primary">
                    <i class="bx bx-plus me-1"></i>Tambah Karyawan
                </a>
            </div>
        </div>
        <div class="card-body">
            @forelse($divisionsWithUsers as $division)
                <!-- Division Card -->
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        <i class="bx bx-buildings"></i>
                                    </span>
                                </span>
                                <div>
                                    <h6 class="mb-0">{{ $division->name }}</h6>
                                    <small class="text-muted">{{ $division->users->count() }} karyawan</small>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" onclick="toggleDivision('division-{{ $division->id }}')">
                                <i class="bx bx-chevron-down" id="icon-division-{{ $division->id }}"></i>
                            </button>
                        </div>
                    </div>
                    <div class="collapse" id="division-{{ $division->id }}">
                        <div class="card-body">
                            @if($division->users->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>NIP</th>
                                                <th>Nama</th>
                                                <th>Email</th>
                                                <th>Jabatan</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($division->users as $user)
                                            <tr>
                                                <td>
                                                    <span class="fw-medium">{{ $user->nip }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-xs me-2">
                                                            <span class="avatar-initial rounded-circle bg-label-info">
                                                                {{ substr($user->name, 0, 1) }}
                                                            </span>
                                                        </span>
                                                        <span>{{ $user->name }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    <span class="badge bg-label-secondary">{{ $user->jobTitle->name ?? 'Belum ditentukan' }}</span>
                                                </td>
                                                <td>
                                                    @if($user->group === 'admin' || $user->group === 'superadmin')
                                                        <span class="badge bg-label-primary">{{ ucfirst($user->group) }}</span>
                                                    @else
                                                        <span class="badge bg-label-success">Karyawan</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-3">
                                    <i class="bx bx-user-x display-4 text-muted"></i>
                                    <p class="text-muted mb-0">Belum ada karyawan di divisi ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-4">
                    <i class="bx bx-buildings display-4 text-muted"></i>
                    <h5 class="mt-2">Belum ada divisi</h5>
                    <p class="text-muted">Silakan buat divisi terlebih dahulu</p>
                </div>
            @endforelse

            <!-- Users without Division -->
            @if($usersWithoutDivision->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded bg-label-warning">
                                        <i class="bx bx-user-x"></i>
                                    </span>
                                </span>
                                <div>
                                    <h6 class="mb-0">Karyawan Tanpa Divisi</h6>
                                    <small class="text-muted">{{ $usersWithoutDivision->count() }} karyawan</small>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-warning" onclick="toggleDivision('division-none')">
                                <i class="bx bx-chevron-down" id="icon-division-none"></i>
                            </button>
                        </div>
                    </div>
                    <div class="collapse" id="division-none">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>NIP</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Jabatan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usersWithoutDivision as $user)
                                        <tr>
                                            <td>
                                                <span class="fw-medium">{{ $user->nip }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="avatar avatar-xs me-2">
                                                        <span class="avatar-initial rounded-circle bg-label-info">
                                                            {{ substr($user->name, 0, 1) }}
                                                        </span>
                                                    </span>
                                                    <span>{{ $user->name }}</span>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge bg-label-secondary">{{ $user->jobTitle->name ?? 'Belum ditentukan' }}</span>
                                            </td>
                                            <td>
                                                @if($user->group === 'admin' || $user->group === 'superadmin')
                                                    <span class="badge bg-label-primary">{{ ucfirst($user->group) }}</span>
                                                @else
                                                    <span class="badge bg-label-success">Karyawan</span>
                                                @endif
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
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleDivision(divisionId) {
    const element = document.getElementById(divisionId);
    const icon = document.getElementById('icon-' + divisionId);
    
    if (element.classList.contains('show')) {
        element.classList.remove('show');
        icon.classList.remove('bx-chevron-up');
        icon.classList.add('bx-chevron-down');
    } else {
        element.classList.add('show');
        icon.classList.remove('bx-chevron-down');
        icon.classList.add('bx-chevron-up');
    }
}

function toggleAll() {
    const divisions = document.querySelectorAll('[id^="division-"]');
    const icons = document.querySelectorAll('[id^="icon-division-"]');
    let allExpanded = true;
    
    // Check if all are expanded
    divisions.forEach(div => {
        if (!div.classList.contains('show')) {
            allExpanded = false;
        }
    });
    
    // Toggle all
    divisions.forEach((div, index) => {
        if (allExpanded) {
            div.classList.remove('show');
            icons[index].classList.remove('bx-chevron-up');
            icons[index].classList.add('bx-chevron-down');
        } else {
            div.classList.add('show');
            icons[index].classList.remove('bx-chevron-down');
            icons[index].classList.add('bx-chevron-up');
        }
    });
}
</script>
@endpush