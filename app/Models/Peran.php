<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peran extends Model
{
    protected $table = 'peran';

    protected $fillable = ['nama_peran'];

    public function pengguna(): HasMany
    {
        return $this->hasMany(User::class, 'peran_id');
    }
}
