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
        
        /* Print Button */
        .no-print {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 11pt;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            z-index: 1000;
            font-family: Arial, sans-serif;
        }
        
        .no-print:hover {
            background: #059669;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                margin: 0;
                padding: 0;
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
    {{-- Tombol Print --}}
    <button onclick="window.print()" class="no-print">
        🖨️ Cetak Laporan
    </button>

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

</body>
</html>
