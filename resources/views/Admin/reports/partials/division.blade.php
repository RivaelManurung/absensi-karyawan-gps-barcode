<!-- Division Report -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Laporan Per Divisi</h5>
    </div>
    <div class="card-body">
        @if(isset($division_reports) && count($division_reports) > 0)
            @foreach($division_reports as $division_report)
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">{{ $division_report['division']->name ?? 'Tanpa Divisi' }}</h6>
                            <span class="badge bg-primary">{{ $division_report['total_employees'] }} Karyawan</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Statistics Row -->
                        <div class="row mb-3">
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <h5 class="text-success mb-1">{{ $division_report['statistics']['present'] }}</h5>
                                    <small class="text-muted">Hadir</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <h5 class="text-warning mb-1">{{ $division_report['statistics']['late'] }}</h5>
                                    <small class="text-muted">Terlambat</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <h5 class="text-danger mb-1">{{ $division_report['statistics']['absent'] }}</h5>
                                    <small class="text-muted">Tidak Hadir</small>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="text-center">
                                    <h5 class="text-info mb-1">{{ $division_report['statistics']['on_leave'] }}</h5>
                                    <small class="text-muted">Izin</small>
                                </div>
                            </div>
                        </div>

                        <!-- Employee List -->
                        @if(isset($division_report['employees']) && count($division_report['employees']) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nama Karyawan</th>
                                            <th>Jabatan</th>
                                            <th>Hadir</th>
                                            <th>Terlambat</th>
                                            <th>Tidak Hadir</th>
                                            <th>Izin</th>
                                            <th>Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($division_report['employees'] as $employee)
                                        <tr>
                                            <td>{{ $employee['user']->name }}</td>
                                            <td>{{ $employee['user']->job_title->name ?? 'N/A' }}</td>
                                            <td><span class="badge bg-success">{{ $employee['statistics']['present'] }}</span></td>
                                            <td><span class="badge bg-warning">{{ $employee['statistics']['late'] }}</span></td>
                                            <td><span class="badge bg-danger">{{ $employee['statistics']['absent'] }}</span></td>
                                            <td><span class="badge bg-info">{{ $employee['statistics']['on_leave'] }}</span></td>
                                            <td>
                                                @php
                                                    $total = $employee['statistics']['present'] + $employee['statistics']['late'] + $employee['statistics']['absent'] + $employee['statistics']['on_leave'];
                                                    $percentage = $total > 0 ? round((($employee['statistics']['present'] + $employee['statistics']['late']) / $total) * 100, 1) : 0;
                                                @endphp
                                                <span class="badge {{ $percentage >= 80 ? 'bg-success' : ($percentage >= 60 ? 'bg-warning' : 'bg-danger') }}">
                                                    {{ $percentage }}%
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="text-center py-4">
                <i class="bx bx-buildings fs-1 text-muted"></i>
                <p class="text-muted mt-2">Tidak ada data laporan divisi untuk periode yang dipilih</p>
            </div>
        @endif
    </div>
</div>
