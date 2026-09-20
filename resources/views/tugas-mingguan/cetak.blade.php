@extends('layouts.laporan-formal')

@section('title', 'Laporan Tugas Mingguan CS')

@section('styles')
<style>
    .tabel-rekap .col-no    { width: 30px; }
    .tabel-rekap .col-angka { width: 70px; }
    .tabel-detail .col-no   { width: 30px; }
    .tabel-detail .col-tgl  { width: 85px; }
    .tabel-detail .col-stat { width: 100px; }
</style>
@endsection

@section('content')
    @php
        $labelPeriode = \Carbon\Carbon::parse($dari)->translatedFormat('d F Y')
            . ' s/d '
            . \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y');
    @endphp

    {{-- Judul --}}
    <div class="judul-laporan">
        <h1>Laporan Tugas Mingguan Petugas Kebersihan</h1>
        <div class="nomor-surat">
            Nomor: {{ str_pad($totalTugas, 3, '0', STR_PAD_LEFT) }}/LAP-TM/{{ now()->format('m/Y') }}
        </div>
    </div>

    {{-- Pembuka --}}
    <div class="pembuka">
        Berdasarkan pencatatan kegiatan tugas mingguan petugas kebersihan di lingkungan
        Puskesmas Kecamatan Cempaka Putih, dengan ini kami laporkan rekapitulasi pelaksanaan
        tugas mingguan untuk periode
        <strong>{{ $labelPeriode }}</strong>
        @if($petugasFilter)
            atas nama petugas <strong>{{ $petugasFilter->name }}</strong>
        @endif
        sebagai berikut:
    </div>

    {{-- Info Periode --}}
    <div class="info-periode">
        <table>
            <tr>
                <td class="label">Periode Pelaporan</td>
                <td class="separator">:</td>
                <td>{{ $labelPeriode }}</td>
            </tr>
            @if($petugasFilter)
            <tr>
                <td class="label">Petugas yang Dilaporkan</td>
                <td class="separator">:</td>
                <td>{{ $petugasFilter->name }} (NIK: {{ $petugasFilter->nik }})</td>
            </tr>
            @endif
            @if($status)
            <tr>
                <td class="label">Filter Status</td>
                <td class="separator">:</td>
                <td>
                    @php $labelStatus = ['proses' => 'Dalam Proses', 'selesai' => 'Menunggu Verifikasi', 'disetujui' => 'Disetujui']; @endphp
                    {{ $labelStatus[$status] ?? ucfirst($status) }}
                </td>
            </tr>
            @endif
            <tr>
                <td class="label">Tanggal Pembuatan</td>
                <td class="separator">:</td>
                <td>{{ now()->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Waktu Pembuatan</td>
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

    {{-- I. Ringkasan --}}
    <div class="ringkasan">
        <h3>I. RINGKASAN REKAPITULASI</h3>
        <table>
            <tr>
                <td class="label">Total Tugas Tercatat</td>
                <td class="value">{{ $totalTugas }}</td>
            </tr>
            <tr>
                <td class="label">Tugas Disetujui Supervisor</td>
                <td class="value">{{ $totalDisetujui }}</td>
            </tr>
            <tr>
                <td class="label">Tugas Selesai (Menunggu Verifikasi)</td>
                <td class="value">{{ $totalSelesai - $totalDisetujui }}</td>
            </tr>
            <tr>
                <td class="label">Tugas Masih Dalam Proses</td>
                <td class="value">{{ $totalProses }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah Petugas yang Terlibat</td>
                <td class="value">{{ $rekapPertugas->count() }} orang</td>
            </tr>
        </table>
    </div>

    {{-- II. Rekap per Petugas --}}
    @if(!$petugasFilter)
    <div class="tabel-detail">
        <h3>II. REKAPITULASI PER PETUGAS</h3>
        @if($rekapPertugas->count() > 0)
            <table class="tabel-formal tabel-rekap">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th>Nama Petugas</th>
                        <th class="col-angka">Total Tugas</th>
                        <th class="col-angka">Disetujui</th>
                        <th class="col-angka">Selesai</th>
                        <th class="col-angka">Proses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapPertugas as $data)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="nama">{{ $data['nama'] }}</td>
                        <td class="text-center">{{ $data['total'] }}</td>
                        <td class="text-center">{{ $data['disetujui'] }}</td>
                        <td class="text-center">{{ $data['selesai'] - $data['disetujui'] }}</td>
                        <td class="text-center">{{ $data['proses'] }}</td>
                    </tr>
                    @endforeach
                    <tr style="font-weight: bold;">
                        <td colspan="2" class="text-center">TOTAL</td>
                        <td class="text-center">{{ $totalTugas }}</td>
                        <td class="text-center">{{ $totalDisetujui }}</td>
                        <td class="text-center">{{ $totalSelesai - $totalDisetujui }}</td>
                        <td class="text-center">{{ $totalProses }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="empty-state">Tidak ada data petugas pada periode ini.</div>
        @endif
    </div>
    @endif

    {{-- III. Rincian Tugas --}}
    <div class="tabel-detail">
        <h3>{{ $petugasFilter ? 'II.' : 'III.' }} RINCIAN PELAKSANAAN TUGAS MINGGUAN</h3>
        @if($daftarTugas->count() > 0)
            <table class="tabel-formal tabel-detail">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th class="col-tgl">Tanggal</th>
                        <th>Nama Petugas</th>
                        <th>Rincian Kegiatan</th>
                        <th>Dokumentasi</th>
                        <th class="col-stat">Status</th>
                        <th>Verifikator</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daftarTugas as $tugas)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">
                            {{ $tugas->tanggal->format('d/m/Y') }}<br>
                            <span style="font-size:8.5pt;">{{ \Carbon\Carbon::parse($tugas->waktu_pelaporan)->format('H:i') }} WIB</span>
                        </td>
                        <td class="nama">{{ $tugas->user->name ?? '-' }}</td>
                        <td style="font-size: 9pt;">{{ $tugas->rincian_kegiatan }}</td>
                        <td class="text-center" style="font-size: 9pt;">
                            @php
                                $jmlSebelum = is_array($tugas->foto_sebelum) ? count($tugas->foto_sebelum) : 0;
                            @endphp
                            {{ $jmlSebelum }} foto sebelum<br>
                            {{ $tugas->foto_setelah ? '1 foto sesudah' : '-' }}
                        </td>
                        <td class="text-center">
                            @if($tugas->status === 'disetujui')
                                <span class="status-selesai">Disetujui</span>
                            @elseif($tugas->status === 'selesai')
                                <span class="status-proses">Menunggu Verifikasi</span>
                            @else
                                <span class="status-pending">Dalam Proses</span>
                            @endif
                        </td>
                        <td style="font-size: 9pt;">
                            @if($tugas->verifikator)
                                {{ $tugas->verifikator->name }}<br>
                                <span style="font-size:8.5pt;">{{ $tugas->diverifikasi_pada?->format('d/m/Y H:i') }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">
                Tidak terdapat data tugas mingguan pada periode pelaporan ini.
            </div>
        @endif
    </div>

    {{-- Penutup --}}
    <div class="penutup">
        Demikian laporan tugas mingguan petugas kebersihan ini dibuat dengan sebenarnya
        berdasarkan data yang tercatat dalam sistem. Laporan ini dapat digunakan sebagai
        bahan evaluasi kinerja dan dokumentasi pelaksanaan tugas berkala petugas kebersihan.
        Atas perhatian dan kerjasamanya, kami ucapkan terima kasih.
    </div>

    {{-- Tanda Tangan --}}
    <div class="ttd-section">
        <div class="ttd-container">
            <div class="ttd-box">
                <div class="ttd-tempat">Mengetahui,</div>
                <div class="ttd-jabatan">
                    Kepala Puskesmas<br>
                    Kecamatan Cempaka Putih
                </div>
                <div class="ttd-nama">(...........................)</div>
                <div class="ttd-nip">NIP. -</div>
            </div>
            <div class="ttd-box right">
                <div class="ttd-tempat">Jakarta, {{ now()->translatedFormat('d F Y') }}</div>
                <div class="ttd-jabatan">
                    {{ ucfirst($pengguna->peran->nama_peran ?? 'Supervisor') }}<br>
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
