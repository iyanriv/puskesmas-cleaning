<?php

namespace App\Http\Controllers;

use App\Models\BarangInventori;
use App\Models\MutasiStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokMasukController extends Controller
{
    // ----------------------------------------------------------------
    // HALAMAN UTAMA: Form Stok Masuk + Riwayat Mutasi
    // ----------------------------------------------------------------

    public function index(Request $request)
    {
        $daftarBarang  = BarangInventori::orderBy('nama_barang')->get();
        $daftarKategori = BarangInventori::$daftarKategori;

        // Filter riwayat mutasi
        $filterBarangId  = $request->get('barang_id');
        $filterJenis     = $request->get('jenis_transaksi');
        $filterDari      = $request->get('dari', now()->startOfMonth()->toDateString());
        $filterSampai    = $request->get('sampai', now()->toDateString());

        $query = MutasiStok::with(['barang', 'petugas'])
            ->whereBetween('tanggal', [$filterDari, $filterSampai])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($filterBarangId) {
            $query->where('barang_id', $filterBarangId);
        }
        if ($filterJenis) {
            $query->where('jenis_transaksi', $filterJenis);
        }

        $riwayat = $query->paginate(20)->withQueryString();

        // Statistik ringkas untuk periode yang ditampilkan
        $totalMasuk     = MutasiStok::whereBetween('tanggal', [$filterDari, $filterSampai])->sum('jumlah_masuk');
        $totalKeluar    = MutasiStok::whereBetween('tanggal', [$filterDari, $filterSampai])->sum('jumlah_keluar');
        $totalJenisBarang = BarangInventori::count();
        $totalStok      = BarangInventori::sum('stok_saat_ini');
        $stokAman       = BarangInventori::whereColumn('stok_saat_ini', '>', 'stok_minimum')->count();
        $stokMenipis    = BarangInventori::whereColumn('stok_saat_ini', '<=', 'stok_minimum')->where('stok_saat_ini', '>', 0)->count();
        $stokHabis      = BarangInventori::where('stok_saat_ini', '<=', 0)->count();

        $jenisTransaksi = [
            'stok_masuk'     => 'Stok Masuk / Restok',
            'distribusi'     => 'Distribusi / Keluar',
            'pemakaian_unit' => 'Pemakaian Unit',
            'stok_awal'      => 'Stok Awal',
            'penyesuaian'    => 'Penyesuaian Stok',
        ];

        return view('stok-masuk.index', compact(
            'daftarBarang',
            'daftarKategori',
            'riwayat',
            'filterBarangId',
            'filterJenis',
            'filterDari',
            'filterSampai',
            'totalMasuk',
            'totalKeluar',
            'totalJenisBarang',
            'totalStok',
            'stokAman',
            'stokMenipis',
            'stokHabis',
            'jenisTransaksi',
        ));
    }

    // ----------------------------------------------------------------
    // SIMPAN STOK MASUK / RESTOK
    // ----------------------------------------------------------------

    public function store(Request $request)
    {
        $request->validate([
            'barang_id'      => 'required|exists:barang_inventori,id',
            'tanggal'        => 'required|date',
            'jumlah_masuk'   => 'required|integer|min:1',
            'sumber_pasokan' => 'nullable|string|max:150',
            'nomor_referensi'=> 'nullable|string|max:100',
            'keterangan'     => 'nullable|string|max:500',
        ], [
            'barang_id.required'   => 'Pilih barang yang akan direstok.',
            'barang_id.exists'     => 'Barang tidak ditemukan.',
            'tanggal.required'     => 'Tanggal masuk wajib diisi.',
            'jumlah_masuk.required'=> 'Jumlah stok masuk wajib diisi.',
            'jumlah_masuk.min'     => 'Jumlah stok masuk minimal 1.',
        ]);

        DB::transaction(function () use ($request) {
            $barang = BarangInventori::lockForUpdate()->findOrFail($request->barang_id);
            $saldoSebelum = $barang->stok_saat_ini;
            $saldoSesudah = $saldoSebelum + (int) $request->jumlah_masuk;

            // Update stok barang
            $barang->increment('stok_saat_ini', (int) $request->jumlah_masuk);

            // Catat mutasi
            MutasiStok::create([
                'nomor_transaksi'  => MutasiStok::buatNomor('SM'),
                'barang_id'        => $barang->id,
                'tanggal'          => $request->tanggal,
                'jenis_transaksi'  => 'stok_masuk',
                'jumlah_masuk'     => (int) $request->jumlah_masuk,
                'jumlah_keluar'    => 0,
                'saldo_sebelumnya' => $saldoSebelum,
                'saldo_setelahnya' => $saldoSesudah,
                'tipe_stok'        => 'gudang',
                'sumber_pasokan'   => $request->sumber_pasokan,
                'nomor_referensi'  => $request->nomor_referensi,
                'keterangan'       => $request->keterangan,
                'petugas_id'       => auth()->id(),
            ]);
        });

        $barang = BarangInventori::find($request->barang_id);
        return redirect()->route('stok-masuk.index')
            ->with('sukses', 'Stok masuk ' . $barang->nama_barang . ' sebesar ' . $request->jumlah_masuk . ' ' . $barang->satuan . ' berhasil dicatat. Stok sekarang: ' . $barang->fresh()->stok_saat_ini . ' ' . $barang->satuan . '.');
    }

    // ----------------------------------------------------------------
    // PENYESUAIAN STOK (koreksi manual oleh Admin)
    // ----------------------------------------------------------------

    public function penyesuaian(Request $request)
    {
        $request->validate([
            'barang_id'   => 'required|exists:barang_inventori,id',
            'tanggal'     => 'required|date',
            'stok_baru'   => 'required|integer|min:0',
            'keterangan'  => 'required|string|max:500',
        ], [
            'barang_id.required'  => 'Pilih barang.',
            'stok_baru.required'  => 'Stok baru wajib diisi.',
            'keterangan.required' => 'Keterangan penyesuaian wajib diisi.',
        ]);

        DB::transaction(function () use ($request) {
            $barang = BarangInventori::lockForUpdate()->findOrFail($request->barang_id);
            $saldoSebelum = $barang->stok_saat_ini;
            $stokBaru     = (int) $request->stok_baru;
            $selisih      = $stokBaru - $saldoSebelum;

            $barang->update(['stok_saat_ini' => $stokBaru]);

            MutasiStok::create([
                'nomor_transaksi'  => MutasiStok::buatNomor('PA'),
                'barang_id'        => $barang->id,
                'tanggal'          => $request->tanggal,
                'jenis_transaksi'  => 'penyesuaian',
                'jumlah_masuk'     => max(0, $selisih),
                'jumlah_keluar'    => max(0, -$selisih),
                'saldo_sebelumnya' => $saldoSebelum,
                'saldo_setelahnya' => $stokBaru,
                'tipe_stok'        => 'gudang',
                'keterangan'       => $request->keterangan,
                'petugas_id'       => auth()->id(),
            ]);
        });

        $barang = BarangInventori::find($request->barang_id);
        return redirect()->route('stok-masuk.index')
            ->with('sukses', 'Penyesuaian stok ' . $barang->nama_barang . ' berhasil. Stok disesuaikan menjadi ' . $request->stok_baru . ' ' . $barang->satuan . '.');
    }
}
