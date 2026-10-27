<?php

namespace App\Exports;

use App\Models\BarangInventori;
use App\Models\PermintaanBarang;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapanPemakaianExport implements FromArray, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    private int    $tahun;
    private string $kategori;
    private int    $jumlahBaris = 0;

    private array $namaBulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
    ];

    public function __construct(int $tahun, string $kategori = '')
    {
        $this->tahun    = $tahun;
        $this->kategori = $kategori;
    }

    public function title(): string
    {
        return 'Pemakaian ' . $this->tahun;
    }

    public function array(): array
    {
        // Ambil data permintaan disetujui dalam tahun ini
        $query = PermintaanBarang::with('barang')
            ->where('status_request', 'disetujui')
            ->whereYear('waktu_approve', $this->tahun);

        if ($this->kategori) {
            $query->whereHas('barang', fn($q) => $q->where('kategori', $this->kategori));
        }

        $semuaPermintaan = $query->get();

        // Daftar barang unik
        $daftarBarangId = $semuaPermintaan->pluck('barang_id')->unique();
        $semuaBarang = BarangInventori::whereIn('id', $daftarBarangId)
            ->orderBy('nama_barang')
            ->get()
            ->keyBy('id');

        if ($semuaBarang->isEmpty()) {
            $barangQuery = BarangInventori::orderBy('nama_barang');
            if ($this->kategori) $barangQuery->where('kategori', $this->kategori);
            $semuaBarang = $barangQuery->get()->keyBy('id');
        }

        // Build matrix
        $matrix = [];
        foreach ($semuaPermintaan as $p) {
            $bId   = $p->barang_id;
            $bulan = (int) $p->waktu_approve->format('n');
            $matrix[$bId][$bulan] = ($matrix[$bId][$bulan] ?? 0) + $p->jumlah;
        }

        // ── ROWS ──
        $rows = [];

        // Judul
        $judulKategori = $this->kategori ? ' — ' . $this->kategori : '';
        $rows[] = ['REKAPAN PEMAKAIAN BARANG KEBERSIHAN' . $judulKategori . ' TAHUN ' . $this->tahun];
        $rows[] = ['PUSKESMAS KECAMATAN CEMPAKA PUTIH'];
        $rows[] = [];

        // Header tabel
        $header = ['No', 'Nama Barang', 'Satuan'];
        foreach ($this->namaBulan as $nm) $header[] = $nm;
        $header[] = 'Total';
        $header[] = 'Rata-rata/Bln';
        $header[] = 'Pembulatan';
        $rows[]   = $header;

        // Data baris
        $no = 1;
        $totalPerBulan = array_fill(1, 12, 0);
        $grandTotal    = 0;

        foreach ($semuaBarang as $bId => $barang) {
            $bulanan = $matrix[$bId] ?? [];
            $total   = array_sum($bulanan);
            $rata    = $total / 12;

            $row = [$no++, $barang->nama_barang, $barang->satuan];
            for ($b = 1; $b <= 12; $b++) {
                $jumlah = $bulanan[$b] ?? 0;
                $row[]  = $jumlah;
                $totalPerBulan[$b] += $jumlah;
            }
            $row[] = $total;
            $row[] = round($rata, 2);
            $row[] = (int) round($rata);
            $rows[] = $row;
            $grandTotal += $total;
        }

        // Baris Total
        $rowTotal = ['', 'TOTAL', ''];
        for ($b = 1; $b <= 12; $b++) $rowTotal[] = $totalPerBulan[$b];
        $rowTotal[] = $grandTotal;
        $rowTotal[] = round($grandTotal / 12, 2);
        $rowTotal[] = (int) round($grandTotal / 12);
        $rows[] = $rowTotal;

        // Baris kosong + keterangan
        $rows[] = [];
        $rows[] = ['', 'Dibuat pada: ' . now()->translatedFormat('d F Y, H:i') . ' WIB'];
        $rows[] = ['', 'Sumber data: Permintaan barang yang telah disetujui gudang (status = disetujui)'];

        $this->jumlahBaris = count($rows);
        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // No
            'B' => 40,  // Nama Barang
            'C' => 10,  // Satuan
            'D' => 7, 'E' => 7, 'F' => 7, 'G' => 7,
            'H' => 7, 'I' => 7, 'J' => 7, 'K' => 7,
            'L' => 7, 'M' => 7, 'N' => 7, 'O' => 7,
            'P' => 10,  // Total
            'Q' => 14,  // Rata-rata
            'R' => 12,  // Pembulatan
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Baris 1: Judul utama
                $sheet->mergeCells('A1:R1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 13],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Baris 2: Sub judul
                $sheet->mergeCells('A2:R2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Baris 4: Header tabel
                $sheet->getStyle('A4:R4')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1A6B3A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']],
                    ],
                ]);
                $sheet->getRowDimension(4)->setRowHeight(28);

                // Temukan baris terakhir data (sebelum total)
                $highestRow = $sheet->getHighestDataRow();

                // Style seluruh area data (baris 5 sampai highestRow - 3)
                $dataEnd = $highestRow - 3; // kurangi 3 (baris total + 2 keterangan)
                if ($dataEnd >= 5) {
                    // Zebra striping
                    for ($r = 5; $r <= $dataEnd - 1; $r++) {
                        $style = ['borders' => ['allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'CCCCCC'],
                        ]]];
                        if ($r % 2 === 0) {
                            $style['fill'] = [
                                'fillType'   => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F2F2F2'],
                            ];
                        }
                        $sheet->getStyle('A' . $r . ':R' . $r)->applyFromArray($style);
                    }

                    // Kolom angka (D–R): rata kanan
                    $sheet->getStyle('D5:R' . ($dataEnd - 1))->applyFromArray([
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);

                    // Baris Total
                    $sheet->getStyle('A' . $dataEnd . ':R' . $dataEnd)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '2D6A4F'],
                        ],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        'borders'   => ['allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color'       => ['rgb' => '1A6B3A'],
                        ]],
                    ]);
                }

                // Freeze pane di D5 (header + 2 kolom kiri)
                $sheet->freezePane('D5');

                // Wrap text kolom B
                $sheet->getStyle('B5:B' . $highestRow)->applyFromArray([
                    'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_TOP],
                ]);

                // Rata kiri kolom B, C
                $sheet->getStyle('B4:C' . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // Row height untuk data
                for ($r = 5; $r <= $dataEnd; $r++) {
                    $sheet->getRowDimension($r)->setRowHeight(18);
                }
            },
        ];
    }
}
