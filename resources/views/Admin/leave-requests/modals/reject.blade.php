<!-- Reject Modal -->
<div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="bx bx-x-circle me-2"></i>
                    Tolak Pengajuan {{ $request->status ? $request->status->label : 'Unknown' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.leave-requests.reject', $request->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <div class="d-flex align-items-center">
                            <i class="bx bx-error-circle fs-4 me-2"></i>
                            <div>
                                <strong>Konfirmasi Penolakan</strong>
                                <div class="mt-1">Anda akan menolak pengajuan dari <strong>{{ $request->user->name }}</strong> untuk tanggal <strong>{{ \Carbon\Carbon::parse($request->date)->format('d F Y') }}</strong>.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="rejection_reason{{ $request->id }}" class="form-label fw-semibold">
                            Alasan Penolakan <span class="text-danger">*</span>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bx bx-x me-1"></i>Tolak Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
