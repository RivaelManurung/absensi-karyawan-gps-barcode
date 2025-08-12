<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            margin: 0;
            color: #333;
        }
        
        .header h2 {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }
        
        .info-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .info-label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            color: #495057;
        }
        
        .info-value {
            display: table-cell;
            width: 70%;
            color: #212529;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #4472C4;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        td {
            font-size: 10px;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        
        /* Page break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>SISTEM ABSENSI KARYAWAN</h1>
        <h2>{{ $title }}</h2>
    </div>
    
    <!-- Info Report -->
    <div class="info-box">
        <div class="info-row">
            <div class="info-label">Jenis Laporan:</div>
            <div class="info-value">{{ $reportTypeLabel }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Periode:</div>
            <div class="info-value">{{ $period }}</div>
        </div>
        @if($selectedDivision)
        <div class="info-row">
            <div class="info-label">Divisi:</div>
            <div class="info-value">{{ $selectedDivision }}</div>
        </div>
        @endif
        @if($selectedEmployee)
        <div class="info-row">
            <div class="info-label">Karyawan:</div>
            <div class="info-value">{{ $selectedEmployee }}</div>
        </div>
        @endif
        <div class="info-row">
            <div class="info-label">Tanggal Cetak:</div>
            <div class="info-value">{{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</div>
        </div>
    </div>
    
    <!-- Statistics Summary -->
    @if(isset($statistics))
    <div class="info-box">
        <h3 style="margin-top: 0; color: #333;">Ringkasan Statistik</h3>
        <div class="info-row">
            <div class="info-label">Total Karyawan:</div>
            <div class="info-value">{{ $statistics['total_employees'] ?? 0 }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Hadir Hari Ini:</div>
            <div class="info-value">{{ $statistics['present_today'] ?? 0 }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Izin Hari Ini:</div>
            <div class="info-value">{{ $statistics['on_leave_today'] ?? 0 }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Absen Hari Ini:</div>
            <div class="info-value">{{ $statistics['absent_today'] ?? 0 }}</div>
        </div>
    </div>
    @endif
    
    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="20%">Nama Karyawan</th>
                <th width="15%">Divisi</th>
                @if($reportType === 'leave-requests')
                    <th width="15%">Jenis Izin</th>
                    <th width="23%">Keterangan</th>
                    <th width="10%">Status</th>
                @else
                    <th width="8%">Masuk</th>
                    <th width="8%">Keluar</th>
                    <th width="10%">Status</th>
                    <th width="22%">Keterangan</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $attendance)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}</td>
                <td>{{ $attendance->user->name ?? 'N/A' }}</td>
                <td>{{ $attendance->user->division->name ?? 'Tanpa Divisi' }}</td>
                @if($reportType === 'leave-requests')
                    <td class="text-center">{{ $attendance->status->name ?? 'N/A' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($attendance->note ?? '', 50) }}</td>
                    <td class="text-center">
                        @if($attendance->approved_at)
                            <span class="badge badge-success">Disetujui</span>
                        @elseif($attendance->rejected_at)
                            <span class="badge badge-danger">Ditolak</span>
                        @else
                            <span class="badge badge-warning">Menunggu</span>
                        @endif
                    </td>
                @else
                    <td class="text-center">
                        {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}
                    </td>
                    <td class="text-center">
                        {{ $attendance->time_out ? \Carbon\Carbon::parse($attendance->time_out)->format('H:i') : '-' }}
                    </td>
                    <td class="text-center">
                        @if($attendance->status)
                            @if($attendance->status->name == 'present')
                                <span class="badge badge-success">Hadir</span>
                            @elseif($attendance->status->name == 'late')
                                <span class="badge badge-warning">Terlambat</span>
                            @elseif($attendance->status->name == 'absent')
                                <span class="badge badge-danger">Tidak Hadir</span>
                            @elseif(in_array($attendance->status->name, ['sick', 'leave', 'excused']))
                                <span class="badge badge-info">{{ ucfirst($attendance->status->name) }}</span>
                            @else
                                {{ ucfirst($attendance->status->name) }}
                            @endif
                        @else
                            N/A
                        @endif
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($attendance->note ?? '', 40) }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    
    @if($attendances->count() == 0)
    <div style="text-align: center; padding: 40px; color: #666;">
        <p>Tidak ada data untuk periode yang dipilih</p>
    </div>
    @endif
    
    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini dicetak pada {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
