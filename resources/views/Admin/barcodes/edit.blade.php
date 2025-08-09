<div class="modal fade" id="editBarcodeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ubah Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBarcodeForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- Input-input di bawah ini sekarang menggunakan old() --}}
                    {{-- Ini akan otomatis terisi jika ada error validasi --}}
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Lokasi</label>
                        <input type="text" class="form-control" id="edit_name" name="name" value="{{ old('name') }}" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit_value" class="form-label">Value Barcode/QR</label>
                        <input type="text" class="form-control" id="edit_value" name="value" value="{{ old('value') }}" required />
                    </div>
                     <div class="mb-3">
                        <label for="edit_latitude" class="form-label">Latitude</label>
                        <input type="text" class="form-control" id="edit_latitude" name="latitude" value="{{ old('latitude') }}" required />
                    </div>
                     <div class="mb-3">
                        <label for="edit_longitude" class="form-label">Longitude</label>
                        <input type="text" class="form-control" id="edit_longitude" name="longitude" value="{{ old('longitude') }}" required />
                    </div>
                    <div class="mb-3">
                        <label for="edit_radius" class="form-label">Radius Toleransi (meter)</label>
                        <input type="number" class="form-control" id="edit_radius" name="radius" value="{{ old('radius') }}" required />
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