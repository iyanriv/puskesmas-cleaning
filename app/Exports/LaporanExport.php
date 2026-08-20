<?php

namespace App\Exports;

use App\Models\CeklisKebersihan;
use App\Models\PermintaanBarang;
use App\Models\SetoranSampah;
use App\Models\PenilaianKinerja;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanExport implements FromView, ShouldAutoSize
{
    protected $dari;
    protected $sampai;
    protected $judul;

    public function __construct($dari, $sampai, $judul)
    {
        $this->dari = $dari;
        $this->sampai = $sampai;
        $this->judul = $judul;
    }

    public function view(): View
    {
        $dari = $this->dari;
        $sampai = $this->sampai;
        $judul = $this->judul;

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

        return view('laporan.excel', compact(
            'judul', 'dari', 'sampai',
            'totalCeklis', 'ceklisSelesai', 'ceklisPersen', 'ceklisPerArea',
            'totalPermintaan', 'permintaanDisetujui', 'permintaanDitolak',
            'totalKgSampah', 'totalSetoran',
            'rataKinerja', 'topPerformer'
        ));
    }
}
