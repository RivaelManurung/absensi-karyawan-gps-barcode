@extends('layouts.admin')

@section('title', 'Ubah Shift')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Shift /</span> Ubah</h4>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Terjadi Kesalahan!</strong> Mohon periksa kembali data yang Anda masukkan.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5>Ubah Shift</h5>
        </div>
        <div class="card-body">
            {{-- Form ini mengirim data ke metode 'update' di controller --}}
            <form action="{{ route('shifts.update', $shift->id) }}" method="POST">
                @csrf
                @method('PUT') {{-- Method untuk update --}}
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="name" class="form-label">Nama Shift</label>
                        {{-- Nilai input diisi dari data shift yang ada --}}
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $shift->name) }}" required />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label">Waktu Mulai</label>
                        <input type="time" class="form-control" id="start_time" name="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($shift->start_time)->format('H:i')) }}" required />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label">Waktu Selesai</label>
                        <input type="time" class="form-control" id="end_time" name="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($shift->end_time)->format('H:i')) }}" required />
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Perbarui Shift</button>
                    <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection