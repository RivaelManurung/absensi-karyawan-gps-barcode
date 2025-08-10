<div class="modal fade" id="createBarcodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Lokasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.barcodes.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="create_name" class="form-label">Nama Lokasi</label>
                        <input type="text" id="create_name" class="form-control" name="name" value="{{ old('name') }}" placeholder="Contoh: Kantor Pusat" required />
                    </div>
                    <div class="mb-3">
                        <label for="create_value" class="form-label">Value Barcode/QR</label>
                        <input type="text" id="create_value" class="form-control" name="value" value="{{ old('value') ?? \Illuminate\Support\Str::random(16) }}" required />
                    </div>
                     <div class="mb-3">
                        <label for="create_latitude" class="form-label">Latitude</label>
                        <input type="text" id="create_latitude" class="form-control" name="latitude" value="{{ old('latitude') }}" placeholder="Contoh: 2.9698" required />
                    </div>
                     <div class="mb-3">
                        <label for="create_longitude" class="form-label">Longitude</label>
                        <input type="text" id="create_longitude" class="form-control" name="longitude" value="{{ old('longitude') }}" placeholder="Contoh: 99.0645" required />
                    </div>
                    <div class="mb-3">
                        <label for="create_radius" class="form-label">Radius Toleransi (meter)</label>
                        <input type="number" id="create_radius" class="form-control" name="radius" value="{{ old('radius', 50) }}" placeholder="Contoh: 50" required />
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