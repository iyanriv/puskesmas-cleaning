<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Peran;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['peran', 'area']);
        
        if ($request->has('cari') && $request->cari != '') {
            $query->where('name', 'like', '%' . $request->cari . '%')
                  ->orWhere('nik', 'like', '%' . $request->cari . '%');
        }

        if ($request->has('peran_id') && $request->peran_id != '') {
            $query->where('peran_id', $request->peran_id);
        }

        $pengguna = $query->paginate(10);
        $peran = Peran::all();

        return view('admin.pengguna.index', compact('pengguna', 'peran'));
    }

    public function create()
    {
        $peran = Peran::all();
        $area = Area::all();
        return view('admin.pengguna.buat', compact('peran', 'area'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'nik' => 'required|unique:users',
            'password' => 'required|min:6',
            'peran_id' => 'required|exists:peran,id',
            'shift' => 'nullable|in:pagi,siang,malam',
            'area_id' => 'nullable|exists:area,id',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->nik = $request->nik;
        $user->password = Hash::make($request->password);
        $user->peran_id = $request->peran_id;
        $user->shift = $request->shift;
        $user->area_id = $request->area_id;
        $user->save();

        return redirect()->route('admin.pengguna.index')->with('sukses', 'Pengguna berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $pengguna = User::findOrFail($id);
        $peran = Peran::all();
        $area = Area::all();
        return view('admin.pengguna.ubah', compact('pengguna', 'peran', 'area'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'nik' => 'required|unique:users,nik,' . $user->id,
            'password' => 'nullable|min:6',
            'peran_id' => 'required|exists:peran,id',
            'shift' => 'nullable|in:pagi,siang,malam',
            'area_id' => 'nullable|exists:area,id',
        ]);

        $user->name = $request->name;
        $user->nik = $request->nik;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->peran_id = $request->peran_id;
        $user->shift = $request->shift;
        $user->area_id = $request->area_id;
        $user->save();

        return redirect()->route('admin.pengguna.index')->with('sukses', 'Pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.pengguna.index')->with('sukses', 'Pengguna berhasil dihapus.');
    }
}
