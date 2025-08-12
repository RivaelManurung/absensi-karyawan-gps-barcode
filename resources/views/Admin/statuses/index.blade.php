@extends('admin.layout.main')
@section('title', 'Kelola Status')

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
            <h5 class="mb-0">Kelola Status</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createStatusModal">
                <i class="bx bx-plus me-1"></i> Tambah Status
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Status</th>
                        <th>Deskripsi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($statuses as $status)
                    <tr>
                        <td><strong>{{ $status->name }}</strong></td>
                        <td>{{ $status->description ?? '-' }}</td>
                        <td>
                            <div class="d-flex">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2 edit-btn"
                                    data-bs-toggle="modal" data-bs-target="#editStatusModal" data-id="{{ $status->id }}"
                                    data-name="{{ $status->name }}" data-description="{{ $status->description }}">
                                    Ubah
                                </button>
                                <form action="{{ route('admin.statuses.destroy', $status->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data status.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($statuses->hasPages())
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-center">
                {{ $statuses->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Memanggil file modal dari file terpisah --}}
@include('admin.statuses.create')
@include('admin.statuses.edit')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Script untuk mengisi data ke Modal Edit
        const editModal = document.getElementById('editStatusModal');
        const editForm = document.getElementById('editStatusForm');
        const editNameInput = document.getElementById('edit_name');
        const editDescriptionInput = document.getElementById('edit_description');

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const statusId = this.getAttribute('data-id');
                const statusName = this.getAttribute('data-name');
                const statusDescription = this.getAttribute('data-description');
                
                // Membuat URL action untuk form edit
                let updateUrl = "{{ route('admin.statuses.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', statusId);
                
                editForm.action = updateUrl;
                editNameInput.value = statusName;
                editDescriptionInput.value = statusDescription || '';
            });
        });

        // Script untuk membuka kembali modal jika ada error validasi dari server
        @if ($errors->any())
            var errorModalId = '';
            @if ($errors->hasBag('store'))
                errorModalId = '#createStatusModal';
            @elseif ($errors->hasBag('update'))
                 // Untuk membuka modal edit lagi, kita butuh tahu ID mana yang gagal
                 // Ini lebih kompleks dan biasanya lebih baik ditangani dengan AJAX
                 // Untuk sekarang, kita bisa tampilkan modal kosong atau yang terakhir di-klik
                 errorModalId = '#editStatusModal'; 
            @endif

            if(errorModalId) {
                var errorModal = new bootstrap.Modal(document.querySelector(errorModalId));
                errorModal.show();
            }
        @endif
    });
</script>
@endpush