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
use App\Models\PenilaianKinerja;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Peran
        $pAdmin = Peran::create(['nama_peran' => 'admin']);
        $pSpv   = Peran::create(['nama_peran' => 'supervisor']);
        $pPj    = Peran::create(['nama_peran' => 'pj_lantai']);
        $pCs    = Peran::create(['nama_peran' => 'cs']);
        $pGdg   = Peran::create(['nama_peran' => 'gudang']);

        // 2. Area
        $area1 = Area::create(['nama_ruangan' => 'Lobi Utama', 'lantai' => 1]);
        $area2 = Area::create(['nama_ruangan' => 'Poli Umum', 'lantai' => 1]);

        // 3. User
        $admin = User::create([
            'name' => 'Admin Sistem', 'nik' => '1111', 'password' => Hash::make('password'), 'peran_id' => $pAdmin->id
        ]);
        $spv = User::create([
            'name' => 'Pak Supervisor', 'nik' => '2222', 'password' => Hash::make('password'), 'peran_id' => $pSpv->id
        ]);
        $cs = User::create([
            'name' => 'Budi CS', 'nik' => '3333', 'password' => Hash::make('password'), 'peran_id' => $pCs->id, 'shift' => 'pagi', 'area_id' => $area1->id
        ]);
        $gudang = User::create([
            'name' => 'Mas Gudang', 'nik' => '4444', 'password' => Hash::make('password'), 'peran_id' => $pGdg->id
        ]);

        // 4. Barang Inventori
        $brg = BarangInventori::create([
            'nama_barang' => 'Sapu Ijuk', 'stok_saat_ini' => 10, 'satuan' => 'pcs', 'stok_minimum' => 2
        ]);
        BarangInventori::create([
            'nama_barang' => 'Pembersih Kaca', 'stok_saat_ini' => 5, 'satuan' => 'botol', 'stok_minimum' => 5
        ]);

        // 5. Ceklis
        CeklisKebersihan::create([
            'user_id' => $cs->id, 'area_id' => $area1->id, 'tanggal' => now()->toDateString(), 'status' => 'selesai',
            'foto_before' => 'dummy.jpg', 'foto_after' => 'dummy2.jpg'
        ]);

        // 6. Permintaan Barang
        PermintaanBarang::create([
            'user_id' => $cs->id, 'barang_id' => $brg->id, 'jumlah' => 2, 'status_request' => 'pending', 'waktu_request' => now()
        ]);

        // 7. Sampah
        SetoranSampah::create([
            'user_id' => $cs->id, 'jenis_sampah' => ['Botol Plastik', 'Kardus'], 'lokasi_setor' => 'Lantai 1',
            'berat_kg' => 2.5, 'tanggal' => now()->toDateString()
        ]);

        // 8. Penilaian
        PenilaianKinerja::create([
            'penilai_id' => $spv->id, 'dinilai_id' => $cs->id, 'tanggal' => now()->toDateString(),
            'nilai_kebersihan' => 4, 'nilai_kedisiplinan' => 4, 'nilai_kerjasama' => 5, 'nilai_inisiatif' => 4
        ]);
    }
}
