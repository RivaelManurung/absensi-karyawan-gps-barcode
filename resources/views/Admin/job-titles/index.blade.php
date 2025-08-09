@extends('admin.layout.main')
@section('title', 'Kelola Jabatan')

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
            <h5 class="mb-0">Kelola Jabatan</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createJobTitleModal">
                <i class="bx bx-plus me-1"></i> Tambah Jabatan
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Jabatan</th>
                        <th>Jumlah Karyawan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($jobTitles as $jobTitle)
                    <tr>
                        <td><strong>{{ $jobTitle->name }}</strong></td>
                        <td>{{ $jobTitle->users_count }}</td>
                        <td>
                             <div class="d-flex">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2 edit-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editJobTitleModal"
                                    data-id="{{ $jobTitle->id }}"
                                    data-name="{{ $jobTitle->name }}">
                                    Ubah
                                </button>
                                <form action="{{ route('admin.job-titles.destroy', $jobTitle->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada data jabatan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($jobTitles->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $jobTitles->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Memanggil file modal dari file terpisah --}}
@include('admin.job-titles.create')
@include('admin.job-titles.edit')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editJobTitleModal');
        const editForm = document.getElementById('editJobTitleForm');
        const editNameInput = document.getElementById('edit_name');

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const jobTitleId = this.getAttribute('data-id');
                const jobTitleName = this.getAttribute('data-name');
                
                let updateUrl = "{{ route('admin.job-titles.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', jobTitleId);
                
                editForm.action = updateUrl;
                editNameInput.value = jobTitleName;
            });
        });
    });
</script>
@endpush