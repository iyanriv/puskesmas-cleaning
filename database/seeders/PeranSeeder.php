<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeranSeeder extends Seeder
{
    public function run(): void
    {
        $peran = ['admin', 'supervisor', 'pj_lantai', 'cs', 'gudang'];

        foreach ($peran as $nama) {
            DB::table('peran')->insert([
                'nama_peran' => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
