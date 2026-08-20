<?php

namespace App\Http\Controllers;

use App\Models\BarangInventori;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;

class PermintaanBarangController extends Controller
{
    // ================================================================
    // SISI CS: Lihat katalog & ajukan permintaan
    // ================================================================

    /**
     * Tampilkan katalog barang + riwayat permintaan CS.
     */
    public function katalog()
    {
        $pengguna = auth()->user();

        $daftarBarang = BarangInventori::orderBy('nama_barang')->get();

        // Riwayat permintaan milik CS ini (10 terakhir)
        $riwayatPermintaan = PermintaanBarang::with('barang')
            ->where('user_id', $pengguna->id)
            ->latest()
            ->take(10)
            ->get();

        return view('barang.katalog', compact('daftarBarang', 'riwayatPermintaan', 'pengguna'));
    }

    /**
     * Proses pengajuan permintaan barang dari CS.
     */
    public function ajukan(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang_inventori,id',
            'jumlah'    => 'required|integer|min:1|max:999',
        ], [
            'barang_id.required' => 'Pilih barang yang ingin diminta.',
            'jumlah.required'    => 'Jumlah wajib diisi.',
            'jumlah.min'         => 'Jumlah minimal 1.',
            'jumlah.max'         => 'Jumlah maksimal 999.',
        ]);

        $pengguna = auth()->user();
        $barang   = BarangInventori::findOrFail($request->barang_id);

        // Cek apakah ada permintaan pending untuk barang yang sama
        $sudahAda = PermintaanBarang::where('user_id', $pengguna->id)
            ->where('barang_id', $request->barang_id)
            ->where('status_request', 'pending')
            ->exists();

        if ($sudahAda) {
            return back()->with('gagal', 'Anda sudah memiliki permintaan ' . $barang->nama_barang . ' yang masih menunggu persetujuan.');
        }

        PermintaanBarang::create([
            'user_id'        => $pengguna->id,
            'barang_id'      => $request->barang_id,
            'jumlah'         => $request->jumlah,
            'status_request' => 'pending',
            'waktu_request'  => now(),
        ]);

        return back()->with('sukses', 'Permintaan ' . $barang->nama_barang . ' sebanyak ' . $request->jumlah . ' ' . $barang->satuan . ' berhasil dikirim!');
    }

    // ================================================================
    // SISI GUDANG: Kelola permintaan
    // ================================================================

    /**
     * Tampilkan daftar permintaan pending untuk Petugas Gudang.
     */
    public function daftarGudang()
    {
        // Pending — menunggu persetujuan
        $permintaanPending = PermintaanBarang::with(['pengguna', 'barang'])
            ->where('status_request', 'pending')
            ->latest()
            ->get();

        // Sudah diproses hari ini
        $sudahDiproses = PermintaanBarang::with(['pengguna', 'barang'])
            ->whereIn('status_request', ['disetujui', 'ditolak'])
            ->whereDate('waktu_approve', today())
            ->latest('waktu_approve')
            ->take(15)
            ->get();

        // Semua barang untuk referensi stok
        $semuaBarang = BarangInventori::orderBy('nama_barang')->get();

        return view('barang.gudang-daftar', compact(
            'permintaanPending',
            'sudahDiproses',
            'semuaBarang'
        ));
    }

    /**
     * Gudang menyetujui permintaan → stok otomatis berkurang.
     */
    public function setujui($id)
    {
        $permintaan = PermintaanBarang::with('barang')->findOrFail($id);

        if ($permintaan->status_request !== 'pending') {
            return back()->with('info', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $barang = $permintaan->barang;

        // Validasi stok mencukupi
        if ($barang->stok_saat_ini < $permintaan->jumlah) {
            return back()->with('gagal',
                'Stok ' . $barang->nama_barang . ' tidak mencukupi. '
                . 'Stok saat ini: ' . $barang->stok_saat_ini . ' ' . $barang->satuan
                . ', diminta: ' . $permintaan->jumlah . ' ' . $barang->satuan . '.'
            );
        }

        // Kurangi stok
        $barang->decrement('stok_saat_ini', $permintaan->jumlah);

        // Update status permintaan
        $permintaan->update([
            'status_request' => 'disetujui',
            'waktu_approve'  => now(),
        ]);

        return back()->with('sukses',
            'Permintaan ' . $permintaan->pengguna->name . ' untuk ' . $barang->nama_barang
            . ' (' . $permintaan->jumlah . ' ' . $barang->satuan . ') telah disetujui. Stok dikurangi.'
        );
    }

    /**
     * Gudang menolak permintaan dengan alasan.
     */
    public function tolak(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $permintaan = PermintaanBarang::with('barang')->findOrFail($id);

        if ($permintaan->status_request !== 'pending') {
            return back()->with('info', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $permintaan->update([
            'status_request'   => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
            'waktu_approve'    => now(),
        ]);

        return back()->with('sukses', 'Permintaan dari ' . $permintaan->pengguna->name . ' telah ditolak.');
    }
}
