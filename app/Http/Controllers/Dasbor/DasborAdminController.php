<?php

namespace App\Http\Controllers\Dasbor;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\BarangInventori;
use App\Models\CeklisKebersihan;
use App\Models\User;
use Illuminate\Http\Request;

class DasborAdminController extends Controller
{
    public function index(Request $request)
    {
        $statistik = [
            'total_pengguna' => User::count(),
            'total_area' => Area::count(),
            'total_barang' => BarangInventori::count(),
            'ceklis_hari_ini' => CeklisKebersihan::where('tanggal', now()->toDateString())->count(),
        ];

        return view('dasbor.admin', compact('statistik'));
    }
}
