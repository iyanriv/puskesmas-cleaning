<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperanShift extends Model
{
    protected $table = 'operan';

    protected $fillable = [
        'pengirim_id',
        'penerima_id',
        'tanggal',
        'waktu',
        'tempat_tugas',
        'waktu_jaga',
        'status_alat',    // JSON: array kondisi peralatan (FR-015)
        'catatan',
        'status_terima',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'status_alat' => 'array',   // otomatis encode/decode JSON
    ];

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }
}
