<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Peran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PetugasCsSeeder extends Seeder
{
    public function run(): void
    {
        $peranCs = Peran::where('nama_peran', 'cs')->first();

        if (! $peranCs) {
            $this->command->error('Peran "cs" tidak ditemukan di database!');
            return;
        }

        $petugasCs = [
            ['name' => 'Yanto',                    'nik' => '600006'],
            ['name' => 'Taufik Hidayat',            'nik' => '500007'],
            ['name' => 'Sutriyono',                 'nik' => '500010'],
            ['name' => 'Achmad Riansyah',           'nik' => '600005'],
            ['name' => 'Nursila',                   'nik' => '500005'],
            ['name' => 'Dhea Nanda Alfitra Dina',   'nik' => '201944'],
            ['name' => 'Abdul Azis Muslim',          'nik' => '201650'],
            ['name' => 'Muhamad Asril',              'nik' => '202231'],
            ['name' => 'Wawan Suhandi',              'nik' => '201926'],
            ['name' => 'Toto Suprianto',             'nik' => '201918'],
            ['name' => 'Devina Shintya Dewi',        'nik' => '201945'],
            ['name' => 'Isman Maulana',              'nik' => '860121'],
            ['name' => 'Erlinda',                    'nik' => '202409'],
            ['name' => 'Ade Reni Aprina',            'nik' => '202505'],
            ['name' => 'Endang Kurniawan',           'nik' => '202506'],
            ['name' => 'Dian Rifan Abdulah',         'nik' => '202601'],
            ['name' => 'Laelatul Khoiriyah',         'nik' => '202602'],
        ];

        $berhasil = 0;
        $sudahAda = 0;

        foreach ($petugasCs as $data) {
            $existing = User::where('nik', $data['nik'])->first();

            if ($existing) {
                $sudahAda++;
                $this->command->warn("  Skip: {$data['name']} (NIK {$data['nik']}) sudah ada.");
                continue;
            }

            User::create([
                'name'     => $data['name'],
                'nik'      => $data['nik'],
                'password' => Hash::make($data['nik']), // password = ID/NIK
                'peran_id' => $peranCs->id,
            ]);

            $berhasil++;
            $this->command->info("  ✓ Ditambahkan: {$data['name']} (NIK {$data['nik']})");
        }

        $this->command->newLine();
        $this->command->info("Selesai: {$berhasil} petugas CS berhasil ditambahkan, {$sudahAda} sudah ada.");
    }
}
