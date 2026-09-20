<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangInventori;
use App\Models\LaporanStok;
use App\Models\ProdukKebersihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangInventori::query();

        if ($request->has('cari') && $request->cari != '') {
            $query->where('nama_barang', 'like', '%' . $request->cari . '%');
        }

        $barang = $query->paginate(10);

        return view('admin.barang.index', compact('barang'));
    }

    public function create()
    {
        $daftarProduk = ProdukKebersihan::orderBy('nama_barang')->get();
        $daftarSatuan = ProdukKebersihan::$daftarSatuan;

        return view('admin.barang.buat', compact('daftarProduk', 'daftarSatuan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang'   => 'required|string|max:150',
            'kode_barang'   => 'nullable|string|max:30|unique:barang_inventori,kode_barang',
            'stok_saat_ini' => 'required|integer|min:0',
            'satuan'        => 'required|string|max:30',
            'stok_minimum'  => 'nullable|integer|min:0',
            'foto_barang'   => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
        ], [
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
        ]);

        DB::transaction(function () use ($request) {
            $barang = new BarangInventori();
            $barang->nama_barang   = $request->nama_barang;
            $barang->kode_barang   = $request->kode_barang ?: null;
            $barang->deskripsi     = $request->deskripsi;
            $barang->stok_saat_ini = $request->stok_saat_ini;
            $barang->satuan        = $request->satuan;
            $barang->stok_minimum  = $request->stok_minimum ?? 5;

            // Cari pasangan produk_kebersihan berdasarkan kode_barang
            if ($request->kode_barang) {
                $produk = ProdukKebersihan::where('kode_barang', $request->kode_barang)->first();
                $barang->produk_id = $produk?->id;
            }

            if ($request->hasFile('foto_barang')) {
                $path = $request->file('foto_barang')->store('barang', 'public');
                $barang->foto_barang = $path;
            }

            $barang->save();

            // Catat ke laporan stok (stok_masuk) jika stok > 0 dan terhubung ke produk_kebersihan
            if ($barang->produk_id && $request->stok_saat_ini > 0) {
                $this->catatStokMasuk($barang->produk_id, $request->stok_saat_ini);
            }
        });

        return redirect()->route('admin.barang.index')
            ->with('sukses', 'Barang ' . $request->nama_barang . ' berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barang       = BarangInventori::findOrFail($id);
        $daftarProduk = ProdukKebersihan::orderBy('nama_barang')->get();
        $daftarSatuan = ProdukKebersihan::$daftarSatuan;

        return view('admin.barang.ubah', compact('barang', 'daftarProduk', 'daftarSatuan'));
    }

    public function update(Request $request, $id)
    {
        $barang = BarangInventori::findOrFail($id);

        $request->validate([
            'nama_barang'   => 'required|string|max:150',
            'kode_barang'   => 'nullable|string|max:30|unique:barang_inventori,kode_barang,' . $id,
            'stok_saat_ini' => 'required|integer|min:0',
            'satuan'        => 'required|string|max:30',
            'stok_minimum'  => 'nullable|integer|min:0',
            'foto_barang'   => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable',
        ], [
            'kode_barang.unique' => 'Kode barang sudah digunakan.',
        ]);

        DB::transaction(function () use ($request, $barang) {
            $stokLama = $barang->stok_saat_ini;

            $barang->nama_barang   = $request->nama_barang;
            $barang->deskripsi     = $request->deskripsi;
            $barang->stok_saat_ini = $request->stok_saat_ini;
            $barang->satuan        = $request->satuan;
            $barang->stok_minimum  = $request->stok_minimum ?? $barang->stok_minimum ?? 5;

            // Update kode_barang dan relasi produk_id
            $kodeBaru = $request->kode_barang ?: null;
            $barang->kode_barang = $kodeBaru;

            if ($kodeBaru) {
                $produk = ProdukKebersihan::where('kode_barang', $kodeBaru)->first();
                $barang->produk_id = $produk?->id;
            } else {
                $barang->produk_id = null;
            }

            if ($request->hasFile('foto_barang')) {
                if ($barang->foto_barang && Storage::disk('public')->exists($barang->foto_barang)) {
                    Storage::disk('public')->delete($barang->foto_barang);
                }
                $path = $request->file('foto_barang')->store('barang', 'public');
                $barang->foto_barang = $path;
            }

            $barang->save();

            // Jika stok bertambah (barang masuk), catat selisihnya ke laporan stok
            $selisih = $request->stok_saat_ini - $stokLama;
            if ($selisih > 0 && $barang->produk_id) {
                $this->catatStokMasuk($barang->produk_id, $selisih);
            }
        });

        return redirect()->route('admin.barang.index')
            ->with('sukses', 'Barang ' . $request->nama_barang . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = BarangInventori::findOrFail($id);

        if ($barang->foto_barang && Storage::disk('public')->exists($barang->foto_barang)) {
            Storage::disk('public')->delete($barang->foto_barang);
        }

        $barang->delete();

        return redirect()->route('admin.barang.index')
            ->with('sukses', 'Barang berhasil dihapus.');
    }

    // ----------------------------------------------------------------
    // HELPER PRIVATE
    // ----------------------------------------------------------------

    /**
     * Tambahkan jumlah ke stok_masuk laporan_stok periode berjalan.
     * Jika laporan belum ada, buat baru.
     */
    private function catatStokMasuk(int $produkId, int $jumlah): void
    {
        $bulan = now()->month;
        $tahun = now()->year;

        $laporan = LaporanStok::firstOrCreate(
            [
                'produk_id'     => $produkId,
                'periode_bulan' => $bulan,
                'periode_tahun' => $tahun,
            ],
            [
                'tanggal'    => now()->startOfMonth()->toDateString(),
                'stok_masuk' => 0,
            ]
        );

        // Tambah (increment) stok_masuk — bukan replace
        $laporan->increment('stok_masuk', $jumlah);
    }
}
