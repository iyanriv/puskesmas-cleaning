<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasMingguan extends Model
{
    use HasFactory;

    protected $table = 'tugas_mingguan';

    protected $fillable = [
        'user_id',
        'tanggal',
        'waktu_pelaporan',
        'rincian_kegiatan',
        'foto_sebelum',
        'foto_setelah',
        'status',
        'catatan_supervisor',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    protected $casts = [
        'foto_sebelum'       => 'array',
        'tanggal'            => 'date',
        'diverifikasi_pada'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function badgeStatus(): string
    {
        return match ($this->status) {
            'proses'     => '<span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Dalam Proses</span>',
            'selesai'    => '<span class="badge bg-primary"><i class="bi bi-check2-circle me-1"></i>Menunggu Verifikasi</span>',
            'disetujui'  => '<span class="badge bg-success"><i class="bi bi-patch-check-fill me-1"></i>Disetujui</span>',
            default      => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }
}
