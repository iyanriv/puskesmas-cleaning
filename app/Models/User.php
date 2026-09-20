<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nik',
        'password',
        'peran_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function peran(): BelongsTo
    {
        return $this->belongsTo(Peran::class, 'peran_id');
    }

    public function ceklis(): HasMany
    {
        return $this->hasMany(CeklisKebersihan::class, 'user_id');
    }

    public function permintaanBarang(): HasMany
    {
        return $this->hasMany(PermintaanBarang::class, 'user_id');
    }

    public function setoranSampah(): HasMany
    {
        return $this->hasMany(SetoranSampah::class, 'user_id');
    }

    public function penilaianPj(): HasMany
    {
        return $this->hasMany(PenilaianPjLantai::class, 'petugas_cs_id');
    }

    public function tugasMingguan(): HasMany
    {
        return $this->hasMany(TugasMingguan::class, 'user_id');
    }

    public function ruteDasbor(): string
    {
        return match ($this->peran?->nama_peran) {
            'admin' => route('dasbor.admin'),
            'supervisor', 'pj_lantai' => route('dasbor.supervisor'),
            'cs' => route('dasbor.cs'),
            'gudang' => route('dasbor.gudang'),
            default => route('login'),
        };
    }
}
