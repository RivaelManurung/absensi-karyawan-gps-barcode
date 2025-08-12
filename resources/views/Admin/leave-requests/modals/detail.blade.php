<!-- Detail Modal -->
<div class="modal fade" id="detailModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-info-circle me-2"></i>
                    Detail Pengajuan Izin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Status Badge -->
                <div class="text-center mb-4">
                    @if($request->approved_at && !$request->rejected_at)
                        <span class="badge bg-success px-4 py-2 fs-6">
                            <i class="bx bx-check me-1"></i>Disetujui
                        </span>
                    @elseif($request->rejected_at)
                        <span class="badge bg-danger px-4 py-2 fs-6">
                            <i class="bx bx-x me-1"></i>Ditolak
                        </span>
                    @else
                        <span class="badge bg-warning px-4 py-2 fs-6">
                            <i class="bx bx-time me-1"></i>Pending
                        </span>
                    @endif
                </div>

                <!-- Informasi Karyawan -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-user me-2"></i>Informasi Karyawan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Nama</label>
                                    <div class="fw-semibold">{{ $request->user->name }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Email</label>
                                    <div>{{ $request->user->email }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Divisi</label>
                                    <div>{{ $request->user->division->name ?? 'N/A' }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Jabatan</label>
                                    <div>{{ $request->user->jobTitle->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Pengajuan -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-calendar me-2"></i>Detail Pengajuan
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Tanggal</label>
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($request->date)->format('d F Y') }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-muted">Jenis</label>
                                    <div>
                                        @if($request->request_type === 'sick')
                                            <span class="badge bg-label-danger">
                                                <i class="bx bx-plus-medical me-1"></i>Sakit
                                            </span>
                                        @elseif($request->request_type === 'excused')
                                            <span class="badge bg-label-info">
                                                <i class="bx bx-info-circle me-1"></i>Izin
                                            </span>
                                        @elseif($request->request_type === 'leave')
                                            <span class="badge bg-label-success">
                                                <i class="bx bx-calendar me-1"></i>Cuti
                                            </span>
                                        @else
                                            <span class="badge bg-label-warning">
                                                <i class="bx bx-hourglass me-1"></i>{{ ucfirst($request->request_type ?? 'Pending') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label text-muted">Diajukan</label>
                                    <div>{{ \Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:i') }}</div>
                                </div>
                                @if($request->approved_at)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Disetujui</label>
                                        <div class="text-success">
                                            {{ \Carbon\Carbon::parse($request->approved_at)->format('d/m/Y H:i') }}
                                            <br><small>oleh {{ $request->approvedBy->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                @elseif($request->rejected_at)
                                    <div class="mb-3">
                                        <label class="form-label text-muted">Ditolak</label>
                                        <div class="text-danger">
                                            {{ \Carbon\Carbon::parse($request->rejected_at)->format('d/m/Y H:i') }}
                                            <br><small>oleh {{ $request->rejectedBy->name ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-note me-2"></i>Keterangan
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $request->note }}</p>
                    </div>
                </div>

                <!-- Lampiran -->
                @if($request->attachment)
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bx bx-paperclip me-2"></i>Lampiran
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-file fs-4 me-2 text-primary"></i>
                            <div>
                                <a href="{{ Storage::url($request->attachment) }}" target="_blank" class="text-decoration-none">
                                    Lihat Lampiran
                                </a>
                                <br><small class="text-muted">Klik untuk membuka file</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Alasan Penolakan -->
                @if($request->rejected_at && $request->rejection_reason)
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="bx bx-x-circle me-2"></i>Alasan Penolakan
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $request->rejection_reason }}</p>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
