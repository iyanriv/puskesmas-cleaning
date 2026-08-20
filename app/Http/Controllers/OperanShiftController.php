<?php

namespace App\Http\Controllers;

use App\Models\OperanShift;
use App\Models\User;
use Illuminate\Http\Request;

class OperanShiftController extends Controller
{
    // Daftar peralatan kebersihan yang dicek kondisinya (FR-015)
    const DAFTAR_ALAT = [
        'Sapu'          => 'bi-brush',
        'Pel'           => 'bi-droplet-half',
        'Ember'         => 'bi-bucket',
        'Kain Lap'      => 'bi-moisture',
        'Toilet Brush'  => 'bi-stars',
        'Serok Sampah'  => 'bi-trash',
        'Tangga'        => 'bi-ladder',
        'Mesin Poles'   => 'bi-gear-fill',
    ];

    /**
     * Tampilkan halaman utama operan shift:
     * - Form kirim operan
     * - Daftar operan masuk yang belum diterima
     * - Riwayat operan hari ini
     */
    public function index()
    {
        $pengguna = auth()->user();
        $hariIni  = now()->toDateString();

        // Daftar CS/PJ lain yang bisa jadi penerima (kecuali diri sendiri)
        $daftarPenerima = User::whereHas('peran', fn ($q) => $q->whereIn('nama_peran', ['cs', 'pj_lantai']))
            ->where('id', '!=', $pengguna->id)
            ->orderBy('name')
            ->get();

        // Daftar alat untuk form (FR-015)
        $daftarAlat = self::DAFTAR_ALAT;

        // Operan MASUK yang belum diterima (penerima = user login)
        $operanMasuk = OperanShift::with('pengirim')
            ->where('penerima_id', $pengguna->id)
            ->where('status_terima', 'menunggu')
            ->latest()
            ->get();

        // Operan yang sudah dikirim hari ini oleh user ini
        $operanDikirim = OperanShift::with('penerima')
            ->where('pengirim_id', $pengguna->id)
            ->where('tanggal', $hariIni)
            ->latest()
            ->get();

        // Riwayat operan diterima hari ini
        $operanDiterima = OperanShift::with('pengirim')
            ->where('penerima_id', $pengguna->id)
            ->where('tanggal', $hariIni)
            ->where('status_terima', 'diterima')
            ->latest()
            ->get();

        return view('operan.index', compact(
            'pengguna',
            'daftarPenerima',
            'daftarAlat',
            'operanMasuk',
            'operanDikirim',
            'operanDiterima'
        ));
    }

    /**
     * Kirim operan shift ke rekan.
     */
    public function kirim(Request $request)
    {
        $request->validate([
            'penerima_id'  => 'required|exists:users,id',
            'tempat_tugas' => 'required|string',
            'waktu_jaga'   => 'required|string',
            'catatan'      => 'required|string|max:1000',
            'status_alat'  => 'nullable|array',   // FR-015: array kondisi peralatan
        ], [
            'penerima_id.required' => 'Pilih rekan penerima operan.',
            'penerima_id.exists'   => 'Penerima tidak ditemukan.',
            'tempat_tugas.required'=> 'Pilih tempat tugas Anda.',
            'waktu_jaga.required'  => 'Pilih waktu jaga/shift Anda.',
            'catatan.required'     => 'Uraian kegiatan wajib diisi.',
        ]);

        $pengguna = auth()->user();

        // Cek apakah sudah ada operan ke orang yang sama hari ini
        $sudahAda = OperanShift::where('pengirim_id', $pengguna->id)
            ->where('penerima_id', $request->penerima_id)
            ->whereDate('tanggal', now()->toDateString())
            ->exists();

        if ($sudahAda) {
            return back()->with('gagal', 'Anda sudah mengirim operan ke rekan ini hari ini.');
        }

        OperanShift::create([
            'pengirim_id'  => $pengguna->id,
            'penerima_id'  => $request->penerima_id,
            'tanggal'      => now()->toDateString(),
            'waktu'        => now()->format('H:i:s'),
            'tempat_tugas' => $request->tempat_tugas,
            'waktu_jaga'   => $request->waktu_jaga,
            'status_alat'  => $request->status_alat ?? [],   // FR-015
            'catatan'      => $request->catatan,
            'status_terima'=> 'menunggu',
        ]);

        return back()->with('sukses', 'Operan shift berhasil dikirim! Menunggu konfirmasi penerima.');
    }

    /**
     * Penerima konfirmasi menerima operan.
     */
    public function terima($id)
    {
        $operan   = OperanShift::findOrFail($id);
        $pengguna = auth()->user();

        // Pastikan yang konfirmasi adalah penerima operan
        if ($operan->penerima_id !== $pengguna->id) {
            abort(403, 'Anda bukan penerima operan ini.');
        }

        if ($operan->status_terima === 'diterima') {
            return back()->with('info', 'Operan ini sudah diterima sebelumnya.');
        }

        $operan->update(['status_terima' => 'diterima']);

        return back()->with('sukses', 'Operan dari ' . $operan->pengirim->name . ' berhasil diterima! ✅');
    }

    /**
     * Detail operan (opsional, untuk melihat catatan & status alat lengkap).
     */
    public function detail($id)
    {
        $operan   = OperanShift::with(['pengirim', 'penerima'])->findOrFail($id);
        $pengguna = auth()->user();

        // Hanya pengirim atau penerima yang boleh melihat
        if (!in_array($pengguna->id, [$operan->pengirim_id, $operan->penerima_id])) {
            abort(403);
        }

        return view('operan.detail', compact('operan'));
    }
}
