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
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Gagal!</strong> {{ session('error') }}
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
                        <th>QR Code</th>
                        <th>Nama Lokasi</th>
                        <th>Value Barcode</th>
                        <th>Radius (meter)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($barcodes as $barcode)
                    <tr>
                        <td>
                            <div class="p-2 border rounded d-inline-block">
                                {!! QrCode::size(60)->generate($barcode->value) !!}
                            </div>
                        </td>
                        <td><strong>{{ $barcode->name }}</strong></td>
                        <td><code>{{ $barcode->value }}</code></td>
                        <td>{{ $barcode->radius }} meter</td>
                        <td>
                             <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('admin.barcodes.show-qr', $barcode->id) }}" target="_blank">
                                        <i class="bx bx-qr-scan me-1"></i> Lihat QR
                                    </a>
                                    <button class="dropdown-item edit-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editBarcodeModal"
                                        data-id="{{ $barcode->id }}"
                                        data-name="{{ $barcode->name }}"
                                        data-value="{{ $barcode->value }}"
                                        data-latitude="{{ $barcode->latitude }}"
                                        data-longitude="{{ $barcode->longitude }}"
                                        data-radius="{{ $barcode->radius }}">
                                        <i class="bx bx-edit-alt me-1"></i> Ubah
                                    </button>
                                    <form action="{{ route('admin.barcodes.destroy', $barcode->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus lokasi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger"><i class="bx bx-trash me-1"></i> Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data lokasi.</td>
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

        // Event listener standar dari Bootstrap 5 untuk mengisi modal saat akan ditampilkan
        editModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const data = button.dataset;
            const editForm = document.getElementById('editBarcodeForm');
            
            let updateUrl = "{{ route('admin.barcodes.update', ':id') }}";
            updateUrl = updateUrl.replace(':id', data.id);
            
            editForm.action = updateUrl;
            editModalEl.querySelector('#edit_name').value = data.name;
            editModalEl.querySelector('#edit_value').value = data.value;
            editModalEl.querySelector('#edit_latitude').value = data.latitude;
            editModalEl.querySelector('#edit_longitude').value = data.longitude;
            editModalEl.querySelector('#edit_radius').value = data.radius;
        });

        // Logika saat halaman reload setelah GAGAL VALIDASI
        @if(session('failed_edit_id') && $errors->hasBag('update'))
            const failedId = "{{ session('failed_edit_id') }}";
            const errorEditModal = new bootstrap.Modal(editModalEl);
            const editForm = document.getElementById('editBarcodeForm');
            
            let errorUpdateUrl = "{{ route('admin.barcodes.update', ':id') }}";
            errorUpdateUrl = errorUpdateUrl.replace(':id', failedId);
            editForm.action = errorUpdateUrl;
            
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