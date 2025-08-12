@extends('admin.layout.main')
@section('title', 'Manajemen Karyawan')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Berhasil!</strong> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Gagal!</strong> {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>@endif
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manajemen Karyawan</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal"><i class="bx bx-plus me-1"></i> Tambah Karyawan</button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Divisi</th>
                        <th>Jabatan</th>
                        <th>Shift</th>
                        <th>Grup</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($users as $user)
                    <tr>
                        <td><strong>{{ $user->name }}</strong></td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->division?->name ?? '-' }}</td>
                        <td>{{ $user->jobTitle?->name ?? '-' }}</td>
                        <td>{{ $user->shift?->name ?? '-' }}</td>
                        <td><span class="badge bg-label-primary me-1">{{ ucfirst($user->group) }}</span></td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item edit-btn"
                                        data-bs-toggle="modal" data-bs-target="#editUserModal"
                                        data-id="{{ $user->id }}"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-group="{{ $user->group }}"
                                        data-division_id="{{ $user->division_id }}"
                                        data-job_title_id="{{ $user->job_title_id }}"
                                        data-shift_id="{{ $user->shift_id }}">
                                        <i class="bx bx-edit-alt me-1"></i> Ubah
                                    </button>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus pengguna ini?');">
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
                        <td colspan="7" class="text-center">Tidak ada data pengguna.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-center">
                {{ $users->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@include('admin.users.create')
@include('admin.users.edit')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModalEl = document.getElementById('editUserModal');
        const editForm = document.getElementById('editUserForm');
        
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const data = this.dataset;
                let updateUrl = "{{ route('admin.users.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', data.id);
                
                editForm.action = updateUrl;
                editModalEl.querySelector('#edit_name').value = data.name;
                editModalEl.querySelector('#edit_email').value = data.email;
                editModalEl.querySelector('#edit_division_id').value = data.division_id;
                editModalEl.querySelector('#edit_job_title_id').value = data.job_title_id;
                editModalEl.querySelector('#edit_shift_id').value = data.shift_id;
                editModalEl.querySelector('#edit_group').value = data.group;
                editModalEl.querySelector('#edit_password').value = '';
                editModalEl.querySelector('#edit_password_confirmation').value = '';
            });
        });

        @if($errors->any())
            var errorModalId = '';
            @if ($errors->hasBag('store'))
                errorModalId = '#createUserModal';
            @elseif ($errors->hasBag('update') && session('failed_edit_id'))
                errorModalId = '#editUserModal';
                const failedId = "{{ session('failed_edit_id') }}";
                let errorUpdateUrl = "{{ route('admin.users.update', ':id') }}";
                editForm.action = errorUpdateUrl.replace(':id', failedId);
            @endif

            if(errorModalId) {
                var errorModal = new bootstrap.Modal(document.querySelector(errorModalId));
                errorModal.show();
            }
        @endif
    });
</script>
@endpush