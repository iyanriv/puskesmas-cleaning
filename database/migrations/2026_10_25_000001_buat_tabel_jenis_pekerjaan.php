<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jenis_pekerjaan')) {
            Schema::create('jenis_pekerjaan', function (Blueprint $table) {
                $table->id();
                $table->string('nama_pekerjaan', 150)->unique();
                $table->text('deskripsi')->nullable();
                $table->boolean('is_aktif')->default(true);
                $table->integer('urutan')->default(0);
                $table->timestamps();
            });

            // Seed 30 jenis pekerjaan default
            $pekerjaanAwal = [
                'Pembersihan Kaca',
                'Pembersihan Dinding',
                'Pembersihan Plafon',
                'Pembersihan Tangga dan Bordes',
                'Pembersihan Lantai Berkala',
                'Pembersihan Keramik dan Nat',
                'Pembersihan Pintu dan Kusen',
                'Pembersihan Railing dan Pegangan Tangan',
                'Pembersihan Area Tinggi (High Dusting)',
                'Pembersihan Sarang Laba-laba',
                'Pembersihan Ventilasi dan Kisi-kisi',
                'Pembersihan Lampu dan Perlengkapan',
                'Pembersihan Furnitur dan Perabot',
                'Pembersihan Kursi dan Sofa',
                'Pembersihan Karpet',
                'Pembersihan Toilet Mendalam (Deep Cleaning)',
                'Pembersihan Wastafel dan Perlengkapan Sanitasi',
                'Pembersihan Saluran Air',
                'Pembersihan Lobi dan Ruang Tunggu',
                'Pembersihan Koridor dan Selasar',
                'Pembersihan Halaman dan Area Luar',
                'Pembersihan Area Parkir',
                'Pembersihan Taman dan Area Hijau',
                'Pembersihan Tempat Sampah',
                'Pembersihan Noda Membandel',
                'Pembersihan Menyeluruh (General Cleaning)',
                'Pembersihan Setelah Kegiatan (Post-Event Cleaning)',
                'Pembersihan Setelah Renovasi (Post-Construction Cleaning)',
                'Disinfeksi Permukaan Sesuai SOP',
                'Pembersihan Insidental atau Khusus',
            ];

            $now = now();
            $dataInsert = [];
            foreach ($pekerjaanAwal as $index => $nama) {
                $dataInsert[] = [
                    'nama_pekerjaan' => $nama,
                    'deskripsi'      => null,
                    'is_aktif'       => true,
                    'urutan'         => $index + 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }

            DB::table('jenis_pekerjaan')->insert($dataInsert);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_pekerjaan');
    }
};
