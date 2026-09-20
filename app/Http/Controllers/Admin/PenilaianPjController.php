<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenilaianPjLantai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenilaianPjController extends Controller
{
    /**
     * Tampilkan seluruh data penilaian dari PJ Lantai di portal Admin.
     */
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', '');
        $tahun = $request->get('tahun', now()->year);
        $cari  = $request->get('cari', '');

        $query = PenilaianPjLantai::query()->with('petugasCs');

        if ($tahun) {
            $query->whereYear('tanggal_penilaian', $tahun);
        }

        if ($bulan !== '') {
            $query->whereMonth('tanggal_penilaian', $bulan);
        }

        if ($cari) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama_petugas_cs', 'like', "%{$cari}%")
                  ->orWhere('nama_pj', 'like', "%{$cari}%")
                  ->orWhere('lokasi_tugas', 'like', "%{$cari}%");
            });
        }

        $penilaian = $query->latest('tanggal_penilaian')->latest('id')->paginate(15);

        // Ringkasan Statistik
        $totalMasuk = PenilaianPjLantai::count();
        $totalBulanIni = PenilaianPjLantai::whereMonth('tanggal_penilaian', now()->month)
            ->whereYear('tanggal_penilaian', now()->year)
            ->count();
        $rataRataSemua = round(PenilaianPjLantai::avg('rata_rata') ?? 0, 1);
        $kategoriUmum = PenilaianPjLantai::hitungKategori($rataRataSemua);

        $daftarBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $daftarTahun = range(now()->year - 2, now()->year + 1);

        $urlFormPublik = route('penilaian-pj.form');

        return view('admin.penilaian-pj.index', compact(
            'penilaian',
            'totalMasuk',
            'totalBulanIni',
            'rataRataSemua',
            'kategoriUmum',
            'daftarBulan',
            'daftarTahun',
            'bulan',
            'tahun',
            'cari',
            'urlFormPublik'
        ));
    }

    /**
     * Tampilkan lembar detail penilaian.
     */
    public function show($id)
    {
        $penilaian = PenilaianPjLantai::with('petugasCs')->findOrFail($id);
        $indikator = PenilaianPjLantai::daftarIndikator();

        return view('admin.penilaian-pj.show', compact('penilaian', 'indikator'));
    }

    /**
     * Hapus data penilaian.
     */
    public function destroy($id)
    {
        $penilaian = PenilaianPjLantai::findOrFail($id);

        // Hapus file bukti jika ada
        if (!empty($penilaian->bukti_dokumentasi) && is_array($penilaian->bukti_dokumentasi)) {
            foreach ($penilaian->bukti_dokumentasi as $file) {
                if (isset($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }

        $penilaian->delete();

        return redirect()->route('admin.penilaian-pj.index')->with('sukses', 'Data penilaian berhasil dihapus.');
    }

    /**
     * Cetak lembar penilaian (formal print view).
     */
    public function cetak($id)
    {
        $penilaian = PenilaianPjLantai::with(['petugasCs'])->findOrFail($id);
        $indikator = PenilaianPjLantai::daftarIndikator();

        // Gunakan user yang sedang login sebagai penilai (karena tidak ada kolom penilai_id di tabel)
        $penilaiData = auth()->user();

        return view('admin.penilaian-pj.cetak-formal', compact('penilaian', 'indikator', 'penilaiData'));
    }
}
