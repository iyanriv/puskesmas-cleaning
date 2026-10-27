<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bukti Transaksi Barang Keluar - {{ $permintaan->kode_transaksi ?? ('BT' . str_pad($permintaan->id, 4, '0', STR_PAD_LEFT)) }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #ffffff;
            padding: 24px 30px;
        }

        /* BANNER HEADER (SIKAP BERSIH) */
        .banner-header {
            background-color: #31999E;
            color: #ffffff;
            padding: 16px 22px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .banner-header h1 {
            font-size: 19px;
            font-weight: bold;
            letter-spacing: 0.8px;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .banner-header .subtitle {
            font-size: 12px;
            opacity: 0.95;
            letter-spacing: 0.4px;
        }

        /* META TRANSAKSI (DARI / KEPADA vs BUKTI TRANSAKSI) */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 22px;
        }
        .meta-table td {
            vertical-align: top;
            font-size: 11.5px;
        }
        .meta-left {
            width: 50%;
        }
        .meta-right {
            width: 50%;
        }

        .meta-label-top {
            color: #4b5563;
            font-size: 10.5px;
            margin-bottom: 4px;
        }
        .meta-person {
            font-size: 13px;
            font-weight: bold;
            color: #0f172a;
        }
        .meta-area {
            font-size: 10.5px;
            color: #64748b;
            margin-top: 2px;
        }

        .meta-right-table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta-right-table td {
            padding: 2px 0;
            font-size: 11.5px;
        }
        .m-lbl { width: 42%; color: #374151; font-weight: 500; }
        .m-sep { width: 5%; color: #374151; }
        .m-val { font-weight: bold; color: #0f172a; }

        /* TABEL BARANG (SESUAI CONTOH GAMBAR) */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table thead tr {
            background-color: #31999E;
            color: #ffffff;
        }
        .items-table th {
            padding: 8px 10px;
            font-size: 10.5px;
            font-weight: bold;
            letter-spacing: 0.3px;
            text-align: left;
        }
        .items-table td {
            padding: 8px 10px;
            font-size: 11px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Zebra striping lembut (seperti contoh gambar) */
        .items-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        .items-table tbody tr:nth-child(even) {
            background-color: #fff9f0;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* TOTAL ROW */
        .total-row td {
            font-weight: bold;
            padding: 10px 10px;
            border-top: 2px solid #374151;
            border-bottom: 2px solid #374151;
            background-color: #ffffff !important;
            font-size: 11.5px;
        }

        /* TANDA TANGAN (SESUAI CONTOH GAMBAR) */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 35px;
        }
        .ttd-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }
        .ttd-title {
            font-size: 11px;
            color: #1e293b;
            font-weight: 600;
            margin-bottom: 50px;
        }
        .ttd-name {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #94a3b8;
            padding-top: 4px;
            display: inline-block;
            min-width: 160px;
        }
        .ttd-role {
            font-size: 9.5px;
            color: #64748b;
            margin-top: 2px;
        }

        /* FOOTER KECIL */
        .footer-note {
            margin-top: 40px;
            padding-top: 8px;
            border-top: 1px dashed #d1d5db;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }

        /* BADGE STATUS */
        .badge-status {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: bold;
        }
        .badge-disetujui { background: #dcfce7; color: #15803d; }
        .badge-ditolak { background: #fee2e2; color: #b91c1c; }
        .badge-pending { background: #fef3c7; color: #b45309; }
    </style>
</head>
<body>

    {{-- 1. BANNER HEADER --}}
    <div class="banner-header">
        <h1>BUKTI TRANSAKSI BARANG KELUAR</h1>
        <div class="subtitle">Puskesmas Cempaka Putih &bull; Pengelolaan Sanitasi & Kebersihan</div>
    </div>

    {{-- 2. METADATA TRANSAKSI --}}
    <table class="meta-table">
        <tr>
            {{-- Sisi Kiri: Dari / Kepada --}}
            <td class="meta-left">
                <div class="meta-label-top">Dari / Kepada:</div>
                <div class="meta-person">{{ $permintaan->pengguna->name }}</div>
                <div class="meta-area">
                    Peran: {{ $permintaan->pengguna->peran->nama_peran ?? 'Cleaning Service' }}
                </div>
            </td>

            {{-- Sisi Kanan: Nomor Bukti Transaksi, Jenis, Tanggal --}}
            <td class="meta-right">
                <table class="meta-right-table">
                    <tr>
                        <td class="m-lbl">Bukti Transaksi</td>
                        <td class="m-sep">:</td>
                        <td class="m-val">{{ $permintaan->kode_transaksi ?? ('BT' . str_pad($permintaan->id, 4, '0', STR_PAD_LEFT)) }}</td>
                    </tr>
                    <tr>
                        <td class="m-lbl">Jenis</td>
                        <td class="m-sep">:</td>
                        <td class="m-val">Keluar (Operasional CS)</td>
                    </tr>
                    <tr>
                        <td class="m-lbl">Tanggal</td>
                        <td class="m-sep">:</td>
                        <td class="m-val">{{ $permintaan->waktu_request->format('d F Y') }}</td>
                    </tr>
                    @if($permintaan->area)
                    <tr>
                        <td class="m-lbl">Area Bertugas</td>
                        <td class="m-sep">:</td>
                        <td class="m-val" style="color: #0f766e;">{{ $permintaan->area->nama_rapi }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- 3. TABEL DETAIL SELURUH BARANG DALAM TRANSAKSI INI --}}
    @php
        $daftarBarang = isset($semuaBarang) && count($semuaBarang) > 0 ? $semuaBarang : [$permintaan];
        $totalQty = 0;
    @endphp

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 6%;">No</th>
                <th style="width: 16%;">Kode Barang</th>
                <th style="width: 44%;">Nama Barang</th>
                <th class="text-center" style="width: 10%;">Qty</th>
                <th class="text-center" style="width: 12%;">Satuan</th>
                <th class="text-center" style="width: 12%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($daftarBarang as $idx => $item)
                @php $totalQty += (int) $item->jumlah; @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $item->barang->kode_barang ?? '-' }}</td>
                    <td><strong>{{ $item->barang->nama_barang }}</strong></td>
                    <td class="text-center fw-bold">{{ $item->jumlah }}</td>
                    <td class="text-center">{{ $item->barang->satuan }}</td>
                    <td class="text-center">
                        @if($item->status_request === 'disetujui')
                            <span class="badge-status badge-disetujui">Disetujui</span>
                        @elseif($item->status_request === 'ditolak')
                            <span class="badge-status badge-ditolak">Ditolak</span>
                        @else
                            <span class="badge-status badge-pending">Pending</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total Qty:</td>
                <td class="text-center">{{ $totalQty }}</td>
                <td colspan="2" style="color: #64748b; font-size: 10px;">Item Diserahterimakan</td>
            </tr>
        </tfoot>
    </table>

    {{-- 4. TANDA TANGAN (SESUAI CONTOH GAMBAR) --}}
    <table class="ttd-table">
        <tr>
            <td>
                <div class="ttd-title">Dibuat / Diterima Oleh:</div>
                <div class="ttd-name">{{ $permintaan->pengguna->name }}</div>
                <div class="ttd-role">Petugas Cleaning Service</div>
            </td>
            <td>
                <div class="ttd-title">Diserahkan Oleh:</div>
                <div class="ttd-name">Admin Warehouse / Gudang</div>
                <div class="ttd-role">Petugas Logistik Sanitasi</div>
            </td>
            <td>
                <div class="ttd-title">Disetujui Oleh:</div>
                <div class="ttd-name">{{ $permintaan->supervisor?->name ?? 'Supervisor' }}</div>
                <div class="ttd-role">Supervisor / Sanitasi Lingkungan</div>
            </td>
        </tr>
    </table>

    {{-- 5. CATATAN DOKUMEN --}}
    <div class="footer-note">
        Dokumen Bukti Transaksi Barang Keluar resmi diterbitkan oleh SIKAP Bersih - Puskesmas Cempaka Putih pada {{ now()->format('d/m/Y H:i') }} WIB.
    </div>

</body>
</html>
