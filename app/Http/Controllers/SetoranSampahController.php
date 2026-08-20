<?php

namespace App\Http\Controllers;

use App\Models\SetoranSampah;
use Illuminate\Http\Request;

class SetoranSampahController extends Controller
{
    // Daftar jenis sampah yang tersedia
    const JENIS_SAMPAH = [
        'Botol Plastik' => '🧴',
        'Kardus'        => '📦',
        'Derigen'       => '🪣',
        'Kertas'        => '📄',
        'Duplex'        => '🗂️',
        'Kaleng'        => '🥫',
        'Koran'         => '📰',
        'Lainnya'       => '➕',
    ];

    // Daftar lokasi setor
    const LOKASI_SETOR = [
        'Lantai 1',
        'Lantai 2',
        'Lantai 3',
        'Lantai 4',
        'Lantai 5',
        'PUSTU Cempaka Putih Barat',
        'PUSTU Cempaka Putih Timur',
        'PUSTU Rawasari'
    ];

    // ================================================================
    // SISI CS: Form setor sampah
    // ================================================================

    /**
     * Tampilkan form setor sampah.
     */
    public function buat()
    {
        $jenisSampah  = self::JENIS_SAMPAH;
        $lokasiSetor  = self::LOKASI_SETOR;
        $pengguna     = auth()->user();

        // Riwayat setoran milik CS ini (7 terakhir)
        $riwayat = SetoranSampah::where('user_id', $pengguna->id)
            ->latest()
            ->take(7)
            ->get();

        return view('sampah.buat', compact('jenisSampah', 'lokasiSetor', 'riwayat', 'pengguna'));
    }

    /**
     * Simpan setoran sampah.
     */
    public function simpan(Request $request)
    {
        $request->validate([
            'lokasi_setor'  => 'required|string',
            'jenis_sampah'  => 'required|array|min:1',
            'jenis_sampah.*'=> 'string',
            'foto_timbangan'=> 'required|image|max:2048',
            'catatan'       => 'required|string|max:500',
        ], [
            'lokasi_setor.required'  => 'Pilih lokasi/area setor.',
            'jenis_sampah.required'  => 'Pilih minimal satu jenis sampah.',
            'jenis_sampah.min'       => 'Pilih minimal satu jenis sampah.',
            'foto_timbangan.required'=> 'Foto bukti wajib diupload.',
            'foto_timbangan.image'   => 'File harus berupa gambar.',
            'foto_timbangan.max'     => 'Ukuran foto maksimal 2MB.',
            'catatan.required'       => 'Catatan/Keterangan nama barang wajib diisi.',
        ]);

        $pengguna = auth()->user();

        // Simpan foto timbangan jika ada
        $namaFoto = null;
        if ($request->hasFile('foto_timbangan')) {
            $namaFoto = $request->file('foto_timbangan')->store('sampah', 'public');
        }

        SetoranSampah::create([
            'user_id'        => $pengguna->id,
            'jenis_sampah'   => $request->jenis_sampah,
            'lokasi_setor'   => $request->lokasi_setor,
            'foto_timbangan' => $namaFoto,
            'catatan'        => $request->catatan,
            'tanggal'        => now()->toDateString(),
        ]);

        return redirect()->route('sampah.buat')
            ->with('sukses', 'Laporan bank sampah berhasil dikirim! ♻️');
    }

    // ================================================================
    // SISI SUPERVISOR: Rekapan
    // ================================================================

    /**
     * Tampilkan rekapan setoran sampah (harian, mingguan, bulanan).
     */
    public function rekapan(Request $request)
    {
        $filter  = $request->get('filter', 'minggu'); // hari | minggu | bulan
        $bulan   = $request->get('bulan', now()->format('Y-m'));

        // Tentukan rentang tanggal berdasarkan filter
        switch ($filter) {
            case 'hari':
                $dari    = now()->startOfDay();
                $sampai  = now()->endOfDay();
                $judul   = 'Hari Ini (' . now()->translatedFormat('d F Y') . ')';
                break;
            case 'bulan':
                $dari    = now()->parse($bulan . '-01')->startOfMonth();
                $sampai  = now()->parse($bulan . '-01')->endOfMonth();
                $judul   = 'Bulan ' . now()->parse($bulan . '-01')->translatedFormat('F Y');
                break;
            default: // minggu
                $dari    = now()->startOfWeek();
                $sampai  = now()->endOfWeek();
                $judul   = 'Minggu Ini (' . $dari->format('d') . '–' . $sampai->translatedFormat('d F Y') . ')';
        }

        // Semua setoran dalam rentang
        $semuaSetoran = SetoranSampah::with('pengguna')
            ->whereBetween('tanggal', [$dari->toDateString(), $sampai->toDateString()])
            ->latest('tanggal')
            ->get();

        // Total berat per jenis sampah -> diganti jadi frekuensi
        $rekapJenis = [];
        foreach ($semuaSetoran as $setoran) {
            $jenisList = is_array($setoran->jenis_sampah) ? $setoran->jenis_sampah : [$setoran->jenis_sampah];
            foreach ($jenisList as $jenis) {
                if (!isset($rekapJenis[$jenis])) {
                    $rekapJenis[$jenis] = ['jumlah_setor' => 0];
                }
                $rekapJenis[$jenis]['jumlah_setor'] += 1;
            }
        }
        arsort($rekapJenis);

        // Total setoran keseluruhan
        $totalSetoran = $semuaSetoran->count();

        // Rekap per petugas
        $rekapPertugas = $semuaSetoran->groupBy('user_id')->map(function ($group) {
            return [
                'nama'         => $group->first()->pengguna->name,
                'jumlah_setor' => $group->count(),
            ];
        })->sortByDesc('jumlah_setor');

        // Daftar bulan untuk filter
        $daftarBulan = collect(range(0, 11))->map(fn($i) => now()->subMonths($i)->format('Y-m'));

        return view('sampah.rekapan', compact(
            'semuaSetoran', 'rekapJenis', 'totalSetoran',
            'rekapPertugas', 'filter', 'bulan', 'judul', 'daftarBulan'
        ));
    }
}
