<div class="modal fade" id="createShiftModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Shift Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.shifts.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Shift</label>
                        <input type="text" class="form-control @if($errors->hasBag('store') && $errors->store->has('name')) is-invalid @endif" name="name" value="{{ old('name') }}" placeholder="Contoh: Shift Pagi" required />
                        @if($errors->hasBag('store') && $errors->store->has('name'))
                            <div class="invalid-feedback">{{ $errors->store->first('name') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Waktu Mulai</label>
                        <input type="time" class="form-control @if($errors->hasBag('store') && $errors->store->has('start_time')) is-invalid @endif" name="start_time" value="{{ old('start_time') }}" required />
                         @if($errors->hasBag('store') && $errors->store->has('start_time'))
                            <div class="invalid-feedback">{{ $errors->store->first('start_time') }}</div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="end_time" class="form-label">Waktu Selesai</label>
                        <input type="time" class="form-control @if($errors->hasBag('store') && $errors->store->has('end_time')) is-invalid @endif" name="end_time" value="{{ old('end_time') }}" required />
                         @if($errors->hasBag('store') && $errors->store->has('end_time'))
                            <div class="invalid-feedback">{{ $errors->store->first('end_time') }}</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>