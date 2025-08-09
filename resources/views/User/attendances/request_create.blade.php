@extends('user.layout.app') {{-- Ganti dengan layout utama Anda --}}

@section('title', 'Pengajuan Izin/Sakit')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Absensi /</span> Form Pengajuan Izin atau Sakit</h4>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Form Pengajuan</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('attendances.request.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="status" class="form-label">Jenis Pengajuan</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="excused" {{ old('status') == 'excused' ? 'selected' : '' }}>Izin</option>
                        <option value="sick" {{ old('status') == 'sick' ? 'selected' : '' }}>Sakit</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="note" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="3" placeholder="Jelaskan alasan Anda..." required>{{ old('note') }}</textarea>
                    @error('note')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="attachment" class="form-label">Lampiran (Opsional)</label>
                    <input class="form-control @error('attachment') is-invalid @enderror" type="file" id="attachment" name="attachment">
                    <div class="form-text">Contoh: Surat dokter, surat izin, dll. (Maksimal 2MB)</div>
                     @error('attachment')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                    <a href="{{ route('attendances.index') }}" class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection