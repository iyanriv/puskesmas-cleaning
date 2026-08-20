<?php

namespace App\Http\Controllers\Dasbor;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\CeklisKebersihan;
use App\Models\SetoranSampah;
use Illuminate\Http\Request;

class DasborSupervisorController extends Controller
{
    public function index(Request $request)
    {
        $hariIni = now()->toDateString();

        $totalArea = Area::count();
        $areaBersih = CeklisKebersihan::where('tanggal', $hariIni)
            ->where('status', 'selesai')
            ->distinct('area_id')
            ->count('area_id');

        $ceklisBaru = CeklisKebersihan::with(['pengguna', 'area'])
            ->where('tanggal', $hariIni)
            ->latest()
            ->take(10)
            ->get();

        $setoranTerbaru = SetoranSampah::with('pengguna')
            ->latest('tanggal')
            ->take(5)
            ->get();

        return view('dasbor.supervisor', compact('totalArea', 'areaBersih', 'ceklisBaru', 'setoranTerbaru'));
    }
}
