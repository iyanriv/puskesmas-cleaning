<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'jenis_pekerjaan';

    protected $fillable = [
        'nama_pekerjaan',
        'deskripsi',
        'is_aktif',
        'urutan',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
        'urutan'   => 'integer',
    ];

    /**
     * Scope untuk pekerjaan yang aktif saja.
     */
    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
