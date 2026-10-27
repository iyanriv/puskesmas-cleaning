<?php

namespace App\Http\Controllers;

use App\Models\TugasMingguan;
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

        // ── Tugas Kebersihan CS (Tugas Mingguan) ──────────────────────
        $totalCeklis   = TugasMingguan::whereBetween('tanggal', [$dari, $sampai])->count();
        $ceklisSelesai = TugasMingguan::whereBetween('tanggal', [$dari, $sampai])
            ->whereIn('status', ['selesai', 'disetujui'])->count();
        $ceklisPersen  = $totalCeklis > 0 ? round(($ceklisSelesai / $totalCeklis) * 100) : 0;

        // Rincian per Petugas CS
        $ceklisPerArea = TugasMingguan::with('user')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->get()
            ->groupBy('user_id')
            ->map(fn($g) => [
                'nama'    => $g->first()->user?->name ?? 'Petugas CS',
                'total'   => $g->count(),
                'selesai' => $g->whereIn('status', ['selesai', 'disetujui'])->count(),
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

        // ── Grafik tugas 8 minggu terakhir ───────────────
        $grafikCeklis = collect(range(7, 0))->map(function ($i) {
            $mulai = now()->startOfWeek()->subWeeks($i)->toDateString();
            $akhir = now()->startOfWeek()->subWeeks($i)->endOfWeek()->toDateString();
            $label = now()->startOfWeek()->subWeeks($i)->format('d/m')
                   . '-'
                   . now()->startOfWeek()->subWeeks($i)->endOfWeek()->format('d/m');
            return [
                'tgl'     => $label,
                'total'   => TugasMingguan::whereBetween('tanggal', [$mulai, $akhir])->count(),
                'selesai' => TugasMingguan::whereBetween('tanggal', [$mulai, $akhir])
                                ->whereIn('status', ['selesai', 'disetujui'])->count(),
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

    /**
     * Preview laporan sebagai HTML di browser — tombol Cetak & Download tersedia.
     */
    public function previewPdf(Request $request)
    {
        $filter  = $request->get('filter', 'minggu');
        $bulan   = $request->get('bulan', now()->format('Y-m'));

        [$dari, $sampai, $judul] = $this->rentangWaktu($filter, $bulan);

        $data = $this->ambilDataLaporan($dari, $sampai);

        // URL download PDF (via DomPDF) dengan parameter yang sama
        $urlDownload = route('laporan.cetak-pdf', $request->all());

        return view('laporan.pdf', array_merge($data, compact(
            'judul', 'dari', 'sampai', 'urlDownload'
        )));
    }

    /**
     * Unduh laporan sebagai file PDF (DomPDF download).
     */
    public function cetakPdf(Request $request)
    {
        $filter  = $request->get('filter', 'minggu');
        $bulan   = $request->get('bulan', now()->format('Y-m'));

        [$dari, $sampai, $judul] = $this->rentangWaktu($filter, $bulan);

        $data = $this->ambilDataLaporan($dari, $sampai);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.pdf-dompdf', array_merge($data, compact(
            'judul', 'dari', 'sampai'
        )))->setPaper('a4', 'portrait');

        return $pdf->download('Laporan-Kebersihan-' . $dari . '-sd-' . $sampai . '.pdf');
    }

    /**
     * Helper: kumpulkan semua data laporan untuk view.
     */
    private function ambilDataLaporan(string $dari, string $sampai): array
    {
        $totalCeklis   = TugasMingguan::whereBetween('tanggal', [$dari, $sampai])->count();
        $ceklisSelesai = TugasMingguan::whereBetween('tanggal', [$dari, $sampai])->whereIn('status', ['selesai', 'disetujui'])->count();
        $ceklisPersen  = $totalCeklis > 0 ? round(($ceklisSelesai / $totalCeklis) * 100) : 0;

        $ceklisPerArea = TugasMingguan::with('user')
            ->whereBetween('tanggal', [$dari, $sampai])->get()
            ->groupBy('user_id')
            ->map(fn($g) => [
                'nama'    => $g->first()->user?->name ?? 'Petugas CS',
                'total'   => $g->count(),
                'selesai' => $g->whereIn('status', ['selesai', 'disetujui'])->count(),
            ]);

        $totalPermintaan     = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->count();
        $permintaanDisetujui = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->where('status_request', 'disetujui')->count();
        $permintaanDitolak   = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->where('status_request', 'ditolak')->count();

        $setoranList   = SetoranSampah::whereBetween('tanggal', [$dari, $sampai])->get();
        $totalKgSampah = 0;
        $totalSetoran  = $setoranList->count();

        $penilaianList = PenilaianPjLantai::whereBetween('tanggal_penilaian', [$dari, $sampai])->with('petugasCs')->get();
        if ($penilaianList->count() > 0) {
            $rataKinerja  = round($penilaianList->avg('rata_rata'), 1);
            $topPerformer = $penilaianList->groupBy(fn($p) => $p->petugas_cs_id ?: $p->nama_petugas_cs)
                ->map(function ($g) {
                    $first = $g->first();
                    return [
                        'nama' => $first->petugasCs?->name ?? $first->nama_petugas_cs ?? 'Petugas',
                        'rata' => round($g->avg('rata_rata'), 1),
                    ];
                })->sortByDesc('rata')->first();
        } else {
            $rataKinerja  = null;
            $topPerformer = null;
        }

        return compact(
            'totalCeklis', 'ceklisSelesai', 'ceklisPersen', 'ceklisPerArea',
            'totalPermintaan', 'permintaanDisetujui', 'permintaanDitolak',
            'totalKgSampah', 'totalSetoran',
            'rataKinerja', 'topPerformer'
        );
    }

    public function exportExcel(Request $request)
    {
        $filter  = $request->get('filter', 'minggu');
        $bulan   = $request->get('bulan', now()->format('Y-m'));

        [$dari, $sampai, $judul] = $this->rentangWaktu($filter, $bulan);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\LaporanExport($dari, $sampai, $judul),
            'Laporan-Kebersihan-' . $dari . '-sd-' . $sampai . '.xlsx'
        );
    }

    public function cetakExcel(Request $request)
    {
        return $this->exportExcel($request);
    }

    /**
     * Helper tentukan rentang tanggal dan judul.
     */
    private function rentangWaktu(string $filter, string $bulan): array
    {
        if ($filter === 'hari') {
            $dari   = now()->toDateString();
            $sampai = now()->toDateString();
            $judul  = 'Hari Ini (' . now()->locale('id')->isoFormat('D MMMM Y') . ')';
        } elseif ($filter === 'bulan') {
            $carbon = Carbon::createFromFormat('Y-m', $bulan);
            $dari   = $carbon->copy()->startOfMonth()->toDateString();
            $sampai = $carbon->copy()->endOfMonth()->toDateString();
            $judul  = 'Bulan ' . $carbon->locale('id')->isoFormat('MMMM Y');
        } else {
            // Default: minggu ini
            $dari   = now()->startOfWeek()->toDateString();
            $sampai = now()->endOfWeek()->toDateString();
            $judul  = 'Minggu Ini (' . now()->startOfWeek()->format('d/m') . ' - ' . now()->endOfWeek()->format('d/m/Y') . ')';
        }

        return [$dari, $sampai, $judul];
    }
}
