<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaporanStok extends Model
{
    protected $table = 'laporan_stok';

    protected $fillable = [
        'produk_id',
        'periode_bulan',
        'periode_tahun',
        'tanggal',
        'stok_masuk',
    ];

    protected $casts = [
        'tanggal'       => 'date',
        'stok_masuk'    => 'integer',
        'periode_bulan' => 'integer',
        'periode_tahun' => 'integer',
    ];

    public function produk(): BelongsTo
    {
        return $this->belongsTo(ProdukKebersihan::class, 'produk_id');
    }

    public function pemakaian(): HasMany
    {
        return $this->hasMany(PemakaianStok::class, 'laporan_stok_id');
    }

    // ----------------------------------------------------------------
    // KALKULASI OTOMATIS
    // ----------------------------------------------------------------

    /**
     * Total pemakaian sebulan = jumlah semua record pemakaian_stok.
     */
    public function totalPemakaian(): int
    {
        return $this->pemakaian->sum('jumlah');
    }

    /**
     * Sisa stok = stok_masuk - total pemakaian.
     */
    public function sisaStok(): int
    {
        return $this->stok_masuk - $this->totalPemakaian();
    }

    /**
     * Pemakaian per unit (dijumlah seluruh minggu).
     * Kembalikan array ['Lantai 1' => 5, 'Lantai 2' => 3, ...]
     */
    public function pemakaianPerUnit(): array
    {
        $hasil = array_fill_keys(ProdukKebersihan::$daftarUnit, 0);

        foreach ($this->pemakaian as $item) {
            if (array_key_exists($item->unit, $hasil)) {
                $hasil[$item->unit] += $item->jumlah;
            }
        }

        return $hasil;
    }

    /**
     * Pemakaian per unit per minggu.
     * Kembalikan array bertingkat: ['Lantai 1' => [1=>0, 2=>0, 3=>0, 4=>0], ...]
     */
    public function pemakaianPerUnitMinggu(): array
    {
        $hasil = [];
        foreach (ProdukKebersihan::$daftarUnit as $unit) {
            $hasil[$unit] = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
        }

        foreach ($this->pemakaian as $item) {
            if (isset($hasil[$item->unit][$item->minggu])) {
                $hasil[$item->unit][$item->minggu] = $item->jumlah;
            }
        }

        return $hasil;
    }
}
