<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $pengguna = [
            ['name' => 'Administrator', 'nik' => '1234567890', 'peran_id' => 1, 'shift' => null, 'area_id' => null],
            ['name' => 'Ibu Rina (Supervisor)', 'nik' => '1111111111', 'peran_id' => 2, 'shift' => null, 'area_id' => null],
            ['name' => 'Bpk. Joko (PJ Lantai)', 'nik' => '4444444444', 'peran_id' => 3, 'shift' => 'pagi', 'area_id' => 1],
            ['name' => 'Bpk. Ahmad (CS)', 'nik' => '2222222222', 'peran_id' => 4, 'shift' => 'pagi', 'area_id' => 2],
            ['name' => 'Ibu Siti (CS)', 'nik' => '5555555555', 'peran_id' => 4, 'shift' => 'siang', 'area_id' => 3],
            ['name' => 'Bpk. Budi (Gudang)', 'nik' => '3333333333', 'peran_id' => 5, 'shift' => null, 'area_id' => null],
        ];

        foreach ($pengguna as $data) {
            User::create([
                'name' => $data['name'],
                'nik' => $data['nik'],
                'password' => Hash::make('password'),
                'peran_id' => $data['peran_id'],
                'shift' => $data['shift'],
                'area_id' => $data['area_id'],
            ]);
        }
    }
}
