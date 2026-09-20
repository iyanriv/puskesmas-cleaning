<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemakaianStok extends Model
{
    protected $table = 'pemakaian_stok';

    protected $fillable = [
        'laporan_stok_id',
        'produk_id',
        'unit',
        'minggu',
        'jumlah',
    ];

    protected $casts = [
        'minggu' => 'integer',
        'jumlah' => 'integer',
    ];

    public function laporanStok(): BelongsTo
    {
        return $this->belongsTo(LaporanStok::class, 'laporan_stok_id');
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(ProdukKebersihan::class, 'produk_id');
    }
}
