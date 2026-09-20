<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk_kebersihan')->cascadeOnDelete();
            $table->unsignedTinyInteger('periode_bulan')->comment('1-12');
            $table->unsignedSmallInteger('periode_tahun');
            $table->date('tanggal')->nullable()->comment('Tanggal pencatatan stok masuk');
            $table->unsignedInteger('stok_masuk')->default(0)->comment('Jumlah stok / barang masuk');
            $table->timestamps();

            // Satu produk hanya boleh punya satu record per periode bulan+tahun
            $table->unique(['produk_id', 'periode_bulan', 'periode_tahun'], 'laporan_stok_produk_periode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_stok');
    }
};
