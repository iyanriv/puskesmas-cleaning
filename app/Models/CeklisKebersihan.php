<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CeklisKebersihan extends Model
{
    protected $table = 'ceklis';

    protected $fillable = [
        'user_id',
        'area_id',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'foto_before',
        'foto_after',
        'lat_long',
        'status',
        'skor',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
}
