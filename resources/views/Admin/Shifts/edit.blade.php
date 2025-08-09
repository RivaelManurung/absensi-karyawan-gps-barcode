<div class="modal fade" id="editShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Shift</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editShiftForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Shift</label>
                        <input type="text" class="form-control @if($errors->hasBag('update') && $errors->update->has('name')) is-invalid @endif" id="edit_name" name="name" required />
                        @if($errors->hasBag('update') && $errors->update->has('name'))
                            <div class="invalid-feedback">{{ $errors->update->first('name') }}</div>
                        @endif
                    </div>
                     <div class="mb-3">
                        <label for="edit_start_time" class="form-label">Waktu Mulai</label>
                        <input type="time" class="form-control @if($errors->hasBag('update') && $errors->update->has('start_time')) is-invalid @endif" id="edit_start_time" name="start_time" required />
                        @if($errors->hasBag('update') && $errors->update->has('start_time'))
                            <div class="invalid-feedback">{{ $errors->update->first('start_time') }}</div>
                        @endif
                    </div>
                     <div class="mb-3">
                        <label for="edit_end_time" class="form-label">Waktu Selesai</label>
                        <input type="time" class="form-control @if($errors->hasBag('update') && $errors->update->has('end_time')) is-invalid @endif" id="edit_end_time" name="end_time" required />
                        @if($errors->hasBag('update') && $errors->update->has('end_time'))
                            <div class="invalid-feedback">{{ $errors->update->first('end_time') }}</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>