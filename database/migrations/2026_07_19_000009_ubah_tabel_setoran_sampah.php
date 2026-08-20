<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ubah tabel setoran_sampah:
     * - Ganti kolom jenis_sampah dari enum → json (multi-jenis)
     * - Hapus status_validasi (tidak dipakai)
     * - Tambah kolom lokasi_setor
     * - Tambah kolom catatan
     */
    public function up(): void
    {
        Schema::table('setoran_sampah', function (Blueprint $table) {
            // Hapus kolom lama
            $table->dropColumn(['jenis_sampah', 'status_validasi']);
        });

        Schema::table('setoran_sampah', function (Blueprint $table) {
            // Tambah kolom baru yang lebih fleksibel
            $table->json('jenis_sampah')->after('user_id');          // multi-jenis
            $table->string('lokasi_setor')->nullable()->after('jenis_sampah'); // lokasi/area setor
            $table->text('catatan')->nullable()->after('foto_timbangan');      // catatan opsional
        });
    }

    public function down(): void
    {
        Schema::table('setoran_sampah', function (Blueprint $table) {
            $table->dropColumn(['jenis_sampah', 'lokasi_setor', 'catatan']);
        });

        Schema::table('setoran_sampah', function (Blueprint $table) {
            $table->enum('jenis_sampah', ['plastik', 'kardus', 'logam', 'kaca', 'organik'])->after('user_id');
            $table->enum('status_validasi', ['menunggu', 'valid', 'ditolak'])->default('menunggu');
        });
    }
};
