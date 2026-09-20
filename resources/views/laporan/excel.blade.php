<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<table>
    {{-- ── Header Kop ── --}}
    <thead>
        <tr>
            <th colspan="6" style="font-size:14pt;font-weight:bold;text-align:center;">
                PEMERINTAH PROVINSI DKI JAKARTA
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size:13pt;font-weight:bold;text-align:center;">
                PUSKESMAS KECAMATAN CEMPAKA PUTIH
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size:10pt;text-align:center;color:#555555;">
                Jl. Cempaka Putih Tengah No. 1, Jakarta Pusat 10510
            </th>
        </tr>
        <tr><th colspan="6"></th></tr>
        <tr>
            <th colspan="6" style="font-size:13pt;font-weight:bold;text-align:center;text-decoration:underline;">
                LAPORAN KINERJA KEBERSIHAN
            </th>
        </tr>
        <tr>
            <th colspan="6" style="font-size:11pt;text-align:center;">Periode: {{ $judul }}</th>
        </tr>
        <tr><th colspan="6"></th></tr>
    </thead>

    <tbody>
        {{-- ── Info Laporan ── --}}
        <tr>
            <td style="font-weight:bold;" colspan="2">Rentang Tanggal</td>
            <td colspan="4">{{ \Carbon\Carbon::parse($dari)->translatedFormat('d F Y') }} s/d {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;" colspan="2">Tanggal Pembuatan</td>
            <td colspan="4">{{ now()->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
        <tr><td colspan="6"></td></tr>

        {{-- ── I. Ringkasan Eksekutif ── --}}
        <tr>
            <td colspan="6" style="font-weight:bold;font-size:11pt;background-color:#d1fae5;">
                I. RINGKASAN EKSEKUTIF
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;background-color:#f3f4f6;" colspan="3">Keterangan</td>
            <td style="font-weight:bold;background-color:#f3f4f6;" colspan="3">Nilai</td>
        </tr>
        <tr>
            <td colspan="3">Total Ceklis Area Dibuat</td>
            <td colspan="3">{{ $totalCeklis }}</td>
        </tr>
        <tr>
            <td colspan="3">Ceklis Area Diselesaikan</td>
            <td colspan="3">{{ $ceklisSelesai }} ({{ $ceklisPersen }}%)</td>
        </tr>
        <tr>
            <td colspan="3">Total Permintaan Barang</td>
            <td colspan="3">{{ $totalPermintaan }}</td>
        </tr>
        <tr>
            <td colspan="3">Permintaan Barang Disetujui</td>
            <td colspan="3">{{ $permintaanDisetujui }}</td>
        </tr>
        <tr>
            <td colspan="3">Permintaan Barang Ditolak</td>
            <td colspan="3">{{ $permintaanDitolak }}</td>
        </tr>
        <tr>
            <td colspan="3">Total Laporan Setoran Sampah</td>
            <td colspan="3">{{ $totalSetoran }} laporan</td>
        </tr>
        <tr>
            <td colspan="3">Rata-rata Nilai Kinerja Petugas</td>
            <td colspan="3">
                @if($rataKinerja)
                    {{ $rataKinerja > 5 ? $rataKinerja . ' / 100' : number_format($rataKinerja, 1) . ' / 5.0' }}
                @else
                    Belum ada data
                @endif
            </td>
        </tr>
        <tr>
            <td colspan="3">Petugas Terbaik (Top Performer)</td>
            <td colspan="3">{{ $topPerformer['nama'] ?? 'Belum ada data' }}</td>
        </tr>
        <tr><td colspan="6"></td></tr>

        {{-- ── II. Rincian Ceklis per Area ── --}}
        <tr>
            <td colspan="6" style="font-weight:bold;font-size:11pt;background-color:#d1fae5;">
                II. RINCIAN CEKLIS KEBERSIHAN PER AREA
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;background-color:#f3f4f6;">No.</td>
            <td style="font-weight:bold;background-color:#f3f4f6;" colspan="2">Nama Ruangan / Area</td>
            <td style="font-weight:bold;background-color:#f3f4f6;">Total Ceklis</td>
            <td style="font-weight:bold;background-color:#f3f4f6;">Diselesaikan</td>
            <td style="font-weight:bold;background-color:#f3f4f6;">Persentase</td>
        </tr>
        @forelse($ceklisPerArea as $area)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td colspan="2">{{ $area['nama'] }}</td>
            <td>{{ $area['total'] }}</td>
            <td>{{ $area['selesai'] }}</td>
            <td>{{ $area['total'] > 0 ? round(($area['selesai'] / $area['total']) * 100) : 0 }}%</td>
        </tr>
        @empty
        <tr>
            <td colspan="6">Tidak ada data ceklis pada periode ini.</td>
        </tr>
        @endforelse
        <tr>
            <td style="font-weight:bold;" colspan="3">TOTAL</td>
            <td style="font-weight:bold;">{{ $totalCeklis }}</td>
            <td style="font-weight:bold;">{{ $ceklisSelesai }}</td>
            <td style="font-weight:bold;">{{ $ceklisPersen }}%</td>
        </tr>
        <tr><td colspan="6"></td></tr>

        {{-- ── III. Ringkasan Permintaan Barang ── --}}
        <tr>
            <td colspan="6" style="font-weight:bold;font-size:11pt;background-color:#d1fae5;">
                III. RINGKASAN PERMINTAAN BARANG
            </td>
        </tr>
        <tr>
            <td style="font-weight:bold;background-color:#f3f4f6;" colspan="4">Keterangan</td>
            <td style="font-weight:bold;background-color:#f3f4f6;">Jumlah</td>
            <td style="font-weight:bold;background-color:#f3f4f6;">Persentase</td>
        </tr>
        <tr>
            <td colspan="4">Total Permintaan Masuk</td>
            <td>{{ $totalPermintaan }}</td>
            <td>100%</td>
        </tr>
        <tr>
            <td colspan="4">Disetujui Gudang</td>
            <td>{{ $permintaanDisetujui }}</td>
            <td>{{ $totalPermintaan > 0 ? round(($permintaanDisetujui / $totalPermintaan) * 100) : 0 }}%</td>
        </tr>
        <tr>
            <td colspan="4">Ditolak Gudang</td>
            <td>{{ $permintaanDitolak }}</td>
            <td>{{ $totalPermintaan > 0 ? round(($permintaanDitolak / $totalPermintaan) * 100) : 0 }}%</td>
        </tr>
        <tr>
            <td colspan="4">Masih Dalam Proses</td>
            <td>{{ $totalPermintaan - $permintaanDisetujui - $permintaanDitolak }}</td>
            <td>{{ $totalPermintaan > 0 ? round((($totalPermintaan - $permintaanDisetujui - $permintaanDitolak) / $totalPermintaan) * 100) : 0 }}%</td>
        </tr>
        <tr><td colspan="6"></td></tr>

        {{-- ── Tanda Tangan ── --}}
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="3" style="text-align:center;">Mengetahui,</td>
            <td colspan="3" style="text-align:center;">Jakarta, {{ now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:center;font-weight:bold;">Kepala Puskesmas Kecamatan Cempaka Putih</td>
            <td colspan="3" style="text-align:center;font-weight:bold;">Supervisor / Admin</td>
        </tr>
        <tr><td colspan="6"></td></tr>
        <tr><td colspan="6"></td></tr>
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="3" style="text-align:center;text-decoration:underline;">(...........................)</td>
            <td colspan="3" style="text-align:center;text-decoration:underline;">(...........................)</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:center;">NIP. -</td>
            <td colspan="3" style="text-align:center;">NIP. -</td>
        </tr>
    </tbody>
</table>
</body>
</html>
