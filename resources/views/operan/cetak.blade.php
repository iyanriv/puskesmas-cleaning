<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Operan Shift - {{ $labelPeriode }}</title>
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
        
        .tabel-operan {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }
        
        .tabel-operan th,
        .tabel-operan td {
            border: 1px solid #000;
            padding: 6px 8px;
            vertical-align: top;
        }
        
        .tabel-operan thead th {
            background: #fff;
            font-weight: bold;
            text-align: center;
        }
        
        .tabel-operan tbody td {
            text-align: left;
        }
        
        .tabel-operan .text-center {
            text-align: center;
        }
        
        .tabel-operan .no {
            width: 30px;
            text-align: center;
        }
        
        .tabel-operan .tanggal {
            width: 80px;
        }
        
        .tabel-operan .nama {
            font-weight: bold;
        }
        
        .tabel-operan .tugas-list {
            margin: 5px 0 0 0;
            padding-left: 15px;
            font-size: 9.5pt;
        }
        
        .tabel-operan .tugas-list li {
            margin-bottom: 2px;
        }
        
        .tabel-operan .catatan-text {
            font-size: 9.5pt;
            font-style: italic;
        }
        
        /* Status */
        .status-selesai { font-weight: bold; }
        .status-menunggu { font-style: italic; }
        .status-dieskalasi { font-weight: bold; text-decoration: underline; }
        
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

    {{-- Judul Laporan --}}
    <div class="judul-laporan">
        <h1>Laporan Operan Shift Petugas Kebersihan</h1>
        <div class="nomor-surat">Nomor: {{ str_pad($totalOperan, 3, '0', STR_PAD_LEFT) }}/LAP-OS/{{ now()->format('m/Y') }}</div>
    </div>

    {{-- Pembuka --}}
    <div class="pembuka">
        Berdasarkan pelaksanaan tugas operan shift petugas kebersihan di lingkungan Puskesmas Kecamatan Cempaka Putih, 
        dengan ini kami laporkan rekapitulasi operan shift periode <strong>{{ $labelPeriode }}</strong> 
        ({{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}) 
        sebagai berikut:
    </div>

    {{-- Info Periode --}}
    <div class="info-periode">
        <table>
            <tr>
                <td class="label">Periode Pelaporan</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pembuatan Laporan</td>
                <td class="separator">:</td>
                <td>{{ now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Waktu Pembuatan Laporan</td>
                <td class="separator">:</td>
                <td>{{ now()->format('H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="label">Dibuat Oleh</td>
                <td class="separator">:</td>
                <td>{{ $pengguna->name }} ({{ ucfirst($pengguna->peran->nama_peran ?? '-') }})</td>
            </tr>
        </table>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="ringkasan">
        <h3>I. RINGKASAN REKAPITULASI</h3>
        <table>
            <tr>
                <td class="label">Total Operan Shift yang Tercatat</td>
                <td class="value">{{ $totalOperan }}</td>
            </tr>
            <tr>
                <td class="label">Operan yang Menunggu Konfirmasi Penerima</td>
                <td class="value">{{ $menungguKonfirmasi }}</td>
            </tr>
            <tr>
                <td class="label">Tugas yang Telah Diselesaikan</td>
                <td class="value">{{ $tugasSelesai }}</td>
            </tr>
            <tr>
                <td class="label">Tugas yang Dieskalasi ke Shift Berikutnya</td>
                <td class="value">{{ $tugasDieskalasi }}</td>
            </tr>
        </table>
    </div>

    {{-- Tabel Detail --}}
    <div class="tabel-detail">
        <h3>II. RINCIAN OPERAN SHIFT</h3>
        
        @if($operanList->count() > 0)
            <table class="tabel-operan">
                <thead>
                    <tr>
                        <th class="no">No.</th>
                        <th class="tanggal">Tanggal/<br>Waktu</th>
                        <th>Petugas Pengirim</th>
                        <th>Petugas Penerima</th>
                        <th>Tempat Tugas &<br>Waktu Jaga</th>
                        <th>Uraian Kegiatan & Tugas</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($operanList as $index => $operan)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($operan->tanggal)->format('d/m/Y') }}<br>
                            {{ \Carbon\Carbon::parse($operan->waktu)->format('H:i') }}
                        </td>
                        <td>
                            <div class="nama">{{ $operan->pengirim->name ?? '-' }}</div>
                            <div style="font-size: 9pt;">({{ ucfirst($operan->pengirim->peran->nama_peran ?? '-') }})</div>
                        </td>
                        <td>
                            <div class="nama">{{ $operan->penerima->name ?? '-' }}</div>
                            <div style="font-size: 9pt;">({{ ucfirst($operan->penerima->peran->nama_peran ?? '-') }})</div>
                        </td>
                        <td>
                            <strong>{{ $operan->tempat_tugas }}</strong><br>
                            <span style="font-size: 9.5pt;">{{ $operan->waktu_jaga }}</span>
                        </td>
                        <td>
                            @if($operan->catatan)
                                <div class="catatan-text">{{ $operan->catatan }}</div>
                            @endif
                            
                            @if($operan->tugas_items && count($operan->tugas_items) > 0)
                                <div style="margin-top: 5px;"><strong>Tugas yang harus diselesaikan:</strong></div>
                                <ol class="tugas-list">
                                    @foreach($operan->tugas_items as $tugas)
                                        <li>{{ $tugas }}</li>
                                    @endforeach
                                </ol>
                            @endif
                            
                            @if($operan->parentOperan)
                                <div style="font-size: 9pt; margin-top: 5px;">
                                    <em>* Eskalasi dari operan sebelumnya (Pengirim awal: {{ $operan->parentOperan->pengirim->name ?? '-' }})</em>
                                </div>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($operan->status_terima === 'menunggu')
                                <span class="status-menunggu">Menunggu<br>Konfirmasi</span>
                            @elseif($operan->status_penyelesaian === 'selesai')
                                <span class="status-selesai">Selesai</span>
                            @elseif($operan->status_penyelesaian === 'dieskalasi')
                                <span class="status-dieskalasi">Dieskalasi</span>
                            @else
                                <span>Diterima</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                Tidak terdapat data operan shift pada periode pelaporan ini.
            </div>
        @endif
    </div>

    {{-- Penutup --}}
    <div class="penutup">
        Demikian laporan operan shift ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya. 
        Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
    </div>

    {{-- Tanda Tangan --}}
    <div class="ttd-section">
        <div class="ttd-container">
            <div class="ttd-box"></div>
            <div class="ttd-box right">
                <div class="ttd-tempat">Jakarta, {{ now()->translatedFormat('d F Y') }}</div>
                <div class="ttd-jabatan">
                    {{ ucfirst($pengguna->peran->nama_peran ?? 'Petugas') }}<br>
                    Puskesmas Kecamatan Cempaka Putih
                </div>
                <div class="ttd-nama">{{ $pengguna->name }}</div>
                <div class="ttd-nip">NIP. -</div>
            </div>
        </div>
    </div>

    {{-- Tembusan --}}
    <div class="tembusan">
        <div class="tembusan-title">Tembusan:</div>
        <ol>
            <li>Kepala Puskesmas Kecamatan Cempaka Putih</li>
            <li>Koordinator Kebersihan</li>
            <li>Arsip</li>
        </ol>
    </div>

</body>
</html>
