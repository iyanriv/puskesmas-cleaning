<?php

namespace App\Http\Controllers;

use App\Exports\PermintaanBarangExport;
use App\Models\Area;
use App\Models\BarangInventori;
use App\Models\LaporanStok;
use App\Models\MutasiStok;
use App\Models\PemakaianStok;
use App\Models\PermintaanBarang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class PermintaanBarangController extends Controller
{
    // ── Kunci session keranjang per-user ────────────────────────────
    private function kunciKeranjang(): string
    {
        return 'keranjang_' . auth()->id();
    }

    // ── Kunci session area per-user ──────────────────────────────────
    private function kunciArea(): string
    {
        return 'keranjang_area_' . auth()->id();
    }

    // ── Ambil area dari session ──────────────────────────────────────
    private function ambilAreaSesi(): ?int
    {
        return session($this->kunciArea());
    }

    // ── Simpan area ke session ───────────────────────────────────────
    private function simpanAreaSesi(?int $areaId): void
    {
        if ($areaId) {
            session([$this->kunciArea() => $areaId]);
        } else {
            session()->forget($this->kunciArea());
        }
    }

    // ── Ambil keranjang dari session ────────────────────────────────
    private function ambilKeranjang(): array
    {
        return session($this->kunciKeranjang(), []);
    }

    // ── Simpan keranjang ke session ─────────────────────────────────
    private function simpanKeranjang(array $keranjang): void
    {
        session([$this->kunciKeranjang() => $keranjang]);
    }

    // ── Hitung total item (jumlah jenis barang) di keranjang ────────
    private function totalItemKeranjang(): int
    {
        return count($this->ambilKeranjang());
    }

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

        // Riwayat permintaan milik CS ini (20 terakhir)
        $riwayatPermintaan = PermintaanBarang::with('barang')
            ->where('user_id', $pengguna->id)
            ->latest()
            ->take(20)
            ->get();

        $keranjang       = $this->ambilKeranjang();
        $jumlahKeranjang = count($keranjang);

        return view('barang.katalog', compact(
            'daftarBarang',
            'riwayatPermintaan',
            'pengguna',
            'keranjang',
            'jumlahKeranjang',
        ));
    }

    /**
     * Proses pengajuan permintaan barang dari CS.
     * (Fallback — satu barang langsung, tanpa keranjang)
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

        // Cek apakah ada permintaan aktif untuk barang yang sama
        $sudahAda = PermintaanBarang::where('user_id', $pengguna->id)
            ->where('barang_id', $request->barang_id)
            ->whereIn('status_request', ['menunggu_spv', 'disetujui_spv'])
            ->exists();

        if ($sudahAda) {
            return back()->with('gagal', 'Anda sudah memiliki permintaan ' . $barang->nama_barang . ' yang masih menunggu persetujuan.');
        }

        PermintaanBarang::create([
            'user_id'        => $pengguna->id,
            'barang_id'      => $request->barang_id,
            'jumlah'         => $request->jumlah,
            'status_request' => 'menunggu_spv',
            'waktu_request'  => now(),
        ]);

        return back()->with('sukses', 'Permintaan ' . $barang->nama_barang . ' sebanyak ' . $request->jumlah . ' ' . $barang->satuan . ' berhasil dikirim dan menunggu persetujuan supervisor!');
    }

    // ================================================================
    // KERANJANG BELANJA (Session-based)
    // ================================================================

    /**
     * Tambahkan satu barang ke keranjang session.
     * Jika barang sudah ada di keranjang, jumlahnya di-update.
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang_inventori,id',
            'jumlah'    => 'required|integer|min:1|max:999',
        ]);

        $barang = BarangInventori::findOrFail($request->barang_id);

        // Validasi stok mencukupi
        if ($barang->stok_saat_ini == 0) {
            return back()->with('gagal', $barang->nama_barang . ' stok habis, tidak bisa ditambahkan.');
        }

        $jumlah = min((int) $request->jumlah, $barang->stok_saat_ini);

        // Cek apakah sudah ada permintaan aktif untuk barang ini
        $sudahPending = PermintaanBarang::where('user_id', auth()->id())
            ->where('barang_id', $barang->id)
            ->whereIn('status_request', ['menunggu_spv', 'disetujui_spv'])
            ->exists();

        if ($sudahPending) {
            return back()->with('gagal', $barang->nama_barang . ' sudah ada permintaan yang masih menunggu persetujuan.');
        }

        $keranjang = $this->ambilKeranjang();

        // Jika barang sudah di keranjang, update jumlahnya
        if (isset($keranjang[$barang->id])) {
            $jumlahBaru = min($keranjang[$barang->id]['jumlah'] + $jumlah, $barang->stok_saat_ini);
            $keranjang[$barang->id]['jumlah'] = $jumlahBaru;
            $pesan = $barang->nama_barang . ' diperbarui menjadi ' . $jumlahBaru . ' ' . $barang->satuan . ' di keranjang.';
        } else {
            $keranjang[$barang->id] = [
                'barang_id'   => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'satuan'      => $barang->satuan,
                'stok_tersedia' => $barang->stok_saat_ini,
                'jumlah'      => $jumlah,
                'foto'        => $barang->foto_barang,
            ];
            $pesan = $barang->nama_barang . ' (' . $jumlah . ' ' . $barang->satuan . ') ditambahkan ke keranjang.';
        }

        $this->simpanKeranjang($keranjang);

        return back()->with('sukses_keranjang', $pesan);
    }

    /**
     * Tampilkan halaman keranjang — review semua item sebelum kirim.
     */
    public function viewCart()
    {
        $pengguna     = auth()->user();
        $keranjang    = $this->ambilKeranjang();
        $areaIdSesi   = $this->ambilAreaSesi();
        $daftarArea   = Area::orderBy('lantai')->get();
        $areaTerpilih = $areaIdSesi ? Area::find($areaIdSesi) : null;

        // Segar-kan data stok dari DB agar tidak stale
        $idBarang = array_keys($keranjang);
        $barangDb = BarangInventori::whereIn('id', $idBarang)->get()->keyBy('id');

        foreach ($keranjang as $id => &$item) {
            if (isset($barangDb[$id])) {
                $b = $barangDb[$id];
                $item['stok_tersedia'] = $b->stok_saat_ini;
                $item['nama_barang']   = $b->nama_barang;
                $item['satuan']        = $b->satuan;
                $item['foto']          = $b->foto_barang;
                if ($item['jumlah'] > $b->stok_saat_ini) {
                    $item['jumlah'] = max(1, $b->stok_saat_ini);
                }
            } else {
                unset($keranjang[$id]);
            }
        }
        unset($item);
        $this->simpanKeranjang($keranjang);

        return view('barang.keranjang', compact(
            'keranjang', 'pengguna',
            'daftarArea', 'areaIdSesi', 'areaTerpilih'
        ));
    }

    /**
     * Simpan atau ubah area/lantai yang dipilih di keranjang (POST).
     */
    public function pilihArea(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:area,id',
        ], [
            'area_id.required' => 'Pilih area/lantai tempat bertugas terlebih dahulu.',
            'area_id.exists'   => 'Area tidak valid.',
        ]);

        $this->simpanAreaSesi((int) $request->area_id);

        return back()->with('sukses_area', 'Area berhasil dipilih.');
    }

    /**
     * Update jumlah satu item di keranjang.
     */
    public function updateCartItem(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|integer',
            'jumlah'    => 'required|integer|min:1|max:999',
        ]);

        $keranjang = $this->ambilKeranjang();
        $id        = $request->barang_id;

        if (! isset($keranjang[$id])) {
            return back()->with('gagal', 'Item tidak ditemukan di keranjang.');
        }

        $barang = BarangInventori::find($id);
        if (! $barang) {
            unset($keranjang[$id]);
            $this->simpanKeranjang($keranjang);
            return back()->with('gagal', 'Barang tidak lagi tersedia.');
        }

        $jumlahBaru = min((int) $request->jumlah, $barang->stok_saat_ini);
        $keranjang[$id]['jumlah']         = $jumlahBaru;
        $keranjang[$id]['stok_tersedia']  = $barang->stok_saat_ini;

        $this->simpanKeranjang($keranjang);

        return back()->with('sukses', 'Jumlah ' . $keranjang[$id]['nama_barang'] . ' diperbarui menjadi ' . $jumlahBaru . '.');
    }

    /**
     * Hapus satu item dari keranjang.
     */
    public function removeCartItem(Request $request)
    {
        $request->validate(['barang_id' => 'required|integer']);

        $keranjang = $this->ambilKeranjang();
        $id        = $request->barang_id;

        $nama = $keranjang[$id]['nama_barang'] ?? 'Barang';
        unset($keranjang[$id]);
        $this->simpanKeranjang($keranjang);

        return back()->with('sukses', $nama . ' dihapus dari keranjang.');
    }

    /**
     * Kosongkan seluruh isi keranjang.
     */
    public function clearCart()
    {
        $this->simpanKeranjang([]);
        $this->simpanAreaSesi(null);
        return redirect()->route('barang.katalog')
            ->with('sukses', 'Keranjang berhasil dikosongkan.');
    }

    /**
     * Kirim semua item di keranjang sebagai permintaan barang.
     * Setiap item menjadi satu record di tabel permintaan_barang.
     */
    public function submitCart()
    {
        $pengguna  = auth()->user();
        $keranjang = $this->ambilKeranjang();
        $areaId    = $this->ambilAreaSesi();

        if (empty($keranjang)) {
            return redirect()->route('barang.katalog')
                ->with('gagal', 'Keranjang kosong. Tambahkan barang terlebih dahulu.');
        }

        // Wajib pilih area sebelum kirim
        if (! $areaId) {
            return redirect()->route('barang.keranjang')
                ->with('gagal', 'Pilih area/lantai tempat bertugas sebelum mengirim permintaan.');
        }

        $berhasil = 0;
        $gagal    = [];
        $kodeTransaksi = PermintaanBarang::buatKodeTransaksi();

        DB::transaction(function () use ($pengguna, $keranjang, $kodeTransaksi, $areaId, &$berhasil, &$gagal) {
            foreach ($keranjang as $id => $item) {
                $barang = BarangInventori::find($id);

                if (! $barang) {
                    $gagal[] = ($item['nama_barang'] ?? 'Barang') . ': tidak ditemukan';
                    continue;
                }

                if ($barang->stok_saat_ini == 0) {
                    $gagal[] = $barang->nama_barang . ': stok habis';
                    continue;
                }

                $sudahAda = PermintaanBarang::where('user_id', $pengguna->id)
                    ->where('barang_id', $id)
                    ->whereIn('status_request', ['menunggu_spv', 'disetujui_spv'])
                    ->exists();

                if ($sudahAda) {
                    $gagal[] = $barang->nama_barang . ': sudah ada permintaan yang sedang diproses';
                    continue;
                }

                $jumlah = min((int) $item['jumlah'], $barang->stok_saat_ini);

                PermintaanBarang::create([
                    'kode_transaksi' => $kodeTransaksi,
                    'user_id'        => $pengguna->id,
                    'barang_id'      => $id,
                    'area_id'        => $areaId,
                    'jumlah'         => $jumlah,
                    'status_request' => 'menunggu_spv',
                    'waktu_request'  => now(),
                ]);

                $berhasil++;
            }
        });

        // Kosongkan keranjang + area setelah berhasil submit
        $this->simpanKeranjang([]);
        $this->simpanAreaSesi(null);

        if ($berhasil > 0 && empty($gagal)) {
            return redirect()->route('barang.katalog')
                ->with('sukses', $berhasil . ' permintaan barang berhasil dikirim dan menunggu persetujuan supervisor!');
        }

        if ($berhasil > 0 && ! empty($gagal)) {
            $pesanGagal = implode('; ', $gagal);
            return redirect()->route('barang.katalog')
                ->with('sukses', $berhasil . ' permintaan berhasil dikirim.')
                ->with('gagal', 'Sebagian gagal: ' . $pesanGagal);
        }

        return redirect()->route('barang.katalog')
            ->with('gagal', 'Semua permintaan gagal dikirim: ' . implode('; ', $gagal));
    }

    // ================================================================
    // SISI SUPERVISOR: Persetujuan tahap pertama
    // ================================================================

    /**
     * Halaman antrian permintaan yang perlu disetujui supervisor.
     */
    public function daftarSupervisor(Request $request)
    {
        // Permintaan yang menunggu persetujuan supervisor
        $permintaanMenunggu = PermintaanBarang::with(['pengguna', 'barang'])
            ->where('status_request', 'menunggu_spv')
            ->latest('waktu_request')
            ->get();

        // Riwayat yang sudah diproses supervisor (dengan filter)
        $queryDiproses = PermintaanBarang::with(['pengguna', 'barang', 'supervisor'])
            ->whereIn('status_request', ['disetujui_spv', 'ditolak_spv', 'disetujui', 'ditolak']);

        $periode = $request->get('periode', 'bulan_ini');
        if ($periode === 'hari_ini') {
            $queryDiproses->whereDate('waktu_approve_spv', today());
        } elseif ($periode === 'minggu_ini') {
            $queryDiproses->whereBetween('waktu_approve_spv', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($periode === 'bulan_ini') {
            $queryDiproses->whereMonth('waktu_approve_spv', now()->month)
                          ->whereYear('waktu_approve_spv', now()->year);
        } elseif ($periode === 'kustom' && $request->filled('dari') && $request->filled('sampai')) {
            $queryDiproses->whereBetween(\DB::raw('DATE(waktu_approve_spv)'), [$request->dari, $request->sampai]);
        }

        if ($request->filled('cari')) {
            $cari = trim($request->cari);
            $queryDiproses->where(function ($q) use ($cari) {
                $q->whereHas('barang', fn ($b) => $b->where('nama_barang', 'like', "%{$cari}%"))
                  ->orWhereHas('pengguna', fn ($u) => $u->where('name', 'like', "%{$cari}%"))
                  ->orWhere('kode_transaksi', 'like', "%{$cari}%");
            });
        }

        $sudahDiproses = $queryDiproses->latest('waktu_approve_spv')
            ->paginate(15)
            ->withQueryString();

        return view('barang.supervisor-daftar', compact(
            'permintaanMenunggu',
            'sudahDiproses',
            'periode'
        ));
    }

    /**
     * Supervisor menyetujui permintaan → diteruskan ke gudang.
     */
    public function setujuiSupervisor($id)
    {
        $permintaan = PermintaanBarang::with(['barang', 'pengguna'])->findOrFail($id);

        if ($permintaan->status_request !== 'menunggu_spv') {
            return back()->with('info', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $permintaan->update([
            'status_request'    => 'disetujui_spv',
            'supervisor_id'     => auth()->id(),
            'waktu_approve_spv' => now(),
        ]);

        $namaPengguna = $permintaan->pengguna?->name ?? 'Petugas';
        $namaBarang   = $permintaan->barang?->nama_barang ?? 'Barang';

        return back()->with('sukses',
            'Permintaan ' . $namaPengguna . ' untuk ' . $namaBarang
            . ' telah disetujui dan diteruskan ke gudang.'
        );
    }

    /**
     * Supervisor menolak permintaan.
     */
    public function tolakSupervisor(Request $request, $id)
    {
        $request->validate([
            'catatan_spv' => 'required|string|max:500',
        ], [
            'catatan_spv.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $permintaan = PermintaanBarang::with(['barang', 'pengguna'])->findOrFail($id);

        if ($permintaan->status_request !== 'menunggu_spv') {
            return back()->with('info', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $permintaan->update([
            'status_request'    => 'ditolak_spv',
            'supervisor_id'     => auth()->id(),
            'catatan_spv'       => $request->catatan_spv,
            'waktu_approve_spv' => now(),
        ]);

        $namaPengguna = $permintaan->pengguna?->name ?? 'Petugas';
        return back()->with('sukses', 'Permintaan dari ' . $namaPengguna . ' telah ditolak.');
    }

    // ================================================================
    // SISI GUDANG: Kelola permintaan
    // ================================================================

    /**
     * Tampilkan daftar permintaan & laporan barang keluar untuk Petugas Gudang & Admin.
     */
    public function daftarGudang(Request $request)
    {
        // Menunggu proses gudang — sudah disetujui supervisor
        $permintaanPending = PermintaanBarang::with(['pengguna', 'barang', 'supervisor', 'area'])
            ->where('status_request', 'disetujui_spv')
            ->latest('waktu_approve_spv')
            ->get();

        // Riwayat barang keluar / diproses (dengan filter periode dan pencarian)
        $queryDiproses = PermintaanBarang::with(['pengguna', 'barang', 'area'])
            ->whereIn('status_request', ['disetujui', 'ditolak']);

        // Filter status
        if ($request->filled('status') && $request->status !== 'semua') {
            $queryDiproses->where('status_request', $request->status);
        }

        // Filter periode
        $periode = $request->get('periode', 'bulan_ini');
        if ($periode === 'hari_ini') {
            $queryDiproses->whereDate('waktu_approve', today());
        } elseif ($periode === 'minggu_ini') {
            $queryDiproses->whereBetween('waktu_approve', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($periode === 'bulan_ini') {
            $queryDiproses->whereMonth('waktu_approve', now()->month)
                          ->whereYear('waktu_approve', now()->year);
        } elseif ($periode === 'kustom' && $request->filled('dari') && $request->filled('sampai')) {
            $queryDiproses->whereBetween(DB::raw('DATE(waktu_approve)'), [$request->dari, $request->sampai]);
        }

        // Filter pencarian (nama barang, nama pemohon, atau kode transaksi)
        if ($request->filled('cari')) {
            $cari = trim($request->cari);
            $queryDiproses->where(function ($q) use ($cari) {
                $q->whereHas('barang', fn ($b) => $b->where('nama_barang', 'like', "%{$cari}%"))
                  ->orWhereHas('pengguna', fn ($u) => $u->where('name', 'like', "%{$cari}%"))
                  ->orWhere('kode_transaksi', 'like', "%{$cari}%");
            });
        }

        $sudahDiproses = $queryDiproses->latest('waktu_approve')
            ->paginate(15)
            ->withQueryString();

        // Hitung total unit barang keluar yang disetujui pada periode ini
        $queryTotalDisetujui = PermintaanBarang::where('status_request', 'disetujui');
        if ($periode === 'hari_ini') {
            $queryTotalDisetujui->whereDate('waktu_approve', today());
        } elseif ($periode === 'minggu_ini') {
            $queryTotalDisetujui->whereBetween('waktu_approve', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($periode === 'bulan_ini') {
            $queryTotalDisetujui->whereMonth('waktu_approve', now()->month)->whereYear('waktu_approve', now()->year);
        } elseif ($periode === 'kustom' && $request->filled('dari') && $request->filled('sampai')) {
            $queryTotalDisetujui->whereBetween(DB::raw('DATE(waktu_approve)'), [$request->dari, $request->sampai]);
        }
        $totalBarangKeluarQty = (int) $queryTotalDisetujui->sum('jumlah');
        $totalTransaksiDisetujui = (int) $queryTotalDisetujui->count();

        // Semua barang untuk referensi stok
        $semuaBarang = BarangInventori::orderBy('nama_barang')->get();

        return view('barang.gudang-daftar', compact(
            'permintaanPending',
            'sudahDiproses',
            'semuaBarang',
            'totalBarangKeluarQty',
            'totalTransaksiDisetujui',
            'periode'
        ));
    }

    /**
     * Gudang menyetujui permintaan → stok otomatis berkurang + dicatat ke laporan stok.
     */
    public function setujui($id)
    {
        $permintaan = PermintaanBarang::with('barang')->findOrFail($id);

        if ($permintaan->status_request !== 'disetujui_spv') {
            return back()->with('info', 'Permintaan ini belum disetujui supervisor atau sudah diproses sebelumnya.');
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

        DB::transaction(function () use ($permintaan, $barang) {
            $saldoSebelum = $barang->stok_saat_ini;

            // Kurangi stok inventori
            $barang->decrement('stok_saat_ini', $permintaan->jumlah);
            $saldoSesudah = $barang->fresh()->stok_saat_ini;

            // Update status permintaan
            $permintaan->update([
                'status_request' => 'disetujui',
                'waktu_approve'  => now(),
            ]);

            // Catat mutasi distribusi ke ledger
            MutasiStok::create([
                'nomor_transaksi'  => MutasiStok::buatNomor('DS'),
                'barang_id'        => $barang->id,
                'tanggal'          => now()->toDateString(),
                'jenis_transaksi'  => 'distribusi',
                'jumlah_masuk'     => 0,
                'jumlah_keluar'    => $permintaan->jumlah,
                'saldo_sebelumnya' => $saldoSebelum,
                'saldo_setelahnya' => $saldoSesudah,
                'tipe_stok'        => 'gudang',
                'unit'             => $permintaan->pengguna?->name ?? '-',
                'nomor_referensi'  => 'REQ-' . $permintaan->id,
                'keterangan'       => 'Distribusi ke ' . ($permintaan->pengguna?->name ?? 'CS') . ' (Permintaan #' . $permintaan->id . ')',
                'petugas_id'       => auth()->id(),
            ]);

            $bulan = now()->month;
            $tahun = now()->year;

            // Reload stok terkini setelah decrement untuk mendapatkan nilai akurat
            $stokSetelahKurang = $barang->fresh()->stok_saat_ini;

            // Pastikan laporan stok periode ini ada
            $laporan = LaporanStok::firstOrCreate(
                [
                    'barang_id'     => $barang->id,
                    'periode_bulan' => $bulan,
                    'periode_tahun' => $tahun,
                ],
                [
                    'tanggal'    => now()->startOfMonth()->toDateString(),
                    'stok_masuk' => $stokSetelahKurang + $permintaan->jumlah, // stok sebelum dikurangi
                ]
            );

            // Tentukan unit dari area CS yang meminta (jika ada), fallback ke 'Lantai 1'
            $unitPeminta = $this->resolveUnit('');

            // Tentukan minggu berdasarkan tanggal hari ini
            $minggu = (int) ceil(now()->day / 7);
            $minggu = min($minggu, 4); // maksimal minggu 4

            // Upsert: tambah ke pemakaian yang sudah ada di unit + minggu ini
            $existing = PemakaianStok::where([
                'laporan_stok_id' => $laporan->id,
                'barang_id'       => $barang->id,
                'unit'            => $unitPeminta,
                'minggu'          => $minggu,
            ])->first();

            if ($existing) {
                $existing->increment('jumlah', $permintaan->jumlah);
            } else {
                PemakaianStok::create([
                    'laporan_stok_id' => $laporan->id,
                    'barang_id'       => $barang->id,
                    'unit'            => $unitPeminta,
                    'minggu'          => $minggu,
                    'jumlah'          => $permintaan->jumlah,
                ]);
            }
        });

        $namaPengguna = $permintaan->pengguna?->name ?? 'Petugas';
        return back()->with('sukses',
            'Permintaan ' . $namaPengguna . ' untuk ' . ($barang->nama_barang ?? 'Barang')
            . ' (' . $permintaan->jumlah . ' ' . ($barang->satuan ?? 'unit') . ') telah disetujui. Stok dikurangi.'
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

        if ($permintaan->status_request !== 'disetujui_spv') {
            return back()->with('info', 'Permintaan ini belum disetujui supervisor atau sudah diproses sebelumnya.');
        }

        $permintaan->update([
            'status_request'   => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
            'waktu_approve'    => now(),
        ]);

        $namaPengguna = $permintaan->pengguna?->name ?? 'Petugas';
        return back()->with('sukses', 'Permintaan dari ' . $namaPengguna . ' telah ditolak.');
    }

    // ================================================================
    // BUKTI TRANSAKSI (Faktur / Surat Permintaan Barang)
    // ================================================================

    /**
     * Tampilkan / download bukti PDF untuk satu permintaan barang.
     */
    public function showProof($id)
    {
        $permintaan = PermintaanBarang::with(['pengguna.peran', 'barang', 'supervisor', 'area'])->findOrFail($id);

        // Ambil semua barang yang diajukan dalam transaksi ini
        $semuaBarang = $permintaan->itemsSatuTransaksi();

        $pdf = Pdf::loadView('barang.bukti', compact('permintaan', 'semuaBarang'))
            ->setPaper('a4', 'portrait');

        $kodeDoc = $permintaan->kode_transaksi ?: ('BT-' . str_pad($permintaan->id, 4, '0', STR_PAD_LEFT));
        $namaFile = 'bukti-transaksi-' . $kodeDoc . '.pdf';

        return $pdf->stream($namaFile);
    }

    // ----------------------------------------------------------------
    // HELPER PRIVATE
    // ----------------------------------------------------------------

    /**
     * Petakan nama area/lantai CS ke nama unit laporan stok.
     * Fallback ke 'Lantai 1' jika tidak cocok.
     */
    private function resolveUnit(string $namaArea): string
    {
        $namaAreaTrim = trim($namaArea);
        if (in_array($namaAreaTrim, BarangInventori::$daftarUnit)) {
            return $namaAreaTrim;
        }

        $namaAreaLower = strtolower($namaAreaTrim);

        $peta = [
            'lantai 1'  => 'Lantai 1',
            'lantai 2'  => 'Lantai 2',
            'lantai 3'  => 'Lantai 3',
            'lantai 4'  => 'Lantai 4',
            'lantai 5'  => 'Lantai 5',
            'lantai 6'  => 'Lantai 6',
            'rawasari'  => 'RWS',
            'rws'       => 'RWS',
            'cpb'       => 'CPB',
            'cpt'       => 'CPT',
            'cssd'      => 'CSSD',
        ];

        // Cocokkan substring
        foreach ($peta as $kata => $unit) {
            if (str_contains($namaAreaLower, $kata)) {
                return $unit;
            }
        }

        return 'Lantai 1'; // default fallback
    }

    /**
     * Export daftar permintaan barang ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $dari   = $request->get('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai', now()->toDateString());
        $status = $request->get('status', '');

        $namaFile = 'Permintaan-Barang-' . $dari . '-sd-' . $sampai . '.xlsx';

        return Excel::download(
            new PermintaanBarangExport($dari, $sampai, $status ?: null),
            $namaFile
        );
    }

    // ================================================================
    // REKAPAN PEMAKAIAN BARANG PER BULAN (Gudang & Admin)
    // ================================================================

    /**
     * Tampilkan halaman rekapan pemakaian barang per bulan format tabel matrix.
     * Format: Baris = nama barang, Kolom = bulan Jan–Des, Total, Rata-rata.
     */
    public function rekapanPemakaian(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);
        $kategori = $request->get('kategori', '');

        // Ambil semua data permintaan yang DISETUJUI GUDANG dalam tahun tersebut
        $query = PermintaanBarang::with('barang')
            ->where('status_request', 'disetujui')
            ->whereYear('waktu_approve', $tahun);

        if ($kategori) {
            $query->whereHas('barang', fn($q) => $q->where('kategori', $kategori));
        }

        $semuaPermintaan = $query->get();

        // Ambil semua barang unik yang pernah diminta di tahun ini
        $daftarBarangId = $semuaPermintaan->pluck('barang_id')->unique();
        $semuaBarang = BarangInventori::whereIn('id', $daftarBarangId)
            ->orderBy('nama_barang')
            ->get()
            ->keyBy('id');

        // Jika tidak ada transaksi, tampilkan semua barang aktif sebagai fallback
        if ($semuaBarang->isEmpty()) {
            $barangQuery = BarangInventori::orderBy('nama_barang');
            if ($kategori) $barangQuery->where('kategori', $kategori);
            $semuaBarang = $barangQuery->get()->keyBy('id');
        }

        // Bangun matrix: [barang_id][bulan] = total_pemakaian
        $matrix = [];
        foreach ($semuaPermintaan as $p) {
            $bId   = $p->barang_id;
            $bulan = (int) $p->waktu_approve->format('n'); // 1–12
            $matrix[$bId][$bulan] = ($matrix[$bId][$bulan] ?? 0) + $p->jumlah;
        }

        // Hitung total & rata-rata per barang
        $rekapan = [];
        foreach ($semuaBarang as $bId => $barang) {
            $bulanan = $matrix[$bId] ?? [];
            $total   = array_sum($bulanan);
            // Hitung bulan yang ada transaksi untuk rata-rata lebih akurat
            $bulanAktif = count(array_filter($bulanan, fn($v) => $v > 0));
            $rata   = $bulanAktif > 0 ? $total / 12 : 0; // rata-rata per tahun / 12 bulan

            $rekapan[] = [
                'barang'      => $barang,
                'bulanan'     => $bulanan,  // [1=>0, 2=>5, 3=>3, ...]
                'total'       => $total,
                'rata_rata'   => $rata,
                'pembulatan'  => (int) round($rata),
            ];
        }

        // Hitung total kolom per bulan (row "TOTAL" di bagian bawah)
        $totalPerBulan = [];
        for ($b = 1; $b <= 12; $b++) {
            $totalPerBulan[$b] = array_sum(array_column(
                array_map(fn($r) => $r['bulanan'], $rekapan),
                $b
            ));
        }
        $grandTotal   = array_sum($totalPerBulan);
        $grandRata    = $grandTotal / 12;

        $daftarTahun  = range(now()->year - 3, now()->year + 1);
        $daftarKategori = BarangInventori::$daftarKategori;
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agt',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        return view('barang.rekapan-pemakaian', compact(
            'rekapan', 'totalPerBulan', 'grandTotal', 'grandRata',
            'tahun', 'kategori', 'daftarTahun', 'daftarKategori', 'namaBulan'
        ));
    }

    /**
     * Export rekapan pemakaian ke Excel (format .xlsx).
     */
    public function exportRekapan(Request $request)
    {
        $tahun    = (int) $request->get('tahun', now()->year);
        $kategori = $request->get('kategori', '');

        $namaFile = 'Rekapan-Pemakaian-Barang-' . $tahun
            . ($kategori ? '-' . str_replace(' ', '_', $kategori) : '')
            . '.xlsx';

        return Excel::download(
            new \App\Exports\RekapanPemakaianExport($tahun, $kategori),
            $namaFile
        );
    }
}
