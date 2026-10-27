<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiStok extends Model
{
    protected $table = 'mutasi_stok';

    protected $fillable = [
        'nomor_transaksi',
        'barang_id',
        'tanggal',
        'jenis_transaksi',
        'jumlah_masuk',
        'jumlah_keluar',
        'saldo_sebelumnya',
        'saldo_setelahnya',
        'tipe_stok',
        'unit',
        'sumber_pasokan',
        'nomor_referensi',
        'keterangan',
        'petugas_id',
    ];

    protected $casts = [
        'tanggal'          => 'date',
        'jumlah_masuk'     => 'integer',
        'jumlah_keluar'    => 'integer',
        'saldo_sebelumnya' => 'integer',
        'saldo_setelahnya' => 'integer',
    ];

    // ----------------------------------------------------------------
    // RELASI
    // ----------------------------------------------------------------

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangInventori::class, 'barang_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    // ----------------------------------------------------------------
    // HELPER & SCOPES
    // ----------------------------------------------------------------

    public static function buatNomor(string $prefix = 'SM'): string
    {
        $today = now()->format('Ymd');
        $prefix = strtoupper($prefix);
        $pattern = "{$prefix}-{$today}-%";

        $last = static::where('nomor_transaksi', 'like', $pattern)
            ->orderByDesc('id')
            ->first();

        if ($last && preg_match("/{$prefix}-{$today}-(\\d+)/", $last->nomor_transaksi, $matches)) {
            $next = (int) $matches[1] + 1;
        } else {
            $next = 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $today, $next);
    }

    public function labelJenisTransaksi(): string
    {
        return match ($this->jenis_transaksi) {
            'stok_masuk'     => 'Stok Masuk / Restok',
            'distribusi'     => 'Distribusi / Keluar',
            'pemakaian_unit' => 'Pemakaian Unit',
            'stok_awal'      => 'Stok Awal',
            'penyesuaian'    => 'Penyesuaian Stok',
            default          => ucfirst(str_replace('_', ' ', $this->jenis_transaksi)),
        };
    }

    public function badgeJenisTransaksi(): string
    {
        return match ($this->jenis_transaksi) {
            'stok_masuk' => '<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1"><i class="bi bi-box-arrow-in-down me-1"></i>Stok Masuk</span>',
            'distribusi' => '<span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1"><i class="bi bi-box-arrow-up-right me-1"></i>Distribusi</span>',
            'pemakaian_unit' => '<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2 py-1"><i class="bi bi-building-check me-1"></i>Pemakaian Unit</span>',
            'stok_awal' => '<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1"><i class="bi bi-asterisk me-1"></i>Stok Awal</span>',
            'penyesuaian' => '<span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1"><i class="bi bi-sliders me-1"></i>Penyesuaian</span>',
            default => '<span class="badge bg-light text-dark border rounded-pill px-2 py-1">' . e($this->labelJenisTransaksi()) . '</span>',
        };
    }
}
