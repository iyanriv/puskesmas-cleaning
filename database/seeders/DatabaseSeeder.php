<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Peran;
use App\Models\Area;
use App\Models\BarangInventori;
use App\Models\CeklisKebersihan;
use App\Models\PermintaanBarang;
use App\Models\SetoranSampah;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jalankan Master Seeders
        $this->call([
            PeranSeeder::class,
            AreaSeeder::class,
            PenggunaSeeder::class,
            BarangInventoriSeeder::class,
        ]);

        // Ambil referensi yang sudah di-seed
        $cs      = User::whereHas('peran', fn($q) => $q->where('nama_peran', 'cs'))->first();
        $spv     = User::whereHas('peran', fn($q) => $q->where('nama_peran', 'supervisor'))->first();
        $admin   = User::whereHas('peran', fn($q) => $q->where('nama_peran', 'admin'))->first();
        $area1   = Area::first();
        $brg     = BarangInventori::first();

        // 2. Data Ceklis Kebersihan
        if ($cs && $area1) {
            CeklisKebersihan::create([
                'user_id'     => $cs->id,
                'area_id'     => $area1->id,
                'tanggal'     => now()->toDateString(),
                'status'      => 'selesai',
                'foto_before' => 'dummy_before.jpg',
                'foto_after'  => 'dummy_after.jpg',
            ]);
        }

        // 3. Permintaan Barang
        if ($cs && $brg) {
            PermintaanBarang::create([
                'user_id'        => $cs->id,
                'barang_id'      => $brg->id,
                'jumlah'         => 2,
                'status_request' => 'pending',
                'waktu_request'  => now(),
            ]);
        }

        // 4. Setoran Bank Sampah
        if ($cs) {
            SetoranSampah::create([
                'user_id'      => $cs->id,
                'jenis_sampah' => ['Botol Plastik', 'Kardus'],
                'lokasi_setor' => 'Lantai 1',
                'catatan'      => 'Botol plastik dan kardus bekas',
                'tanggal'      => now()->toDateString(),
            ]);
        }

        // 5. Tugas Mingguan CS
        if ($cs) {
            \App\Models\TugasMingguan::create([
                'user_id'          => $cs->id,
                'tanggal'          => now()->toDateString(),
                'waktu_pelaporan'  => now()->format('H:i'),
                'rincian_kegiatan' => 'Pembersihan kaca luar dan polishing lantai ruang tunggu utama',
                'status'           => 'selesai',
            ]);
        }

        // 6. Penilaian PJ Lantai
        if ($cs) {
            \App\Models\PenilaianPjLantai::create([
                'petugas_cs_id'              => $cs->id,
                'nama_petugas_cs'            => $cs->name,
                'lokasi_tugas'               => 'Lantai 1 - Rawat Jalan',
                'nama_pj'                    => 'Bpk. Joko (PJ Lantai)',
                'tanggal_penilaian'          => now()->toDateString(),
                'skor_disiplin'              => 90,
                'skor_komunikasi'            => 88,
                'skor_sapu_pel'              => 92,
                'skor_lap_kaca_perabot'      => 85,
                'skor_tangga'                => 88,
                'skor_kontrol_kebersihan'    => 90,
                'skor_buang_sampah'          => 95,
                'skor_koordinasi'            => 87,
                'skor_tidak_tinggalkan_tugas' => 90,
                'skor_toilet'                => 88,
                'skor_pelihara_sarana'       => 85,
                'skor_kerjasama'             => 90,
                'skor_kesopanan'             => 92,
                'skor_cekatan'               => 88,
                'skor_sop'                   => 90,
                'skor_kepuasan'              => 92,
                'masukan_evaluasi'           => 'Pekerjaan sangat baik dan rapi, tingkatkan terus koordinasi.',
                'rata_rata'                  => 89.38,
                'kategori'                   => 'Sangat Baik',
            ]);
        }
    }
}
