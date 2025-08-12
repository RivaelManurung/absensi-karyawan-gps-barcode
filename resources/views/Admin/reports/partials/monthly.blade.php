<!-- Monthly Report -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Laporan Bulanan</h5>
    </div>
    <div class="card-body">
        @if(isset($monthly_reports) && count($monthly_reports) > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Bulan</th>
                            <th>Total Hari Kerja</th>
                            <th>Total Kehadiran</th>
                            <th>Total Terlambat</th>
                            <th>Total Tidak Hadir</th>
                            <th>Total Izin</th>
                            <th>Persentase Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($monthly_reports as $report)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($report['month'])->format('F Y') }}</td>
                            <td>{{ $report['working_days'] }}</td>
                            <td>
                                <span class="badge bg-success">{{ $report['present'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning">{{ $report['late'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-danger">{{ $report['absent'] }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $report['on_leave'] }}</span>
                            </td>
                            <td>
                                @php
                                    $percentage = $report['working_days'] > 0 ? round(($report['present'] / ($report['working_days'] * ($statistics['total_employees'] ?? 1))) * 100, 2) : 0;
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
        @else
            <div class="text-center py-4">
                <i class="bx bx-calendar-x fs-1 text-muted"></i>
                <p class="text-muted mt-2">Tidak ada data laporan bulanan untuk periode yang dipilih</p>
            </div>
        @endif
    </div>
</div>
