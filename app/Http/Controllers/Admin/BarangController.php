<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarangInventori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangInventori::query();
        
        if ($request->has('cari') && $request->cari != '') {
            $query->where('nama_barang', 'like', '%' . $request->cari . '%');
        }

        $barang = $query->paginate(10);

        return view('admin.barang.index', compact('barang'));
    }

    public function create()
    {
        return view('admin.barang.buat');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required',
            'stok_saat_ini' => 'required|integer|min:0',
            'satuan' => 'required',
            'foto_barang' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable'
        ]);

        $barang = new BarangInventori();
        $barang->nama_barang = $request->nama_barang;
        $barang->deskripsi = $request->deskripsi;
        $barang->stok_saat_ini = $request->stok_saat_ini;
        $barang->satuan = $request->satuan;

        if ($request->hasFile('foto_barang')) {
            $path = $request->file('foto_barang')->store('barang', 'public');
            $barang->foto_barang = $path;
        }

        $barang->save();

        return redirect()->route('admin.barang.index')->with('sukses', 'Barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barang = BarangInventori::findOrFail($id);
        return view('admin.barang.ubah', compact('barang'));
    }

    public function update(Request $request, $id)
    {
        $barang = BarangInventori::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required',
            'stok_saat_ini' => 'required|integer|min:0',
            'satuan' => 'required',
            'foto_barang' => 'image|mimes:jpeg,png,jpg,gif|max:2048|nullable'
        ]);

        $barang->nama_barang = $request->nama_barang;
        $barang->deskripsi = $request->deskripsi;
        $barang->stok_saat_ini = $request->stok_saat_ini;
        $barang->satuan = $request->satuan;

        if ($request->hasFile('foto_barang')) {
            if ($barang->foto_barang && Storage::disk('public')->exists($barang->foto_barang)) {
                Storage::disk('public')->delete($barang->foto_barang);
            }
            $path = $request->file('foto_barang')->store('barang', 'public');
            $barang->foto_barang = $path;
        }

        $barang->save();

        return redirect()->route('admin.barang.index')->with('sukses', 'Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = BarangInventori::findOrFail($id);
        
        if ($barang->foto_barang && Storage::disk('public')->exists($barang->foto_barang)) {
            Storage::disk('public')->delete($barang->foto_barang);
        }
        
        $barang->delete();

        return redirect()->route('admin.barang.index')->with('sukses', 'Barang berhasil dihapus.');
    }
}
