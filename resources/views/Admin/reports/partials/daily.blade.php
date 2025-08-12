<!-- Daily Report -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Laporan Harian</h5>
    </div>
    <div class="card-body">
        @if(isset($daily_reports) && count($daily_reports) > 0)
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Total Karyawan</th>
                            <th>Hadir</th>
                            <th>Terlambat</th>
                            <th>Tidak Hadir</th>
                            <th>Izin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($daily_reports as $report)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($report['date'])->format('d/m/Y') }}</td>
                            <td>{{ $report['total_employees'] }}</td>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="bx bx-calendar-x fs-1 text-muted"></i>
                <p class="text-muted mt-2">Tidak ada data laporan harian untuk periode yang dipilih</p>
            </div>
        @endif
    </div>
</div>
