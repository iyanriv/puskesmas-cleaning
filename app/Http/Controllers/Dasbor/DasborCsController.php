<?php

namespace App\Http\Controllers\Dasbor;

use App\Http\Controllers\Controller;
use App\Models\CeklisKebersihan;
use App\Models\TugasMingguan;
use App\Models\OperanShift;
use App\Models\PermintaanBarang;
use App\Models\SetoranSampah;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DasborCsController extends Controller
{
    public function index(Request $request)
    {
        $pengguna = $request->user();
        $hariIni = now()->toDateString();

        $totalTugasMingguan = TugasMingguan::where('user_id', $pengguna->id)->count();
        $selesaiTugasMingguan = TugasMingguan::where('user_id', $pengguna->id)
            ->whereIn('status', ['selesai', 'disetujui'])
            ->count();

        $persentase = $totalTugasMingguan > 0 ? round(($selesaiTugasMingguan / $totalTugasMingguan) * 100) : 100;

        // FR-017: Jumlah operan masuk yang belum diterima — untuk notifikasi di dasbor
        $operanMenunggu = OperanShift::where('penerima_id', $pengguna->id)
            ->where('status_terima', 'menunggu')
            ->with('pengirim')
            ->latest()
            ->get();

        $aktivitasTerakhir = collect()
            ->merge(
                TugasMingguan::where('user_id', $pengguna->id)
                    ->latest()
                    ->take(3)
                    ->get()
                    ->map(fn ($t) => [
                        'teks' => 'Tugas Mingguan: ' . Str::limit($t->rincian_kegiatan, 25) . ' (' . ucfirst($t->status) . ')',
                        'waktu' => $t->updated_at->format('H:i'),
                        'warna' => $t->status === 'disetujui' || $t->status === 'selesai' ? 'hijau' : 'kuning',
                    ])
            )
            ->merge(
                PermintaanBarang::with('barang')
                    ->where('user_id', $pengguna->id)
                    ->latest()
                    ->take(2)
                    ->get()
                    ->map(fn ($p) => [
                        'teks' => 'Minta barang: ' . ($p->barang?->nama_barang ?? 'Barang') . ' x' . $p->jumlah,
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
                        'teks' => 'Setor sampah: '.$s->jenisSampahTeks(),
                        'waktu' => $s->created_at->format('H:i'),
                        'warna' => 'hijau',
                    ])
            )
            ->sortByDesc('waktu')
            ->take(5)
            ->values();

        return view('dasbor.cs', compact(
            'pengguna', 'totalTugasMingguan', 'selesaiTugasMingguan', 'persentase',
            'aktivitasTerakhir', 'operanMenunggu'
        ));
    }
}
