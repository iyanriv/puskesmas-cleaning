<?php

namespace App\Http\Controllers;

use App\Helpers\KompresiFoto;
use App\Models\TugasMingguan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TugasMingguanController extends Controller
{
    /**
     * Tampilkan daftar riwayat Tugas Mingguan CS.
     */
    public function index(Request $request)
    {
        $pengguna = auth()->user();
        $isCs = in_array($pengguna->peran?->nama_peran ?? '', ['cs', 'pj_lantai']);

        $baseQuery = TugasMingguan::query();

        if ($isCs) {
            $baseQuery->where('user_id', $pengguna->id);
        } else {
            // Filter untuk Supervisor / Admin
            if ($request->filled('petugas_id')) {
                $baseQuery->where('user_id', $request->petugas_id);
            }
        }

        // Statistik ringkas (berdasarkan scope petugas yang aktif)
        $totalTugas = (clone $baseQuery)->count();
        $tugasSelesai = (clone $baseQuery)->whereIn('status', ['selesai', 'disetujui'])->count();
        $tugasProses = (clone $baseQuery)->where('status', 'proses')->count();

        $query = (clone $baseQuery)->with(['user', 'verifikator'])->latest('tanggal')->latest('waktu_pelaporan');
        if (!$isCs && $request->filled('status')) {
            $query->where('status', $request->status);
        }

        $daftarTugas = $query->paginate(15)->withQueryString();

        $daftarCs = !$isCs ? User::whereHas('peran', fn($q) => $q->where('nama_peran', 'cs'))->orderBy('name')->get() : collect();

        return view('tugas-mingguan.index', compact(
            'daftarTugas',
            'pengguna',
            'isCs',
            'totalTugas',
            'tugasSelesai',
            'tugasProses',
            'daftarCs'
        ));
    }

    /**
     * Tampilkan formulir pembuatan Tugas Mingguan CS baru.
     */
    public function buat()
    {
        $pengguna = auth()->user();
        $hariIni = now()->toDateString();
        $jamSekarang = now()->format('H:i');

        return view('tugas-mingguan.buat', compact('pengguna', 'hariIni', 'jamSekarang'));
    }

    /**
     * Simpan tugas mingguan baru.
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'waktu_pelaporan'  => 'required',
            'rincian_kegiatan' => 'required|string|min:5',
            'foto_sebelum'     => 'nullable|array|max:5',
            'foto_sebelum.*'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // Maks 10MB per file
            'foto_setelah'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // Maks 10MB
        ], [
            'tanggal.required'          => 'Tanggal pengerjaan wajib diisi.',
            'waktu_pelaporan.required'  => 'Waktu pelaporan wajib diisi.',
            'rincian_kegiatan.required' => 'Rincian kegiatan wajib diisi.',
            'foto_sebelum.max'          => 'Maksimal 5 foto sebelum yang dapat diunggah.',
            'foto_sebelum.*.image'      => 'Berkas foto sebelum harus berupa gambar.',
            'foto_sebelum.*.max'        => 'Ukuran masing-masing foto sebelum maksimal 10MB.',
            'foto_setelah.image'        => 'Berkas foto setelah harus berupa gambar.',
            'foto_setelah.max'          => 'Ukuran foto setelah maksimal 10MB.',
        ]);

        $pengguna = auth()->user();

        // 1. Unggah & kompres Foto Sebelum (hingga 5 file, max 1280px, quality 80)
        $pathsFotoSebelum = [];
        if ($request->hasFile('foto_sebelum')) {
            $files = is_array($request->file('foto_sebelum'))
                ? $request->file('foto_sebelum')
                : [$request->file('foto_sebelum')];

            $pathsFotoSebelum = KompresiFoto::simpanBanyak(
                array_filter($files, fn($f) => $f && $f->isValid()),
                'tugas-mingguan/sebelum',
                1280,
                80
            );
        }

        // 2. Unggah & kompres Foto Setelah (1 file)
        $pathFotoSetelah = null;
        if ($request->hasFile('foto_setelah') && $request->file('foto_setelah')->isValid()) {
            $pathFotoSetelah = KompresiFoto::simpan(
                $request->file('foto_setelah'),
                'tugas-mingguan/setelah',
                1280,
                80
            );
        }

        $status = $pathFotoSetelah ? 'selesai' : 'proses';

        $tugas = TugasMingguan::create([
            'user_id'          => $pengguna->id,
            'tanggal'          => $request->tanggal,
            'waktu_pelaporan'  => $request->waktu_pelaporan,
            'rincian_kegiatan' => $request->rincian_kegiatan,
            'foto_sebelum'     => $pathsFotoSebelum,
            'foto_setelah'     => $pathFotoSetelah,
            'status'           => $status,
        ]);

        if ($status === 'proses') {
            return redirect()->route('tugas-mingguan.isi-after', $tugas->id)
                ->with('sukses', 'Tugas Mingguan berhasil dicatat! Silakan unggah foto setelah pengerjaan bila telah tuntas.');
        }

        return redirect()->route('tugas-mingguan.detail', $tugas->id)
            ->with('sukses', 'Tugas Mingguan berhasil dikirim dan tersimpan lengkap!');
    }

    /**
     * Tampilkan detail Tugas Mingguan.
     */
    public function detail($id)
    {
        $tugas = TugasMingguan::with(['user', 'verifikator'])->findOrFail($id);
        $pengguna = auth()->user();

        return view('tugas-mingguan.detail', compact('tugas', 'pengguna'));
    }

    /**
     * Form unggah foto setelah pengerjaan (jika sebelumnya disimpan dalam status proses).
     */
    public function isiAfter($id)
    {
        $tugas = TugasMingguan::with('user')->findOrFail($id);
        $pengguna = auth()->user();

        // Hanya pemilik atau admin/supervisor yang boleh mengakses
        if ($tugas->user_id !== $pengguna->id && !in_array($pengguna->peran?->nama_peran ?? '', ['admin', 'supervisor'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk tugas ini.');
        }

        return view('tugas-mingguan.isi-after', compact('tugas', 'pengguna'));
    }

    /**
     * Simpan foto setelah pengerjaan.
     */
    public function simpanAfter(Request $request, $id)
    {
        $request->validate([
            'foto_setelah' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
        ], [
            'foto_setelah.required' => 'Foto setelah pengerjaan wajib diunggah.',
            'foto_setelah.image'    => 'File harus berupa gambar.',
            'foto_setelah.max'      => 'Ukuran foto maksimal 10MB.',
        ]);

        $tugas = TugasMingguan::findOrFail($id);

        if ($request->hasFile('foto_setelah') && $request->file('foto_setelah')->isValid()) {
            if ($tugas->foto_setelah && Storage::disk('public')->exists($tugas->foto_setelah)) {
                Storage::disk('public')->delete($tugas->foto_setelah);
            }
            // Simpan dengan kompresi (max 1280px, quality 80)
            $tugas->foto_setelah = KompresiFoto::simpan(
                $request->file('foto_setelah'),
                'tugas-mingguan/setelah',
                1280,
                80
            );
            $tugas->status = 'selesai';
            $tugas->save();
        }

        return redirect()->route('tugas-mingguan.detail', $tugas->id)
            ->with('sukses', 'Bukti foto setelah pengerjaan berhasil diunggah! Tugas kini selesai.');
    }

    /**
     * Verifikasi tugas mingguan oleh Supervisor / Admin.
     */
    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'status'             => 'required|in:disetujui,proses',
            'catatan_supervisor' => 'nullable|string|max:1000',
        ]);

        $tugas = TugasMingguan::findOrFail($id);
        $tugas->status = $request->status;
        $tugas->catatan_supervisor = $request->catatan_supervisor;
        $tugas->diverifikasi_oleh = auth()->id();
        $tugas->diverifikasi_pada = now();
        $tugas->save();

        return redirect()->route('tugas-mingguan.detail', $tugas->id)
            ->with('sukses', 'Status verifikasi tugas mingguan berhasil diperbarui!');
    }

    /**
     * Cetak laporan tugas mingguan (Admin/Supervisor).
     */
    public function cetakLaporan(Request $request)
    {
        $petugasId = $request->get('petugas_id', '');
        $status    = $request->get('status', '');
        $dari      = $request->get('dari', now()->startOfMonth()->toDateString());
        $sampai    = $request->get('sampai', now()->toDateString());

        $query = TugasMingguan::with(['user', 'verifikator'])
            ->whereBetween('tanggal', [$dari, $sampai])
            ->latest('tanggal')
            ->latest('waktu_pelaporan');

        if ($petugasId) {
            $query->where('user_id', $petugasId);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $daftarTugas = $query->get();

        // Rekap per petugas
        $rekapPertugas = $daftarTugas->groupBy('user_id')->map(fn($g) => [
            'nama'      => $g->first()->user->name ?? '-',
            'total'     => $g->count(),
            'selesai'   => $g->whereIn('status', ['selesai', 'disetujui'])->count(),
            'disetujui' => $g->where('status', 'disetujui')->count(),
            'proses'    => $g->where('status', 'proses')->count(),
        ])->sortByDesc('total');

        $totalTugas    = $daftarTugas->count();
        $totalSelesai  = $daftarTugas->whereIn('status', ['selesai', 'disetujui'])->count();
        $totalDisetujui = $daftarTugas->where('status', 'disetujui')->count();
        $totalProses   = $daftarTugas->where('status', 'proses')->count();

        $petugasFilter = $petugasId ? User::find($petugasId) : null;
        $pengguna      = auth()->user();

        return view('tugas-mingguan.cetak', compact(
            'daftarTugas', 'rekapPertugas',
            'totalTugas', 'totalSelesai', 'totalDisetujui', 'totalProses',
            'dari', 'sampai', 'petugasFilter', 'pengguna', 'status'
        ));
    }
}
