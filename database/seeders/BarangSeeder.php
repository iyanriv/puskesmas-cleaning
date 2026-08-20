<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        $barang = [
            ['nama_barang' => 'Sabun Cuci', 'deskripsi' => 'Sabun pembersih lantai', 'stok_saat_ini' => 20, 'satuan' => 'botol', 'stok_minimum' => 5],
            ['nama_barang' => 'Tissue', 'deskripsi' => 'Tissue toilet', 'stok_saat_ini' => 50, 'satuan' => 'pak', 'stok_minimum' => 10],
            ['nama_barang' => 'Sapu', 'deskripsi' => 'Sapu lantai', 'stok_saat_ini' => 8, 'satuan' => 'pcs', 'stok_minimum' => 3],
            ['nama_barang' => 'Pel', 'deskripsi' => 'Pel lantai', 'stok_saat_ini' => 6, 'satuan' => 'pcs', 'stok_minimum' => 3],
            ['nama_barang' => 'Kantong Sampah', 'deskripsi' => 'Kantong sampah hitam', 'stok_saat_ini' => 100, 'satuan' => 'roll', 'stok_minimum' => 20],
        ];

        foreach ($barang as $item) {
            DB::table('barang_inventori')->insert([
                'nama_barang' => $item['nama_barang'],
                'deskripsi' => $item['deskripsi'],
                'stok_saat_ini' => $item['stok_saat_ini'],
                'satuan' => $item['satuan'],
                'stok_minimum' => $item['stok_minimum'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
