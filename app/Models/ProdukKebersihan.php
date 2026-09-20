<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProdukKebersihan extends Model
{
    protected $table = 'produk_kebersihan';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
    ];

    // Daftar satuan yang valid
    public static array $daftarSatuan = [
        'buah', 'botol', 'galon', 'pak', 'bungkus', 'pouch', 'roll', 'pasang', 'kaleng',
    ];

    // Daftar unit/lokasi yang valid
    public static array $daftarUnit = [
        'Lantai 1', 'Lantai 2', 'Lantai 3', 'Lantai 4', 'Lantai 5', 'Lantai 6',
        'Rawasari', 'CPB', 'CPT', 'CSSD',
    ];

    public function laporanStok(): HasMany
    {
        return $this->hasMany(LaporanStok::class, 'produk_id');
    }

    public function pemakaianStok(): HasMany
    {
        return $this->hasMany(PemakaianStok::class, 'produk_id');
    }

    /**
     * Buat kode barang otomatis berdasarkan urutan terakhir.
     * Format: BRG-0001, BRG-0002, dst.
     */
    public static function buatKodeOtomatis(): string
    {
        $terakhir = static::orderByDesc('id')->first();

        if (! $terakhir) {
            return 'BRG-0001';
        }

        // Ambil nomor urut dari kode terakhir, jika mengikuti format BRG-XXXX
        if (preg_match('/^BRG-(\d+)$/', $terakhir->kode_barang, $cocok)) {
            $nomorBaru = (int) $cocok[1] + 1;
            return 'BRG-' . str_pad($nomorBaru, 4, '0', STR_PAD_LEFT);
        }

        return 'BRG-' . str_pad($terakhir->id + 1, 4, '0', STR_PAD_LEFT);
    }
}
