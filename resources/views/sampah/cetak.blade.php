@extends('layouts.laporan-formal')

@section('title', 'Laporan Bank Sampah - ' . $labelPeriode)

@section('styles')
<style>
    .tabel-jenis .col-no    { width: 30px; }
    .tabel-jenis .col-angka { width: 80px; }
    .tabel-detail .col-no   { width: 30px; }
    .tabel-detail .col-tgl  { width: 80px; }
    .tabel-detail .col-stat { width: 90px; }
</style>
@endsection

@section('content')

    {{-- Judul Laporan --}}
    <div class="judul-laporan">
        <h1>Laporan Bank Sampah</h1>
        <div class="nomor-surat">
            Nomor: {{ str_pad($totalSetoran, 3, '0', STR_PAD_LEFT) }}/LAP-BS/{{ now()->format('m/Y') }}
        </div>
    </div>

    {{-- Pembuka --}}
    <div class="pembuka">
        Berdasarkan pencatatan kegiatan bank sampah di lingkungan Puskesmas Kecamatan Cempaka Putih,
        dengan ini kami laporkan rekapitulasi setoran sampah daur ulang untuk periode
        <strong>{{ $labelPeriode }}</strong>
        ({{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s/d
        {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}) sebagai berikut:
    </div>

    {{-- Info Periode --}}
    <div class="info-periode">
        <table>
            <tr>
                <td class="label">Periode Pelaporan</td>
                <td class="separator">:</td>
                <td>{{ $labelPeriode }}</td>
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
                <td class="label">Total Laporan Setoran Diterima</td>
                <td class="value">{{ $totalSetoran }}</td>
            </tr>
            <tr>
                <td class="label">Laporan yang Telah Divalidasi</td>
                <td class="value">{{ $totalValid }}</td>
            </tr>
            <tr>
                <td class="label">Laporan Menunggu Validasi</td>
                <td class="value">{{ $totalMenunggu }}</td>
            </tr>
            <tr>
                <td class="label">Laporan yang Ditolak</td>
                <td class="value">{{ $totalDitolak }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah Jenis Sampah Tercatat</td>
                <td class="value">{{ count($rekapJenis) }} jenis</td>
            </tr>
        </table>
    </div>

    {{-- II. Rekap per Jenis --}}
    <div class="tabel-detail">
        <h3>II. REKAPITULASI PER JENIS SAMPAH</h3>
        @if(count($rekapJenis) > 0)
            <table class="tabel-formal tabel-jenis">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th>Jenis Sampah</th>
                        <th class="col-angka">Jumlah Laporan</th>
                        <th class="col-angka">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapJenis as $jenis => $jumlah)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="nama">{{ $jenis }}</td>
                        <td class="text-center">{{ $jumlah }} laporan</td>
                        <td class="text-center">
                            {{ $totalSetoran > 0 ? number_format(($jumlah / $totalSetoran) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                    <tr style="font-weight: bold;">
                        <td colspan="2" class="text-center">TOTAL</td>
                        <td class="text-center">{{ array_sum($rekapJenis) }}</td>
                        <td class="text-center">100%</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="empty-state">Tidak ada data jenis sampah pada periode ini.</div>
        @endif
    </div>

    {{-- III. Rekap per Petugas --}}
    <div class="tabel-detail">
        <h3>III. REKAPITULASI PER PETUGAS</h3>
        @if($rekapPertugas->count() > 0)
            <table class="tabel-formal">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th>Nama Petugas</th>
                        <th class="col-angka">Total Laporan</th>
                        <th class="col-angka">Tervalidasi</th>
                        <th class="col-angka">Belum Valid</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rekapPertugas as $data)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="nama">{{ $data['nama'] }}</td>
                        <td class="text-center">{{ $data['jumlah'] }}</td>
                        <td class="text-center">{{ $data['valid'] }}</td>
                        <td class="text-center">{{ $data['jumlah'] - $data['valid'] }}</td>
                    </tr>
                    @endforeach
                    <tr style="font-weight: bold;">
                        <td colspan="2" class="text-center">TOTAL</td>
                        <td class="text-center">{{ $totalSetoran }}</td>
                        <td class="text-center">{{ $totalValid }}</td>
                        <td class="text-center">{{ $totalSetoran - $totalValid }}</td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="empty-state">Tidak ada data petugas pada periode ini.</div>
        @endif
    </div>

    {{-- IV. Detail Setoran --}}
    <div class="tabel-detail">
        <h3>IV. RINCIAN SETORAN SAMPAH</h3>
        @if($semuaSetoran->count() > 0)
            <table class="tabel-formal tabel-detail">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th class="col-tgl">Tanggal</th>
                        <th>Nama Petugas</th>
                        <th>Jenis Sampah</th>
                        <th>Lokasi Setor</th>
                        <th>Keterangan</th>
                        <th class="col-stat">Status Validasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($semuaSetoran as $setoran)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">
                            {{ $setoran->tanggal->format('d/m/Y') }}
                        </td>
                        <td class="nama">{{ $setoran->pengguna->name ?? '-' }}</td>
                        <td>{{ $setoran->jenisSampahTeks() }}</td>
                        <td>{{ $setoran->lokasi_setor }}</td>
                        <td style="font-size: 9pt; font-style: italic;">
                            {{ $setoran->catatan ?? '-' }}
                            @if($setoran->status_validasi === 'ditolak' && $setoran->catatan_validasi)
                                <br><span style="color: #dc2626;">Alasan tolak: {{ $setoran->catatan_validasi }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($setoran->status_validasi === 'valid')
                                <span class="status-selesai">Tervalidasi</span>
                                @if($setoran->validator)
                                    <br><span style="font-size:8pt;">{{ $setoran->validator->name }}</span>
                                @endif
                            @elseif($setoran->status_validasi === 'ditolak')
                                <span class="status-ditolak">Ditolak</span>
                            @else
                                <span class="status-pending">Menunggu</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="empty-state">Tidak terdapat data setoran sampah pada periode ini.</div>
        @endif
    </div>

    {{-- Penutup --}}
    <div class="penutup">
        Demikian laporan bank sampah ini dibuat dengan sebenarnya berdasarkan data yang tercatat
        dalam sistem. Laporan ini dapat digunakan sebagai bahan evaluasi dan dokumentasi
        kegiatan pengelolaan sampah daur ulang di Puskesmas Kecamatan Cempaka Putih.
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
            <li>Pengelola Bank Sampah</li>
            <li>Arsip</li>
        </ol>
    </div>

@endsection
