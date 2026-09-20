<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Penilaian PJ Lantai - {{ $penilaian->nama_petugas_cs }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 15mm 20mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }

        /* Kop Surat */
        .kop-surat {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-container {
            display: table;
            width: 100%;
        }

        .kop-logo {
            display: table-cell;
            width: 90px;
            vertical-align: middle;
            padding-right: 15px;
        }

        .kop-logo img {
            width: 85px;
            height: auto;
        }

        .kop-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .kop-text h1 {
            font-size: 16pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .kop-text h2 {
            font-size: 14pt;
            font-weight: bold;
            margin: 2px 0;
        }

        .kop-text p {
            font-size: 10pt;
            margin: 2px 0;
            font-weight: normal;
        }

        /* Judul Dokumen */
        .doc-title {
            text-align: center;
            margin: 25px 0 20px;
        }

        .doc-title h3 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 5px;
        }

        .doc-title p {
            font-size: 10pt;
            font-style: italic;
        }

        /* Info Petugas */
        .info-box {
            border: 1px solid #000;
            padding: 12px;
            margin-bottom: 20px;
            background: #f9f9f9;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }

        .info-row:last-child {
            margin-bottom: 0;
        }

        .info-label {
            display: table-cell;
            width: 180px;
            font-weight: bold;
            padding-right: 10px;
        }

        .info-separator {
            display: table-cell;
            width: 15px;
        }

        .info-value {
            display: table-cell;
        }

        /* Skor Total */
        .skor-box {
            text-align: center;
            border: 2px solid #000;
            padding: 15px;
            margin: 20px auto;
            width: 400px;
            background: #f0f0f0;
        }

        .skor-box .label {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .skor-box .nilai {
            font-size: 28pt;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 8px;
        }

        .skor-box .kategori {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* Tabel Penilaian */
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 20px 0 10px;
            padding: 5px 10px;
            background: #e0e0e0;
            border-left: 4px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        table thead th {
            background: #d0d0d0;
            font-weight: bold;
            text-align: center;
        }

        table tbody td:nth-child(1) {
            width: 35px;
            text-align: center;
            font-weight: bold;
        }

        table tbody td:nth-child(3) {
            width: 60px;
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
        }

        table tbody td:nth-child(4) {
            width: 100px;
            text-align: center;
        }

        .indikator-desc {
            font-size: 9pt;
            color: #444;
            font-style: italic;
            margin-top: 3px;
        }

        /* Masukan Evaluasi */
        .evaluasi-box {
            border: 1px solid #000;
            padding: 12px;
            margin: 15px 0;
            min-height: 80px;
            background: #fafafa;
        }

        .evaluasi-box h4 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .evaluasi-box p {
            font-size: 10pt;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        /* Footer Dokumen */
        .doc-footer {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-area {
            display: table;
            width: 100%;
            margin-top: 30px;
        }

        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
        }

        .signature-box p {
            margin-bottom: 5px;
        }

        .signature-space {
            height: 60px;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 5px;
        }

        /* Print Styles */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            @page {
                margin: 15mm 20mm;
            }

            table {
                page-break-inside: avoid;
            }

            .section-title {
                page-break-after: avoid;
            }

            .doc-footer {
                page-break-inside: avoid;
            }
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 12px 24px;
            background: #12a65a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
        }

        .print-button:hover {
            background: #0d8a4a;
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>
<body>

    {{-- Print Button --}}
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Cetak Dokumen
    </button>

    {{-- KOP SURAT --}}
    <div class="kop-surat">
        <div class="kop-container">
            <div class="kop-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Puskesmas">
            </div>
            <div class="kop-text">
                <h1>PUSKESMAS KECAMATAN CEMPAKA PUTIH</h1>
                <h2>Dinas Kesehatan Kota Jakarta Pusat</h2>
                <p>Jl. Cempaka Putih Tengah No. 1, Jakarta Pusat 10510</p>
                <p>Telp: (021) 4244515 | Email: puskesmas.cempaka@jakarta.go.id</p>
            </div>
        </div>
    </div>

    {{-- JUDUL DOKUMEN --}}
    <div class="doc-title">
        <h3>LEMBAR PENILAIAN KINERJA PETUGAS CLEANING SERVICE</h3>
        <p>Penilaian oleh Penanggung Jawab Kebersihan Ruangan</p>
    </div>

    {{-- INFORMASI PETUGAS YANG DINILAI --}}
    <div class="info-box">
        <div class="info-row">
            <div class="info-label">Nama Petugas CS</div>
            <div class="info-separator">:</div>
            <div class="info-value">{{ $penilaian->nama_petugas_cs }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Lokasi Penugasan</div>
            <div class="info-separator">:</div>
            <div class="info-value">{{ $penilaian->lokasi_tugas }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal Penilaian</div>
            <div class="info-separator">:</div>
            <div class="info-value">{{ $penilaian->tanggal_penilaian->translatedFormat('l, d F Y') }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Penilai (PJ Kebersihan)</div>
            <div class="info-separator">:</div>
            <div class="info-value">{{ $penilaian->nama_pj }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nomor Kontak Penilai</div>
            <div class="info-separator">:</div>
            <div class="info-value">{{ $penilaian->no_hp_pj ?? '-' }}</div>
        </div>
    </div>

    {{-- SKOR TOTAL --}}
    <div class="skor-box">
        <div class="label">RATA-RATA SKOR PENILAIAN</div>
        <div class="nilai">{{ $penilaian->rata_rata }}</div>
        <div class="kategori">{{ $penilaian->kategori }}</div>
    </div>

    {{-- RINCIAN PENILAIAN PER INDIKATOR --}}
    @foreach($indikator as $kategoriNama => $items)
        <div class="section-title">{{ strtoupper($kategoriNama) }}</div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Indikator Penilaian</th>
                    <th>Skor</th>
                    <th>Kategori</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $fieldKey => $info)
                    @php
                        $skor = (int) $penilaian->{$fieldKey};
                        $kat = \App\Models\PenilaianPjLantai::hitungKategori($skor);
                    @endphp
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>{{ $info['label'] }}</strong>
                            <div class="indikator-desc">{{ $info['deskripsi'] }}</div>
                        </td>
                        <td>{{ $skor }}</td>
                        <td>{{ $kat }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach

    {{-- MASUKAN / EVALUASI --}}
    <div class="evaluasi-box">
        <h4>Masukan / Evaluasi dari Penanggung Jawab Kebersihan:</h4>
        <p>{{ $penilaian->masukan_evaluasi }}</p>
    </div>

    {{-- FOOTER & TANDA TANGAN --}}
    <div class="doc-footer">
        <div class="signature-area">
            <div class="signature-box">
                <p>Mengetahui,</p>
                <p><strong>Kepala Puskesmas</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">(...........................)</p>
            </div>
            <div class="signature-box">
                <p>Jakarta, {{ now()->translatedFormat('d F Y') }}</p>
                <p><strong>Penanggung Jawab Kebersihan</strong></p>
                <div class="signature-space"></div>
                <p class="signature-name">{{ $penilaian->nama_pj }}</p>
            </div>
        </div>

        <p style="margin-top: 30px; font-size: 9pt; text-align: center; color: #666;">
            Dokumen ini dicetak dari Sistem Informasi Manajemen Kebersihan Puskesmas Cempaka Putih<br>
            Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }} WIB
        </p>
    </div>

</body>
</html>
