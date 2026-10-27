<?php

namespace App\Exports;

use App\Models\PenilaianPjLantai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PenilaianPjExport implements
    FromCollection,
    ShouldAutoSize,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle
{
    protected $bulan;
    protected $tahun;
    protected $cari;

    public function __construct($bulan = null, $tahun = null, $cari = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
        $this->cari  = $cari;
    }

    public function title(): string
    {
        return 'Penilaian PJ Lantai';
    }

    public function collection()
    {
        $query = PenilaianPjLantai::with('petugasCs');

        if ($this->tahun) {
            $query->whereYear('tanggal_penilaian', $this->tahun);
        }
        if ($this->bulan !== '' && $this->bulan !== null) {
            $query->whereMonth('tanggal_penilaian', $this->bulan);
        }
        if ($this->cari) {
            $query->where(function ($q) {
                $q->where('nama_petugas_cs', 'like', "%{$this->cari}%")
                  ->orWhere('nama_pj', 'like', "%{$this->cari}%")
                  ->orWhere('lokasi_tugas', 'like', "%{$this->cari}%");
            });
        }

        return $query->latest('tanggal_penilaian')->latest('id')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Penilaian',
            'Nama Petugas CS',
            'Nama PJ Lantai',
            'Lokasi Tugas',
            'Rata-rata Skor',
            'Kategori',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $kategori = PenilaianPjLantai::hitungKategori($row->rata_rata);

        return [
            $no,
            $row->tanggal_penilaian,
            $row->petugasCs?->name ?? $row->nama_petugas_cs ?? '-',
            $row->nama_pj ?? '-',
            $row->lokasi_tugas ?? '-',
            $row->rata_rata,
            $kategori,
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
