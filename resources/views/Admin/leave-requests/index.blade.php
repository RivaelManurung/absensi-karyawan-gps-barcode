@extends('Admin.Layout.main')

@section('title', 'Kelola Pengajuan Izin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center py-3 mb-4">
        <h4 class="fw-bold mb-0">
            <span class="text-muted fw-light">Absensi /</span> Kelola Pengajuan Izin
        </h4>
        
        <!-- Filter Controls -->
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('admin.leave-requests.index') }}" class="d-flex gap-2">
                <select name="filter" class="form-select form-select-sm" style="width: auto;">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('filter') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('filter') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="rejected" {{ request('filter') === 'rejected' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control form-control-sm" style="width: auto;">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bx bx-search"></i> Filter
                </button>
                @if(request()->hasAny(['filter', 'date']))
                    <a href="{{ route('admin.leave-requests.index') }}" class="btn btn-sm btn-secondary">
                        <i class="bx bx-x"></i> Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-4">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-warning small fw-bold">PENDING</div>
                            <div class="h4 mb-0 fw-bold">
                                {{ $leaveRequests->where('approval_status', 'pending')->count() }}
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
        
        <div class="col-md-3 col-6 mb-4">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-success small fw-bold">DISETUJUI</div>
                            <div class="h4 mb-0 fw-bold">
                                {{ $leaveRequests->where('approval_status', 'approved')->count() }}
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
        
        <div class="col-md-3 col-6 mb-4">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-danger small fw-bold">DITOLAK</div>
                            <div class="h4 mb-0 fw-bold">
                                {{ $leaveRequests->where('approval_status', 'rejected')->count() }}
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
        
        <div class="col-md-3 col-6 mb-4">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-info small fw-bold">TOTAL</div>
                            <div class="h4 mb-0 fw-bold">{{ $leaveRequests->total() }}</div>
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
            <h5 class="mb-0">Daftar Pengajuan Izin & Sakit</h5>
            <span class="badge bg-label-primary">{{ $leaveRequests->total() }} Total Pengajuan</span>
        </div>
        
        <div class="card-body p-0">
            @if($leaveRequests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Karyawan</th>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Diproses</th>
                                <th style="width: 200px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaveRequests as $index => $request)
                            <tr>
                                <td class="fw-semibold">{{ $leaveRequests->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm me-3">
                                            <span class="avatar-initial rounded-circle bg-label-primary">
                                                {{ substr($request->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $request->user->name }}</div>
                                            <div class="text-muted small">{{ $request->user->division->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($request->date)->format('d M Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($request->date)->diffForHumans() }}</div>
                                </td>
                                <td>
                                    @if($request->status && $request->status->name === 'sick')
                                        <span class="badge bg-label-danger">
                                            <i class="bx bx-plus-medical me-1"></i>Sakit
                                        </span>
                                    @elseif($request->status && $request->status->name === 'excused')
                                        <span class="badge bg-label-info">
                                            <i class="bx bx-info-circle me-1"></i>Izin
                                        </span>
                                    @else
                                        <span class="badge bg-label-warning">
                                            <i class="bx bx-hourglass me-1"></i>Pending
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
                                    @if($request->approval_status === 'pending')
                                        <span class="badge bg-label-warning">
                                            <i class="bx bx-time me-1"></i>Pending
                                        </span>
                                    @elseif($request->approval_status === 'approved')
                                        <span class="badge bg-label-success">
                                            <i class="bx bx-check me-1"></i>Disetujui
                                        </span>
                                    @else
                                        <span class="badge bg-label-danger">
                                            <i class="bx bx-x me-1"></i>Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->approved_at)
                                        <div class="text-success small">
                                            <i class="bx bx-check-circle"></i>
                                            {{ \Carbon\Carbon::parse($request->approved_at)->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="text-muted smaller">
                                            oleh {{ $request->approvedBy->name ?? 'N/A' }}
                                        </div>
                                    @elseif($request->rejected_at)
                                        <div class="text-danger small">
                                            <i class="bx bx-x-circle"></i>
                                            {{ \Carbon\Carbon::parse($request->rejected_at)->format('d/m/Y H:i') }}
                                        </div>
                                        <div class="text-muted smaller">
                                            oleh {{ $request->rejectedBy->name ?? 'N/A' }}
                                        </div>
                                    @else
                                        <span class="text-muted small">Belum diproses</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- Detail Button -->
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" data-bs-target="#detailModal{{ $request->id }}">
                                            <i class="bx bx-show"></i>
                                        </button>
                                        
                                        @if($request->approval_status === 'pending')
                                            <!-- Approve Button -->
                                            <form action="{{ route('admin.leave-requests.approve', $request->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" 
                                                        onclick="return confirm('Yakin ingin menyetujui pengajuan ini?')">
                                                    <i class="bx bx-check"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Reject Button -->
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                                <i class="bx bx-x"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($leaveRequests->hasPages())
                    <div class="card-footer bg-transparent border-top">
                        <div class="d-flex justify-content-center">
                            {{ $leaveRequests->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="avatar avatar-xl mx-auto mb-3">
                        <span class="avatar-initial rounded bg-label-secondary">
                            <i class="bx bx-envelope fs-2"></i>
                        </span>
                    </div>
                    <h5 class="mb-1">Tidak ada pengajuan</h5>
                    <p class="text-muted mb-0">Belum ada pengajuan izin atau sakit yang masuk.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Detail & Reject Modals -->
@foreach($leaveRequests as $request)
    @include('admin.leave-requests.modals.detail', ['request' => $request])
    @if($request->approval_status === 'pending')
        @include('admin.leave-requests.modals.reject', ['request' => $request])
    @endif
@endforeach

@endsection

@push('styles')
<style>
.border-left-warning { border-left: 4px solid #ffab00 !important; }
.border-left-success { border-left: 4px solid #71dd37 !important; }
.border-left-danger { border-left: 4px solid #ff3e1d !important; }
.border-left-info { border-left: 4px solid #03c3ec !important; }
.smaller { font-size: 0.75rem; }
</style>
@endpush
