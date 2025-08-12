<div class="modal fade" id="createStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Status Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.statuses.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Status</label>
                        <input type="text" class="form-control @if($errors->hasBag('store')) is-invalid @endif" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Pending" required />
                        @if($errors->hasBag('store'))
                            @foreach($errors->store->get('name') as $message)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @endforeach
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control @if($errors->hasBag('store')) is-invalid @endif" id="description" name="description" placeholder="Contoh: Status untuk izin yang sedang menunggu persetujuan">{{ old('description') }}</textarea>
                        @if($errors->hasBag('store'))
                            @foreach($errors->store->get('description') as $message)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @endforeach
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
