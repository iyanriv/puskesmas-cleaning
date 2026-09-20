<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk_kebersihan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 30)->unique()->comment('Kode unik barang kebersihan');
            $table->string('nama_barang', 150);
            $table->string('satuan', 30)->default('buah')
                  ->comment('buah, botol, galon, pak, bungkus, pouch, roll, pasang, kaleng');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk_kebersihan');
    }
};
