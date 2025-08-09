@extends('admin.layout.main')
@section('title', 'Kelola Divisi')

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
            <h5 class="mb-0">Kelola Divisi</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createDivisionModal">
                <i class="bx bx-plus me-1"></i> Tambah Divisi
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Divisi</th>
                        <th>Jumlah Karyawan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($divisions as $division)
                    <tr>
                        <td><strong>{{ $division->name }}</strong></td>
                        <td>{{ $division->users_count }}</td>
                        <td>
                             <div class="d-flex">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2 edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editDivisionModal"
                                    data-id="{{ $division->id }}"
                                    data-name="{{ $division->name }}">
                                    Ubah
                                </button>
                                <form action="{{ route('admin.divisions.destroy', $division->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data divisi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($divisions->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $divisions->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Memanggil file modal dari file terpisah --}}
@include('admin.divisions.create')
@include('admin.divisions.edit')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Script untuk mengisi data ke Modal Edit
        const editModal = document.getElementById('editDivisionModal');
        const editForm = document.getElementById('editDivisionForm');
        const editNameInput = document.getElementById('edit_name');

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const divisionId = this.getAttribute('data-id');
                const divisionName = this.getAttribute('data-name');
                
                // Membuat URL action untuk form edit
                let updateUrl = "{{ route('admin.divisions.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', divisionId);
                
                editForm.action = updateUrl;
                editNameInput.value = divisionName;
            });
        });

        // Script untuk membuka kembali modal jika ada error validasi dari server
        @if ($errors->any())
            var errorModalId = '';
            @if ($errors->hasBag('store'))
                errorModalId = '#createDivisionModal';
            @elseif ($errors->hasBag('update'))
                 // Untuk membuka modal edit lagi, kita butuh tahu ID mana yang gagal
                 // Ini lebih kompleks dan biasanya lebih baik ditangani dengan AJAX
                 // Untuk sekarang, kita bisa tampilkan modal kosong atau yang terakhir di-klik
                 errorModalId = '#editDivisionModal'; 
            @endif

            if(errorModalId) {
                var errorModal = new bootstrap.Modal(document.querySelector(errorModalId));
                errorModal.show();
            }
        @endif
    });
</script>
@endpush