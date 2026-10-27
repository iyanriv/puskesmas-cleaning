<?php

namespace App\Exports;

use App\Models\SetoranSampah;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SetoranSampahExport implements
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
        return 'Bank Sampah';
    }

    public function collection()
    {
        return SetoranSampah::with(['pengguna', 'validator'])
            ->whereBetween('tanggal', [$this->dari, $this->sampai])
            ->latest('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Petugas',
            'Lokasi Setor',
            'Jenis Sampah',
            'Catatan',
            'Status Validasi',
            'Validator',
            'Catatan Validasi',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $jenis = is_array($row->jenis_sampah)
            ? implode(', ', $row->jenis_sampah)
            : ($row->jenis_sampah ?? '-');

        $statusValidasi = match ($row->status_validasi) {
            'valid'   => 'Valid',
            'ditolak' => 'Ditolak',
            'menunggu'=> 'Menunggu',
            default   => 'Menunggu',
        };

        return [
            $no,
            $row->tanggal,
            $row->pengguna?->name ?? '-',
            $row->lokasi_setor ?? '-',
            $jenis,
            $row->catatan ?? '-',
            $statusValidasi,
            $row->validator?->name ?? '-',
            $row->catatan_validasi ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD97706'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
