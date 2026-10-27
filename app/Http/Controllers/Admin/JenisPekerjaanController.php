<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPekerjaan;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class JenisPekerjaanController extends Controller
{
    /**
     * Tampilkan daftar master jenis pekerjaan.
     */
    public function index(Request $request)
    {
        $query = JenisPekerjaan::query();

        if ($request->filled('cari')) {
            $query->where('nama_pekerjaan', 'like', '%' . trim($request->cari) . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_aktif', $request->status === 'aktif');
        }

        $jenisPekerjaan = $query->orderBy('urutan', 'asc')
            ->orderBy('nama_pekerjaan', 'asc')
            ->paginate(15)
            ->withQueryString();

        $totalAktif    = JenisPekerjaan::where('is_aktif', true)->count();
        $totalNonAktif = JenisPekerjaan::where('is_aktif', false)->count();

        return view('admin.jenis-pekerjaan.index', compact('jenisPekerjaan', 'totalAktif', 'totalNonAktif'));
    }

    /**
     * Simpan jenis pekerjaan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pekerjaan' => 'required|string|max:150|unique:jenis_pekerjaan,nama_pekerjaan',
            'deskripsi'      => 'nullable|string|max:500',
            'urutan'         => 'nullable|integer|min:0',
        ], [
            'nama_pekerjaan.required' => 'Nama jenis pekerjaan wajib diisi.',
            'nama_pekerjaan.unique'   => 'Jenis pekerjaan ini sudah terdaftar.',
            'nama_pekerjaan.max'      => 'Nama jenis pekerjaan maksimal 150 karakter.',
        ]);

        $maxUrutan = (int) JenisPekerjaan::max('urutan');

        JenisPekerjaan::create([
            'nama_pekerjaan' => trim($request->nama_pekerjaan),
            'deskripsi'      => $request->deskripsi ? trim($request->deskripsi) : null,
            'is_aktif'       => $request->has('is_aktif') ? (bool) $request->is_aktif : true,
            'urutan'         => $request->filled('urutan') ? (int) $request->urutan : ($maxUrutan + 1),
        ]);

        return redirect()->route('admin.jenis-pekerjaan.index')->with('sukses', 'Jenis pekerjaan baru berhasil ditambahkan.');
    }

    /**
     * Perbarui jenis pekerjaan.
     */
    public function update(Request $request, $id)
    {
        $pekerjaan = JenisPekerjaan::findOrFail($id);

        $request->validate([
            'nama_pekerjaan' => 'required|string|max:150|unique:jenis_pekerjaan,nama_pekerjaan,' . $id,
            'deskripsi'      => 'nullable|string|max:500',
            'urutan'         => 'nullable|integer|min:0',
        ], [
            'nama_pekerjaan.required' => 'Nama jenis pekerjaan wajib diisi.',
            'nama_pekerjaan.unique'   => 'Jenis pekerjaan ini sudah terdaftar.',
            'nama_pekerjaan.max'      => 'Nama jenis pekerjaan maksimal 150 karakter.',
        ]);

        $pekerjaan->update([
            'nama_pekerjaan' => trim($request->nama_pekerjaan),
            'deskripsi'      => $request->deskripsi ? trim($request->deskripsi) : null,
            'is_aktif'       => $request->has('is_aktif') ? (bool) $request->is_aktif : false,
            'urutan'         => $request->filled('urutan') ? (int) $request->urutan : $pekerjaan->urutan,
        ]);

        return redirect()->route('admin.jenis-pekerjaan.index')->with('sukses', 'Jenis pekerjaan berhasil diperbarui.');
    }

    /**
     * Toggle status aktif / non-aktif.
     */
    public function toggleStatus($id)
    {
        $pekerjaan = JenisPekerjaan::findOrFail($id);
        $pekerjaan->is_aktif = !$pekerjaan->is_aktif;
        $pekerjaan->save();

        $statusTeks = $pekerjaan->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('sukses', "Jenis pekerjaan '{$pekerjaan->nama_pekerjaan}' berhasil {$statusTeks}.");
    }

    /**
     * Hapus jenis pekerjaan.
     */
    public function destroy($id)
    {
        $pekerjaan = JenisPekerjaan::findOrFail($id);

        try {
            $nama = $pekerjaan->nama_pekerjaan;
            $pekerjaan->delete();
            return redirect()->route('admin.jenis-pekerjaan.index')->with('sukses', "Jenis pekerjaan '{$nama}' berhasil dihapus.");
        } catch (QueryException $e) {
            return redirect()->route('admin.jenis-pekerjaan.index')->with('gagal', 'Gagal menghapus jenis pekerjaan ini.');
        }
    }
}
