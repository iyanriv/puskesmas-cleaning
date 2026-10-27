<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom kategori pada barang_inventori jika belum ada
        if (Schema::hasTable('barang_inventori') && ! Schema::hasColumn('barang_inventori', 'kategori')) {
            Schema::table('barang_inventori', function (Blueprint $table) {
                $table->string('kategori', 50)->nullable()->default('Pembersih & Sanitasi')->after('nama_barang');
            });
        }

        // 2. Buat tabel mutasi_stok untuk riwayat mutasi / ledger stok
        if (! Schema::hasTable('mutasi_stok')) {
            Schema::create('mutasi_stok', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_transaksi', 50)->unique();
                $table->foreignId('barang_id')->constrained('barang_inventori')->cascadeOnDelete();
                $table->date('tanggal');
                $table->string('jenis_transaksi', 30); // stok_masuk, distribusi, pemakaian_unit, stok_awal, penyesuaian
                $table->unsignedInteger('jumlah_masuk')->default(0);
                $table->unsignedInteger('jumlah_keluar')->default(0);
                $table->integer('saldo_sebelumnya')->default(0);
                $table->integer('saldo_setelahnya')->default(0);
                $table->string('tipe_stok', 20)->default('gudang'); // gudang, unit
                $table->string('unit', 100)->nullable(); // lokasi/unit pemakai
                $table->string('sumber_pasokan', 150)->nullable(); // Dinas Kesehatan, Pengadaan, Vendor, dll
                $table->string('nomor_referensi', 100)->nullable(); // no nota, invoice, id permintaan
                $table->text('keterangan')->nullable();
                $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['barang_id', 'tanggal']);
                $table->index('jenis_transaksi');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mutasi_stok');

        if (Schema::hasTable('barang_inventori') && Schema::hasColumn('barang_inventori', 'kategori')) {
            Schema::table('barang_inventori', function (Blueprint $table) {
                $table->dropColumn('kategori');
            });
        }
    }
};
