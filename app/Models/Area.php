<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'area';

    protected $fillable = ['lantai'];

    /**
     * Accessor fallback jika ada bagian kode yang masih mengakses nama_ruangan
     */
    public function getNamaRuanganAttribute(): string
    {
        return $this->lantai ?? '';
    }


    public function ceklis(): HasMany
    {
        return $this->hasMany(CeklisKebersihan::class, 'area_id');
    }
}
