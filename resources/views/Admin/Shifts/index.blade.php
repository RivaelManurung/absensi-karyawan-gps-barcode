@extends('admin.layout.main')
@section('title', 'Kelola Shift')

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
            <h5 class="mb-0">Kelola Shift Kerja</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createShiftModal">
                <i class="bx bx-plus me-1"></i> Tambah Shift
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Shift</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($shifts as $shift)
                    <tr>
                        <td><strong>{{ $shift->name }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}</td>
                        <td>
                            <div class="d-flex">
                                <button type="button" class="btn btn-sm btn-outline-primary me-2 edit-btn"
                                    data-bs-toggle="modal" data-bs-target="#editShiftModal" data-id="{{ $shift->id }}"
                                    data-name="{{ $shift->name }}"
                                    data-start_time="{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}"
                                    data-end_time="{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}">
                                    Ubah
                                </button>
                                <form action="{{ route('admin.shifts.destroy', $shift->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus shift ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data shift.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($shifts->hasPages())
        <div class="card-footer d-flex justify-content-end">
            {{ $shifts->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Memanggil file modal dari file terpisah --}}
@include('admin.shifts.create')
@include('admin.shifts.edit')

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModal = document.getElementById('editShiftModal');
        const editForm = document.getElementById('editShiftForm');
        
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function () {
                const shiftId = this.dataset.id;
                const shiftName = this.dataset.name;
                const shiftStartTime = this.dataset.start_time;
                const shiftEndTime = this.dataset.end_time;
                
                let updateUrl = "{{ route('admin.shifts.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', shiftId);
                
                editForm.action = updateUrl;
                editModal.querySelector('#edit_name').value = shiftName;
                editModal.querySelector('#edit_start_time').value = shiftStartTime;
                editModal.querySelector('#edit_end_time').value = shiftEndTime;
            });
        });

        // Script untuk membuka kembali modal jika ada error validasi
        @if ($errors->any())
            var errorModalId = '';
            @if ($errors->hasBag('store'))
                errorModalId = '#createShiftModal';
            @elseif ($errors->hasBag('update'))
                 errorModalId = '#editShiftModal'; 
            @endif

            if(errorModalId) {
                var errorModal = new bootstrap.Modal(document.querySelector(errorModalId));
                errorModal.show();
            }
        @endif
    });
</script>
@endpush