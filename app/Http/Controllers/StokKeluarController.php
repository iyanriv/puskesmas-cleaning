<?php

namespace App\Http\Controllers;

use App\Exports\BarangKeluarExport;
use App\Models\BarangInventori;
use App\Models\LaporanStok;
use App\Models\MutasiStok;
use App\Models\PemakaianStok;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StokKeluarController extends Controller
{
    // ================================================================
    // HALAMAN UTAMA: Laporan Barang Keluar & Distribusi
    // ================================================================

    public function index(Request $request)
    {
        $daftarBarang = BarangInventori::orderBy('nama_barang')->get();
        $daftarUnit   = BarangInventori::$daftarUnit;

        // Filter periode
        $periode = $request->get('periode', 'bulan_ini');
        if ($periode === 'hari_ini') {
            $filterDari   = today()->toDateString();
            $filterSampai = today()->toDateString();
        } elseif ($periode === 'minggu_ini') {
            $filterDari   = now()->startOfWeek()->toDateString();
            $filterSampai = now()->endOfWeek()->toDateString();
        } elseif ($periode === 'kustom' && $request->filled('dari') && $request->filled('sampai')) {
            $filterDari   = $request->dari;
            $filterSampai = $request->sampai;
        } else {
            // Default: bulan berjalan
            $periode      = 'bulan_ini';
            $filterDari   = $request->get('dari', now()->startOfMonth()->toDateString());
            $filterSampai = $request->get('sampai', now()->toDateString());
        }

        $filterBarangId = $request->get('barang_id');
        $filterUnit     = $request->get('unit');
        $filterCari     = $request->get('cari');

        // Query riwayat pengeluaran barang (jumlah_keluar > 0)
        $query = MutasiStok::with(['barang', 'petugas'])
            ->where('jumlah_keluar', '>', 0)
            ->whereBetween('tanggal', [$filterDari, $filterSampai])
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        if ($filterBarangId) {
            $query->where('barang_id', $filterBarangId);
        }

        if ($filterUnit) {
            $query->where('unit', $filterUnit);
        }

        if ($filterCari) {
            $cari = trim($filterCari);
            $query->where(function ($q) use ($cari) {
                $q->where('nomor_transaksi', 'like', "%{$cari}%")
                  ->orWhere('nomor_referensi', 'like', "%{$cari}%")
                  ->orWhere('keterangan', 'like', "%{$cari}%")
                  ->orWhere('unit', 'like', "%{$cari}%")
                  ->orWhereHas('barang', fn ($b) => $b->where('nama_barang', 'like', "%{$cari}%")->orWhere('kode_barang', 'like', "%{$cari}%"))
                  ->orWhereHas('petugas', fn ($u) => $u->where('name', 'like', "%{$cari}%"));
            });
        }

        $riwayatKeluar = $query->paginate(15)->withQueryString();

        // ── Statistik ringkas untuk periode yang difilter ─────────────
        $statsQuery = MutasiStok::where('jumlah_keluar', '>', 0)
            ->whereBetween('tanggal', [$filterDari, $filterSampai]);

        if ($filterBarangId) {
            $statsQuery->where('barang_id', $filterBarangId);
        }
        if ($filterUnit) {
            $statsQuery->where('unit', $filterUnit);
        }

        $totalKeluarQty        = (int) $statsQuery->sum('jumlah_keluar');
        $totalTransaksiKeluar  = (int) $statsQuery->count();
        $totalJenisBarangKeluar = (int) $statsQuery->distinct('barang_id')->count('barang_id');

        // Top 5 barang keluar di periode ini
        $topBarangKeluar = MutasiStok::select('barang_id', DB::raw('SUM(jumlah_keluar) as total_keluar'))
            ->with('barang')
            ->where('jumlah_keluar', '>', 0)
            ->whereBetween('tanggal', [$filterDari, $filterSampai])
            ->groupBy('barang_id')
            ->orderByDesc('total_keluar')
            ->take(5)
            ->get();

        return view('stok-keluar.index', compact(
            'daftarBarang',
            'daftarUnit',
            'riwayatKeluar',
            'periode',
            'filterDari',
            'filterSampai',
            'filterBarangId',
            'filterUnit',
            'filterCari',
            'totalKeluarQty',
            'totalTransaksiKeluar',
            'totalJenisBarangKeluar',
            'topBarangKeluar'
        ));
    }

    // ================================================================
    // CATAT BARANG KELUAR / DISTRIBUSI LANGSUNG
    // ================================================================

    public function store(Request $request)
    {
        $request->validate([
            'barang_id'       => 'required|exists:barang_inventori,id',
            'tanggal'         => 'required|date',
            'jumlah_keluar'   => 'required|integer|min:1',
            'unit'            => 'required|string|max:100',
            'penerima'        => 'nullable|string|max:150',
            'nomor_referensi' => 'nullable|string|max:100',
            'keterangan'      => 'nullable|string|max:500',
        ], [
            'barang_id.required'     => 'Pilih barang yang akan dikeluarkan.',
            'barang_id.exists'       => 'Barang tidak ditemukan.',
            'tanggal.required'       => 'Tanggal keluar wajib diisi.',
            'jumlah_keluar.required' => 'Jumlah barang keluar wajib diisi.',
            'jumlah_keluar.min'      => 'Jumlah barang keluar minimal 1.',
            'unit.required'          => 'Unit / Ruangan tujuan wajib dipilih.',
        ]);

        $barang = BarangInventori::findOrFail($request->barang_id);

        if ($barang->stok_saat_ini < $request->jumlah_keluar) {
            return back()->withInput()->with('gagal',
                'Stok tidak mencukupi! Stok saat ini: ' . $barang->stok_saat_ini . ' ' . $barang->satuan .
                ', diminta: ' . $request->jumlah_keluar . ' ' . $barang->satuan . '.'
            );
        }

        DB::transaction(function () use ($request, $barang) {
            $saldoSebelum = $barang->stok_saat_ini;
            $saldoSesudah = $saldoSebelum - (int) $request->jumlah_keluar;

            // Kurangi stok inventori
            $barang->decrement('stok_saat_ini', (int) $request->jumlah_keluar);

            $nomorTransaksi = MutasiStok::buatNomor('BK');
            $nomorRef = $request->nomor_referensi ?: $nomorTransaksi;

            $ket = '';
            if ($request->filled('penerima')) {
                $ket .= 'Penerima: ' . $request->penerima . '. ';
            }
            if ($request->filled('keterangan')) {
                $ket .= $request->keterangan;
            } else {
                $ket .= 'Distribusi langsung ke unit ' . $request->unit;
            }

            // Catat ke ledger mutasi stok
            $mutasi = MutasiStok::create([
                'nomor_transaksi'  => $nomorTransaksi,
                'barang_id'        => $barang->id,
                'tanggal'          => $request->tanggal,
                'jenis_transaksi'  => 'distribusi',
                'jumlah_masuk'     => 0,
                'jumlah_keluar'    => (int) $request->jumlah_keluar,
                'saldo_sebelumnya' => $saldoSebelum,
                'saldo_setelahnya' => $saldoSesudah,
                'tipe_stok'        => 'gudang',
                'unit'             => $request->unit,
                'nomor_referensi'  => $nomorRef,
                'keterangan'       => trim($ket),
                'petugas_id'       => auth()->id(),
            ]);

            // Sinkronkan ke rekap laporan stok bulanan & pemakaian unit
            $tgl = \Carbon\Carbon::parse($request->tanggal);
            $bulan = $tgl->month;
            $tahun = $tgl->year;
            $minggu = min(4, (int) ceil($tgl->day / 7));

            $laporan = LaporanStok::firstOrCreate(
                [
                    'barang_id'     => $barang->id,
                    'periode_bulan' => $bulan,
                    'periode_tahun' => $tahun,
                ],
                [
                    'produk_id'  => $barang->id,
                    'tanggal'    => $tgl->startOfMonth()->toDateString(),
                    'stok_masuk' => $saldoSebelum,
                ]
            );

            // Pemakaian unit
            $pemakaian = PemakaianStok::where([
                'laporan_stok_id' => $laporan->id,
                'barang_id'       => $barang->id,
                'unit'            => $request->unit,
                'minggu'          => $minggu,
            ])->first();

            if ($pemakaian) {
                $pemakaian->increment('jumlah', (int) $request->jumlah_keluar);
            } else {
                PemakaianStok::create([
                    'laporan_stok_id' => $laporan->id,
                    'barang_id'       => $barang->id,
                    'produk_id'       => $barang->id,
                    'unit'            => $request->unit,
                    'minggu'          => $minggu,
                    'jumlah'          => (int) $request->jumlah_keluar,
                ]);
            }
        });

        return redirect()->route('stok-keluar.index')
            ->with('sukses', 'Pengeluaran ' . $request->jumlah_keluar . ' ' . $barang->satuan . ' ' . $barang->nama_barang . ' ke ' . $request->unit . ' berhasil dicatat.');
    }

    // ================================================================
    // EXPORT EXCEL
    // ================================================================

    public function exportExcel(Request $request)
    {
        $dari     = $request->get('dari', now()->startOfMonth()->toDateString());
        $sampai   = $request->get('sampai', now()->toDateString());
        $barangId = $request->get('barang_id');
        $unit     = $request->get('unit');

        $namaFile = 'Laporan-Barang-Keluar-' . $dari . '-sd-' . $sampai . '.xlsx';

        return Excel::download(
            new BarangKeluarExport($dari, $sampai, $barangId, $unit),
            $namaFile
        );
    }

    // ================================================================
    // CETAK REKAP LAPORAN PDF
    // ================================================================

    public function cetakPdf(Request $request)
    {
        $dari     = $request->get('dari', now()->startOfMonth()->toDateString());
        $sampai   = $request->get('sampai', now()->toDateString());
        $barangId = $request->get('barang_id');
        $unit     = $request->get('unit');

        $query = MutasiStok::with(['barang', 'petugas'])
            ->where('jumlah_keluar', '>', 0)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderBy('tanggal')
            ->orderBy('id');

        if ($barangId) {
            $query->where('barang_id', $barangId);
        }
        if ($unit) {
            $query->where('unit', $unit);
        }

        $daftarKeluar = $query->get();
        $totalUnitKeluar = $daftarKeluar->sum('jumlah_keluar');

        $pdf = Pdf::loadView('stok-keluar.cetak-pdf', compact(
            'daftarKeluar',
            'dari',
            'sampai',
            'totalUnitKeluar'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan-Barang-Keluar-' . $dari . '-sd-' . $sampai . '.pdf');
    }

    // ================================================================
    // BUKTI PENGELUARAN BARANG / SURAT JALAN PDF
    // ================================================================

    public function showBukti($id)
    {
        $mutasi = MutasiStok::with(['barang', 'petugas'])->findOrFail($id);

        $pdf = Pdf::loadView('stok-keluar.bukti-pdf', compact('mutasi'))
            ->setPaper('a5', 'landscape');

        $namaFile = 'Bukti-Pengeluaran-' . $mutasi->nomor_transaksi . '.pdf';

        return $pdf->stream($namaFile);
    }
}
