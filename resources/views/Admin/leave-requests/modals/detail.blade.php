<!-- Detail Modal -->
<div class="modal fade" id="detailModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bx bx-info-circle me-2"></i>
                    Detail Pengajuan {{ $request->status ? $request->status->label : 'Unknown' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Informasi Karyawan -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Informasi Karyawan</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted" style="width: 120px;">Nama</td>
                                <td class="fw-semibold">{{ $request->user->name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Divisi</td>
                                <td>{{ $request->user->division->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jabatan</td>
                                <td>{{ $request->user->jobTitle->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $request->user->email }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Detail Pengajuan</h6>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted" style="width: 120px;">Tanggal</td>
                                <td class="fw-semibold">{{ \Carbon\Carbon::parse($request->date)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis</td>
                                <td>
                                    @if($request->status)
                                        <span class="badge px-3 py-2 rounded-pill" 
                                              style="background-color: {{ $request->status->color }}20; color: {{ $request->status->color }};">
                                            @if($request->status->icon)
                                                <i class="bx {{ $request->status->icon }} me-1"></i>
                                            @endif
                                            {{ $request->status->label }}
                                        </span>
                                    @else
                                        <span class="badge bg-label-secondary">Unknown</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Diajukan</td>
                                <td>{{ \Carbon\Carbon::parse($request->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>
                                    @if($request->approval_status === 'pending')
                                        <span class="badge bg-label-warning">
                                            <i class="bx bx-time me-1"></i>Menunggu Persetujuan
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
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-2">Keterangan</h6>
                    <div class="bg-light p-3 rounded">
                        <p class="mb-0">{{ $request->note }}</p>
                    </div>
                </div>

                <!-- Lampiran -->
                @if($request->attachment)
                <div class="mb-4">
                    <h6 class="fw-bold mb-2">Lampiran</h6>
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <i class="bx bx-paperclip fs-4 me-2 text-primary"></i>
                        <div>
                            <a href="{{ Storage::url($request->attachment) }}" target="_blank" class="text-decoration-none">
                                <div class="fw-semibold">{{ basename($request->attachment) }}</div>
                                <small class="text-muted">Klik untuk melihat lampiran</small>
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Informasi Approval -->
                @if($request->approved_at || $request->rejected_at)
                <div class="mb-4">
                    <h6 class="fw-bold mb-2">Informasi Persetujuan</h6>
                    <div class="bg-light p-3 rounded">
                        @if($request->approved_at)
                            <div class="d-flex align-items-center text-success mb-2">
                                <i class="bx bx-check-circle fs-5 me-2"></i>
                                <div>
                                    <div class="fw-semibold">Disetujui</div>
                                    <small>{{ \Carbon\Carbon::parse($request->approved_at)->format('d F Y, H:i') }}</small>
                                </div>
                            </div>
                            <div class="text-muted">
                                <small>Disetujui oleh: {{ $request->approvedBy->name ?? 'N/A' }}</small>
                            </div>
                        @endif

                        @if($request->rejected_at)
                            <div class="d-flex align-items-center text-danger mb-2">
                                <i class="bx bx-x-circle fs-5 me-2"></i>
                                <div>
                                    <div class="fw-semibold">Ditolak</div>
                                    <small>{{ \Carbon\Carbon::parse($request->rejected_at)->format('d F Y, H:i') }}</small>
                                </div>
                            </div>
                            <div class="text-muted mb-2">
                                <small>Ditolak oleh: {{ $request->rejectedBy->name ?? 'N/A' }}</small>
                            </div>
                            @if($request->rejection_reason)
                                <div class="mt-2">
                                    <strong>Alasan penolakan:</strong>
                                    <div class="bg-danger-subtle p-2 rounded mt-1">
                                        {{ $request->rejection_reason }}
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                @if($request->approval_status === 'pending')
                    <form action="{{ route('admin.leave-requests.approve', $request->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success" onclick="return confirm('Yakin ingin menyetujui pengajuan ini?')">
                            <i class="bx bx-check me-1"></i>Setujui
                        </button>
                    </form>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Tolak
                    </button>
                @endif
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
