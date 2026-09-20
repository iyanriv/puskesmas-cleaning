<?php

namespace App\Http\Controllers;

use App\Models\CeklisKebersihan;
use App\Models\PermintaanBarang;
use App\Models\SetoranSampah;
use App\Models\PenilaianPjLantai;
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
                'nama'    => $g->first()->area?->lantai ?? '-',
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
        $setoranList   = SetoranSampah::whereBetween('tanggal', [$dari, $sampai])->get();
        $totalKgSampah = 0; // kolom berat_kg sudah dihapus; gunakan jumlah setoran sebagai metrik
        $totalSetoran  = $setoranList->count();

        // ── Penilaian PJ Lantai ───────────────────────────
        $penilaianList = PenilaianPjLantai::whereBetween('tanggal_penilaian', [$dari, $sampai])
            ->with('petugasCs')->get();

        if ($penilaianList->count() > 0) {
            $rataKinerja = round($penilaianList->avg('rata_rata'), 1);
            $topPerformer = $penilaianList->groupBy(function ($p) {
                return $p->petugas_cs_id ?: $p->nama_petugas_cs;
            })->map(function ($g) {
                $first = $g->first();
                $nama  = $first->petugasCs?->name ?? $first->nama_petugas_cs ?? 'Petugas';
                return [
                    'nama' => $nama,
                    'rata' => round($g->avg('rata_rata'), 1),
                ];
            })->sortByDesc('rata')->first();
        } else {
            $rataKinerja  = null;
            $topPerformer = null;
        }

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
            'nama'    => $g->first()->area?->lantai ?? '-',
            'total'   => $g->count(),
            'selesai' => $g->where('status', 'selesai')->count(),
        ]);

        $totalPermintaan     = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->count();
        $permintaanDisetujui = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->where('status_request', 'disetujui')->count();
        $permintaanDitolak   = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->where('status_request', 'ditolak')->count();

        $setoranList   = SetoranSampah::whereBetween('tanggal', [$dari, $sampai])->get();
        $totalKgSampah = 0; // kolom berat_kg sudah dihapus
        $totalSetoran  = $setoranList->count();

        $penilaianList = PenilaianPjLantai::whereBetween('tanggal_penilaian', [$dari, $sampai])->with('petugasCs')->get();
        if ($penilaianList->count() > 0) {
            $rataKinerja = round($penilaianList->avg('rata_rata'), 1);
            $topPerformer = $penilaianList->groupBy(function ($p) {
                return $p->petugas_cs_id ?: $p->nama_petugas_cs;
            })->map(function ($g) {
                $first = $g->first();
                $nama  = $first->petugasCs?->name ?? $first->nama_petugas_cs ?? 'Petugas';
                return ['nama' => $nama, 'rata' => round($g->avg('rata_rata'), 1)];
            })->sortByDesc('rata')->first();
        } else {
            $rataKinerja  = null;
            $topPerformer = null;
        }

        // Tampilkan preview HTML (bukan DomPDF stream)
        // agar user bisa review dulu sebelum cetak via browser
        return view('laporan.pdf', compact(
            'judul', 'dari', 'sampai',
            'totalCeklis', 'ceklisSelesai', 'ceklisPersen', 'ceklisPerArea',
            'totalPermintaan', 'permintaanDisetujui', 'permintaanDitolak',
            'totalKgSampah', 'totalSetoran',
            'rataKinerja', 'topPerformer'
        ));
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
