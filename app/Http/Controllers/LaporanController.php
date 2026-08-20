<?php

namespace App\Http\Controllers;

use App\Models\CeklisKebersihan;
use App\Models\PermintaanBarang;
use App\Models\SetoranSampah;
use App\Models\PenilaianKinerja;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Dashboard laporan utama.
     */
    public function index(Request $request)
    {
        $filter  = $request->get('filter', 'minggu');
        $bulan   = $request->get('bulan', now()->format('Y-m'));

        // Tentukan rentang
        [$dari, $sampai, $judul] = $this->rentangWaktu($filter, $bulan);

        // ── Ceklis Kebersihan ──────────────────────────────
        $totalCeklis   = CeklisKebersihan::whereBetween('tanggal', [$dari, $sampai])->count();
        $ceklisSelesai = CeklisKebersihan::whereBetween('tanggal', [$dari, $sampai])
            ->where('status', 'selesai')->count();
        $ceklisPersen  = $totalCeklis > 0 ? round(($ceklisSelesai / $totalCeklis) * 100) : 0;

        // Per area
        $ceklisPerArea = CeklisKebersihan::with('area')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->get()
            ->groupBy('area_id')
            ->map(fn($g) => [
                'nama'    => $g->first()->area?->nama_ruangan ?? '-',
                'total'   => $g->count(),
                'selesai' => $g->where('status', 'selesai')->count(),
            ]);

        // ── Permintaan Barang ─────────────────────────────
        $totalPermintaan     = PermintaanBarang::whereBetween('waktu_request', [
            $dari . ' 00:00:00', $sampai . ' 23:59:59'
        ])->count();
        $permintaanDisetujui = PermintaanBarang::whereBetween('waktu_request', [
            $dari . ' 00:00:00', $sampai . ' 23:59:59'
        ])->where('status_request', 'disetujui')->count();
        $permintaanDitolak   = PermintaanBarang::whereBetween('waktu_request', [
            $dari . ' 00:00:00', $sampai . ' 23:59:59'
        ])->where('status_request', 'ditolak')->count();

        // ── Bank Sampah ───────────────────────────────────
        $setoranList  = SetoranSampah::whereBetween('tanggal', [$dari, $sampai])->get();
        $totalKgSampah = $setoranList->sum('berat_kg');
        $totalSetoran  = $setoranList->count();

        // ── Penilaian Kinerja ─────────────────────────────
        $penilaianList   = PenilaianKinerja::whereBetween('tanggal', [$dari, $sampai])
            ->with('dinilai')->get();
        $rataKinerja     = $penilaianList->count() > 0
            ? round($penilaianList->avg(fn($p) => $p->rataRata()), 1)
            : null;

        // Top performer bulan ini
        $topPerformer = $penilaianList->groupBy('dinilai_id')->map(fn($g) => [
            'nama'     => $g->first()->dinilai->name,
            'rata'     => round($g->avg(fn($p) => $p->rataRata()), 1),
        ])->sortByDesc('rata')->first();

        // ── Grafik ceklis 7 hari terakhir ─────────────────
        $grafikCeklis = collect(range(6, 0))->map(function($i) {
            $tgl = now()->subDays($i)->toDateString();
            return [
                'tgl'     => now()->subDays($i)->format('d/m'),
                'total'   => CeklisKebersihan::where('tanggal', $tgl)->count(),
                'selesai' => CeklisKebersihan::where('tanggal', $tgl)->where('status', 'selesai')->count(),
            ];
        });

        $daftarBulan = collect(range(0, 11))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        return view('laporan.index', compact(
            'filter', 'bulan', 'judul',
            'totalCeklis', 'ceklisSelesai', 'ceklisPersen', 'ceklisPerArea',
            'totalPermintaan', 'permintaanDisetujui', 'permintaanDitolak',
            'totalKgSampah', 'totalSetoran',
            'rataKinerja', 'topPerformer',
            'grafikCeklis', 'daftarBulan'
        ));
    }
    public function cetakPdf(Request $request)
    {
        $filter  = $request->get('filter', 'minggu');
        $bulan   = $request->get('bulan', now()->format('Y-m'));

        [$dari, $sampai, $judul] = $this->rentangWaktu($filter, $bulan);

        // -- Ambil data --
        $totalCeklis   = CeklisKebersihan::whereBetween('tanggal', [$dari, $sampai])->count();
        $ceklisSelesai = CeklisKebersihan::whereBetween('tanggal', [$dari, $sampai])->where('status', 'selesai')->count();
        $ceklisPersen  = $totalCeklis > 0 ? round(($ceklisSelesai / $totalCeklis) * 100) : 0;
        
        $ceklisPerArea = CeklisKebersihan::with('area')->whereBetween('tanggal', [$dari, $sampai])->get()->groupBy('area_id')->map(fn($g) => [
            'nama'    => $g->first()->area?->nama_ruangan ?? '-',
            'total'   => $g->count(),
            'selesai' => $g->where('status', 'selesai')->count(),
        ]);

        $totalPermintaan     = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->count();
        $permintaanDisetujui = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->where('status_request', 'disetujui')->count();
        $permintaanDitolak   = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->where('status_request', 'ditolak')->count();

        $setoranList  = SetoranSampah::whereBetween('tanggal', [$dari, $sampai])->get();
        $totalKgSampah = $setoranList->sum('berat_kg');
        $totalSetoran  = $setoranList->count();

        $penilaianList   = PenilaianKinerja::whereBetween('tanggal', [$dari, $sampai])->with('dinilai')->get();
        $rataKinerja     = $penilaianList->count() > 0 ? round($penilaianList->avg(fn($p) => $p->rataRata()), 1) : null;
        $topPerformer = $penilaianList->groupBy('dinilai_id')->map(fn($g) => [
            'nama'     => $g->first()->dinilai->name,
            'rata'     => round($g->avg(fn($p) => $p->rataRata()), 1),
        ])->sortByDesc('rata')->first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf', compact(
            'judul', 'dari', 'sampai',
            'totalCeklis', 'ceklisSelesai', 'ceklisPersen', 'ceklisPerArea',
            'totalPermintaan', 'permintaanDisetujui', 'permintaanDitolak',
            'totalKgSampah', 'totalSetoran',
            'rataKinerja', 'topPerformer'
        ));

        return $pdf->stream('Laporan_Kebersihan_'.$judul.'.pdf');
    }

    public function cetakExcel(Request $request)
    {
        $filter  = $request->get('filter', 'minggu');
        $bulan   = $request->get('bulan', now()->format('Y-m'));
        [$dari, $sampai, $judul] = $this->rentangWaktu($filter, $bulan);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanExport($dari, $sampai, $judul), 
            'Laporan_Kebersihan_'.$judul.'.xlsx'
        );
    }

    // ────────────────────────────────────────────────────────
    private function rentangWaktu(string $filter, string $bulan): array
    {
        switch ($filter) {
            case 'hari':
                return [
                    now()->toDateString(),
                    now()->toDateString(),
                    'Hari Ini (' . now()->translatedFormat('d F Y') . ')',
                ];
            case 'bulan':
                $awal  = Carbon::parse($bulan . '-01')->startOfMonth()->toDateString();
                $akhir = Carbon::parse($bulan . '-01')->endOfMonth()->toDateString();
                return [$awal, $akhir, 'Bulan ' . Carbon::parse($bulan . '-01')->translatedFormat('F Y')];
            default: // minggu
                return [
                    now()->startOfWeek()->toDateString(),
                    now()->endOfWeek()->toDateString(),
                    'Minggu Ini (' . now()->startOfWeek()->format('d') . '–' . now()->endOfWeek()->translatedFormat('d F Y') . ')',
                ];
        }
    }
}
