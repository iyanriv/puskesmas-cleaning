<?php

namespace App\Exports;

use App\Models\LaporanStok;
use App\Models\ProdukKebersihan;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanStokExport implements FromArray, WithTitle, WithColumnWidths, WithStyles, WithEvents
{
    private int    $bulan;
    private int    $tahun;
    private string $filterUnit;
    private int    $filterMinggu;
    private array  $daftarUnit;
    private array  $daftarMinggu;
    private array  $namaBulan;
    private bool   $isFilteredUnit;

    // Baris awal konten (setelah header laporan)
    private int $barisHeaderTabel = 5;
    // Jumlah baris data (diisi saat build)
    private int $jumlahBarisData  = 0;

    public function __construct(int $bulan, int $tahun, string $filterUnit = '', int $filterMinggu = 0)
    {
        $this->bulan        = $bulan;
        $this->tahun        = $tahun;
        $this->filterUnit   = $filterUnit;
        $this->filterMinggu = $filterMinggu;
        $allUnits           = ProdukKebersihan::$daftarUnit;
        $this->isFilteredUnit = $filterUnit && in_array($filterUnit, $allUnits);

        $this->daftarUnit   = $this->isFilteredUnit ? [$filterUnit] : $allUnits;
        $this->daftarMinggu = ($this->isFilteredUnit && $filterMinggu) ? [$filterMinggu] : [1, 2, 3, 4];

        $this->namaBulan  = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
    }

    public function title(): string
    {
        $t = 'Laporan Stok ' . $this->namaBulan[$this->bulan] . ' ' . $this->tahun;
        if ($this->isFilteredUnit) {
            $t .= ' - ' . $this->filterUnit;
        }
        return mb_substr($t, 0, 31);
    }

    public function array(): array
    {
        $query = LaporanStok::with(['produk', 'pemakaian'])
            ->where('periode_bulan', $this->bulan)
            ->where('periode_tahun', $this->tahun);

        if ($this->isFilteredUnit) {
            $query->whereHas('pemakaian', function ($q) {
                $q->where('unit', $this->filterUnit)->where('jumlah', '>', 0);
                if ($this->filterMinggu) {
                    $q->where('minggu', $this->filterMinggu);
                }
            });
        }

        $laporan = $query->orderBy('id')->get();
        $this->jumlahBarisData = $laporan->count();

        $judulLaporan = 'STOK BARANG KEBERSIHAN PUSKESMAS CEMPAKA PUTIH '
            . strtoupper($this->namaBulan[$this->bulan]) . ' TAHUN ' . $this->tahun;
        if ($this->isFilteredUnit) {
            $judulLaporan .= ' - ' . strtoupper($this->filterUnit);
            if ($this->filterMinggu) {
                $judulLaporan .= ' (WEEK ' . $this->filterMinggu . ')';
            }
        }

        $rows = [];

        // ---- Baris 1: Judul utama ----
        $rows[] = [$judulLaporan];
        $subJudul = 'Periode: ' . $this->namaBulan[$this->bulan] . ' ' . $this->tahun;
        if ($this->isFilteredUnit) {
            $subJudul .= ' | Unit: ' . $this->filterUnit . ($this->filterMinggu ? ' (Week ' . $this->filterMinggu . ')' : ' (Semua Minggu)');
        }
        $rows[] = [$subJudul];
        $rows[] = []; // kosong

        // ---- Baris 4: Judul bagian tabel ----
        $rows[] = [$this->isFilteredUnit ? 'A. TABEL PEMAKAIAN & SISA STOK' : 'A. TABEL STOK DAN PEMAKAIAN'];

        // ---- Baris 5: Header tabel (baris 1 dari 2) ----
        $header1 = ['No', 'Nama Barang', 'Kode', 'Satuan', 'Tanggal', 'Stok/Masuk'];
        $mingguCount = count($this->daftarMinggu);
        foreach ($this->daftarUnit as $unit) {
            $header1[] = $unit;
            for ($i = 1; $i < $mingguCount; $i++) {
                $header1[] = '';
            }
        }
        $header1[] = 'Total Pemakaian';
        $header1[] = 'Sisa Stok';
        $rows[] = $header1;

        // ---- Baris 6: Header minggu ----
        $header2 = ['', '', '', '', '', ''];
        foreach ($this->daftarUnit as $unit) {
            foreach ($this->daftarMinggu as $w) {
                $header2[] = 'Wk ' . $w;
            }
        }
        $header2[] = '';
        $header2[] = '';
        $rows[] = $header2;

        // ---- Data ----
        foreach ($laporan as $no => $item) {
            $grid = $item->pemakaianPerUnitMinggu();
            $row  = [
                $no + 1,
                $item->produk->nama_barang,
                $item->produk->kode_barang,
                $item->produk->satuan,
                $item->tanggal ? $item->tanggal->format('d/m/Y') : '-',
                $item->stok_masuk,
            ];
            $totalPakaiBaris = 0;
            foreach ($this->daftarUnit as $unit) {
                foreach ($this->daftarMinggu as $w) {
                    $jml = $grid[$unit][$w] ?? 0;
                    $row[] = $jml;
                    $totalPakaiBaris += $jml;
                }
            }
            $row[] = $this->isFilteredUnit ? $totalPakaiBaris : $item->totalPemakaian();
            $row[] = $item->sisaStok();
            $rows[] = $row;
        }

        // ---- Bagian B: Rekap (Hanya jika cetak semua unit) ----
        if (!$this->isFilteredUnit) {
            $rows[] = [];
            $rows[] = [];
            $rows[] = ['B. REKAP PEMAKAIAN BARANG BERDASARKAN UNIT'];

            $headerRekap = ['No', 'Nama Barang', 'Kode', 'Satuan'];
            foreach ($this->daftarUnit as $unit) {
                $headerRekap[] = $unit;
            }
            $headerRekap[] = 'Total';
            $rows[] = $headerRekap;

            foreach ($laporan as $no => $item) {
                $perUnit = $item->pemakaianPerUnit();
                $row = [
                    $no + 1,
                    $item->produk->nama_barang,
                    $item->produk->kode_barang,
                    $item->produk->satuan,
                ];
                foreach ($this->daftarUnit as $unit) {
                    $row[] = $perUnit[$unit] ?? 0;
                }
                $row[] = array_sum($perUnit);
                $rows[] = $row;
            }
        }

        // ---- Keterangan cetak ----
        $rows[] = [];
        $rows[] = ['Dicetak pada: ' . now()->format('d/m/Y H:i') . ' WIB'];

        return $rows;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,   // No
            'B' => 24,  // Nama Barang
            'C' => 12,  // Kode
            'D' => 10,  // Satuan
            'E' => 12,  // Tanggal
            'F' => 12,  // Stok/Masuk
        ];

        $kolom = 7;
        foreach ($this->daftarUnit as $unit) {
            foreach ($this->daftarMinggu as $w) {
                $huruf = $this->kolomKeHuruf($kolom);
                $widths[$huruf] = $this->isFilteredUnit ? 11 : 7;
                $kolom++;
            }
        }

        // Total Pemakaian & Sisa Stok
        $widths[$this->kolomKeHuruf($kolom)]     = 14;
        $widths[$this->kolomKeHuruf($kolom + 1)] = 10;

        return $widths;
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet        = $event->sheet->getDelegate();
                $jumlahUnit   = count($this->daftarUnit);
                $jumlahMinggu = count($this->daftarMinggu);
                $totalKolom   = 6 + ($jumlahUnit * $jumlahMinggu) + 2; // info + pemakaian + total + sisa
                $kolAkhir     = $this->kolomKeHuruf($totalKolom);

                // ---- Merge judul utama ----
                $sheet->mergeCells("A1:{$kolAkhir}1");
                $sheet->mergeCells("A2:{$kolAkhir}2");
                $sheet->mergeCells("A4:{$kolAkhir}4");

                // Style judul
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1A6B3A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['rgb' => '555555']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A4')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A6B3A']],
                ]);

                // ---- Merge header tabel: kolom tetap (rowspan 2) ----
                $fixedCols = ['A', 'B', 'C', 'D', 'E', 'F'];
                foreach ($fixedCols as $c) {
                    $sheet->mergeCells("{$c}5:{$c}6");
                }
                // Merge Total & Sisa (colspan 1, rowspan 2)
                $kolTotal = $this->kolomKeHuruf($totalKolom - 1);
                $kolSisa  = $this->kolomKeHuruf($totalKolom);
                $sheet->mergeCells("{$kolTotal}5:{$kolTotal}6");
                $sheet->mergeCells("{$kolSisa}5:{$kolSisa}6");

                // Merge header unit (colspan = $jumlahMinggu jika > 1)
                $startKol = 7;
                foreach ($this->daftarUnit as $unit) {
                    if ($jumlahMinggu > 1) {
                        $awal  = $this->kolomKeHuruf($startKol);
                        $akhir = $this->kolomKeHuruf($startKol + $jumlahMinggu - 1);
                        $sheet->mergeCells("{$awal}5:{$akhir}5");
                    }
                    $startKol += $jumlahMinggu;
                }

                // ---- Style header tabel (baris 5-6) ----
                $sheet->getStyle("A5:{$kolAkhir}6")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '1A4D2E']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D4EDDA']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
                ]);

                // ---- Style data tabel ----
                $barisDataMulai = 7;
                $barisDataAkhir = 6 + $this->jumlahBarisData;

                if ($barisDataAkhir >= $barisDataMulai) {
                    $sheet->getStyle("A{$barisDataMulai}:{$kolAkhir}{$barisDataAkhir}")->applyFromArray([
                        'font'      => ['size' => 8],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                    ]);

                    // Center kolom numerik
                    for ($r = $barisDataMulai; $r <= $barisDataAkhir; $r++) {
                        $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        // Kolom pemakaian
                        for ($c = 7; $c <= $totalKolom; $c++) {
                            $sheet->getStyle($this->kolomKeHuruf($c) . $r)
                                  ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        }
                    }

                    // Zebra striping
                    for ($r = $barisDataMulai; $r <= $barisDataAkhir; $r += 2) {
                        $sheet->getStyle("A{$r}:{$kolAkhir}{$r}")->getFill()
                              ->setFillType(Fill::FILL_SOLID)
                              ->getStartColor()->setRGB('F9FAFB');
                    }
                }

                // ---- Bagian B: Rekap (Hanya jika tidak difilter unit) ----
                if (!$this->isFilteredUnit) {
                    $barisBagianB        = $barisDataAkhir + 3;
                    $barisHeaderRekap    = $barisBagianB + 1;
                    $barisDataRekapMulai = $barisHeaderRekap + 1;
                    $barisDataRekapAkhir = $barisDataRekapMulai + $this->jumlahBarisData - 1;
                    $totalKolomRekap     = 4 + count($this->daftarUnit) + 1;
                    $kolAkhirRekap       = $this->kolomKeHuruf($totalKolomRekap);

                    // Judul bagian B
                    $sheet->mergeCells("A{$barisBagianB}:{$kolAkhirRekap}{$barisBagianB}");
                    $sheet->getStyle("A{$barisBagianB}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A3A6B']],
                    ]);

                    // Header rekap
                    $sheet->getStyle("A{$barisHeaderRekap}:{$kolAkhirRekap}{$barisHeaderRekap}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '1A3A6B']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'CFE2FF']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'AAAAAA']]],
                    ]);

                    if ($barisDataRekapAkhir >= $barisDataRekapMulai) {
                        $sheet->getStyle("A{$barisDataRekapMulai}:{$kolAkhirRekap}{$barisDataRekapAkhir}")->applyFromArray([
                            'font'    => ['size' => 8],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CCCCCC']]],
                        ]);
                    }
                }

                // Freeze panes (beku baris header)
                $sheet->freezePane('C7');

                // Print setup
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $paperSize = $this->isFilteredUnit
                    ? \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4
                    : \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3;
                $sheet->getPageSetup()->setPaperSize($paperSize);
                $sheet->getPageSetup()->setFitToPage(true);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }

    // ---------------------------------------------------------------
    // HELPER: konversi index kolom (1-based) ke huruf Excel (A, B, ... Z, AA, AB, ...)
    // ---------------------------------------------------------------
    private function kolomKeHuruf(int $index): string
    {
        $huruf = '';
        while ($index > 0) {
            $sisa   = ($index - 1) % 26;
            $huruf  = chr(65 + $sisa) . $huruf;
            $index  = intdiv($index - 1, 26);
        }
        return $huruf;
    }
}
