<?php

namespace App\Exports;

use App\Models\TugasMingguan;
use App\Models\PermintaanBarang;
use App\Models\SetoranSampah;
use App\Models\PenilaianPjLantai;
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

        $totalCeklis   = TugasMingguan::whereBetween('tanggal', [$dari, $sampai])->count();
        $ceklisSelesai = TugasMingguan::whereBetween('tanggal', [$dari, $sampai])->whereIn('status', ['selesai', 'disetujui'])->count();
        $ceklisPersen  = $totalCeklis > 0 ? round(($ceklisSelesai / $totalCeklis) * 100) : 0;
        
        $ceklisPerArea = TugasMingguan::with('user')->whereBetween('tanggal', [$dari, $sampai])->get()->groupBy('user_id')->map(fn($g) => [
            'nama'    => $g->first()->user?->name ?? 'Petugas CS',
            'total'   => $g->count(),
            'selesai' => $g->whereIn('status', ['selesai', 'disetujui'])->count(),
        ]);

        $totalPermintaan     = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->count();
        $permintaanDisetujui = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->where('status_request', 'disetujui')->count();
        $permintaanDitolak   = PermintaanBarang::whereBetween('waktu_request', [$dari . ' 00:00:00', $sampai . ' 23:59:59'])->where('status_request', 'ditolak')->count();

        $setoranList   = SetoranSampah::whereBetween('tanggal', [$dari, $sampai])->get();
        $totalKgSampah = 0; // kolom berat_kg sudah dihapus; gunakan jumlah setoran sebagai metrik
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

        return view('laporan.excel', compact(
            'judul', 'dari', 'sampai',
            'totalCeklis', 'ceklisSelesai', 'ceklisPersen', 'ceklisPerArea',
            'totalPermintaan', 'permintaanDisetujui', 'permintaanDitolak',
            'totalKgSampah', 'totalSetoran',
            'rataKinerja', 'topPerformer'
        ));
    }
}
