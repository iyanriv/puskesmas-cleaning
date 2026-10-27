<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanBarang extends Model
{
    protected $table = 'permintaan_barang';

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'supervisor_id',
        'barang_id',
        'area_id',
        'jumlah',
        'status_request',
        'alasan_penolakan',
        'catatan_spv',
        'waktu_request',
        'waktu_approve_spv',
        'waktu_approve',
    ];

    protected $casts = [
        'waktu_request'    => 'datetime',
        'waktu_approve_spv' => 'datetime',
        'waktu_approve'    => 'datetime',
    ];

    /**
     * Status yang berarti masih menunggu tindakan
     */
    public static array $statusMenunggu = ['menunggu_spv', 'disetujui_spv'];

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(BarangInventori::class, 'barang_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Area::class, 'area_id');
    }

    /**
     * Ambil semua barang yang diajukan bersamaan dalam satu transaksi/checkout ini.
     */
    public function itemsSatuTransaksi()
    {
        if ($this->kode_transaksi) {
            return self::with(['barang', 'pengguna'])
                ->where('kode_transaksi', $this->kode_transaksi)
                ->orderBy('id')
                ->get();
        }

        // Fallback untuk data historis: ambil item dari user yang sama dalam waktu berdekatan
        return self::with(['barang', 'pengguna'])
            ->where('user_id', $this->user_id)
            ->whereBetween('waktu_request', [
                $this->waktu_request->copy()->subMinutes(5),
                $this->waktu_request->copy()->addMinutes(5)
            ])
            ->orderBy('id')
            ->get();
    }

    /**
     * Generate kode transaksi baru (misal: BT-2610-001)
     */
    public static function buatKodeTransaksi(): string
    {
        $prefix = 'BT-' . date('ym') . '-';
        $terakhir = self::where('kode_transaksi', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('kode_transaksi');

        $nomor = 1;
        if ($terakhir && preg_match('/-(\d+)$/', $terakhir, $m)) {
            $nomor = (int) $m[1] + 1;
        }

        return $prefix . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Label status untuk tampilan UI
     */
    public function labelStatus(): string
    {
        return match($this->status_request) {
            'menunggu_spv'  => 'Menunggu Supervisor',
            'disetujui_spv' => 'Disetujui Supervisor',
            'ditolak_spv'   => 'Ditolak Supervisor',
            'disetujui'     => 'Disetujui',
            'ditolak'       => 'Ditolak',
            default         => ucfirst($this->status_request),
        };
    }

    /**
     * Kelas badge Bootstrap untuk status
     */
    public function badgeStatus(): string
    {
        return match($this->status_request) {
            'menunggu_spv'  => 'warning',
            'disetujui_spv' => 'info',
            'ditolak_spv'   => 'danger',
            'disetujui'     => 'success',
            'ditolak'       => 'danger',
            default         => 'secondary',
        };
    }
}
