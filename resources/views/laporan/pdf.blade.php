<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kinerja Kebersihan - {{ $judul }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background: #e5e7eb; /* abu-abu di luar kertas saat preview */
        }

        /* ── Wrapper kertas saat tampil di browser ── */
        .halaman {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            margin: 20mm auto;
            padding: 2cm 2.5cm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        /* ── Tombol cetak (hanya tampil di layar) ── */
        .tombol-cetak {
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
            box-shadow: 0 4px 12px rgba(16,185,129,0.35);
            z-index: 1000;
            font-family: Arial, sans-serif;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .tombol-cetak:hover { background: #059669; }

        /* ── Kop Surat ── */
        .kop-surat {
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 5px;
        }
        .kop-table { width: 100%; border-collapse: collapse; }
        .kop-table td { vertical-align: middle; padding: 0; border: none; }
        .kop-logo  { width: 85px; padding-right: 12px; }
        .kop-logo img { width: 80px; height: 80px; }
        .kop-teks  { text-align: center; }
        .kop-teks h2 {
            font-size: 17pt; font-weight: bold;
            text-transform: uppercase; margin-bottom: 2px;
        }
        .kop-teks h3 { font-size: 15pt; font-weight: bold; margin-bottom: 4px; }
        .kop-teks p  { font-size: 10pt; line-height: 1.4; }
        .garis-tipis { border-top: 1px solid #000; margin-top: 3px; }

        /* ── Judul ── */
        .judul-laporan { text-align: center; margin: 28px 0 22px; }
        .judul-laporan h1 {
            font-size: 14pt; font-weight: bold;
            text-decoration: underline; text-transform: uppercase; margin-bottom: 5px;
        }
        .nomor-surat { font-size: 11pt; }

        /* ── Pembuka ── */
        .pembuka {
            text-align: justify; margin-bottom: 20px; text-indent: 50px;
        }

        /* ── Info Periode ── */
        .info-periode { margin: 20px 0; }
        .info-periode table { width: 100%; font-size: 11pt; border-collapse: collapse; }
        .info-periode td { padding: 3px 0; vertical-align: top; border: none; }
        .info-periode .label    { width: 200px; font-weight: bold; }
        .info-periode .separator { width: 15px; }

        /* ── Ringkasan ── */
        .ringkasan { margin: 25px 0; }
        .ringkasan h3 {
            font-size: 12pt; font-weight: bold;
            margin-bottom: 10px; text-decoration: underline;
        }
        .ringkasan table { width: 100%; border-collapse: collapse; font-size: 11pt; }
        .ringkasan td { padding: 5px 10px; border: 1px solid #000; }
        .ringkasan .label { width: 70%; }
        .ringkasan .value { width: 30%; text-align: center; font-weight: bold; }

        /* ── Tabel Detail ── */
        .tabel-detail { margin: 25px 0; }
        .tabel-detail h3 {
            font-size: 12pt; font-weight: bold;
            margin-bottom: 10px; text-decoration: underline;
        }
        .tabel-formal { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .tabel-formal th,
        .tabel-formal td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        .tabel-formal thead th { font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .nama { font-weight: bold; }

        /* ── Penutup ── */
        .penutup { margin-top: 25px; text-align: justify; text-indent: 50px; }

        /* ── TTD ── */
        .ttd-section { margin-top: 40px; }
        .ttd-table { width: 100%; border-collapse: collapse; }
        .ttd-table td { width: 50%; vertical-align: top; border: none; padding: 0; }
        .ttd-tempat  { margin-bottom: 5px; }
        .ttd-jabatan { font-weight: bold; margin-bottom: 70px; }
        .ttd-nama    { font-weight: bold; text-decoration: underline; }
        .ttd-nip     { font-size: 10pt; margin-top: 2px; }

        /* ── Tembusan ── */
        .tembusan { margin-top: 30px; }
        .tembusan-title { font-weight: bold; margin-bottom: 5px; }
        .tembusan ol { margin-left: 20px; padding-left: 5px; }
        .tembusan li { margin-bottom: 3px; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 20px; font-style: italic; }

        /* ── PRINT: hilangkan latar abu, box shadow, tombol ── */
        @media print {
            body    { background: white !important; }
            .halaman {
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
            }
            .tombol-cetak { display: none !important; }

            @page {
                size: A4;
                margin: 2cm 2.5cm;
            }
        }
    </style>
</head>
<body>

    {{-- Tombol Cetak --}}
    <button onclick="window.print()" class="tombol-cetak">
        🖨️ Cetak Laporan
    </button>

    <div class="halaman">

        {{-- Kop Surat --}}
        <div class="kop-surat">
            <table class="kop-table">
                <tr>
                    @if(file_exists(public_path('images/logo.png')))
                    <td class="kop-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo">
                    </td>
                    @endif
                    <td class="kop-teks">
                        <h2>Pemerintah Provinsi DKI Jakarta</h2>
                        <h3>Puskesmas Kecamatan Cempaka Putih</h3>
                        <p>Jl. Cempaka Putih Tengah No. 1, Jakarta Pusat 10510</p>
                        <p>Telepon: (021) 4244624 &nbsp;|&nbsp; Email: info@puskesmas-cempaka.id</p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="garis-tipis"></div>

        {{-- Judul --}}
        <div class="judul-laporan">
            <h1>Laporan Kinerja Kebersihan</h1>
            <div class="nomor-surat">
                Nomor: {{ now()->format('m') }}/LAP-KB/{{ now()->format('m/Y') }}
            </div>
        </div>

        {{-- Pembuka --}}
        <div class="pembuka">
            Berdasarkan data yang tercatat dalam Sistem Informasi Manajemen Kebersihan
            Puskesmas Kecamatan Cempaka Putih, dengan ini kami laporkan rekapitulasi
            kinerja kebersihan untuk periode <strong>{{ $judul }}</strong>
            ({{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s/d
            {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}) sebagai berikut:
        </div>

        {{-- Info Periode --}}
        <div class="info-periode">
            <table>
                <tr>
                    <td class="label">Periode Pelaporan</td>
                    <td class="separator">:</td>
                    <td>{{ $judul }}</td>
                </tr>
                <tr>
                    <td class="label">Rentang Tanggal</td>
                    <td class="separator">:</td>
                    <td>
                        {{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s/d
                        {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}
                    </td>
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
            </table>
        </div>

        {{-- I. Ringkasan --}}
        <div class="ringkasan">
            <h3>I. RINGKASAN EKSEKUTIF</h3>
            <table>
                <tr>
                    <td class="label">Total Ceklis Area Dibuat</td>
                    <td class="value">{{ $totalCeklis }}</td>
                </tr>
                <tr>
                    <td class="label">Ceklis Area Diselesaikan</td>
                    <td class="value">{{ $ceklisSelesai }} ({{ $ceklisPersen }}%)</td>
                </tr>
                <tr>
                    <td class="label">Total Permintaan Barang</td>
                    <td class="value">{{ $totalPermintaan }}</td>
                </tr>
                <tr>
                    <td class="label">Permintaan Barang Disetujui</td>
                    <td class="value">{{ $permintaanDisetujui }}</td>
                </tr>
                <tr>
                    <td class="label">Permintaan Barang Ditolak</td>
                    <td class="value">{{ $permintaanDitolak }}</td>
                </tr>
                <tr>
                    <td class="label">Total Laporan Setoran Sampah</td>
                    <td class="value">{{ $totalSetoran }} laporan</td>
                </tr>
                <tr>
                    <td class="label">Rata-rata Nilai Kinerja Petugas</td>
                    <td class="value">
                        @if($rataKinerja)
                            {{ $rataKinerja > 5
                                ? $rataKinerja . ' / 100'
                                : number_format($rataKinerja, 1) . ' / 5.0' }}
                        @else
                            Belum ada data
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Petugas Terbaik (Top Performer)</td>
                    <td class="value">{{ $topPerformer['nama'] ?? 'Belum ada data' }}</td>
                </tr>
            </table>
        </div>

        {{-- II. Ceklis per Area --}}
        <div class="tabel-detail">
            <h3>II. RINCIAN CEKLIS KEBERSIHAN PER AREA</h3>
            @if($ceklisPerArea->count() > 0)
                <table class="tabel-formal">
                    <thead>
                        <tr>
                            <th style="width:30px;">No.</th>
                            <th>Nama Ruangan / Area</th>
                            <th style="width:75px;">Total Ceklis</th>
                            <th style="width:75px;">Diselesaikan</th>
                            <th style="width:75px;">Belum Selesai</th>
                            <th style="width:75px;">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ceklisPerArea as $area)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="nama">{{ $area['nama'] }}</td>
                            <td class="text-center">{{ $area['total'] }}</td>
                            <td class="text-center">{{ $area['selesai'] }}</td>
                            <td class="text-center">{{ $area['total'] - $area['selesai'] }}</td>
                            <td class="text-center">
                                {{ $area['total'] > 0 ? round(($area['selesai'] / $area['total']) * 100) : 0 }}%
                            </td>
                        </tr>
                        @endforeach
                        <tr style="font-weight:bold;">
                            <td colspan="2" class="text-center">TOTAL</td>
                            <td class="text-center">{{ $totalCeklis }}</td>
                            <td class="text-center">{{ $ceklisSelesai }}</td>
                            <td class="text-center">{{ $totalCeklis - $ceklisSelesai }}</td>
                            <td class="text-center">{{ $ceklisPersen }}%</td>
                        </tr>
                    </tbody>
                </table>
            @else
                <div class="empty-state">Tidak terdapat data ceklis kebersihan pada periode ini.</div>
            @endif
        </div>

        {{-- III. Permintaan Barang --}}
        <div class="tabel-detail">
            <h3>III. RINGKASAN PERMINTAAN BARANG</h3>
            <table class="tabel-formal">
                <thead>
                    <tr>
                        <th>Keterangan</th>
                        <th style="width:90px;">Jumlah</th>
                        <th style="width:90px;">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Total Permintaan Masuk</td>
                        <td class="text-center">{{ $totalPermintaan }}</td>
                        <td class="text-center">100%</td>
                    </tr>
                    <tr>
                        <td>Disetujui Gudang</td>
                        <td class="text-center">{{ $permintaanDisetujui }}</td>
                        <td class="text-center">
                            {{ $totalPermintaan > 0 ? round(($permintaanDisetujui / $totalPermintaan) * 100) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td>Ditolak Gudang</td>
                        <td class="text-center">{{ $permintaanDitolak }}</td>
                        <td class="text-center">
                            {{ $totalPermintaan > 0 ? round(($permintaanDitolak / $totalPermintaan) * 100) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td>Masih Dalam Proses</td>
                        <td class="text-center">{{ $totalPermintaan - $permintaanDisetujui - $permintaanDitolak }}</td>
                        <td class="text-center">
                            {{ $totalPermintaan > 0
                                ? round((($totalPermintaan - $permintaanDisetujui - $permintaanDitolak) / $totalPermintaan) * 100)
                                : 0 }}%
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Penutup --}}
        <div class="penutup">
            Demikian laporan kinerja kebersihan ini disusun berdasarkan data yang tercatat
            dalam sistem informasi manajemen kebersihan. Laporan ini dapat dijadikan acuan
            dalam evaluasi kinerja dan pengambilan keputusan manajemen. Atas perhatian dan
            kerjasamanya, kami ucapkan terima kasih.
        </div>

        {{-- TTD --}}
        <div class="ttd-section">
            <table class="ttd-table">
                <tr>
                    <td>
                        <div class="ttd-tempat">Mengetahui,</div>
                        <div class="ttd-jabatan">
                            Kepala Puskesmas<br>
                            Kecamatan Cempaka Putih
                        </div>
                        <div class="ttd-nama">(...........................)</div>
                        <div class="ttd-nip">NIP. -</div>
                    </td>
                    <td>
                        <div class="ttd-tempat">
                            Jakarta, {{ now()->translatedFormat('d F Y') }}
                        </div>
                        <div class="ttd-jabatan">
                            Supervisor / Admin<br>
                            Puskesmas Kecamatan Cempaka Putih
                        </div>
                        <div class="ttd-nama">(...........................)</div>
                        <div class="ttd-nip">NIP. -</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Tembusan --}}
        <div class="tembusan">
            <div class="tembusan-title">Tembusan:</div>
            <ol>
                <li>Kepala Puskesmas Kecamatan Cempaka Putih</li>
                <li>Koordinator Kebersihan</li>
                <li>Koordinator Gudang</li>
                <li>Arsip</li>
            </ol>
        </div>

    </div>{{-- /halaman --}}

</body>
</html>
