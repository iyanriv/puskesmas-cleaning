<?php

namespace App\Exports;

use App\Models\MutasiStok;
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
use PhpOffice\PhpSpreadsheet\Style\Border;

class BarangKeluarExport implements
    FromCollection,
    ShouldAutoSize,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle
{
    protected $dari;
    protected $sampai;
    protected $barangId;
    protected $unit;

    public function __construct($dari, $sampai, $barangId = null, $unit = null)
    {
        $this->dari     = $dari;
        $this->sampai   = $sampai;
        $this->barangId = $barangId;
        $this->unit     = $unit;
    }

    public function title(): string
    {
        return 'Laporan Barang Keluar';
    }

    public function collection()
    {
        $query = MutasiStok::with(['barang', 'petugas'])
            ->where('jumlah_keluar', '>', 0)
            ->whereBetween('tanggal', [$this->dari, $this->sampai])
            ->orderBy('tanggal')
            ->orderBy('id');

        if ($this->barangId) {
            $query->where('barang_id', $this->barangId);
        }

        if ($this->unit) {
            $query->where('unit', 'like', "%{$this->unit}%");
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'No. Transaksi',
            'Tanggal',
            'Kode Barang',
            'Nama Barang',
            'Satuan',
            'Jumlah Keluar',
            'Saldo Sebelum',
            'Saldo Sesudah',
            'Unit / Tujuan',
            'Referensi',
            'Keterangan',
            'Petugas Input',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $row->nomor_transaksi,
            $row->tanggal ? Carbon::parse($row->tanggal)->format('d/m/Y') : '-',
            $row->barang?->kode_barang ?? '-',
            $row->barang?->nama_barang ?? '-',
            $row->barang?->satuan ?? '-',
            $row->jumlah_keluar,
            $row->saldo_sebelumnya,
            $row->saldo_setelahnya,
            $row->unit ?? '-',
            $row->nomor_referensi ?? '-',
            $row->keterangan ?? '-',
            $row->petugas?->name ?? 'Admin / Gudang',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF205F63'], // Teal SIKAP Bersih
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
