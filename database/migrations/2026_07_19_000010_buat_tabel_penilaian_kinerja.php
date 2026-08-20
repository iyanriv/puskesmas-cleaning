<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian_kinerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilai_id')->constrained('users')->cascadeOnDelete();  // Supervisor
            $table->foreignId('dinilai_id')->constrained('users')->cascadeOnDelete();  // CS / PJ
            $table->date('tanggal');
            $table->tinyInteger('nilai_kebersihan')->comment('1-5');     // Kebersihan area
            $table->tinyInteger('nilai_kedisiplinan')->comment('1-5');   // Kehadiran & ketepatan waktu
            $table->tinyInteger('nilai_kerjasama')->comment('1-5');      // Kerjasama tim
            $table->tinyInteger('nilai_inisiatif')->comment('1-5');      // Inisiatif & tanggap
            $table->text('catatan')->nullable();                          // Catatan tambahan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian_kinerja');
    }
};
