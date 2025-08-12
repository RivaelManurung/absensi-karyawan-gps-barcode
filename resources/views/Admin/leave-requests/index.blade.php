@extends('admin.layout.main')

@section('title', 'Kelola Pengajuan Izin')

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

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">Kelola Pengajuan Izin</h4>
            <p class="text-muted mb-0">Kelola semua pengajuan izin, sakit, dan cuti karyawan</p>
        </div>
        
        <!-- Filter Controls -->
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('admin.leave-requests.index') }}" class="d-flex gap-2" id="filterForm">
                <select name="filter" class="form-select" style="width: auto;" onchange="document.getElementById('filterForm').submit();">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('filter') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('filter') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('filter') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control" style="width: auto;" onchange="document.getElementById('filterForm').submit();">
                @if(request()->hasAny(['filter', 'date']))
                    <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-outline-secondary">
                        <i class="bx bx-x me-1"></i>Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="me-1">
                            <p class="text-heading mb-2">Pending</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ $statistics['pending'] }}</h4>
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
                            <p class="text-heading mb-2">Disetujui</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ $statistics['approved'] }}</h4>
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
                            <p class="text-heading mb-2">Ditolak</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ $statistics['rejected'] }}</h4>
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
        
        <div class="col-xl-3 col-md-6 col-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="me-1">
                            <p class="text-heading mb-2">Total Pengajuan</p>
                            <div class="d-flex align-items-center">
                                <h4 class="mb-2 me-1 display-6">{{ $statistics['total'] }}</h4>
                            </div>
                        </div>
                        <div class="avatar">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="bx bx-envelope fs-4"></i>
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
            <h5 class="mb-0">Daftar Pengajuan Izin</h5>
            <div class="d-flex gap-2">
                <span class="badge bg-label-primary">
                    {{ is_array($leaveRequests) ? count($leaveRequests) : $leaveRequests->total() }} 
                    @if(request('filter'))
                        {{ ucfirst(request('filter')) }} 
                    @endif
                    Pengajuan
                    @if(request('date'))
                        ({{ \Carbon\Carbon::parse(request('date'))->format('d M Y') }})
                    @endif
                </span>
            </div>
        </div>
        
        <div class="card-body">
            @if((is_array($leaveRequests) ? count($leaveRequests) : $leaveRequests->count()) > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Karyawan</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Diproses</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $requestList = is_array($leaveRequests) ? $leaveRequests : $leaveRequests->items(); @endphp
                            @foreach($requestList as $index => $request)
                            <tr>
                                <td class="fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm me-2">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ substr($request->user->name, 0, 1) }}
                                            </span>
                                        </span>
                                        <div>
                                            <div class="fw-semibold">{{ $request->user->name }}</div>
                                            <small class="text-muted">{{ $request->user->division->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($request->date)->format('d M Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($request->date)->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($request->request_type === 'sick' || ($request->status && $request->status->name === 'sick'))
                                        <span class="badge bg-label-danger">
                                            <i class="bx bx-plus-medical me-1"></i>Sakit
                                        </span>
                                    @elseif($request->request_type === 'excused' || ($request->status && $request->status->name === 'excused'))
                                        <span class="badge bg-label-info">
                                            <i class="bx bx-info-circle me-1"></i>Izin
                                        </span>
                                    @elseif($request->request_type === 'leave' || ($request->status && $request->status->name === 'leave'))
                                        <span class="badge bg-label-success">
                                            <i class="bx bx-calendar me-1"></i>Cuti
                                        </span>
                                    @else
                                        <span class="badge bg-label-warning">
                                            <i class="bx bx-hourglass me-1"></i>{{ ucfirst($request->request_type ?? 'Pending') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $request->note }}">
                                        {{ $request->note }}
                                    </div>
                                    @if($request->attachment)
                                        <div class="mt-1">
                                            <small class="text-success">
                                                <i class="bx bx-paperclip"></i> Ada lampiran
                                            </small>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($request->approved_at && !$request->rejected_at)
                                        <span class="badge bg-label-success">
                                            <i class="bx bx-check me-1"></i>Disetujui
                                        </span>
                                    @elseif($request->rejected_at)
                                        <span class="badge bg-label-danger">
                                            <i class="bx bx-x me-1"></i>Ditolak
                                        </span>
                                    @else
                                        <span class="badge bg-label-warning">
                                            <i class="bx bx-time me-1"></i>Pending
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->approved_at)
                                        <div class="text-success small">
                                            <i class="bx bx-check-circle"></i>
                                            {{ \Carbon\Carbon::parse($request->approved_at)->format('d/m/Y H:i') }}
                                        </div>
                                        <small class="text-muted">
                                            oleh {{ $request->approvedBy->name ?? 'N/A' }}
                                        </small>
                                    @elseif($request->rejected_at)
                                        <div class="text-danger small">
                                            <i class="bx bx-x-circle"></i>
                                            {{ \Carbon\Carbon::parse($request->rejected_at)->format('d/m/Y H:i') }}
                                        </div>
                                        <small class="text-muted">
                                            oleh {{ $request->rejectedBy->name ?? 'N/A' }}
                                        </small>
                                    @else
                                        <span class="text-muted small">Belum diproses</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#detailModal{{ $request->id }}">
                                                    <i class="bx bx-show me-2"></i>Detail
                                                </a>
                                            </li>
                                            @if(!$request->approved_at && !$request->rejected_at)
                                                <li>
                                                    <form action="{{ route('admin.leave-requests.approve', $request->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item text-success" onclick="return confirm('Yakin ingin menyetujui pengajuan ini?')">
                                                            <i class="bx bx-check me-2"></i>Setujui
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                        <i class="bx bx-x me-2"></i>Tolak
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if(!is_array($leaveRequests) && $leaveRequests->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $leaveRequests->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="bx bx-envelope display-4 text-muted"></i>
                    <h5 class="mt-2">Tidak ada pengajuan</h5>
                    <p class="text-muted">Belum ada pengajuan izin atau sakit yang masuk.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Detail & Reject Modals -->
@php $requestList = is_array($leaveRequests) ? $leaveRequests : $leaveRequests->items(); @endphp
@foreach($requestList as $request)
    @include('admin.leave-requests.modals.detail', ['request' => $request])
    @if(!$request->approved_at && !$request->rejected_at)
        @include('admin.leave-requests.modals.reject', ['request' => $request])
    @endif
@endforeach

@endsection
