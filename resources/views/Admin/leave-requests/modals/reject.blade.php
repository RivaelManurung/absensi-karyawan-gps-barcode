<!-- Reject Modal -->
<div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title text-danger">
                    <i class="bx bx-x-circle me-2"></i>
                    Tolak Pengajuan Izin
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.leave-requests.reject', $request->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <!-- Warning Alert -->
                    <div class="alert alert-warning d-flex align-items-start">
                        <i class="bx bx-error-circle fs-4 me-3 mt-1"></i>
                        <div>
                            <h6 class="alert-heading mb-2">Konfirmasi Penolakan</h6>
                            <p class="mb-0">
                                Anda akan menolak pengajuan dari <strong>{{ $request->user->name }}</strong> 
                                untuk tanggal <strong>{{ \Carbon\Carbon::parse($request->date)->format('d F Y') }}</strong>.
                            </p>
                        </div>
                    </div>

                    <!-- Employee Info Card -->
                    <div class="card mb-3">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-sm me-3">
                                    <span class="avatar-initial rounded-circle bg-label-primary">
                                        {{ substr($request->user->name, 0, 1) }}
                                    </span>
                                </span>
                                <div>
                                    <h6 class="mb-0">{{ $request->user->name }}</h6>
                                    <small class="text-muted">{{ $request->user->division->name ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rejection Reason -->
                    <div class="mb-3">
                        <label for="rejection_reason{{ $request->id }}" class="form-label">
                            <i class="bx bx-note me-1"></i>Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="rejection_reason{{ $request->id }}" 
                                  name="rejection_reason" 
                                  rows="4" 
                                  placeholder="Jelaskan alasan mengapa pengajuan ini ditolak..."
                                  required></textarea>
                        <div class="form-text">
                            <i class="bx bx-info-circle me-1"></i>
                            Alasan ini akan dilihat oleh karyawan yang mengajukan.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-check me-1"></i>Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
