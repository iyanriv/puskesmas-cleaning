<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'area';

    protected $fillable = ['nama_ruangan', 'lantai'];

    public function pengguna(): HasMany
    {
        return $this->hasMany(User::class, 'area_id');
    }

    public function ceklis(): HasMany
    {
        return $this->hasMany(CeklisKebersihan::class, 'area_id');
    }
}
