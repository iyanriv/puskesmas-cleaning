<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'status_alat',        // JSON: array kondisi peralatan (FR-015)
        'catatan',
        'status_terima',
        'tugas_items',        // JSON: daftar item tugas yang dioper
        'status_penyelesaian',// enum: belum | selesai | dieskalasi
        'catatan_eskalasi',   // alasan tidak bisa mengerjakan
        'parent_operan_id',   // referensi operan induk saat dieskalasi
        'dibaca_pada',        // waktu pertama kali penerima membaca notifikasi
    ];

    protected $casts = [
        'tanggal'      => 'date',
        'status_alat'  => 'array',
        'tugas_items'  => 'array',
        'dibaca_pada'  => 'datetime',
    ];

    // -------------------------------------------------------
    // Relationships
    // -------------------------------------------------------

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penerima_id');
    }

    /** Operan induk (yang dieskalasi dari sini) */
    public function parentOperan(): BelongsTo
    {
        return $this->belongsTo(OperanShift::class, 'parent_operan_id');
    }

    /** Operan eskalasi turunan dari operan ini */
    public function eskalasiBerikutnya(): HasOne
    {
        return $this->hasOne(OperanShift::class, 'parent_operan_id');
    }

    // -------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------

    /** Apakah operan memiliki tugas yang belum selesai? */
    public function adaTugasBelumSelesai(): bool
    {
        return !empty($this->tugas_items) && $this->status_penyelesaian === 'belum';
    }

    /** Badge HTML untuk status penerimaan operan */
    public function badgeTerima(): string
    {
        return match ($this->status_terima) {
            'diterima' => '<span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-check2 me-1"></i>Diterima</span>',
            default    => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="bi bi-clock me-1"></i>Menunggu</span>',
        };
    }

    /** Badge HTML untuk status penyelesaian tugas */
    public function badgePenyelesaian(): string
    {
        return match ($this->status_penyelesaian) {
            'selesai'    => '<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Tugas Selesai</span>',
            'dieskalasi' => '<span class="badge bg-secondary"><i class="bi bi-arrow-right-circle me-1"></i>Diteruskan</span>',
            default      => '<span class="badge bg-danger"><i class="bi bi-exclamation-circle me-1"></i>Belum Selesai</span>',
        };
    }
}
