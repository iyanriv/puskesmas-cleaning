<?php

namespace App\Http\Controllers;

use App\Models\PenilaianKinerja;
use App\Models\User;
use Illuminate\Http\Request;

class PenilaianKinerjaController extends Controller
{
    /**
     * Daftar petugas untuk dinilai oleh Supervisor.
     */
    public function index()
    {
        $pengguna = auth()->user();

        // Semua CS dan PJ yang bisa dinilai
        $daftarPetugas = User::whereHas('peran', fn($q) =>
            $q->whereIn('nama_peran', ['cs', 'pj_lantai'])
        )->with('area')->orderBy('name')->get();

        // Penilaian bulan ini oleh supervisor ini
        $sudahDinilai = PenilaianKinerja::where('penilai_id', $pengguna->id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->pluck('dinilai_id')
            ->toArray();

        return view('penilaian.index', compact('daftarPetugas', 'sudahDinilai'));
    }

    /**
     * Form penilaian untuk satu petugas.
     */
    public function buat($id)
    {
        $pengguna = auth()->user();
        $dinilai  = User::with('area', 'peran')->findOrFail($id);

        // Cek sudah dinilai bulan ini
        $sudah = PenilaianKinerja::where('penilai_id', $pengguna->id)
            ->where('dinilai_id', $id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->first();

        // Riwayat 3 penilaian sebelumnya
        $riwayat = PenilaianKinerja::where('dinilai_id', $id)
            ->with('penilai')
            ->latest('tanggal')
            ->take(3)
            ->get();

        return view('penilaian.buat', compact('dinilai', 'sudah', 'riwayat'));
    }

    /**
     * Simpan penilaian.
     */
    public function simpan(Request $request, $id)
    {
        $request->validate([
            'nilai_kebersihan'   => 'required|integer|min:1|max:5',
            'nilai_kedisiplinan' => 'required|integer|min:1|max:5',
            'nilai_kerjasama'    => 'required|integer|min:1|max:5',
            'nilai_inisiatif'    => 'required|integer|min:1|max:5',
            'catatan'            => 'nullable|string|max:1000',
        ], [
            'nilai_kebersihan.required'   => 'Nilai kebersihan wajib diisi.',
            'nilai_kedisiplinan.required' => 'Nilai kedisiplinan wajib diisi.',
            'nilai_kerjasama.required'    => 'Nilai kerjasama wajib diisi.',
            'nilai_inisiatif.required'    => 'Nilai inisiatif wajib diisi.',
        ]);

        $pengguna = auth()->user();
        $dinilai  = User::findOrFail($id);

        // Cek sudah dinilai bulan ini (mencegah duplikasi)
        $sudah = PenilaianKinerja::where('penilai_id', $pengguna->id)
            ->where('dinilai_id', $id)
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->exists();

        if ($sudah) {
            return redirect()->route('penilaian.index')
                ->with('info', $dinilai->name . ' sudah dinilai bulan ini.');
        }

        PenilaianKinerja::create([
            'penilai_id'         => $pengguna->id,
            'dinilai_id'         => $id,
            'tanggal'            => now()->toDateString(),
            'nilai_kebersihan'   => $request->nilai_kebersihan,
            'nilai_kedisiplinan' => $request->nilai_kedisiplinan,
            'nilai_kerjasama'    => $request->nilai_kerjasama,
            'nilai_inisiatif'    => $request->nilai_inisiatif,
            'catatan'            => $request->catatan,
        ]);

        return redirect()->route('penilaian.index')
            ->with('sukses', 'Penilaian untuk ' . $dinilai->name . ' berhasil disimpan! ⭐');
    }

    /**
     * Lihat detail riwayat penilaian satu petugas.
     */
    public function detail($id)
    {
        $dinilai   = User::with('area', 'peran')->findOrFail($id);
        $penilaian = PenilaianKinerja::where('dinilai_id', $id)
            ->with('penilai')
            ->latest('tanggal')
            ->get();

        // Rata-rata per aspek seluruh waktu
        $rataAspek = [
            'kebersihan'   => round($penilaian->avg('nilai_kebersihan'), 1),
            'kedisiplinan' => round($penilaian->avg('nilai_kedisiplinan'), 1),
            'kerjasama'    => round($penilaian->avg('nilai_kerjasama'), 1),
            'inisiatif'    => round($penilaian->avg('nilai_inisiatif'), 1),
        ];

        return view('penilaian.detail', compact('dinilai', 'penilaian', 'rataAspek'));
    }

    /**
     * Rekap semua petugas (untuk supervisor/admin).
     */
    public function rekap()
    {
        $bulan = request('bulan', now()->format('Y-m'));
        [$tahun, $bln] = explode('-', $bulan);

        $semuaPenilaian = PenilaianKinerja::with(['dinilai.area', 'penilai'])
            ->whereMonth('tanggal', $bln)
            ->whereYear('tanggal', $tahun)
            ->get();

        // Kelompokkan per petugas
        $rekapPerPetugas = $semuaPenilaian->groupBy('dinilai_id')->map(function ($group) {
            $item = $group->first();
            return [
                'nama'           => $item->dinilai->name,
                'area'           => $item->dinilai->area?->nama_ruangan ?? '-',
                'kebersihan'     => round($group->avg('nilai_kebersihan'), 1),
                'kedisiplinan'   => round($group->avg('nilai_kedisiplinan'), 1),
                'kerjasama'      => round($group->avg('nilai_kerjasama'), 1),
                'inisiatif'      => round($group->avg('nilai_inisiatif'), 1),
                'rata_rata'      => round(($group->avg('nilai_kebersihan') + $group->avg('nilai_kedisiplinan') + $group->avg('nilai_kerjasama') + $group->avg('nilai_inisiatif')) / 4, 1),
                'jumlah'         => $group->count(),
            ];
        })->sortByDesc('rata_rata');

        $daftarBulan = collect(range(0, 11))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        return view('penilaian.rekap', compact('rekapPerPetugas', 'bulan', 'daftarBulan'));
    }
}
