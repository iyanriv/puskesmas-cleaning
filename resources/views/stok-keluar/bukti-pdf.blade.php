<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bukti Pengeluaran Barang - {{ $mutasi->nomor_transaksi }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9pt;
            color: #1a1a2e;
            background: #ffffff;
            padding: 16px 20px;
        }

        .banner-header {
            background-color: #205F63;
            color: #ffffff;
            padding: 12px 18px;
            border-radius: 4px;
            margin-bottom: 14px;
        }
        .banner-header h1 {
            font-size: 14pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .banner-header .subtitle {
            font-size: 8.5pt;
            opacity: 0.95;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }
        .meta-table td {
            vertical-align: top;
            padding: 2px 4px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            font-size: 8.5pt;
        }
        .items-table th {
            background-color: #205F63;
            color: #ffffff;
            font-weight: bold;
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
            text-transform: uppercase;
            font-size: 8pt;
        }
        .items-table td {
            padding: 6px 8px;
            border: 1px solid #cbd5e1;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8.5pt;
        }
        .ttd-table td {
            text-align: center;
            vertical-align: top;
            width: 33.33%;
        }
        .ttd-space {
            height: 45px;
        }
        .ttd-line {
            display: inline-block;
            border-bottom: 1px solid #1a1a2e;
            min-width: 130px;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="banner-header">
        <h1>BUKTI PENGELUARAN / DISTRIBUSI BARANG</h1>
        <div class="subtitle">SIKAP Bersih &bull; Puskesmas Kecamatan Cempaka Putih</div>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;">
                <div style="color: #64748b; font-size: 7.5pt; text-transform: uppercase; font-weight: bold;">Tujuan Distribusi / Unit:</div>
                <div style="font-size: 11pt; font-weight: bold; color: #205F63;">{{ $mutasi->unit ?? 'Gudang Puskesmas' }}</div>
                <div style="color: #334155; margin-top: 2px;">
                    {{ $mutasi->keterangan ?: 'Distribusi perlengkapan kebersihan & logistik' }}
                </div>
            </td>
            <td style="width: 50%; text-align: right;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td class="text-right" style="color: #64748b; font-weight: bold;">No. Transaksi :</td>
                        <td class="text-right font-mono" style="font-weight: bold; color: #205F63;">{{ $mutasi->nomor_transaksi }}</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="color: #64748b; font-weight: bold;">Tanggal :</td>
                        <td class="text-right">{{ $mutasi->tanggal ? $mutasi->tanggal->format('d/m/Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="color: #64748b; font-weight: bold;">No. Referensi :</td>
                        <td class="text-right">{{ $mutasi->nomor_referensi ?: '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 30px;">No</th>
                <th style="width: 90px;">Kode Barang</th>
                <th>Nama Barang Logistik</th>
                <th class="text-center" style="width: 80px;">Satuan</th>
                <th class="text-center" style="width: 90px;">Jumlah Keluar</th>
                <th class="text-center" style="width: 90px;">Sisa Stok Gudang</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td class="text-center" style="font-family: monospace;">{{ $mutasi->barang->kode_barang ?? '-' }}</td>
                <td><strong>{{ $mutasi->barang->nama_barang ?? '-' }}</strong></td>
                <td class="text-center">{{ $mutasi->barang->satuan ?? '-' }}</td>
                <td class="text-center" style="font-weight: bold; color: #b91c1c; font-size: 10pt;">
                    {{ number_format($mutasi->jumlah_keluar) }}
                </td>
                <td class="text-center" style="font-weight: bold; color: #047857;">
                    {{ number_format($mutasi->saldo_setelahnya) }}
                </td>
            </tr>
        </tbody>
    </table>

    <table class="ttd-table">
        <tr>
            <td>
                <div>Diserahkan Oleh (Gudang):</div>
                <div class="ttd-space"></div>
                <div class="ttd-line">{{ $mutasi->petugas->name ?? 'Petugas Gudang' }}</div>
            </td>
            <td>
                <div>Mengetahui (Supervisor):</div>
                <div class="ttd-space"></div>
                <div class="ttd-line">Supervisor Kebersihan</div>
            </td>
            <td>
                <div>Diterima Oleh (Unit/CS):</div>
                <div class="ttd-space"></div>
                <div class="ttd-line">Penerima Barang</div>
            </td>
        </tr>
    </table>

</body>
</html>
