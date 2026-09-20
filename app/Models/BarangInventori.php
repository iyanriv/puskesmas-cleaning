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
        'produk_id',
        'nama_barang',
        'deskripsi',
        'foto_barang',
        'stok_saat_ini',
        'satuan',
        'stok_minimum',
    ];

    // ----------------------------------------------------------------
    // RELASI
    // ----------------------------------------------------------------

    public function permintaan(): HasMany
    {
        return $this->hasMany(PermintaanBarang::class, 'barang_id');
    }

    /**
     * Relasi ke produk_kebersihan — untuk sinkronisasi laporan stok.
     */
    public function produkKebersihan(): BelongsTo
    {
        return $this->belongsTo(ProdukKebersihan::class, 'produk_id');
    }

    // ----------------------------------------------------------------
    // HELPER
    // ----------------------------------------------------------------

    public function stokMenipis(): bool
    {
        return $this->stok_saat_ini <= $this->stok_minimum;
    }

    /**
     * Pastikan laporan_stok periode bulan/tahun sudah ada untuk produk ini.
     * Jika belum ada, buat dengan stok_masuk = 0.
     * Kembalikan instance LaporanStok.
     */
    public function pastikanLaporanPeriode(int $bulan, int $tahun): ?LaporanStok
    {
        if (! $this->produk_id) {
            return null;
        }

        return LaporanStok::firstOrCreate(
            [
                'produk_id'     => $this->produk_id,
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
