@extends('layouts.laporan-formal')

@section('title', 'Laporan Stok Barang')

@section('styles')
<style>
    .tabel-stok .col-no     { width: 30px; }
    .tabel-stok .col-kode   { width: 80px; }
    .tabel-stok .col-satuan { width: 60px; }
    .tabel-stok .col-angka  { width: 70px; }
</style>
@endsection

@section('content')
    @php
        // Hitung total dari koleksi
        $totalMasuk   = $daftarBarang->sum('stok_masuk');
        $totalPakai   = $daftarBarang->sum(fn($l) => $l->totalPemakaian());
        $totalSisa    = $daftarBarang->sum(fn($l) => $l->sisaStok());
        $namaBulanStr = $daftarBarang->first()
            ? \Carbon\Carbon::create($daftarBarang->first()->periode_tahun, $daftarBarang->first()->periode_bulan, 1)->translatedFormat('F Y')
            : $namaBulan . ' ' . $tahun;
    @endphp

    {{-- Judul Laporan --}}
    <div class="judul-laporan">
        <h1>Laporan Stok Barang Kebersihan</h1>
        <div class="nomor-surat">
            Nomor: {{ str_pad($daftarBarang->count(), 3, '0', STR_PAD_LEFT) }}/LAP-SB/{{ now()->format('m/Y') }}
        </div>
    </div>

    {{-- Pembuka --}}
    <div class="pembuka">
        Berdasarkan pencatatan dan monitoring stok barang kebersihan di lingkungan Puskesmas Kecamatan
        Cempaka Putih, dengan ini kami laporkan rekapitulasi stok barang untuk periode
        <strong>{{ $namaBulanStr }}</strong> sebagai berikut:
    </div>

    {{-- Info Periode --}}
    <div class="info-periode">
        <table>
            <tr>
                <td class="label">Periode Laporan</td>
                <td class="separator">:</td>
                <td>{{ $namaBulanStr }}</td>
            </tr>
            @if($isFilteredUnit)
            <tr>
                <td class="label">Filter Unit / Lokasi</td>
                <td class="separator">:</td>
                <td>{{ $filterUnit }}</td>
            </tr>
            @endif
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
                <td>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->peran->nama_peran ?? '-') }})</td>
            </tr>
        </table>
    </div>

    {{-- Ringkasan Statistik --}}
    <div class="ringkasan">
        <h3>I. RINGKASAN REKAPITULASI STOK</h3>
        <table>
            <tr>
                <td class="label">Total Jenis Barang Tercatat</td>
                <td class="value">{{ $daftarBarang->count() }} jenis</td>
            </tr>
            <tr>
                <td class="label">Total Stok Masuk / Pengadaan</td>
                <td class="value">{{ number_format($totalMasuk, 0, ',', '.') }} unit</td>
            </tr>
            <tr>
                <td class="label">Total Pemakaian Seluruh Unit</td>
                <td class="value">{{ number_format($totalPakai, 0, ',', '.') }} unit</td>
            </tr>
            <tr>
                <td class="label">Total Sisa Stok Akhir Periode</td>
                <td class="value">{{ number_format($totalSisa, 0, ',', '.') }} unit</td>
            </tr>
        </table>
    </div>

    {{-- Tabel Detail --}}
    <div class="tabel-detail">
        <h3>II. RINCIAN STOK BARANG KEBERSIHAN</h3>

        @if($daftarBarang->count() > 0)
            <table class="tabel-formal tabel-stok">
                <thead>
                    <tr>
                        <th class="col-no">No.</th>
                        <th class="col-kode">Kode Barang</th>
                        <th>Nama Barang</th>
                        <th class="col-satuan">Satuan</th>
                        <th class="col-angka">Stok Masuk</th>
                        <th class="col-angka">Total Pemakaian</th>
                        <th class="col-angka">Sisa Stok</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($daftarBarang as $index => $item)
                    @php
                        $pemakaian = $item->totalPemakaian();
                        $sisa      = $item->sisaStok();
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $item->produk->kode_produk ?? '-' }}</td>
                        <td class="nama">{{ $item->produk->nama_barang ?? '-' }}</td>
                        <td class="text-center">{{ $item->produk->satuan ?? '-' }}</td>
                        <td class="text-right">{{ number_format($item->stok_masuk, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($pemakaian, 0, ',', '.') }}</td>
                        <td class="text-right">
                            <strong>{{ number_format($sisa, 0, ',', '.') }}</strong>
                            @if($sisa <= 5 && $sisa >= 0)
                                <br><span style="font-size: 9pt; font-style: italic;">(Stok Menipis)</span>
                            @elseif($sisa < 0)
                                <br><span style="font-size: 9pt; font-style: italic; color: red;">(Defisit)</span>
                            @endif
                        </td>
                        <td>-</td>
                    </tr>
                    @endforeach

                    {{-- Total Row --}}
                    <tr style="font-weight: bold;">
                        <td colspan="4" class="text-center">TOTAL</td>
                        <td class="text-right">{{ number_format($totalMasuk, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalPakai, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($totalSisa, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @else
            <div class="empty-state">
                Tidak terdapat data stok barang pada periode pelaporan ini.
            </div>
        @endif
    </div>

    {{-- Catatan Stok Menipis --}}
    @php $stokMenipis = $daftarBarang->filter(fn($l) => $l->sisaStok() <= 5); @endphp
    @if($stokMenipis->count() > 0)
        <div style="margin: 20px 0; padding: 10px; border: 1px solid #000;">
            <div style="font-weight: bold; margin-bottom: 5px;">CATATAN PENTING:</div>
            <div style="font-size: 10pt;">
                Terdapat <strong>{{ $stokMenipis->count() }} jenis barang</strong> dengan sisa stok
                menipis (≤5 unit). Diperlukan pengadaan segera untuk menjaga ketersediaan
                barang kebersihan pada periode berikutnya.
            </div>
        </div>
    @endif

    {{-- Penutup --}}
    <div class="penutup">
        Demikian laporan stok barang kebersihan ini dibuat dengan sebenarnya berdasarkan data
        yang tercatat dalam sistem. Laporan ini dapat digunakan sebagai bahan evaluasi dan
        perencanaan pengadaan barang untuk periode berikutnya.
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
                    {{ ucfirst(auth()->user()->peran->nama_peran ?? 'Petugas') }}<br>
                    Puskesmas Kecamatan Cempaka Putih
                </div>
                <div class="ttd-nama">{{ auth()->user()->name }}</div>
                <div class="ttd-nip">NIP. -</div>
            </div>
        </div>
    </div>

    {{-- Tembusan --}}
    <div class="tembusan">
        <div class="tembusan-title">Tembusan:</div>
        <ol>
            <li>Kepala Puskesmas Kecamatan Cempaka Putih</li>
            <li>Koordinator Gudang</li>
            <li>Koordinator Kebersihan</li>
            <li>Arsip</li>
        </ol>
    </div>
@endsection
