<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class AttendanceReportExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $attendances;
    protected $reportType;
    protected $title;

    public function __construct($attendances, $reportType, $title = 'Laporan Absensi')
    {
        $this->attendances = $attendances;
        $this->reportType = $reportType;
        $this->title = $title;
    }

    public function collection()
    {
        if ($this->reportType === 'leave-requests') {
            return $this->attendances->map(function ($attendance, $index) {
                return [
                    'no' => $index + 1,
                    'tanggal' => Carbon::parse($attendance->date)->format('d/m/Y'),
                    'nama_karyawan' => $attendance->user->name ?? '',
                    'divisi' => $attendance->user->division->name ?? '',
                    'jabatan' => $attendance->user->jobTitle->name ?? '',
                    'jenis_izin' => ucfirst($attendance->status->name ?? ''),
                    'keterangan' => $attendance->note ?? '',
                    'status_approval' => $attendance->approved_at ? 'Disetujui' : ($attendance->rejected_at ? 'Ditolak' : 'Menunggu'),
                    'disetujui_oleh' => $attendance->approved_at ? ($attendance->approvedBy->name ?? '') : ($attendance->rejected_at ? ($attendance->rejectedBy->name ?? '') : ''),
                    'tanggal_approval' => $attendance->approved_at ? Carbon::parse($attendance->approved_at)->format('d/m/Y H:i') : ($attendance->rejected_at ? Carbon::parse($attendance->rejected_at)->format('d/m/Y H:i') : '')
                ];
            });
        } else {
            return $this->attendances->map(function ($attendance, $index) {
                $duration = '';
                if ($attendance->time_in && $attendance->time_out) {
                    $timeIn = Carbon::parse($attendance->time_in);
                    $timeOut = Carbon::parse($attendance->time_out);
                    $diff = $timeOut->diff($timeIn);
                    $duration = $diff->format('%h jam %i menit');
                }

                return [
                    'no' => $index + 1,
                    'tanggal' => Carbon::parse($attendance->date)->format('d/m/Y'),
                    'nama_karyawan' => $attendance->user->name ?? '',
                    'divisi' => $attendance->user->division->name ?? '',
                    'jabatan' => $attendance->user->jobTitle->name ?? '',
                    'jam_masuk' => $attendance->time_in ? Carbon::parse($attendance->time_in)->format('H:i') : '',
                    'jam_keluar' => $attendance->time_out ? Carbon::parse($attendance->time_out)->format('H:i') : '',
                    'durasi' => $duration,
                    'status' => ucfirst($attendance->status->name ?? ''),
                    'keterangan' => $attendance->note ?? ''
                ];
            });
        }
    }

    public function headings(): array
    {
        if ($this->reportType === 'leave-requests') {
            return [
                'No',
                'Tanggal',
                'Nama Karyawan',
                'Divisi',
                'Jabatan',
                'Jenis Izin',
                'Keterangan',
                'Status Approval',
                'Disetujui Oleh',
                'Tanggal Approval'
            ];
        } else {
            return [
                'No',
                'Tanggal',
                'Nama Karyawan',
                'Divisi',
                'Jabatan',
                'Jam Masuk',
                'Jam Keluar',
                'Durasi',
                'Status',
                'Keterangan'
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:' . chr(65 + count($this->headings()) - 1) . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style untuk data
        $lastRow = $this->attendances->count() + 1;
        $sheet->getStyle('A2:' . chr(65 + count($this->headings()) - 1) . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Auto height untuk rows
        for ($i = 1; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1);
        }

        return [];
    }

    public function columnWidths(): array
    {
        if ($this->reportType === 'leave-requests') {
            return [
                'A' => 5,   // No
                'B' => 15,  // Tanggal
                'C' => 25,  // Nama Karyawan
                'D' => 20,  // Divisi
                'E' => 20,  // Jabatan
                'F' => 15,  // Jenis Izin
                'G' => 30,  // Keterangan
                'H' => 15,  // Status Approval
                'I' => 20,  // Disetujui Oleh
                'J' => 20   // Tanggal Approval
            ];
        } else {
            return [
                'A' => 5,   // No
                'B' => 15,  // Tanggal
                'C' => 25,  // Nama Karyawan
                'D' => 20,  // Divisi
                'E' => 20,  // Jabatan
                'F' => 12,  // Jam Masuk
                'G' => 12,  // Jam Keluar
                'H' => 15,  // Durasi
                'I' => 15,  // Status
                'J' => 30   // Keterangan
            ];
        }
    }

    public function title(): string
    {
        return $this->title;
    }
}
