<!-- Employee Report -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Laporan Per Karyawan</h5>
    </div>
    <div class="card-body">
        @if(isset($employee_report) && $employee_report)
            <!-- Employee Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Informasi Karyawan</h6>
                            <p class="mb-1"><strong>Nama:</strong> {{ $employee_report['user']->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Divisi:</strong> {{ $employee_report['user']->division->name ?? 'Tanpa Divisi' }}</p>
                            <p class="mb-0"><strong>Jabatan:</strong> {{ $employee_report['user']->job_title->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Statistik Periode</h6>
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1"><strong>Hadir:</strong> <span class="badge bg-success">{{ $employee_report['statistics']['present'] }}</span></p>
                                    <p class="mb-0"><strong>Terlambat:</strong> <span class="badge bg-warning">{{ $employee_report['statistics']['late'] }}</span></p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1"><strong>Tidak Hadir:</strong> <span class="badge bg-danger">{{ $employee_report['statistics']['absent'] }}</span></p>
                                    <p class="mb-0"><strong>Izin:</strong> <span class="badge bg-info">{{ $employee_report['statistics']['on_leave'] }}</span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Details -->
            @if(isset($employee_report['attendances']) && $employee_report['attendances']->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Keluar</th>
                                <th>Total Jam</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employee_report['attendances'] as $attendance)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                                <td>
                                    @if($attendance->time_in)
                                        {{ \Carbon\Carbon::parse($attendance->time_in)->format('H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->time_out)
                                        {{ \Carbon\Carbon::parse($attendance->time_out)->format('H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($attendance->time_in && $attendance->time_out)
                                        @php
                                            $timeIn = \Carbon\Carbon::parse($attendance->time_in);
                                            $timeOut = \Carbon\Carbon::parse($attendance->time_out);
                                            $totalHours = $timeOut->diffInHours($timeIn);
                                            $totalMinutes = $timeOut->diffInMinutes($timeIn) % 60;
                                        @endphp
                                        {{ $totalHours }}j {{ $totalMinutes }}m
                                    @else
                                        <span class="text-muted">-</span>
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
                                <td>{{ $attendance->note ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($employee_report['attendances'], 'links'))
                    <div class="d-flex justify-content-center mt-3">
                        {{ $employee_report['attendances']->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-4">
                    <i class="bx bx-calendar-x fs-1 text-muted"></i>
                    <p class="text-muted mt-2">Tidak ada data absensi untuk karyawan ini pada periode yang dipilih</p>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="bx bx-user-x fs-1 text-muted"></i>
                <p class="text-muted mt-2">Silakan pilih karyawan untuk melihat laporan</p>
            </div>
        @endif
    </div>
</div>
