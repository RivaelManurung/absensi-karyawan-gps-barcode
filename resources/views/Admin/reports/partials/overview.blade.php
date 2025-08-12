<!-- Overview Report -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Ringkasan Absensi</h5>
    </div>
    <div class="card-body">
        @if(isset($attendances) && $attendances->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Karyawan</th>
                            <th>Divisi</th>
                            <th>Jam Masuk</th>
                            <th>Jam Keluar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $attendance)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                            <td>{{ $attendance->user->name ?? 'N/A' }}</td>
                            <td>{{ $attendance->user->division->name ?? 'Tanpa Divisi' }}</td>
                            <td>
                                @if($attendance->time_in)
                                    {{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}
                                @else
                                    <span class="badge bg-secondary">Belum Masuk</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->time_out)
                                    {{ \Carbon\Carbon::parse($attendance->time_out)->format('H:i') }}
                                @else
                                    <span class="badge bg-warning">Belum Keluar</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->status)
                                    @if($attendance->status->name == 'present')
                                        <span class="badge bg-success">Hadir</span>
                                    @elseif($attendance->status->name == 'late')
                                        <span class="badge bg-warning">Terlambat</span>
                                    @elseif($attendance->status->name == 'absent')
                                        <span class="badge bg-danger">Tidak Hadir</span>
                                    @elseif(in_array($attendance->status->name, ['sick', 'leave', 'excused']))
                                        <span class="badge bg-info">{{ ucfirst($attendance->status->name) }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($attendance->status->name) }}</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">N/A</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($attendances, 'links'))
                <div class="d-flex justify-content-center mt-3">
                    {{ $attendances->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="bx bx-calendar-x fs-1 text-muted"></i>
                <p class="text-muted mt-2">Tidak ada data absensi untuk periode yang dipilih</p>
            </div>
        @endif
    </div>
</div>
