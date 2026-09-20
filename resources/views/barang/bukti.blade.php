<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bukti Permintaan Barang #{{ $permintaan->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #ffffff;
        }

        /* HEADER */
        .header {
            background: #ffffff;
            padding: 16px 24px;
            color: #1a1a2e;
            display: table;
            width: 100%;
            border-bottom: 2px solid #0a7040;
        }
        .header-logo  { display: table-cell; width: 80px; vertical-align: middle; padding-right: 14px; }
        .header-logo img { width: 64px; height: 64px; border-radius: 4px; }
        .header-left  { display: table-cell; width: 52%; vertical-align: middle; }
        .header-right { display: table-cell; width: 40%; text-align: right; vertical-align: middle; }
        .instansi-nama  { font-size: 14px; font-weight: bold; letter-spacing: 0.5px; color: #0a7040; }
        .instansi-sub   { font-size: 9.5px; margin-top: 2px; color: #6b7280; }
        .doc-title      { font-size: 17px; font-weight: bold; letter-spacing: 1px; color: #1a1a2e; }
        .doc-nomor      { font-size: 10px; margin-top: 4px; color: #6b7280; }

        /* DIVIDER */
        .divider { height: 4px; background: #12a65a; }

        /* BODY */
        .body-wrapper { padding: 24px 30px; }

        /* Status banner */
        .status-banner {
            padding: 8px 16px; border-radius: 6px;
            font-size: 10px; font-weight: bold;
            text-align: center; margin-bottom: 20px;
            letter-spacing: 0.5px;
        }
        .status-disetujui { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .status-ditolak   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .status-pending   { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        /* Info grid */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .info-table td { padding: 5px 0; vertical-align: top; font-size: 11px; }
        .info-label  { color: #6b7280; width: 38%; }
        .info-colon  { width: 4%; color: #6b7280; }
        .info-value  { font-weight: 600; }

        /* Section title */
        .section-title {
            font-size: 10px; font-weight: bold;
            text-transform: uppercase; letter-spacing: 0.8px;
            color: #0a7040;
            border-bottom: 1.5px solid #0a7040;
            padding-bottom: 4px; margin-bottom: 10px;
        }

        /* Detail table */
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .detail-table thead tr { background: #0a7040; color: white; }
        .detail-table th {
            padding: 8px 10px; text-align: left;
            font-size: 10px; font-weight: bold; letter-spacing: 0.4px;
        }
        .detail-table tbody tr:nth-child(even) { background: #f0fdf4; }
        .detail-table td { padding: 8px 10px; font-size: 11px; border-bottom: 1px solid #e5e7eb; }
        .text-center { text-align: center; }

        /* Alasan penolakan */
        .alasan-box {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 6px;
            padding: 10px 14px; margin-bottom: 20px; font-size: 11px;
        }
        .alasan-label { font-weight: bold; color: #c2410c; margin-bottom: 3px; }

        /* Tanda tangan */
        .ttd-table { width: 100%; border-collapse: collapse; margin-top: 28px; }
        .ttd-table td { text-align: center; vertical-align: top; width: 33%; padding: 0 10px; }
        .ttd-label   { font-size: 10px; color: #6b7280; margin-bottom: 4px; }
        .ttd-nama    { font-weight: bold; font-size: 11px; }
        .ttd-jabatan { font-size: 10px; color: #6b7280; }
        .ttd-box     { height: 55px; border-bottom: 1px solid #374151; margin: 6px 20px; }

        /* Footer */
        .footer {
            margin-top: 30px; padding-top: 10px;
            border-top: 1px dashed #d1d5db;
            text-align: center; font-size: 9px; color: #9ca3af;
        }
        .badge-no {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            color: #065f46; padding: 2px 10px;
            border-radius: 20px; font-size: 10px; font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-logo">
            <img src="{{ public_path('images/logo.png') }}" alt="Logo Puskesmas">
        </div>
        <div class="header-left">
            <div class="instansi-nama">Puskesmas Cleaning Service</div>
            <div class="instansi-sub">Sistem Informasi Manajemen Kebersihan</div>
        </div>
        <div class="header-right">
            <div class="doc-title">BUKTI PERMINTAAN BARANG</div>
            <div class="doc-nomor">No. <strong>BPB-{{ str_pad($permintaan->id, 5, '0', STR_PAD_LEFT) }}</strong></div>
        </div>
    </div>
    <div class="divider"></div>

    {{-- BODY --}}
    <div class="body-wrapper">

        {{-- Status Banner --}}
        @php
            $statusClass = match($permintaan->status_request) {
                'disetujui' => 'status-disetujui',
                'ditolak'   => 'status-ditolak',
                default     => 'status-pending',
            };
            $statusTeks = match($permintaan->status_request) {
                'disetujui' => 'PERMINTAAN DISETUJUI',
                'ditolak'   => 'PERMINTAAN DITOLAK',
                default     => 'MENUNGGU PERSETUJUAN',
            };
        @endphp
        <div class="status-banner {{ $statusClass }}">{{ $statusTeks }}</div>

        {{-- Info Dokumen --}}
        <div class="section-title">Informasi Dokumen</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Nomor Dokumen</td>
                <td class="info-colon">:</td>
                <td class="info-value"><span class="badge-no">BPB-{{ str_pad($permintaan->id, 5, '0', STR_PAD_LEFT) }}</span></td>
            </tr>
            <tr>
                <td class="info-label">Tanggal Permintaan</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $permintaan->waktu_request->format('d/m/Y H:i') }} WIB</td>
            </tr>
            @if($permintaan->waktu_approve)
            <tr>
                <td class="info-label">Tanggal Diproses</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $permintaan->waktu_approve->format('d/m/Y H:i') }} WIB</td>
            </tr>
            @endif
            <tr>
                <td class="info-label">Nama Pemohon</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $permintaan->pengguna->name }}</td>
            </tr>
            <tr>
                <td class="info-label">Jabatan / Peran</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $permintaan->pengguna->peran->nama_peran ?? '-' }}</td>
            </tr>
            <tr>
                <td class="info-label">Area / Lantai</td>
                <td class="info-colon">:</td>
                <td class="info-value">{{ $permintaan->pengguna->area->lantai ?? '-' }}</td>
            </tr>
        </table>

        {{-- Detail Barang --}}
        <div class="section-title">Detail Barang yang Diminta</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th class="text-center" style="width:6%">No</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Satuan</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td><strong>{{ $permintaan->barang->nama_barang }}</strong></td>
                    <td class="text-center">{{ $permintaan->barang->satuan }}</td>
                    <td class="text-center"><strong>{{ $permintaan->jumlah }}</strong></td>
                    <td class="text-center">
                        @if($permintaan->status_request === 'disetujui') Disetujui
                        @elseif($permintaan->status_request === 'ditolak') Ditolak
                        @else Pending @endif
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Alasan Penolakan --}}
        @if($permintaan->status_request === 'ditolak' && $permintaan->alasan_penolakan)
        <div class="alasan-box">
            <div class="alasan-label">Alasan Penolakan:</div>
            <div>{{ $permintaan->alasan_penolakan }}</div>
        </div>
        @endif

        {{-- Tanda Tangan --}}
        <div class="section-title">Tanda Tangan</div>
        <table class="ttd-table">
            <tr>
                <td>
                    <div class="ttd-label">Pemohon</div>
                    <div class="ttd-box"></div>
                    <div class="ttd-nama">{{ $permintaan->pengguna->name }}</div>
                    <div class="ttd-jabatan">{{ $permintaan->pengguna->peran->nama_peran ?? '-' }}</div>
                </td>
                <td>
                    <div class="ttd-label">Diketahui Oleh</div>
                    <div class="ttd-box"></div>
                    <div class="ttd-nama">Supervisor</div>
                    <div class="ttd-jabatan">Supervisor Kebersihan</div>
                </td>
                <td>
                    <div class="ttd-label">Petugas Gudang</div>
                    <div class="ttd-box"></div>
                    <div class="ttd-nama">Petugas Gudang</div>
                    <div class="ttd-jabatan">Pengelola Gudang</div>
                </td>
            </tr>
        </table>

        {{-- Footer --}}
        <div class="footer">
            Dokumen dicetak otomatis oleh Sistem Informasi Manajemen Kebersihan Puskesmas &nbsp;|&nbsp;
            Dicetak: {{ now()->format('d/m/Y H:i') }} WIB
        </div>

    </div>
</body>
</html>
