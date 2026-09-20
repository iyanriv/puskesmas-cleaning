<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judulLaporan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 7.5pt;
            color: #1a1a1a;
            background: #fff;
        }

        .halaman {
            padding: 12mm 10mm 10mm 10mm;
        }

        /* ---- HEADER ---- */
        .header-laporan {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2.5px solid #1a6b3a;
            padding-bottom: 8px;
        }
        .header-laporan .judul-utama {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #1a6b3a;
            letter-spacing: 0.5px;
            line-height: 1.4;
        }
        .header-laporan .sub-judul {
            font-size: 8.5pt;
            font-weight: bold;
            color: #333;
            margin-top: 3px;
        }
        .header-laporan .periode {
            font-size: 8pt;
            color: #555;
            margin-top: 2px;
        }

        /* ---- BAGIAN JUDUL TABEL ---- */
        .judul-bagian {
            background-color: #1a6b3a;
            color: white;
            font-weight: bold;
            font-size: 8pt;
            padding: 4px 8px;
            margin-top: 12px;
            margin-bottom: 0;
            border-radius: 3px 3px 0 0;
        }

        /* ---- TABEL UTAMA ---- */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7pt;
        }

        th, td {
            border: 0.5px solid #b0b0b0;
            padding: 2.5px 3px;
            vertical-align: middle;
        }

        thead th {
            background-color: #d4edda;
            text-align: center;
            font-weight: bold;
            font-size: 6.5pt;
            color: #1a4d2e;
        }

        tbody tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        tbody tr td {
            vertical-align: middle;
        }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-left   { text-align: left; }
        .fw-bold     { font-weight: bold; }
        .text-muted  { color: #666; }

        /* Kolom No & Nama sticky-like (bold) */
        .col-no     { width: 20px; text-align: center; }
        .col-nama   { min-width: 90px; font-weight: bold; }
        .col-kode   { width: 60px; text-align: center; font-size: 6pt; color: #555; }
        .col-satuan { width: 40px; text-align: center; }
        .col-tgl    { width: 55px; text-align: center; font-size: 6pt; }
        .col-stok   { width: 40px; text-align: center; font-weight: bold; color: #1a4d8f; }
        .col-pemakaian { width: 22px; text-align: center; }
        .col-total  { width: 42px; text-align: center; font-weight: bold; }
        .col-sisa   { width: 38px; text-align: center; font-weight: bold; }

        .negatif { color: #c0392b; font-weight: bold; }

        /* ---- TABEL REKAP ---- */
        .rekap-header th {
            background-color: #cfe2ff;
            color: #1a3a6b;
        }

        /* ---- FOOTER ---- */
        .footer-ttd {
            margin-top: 18px;
            display: table;
            width: 100%;
        }
        .footer-ttd .ttd-blok {
            display: table-cell;
            width: 33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .footer-ttd .label-ttd {
            font-size: 7.5pt;
            color: #333;
            margin-bottom: 45px;
        }
        .footer-ttd .garis-ttd {
            border-top: 1px solid #333;
            padding-top: 3px;
            font-size: 7.5pt;
            font-weight: bold;
        }

        .footer-keterangan {
            margin-top: 8px;
            font-size: 6.5pt;
            color: #777;
            border-top: 0.5px solid #ccc;
            padding-top: 4px;
        }

        /* Page break */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
<div class="halaman">

    {{-- ============================================================
         HEADER
         ============================================================ --}}
    <div class="header-laporan">
        <div class="judul-utama">{{ $judulLaporan }}</div>
        <div class="sub-judul">PUSKESMAS CEMPAKA PUTIH</div>
        <div class="periode">
            Periode: {{ strtoupper($namaBulan) }} {{ $tahun }}
            @if($isFilteredUnit)
                &mdash; Unit: <strong>{{ $filterUnit }}</strong>
                @if($filterMinggu) (Week {{ $filterMinggu }}) @else (Semua Minggu) @endif
            @endif
        </div>
    </div>

    @if($laporan->isEmpty())
        <p style="text-align:center; color:#888; margin-top:30px;">
            @if($isFilteredUnit)
                Tidak ada data pemakaian untuk unit <strong>{{ $filterUnit }}</strong> pada periode ini.
            @else
                Tidak ada data untuk periode ini.
            @endif
        </p>
    @else

    {{-- ============================================================
         TABEL STOK DAN PEMAKAIAN
         ============================================================ --}}
    <div class="judul-bagian">
        @if($isFilteredUnit)
            A. TABEL PEMAKAIAN &amp; SISA STOK &mdash; {{ strtoupper($filterUnit) }}
        @else
            A. TABEL STOK DAN PEMAKAIAN
        @endif
    </div>
    <table>
        <thead>
            <tr>
                <th rowspan="2" class="col-no">No</th>
                <th rowspan="2" class="col-nama text-left">Nama Barang</th>
                <th rowspan="2" class="col-kode">Kode</th>
                <th rowspan="2" class="col-satuan">Satuan</th>
                <th rowspan="2" class="col-tgl">Tanggal</th>
                <th rowspan="2" class="col-stok">Stok/<br>Masuk</th>
                @foreach($unitList as $unit)
                    <th colspan="{{ count($mingguList) }}" class="text-center" style="font-size:{{ $isFilteredUnit ? '7.5pt' : '6pt' }};">{{ $unit }}</th>
                @endforeach
                <th rowspan="2" class="col-total">
                    @if($isFilteredUnit)
                        Pemakaian<br>{{ $filterMinggu ? 'Wk '.$filterMinggu : 'Total' }}
                    @else
                        Total<br>Pakai
                    @endif
                </th>
                <th rowspan="2" class="col-sisa">Sisa<br>Stok</th>
            </tr>
            <tr>
                @foreach($unitList as $unit)
                    @foreach($mingguList as $w)
                        <th class="col-pemakaian" style="font-size:{{ $isFilteredUnit ? '7pt' : '5.5pt' }};">W{{ $w }}</th>
                    @endforeach
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $no => $item)
                @php
                    $grid  = $item->pemakaianPerUnitMinggu();
                    if ($isFilteredUnit) {
                        $total = 0;
                        foreach ($mingguList as $w) {
                            $total += ($grid[$filterUnit][$w] ?? 0);
                        }
                    } else {
                        $total = $item->totalPemakaian();
                    }
                    $sisa  = $item->sisaStok();
                @endphp
                <tr>
                    <td class="col-no text-muted">{{ $no + 1 }}</td>
                    <td class="col-nama">{{ $item->produk->nama_barang }}</td>
                    <td class="col-kode">{{ $item->produk->kode_barang }}</td>
                    <td class="col-satuan text-center">{{ $item->produk->satuan }}</td>
                    <td class="col-tgl">{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</td>
                    <td class="col-stok">{{ $item->stok_masuk }}</td>
                    @foreach($unitList as $unit)
                        @foreach($mingguList as $w)
                            <td class="col-pemakaian {{ ($grid[$unit][$w] ?? 0) > 0 ? 'fw-bold' : 'text-muted' }}">
                                {{ $grid[$unit][$w] ?? 0 }}
                            </td>
                        @endforeach
                    @endforeach
                    <td class="col-total fw-bold">{{ $total }}</td>
                    <td class="col-sisa {{ $sisa < 0 ? 'negatif' : 'fw-bold' }}">{{ $sisa }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!$isFilteredUnit)
    {{-- ============================================================
         TABEL REKAP
         ============================================================ --}}
    <div class="judul-bagian" style="margin-top:16px; background-color:#1a3a6b;">
        B. REKAP PEMAKAIAN BARANG BERDASARKAN UNIT
    </div>
    <table>
        <thead class="rekap-header">
            <tr>
                <th class="col-no">No</th>
                <th class="text-left col-nama">Nama Barang</th>
                <th class="col-kode">Kode</th>
                <th class="col-satuan">Satuan</th>
                @foreach($daftarUnit as $unit)
                    <th style="width:42px; font-size:6pt;">{{ $unit }}</th>
                @endforeach
                <th class="col-total">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $no => $item)
                @php
                    $perUnit    = $item->pemakaianPerUnit();
                    $totalRekap = array_sum($perUnit);
                @endphp
                <tr>
                    <td class="col-no text-muted">{{ $no + 1 }}</td>
                    <td class="col-nama">{{ $item->produk->nama_barang }}</td>
                    <td class="col-kode">{{ $item->produk->kode_barang }}</td>
                    <td class="col-satuan text-center">{{ $item->produk->satuan }}</td>
                    @foreach($daftarUnit as $unit)
                        <td class="text-center {{ ($perUnit[$unit] ?? 0) > 0 ? 'fw-bold' : 'text-muted' }}">
                            {{ $perUnit[$unit] ?? 0 }}
                        </td>
                    @endforeach
                    <td class="col-total fw-bold" style="color:#1a3a6b;">{{ $totalRekap }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- ============================================================
         TANDA TANGAN
         ============================================================ --}}
    <div class="footer-ttd">
        <div class="ttd-blok">
            <div class="label-ttd">Dibuat Oleh,</div>
            <div class="garis-ttd">Petugas Gudang</div>
        </div>
        <div class="ttd-blok">
            <div class="label-ttd">Diperiksa Oleh,</div>
            <div class="garis-ttd">Supervisor</div>
        </div>
        <div class="ttd-blok">
            <div class="label-ttd">
                Jakarta, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}<br>
                Diketahui Oleh,
            </div>
            <div class="garis-ttd">Kepala Puskesmas</div>
        </div>
    </div>

    <div class="footer-keterangan">
        * Laporan ini digenerate secara otomatis oleh SIM Kebersihan Puskesmas Cempaka Putih.
        Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y HH:mm') }} WIB.
    </div>

    @endif

</div>
</body>
</html>
