<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\CeklisKebersihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CeklisKebersihanController extends Controller
{
    /**
     * Tampilkan daftar area dan status ceklis hari ini milik CS.
     */
    public function index()
    {
        $pengguna = auth()->user();
        $hariIni  = now()->toDateString();

        // Ambil semua area beserta status ceklis hari ini milik CS ini
        $daftarArea = Area::orderBy('lantai')->orderBy('nama_ruangan')->get()->map(function ($area) use ($pengguna, $hariIni) {
            $ceklis = CeklisKebersihan::where('user_id', $pengguna->id)
                ->where('area_id', $area->id)
                ->where('tanggal', $hariIni)
                ->first();

            return [
                'area'   => $area,
                'ceklis' => $ceklis,
                'status' => $ceklis ? $ceklis->status : 'belum',
            ];
        });

        return view('ceklis.daftar-area', compact('daftarArea', 'pengguna'));
    }

    /**
     * Tampilkan form pengisian foto BEFORE untuk area tertentu.
     */
    public function buat($area_id)
    {
        $area     = Area::findOrFail($area_id);
        $pengguna = auth()->user();
        $hariIni  = now()->toDateString();

        // Cek apakah sudah ada ceklis hari ini untuk area ini
        $ceklisAda = CeklisKebersihan::where('user_id', $pengguna->id)
            ->where('area_id', $area_id)
            ->where('tanggal', $hariIni)
            ->first();

        // Jika sudah ada dan statusnya selesai, arahkan kembali
        if ($ceklisAda && $ceklisAda->status === 'selesai') {
            return redirect()->route('ceklis.index')
                ->with('info', 'Ceklis area ' . $area->nama_ruangan . ' sudah selesai hari ini.');
        }

        // Jika ceklis sedang proses (foto before sudah ada), lanjut ke foto after
        if ($ceklisAda && $ceklisAda->foto_before) {
            return redirect()->route('ceklis.isi-after', $ceklisAda->id);
        }

        return view('ceklis.buat', compact('area'));
    }

    /**
     * Simpan foto BEFORE & buat record ceklis baru dengan status 'proses'.
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'area_id'   => 'required|exists:area,id',
            'foto_before' => 'required|image|max:2048',
            'lat_long'  => 'nullable|string',
        ], [
            'foto_before.required' => 'Foto BEFORE wajib diambil.',
            'foto_before.image'    => 'File harus berupa gambar.',
            'foto_before.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        $pengguna = auth()->user();
        $hariIni  = now()->toDateString();

        // Simpan foto ke storage/app/public/ceklis/
        $namaFoto = $request->file('foto_before')->store('ceklis', 'public');

        $ceklis = CeklisKebersihan::create([
            'user_id'     => $pengguna->id,
            'area_id'     => $request->area_id,
            'tanggal'     => $hariIni,
            'waktu_mulai' => now()->format('H:i:s'),
            'foto_before' => $namaFoto,
            'lat_long'    => $request->lat_long,
            'status'      => 'proses',
        ]);

        return redirect()->route('ceklis.isi-after', $ceklis->id)
            ->with('sukses', 'Foto BEFORE berhasil disimpan! Sekarang selesaikan pekerjaan dan ambil foto AFTER.');
    }

    /**
     * Tampilkan form pengisian foto AFTER.
     */
    public function isiAfter($id)
    {
        $ceklis   = CeklisKebersihan::with('area')->findOrFail($id);
        $pengguna = auth()->user();

        // Pastikan ceklis milik pengguna yang login
        if ($ceklis->user_id !== $pengguna->id) {
            abort(403, 'Akses ditolak.');
        }

        if ($ceklis->status === 'selesai') {
            return redirect()->route('ceklis.index')
                ->with('info', 'Ceklis ini sudah selesai.');
        }

        return view('ceklis.isi-after', compact('ceklis'));
    }

    /**
     * Simpan foto AFTER dan tandai ceklis sebagai SELESAI.
     */
    public function simpanAfter(Request $request, $id)
    {
        $request->validate([
            'foto_after' => 'required|image|max:2048',
            'lat_long'   => 'nullable|string',
        ], [
            'foto_after.required' => 'Foto AFTER wajib diambil.',
            'foto_after.image'    => 'File harus berupa gambar.',
            'foto_after.max'      => 'Ukuran foto maksimal 2MB.',
        ]);

        $ceklis   = CeklisKebersihan::findOrFail($id);
        $pengguna = auth()->user();

        if ($ceklis->user_id !== $pengguna->id) {
            abort(403, 'Akses ditolak.');
        }

        // Simpan foto after
        $namaFoto = $request->file('foto_after')->store('ceklis', 'public');

        $ceklis->update([
            'foto_after'    => $namaFoto,
            'waktu_selesai' => now()->format('H:i:s'),
            'lat_long'      => $request->lat_long ?? $ceklis->lat_long,
            'status'        => 'selesai',
        ]);

        return redirect()->route('dasbor.cs')
            ->with('sukses', 'Ceklis area ' . $ceklis->area->nama_ruangan . ' berhasil diselesaikan! ✅');
    }

    /**
     * Detail ceklis (untuk dilihat supervisor).
     */
    public function detail($id)
    {
        $ceklis = CeklisKebersihan::with(['pengguna', 'area'])->findOrFail($id);

        return view('ceklis.detail', compact('ceklis'));
    }

    /**
     * Supervisor memberikan skor & catatan pada ceklis (FR-029).
     */
    public function nilaiCeklis(Request $request, $id)
    {
        $request->validate([
            'skor'    => 'required|integer|min:1|max:5',
            'catatan' => 'nullable|string|max:500',
        ], [
            'skor.required' => 'Pilih skor penilaian (1–5 bintang).',
            'skor.min'      => 'Skor minimal 1.',
            'skor.max'      => 'Skor maksimal 5.',
        ]);

        $ceklis = CeklisKebersihan::findOrFail($id);

        // Hanya ceklis yang sudah selesai bisa dinilai
        if ($ceklis->status !== 'selesai') {
            return back()->with('gagal', 'Hanya ceklis yang sudah selesai yang dapat dinilai.');
        }

        $ceklis->update([
            'skor'    => $request->skor,
            'catatan' => $request->catatan,
        ]);

        return back()->with('sukses', 'Penilaian ceklis berhasil disimpan! ⭐');
    }
}
