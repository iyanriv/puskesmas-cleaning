<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SetoranSampah extends Model
{
    protected $table = 'setoran_sampah';

    protected $fillable = [
        'user_id',
        'jenis_sampah',      // JSON - array multi-pilih
        'lokasi_setor',      // string lokasi/area
        'foto_timbangan',
        'catatan',
        'tanggal',
        'status_validasi',   // menunggu | valid | ditolak
        'catatan_validasi',  // catatan dari supervisor saat validasi
        'validator_id',      // FK ke users (supervisor yang validasi)
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'jenis_sampah' => 'array',    // otomatis encode/decode JSON
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validator_id');
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

