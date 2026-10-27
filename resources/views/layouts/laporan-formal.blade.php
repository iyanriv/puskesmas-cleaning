<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Laporan')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        @page {
            size: A4;
            margin: 2cm 2.5cm;
        }
        
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #e5e7eb; /* abu-abu di luar kertas saat preview */
        }

        /* ── Wrapper kertas A4 saat preview di browser ── */
        .halaman-wrapper {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 20mm auto;
            padding: 2cm 2.5cm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        /* Header Kop Surat */
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 5px;
        }
        
        .kop-surat-inner {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
        }
        
        .logo-instansi {
            width: 90px;
            height: 90px;
        }
        
        .logo-instansi img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        
        .info-instansi {
            text-align: center;
        }
        
        .info-instansi h2 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        
        .info-instansi h3 {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 4px;
        }
        
        .info-instansi .alamat {
            font-size: 10pt;
            line-height: 1.3;
        }
        
        .garis-tebal {
            border-top: 1px solid #000;
            margin-top: 2px;
        }
        
        /* Judul Laporan */
        .judul-laporan {
            text-align: center;
            margin: 30px 0 25px 0;
        }
        
        .judul-laporan h1 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .judul-laporan .nomor-surat {
            font-size: 11pt;
            margin-top: 3px;
        }
        
        /* Pembuka */
        .pembuka {
            text-align: justify;
            margin-bottom: 20px;
            text-indent: 50px;
        }
        
        /* Info Periode */
        .info-periode {
            margin: 20px 0;
        }
        
        .info-periode table {
            width: 100%;
            font-size: 11pt;
        }
        
        .info-periode td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .info-periode .label {
            width: 180px;
            font-weight: bold;
        }
        
        .info-periode .separator {
            width: 20px;
        }
        
        /* Ringkasan Statistik */
        .ringkasan {
            margin: 25px 0;
            page-break-inside: avoid;
        }
        
        .ringkasan h3 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        
        .ringkasan table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11pt;
        }
        
        .ringkasan table td {
            padding: 5px 10px;
            border: 1px solid #000;
        }
        
        .ringkasan table .label {
            width: 70%;
            font-weight: normal;
        }
        
        .ringkasan table .value {
            width: 30%;
            text-align: center;
            font-weight: bold;
        }
        
        /* Tabel Detail */
        .tabel-detail {
            margin: 25px 0;
            page-break-inside: auto;
        }
        
        .tabel-detail h3 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-decoration: underline;
        }
        
        .tabel-formal {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        
        .tabel-formal th,
        .tabel-formal td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        
        .tabel-formal thead th {
            background: #fff;
            font-weight: bold;
            text-align: center;
        }
        
        .tabel-formal tbody td {
            text-align: left;
        }
        
        .tabel-formal .text-center {
            text-align: center;
        }
        
        .tabel-formal .text-right {
            text-align: right;
        }
        
        .tabel-formal .no {
            width: 30px;
            text-align: center;
        }
        
        .tabel-formal .nama {
            font-weight: bold;
        }
        
        /* Status */
        .status-selesai { font-weight: bold; }
        .status-proses { font-style: italic; }
        .status-pending { font-style: italic; }
        .status-ditolak { text-decoration: line-through; }
        
        /* Penutup */
        .penutup {
            margin-top: 25px;
            text-align: justify;
            text-indent: 50px;
        }
        
        /* Tanda Tangan */
        .ttd-section {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        
        .ttd-container {
            display: flex;
            justify-content: space-between;
        }
        
        .ttd-box {
            width: 45%;
        }
        
        .ttd-box.right {
            text-align: left;
            margin-left: auto;
        }
        
        .ttd-tempat {
            margin-bottom: 5px;
        }
        
        .ttd-jabatan {
            font-weight: bold;
            margin-bottom: 70px;
        }
        
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .ttd-nip {
            font-size: 10pt;
            margin-top: 2px;
        }
        
        /* TTD 4 Pihak (2 Baris) */
        .ttd-table-4 {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-top: 10px;
        }
        .ttd-table-4 td {
            border: none !important;
            padding: 4px 10px;
            vertical-align: top;
        }
        .ttd-table-4 .ttd-space {
            height: 55px;
        }
        
        /* Tembusan */
        .tembusan {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        
        .tembusan-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .tembusan ol {
            margin-left: 20px;
            padding-left: 5px;
        }
        
        .tembusan li {
            margin-bottom: 3px;
        }
        
        /* ── Toolbar Aksi (hanya tampil di layar, bukan saat print) ── */
        .toolbar-aksi {
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        .tombol-aksi {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 10.5pt;
            font-weight: 600;
            cursor: pointer;
            font-family: Arial, sans-serif;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transition: filter 0.15s;
        }
        .tombol-aksi:hover { filter: brightness(0.9); }
        .tombol-cetak { background: #10b981; color: white; }
        .tombol-unduh { background: #3b82f6; color: white; }

        @media print {
            body    { background: white !important; }
            .halaman-wrapper {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .toolbar-aksi { display: none !important; }

            @page {
                size: A4;
                margin: 2cm 2.5cm;
            }
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            font-style: italic;
        }
        
        @yield('styles')
    </style>
</head>
<body>

    {{-- Toolbar Aksi --}}
    <div class="toolbar-aksi">
        @if(isset($urlDownload) && $urlDownload)
            <a href="{{ $urlDownload }}" class="tombol-aksi tombol-unduh" id="btn-unduh-pdf">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg>
                Unduh PDF
            </a>
        @else
            <button onclick="window.print()" class="tombol-aksi tombol-unduh" id="btn-unduh-pdf" title="Unduh / Simpan sebagai PDF">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                </svg>
                Unduh PDF
            </button>
        @endif
        <button onclick="window.print()" class="tombol-aksi tombol-cetak" id="btn-cetak">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak Laporan
        </button>
    </div>

    {{-- Kertas A4 --}}
    <div class="halaman-wrapper">

        {{-- Kop Surat --}}
        <div class="kop-surat">
            <div class="kop-surat-inner">
                @if(file_exists(public_path('images/logo.png')))
                    <div class="logo-instansi">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Puskesmas">
                    </div>
                @endif
                <div class="info-instansi">
                    <h2>Pemerintah Provinsi DKI Jakarta</h2>
                    <h3>Puskesmas Kecamatan Cempaka Putih</h3>
                    <div class="alamat">
                        Jl. Cempaka Putih Tengah No. 1, Jakarta Pusat 10510<br>
                        Telepon: (021) 4244624 | Email: info@puskesmas-cempaka.id
                    </div>
                </div>
            </div>
        </div>
        <div class="garis-tebal"></div>

        {{-- Content --}}
        @yield('content')

    </div>{{-- /halaman-wrapper --}}

</body>
</html>
