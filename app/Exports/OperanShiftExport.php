<?php

namespace App\Exports;

use App\Models\OperanShift;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OperanShiftExport implements
    FromCollection,
    ShouldAutoSize,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle
{
    protected $dari;
    protected $sampai;

    public function __construct($dari, $sampai)
    {
        $this->dari   = $dari;
        $this->sampai = $sampai;
    }

    public function title(): string
    {
        return 'Operan Shift';
    }

    public function collection()
    {
        return OperanShift::with(['pengirim', 'penerima', 'parentOperan'])
            ->whereBetween('tanggal', [$this->dari, $this->sampai])
            ->latest('tanggal')
            ->latest('waktu')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Waktu',
            'Pengirim',
            'Penerima',
            'Tempat Tugas',
            'Waktu Jaga',
            'Catatan',
            'Status Terima',
            'Status Penyelesaian',
            'Eskalasi Dari',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $statusTerima = match ($row->status_terima) {
            'diterima'  => 'Diterima',
            'menunggu'  => 'Menunggu',
            default     => ucfirst($row->status_terima ?? '-'),
        };

        $statusSelesai = match ($row->status_penyelesaian) {
            'selesai'    => 'Selesai',
            'dieskalasi' => 'Dieskalasi',
            'belum'      => 'Belum Selesai',
            default      => ucfirst($row->status_penyelesaian ?? '-'),
        };

        return [
            $no,
            $row->tanggal,
            $row->waktu,
            $row->pengirim?->name ?? '-',
            $row->penerima?->name ?? '-',
            $row->tempat_tugas ?? '-',
            $row->waktu_jaga ?? '-',
            $row->catatan ?? '-',
            $statusTerima,
            $statusSelesai,
            $row->parentOperan?->pengirim?->name ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0EA5E9'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
