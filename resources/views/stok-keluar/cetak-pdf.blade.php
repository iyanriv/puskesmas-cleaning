<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Keluar & Distribusi</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8pt;
            color: #1a1a1a;
            padding: 15mm 12mm;
        }
        .header-laporan {
            text-align: center;
            border-bottom: 2.5px solid #205F63;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .kop-instansi {
            font-size: 9pt;
            font-weight: bold;
            color: #555;
            text-transform: uppercase;
        }
        .judul-utama {
            font-size: 13pt;
            font-weight: bold;
            color: #205F63;
            margin: 3px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .periode {
            font-size: 8.5pt;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            font-size: 8pt;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
        }
        th {
            background-color: #205F63;
            color: #ffffff;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 7.5pt;
        }
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: monospace; }
        .total-row {
            background-color: #e2e8f0;
            font-weight: bold;
        }
        .ttd-section {
            margin-top: 25px;
            width: 100%;
        }
        .ttd-box {
            width: 35%;
            text-align: center;
            float: right;
        }
        .ttd-box-kiri {
            width: 35%;
            text-align: center;
            float: left;
        }
        .clear { clear: both; }
        .garis-bawah {
            display: inline-block;
            border-bottom: 1px solid #333;
            min-width: 160px;
            font-weight: bold;
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <div class="header-laporan">
        <div class="kop-instansi">PEMERINTAH PROVINSI DKI JAKARTA &bull; DINAS KESEHATAN</div>
        <div class="judul-utama">PUSKESMAS KECAMATAN CEMPAKA PUTIH</div>
        <div class="kop-instansi" style="font-size: 10pt; color: #205F63; font-weight: bold;">LAPORAN PENGELUARAN & DISTRIBUSI BARANG LOGISTIK</div>
        <div class="periode">
            Periode: <strong>{{ \Carbon\Carbon::parse($dari)->format('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($sampai)->format('d F Y') }}</strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 110px;">No. Transaksi</th>
                <th style="width: 70px;">Tanggal</th>
                <th style="width: 70px;">Kode</th>
                <th>Nama Barang</th>
                <th style="width: 50px;">Satuan</th>
                <th style="width: 55px;">Keluar</th>
                <th style="width: 65px;">Saldo Akhir</th>
                <th style="width: 100px;">Unit / Tujuan</th>
                <th>Referensi & Keterangan</th>
                <th style="width: 90px;">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($daftarKeluar as $idx => $item)
            <tr>
                <td class="text-center">{{ $idx + 1 }}</td>
                <td class="font-mono text-center">{{ $item->nomor_transaksi }}</td>
                <td class="text-center">{{ $item->tanggal ? $item->tanggal->format('d/m/Y') : '-' }}</td>
                <td class="text-center">{{ $item->barang->kode_barang ?? '-' }}</td>
                <td><strong>{{ $item->barang->nama_barang ?? '-' }}</strong></td>
                <td class="text-center">{{ $item->barang->satuan ?? '-' }}</td>
                <td class="text-center" style="font-weight: bold; color: #b91c1c;">{{ number_format($item->jumlah_keluar) }}</td>
                <td class="text-center">{{ number_format($item->saldo_setelahnya) }}</td>
                <td>{{ $item->unit ?? '-' }}</td>
                <td>{{ $item->keterangan ?: ($item->nomor_referensi ? 'Ref: ' . $item->nomor_referensi : '-') }}</td>
                <td class="text-center">{{ $item->petugas->name ?? 'Gudang' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="11" class="text-center" style="padding: 20px;">Tidak ada data pengeluaran barang pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL BARANG KELUAR:</td>
                <td class="text-center" style="color: #b91c1c; font-size: 9pt;">{{ number_format($totalUnitKeluar) }}</td>
                <td colspan="4">unit terdistribusi</td>
            </tr>
        </tfoot>
    </table>

    <div class="ttd-section">
        <div class="ttd-box-kiri">
            <div>Mengetahui,</div>
            <div>Kepala Sub Bagian Tata Usaha</div>
            <div class="garis-bawah">Kasubag TU Puskesmas</div>
            <div>NIP. ........................................</div>
        </div>
        <div class="ttd-box">
            <div>Jakarta, {{ now()->translatedFormat('d F Y') }}</div>
            <div>Petugas Pengelola Barang / Gudang</div>
            <div class="garis-bawah">{{ auth()->user()->name ?? 'Petugas Gudang' }}</div>
            <div>SIKAP Bersih &bull; Puskesmas Cempaka Putih</div>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
