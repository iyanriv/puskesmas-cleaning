<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetoranSampah extends Model
{
    protected $table = 'setoran_sampah';

    protected $fillable = [
        'user_id',
        'jenis_sampah',   // JSON - array multi-pilih
        'lokasi_setor',   // string lokasi/area
        'foto_timbangan',
        'catatan',
        'tanggal',
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'berat_kg'     => 'decimal:2',
        'jenis_sampah' => 'array',    // otomatis encode/decode JSON
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Ambil jenis sampah sebagai string yang dipisah koma.
     */
    public function jenisSampahTeks(): string
    {
        return is_array($this->jenis_sampah)
            ? implode(', ', $this->jenis_sampah)
            : ($this->jenis_sampah ?? '-');
    }
}

