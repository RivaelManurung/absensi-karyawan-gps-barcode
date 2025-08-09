@extends('user.layout.single-page') {{-- ✅ Menggunakan layout baru tanpa sidebar --}}

@section('title', 'Dashboard Karyawan')

@section('content')

{{-- Notifikasi Global --}}
@if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if (session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<ul class="nav nav-pills flex-column flex-md-row mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-attendance" type="button" role="tab" aria-selected="true">
            <i class="bx bx-fingerprint me-1"></i> Absensi Hari Ini
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-history" type="button" role="tab" aria-selected="false">
            <i class="bx bx-history me-1"></i> Riwayat Absensi
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-request" type="button" role="tab" aria-selected="false">
            <i class="bx bx-envelope me-1"></i> Ajukan Izin
        </button>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane fade show active" id="tab-attendance" role="tabpanel">
        <div class="card">
            <div class="card-body text-center">
                <h4 class="fw-bold mb-4">Absensi - {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</h4>
                 @if (!$todayAttendance)
                    {{-- Form Clock In --}}
                    <p>Silakan scan QR code di lokasi Anda dan isi form di bawah ini.</p>
                    <form action="{{ route('attendances.clockin') }}" method="POST" id="attendanceForm">
                        @csrf
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <div class="mb-3 mx-auto" style="max-width: 400px;">
                            <input type="text" name="barcode_value" class="form-control" placeholder="Masukkan Kode Lokasi" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">Absen Masuk</button>
                    </form>
                @elseif (!$todayAttendance->time_out)
                    {{-- Tombol Clock Out --}}
                    <h5 class="text-success">Absensi Masuk Berhasil!</h5>
                    <p>Tercatat masuk pada jam: <strong>{{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i:s') }}</strong></p>
                    <form action="{{ route('attendances.clockout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-lg">Absen Pulang</button>
                    </form>
                @else
                    {{-- Pesan Selesai --}}
                    <h5 class="text-success">Absensi Hari Ini Selesai</h5>
                    <p>Terima kasih, Anda sudah menyelesaikan absensi untuk hari ini.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="tab-history" role="tabpanel">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Daftar Riwayat Kehadiran</h5></div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th><th>Status</th><th>Jam Masuk/Pulang</th><th>Shift</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($history as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/y') }}</td>
                            <td>
                                @php $statusMap = ['present'=>'success','late'=>'warning','excused'=>'info','sick'=>'danger']; @endphp
                                <span class="badge bg-label-{{ $statusMap[$attendance->status] ?? 'dark' }}">{{ Str::ucfirst($attendance->status) }}</span>
                            </td>
                            <td>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }} / {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}</td>
                            <td>{{ $attendance->shift->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($history->hasPages())
            <div class="card-footer d-flex justify-content-center">
                {{ $history->links() }}
            </div>
            @endif
        </div>
    </div>

    <div class="tab-pane fade" id="tab-request" role="tabpanel">
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Form Pengajuan Izin / Sakit</h5></div>
            <div class="card-body">
                <p>Jika tidak dapat masuk kerja pada hari ini, silakan isi form di bawah ini.</p>
                <form action="{{ route('attendances.request.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="status" class="form-label">Jenis Pengajuan</label>
                        <select class="form-select @error('status') is-invalid @enderror" name="status" required>
                            <option value="">-- Pilih Jenis --</option>
                            <option value="excused" @if(old('status') == 'excused') selected @endif>Izin</option>
                            <option value="sick" @if(old('status') == 'sick') selected @endif>Sakit</option>
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Keterangan</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" name="note" rows="3" required>{{ old('note') }}</textarea>
                        @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="attachment" class="form-label">Lampiran (Opsional)</label>
                        <input class="form-control @error('attachment') is-invalid @enderror" type="file" name="attachment">
                        @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-info">Kirim Pengajuan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script untuk geolocation
    if (document.getElementById('attendanceForm')) {
        navigator.geolocation.getCurrentPosition(function(position) {
            document.getElementById('latitude').value = position.coords.latitude;
            document.getElementById('longitude').value = position.coords.longitude;
        });
    }

    // Script untuk mengaktifkan tab yang benar setelah redirect atau validasi error
    @if(session('active_tab') === 'request' || $errors->any())
        const triggerEl = document.querySelector('button[data-bs-target="#tab-request"]');
        if(triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }
    @endif
});
</script>
@endpush