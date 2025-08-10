@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">Riwayat Absensi Saya</h4>
        <a href="{{ route('attendances.index') }}" class="btn btn-secondary">
            <i class="bx bx-arrow-back me-1"></i> Kembali ke Absensi
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Daftar Kehadiran</h5>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Pulang</th>
                        <th>Shift</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($history as $attendance)
                    <tr>
                        <td><strong>{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') }}</strong></td>
                        <td>{{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}</td>
                        <td>{{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}</td>
                        <td>{{ $attendance->shift->name ?? '-' }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'present' => ['class' => 'success', 'text' => 'Hadir'],
                                    'late' => ['class' => 'warning', 'text' => 'Terlambat'],
                                    'excused' => ['class' => 'info', 'text' => 'Izin'],
                                    'sick' => ['class' => 'danger', 'text' => 'Sakit'],
                                    'absent' => ['class' => 'secondary', 'text' => 'Alpa'],
                                ];
                            @endphp
                            <span class="badge bg-label-{{ $statusMap[$attendance->status]['class'] ?? 'dark' }}">
                                {{ $statusMap[$attendance->status]['text'] ?? Str::ucfirst($attendance->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada riwayat absensi.</td>
                    </tr>
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
@endsection