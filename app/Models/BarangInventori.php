<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangInventori extends Model
{
    protected $table = 'barang_inventori';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'kategori',
        'deskripsi',
        'foto_barang',
        'stok_saat_ini',
        'satuan',
        'stok_minimum',
    ];

    // Daftar kategori barang
    public static array $daftarKategori = [
        'Pembersih & Sanitasi',
        'Peralatan Kebersihan',
        'Perlengkapan Keselamatan',
        'Kantong & Kemasan',
        'Pewangi & Deodorant',
        'Lainnya',
    ];

    // Daftar satuan yang valid
    public static array $daftarSatuan = [
        'buah', 'botol', 'galon', 'pak', 'bungkus', 'pouch', 'roll', 'pasang', 'kaleng', 'pcs',
    ];

    // Daftar unit/lokasi yang valid untuk rekap pemakaian stok
    public static array $daftarUnit = [
        'Lantai 1', 'Lantai 2', 'Lantai 3', 'Lantai 4', 'Lantai 5', 'Lantai 6',
        'Rawasari', 'CPB', 'CPT', 'CSSD',
    ];

    // ----------------------------------------------------------------
    // RELASI
    // ----------------------------------------------------------------

    public function permintaan(): HasMany
    {
        return $this->hasMany(PermintaanBarang::class, 'barang_id');
    }

    public function laporanStok(): HasMany
    {
        return $this->hasMany(LaporanStok::class, 'barang_id');
    }

    public function pemakaianStok(): HasMany
    {
        return $this->hasMany(PemakaianStok::class, 'barang_id');
    }

    public function mutasiStok(): HasMany
    {
        return $this->hasMany(MutasiStok::class, 'barang_id');
    }

    // ----------------------------------------------------------------
    // HELPER
    // ----------------------------------------------------------------

    /**
     * Status stok: 'aman', 'menipis', atau 'habis'.
     */
    public function statusStok(): string
    {
        if ($this->stok_saat_ini <= 0) {
            return 'habis';
        }
        if ($this->stok_saat_ini <= $this->stok_minimum) {
            return 'menipis';
        }
        return 'aman';
    }

    public function stokMenipis(): bool
    {
        return in_array($this->statusStok(), ['menipis', 'habis']);
    }

    public function badgeStatusStok(): string
    {
        return match ($this->statusStok()) {
            'habis'   => '<span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1"><i class="bi bi-x-circle me-1"></i>Habis</span>',
            'menipis' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>Menipis</span>',
            default   => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1"><i class="bi bi-check-circle me-1"></i>Aman</span>',
        };
    }

    /**
     * Buat kode barang otomatis jika belum ditentukan.
     * Format: BRG-0001, BRG-0002, dst.
     */
    public static function buatKodeOtomatis(): string
    {
        $terakhir = static::whereNotNull('kode_barang')->orderByDesc('id')->first();

        if (! $terakhir) {
            return 'BRG-0001';
        }

        if (preg_match('/^BRG-(\d+)$/', $terakhir->kode_barang, $cocok)) {
            $nomorBaru = (int) $cocok[1] + 1;
            return 'BRG-' . str_pad($nomorBaru, 4, '0', STR_PAD_LEFT);
        }

        return 'BRG-' . str_pad($terakhir->id + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Pastikan laporan_stok periode bulan/tahun sudah ada untuk barang ini.
     * Jika belum ada, buat dengan stok_masuk = 0.
     * Kembalikan instance LaporanStok.
     */
    public function pastikanLaporanPeriode(int $bulan, int $tahun): LaporanStok
    {
        return LaporanStok::firstOrCreate(
            [
                'barang_id'     => $this->id,
                'periode_bulan' => $bulan,
                'periode_tahun' => $tahun,
            ],
            [
                'tanggal'    => now()->startOfMonth()->toDateString(),
                'stok_masuk' => 0,
            ]
        );
    }
}
