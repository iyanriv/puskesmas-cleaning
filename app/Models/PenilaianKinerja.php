<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianKinerja extends Model
{
    protected $table = 'penilaian_kinerja';

    protected $fillable = [
        'penilai_id',
        'dinilai_id',
        'tanggal',
        'nilai_kebersihan',
        'nilai_kedisiplinan',
        'nilai_kerjasama',
        'nilai_inisiatif',
        'catatan',
    ];

    protected $casts = [
        'tanggal'            => 'date',
        'nilai_kebersihan'   => 'integer',
        'nilai_kedisiplinan' => 'integer',
        'nilai_kerjasama'    => 'integer',
        'nilai_inisiatif'    => 'integer',
    ];

    public function penilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penilai_id');
    }

    public function dinilai(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dinilai_id');
    }

    /**
     * Hitung rata-rata nilai dari 4 aspek.
     */
    public function rataRata(): float
    {
        return round(
            ($this->nilai_kebersihan + $this->nilai_kedisiplinan
            + $this->nilai_kerjasama + $this->nilai_inisiatif) / 4,
            1
        );
    }

    /**
     * Label grade berdasarkan rata-rata.
     */
    public function grade(): string
    {
        $rata = $this->rataRata();
        return match (true) {
            $rata >= 4.5 => 'Sangat Baik',
            $rata >= 3.5 => 'Baik',
            $rata >= 2.5 => 'Cukup',
            $rata >= 1.5 => 'Kurang',
            default      => 'Buruk',
        };
    }
}
