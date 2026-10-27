<?php

namespace App\Http\Controllers;

use App\Exports\LaporanStokExport;
use App\Models\BarangInventori;
use App\Models\LaporanStok;
use App\Models\PemakaianStok;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StokBarangController extends Controller
{
    // ---------------------------------------------------------------
    // HALAMAN UTAMA — tampilkan laporan berdasarkan periode + filter unit
    // ---------------------------------------------------------------

    public function index(Request $request)
    {
        $bulan      = (int) $request->get('bulan', now()->month);
        $tahun      = (int) $request->get('tahun', now()->year);
        $filterUnit = $request->get('unit', '');   // unit/lokasi yang dipilih (kosong = semua)
        $filterMinggu = (int) $request->get('minggu', 0); // 0 = semua minggu

        $daftarUnit   = BarangInventori::$daftarUnit;
        $daftarSatuan = BarangInventori::$daftarSatuan;
        $daftarBulan  = $this->namaBulan();
        $daftarTahun  = range(now()->year - 3, now()->year + 1);

        // ── Query laporan stok periode ini ──────────────────────────
        $query = LaporanStok::with(['barang', 'pemakaian'])
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun);

        // Jika filter unit dipilih, hanya tampilkan produk yang punya
        // pemakaian di unit tersebut (atau tampilkan semua jika tidak ada filter)
        if ($filterUnit && in_array($filterUnit, $daftarUnit)) {
            $query->whereHas('pemakaian', function ($q) use ($filterUnit) {
                $q->where('unit', $filterUnit)->where('jumlah', '>', 0);
            });
        }

        $laporan = $query->orderBy('id')->get();

        // ── Bangun baris tabel ringkas ───────────────────────────────
        $barisTabel = collect();

        foreach ($laporan as $item) {
            $pemakaianGrid = $item->pemakaianPerUnitMinggu(); // [unit][minggu] = jumlah
            $perUnit       = $item->pemakaianPerUnit();       // [unit] = total
            $totalPemakaian = $item->totalPemakaian();
            $sisaStok      = $item->sisaStok();

            $namaBarang  = $item->barang?->nama_barang ?? 'Barang';
            $kodeBarang  = $item->barang?->kode_barang ?? '-';
            $satuanBarang = $item->barang?->satuan ?? '-';

            if ($filterUnit && in_array($filterUnit, $daftarUnit)) {
                // Mode filter: tampilkan baris per minggu untuk unit yang dipilih
                $unitList = [$filterUnit];
                $mingguList = $filterMinggu ? [$filterMinggu] : [1, 2, 3, 4];

                foreach ($unitList as $unit) {
                    foreach ($mingguList as $minggu) {
                        $jumlah = $pemakaianGrid[$unit][$minggu] ?? 0;
                        if ($jumlah > 0 || $filterMinggu) {
                            $barisTabel->push([
                                'laporan_id'     => $item->id,
                                'nama_barang'    => $namaBarang,
                                'kode_barang'    => $kodeBarang,
                                'satuan'         => $satuanBarang,
                                'tanggal'        => $item->tanggal,
                                'stok_masuk'     => $item->stok_masuk,
                                'unit'           => $unit,
                                'minggu'         => 'Week ' . $minggu,
                                'minggu_int'     => $minggu,
                                'jumlah'         => $jumlah,
                                'total_pemakaian'=> $totalPemakaian,
                                'sisa_stok'      => $sisaStok,
                                'produk_id'      => $item->barang_id,
                                'barang_id'      => $item->barang_id,
                                'produk_nama'    => $namaBarang,
                                'produk_kode'    => $kodeBarang,
                                'produk_satuan'  => $satuanBarang,
                                'tanggal_raw'    => $item->tanggal ? $item->tanggal->format('Y-m-d') : '',
                                'stok_masuk_raw' => $item->stok_masuk,
                            ]);
                        }
                    }
                }
            } else {
                // Mode tanpa filter: satu baris per produk dengan rekap per unit
                $barisTabel->push([
                    'laporan_id'      => $item->id,
                    'nama_barang'     => $namaBarang,
                    'kode_barang'     => $kodeBarang,
                    'satuan'          => $satuanBarang,
                    'tanggal'         => $item->tanggal,
                    'stok_masuk'      => $item->stok_masuk,
                    'pemakaian_unit'  => $perUnit,
                    'total_pemakaian' => $totalPemakaian,
                    'sisa_stok'       => $sisaStok,
                    'produk_id'       => $item->barang_id,
                    'barang_id'       => $item->barang_id,
                    'produk_nama'     => $namaBarang,
                    'produk_kode'     => $kodeBarang,
                    'produk_satuan'   => $satuanBarang,
                    'tanggal_raw'     => $item->tanggal ? $item->tanggal->format('Y-m-d') : '',
                    'stok_masuk_raw'  => $item->stok_masuk,
                ]);
            }
        }

        // Ambil semua barang inventori untuk modal tambah barang ke periode
        $semuaBarang = BarangInventori::orderBy('nama_barang')->get();
        $semuaProduk = $semuaBarang; // alias untuk view

        return view('stok-barang.index', compact(
            'laporan',
            'barisTabel',
            'semuaBarang',
            'semuaProduk',
            'bulan',
            'tahun',
            'filterUnit',
            'filterMinggu',
            'daftarUnit',
            'daftarSatuan',
            'daftarBulan',
            'daftarTahun'
        ));
    }

    // ---------------------------------------------------------------
    // SIMPAN BARANG BARU + LAPORAN STOK PERIODE
    // ---------------------------------------------------------------

    public function simpanBarang(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $request->validate([
            'nama_barang' => 'required|string|max:150',
            'kode_barang' => 'required|string|max:30|unique:barang_inventori,kode_barang',
            'satuan'      => 'required|in:' . implode(',', BarangInventori::$daftarSatuan),
            'tanggal'     => 'nullable|date',
            'stok_masuk'  => 'required|integer|min:0',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique'   => 'Kode barang sudah digunakan oleh barang lain.',
            'satuan.required'      => 'Satuan wajib dipilih.',
            'satuan.in'            => 'Satuan tidak valid.',
            'stok_masuk.required'  => 'Jumlah stok/masuk wajib diisi.',
            'stok_masuk.min'       => 'Stok tidak boleh negatif.',
        ]);

        DB::transaction(function () use ($request, $bulan, $tahun) {
            $barang = BarangInventori::create([
                'kode_barang'   => $request->kode_barang,
                'nama_barang'   => $request->nama_barang,
                'satuan'        => $request->satuan,
                'stok_saat_ini' => $request->stok_masuk,
                'stok_minimum'  => 5,
            ]);

            // Buat laporan stok untuk periode ini
            LaporanStok::create([
                'barang_id'     => $barang->id,
                'periode_bulan' => $bulan,
                'periode_tahun' => $tahun,
                'tanggal'       => $request->tanggal ?: now()->toDateString(),
                'stok_masuk'    => $request->stok_masuk,
            ]);
        });

        return redirect()->route('stok-barang.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('sukses', 'Barang ' . $request->nama_barang . ' berhasil ditambahkan.');
    }

    // ---------------------------------------------------------------
    // TAMBAH BARANG YANG SUDAH ADA KE PERIODE BARU
    // ---------------------------------------------------------------

    public function tambahKePeriode(Request $request)
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);
        $barangId = $request->get('barang_id', $request->get('produk_id'));

        $request->merge(['barang_id' => $barangId]);

        $request->validate([
            'barang_id'  => 'required|exists:barang_inventori,id',
            'tanggal'    => 'nullable|date',
            'stok_masuk' => 'required|integer|min:0',
        ], [
            'barang_id.required'  => 'Pilih barang yang akan ditambahkan.',
            'barang_id.exists'    => 'Barang tidak ditemukan.',
            'stok_masuk.required' => 'Jumlah stok wajib diisi.',
            'stok_masuk.min'      => 'Stok tidak boleh negatif.',
        ]);

        $sudahAda = LaporanStok::where('barang_id', $barangId)
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->exists();

        if ($sudahAda) {
            $barang = BarangInventori::find($barangId);
            return back()->with('gagal', $barang->nama_barang . ' sudah ada dalam laporan periode ini.');
        }

        LaporanStok::create([
            'barang_id'     => $barangId,
            'periode_bulan' => $bulan,
            'periode_tahun' => $tahun,
            'tanggal'       => $request->tanggal ?: now()->toDateString(),
            'stok_masuk'    => $request->stok_masuk,
        ]);

        $barang = BarangInventori::find($barangId);
        return redirect()->route('stok-barang.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('sukses', $barang->nama_barang . ' berhasil ditambahkan ke periode ini.');
    }

    // ---------------------------------------------------------------
    // UPDATE STOK MASUK + INFO BARANG
    // ---------------------------------------------------------------

    public function updateLaporan(Request $request, $id)
    {
        $laporan = LaporanStok::with('barang')->findOrFail($id);

        $request->validate([
            'nama_barang' => 'required|string|max:150',
            'kode_barang' => 'required|string|max:30|unique:barang_inventori,kode_barang,' . $laporan->barang_id,
            'satuan'      => 'required|in:' . implode(',', BarangInventori::$daftarSatuan),
            'tanggal'     => 'nullable|date',
            'stok_masuk'  => 'required|integer|min:0',
        ], [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'kode_barang.required' => 'Kode barang wajib diisi.',
            'kode_barang.unique'   => 'Kode barang sudah digunakan oleh barang lain.',
            'satuan.required'      => 'Satuan wajib dipilih.',
            'stok_masuk.required'  => 'Jumlah stok wajib diisi.',
            'stok_masuk.min'       => 'Stok tidak boleh negatif.',
        ]);

        // Validasi: stok tidak boleh kurang dari total pemakaian
        $laporan->load('pemakaian');
        $totalPemakaian = $laporan->totalPemakaian();
        if ($request->stok_masuk < $totalPemakaian) {
            return back()->withInput()
                ->with('gagal', 'Stok tidak boleh kurang dari total pemakaian (' . $totalPemakaian . '). Kurangi pemakaian terlebih dahulu.');
        }

        DB::transaction(function () use ($request, $laporan) {
            if ($laporan->barang) {
                $laporan->barang->update([
                    'kode_barang' => $request->kode_barang,
                    'nama_barang' => $request->nama_barang,
                    'satuan'      => $request->satuan,
                ]);
            }

            $laporan->update([
                'tanggal'    => $request->tanggal ?: $laporan->tanggal,
                'stok_masuk' => $request->stok_masuk,
            ]);
        });

        return redirect()->route('stok-barang.index', [
            'bulan' => $laporan->periode_bulan,
            'tahun' => $laporan->periode_tahun,
        ])->with('sukses', 'Data ' . $request->nama_barang . ' berhasil diperbarui.');
    }

    // ---------------------------------------------------------------
    // UPDATE PEMAKAIAN (AJAX — dipanggil realtime dari browser)
    // ---------------------------------------------------------------

    public function updatePemakaian(Request $request, $laporanId)
    {
        $laporan = LaporanStok::with('pemakaian')->findOrFail($laporanId);

        $request->validate([
            'unit'   => 'required|in:' . implode(',', BarangInventori::$daftarUnit),
            'minggu' => 'required|integer|in:1,2,3,4',
            'jumlah' => 'required|integer|min:0',
        ], [
            'unit.required'   => 'Unit wajib diisi.',
            'unit.in'         => 'Unit tidak valid.',
            'minggu.required' => 'Minggu wajib diisi.',
            'minggu.in'       => 'Minggu harus antara 1-4.',
            'jumlah.min'      => 'Jumlah tidak boleh negatif.',
        ]);

        // Hitung total pemakaian baru jika nilai ini diubah
        $pemakaianLain = $laporan->pemakaian
            ->where('unit', '!=', $request->unit)
            ->sum('jumlah')
            + $laporan->pemakaian
            ->where('unit', $request->unit)
            ->where('minggu', '!=', (int) $request->minggu)
            ->sum('jumlah');

        $totalBaru = $pemakaianLain + (int) $request->jumlah;

        if ($totalBaru > $laporan->stok_masuk) {
            return response()->json([
                'status'  => 'peringatan',
                'pesan'   => 'Pemakaian melebihi stok tersedia (' . $laporan->stok_masuk . '). Sisa stok akan menjadi negatif.',
                'total_pemakaian' => $totalBaru,
                'sisa_stok'       => $laporan->stok_masuk - $totalBaru,
            ], 422);
        }

        // Upsert: buat atau update record pemakaian
        PemakaianStok::updateOrCreate(
            [
                'laporan_stok_id' => $laporan->id,
                'barang_id'       => $laporan->barang_id,
                'unit'            => $request->unit,
                'minggu'          => $request->minggu,
            ],
            ['jumlah' => $request->jumlah]
        );

        // Hitung ulang totals
        $laporan->load('pemakaian');
        $totalPemakaian = $laporan->totalPemakaian();
        $sisaStok       = $laporan->sisaStok();
        $perUnit        = $laporan->pemakaianPerUnit();

        // ── Sinkronisasi stok_saat_ini di barang_inventori ──────────
        BarangInventori::where('id', $laporan->barang_id)
            ->update(['stok_saat_ini' => max(0, $sisaStok)]);

        return response()->json([
            'status'          => 'sukses',
            'total_pemakaian' => $totalPemakaian,
            'sisa_stok'       => $sisaStok,
            'per_unit'        => $perUnit,
            'total_rekap'     => array_sum($perUnit),
        ]);
    }

    // ---------------------------------------------------------------
    // API: AMBIL DATA PEMAKAIAN TERSIMPAN (untuk modal input)
    // ---------------------------------------------------------------

    public function dataPemakaian($laporanId)
    {
        $laporan = LaporanStok::with('pemakaian')->findOrFail($laporanId);
        $grid    = $laporan->pemakaianPerUnitMinggu();

        return response()->json($grid);
    }

    // ---------------------------------------------------------------
    // HAPUS BARANG DARI LAPORAN PERIODE
    // ---------------------------------------------------------------

    public function hapusLaporan($id)
    {
        $laporan = LaporanStok::with('barang')->findOrFail($id);
        $namaBarang = $laporan->barang?->nama_barang ?? 'Barang';
        $bulan = $laporan->periode_bulan;
        $tahun = $laporan->periode_tahun;

        // Hapus pemakaian dulu
        $laporan->pemakaian()->delete();
        $laporan->delete();

        return redirect()->route('stok-barang.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('sukses', $namaBarang . ' berhasil dihapus dari laporan periode ini.');
    }

    // ---------------------------------------------------------------
    // EXPORT EXCEL
    // ---------------------------------------------------------------

    public function exportExcel(Request $request)
    {
        $bulan        = (int) $request->get('bulan', now()->month);
        $tahun        = (int) $request->get('tahun', now()->year);
        $filterUnit   = $request->get('unit', '');
        $filterMinggu = (int) $request->get('minggu', 0);

        $suffix = '';
        if ($filterUnit && in_array($filterUnit, BarangInventori::$daftarUnit)) {
            $suffix .= '_' . str_replace(' ', '_', $filterUnit);
            if ($filterMinggu) {
                $suffix .= '_Wk' . $filterMinggu;
            }
        }

        $namaFile = 'Laporan_Stok_' . $this->namaBulan()[$bulan] . '_' . $tahun . $suffix . '.xlsx';

        return Excel::download(new LaporanStokExport($bulan, $tahun, $filterUnit, $filterMinggu), $namaFile);
    }

    // ---------------------------------------------------------------
    // CETAK PDF
    // ---------------------------------------------------------------

    public function cetakPdf(Request $request)
    {
        $bulan        = (int) $request->get('bulan', now()->month);
        $tahun        = (int) $request->get('tahun', now()->year);
        $filterUnit   = $request->get('unit', '');
        $filterMinggu = (int) $request->get('minggu', 0);

        $daftarUnit = BarangInventori::$daftarUnit;
        $namaBulan  = $this->namaBulan()[$bulan];

        $query = LaporanStok::with(['barang', 'pemakaian'])
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun);

        $isFilteredUnit = $filterUnit && in_array($filterUnit, $daftarUnit);

        if ($isFilteredUnit) {
            $query->whereHas('pemakaian', function ($q) use ($filterUnit, $filterMinggu) {
                $q->where('unit', $filterUnit)->where('jumlah', '>', 0);
                if ($filterMinggu) {
                    $q->where('minggu', $filterMinggu);
                }
            });
        }

        $laporanData = $query->first();
        
        $queryDaftar = LaporanStok::with(['barang', 'pemakaian'])
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun);

        if ($isFilteredUnit) {
            $queryDaftar->whereHas('pemakaian', function ($q) use ($filterUnit, $filterMinggu) {
                $q->where('unit', $filterUnit)->where('jumlah', '>', 0);
                if ($filterMinggu) {
                    $q->where('minggu', $filterMinggu);
                }
            });
        }

        $daftarBarang = $queryDaftar->orderBy('id')->get();

        $unitList   = $isFilteredUnit ? [$filterUnit] : $daftarUnit;
        $mingguList = ($isFilteredUnit && $filterMinggu) ? [$filterMinggu] : [1, 2, 3, 4];

        $judulLaporan = 'Laporan Stok Barang Kebersihan ' . $namaBulan . ' ' . $tahun;
        if ($isFilteredUnit) {
            $judulLaporan .= ' - ' . $filterUnit;
            if ($filterMinggu) {
                $judulLaporan .= ' (Minggu ke-' . $filterMinggu . ')';
            }
        }

        $laporan = $laporanData;
        return view('stok-barang.cetak-pdf-formal', compact(
            'laporan',
            'daftarBarang',
            'daftarUnit',
            'namaBulan',
            'tahun',
            'judulLaporan',
            'bulan',
            'filterUnit',
            'filterMinggu',
            'unitList',
            'mingguList',
            'isFilteredUnit'
        ));
    }

    // ---------------------------------------------------------------
    // HELPER
    // ---------------------------------------------------------------

    private function namaBulan(): array
    {
        return [
            1  => 'Januari',
            2  => 'Februari',
            3  => 'Maret',
            4  => 'April',
            5  => 'Mei',
            6  => 'Juni',
            7  => 'Juli',
            8  => 'Agustus',
            9  => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
    }

    // ---------------------------------------------------------------
    // API: GENERATE KODE OTOMATIS
    // ---------------------------------------------------------------

    public function kodeOtomatis()
    {
        return response()->json([
            'kode' => BarangInventori::buatKodeOtomatis(),
        ]);
    }
}
