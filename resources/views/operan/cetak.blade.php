@extends('layouts.laporan-formal')

@section('title', 'Laporan Operan Shift - ' . $labelPeriode)

@section('styles')
<style>
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
        width: 35px;
    }
    .tabel-operan .tanggal {
        width: 75px;
    }
    .tabel-operan .nama {
        font-weight: bold;
    }
    .tabel-operan .tugas-list {
        margin: 0;
        padding-left: 18px;
        font-size: 9.5pt;
    }
    .tabel-operan .tugas-list li {
        margin-bottom: 2px;
    }
    .tabel-operan .catatan-text {
        font-size: 9.5pt;
        font-style: italic;
    }
    .status-selesai { font-weight: bold; }
    .status-menunggu { font-style: italic; }
    .status-dieskalasi { font-weight: bold; text-decoration: underline; }
</style>
@endsection

@section('content')
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
@endsection
