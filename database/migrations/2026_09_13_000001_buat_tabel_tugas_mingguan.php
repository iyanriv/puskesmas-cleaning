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
        Schema::create('tugas_mingguan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu_pelaporan');
            $table->text('rincian_kegiatan');
            $table->json('foto_sebelum')->nullable(); // Array of image paths (up to 5 photos)
            $table->string('foto_setelah')->nullable(); // 1 image path
            $table->enum('status', ['proses', 'selesai', 'disetujui'])->default('proses');
            $table->text('catatan_supervisor')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('diverifikasi_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas_mingguan');
    }
};
