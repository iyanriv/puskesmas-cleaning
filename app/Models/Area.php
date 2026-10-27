<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

    /**
     * Accessor: nama lantai dalam format tampilan yang rapi.
     * Singkatan (CSSD, CPB, CPT) tetap huruf besar,
     * nama panjang dikonversi ke Title Case.
     */
    public function getNamaRapiAttribute(): string
    {
        $lantai = $this->lantai ?? '';

        // Daftar singkatan yang harus tetap uppercase
        $singkatan = ['CSSD', 'CPB', 'CPT'];
        if (in_array(strtoupper(trim($lantai)), $singkatan)) {
            return strtoupper(trim($lantai));
        }

        return Str::title(strtolower($lantai));
    }
}
