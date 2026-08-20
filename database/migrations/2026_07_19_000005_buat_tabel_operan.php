<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('penerima_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('waktu');
            $table->json('status_alat')->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status_terima', ['menunggu', 'diterima'])->default('menunggu');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operan');
    }
};
