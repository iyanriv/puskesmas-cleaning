<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BarangInventori extends Model
{
    protected $table = 'barang_inventori';

    protected $fillable = [
        'nama_barang',
        'deskripsi',
        'foto_barang',
        'stok_saat_ini',
        'satuan',
        'stok_minimum',
    ];

    public function permintaan(): HasMany
    {
        return $this->hasMany(PermintaanBarang::class, 'barang_id');
    }

    public function stokMenipis(): bool
    {
        return $this->stok_saat_ini <= $this->stok_minimum;
    }
}
