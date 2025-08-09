@extends('admin.layout.main')
@section('title', 'Kelola Lokasi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Berhasil!</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Kelola Lokasi (Barcode)</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBarcodeModal">
                <i class="bx bx-plus me-1"></i> Tambah Lokasi
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Lokasi</th>
                        <th>Value Barcode</th>
                        <th>Radius (meter)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($barcodes as $barcode)
                    <tr>
                        <td><strong>{{ $barcode->name }}</strong></td>
                        <td><code>{{ $barcode->value }}</code></td>
                        <td>{{ $barcode->radius }} meter</td>
                        <td>
                            <div class="d-flex">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2 edit-btn"
                                    data-bs-toggle="modal" data-bs-target="#editBarcodeModal"
                                    data-id="{{ $barcode->id }}" data-name="{{ $barcode->name }}"
                                    data-value="{{ $barcode->value }}" data-latitude="{{ $barcode->latitude }}"
                                    data-longitude="{{ $barcode->longitude }}" data-radius="{{ $barcode->radius }}">
                                    Ubah
                                </button>
                                <form action="{{ route('admin.barcodes.destroy', $barcode->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus lokasi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data lokasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($barcodes->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $barcodes->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Memanggil file modal dari file terpisah --}}
@include('admin.barcodes.create')
@include('admin.barcodes.edit')

@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModalEl = document.getElementById('editBarcodeModal');
        const editForm = document.getElementById('editBarcodeForm');
        
        // --- Bagian 1: Logika saat tombol "Ubah" di-klik (Happy Path) ---
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const data = this.dataset;
                
                let updateUrl = "{{ route('admin.barcodes.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', data.id);
                
                editForm.action = updateUrl;
                // Mengisi form dengan data LAMA dari tabel
                editModalEl.querySelector('#edit_name').value = data.name;
                editModalEl.querySelector('#edit_value').value = data.value;
                editModalEl.querySelector('#edit_latitude').value = data.latitude;
                editModalEl.querySelector('#edit_longitude').value = data.longitude;
                editModalEl.querySelector('#edit_radius').value = data.radius;
            });
        });

        // --- Bagian 2: Logika saat halaman reload setelah GAGAL VALIDASI ---
        @if(session('failed_edit_id') && $errors->hasBag('update'))
            // 1. Dapatkan ID yang gagal dari session
            const failedId = "{{ session('failed_edit_id') }}";
            
            // 2. Siapkan modal dan form
            const errorEditModal = new bootstrap.Modal(editModalEl);
            
            // 3. Atur action form-nya lagi
            let errorUpdateUrl = "{{ route('admin.barcodes.update', ':id') }}";
            errorUpdateUrl = errorUpdateUrl.replace(':id', failedId);
            editForm.action = errorUpdateUrl;
            
            // 4. Tampilkan modal
            // Input-nya akan otomatis terisi oleh helper old() dari Blade di edit.blade.php
            errorEditModal.show();
        @endif

        // Script untuk membuka modal TAMBAH jika gagal validasi
        @if ($errors->hasBag('store'))
            const addModal = new bootstrap.Modal(document.getElementById('createBarcodeModal'));
            addModal.show();
        @endif
    });
</script>
@endpush