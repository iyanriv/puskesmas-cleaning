<?php

namespace App\Exports;

use App\Models\PermintaanBarang;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PermintaanBarangExport implements
    FromCollection,
    ShouldAutoSize,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle
{
    protected $dari;
    protected $sampai;
    protected $status;

    public function __construct($dari, $sampai, $status = null)
    {
        $this->dari   = $dari;
        $this->sampai = $sampai;
        $this->status = $status;
    }

    public function title(): string
    {
        return 'Permintaan Barang';
    }

    public function collection()
    {
        $query = PermintaanBarang::with(['pengguna', 'barang'])
            ->whereBetween('waktu_request', [
                $this->dari . ' 00:00:00',
                $this->sampai . ' 23:59:59',
            ])
            ->latest('waktu_request');

        if ($this->status) {
            $query->where('status_request', $this->status);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Waktu Request',
            'Nama Petugas',
            'Nama Barang',
            'Jumlah',
            'Satuan',
            'Status',
            'Waktu Diproses',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $statusLabel = match ($row->status_request) {
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            'pending'   => 'Menunggu',
            default     => ucfirst($row->status_request ?? '-'),
        };

        return [
            $no,
            $row->waktu_request ? Carbon::parse($row->waktu_request)->format('d/m/Y H:i') : '-',
            $row->pengguna?->name ?? '-',
            $row->barang?->nama_barang ?? '-',
            $row->jumlah,
            $row->barang?->satuan ?? '-',
            $statusLabel,
            $row->waktu_approve ? Carbon::parse($row->waktu_approve)->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF2563EB'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}
