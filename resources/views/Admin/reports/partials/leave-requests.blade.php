<!-- Leave Requests Report -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Laporan Pengajuan Izin</h5>
    </div>
    <div class="card-body">
        @if(isset($leave_requests) && $leave_requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <th>Nama Karyawan</th>
                            <th>Divisi</th>
                            <th>Tanggal Izin</th>
                            <th>Durasi</th>
                            <th>Jenis Izin</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Disetujui Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leave_requests as $request)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</td>
                            <td>{{ $request->user->name ?? 'N/A' }}</td>
                            <td>{{ $request->user->division->name ?? 'Tanpa Divisi' }}</td>
                            <td>{{ \Carbon\Carbon::parse($request->date)->format('d/m/Y') }}</td>
                            <td>1 hari</td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($request->status->name ?? 'N/A') }}</span>
                            </td>
                            <td>
                                <span class="text-truncate d-inline-block" style="max-width: 200px;" title="{{ $request->note }}">
                                    {{ Str::limit($request->note ?? 'N/A', 50) }}
                                </span>
                            </td>
                            <td>
                                @if($request->approved_at)
                                    <span class="badge bg-success">Disetujui</span>
                                @elseif($request->rejected_at)
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-warning">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                @if($request->approved_by)
                                    {{ $request->approvedBy->name ?? 'N/A' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($request->approved_at)->format('d/m/Y H:i') }}
                                    </small>
                                @elseif($request->rejected_by)
                                    {{ $request->rejectedBy->name ?? 'N/A' }}
                                    <br>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($request->rejected_at)->format('d/m/Y H:i') }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($leave_requests, 'links'))
                <div class="d-flex justify-content-center mt-3">
                    {{ $leave_requests->appends(request()->query())->links() }}
                </div>
            @endif

            <!-- Summary -->
            @if(isset($leave_summary))
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Ringkasan Pengajuan Izin</h6>
                                <div class="row">
                                    <div class="col-md-3 col-6">
                                        <div class="text-center">
                                            <h5 class="text-primary mb-1">{{ $leave_summary['total'] ?? 0 }}</h5>
                                            <small class="text-muted">Total Pengajuan</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="text-center">
                                            <h5 class="text-warning mb-1">{{ $leave_summary['pending'] ?? 0 }}</h5>
                                            <small class="text-muted">Menunggu</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="text-center">
                                            <h5 class="text-success mb-1">{{ $leave_summary['approved'] ?? 0 }}</h5>
                                            <small class="text-muted">Disetujui</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6">
                                        <div class="text-center">
                                            <h5 class="text-danger mb-1">{{ $leave_summary['rejected'] ?? 0 }}</h5>
                                            <small class="text-muted">Ditolak</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="bx bx-file-blank fs-1 text-muted"></i>
                <p class="text-muted mt-2">Tidak ada data pengajuan izin untuk periode yang dipilih</p>
            </div>
        @endif
    </div>
</div>
