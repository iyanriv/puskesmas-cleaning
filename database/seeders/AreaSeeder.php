<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $area = [
            ['nama_ruangan' => 'Lobby Utama', 'lantai' => 1],
            ['nama_ruangan' => 'Poli KIA', 'lantai' => 1],
            ['nama_ruangan' => 'Poli Gigi', 'lantai' => 2],
            ['nama_ruangan' => 'Apotek', 'lantai' => 1],
            ['nama_ruangan' => 'Ruang Tunggu', 'lantai' => 2],
            ['nama_ruangan' => 'Toilet Lantai 1', 'lantai' => 1],
            ['nama_ruangan' => 'Toilet Lantai 2', 'lantai' => 2],
        ];

        foreach ($area as $item) {
            DB::table('area')->insert([
                'nama_ruangan' => $item['nama_ruangan'],
                'lantai' => $item['lantai'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
