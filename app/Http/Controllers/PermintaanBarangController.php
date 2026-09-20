<?php

namespace App\Http\Controllers;

use App\Models\BarangInventori;
use App\Models\LaporanStok;
use App\Models\PemakaianStok;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PermintaanBarangController extends Controller
{
    // ── Kunci session keranjang per-user ────────────────────────────
    private function kunciKeranjang(): string
    {
        return 'keranjang_' . auth()->id();
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

        // Cek apakah sudah ada permintaan pending untuk barang ini
        $sudahPending = PermintaanBarang::where('user_id', auth()->id())
            ->where('barang_id', $barang->id)
            ->where('status_request', 'pending')
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
        $pengguna  = auth()->user();
        $keranjang = $this->ambilKeranjang();

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
                // Kap jumlah jika stok berkurang
                if ($item['jumlah'] > $b->stok_saat_ini) {
                    $item['jumlah'] = max(1, $b->stok_saat_ini);
                }
            } else {
                // Barang sudah dihapus dari master — buang dari keranjang
                unset($keranjang[$id]);
            }
        }
        unset($item);
        $this->simpanKeranjang($keranjang);

        return view('barang.keranjang', compact('keranjang', 'pengguna'));
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

        if (empty($keranjang)) {
            return redirect()->route('barang.katalog')->with('gagal', 'Keranjang kosong. Tambahkan barang terlebih dahulu.');
        }

        $berhasil = 0;
        $gagal    = [];

        DB::transaction(function () use ($pengguna, $keranjang, &$berhasil, &$gagal) {
            foreach ($keranjang as $id => $item) {
                $barang = BarangInventori::find($id);

                // Barang sudah dihapus
                if (! $barang) {
                    $gagal[] = ($item['nama_barang'] ?? 'Barang') . ': tidak ditemukan';
                    continue;
                }

                // Stok habis
                if ($barang->stok_saat_ini == 0) {
                    $gagal[] = $barang->nama_barang . ': stok habis';
                    continue;
                }

                // Sudah ada permintaan pending
                $sudahAda = PermintaanBarang::where('user_id', $pengguna->id)
                    ->where('barang_id', $id)
                    ->where('status_request', 'pending')
                    ->exists();

                if ($sudahAda) {
                    $gagal[] = $barang->nama_barang . ': sudah ada permintaan pending';
                    continue;
                }

                $jumlah = min((int) $item['jumlah'], $barang->stok_saat_ini);

                PermintaanBarang::create([
                    'user_id'        => $pengguna->id,
                    'barang_id'      => $id,
                    'jumlah'         => $jumlah,
                    'status_request' => 'pending',
                    'waktu_request'  => now(),
                ]);

                $berhasil++;
            }
        });

        // Kosongkan keranjang setelah submit
        $this->simpanKeranjang([]);

        if ($berhasil > 0 && empty($gagal)) {
            return redirect()->route('barang.katalog')
                ->with('sukses', $berhasil . ' permintaan barang berhasil dikirim ke gudang!');
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
     * Gudang menyetujui permintaan → stok otomatis berkurang + dicatat ke laporan stok.
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

        DB::transaction(function () use ($permintaan, $barang) {
            // Kurangi stok inventori
            $barang->decrement('stok_saat_ini', $permintaan->jumlah);

            // Update status permintaan
            $permintaan->update([
                'status_request' => 'disetujui',
                'waktu_approve'  => now(),
            ]);

            // --------------------------------------------------------
            // Catat ke pemakaian_stok (laporan stok) secara otomatis
            // Hanya jika barang ini terhubung ke produk_kebersihan
            // --------------------------------------------------------
            if ($barang->produk_id) {
                $bulan = now()->month;
                $tahun = now()->year;

                // Reload stok terkini setelah decrement untuk mendapatkan nilai akurat
                $stokSetelahKurang = $barang->fresh()->stok_saat_ini;

                // Pastikan laporan stok periode ini ada
                $laporan = LaporanStok::firstOrCreate(
                    [
                        'produk_id'     => $barang->produk_id,
                        'periode_bulan' => $bulan,
                        'periode_tahun' => $tahun,
                    ],
                    [
                        'tanggal'    => now()->startOfMonth()->toDateString(),
                        'stok_masuk' => $stokSetelahKurang + $permintaan->jumlah, // stok sebelum dikurangi
                    ]
                );

                // Tentukan unit dari area CS yang meminta (jika ada), fallback ke 'Lantai 1'
                // Fallback ke 'Lantai 1' karena User tidak lagi memiliki relasi area
                $unitPeminta = $this->resolveUnit('');

                // Tentukan minggu berdasarkan tanggal hari ini
                $minggu = (int) ceil(now()->day / 7);
                $minggu = min($minggu, 4); // maksimal minggu 4

                // Upsert: tambah ke pemakaian yang sudah ada di unit + minggu ini
                $existing = PemakaianStok::where([
                    'laporan_stok_id' => $laporan->id,
                    'produk_id'       => $barang->produk_id,
                    'unit'            => $unitPeminta,
                    'minggu'          => $minggu,
                ])->first();

                if ($existing) {
                    $existing->increment('jumlah', $permintaan->jumlah);
                } else {
                    PemakaianStok::create([
                        'laporan_stok_id' => $laporan->id,
                        'produk_id'       => $barang->produk_id,
                        'unit'            => $unitPeminta,
                        'minggu'          => $minggu,
                        'jumlah'          => $permintaan->jumlah,
                    ]);
                }
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

        if ($permintaan->status_request !== 'pending') {
            return back()->with('info', 'Permintaan ini sudah diproses sebelumnya.');
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
        $permintaan = PermintaanBarang::with(['pengguna', 'barang'])->findOrFail($id);

        $pdf = Pdf::loadView('barang.bukti', compact('permintaan'))
            ->setPaper('a4', 'portrait');

        $namaFile = 'bukti-permintaan-' . $permintaan->id . '.pdf';

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
        if (in_array($namaAreaTrim, \App\Models\ProdukKebersihan::$daftarUnit)) {
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
}
