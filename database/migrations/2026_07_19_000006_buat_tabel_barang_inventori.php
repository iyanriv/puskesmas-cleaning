<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_inventori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->text('deskripsi')->nullable();
            $table->string('foto_barang')->nullable();
            $table->unsignedInteger('stok_saat_ini')->default(0);
            $table->string('satuan')->default('pcs');
            $table->unsignedInteger('stok_minimum')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang_inventori');
    }
};
