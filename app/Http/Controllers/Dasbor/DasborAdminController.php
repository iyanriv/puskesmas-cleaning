<?php

namespace App\Http\Controllers\Dasbor;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\BarangInventori;
use App\Models\CeklisKebersihan;
use App\Models\PermintaanBarang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DasborAdminController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        // 1. Data Ringkas Utama
        $totalPengguna = User::count();
        $totalCS       = User::whereHas('peran', fn($q) => $q->where('nama_peran', 'cs'))->count();
        $totalArea     = Area::count();
        $totalBarang   = BarangInventori::count();
        $totalStokFisik = (int) BarangInventori::sum('stok_saat_ini');

        // 2. Status Ceklis Hari Ini
        $ceklisHariIni = CeklisKebersihan::where('tanggal', $today)->get();
        $totalCeklisHariIni = $ceklisHariIni->count();
        $ceklisSelesaiHariIni = $ceklisHariIni->where('status', 'selesai')->count();

        // Area yang sudah selesai dibersihkan hari ini
        $areaSelesaiIds = $ceklisHariIni->where('status', 'selesai')->pluck('area_id')->unique()->toArray();
        $areaBersihCount = count($areaSelesaiIds);
        $areaBelumBersih = max(0, $totalArea - $areaBersihCount);
        $persenKebersihan = $totalArea > 0 ? round(($areaBersihCount / $totalArea) * 100) : 0;

        // 3. Stok Kritis & Permintaan Pending
        $stokKritisList = BarangInventori::whereColumn('stok_saat_ini', '<=', 'stok_minimum')
            ->orderBy('stok_saat_ini')
            ->take(6)
            ->get();
        $totalStokKritis = BarangInventori::whereColumn('stok_saat_ini', '<=', 'stok_minimum')->count();

        $permintaanPending = PermintaanBarang::where('status_request', 'pending')->count();

        // 4. Tren Ceklis 7 Hari Terakhir (Line Chart)
        $labelHari = [];
        $dataCeklisSelesai = [];
        $dataCeklisTotal = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->toDateString();
            $namaHari = Carbon::parse($tgl)->locale('id')->isoFormat('ddd, D MMM');
            
            $ceklisTgl = CeklisKebersihan::where('tanggal', $tgl)->get();
            $labelHari[] = $namaHari;
            $dataCeklisSelesai[] = $ceklisTgl->where('status', 'selesai')->count();
            $dataCeklisTotal[] = $ceklisTgl->count();
        }

        // 5. Top 5 Barang yang Paling Sering Diminta / Dipakai (Bar Chart)
        $topBarang = PermintaanBarang::select('barang_id', DB::raw('SUM(jumlah) as total_jumlah'))
            ->where('status_request', 'disetujui')
            ->groupBy('barang_id')
            ->orderByDesc('total_jumlah')
            ->with('barang')
            ->take(5)
            ->get()
            ->filter(fn($p) => $p->barang !== null);

        if ($topBarang->isEmpty()) {
            // Fallback ke PemakaianStok
            $topPemakaian = \App\Models\PemakaianStok::select('produk_id', DB::raw('SUM(jumlah) as total_jumlah'))
                ->groupBy('produk_id')
                ->orderByDesc('total_jumlah')
                ->with('produk')
                ->take(5)
                ->get()
                ->filter(fn($p) => $p->produk !== null && $p->total_jumlah > 0);

            $labelTopBarang = $topPemakaian->pluck('produk.nama_barang')->values()->toArray();
            $dataTopBarang  = $topPemakaian->pluck('total_jumlah')->values()->toArray();
        } else {
            $labelTopBarang = $topBarang->pluck('barang.nama_barang')->values()->toArray();
            $dataTopBarang  = $topBarang->pluck('total_jumlah')->values()->toArray();
        }

        // 6. Ceklis Aktivitas Terbaru Hari Ini
        $ceklisTerbaru = CeklisKebersihan::with(['pengguna', 'area'])
            ->where('tanggal', $today)
            ->latest('waktu_mulai')
            ->take(6)
            ->get();

        return view('dasbor.admin', compact(
            'totalPengguna',
            'totalCS',
            'totalArea',
            'totalBarang',
            'totalStokFisik',
            'totalCeklisHariIni',
            'ceklisSelesaiHariIni',
            'areaBersihCount',
            'areaBelumBersih',
            'persenKebersihan',
            'totalStokKritis',
            'stokKritisList',
            'permintaanPending',
            'labelHari',
            'dataCeklisSelesai',
            'dataCeklisTotal',
            'labelTopBarang',
            'dataTopBarang',
            'ceklisTerbaru'
        ));
    }
}
