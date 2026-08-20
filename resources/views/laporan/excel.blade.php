<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<table>
    <thead>
        <tr>
            <th colspan="5" style="font-size: 14pt; font-weight: bold; text-align: center;">SIM Kebersihan Puskesmas</th>
        </tr>
        <tr>
            <th colspan="5" style="font-size: 12pt; font-weight: bold; text-align: center;">Laporan Terpadu Kinerja &amp; Operasional</th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center;">Periode: {{ $judul }}</th>
        </tr>
        <tr>
            <th colspan="5"></th>
        </tr>
    </thead>
    <tbody>
        <!-- Ringkasan -->
        <tr>
            <td colspan="5" style="font-weight: bold; background-color: #12a65a; color: #ffffff;">Ringkasan Eksekutif</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Total Ceklis Area</td>
            <td colspan="3">{{ $totalCeklis }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Tingkat Penyelesaian Ceklis</td>
            <td colspan="3">{{ $ceklisPersen }}%</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Permintaan Barang Disetujui</td>
            <td colspan="3">{{ $permintaanDisetujui }} dari {{ $totalPermintaan }}</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Total Laporan Sampah</td>
            <td colspan="3">{{ $totalSetoran }} laporan</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Rata-rata Kinerja Petugas</td>
            <td colspan="3">{{ $rataKinerja ?? '-' }} / 5.0</td>
        </tr>
        <tr>
            <td colspan="2" style="font-weight: bold;">Top Performer</td>
            <td colspan="3">{{ $topPerformer['nama'] ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="5"></td>
        </tr>

        <!-- Detail -->
        <tr>
            <td colspan="5" style="font-weight: bold; background-color: #12a65a; color: #ffffff;">Rincian Ceklis Kebersihan per Area</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #e5e7eb;">No</td>
            <td style="font-weight: bold; background-color: #e5e7eb;">Nama Ruangan / Area</td>
            <td style="font-weight: bold; background-color: #e5e7eb;">Total Ceklis Dibuat</td>
            <td style="font-weight: bold; background-color: #e5e7eb;">Diselesaikan</td>
            <td style="font-weight: bold; background-color: #e5e7eb;">Status Penyelesaian</td>
        </tr>
        @forelse($ceklisPerArea as $index => $area)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $area['nama'] }}</td>
                <td>{{ $area['total'] }}</td>
                <td>{{ $area['selesai'] }}</td>
                <td>{{ $area['total'] > 0 ? round(($area['selesai']/$area['total'])*100) : 0 }}%</td>
            </tr>
        @empty
            <tr>
                <td colspan="5">Tidak ada data ceklis pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>
</body>
</html>
