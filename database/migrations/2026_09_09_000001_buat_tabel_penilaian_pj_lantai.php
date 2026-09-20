<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penilaian_pj_lantai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('petugas_cs_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_petugas_cs');
            $table->string('lokasi_tugas');
            $table->string('nama_pj');
            $table->date('tanggal_penilaian');

            // Kehadiran / Absensi
            $table->integer('skor_disiplin');
            $table->integer('skor_komunikasi');

            // Kinerja / Pelayanan
            $table->integer('skor_sapu_pel');
            $table->integer('skor_lap_kaca_perabot');
            $table->integer('skor_tangga');
            $table->integer('skor_kontrol_kebersihan');
            $table->integer('skor_buang_sampah');
            $table->integer('skor_koordinasi');
            $table->integer('skor_tidak_tinggalkan_tugas');
            $table->integer('skor_toilet');
            $table->integer('skor_pelihara_sarana');
            $table->integer('skor_kerjasama');
            $table->integer('skor_kesopanan');
            $table->integer('skor_cekatan');

            // Mutu Pelayanan
            $table->integer('skor_sop');
            $table->integer('skor_kepuasan');

            // Evaluasi & Dokumentasi
            $table->text('masukan_evaluasi');
            $table->json('bukti_dokumentasi')->nullable();

            // Hasil kalkulasi
            $table->decimal('rata_rata', 5, 2);
            $table->string('kategori', 50);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaian_pj_lantai');
    }
};
