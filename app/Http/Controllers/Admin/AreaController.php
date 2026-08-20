<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $query = Area::query();
        
        if ($request->has('cari') && $request->cari != '') {
            $query->where('nama_ruangan', 'like', '%' . $request->cari . '%')
                  ->orWhere('lantai', 'like', '%' . $request->cari . '%');
        }

        $area = $query->paginate(10);

        return view('admin.area.index', compact('area'));
    }

    public function create()
    {
        return view('admin.area.buat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required',
            'lantai' => 'required|integer',
        ]);

        $area = new Area();
        $area->nama_ruangan = $request->nama_ruangan;
        $area->lantai = $request->lantai;
        $area->save();

        return redirect()->route('admin.area.index')->with('sukses', 'Area berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $area = Area::findOrFail($id);
        return view('admin.area.ubah', compact('area'));
    }

    public function update(Request $request, $id)
    {
        $area = Area::findOrFail($id);

        $request->validate([
            'nama_ruangan' => 'required',
            'lantai' => 'required|integer',
        ]);

        $area->nama_ruangan = $request->nama_ruangan;
        $area->lantai = $request->lantai;
        $area->save();

        return redirect()->route('admin.area.index')->with('sukses', 'Area berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $area = Area::findOrFail($id);
        
        // Cek jika area digunakan oleh user
        if (User::where('area_id', $id)->exists()) {
            return redirect()->route('admin.area.index')->with('gagal', 'Area tidak dapat dihapus karena sedang digunakan oleh pengguna.');
        }

        try {
            $area->delete();
            return redirect()->route('admin.area.index')->with('sukses', 'Area berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('admin.area.index')->with('gagal', 'Area tidak dapat dihapus karena sedang digunakan dalam data lain (misal checklist).');
        }
    }
}
