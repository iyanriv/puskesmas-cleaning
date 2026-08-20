<?php

namespace App\Http\Controllers\Dasbor;

use App\Http\Controllers\Controller;
use App\Models\CeklisKebersihan;
use App\Models\PermintaanBarang;
use App\Models\SetoranSampah;
use Illuminate\Http\Request;

class DasborCsController extends Controller
{
    public function index(Request $request)
    {
        $pengguna = $request->user();
        $hariIni = now()->toDateString();

        $totalCeklis = CeklisKebersihan::where('user_id', $pengguna->id)
            ->where('tanggal', $hariIni)
            ->count();

        $selesaiCeklis = CeklisKebersihan::where('user_id', $pengguna->id)
            ->where('tanggal', $hariIni)
            ->where('status', 'selesai')
            ->count();

        $persentase = $totalCeklis > 0 ? round(($selesaiCeklis / $totalCeklis) * 100) : 0;

        $aktivitasTerakhir = collect()
            ->merge(
                CeklisKebersihan::with('area')
                    ->where('user_id', $pengguna->id)
                    ->latest()
                    ->take(3)
                    ->get()
                    ->map(fn ($c) => [
                        'teks' => 'Ceklis '.$c->area->nama_ruangan.' '.($c->status === 'selesai' ? 'selesai' : 'proses'),
                        'waktu' => $c->updated_at->format('H:i'),
                        'warna' => $c->status === 'selesai' ? 'hijau' : 'kuning',
                    ])
            )
            ->merge(
                PermintaanBarang::with('barang')
                    ->where('user_id', $pengguna->id)
                    ->latest()
                    ->take(2)
                    ->get()
                    ->map(fn ($p) => [
                        'teks' => 'Minta barang: '.$p->barang->nama_barang.' x'.$p->jumlah,
                        'waktu' => $p->created_at->format('H:i'),
                        'warna' => 'kuning',
                    ])
            )
            ->merge(
                SetoranSampah::where('user_id', $pengguna->id)
                    ->latest()
                    ->take(2)
                    ->get()
                    ->map(fn ($s) => [
                        'teks' => 'Setor sampah: '.$s->berat_kg.' KG '.ucfirst($s->jenisSampahTeks()),
                        'waktu' => $s->created_at->format('H:i'),
                        'warna' => 'hijau',
                    ])
            )
            ->sortByDesc('waktu')
            ->take(5)
            ->values();

        return view('dasbor.cs', compact('pengguna', 'totalCeklis', 'selesaiCeklis', 'persentase', 'aktivitasTerakhir'));
    }
}
