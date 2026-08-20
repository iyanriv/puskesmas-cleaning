<?php

namespace App\Http\Controllers\Dasbor;

use App\Http\Controllers\Controller;
use App\Models\BarangInventori;
use App\Models\PermintaanBarang;
use Illuminate\Http\Request;

class DasborGudangController extends Controller
{
    public function index(Request $request)
    {
        $stokMenipis = BarangInventori::whereColumn('stok_saat_ini', '<=', 'stok_minimum')->get();
        $permintaanPending = PermintaanBarang::with(['pengguna', 'barang'])
            ->where('status_request', 'pending')
            ->latest()
            ->get();

        return view('dasbor.gudang', compact('stokMenipis', 'permintaanPending'));
    }
}
