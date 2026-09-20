<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

use App\Models\ProdukKebersihan;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $query = Area::query();
        
        if ($request->has('cari') && $request->cari != '') {
            $query->where('lantai', 'like', '%' . $request->cari . '%');
        }

        $area = $query->paginate(10);

        return view('admin.area.index', compact('area'));
    }

    public function create()
    {
        $daftarUnit = ProdukKebersihan::$daftarUnit;
        return view('admin.area.buat', compact('daftarUnit'));
    }

    public function store(Request $request)
    {
        $daftarUnit = ProdukKebersihan::$daftarUnit;

        $request->validate([
            'lantai' => 'required|in:' . implode(',', $daftarUnit),
        ]);

        $area = new Area();
        $area->lantai = $request->lantai;
        $area->save();

        return redirect()->route('admin.area.index')->with('sukses', 'Area lantai/unit berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $area = Area::findOrFail($id);
        $daftarUnit = ProdukKebersihan::$daftarUnit;
        return view('admin.area.ubah', compact('area', 'daftarUnit'));
    }

    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);

        $daftarUnit = ProdukKebersihan::$daftarUnit;

        $request->validate([
            'lantai' => 'required|in:' . implode(',', $daftarUnit),
        ]);

        $area->lantai = $request->lantai;
        $area->save();

        return redirect()->route('admin.area.index')->with('sukses', 'Area lantai/unit berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        
        // Cek jika area digunakan dalam data ceklis kebersihan
        if (\App\Models\CeklisKebersihan::where('area_id', $id)->exists()) {
            return redirect()->route('admin.area.index')->with('gagal', 'Area tidak dapat dihapus karena sudah memiliki riwayat ceklis kebersihan.');
        }

        try {
            $area->delete();
            return redirect()->route('admin.area.index')->with('sukses', 'Area berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('admin.area.index')->with('gagal', 'Area tidak dapat dihapus karena sedang digunakan dalam data lain (misal checklist).');
        }
    }
}
