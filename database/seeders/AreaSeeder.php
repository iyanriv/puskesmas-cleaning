<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $daftarUnit = ['Lantai 1', 'Lantai 2', 'Lantai 3', 'Lantai 4', 'Lantai 5', 'Lantai 6', 'CSSD', 'CPB', 'CPT', 'RWS'];

        foreach ($daftarUnit as $unit) {
            DB::table('area')->updateOrInsert(
                ['lantai' => $unit],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
