<?php

namespace App\Http\Controllers\Dasbor;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\BarangInventori;
use App\Models\TugasMingguan;
use App\Models\OperanShift;
use App\Models\SetoranSampah;
use Illuminate\Http\Request;

class DasborSupervisorController extends Controller
{
    public function index(Request $request)
    {
        $hariIni = now()->toDateString();

        // --- KPI Tugas Mingguan ---
        $totalTugasHariIni = TugasMingguan::whereDate('tanggal', $hariIni)->count();

        $tugasMenungguVerifikasi = TugasMingguan::where('status', 'selesai')->count();

        $tugasDisetujuiHariIni = TugasMingguan::whereDate('tanggal', $hariIni)
            ->where('status', 'disetujui')
            ->count();

        $tugasProsesHariIni = TugasMingguan::whereDate('tanggal', $hariIni)
            ->where('status', 'proses')
            ->count();

        // --- KPI Setoran Sampah ---
        $setoranMenunggu = SetoranSampah::where('status_validasi', 'menunggu')->count();

        // --- Operan Shift Eskalasi (butuh perhatian) ---
        $operanEskalasi = OperanShift::where('status_penyelesaian', 'dieskalasi')
            ->whereDate('tanggal', $hariIni)
            ->count();

        // --- Feed Tugas Terbaru untuk ditampilkan ---
        $tugasTerbaru = TugasMingguan::with('user')
            ->whereDate('tanggal', $hariIni)
            ->latest()
            ->take(10)
            ->get();

        // --- Feed Setoran Sampah Terbaru (menunggu validasi) ---
        $setoranTerbaru = SetoranSampah::with('pengguna')
            ->latest('tanggal')
            ->take(5)
            ->get();

        // --- Grafik tren tugas 7 hari terakhir ---
        $tren = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->toDateString();
            $tren[] = [
                'tanggal' => now()->subDays($i)->translatedFormat('D'),
                'total'   => TugasMingguan::whereDate('tanggal', $tgl)->count(),
                'selesai' => TugasMingguan::whereDate('tanggal', $tgl)
                    ->whereIn('status', ['selesai', 'disetujui'])
                    ->count(),
            ];
        }

        // --- Monitoring Stok Menipis (<= 5) ---
        $stokMenipisCount = BarangInventori::where('stok_saat_ini', '<=', 5)->count();

        return view('dasbor.supervisor', compact(
            'totalTugasHariIni',
            'tugasMenungguVerifikasi',
            'tugasDisetujuiHariIni',
            'tugasProsesHariIni',
            'setoranMenunggu',
            'operanEskalasi',
            'stokMenipisCount',
            'tugasTerbaru',
            'setoranTerbaru',
            'tren',
        ));
    }
}
