<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setoran_sampah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis_sampah', ['plastik', 'kardus', 'logam', 'kaca', 'organik']);
            $table->decimal('berat_kg', 8, 2);
            $table->string('foto_timbangan')->nullable();
            $table->date('tanggal');
            $table->enum('status_validasi', ['menunggu', 'valid', 'ditolak'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setoran_sampah');
    }
};
