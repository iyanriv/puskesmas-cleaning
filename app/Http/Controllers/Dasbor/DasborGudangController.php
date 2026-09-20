<?php

namespace App\Http\Controllers\Dasbor;

use App\Http\Controllers\Controller;
use App\Models\BarangInventori;
use App\Models\LaporanStok;
use App\Models\PermintaanBarang;
use App\Models\ProdukKebersihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DasborGudangController extends Controller
{
    public function index(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        // Jika periode bulan/tahun ini belum ada data laporan, cari periode terakhir yang memiliki data
        $cekAdaData = LaporanStok::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->exists();

        if (! $cekAdaData) {
            $periodeTerakhir = LaporanStok::orderByDesc('periode_tahun')
                ->orderByDesc('periode_bulan')
                ->first();
            if ($periodeTerakhir) {
                $bulan = $periodeTerakhir->periode_bulan;
                $tahun = $periodeTerakhir->periode_tahun;
            }
        }

        // 1. Ringkasan Metrik Master Barang
        $totalBarang = BarangInventori::count();
        $stokMenipis = BarangInventori::whereColumn('stok_saat_ini', '<=', 'stok_minimum')
            ->orderBy('stok_saat_ini')
            ->get();
        $stokHabisCount = BarangInventori::where('stok_saat_ini', '<=', 0)->count();
        $totalStokTersedia = BarangInventori::sum('stok_saat_ini');

        // 2. Permintaan Barang
        $permintaanPending = PermintaanBarang::with(['pengguna', 'barang'])
            ->where('status_request', 'pending')
            ->latest('waktu_request')
            ->get();

        $permintaanDisetujuiBulanIni = PermintaanBarang::where('status_request', 'disetujui')
            ->whereMonth('waktu_approve', $bulan)
            ->whereYear('waktu_approve', $tahun)
            ->count();

        // 3. Data Pemakaian & Stok Masuk Periode Aktif
        $totalStokMasuk = LaporanStok::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->sum('stok_masuk');

        $totalPemakaianBulanIni = DB::table('pemakaian_stok')
            ->join('laporan_stok', 'pemakaian_stok.laporan_stok_id', '=', 'laporan_stok.id')
            ->where('laporan_stok.periode_bulan', $bulan)
            ->where('laporan_stok.periode_tahun', $tahun)
            ->sum('pemakaian_stok.jumlah');

        // 4. Data Grafik 1: Proporsi Pemakaian per Unit / Lokasi
        $pemakaianPerUnit = DB::table('pemakaian_stok')
            ->join('laporan_stok', 'pemakaian_stok.laporan_stok_id', '=', 'laporan_stok.id')
            ->where('laporan_stok.periode_bulan', $bulan)
            ->where('laporan_stok.periode_tahun', $tahun)
            ->select('pemakaian_stok.unit', DB::raw('SUM(pemakaian_stok.jumlah) as total'))
            ->groupBy('pemakaian_stok.unit')
            ->having('total', '>', 0)
            ->orderByDesc('total')
            ->get();

        // 5. Data Grafik 2: Top 5 Barang Paling Sering / Banyak Dipakai
        $topBarang = DB::table('pemakaian_stok')
            ->join('laporan_stok', 'pemakaian_stok.laporan_stok_id', '=', 'laporan_stok.id')
            ->join('produk_kebersihan', 'laporan_stok.produk_id', '=', 'produk_kebersihan.id')
            ->where('laporan_stok.periode_bulan', $bulan)
            ->where('laporan_stok.periode_tahun', $tahun)
            ->select(
                'produk_kebersihan.nama_barang',
                'produk_kebersihan.satuan',
                DB::raw('SUM(pemakaian_stok.jumlah) as total_pakai')
            )
            ->groupBy('produk_kebersihan.id', 'produk_kebersihan.nama_barang', 'produk_kebersihan.satuan')
            ->having('total_pakai', '>', 0)
            ->orderByDesc('total_pakai')
            ->limit(5)
            ->get();

        // 6. Data Grafik 3: Tren Permintaan 7 Hari Terakhir
        $trenPermintaan = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->format('Y-m-d');
            $label = now()->subDays($i)->locale('id')->isoFormat('D MMM');
            $jmlTotal = PermintaanBarang::whereDate('waktu_request', $tgl)->count();
            $jmlDisetujui = PermintaanBarang::whereDate('waktu_request', $tgl)->where('status_request', 'disetujui')->count();
            $trenPermintaan[] = [
                'tanggal' => $label,
                'total' => $jmlTotal,
                'disetujui' => $jmlDisetujui,
            ];
        }

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        return view('dasbor.gudang', compact(
            'totalBarang',
            'stokMenipis',
            'stokHabisCount',
            'totalStokTersedia',
            'permintaanPending',
            'permintaanDisetujuiBulanIni',
            'totalStokMasuk',
            'totalPemakaianBulanIni',
            'pemakaianPerUnit',
            'topBarang',
            'trenPermintaan',
            'bulan',
            'tahun',
            'daftarBulan'
        ));
    }
}
