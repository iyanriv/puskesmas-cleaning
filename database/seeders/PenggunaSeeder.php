<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PenggunaSeeder extends Seeder
{
    public function run(): void
    {
        $roles = \App\Models\Peran::pluck('id', 'nama_peran');

        $pengguna = [
            ['name' => 'Administrator', 'nik' => '1234567890', 'role' => 'admin'],
            ['name' => 'Ibu Rina (Supervisor)', 'nik' => '1111111111', 'role' => 'supervisor'],
            ['name' => 'Bpk. Joko (PJ Lantai)', 'nik' => '4444444444', 'role' => 'pj_lantai'],
            ['name' => 'Bpk. Ahmad (CS)', 'nik' => '2222222222', 'role' => 'cs'],
            ['name' => 'Ibu Siti (CS)', 'nik' => '5555555555', 'role' => 'cs'],
            ['name' => 'Bpk. Budi (Gudang)', 'nik' => '3333333333', 'role' => 'gudang'],
        ];

        foreach ($pengguna as $data) {
            User::firstOrCreate(
                ['nik' => $data['nik']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password'),
                    'peran_id' => $roles[$data['role']] ?? null,
                ]
            );
        }
    }
}
