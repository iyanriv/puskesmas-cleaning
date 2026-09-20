<?php

namespace App\Http\Controllers;

use App\Models\OperanShift;
use App\Models\Area;
use App\Models\User;
use Illuminate\Http\Request;

class OperanShiftController extends Controller
{
    /**
     * Tampilkan halaman utama operan shift.
     */
    public function index()
    {
        $pengguna = auth()->user();
        $hariIni  = now()->toDateString();

        // Jika admin atau supervisor, tampilkan view monitoring saja
        if (in_array($pengguna->peran?->nama_peran ?? '', ['admin', 'supervisor'])) {
            return $this->monitoringOperan();
        }

        // Daftar CS/PJ lain yang bisa jadi penerima (kecuali diri sendiri)
        $daftarPenerima = User::whereHas('peran', fn ($q) => $q->whereIn('nama_peran', ['cs', 'pj_lantai']))
            ->where('id', '!=', $pengguna->id)
            ->orderBy('name')
            ->get();

        // Daftar area/lantai untuk dropdown Tempat Tugas
        $daftarArea = Area::orderBy('lantai')->pluck('lantai');

        // Operan MASUK yang belum diterima (penerima = user login)
        $operanMasuk = OperanShift::with(['pengirim', 'parentOperan.pengirim'])
            ->where('penerima_id', $pengguna->id)
            ->where('status_terima', 'menunggu')
            ->latest()
            ->get();

        // Tugas dari shift sebelumnya yang SUDAH DITERIMA tapi belum selesai/dieskalasi
        // (bukan dari hari ini, dan statusnya belum selesai)
        $tugasAktif = OperanShift::with(['pengirim', 'parentOperan.pengirim'])
            ->where('penerima_id', $pengguna->id)
            ->where('status_terima', 'diterima')
            ->where('status_penyelesaian', 'belum')
            ->whereNotNull('tugas_items')
            ->latest()
            ->get();

        // Operan yang sudah dikirim hari ini oleh user ini
        $operanDikirim = OperanShift::with(['penerima', 'eskalasiBerikutnya.penerima'])
            ->where('pengirim_id', $pengguna->id)
            ->where('tanggal', $hariIni)
            ->latest()
            ->get();

        // Riwayat operan diterima hari ini
        $operanDiterima = OperanShift::with('pengirim')
            ->where('penerima_id', $pengguna->id)
            ->where('tanggal', $hariIni)
            ->where('status_terima', 'diterima')
            ->latest()
            ->get();

        return view('operan.index', compact(
            'pengguna',
            'daftarPenerima',
            'daftarArea',
            'operanMasuk',
            'tugasAktif',
            'operanDikirim',
            'operanDiterima'
        ));
    }

    /**
     * Monitoring operan shift untuk Admin/Supervisor (Read-only).
     */
    private function monitoringOperan()
    {
        $pengguna = auth()->user();
        $hariIni  = now()->toDateString();

        // Filter periode
        $periode = request('periode', 'hari_ini');
        
        switch ($periode) {
            case 'kemarin':
                $dari = now()->subDay()->toDateString();
                $sampai = now()->subDay()->toDateString();
                break;
            case 'minggu_ini':
                $dari = now()->startOfWeek()->toDateString();
                $sampai = now()->endOfWeek()->toDateString();
                break;
            case 'bulan_ini':
                $dari = now()->startOfMonth()->toDateString();
                $sampai = now()->endOfMonth()->toDateString();
                break;
            default: // hari_ini
                $dari = $hariIni;
                $sampai = $hariIni;
        }

        // Semua operan dalam periode (dengan pagination)
        $operanList = OperanShift::with(['pengirim', 'penerima', 'parentOperan'])
            ->whereBetween('tanggal', [$dari, $sampai])
            ->latest('tanggal')
            ->latest('waktu')
            ->paginate(20)
            ->appends(['periode' => $periode]);

        // Statistik
        $totalOperan = OperanShift::whereBetween('tanggal', [$dari, $sampai])->count();
        $menungguKonfirmasi = OperanShift::whereBetween('tanggal', [$dari, $sampai])
            ->where('status_terima', 'menunggu')->count();
        $tugasSelesai = OperanShift::whereBetween('tanggal', [$dari, $sampai])
            ->where('status_penyelesaian', 'selesai')->count();
        $tugasDieskalasi = OperanShift::whereBetween('tanggal', [$dari, $sampai])
            ->where('status_penyelesaian', 'dieskalasi')->count();

        return view('operan.monitoring', compact(
            'pengguna',
            'operanList',
            'periode',
            'totalOperan',
            'menungguKonfirmasi',
            'tugasSelesai',
            'tugasDieskalasi',
            'dari',
            'sampai'
        ));
    }

    /**
     * Kirim operan shift ke rekan.
     */
    public function kirim(Request $request)
    {
        $request->validate([
            'penerima_id'  => 'required|exists:users,id',
            'tempat_tugas' => 'required|string',
            'waktu_jaga'   => 'required|string',
            'catatan'      => 'required|string|max:1000',
            'tugas_items'  => 'nullable|array',
            'tugas_items.*'=> 'nullable|string|max:300',
        ], [
            'penerima_id.required'  => 'Pilih rekan penerima operan.',
            'penerima_id.exists'    => 'Penerima tidak ditemukan.',
            'tempat_tugas.required' => 'Pilih tempat tugas Anda.',
            'waktu_jaga.required'   => 'Pilih waktu jaga/shift Anda.',
            'catatan.required'      => 'Uraian kegiatan wajib diisi.',
        ]);

        $pengguna = auth()->user();

        // Cek apakah sudah ada operan ke orang yang sama hari ini
        $sudahAda = OperanShift::where('pengirim_id', $pengguna->id)
            ->where('penerima_id', $request->penerima_id)
            ->whereDate('tanggal', now()->toDateString())
            ->exists();

        if ($sudahAda) {
            return back()->with('gagal', 'Anda sudah mengirim operan ke rekan ini hari ini.');
        }

        // Bersihkan item tugas kosong
        $tugasItems = null;
        if ($request->filled('tugas_items')) {
            $items = array_filter(array_map('trim', $request->tugas_items));
            $tugasItems = !empty($items) ? array_values($items) : null;
        }

        OperanShift::create([
            'pengirim_id'        => $pengguna->id,
            'penerima_id'        => $request->penerima_id,
            'tanggal'            => now()->toDateString(),
            'waktu'              => now()->format('H:i:s'),
            'tempat_tugas'       => $request->tempat_tugas,
            'waktu_jaga'         => $request->waktu_jaga,
            'catatan'            => $request->catatan,
            'tugas_items'        => $tugasItems,
            'status_penyelesaian'=> 'belum',
            'status_terima'      => 'menunggu',
        ]);

        return back()->with('sukses', 'Operan shift berhasil dikirim! Menunggu konfirmasi penerima.');
    }

    /**
     * Penerima konfirmasi menerima operan.
     */
    public function terima($id)
    {
        $operan   = OperanShift::findOrFail($id);
        $pengguna = auth()->user();

        // Pastikan yang konfirmasi adalah penerima operan
        if ($operan->penerima_id !== $pengguna->id) {
            abort(403, 'Anda bukan penerima operan ini.');
        }

        if ($operan->status_terima === 'diterima') {
            return back()->with('info', 'Operan ini sudah diterima sebelumnya.');
        }

        $operan->update([
            'status_terima' => 'diterima',
            'dibaca_pada'   => now(),
        ]);

        $pesanTambahan = $operan->tugas_items ? ' Ada ' . count($operan->tugas_items) . ' tugas yang perlu diselesaikan.' : '';

        $namaPengirim = $operan->pengirim?->name ?? 'Rekan';
        return back()->with('sukses', 'Operan dari ' . $namaPengirim . ' berhasil diterima! ✅' . $pesanTambahan);
    }

    /**
     * Tandai tugas operan sebagai selesai dikerjakan.
     */
    public function selesaikan($id)
    {
        $operan   = OperanShift::findOrFail($id);
        $pengguna = auth()->user();

        if ($operan->penerima_id !== $pengguna->id) {
            abort(403, 'Anda bukan penerima operan ini.');
        }

        if ($operan->status_penyelesaian !== 'belum') {
            return back()->with('info', 'Status tugas ini sudah diperbarui sebelumnya.');
        }

        $operan->update(['status_penyelesaian' => 'selesai']);

        return back()->with('sukses', '✅ Tugas operan berhasil ditandai selesai dikerjakan!');
    }

    /**
     * Eskalasi tugas ke shift berikutnya karena tidak bisa dikerjakan.
     */
    public function eskalasi(Request $request, $id)
    {
        $request->validate([
            'penerima_eskalasi_id' => 'required|exists:users,id',
            'catatan_eskalasi'     => 'required|string|min:10|max:500',
        ], [
            'penerima_eskalasi_id.required' => 'Pilih rekan penerima eskalasi.',
            'catatan_eskalasi.required'     => 'Alasan kenapa tidak bisa mengerjakan wajib diisi.',
            'catatan_eskalasi.min'          => 'Alasan minimal 10 karakter.',
        ]);

        $operanAsal = OperanShift::findOrFail($id);
        $pengguna   = auth()->user();

        if ($operanAsal->penerima_id !== $pengguna->id) {
            abort(403, 'Anda bukan penerima operan ini.');
        }

        if ($operanAsal->status_penyelesaian !== 'belum') {
            return back()->with('info', 'Tugas ini sudah selesai atau sudah dieskalasi.');
        }

        // Tandai operan asal sebagai dieskalasi
        $operanAsal->update([
            'status_penyelesaian' => 'dieskalasi',
            'catatan_eskalasi'    => $request->catatan_eskalasi,
        ]);

        // Buat operan baru untuk shift berikutnya
        OperanShift::create([
            'pengirim_id'         => $pengguna->id,
            'penerima_id'         => $request->penerima_eskalasi_id,
            'tanggal'             => now()->toDateString(),
            'waktu'               => now()->format('H:i:s'),
            'tempat_tugas'        => $operanAsal->tempat_tugas,
            'waktu_jaga'          => $operanAsal->waktu_jaga,
            'catatan'             => $operanAsal->catatan,
            'tugas_items'         => $operanAsal->tugas_items,
            'status_penyelesaian' => 'belum',
            'status_terima'       => 'menunggu',
            'parent_operan_id'    => $operanAsal->id,
        ]);

        $penerima = User::find($request->penerima_eskalasi_id);

        return back()->with('sukses', '📤 Tugas berhasil diteruskan ke ' . ($penerima->name ?? 'rekan') . '. Pastikan mereka konfirmasi terima.');
    }

    /**
     * Detail operan (opsional, untuk melihat catatan & status alat lengkap).
     */
    public function detail($id)
    {
        $operan   = OperanShift::with(['pengirim', 'penerima', 'parentOperan.pengirim', 'eskalasiBerikutnya.penerima'])->findOrFail($id);
        $pengguna = auth()->user();

        // Admin dan supervisor bisa melihat semua
        $isAdminOrSupervisor = in_array($pengguna->peran?->nama_peran ?? '', ['admin', 'supervisor']);

        if (!$isAdminOrSupervisor && !in_array($pengguna->id, [$operan->pengirim_id, $operan->penerima_id])) {
            abort(403);
        }

        return view('operan.detail', compact('operan', 'pengguna'));
    }

    /**
     * Cetak laporan operan shift (PDF).
     */
    public function cetakLaporan()
    {
        $pengguna = auth()->user();
        
        // Filter periode
        $periode = request('periode', 'hari_ini');
        $hariIni = now()->toDateString();
        
        switch ($periode) {
            case 'kemarin':
                $dari = now()->subDay()->toDateString();
                $sampai = now()->subDay()->toDateString();
                break;
            case 'minggu_ini':
                $dari = now()->startOfWeek()->toDateString();
                $sampai = now()->endOfWeek()->toDateString();
                break;
            case 'bulan_ini':
                $dari = now()->startOfMonth()->toDateString();
                $sampai = now()->endOfMonth()->toDateString();
                break;
            default: // hari_ini
                $dari = $hariIni;
                $sampai = $hariIni;
        }

        // Semua operan dalam periode
        $operanList = OperanShift::with(['pengirim', 'penerima', 'parentOperan'])
            ->whereBetween('tanggal', [$dari, $sampai])
            ->latest('tanggal')
            ->latest('waktu')
            ->get();

        // Statistik
        $totalOperan = $operanList->count();
        $menungguKonfirmasi = $operanList->where('status_terima', 'menunggu')->count();
        $tugasSelesai = $operanList->where('status_penyelesaian', 'selesai')->count();
        $tugasDieskalasi = $operanList->where('status_penyelesaian', 'dieskalasi')->count();

        // Label periode untuk judul
        $labelPeriode = [
            'hari_ini' => 'Hari Ini',
            'kemarin' => 'Kemarin',
            'minggu_ini' => 'Minggu Ini',
            'bulan_ini' => 'Bulan Ini',
        ][$periode] ?? 'Hari Ini';

        return view('operan.cetak', compact(
            'pengguna',
            'operanList',
            'periode',
            'labelPeriode',
            'totalOperan',
            'menungguKonfirmasi',
            'tugasSelesai',
            'tugasDieskalasi',
            'dari',
            'sampai'
        ));
    }
}