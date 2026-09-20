@extends('layouts.laporan-formal')

@section('title', 'Lembar Penilaian PJ Lantai - ' . $penilaian->nama_petugas_cs)

@section('styles')
<style>
    .info-box {
        border: 1px solid #000;
        padding: 10px 12px;
        margin: 20px 0;
    }
    .info-box table {
        width: 100%;
        font-size: 11pt;
    }
    .info-box td {
        padding: 3px 0;
        vertical-align: top;
    }
    .info-box .label {
        width: 180px;
        font-weight: bold;
    }
    .info-box .separator {
        width: 20px;
    }

    /* Tabel Indikator */
    .tabel-indikator {
        width: 100%;
        border-collapse: collapse;
        font-size: 10pt;
        margin-bottom: 10px;
    }
    .tabel-indikator th,
    .tabel-indikator td {
        border: 1px solid #000;
        padding: 6px 8px;
        vertical-align: top;
    }
    .tabel-indikator thead th {
        font-weight: bold;
        text-align: center;
        background: #fff;
    }
    .tabel-indikator .no { width: 30px; text-align: center; }
    .tabel-indikator .skor-col { width: 55px; text-align: center; font-weight: bold; }
    .tabel-indikator .grup-header {
        background: #f3f4f6;
        font-weight: bold;
        font-style: italic;
    }

    /* Ringkasan skor */
    .kotak-nilai {
        display: inline-block;
        border: 2px solid #000;
        padding: 4px 14px;
        font-size: 13pt;
        font-weight: bold;
        min-width: 45px;
        text-align: center;
    }
</style>
@endsection

@section('content')

    {{-- Judul Laporan --}}
    <div class="judul-laporan">
        <h1>Lembar Penilaian Kinerja Penanggung Jawab Lantai</h1>
        <div class="nomor-surat">
            Nomor: {{ str_pad($penilaian->id, 3, '0', STR_PAD_LEFT) }}/LPN-PJ/{{ $penilaian->tanggal_penilaian->format('m/Y') }}
        </div>
    </div>

    {{-- Pembuka --}}
    <div class="pembuka">
        Berdasarkan pengamatan dan evaluasi kinerja petugas Penanggung Jawab Lantai di lingkungan
        Puskesmas Kecamatan Cempaka Putih, dengan ini kami laporkan hasil penilaian kinerja
        pada tanggal <strong>{{ $penilaian->tanggal_penilaian->translatedFormat('d F Y') }}</strong> sebagai berikut:
    </div>

    {{-- Identitas Petugas --}}
    <div class="info-box">
        <table>
            <tr>
                <td class="label">Nama Petugas yang Dinilai</td>
                <td class="separator">:</td>
                <td><strong>{{ $penilaian->nama_petugas_cs }}</strong></td>
            </tr>
            <tr>
                <td class="label">Jabatan</td>
                <td class="separator">:</td>
                <td>Penanggung Jawab Lantai / Cleaning Service</td>
            </tr>
            <tr>
                <td class="label">Area / Lokasi Tugas</td>
                <td class="separator">:</td>
                <td>{{ $penilaian->lokasi_tugas }}</td>
            </tr>
            <tr>
                <td class="label">Nama PJ Lantai</td>
                <td class="separator">:</td>
                <td>{{ $penilaian->nama_pj }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Penilaian</td>
                <td class="separator">:</td>
                <td>{{ $penilaian->tanggal_penilaian->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Dinilai Oleh</td>
                <td class="separator">:</td>
                <td>{{ $penilaiData->name }} ({{ ucfirst($penilaiData->peran->nama_peran ?? '-') }})</td>
            </tr>
        </table>
    </div>

    {{-- Tabel Indikator Penilaian --}}
    <div class="tabel-detail">
        <h3>I. RINCIAN PENILAIAN KINERJA</h3>

        <table class="tabel-indikator">
            <thead>
                <tr>
                    <th class="no">No.</th>
                    <th>Indikator Penilaian</th>
                    <th>Deskripsi</th>
                    <th class="skor-col">Skor<br>(1–5)</th>
                </tr>
            </thead>
            <tbody>
                @php $nomorUrut = 1; @endphp

                @foreach($indikator as $grupNama => $items)
                    {{-- Baris header grup --}}
                    <tr>
                        <td colspan="4" class="grup-header">{{ $grupNama }}</td>
                    </tr>

                    @foreach($items as $kolom => $info)
                    <tr>
                        <td class="no">{{ $nomorUrut++ }}</td>
                        <td>{{ $info['label'] }}</td>
                        <td style="font-size: 9pt; color: #555;">{{ $info['deskripsi'] }}</td>
                        <td class="skor-col">{{ $penilaian->$kolom ?? '-' }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Ringkasan Hasil --}}
    <div class="ringkasan">
        <h3>II. RINGKASAN HASIL PENILAIAN</h3>
        <table>
            <tr>
                <td class="label">Total Indikator yang Dinilai</td>
                <td class="value">16 indikator</td>
            </tr>
            <tr>
                <td class="label">Nilai Rata-Rata</td>
                <td class="value">
                    <span class="kotak-nilai">{{ number_format($penilaian->rata_rata, 1) }}</span>
                </td>
            </tr>
            <tr>
                <td class="label">Kategori Penilaian</td>
                <td class="value"><strong>{{ $penilaian->kategori }}</strong></td>
            </tr>
            <tr>
                <td class="label">Keterangan Kategori</td>
                <td class="value">
                    @if($penilaian->rata_rata >= 86)
                        Sangat Baik (skor ≥ 86)
                    @elseif($penilaian->rata_rata >= 76)
                        Baik (skor 76 – 85)
                    @elseif($penilaian->rata_rata >= 75)
                        Cukup (skor 75)
                    @elseif($penilaian->rata_rata > 50)
                        Kurang (skor 51 – 74)
                    @else
                        Sangat Kurang (skor ≤ 50)
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Masukan / Evaluasi --}}
    @if($penilaian->masukan_evaluasi)
    <div style="margin: 20px 0;">
        <div style="font-weight: bold; font-size: 11pt; text-decoration: underline; margin-bottom: 8px;">
            III. MASUKAN DAN SARAN PERBAIKAN
        </div>
        <div style="border: 1px solid #000; padding: 10px 12px; min-height: 60px; font-size: 11pt; text-align: justify;">
            {{ $penilaian->masukan_evaluasi }}
        </div>
    </div>
    @endif

    {{-- Bukti Dokumentasi --}}
    @if($penilaian->bukti_dokumentasi && count($penilaian->bukti_dokumentasi) > 0)
    <div style="margin: 20px 0;">
        <div style="font-weight: bold; font-size: 11pt; text-decoration: underline; margin-bottom: 8px;">
            {{ $penilaian->masukan_evaluasi ? 'IV.' : 'III.' }} BUKTI DOKUMENTASI
        </div>
        <div style="font-size: 10pt;">
            Terlampir {{ count($penilaian->bukti_dokumentasi) }} berkas dokumentasi foto sebagai bukti penilaian.
        </div>
    </div>
    @endif

    {{-- Penutup --}}
    <div class="penutup">
        Demikian lembar penilaian kinerja ini dibuat dengan sebenarnya berdasarkan hasil evaluasi
        dan pengamatan langsung di lapangan. Penilaian ini dapat digunakan sebagai bahan evaluasi
        kinerja dan perencanaan pengembangan kapasitas petugas kebersihan. Atas perhatian dan
        kerjasamanya, kami ucapkan terima kasih.
    </div>

    {{-- Tanda Tangan --}}
    <div class="ttd-section">
        <div class="ttd-container">
            <div class="ttd-box">
                <div class="ttd-tempat">Mengetahui,</div>
                <div class="ttd-jabatan">
                    Petugas yang Dinilai<br>
                    (PJ Lantai / Cleaning Service)
                </div>
                <div class="ttd-nama">{{ $penilaian->nama_petugas_cs }}</div>
                <div class="ttd-nip">NIP. -</div>
            </div>
            <div class="ttd-box right">
                <div class="ttd-tempat">
                    Jakarta, {{ $penilaian->tanggal_penilaian->translatedFormat('d F Y') }}
                </div>
                <div class="ttd-jabatan">
                    Penilai<br>
                    ({{ ucfirst($penilaiData->peran->nama_peran ?? 'Supervisor') }})
                </div>
                <div class="ttd-nama">{{ $penilaiData->name }}</div>
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
            <li>Yang bersangkutan</li>
            <li>Arsip</li>
        </ol>
    </div>

@endsection
