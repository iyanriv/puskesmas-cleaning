<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang_inventori', function (Blueprint $table) {
            // Kode barang — disamakan dengan produk_kebersihan.kode_barang
            $table->string('kode_barang', 30)->nullable()->unique()->after('id')
                  ->comment('Kode barang, sama dengan produk_kebersihan.kode_barang');

            // FK ke produk_kebersihan — jembatan antara sistem inventori dan laporan stok
            $table->foreignId('produk_id')->nullable()->after('kode_barang')
                  ->constrained('produk_kebersihan')
                  ->nullOnDelete()
                  ->comment('Relasi ke produk_kebersihan untuk pencatatan laporan stok otomatis');
        });
    }

    public function down(): void
    {
        Schema::table('barang_inventori', function (Blueprint $table) {
            $table->dropForeign(['produk_id']);
            $table->dropColumn(['kode_barang', 'produk_id']);
        });
    }
};
