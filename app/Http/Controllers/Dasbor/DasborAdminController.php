<?php

namespace App\Http\Controllers\Dasbor;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\BarangInventori;
use App\Models\PemakaianStok;
use App\Models\PermintaanBarang;
use App\Models\TugasMingguan;
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

        // 2. Status Tugas Kebersihan Hari Ini (Menggunakan modul Tugas Mingguan)
        $tugasHariIni = TugasMingguan::where('tanggal', $today)->get();
        $totalCeklisHariIni = $tugasHariIni->count();
        $ceklisSelesaiHariIni = $tugasHariIni->whereIn('status', ['selesai', 'disetujui'])->count();

        $areaBersihCount = $ceklisSelesaiHariIni;
        $areaBelumBersih = max(0, $totalCeklisHariIni - $ceklisSelesaiHariIni);
        $persenKebersihan = $totalCeklisHariIni > 0 ? round(($ceklisSelesaiHariIni / $totalCeklisHariIni) * 100) : 0;

        // 3. Stok Kritis & Permintaan Pending
        $stokKritisList = BarangInventori::whereColumn('stok_saat_ini', '<=', 'stok_minimum')
            ->orderBy('stok_saat_ini')
            ->take(6)
            ->get();
        $totalStokKritis = BarangInventori::whereColumn('stok_saat_ini', '<=', 'stok_minimum')->count();

        $permintaanPending = PermintaanBarang::where('status_request', 'pending')->count();

        // 4. Tren Tugas Kebersihan 7 Hari Terakhir (Line Chart)
        $labelHari = [];
        $dataCeklisSelesai = [];
        $dataCeklisTotal = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->toDateString();
            $namaHari = Carbon::parse($tgl)->locale('id')->isoFormat('ddd, D MMM');
            
            $tugasTgl = TugasMingguan::where('tanggal', $tgl)->get();
            $labelHari[] = $namaHari;
            $dataCeklisSelesai[] = $tugasTgl->whereIn('status', ['selesai', 'disetujui'])->count();
            $dataCeklisTotal[] = $tugasTgl->count();
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
            $topPemakaian = PemakaianStok::select('barang_id', DB::raw('SUM(jumlah) as total_jumlah'))
                ->groupBy('barang_id')
                ->orderByDesc('total_jumlah')
                ->with('barang')
                ->take(5)
                ->get()
                ->filter(fn($p) => $p->barang !== null && $p->total_jumlah > 0);

            $labelTopBarang = $topPemakaian->pluck('barang.nama_barang')->values()->toArray();
            $dataTopBarang  = $topPemakaian->pluck('total_jumlah')->values()->toArray();
        } else {
            $labelTopBarang = $topBarang->pluck('barang.nama_barang')->values()->toArray();
            $dataTopBarang  = $topBarang->pluck('total_jumlah')->values()->toArray();
        }

        // 6. Tugas Aktivitas Terbaru Hari Ini
        $tugasTerbaru = TugasMingguan::with(['user', 'verifikator'])
            ->where('tanggal', $today)
            ->latest()
            ->take(6)
            ->get();

        $ceklisTerbaru = $tugasTerbaru;

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
            'ceklisTerbaru',
            'tugasTerbaru'
        ));
    }
}
