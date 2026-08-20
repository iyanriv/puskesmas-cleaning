<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Kebersihan - {{ $judul }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #12a65a;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 { margin: 0; color: #064e2b; }
        .header p { margin: 5px 0 0 0; color: #555; }
        
        .section-title {
            background-color: #12a65a;
            color: white;
            padding: 5px 10px;
            font-weight: bold;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f6f9;
            padding: 8px;
            text-align: left;
        }
        td {
            padding: 8px;
        }
        
        .summary-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .summary-title { font-weight: bold; margin-bottom: 5px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <table style="border: none; margin-bottom: 0;">
        <tr style="border: none;">
            <td style="border: none; width: 80px; text-align: center; vertical-align: middle;">
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="width: 70px;">
            </td>
            <td style="border: none; text-align: center; vertical-align: middle;">
                <h2 style="margin: 0; color: #064e2b;">SIM Kebersihan Puskesmas</h2>
                <p style="margin: 5px 0 0 0; color: #555;"><strong>Laporan Terpadu Kinerja & Operasional</strong></p>
                <p style="margin: 5px 0 0 0; color: #555;">Periode: {{ $judul }}</p>
            </td>
            <td style="border: none; width: 80px;"></td>
        </tr>
    </table>
    <div style="border-bottom: 2px solid #12a65a; margin-top: 10px; margin-bottom: 20px;"></div>

    <!-- Ringkasan Eksekutif -->
    <div class="section-title">Ringkasan Eksekutif</div>
    <table>
        <tr>
            <th>Total Ceklis Area</th>
            <td class="text-right">{{ $totalCeklis }}</td>
            <th>Tingkat Penyelesaian</th>
            <td class="text-right">{{ $ceklisPersen }}%</td>
        </tr>
        <tr>
            <th>Permintaan Barang Disetujui</th>
            <td class="text-right">{{ $permintaanDisetujui }} dari {{ $totalPermintaan }}</td>
            <th>Total Laporan Sampah</th>
            <td class="text-right">{{ $totalSetoran }} laporan</td>
        </tr>
        <tr>
            <th>Rata-rata Kinerja Petugas</th>
            <td class="text-right">{{ $rataKinerja ?? '-' }} / 5.0</td>
            <th>Top Performer</th>
            <td class="text-right">{{ $topPerformer['nama'] ?? '-' }}</td>
        </tr>
    </table>

    <!-- Detail Ceklis Per Area -->
    <div class="section-title">Rincian Ceklis Kebersihan per Area</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Ruangan / Area</th>
                <th class="text-center">Total Ceklis Dibuat</th>
                <th class="text-center">Diselesaikan</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ceklisPerArea as $index => $area)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $area['nama'] }}</td>
                    <td class="text-center">{{ $area['total'] }}</td>
                    <td class="text-center">{{ $area['selesai'] }}</td>
                    <td class="text-center">
                        {{ $area['total'] > 0 ? round(($area['selesai']/$area['total'])*100) : 0 }}%
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data ceklis pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <br><br>
    <table style="border: none; margin-top: 50px;">
        <tr style="border: none;">
            <td style="border: none; width: 60%;"></td>
            <td style="border: none; width: 40%; text-align: center;">
                <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
                <br><br><br><br>
                <p><strong>( ______________________ )</strong><br>Admin / Supervisor</p>
            </td>
        </tr>
    </table>

</body>
</html>
