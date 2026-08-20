<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanBarang extends Model
{
    protected $table = 'permintaan_barang';

    protected $fillable = [
        'user_id',
        'barang_id',
        'jumlah',
        'status_request',
        'alasan_penolakan',
        'waktu_request',
        'waktu_approve',
    ];

    protected $casts = [
        'waktu_request' => 'datetime',
        'waktu_approve' => 'datetime',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangInventori::class, 'barang_id');
    }
}
