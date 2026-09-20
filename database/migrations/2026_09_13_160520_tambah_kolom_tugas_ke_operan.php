<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operan', function (Blueprint $table) {
            // Daftar item tugas yang dioper (checklist JSON)
            $table->json('tugas_items')->nullable()->after('catatan');

            // Status penyelesaian tugas operan
            $table->enum('status_penyelesaian', ['belum', 'selesai', 'dieskalasi'])
                  ->default('belum')
                  ->after('tugas_items');

            // Catatan alasan jika tugas dieskalasi ke shift berikutnya
            $table->text('catatan_eskalasi')->nullable()->after('status_penyelesaian');

            // Referensi ke operan induk (saat dieskalasi)
            $table->foreignId('parent_operan_id')
                  ->nullable()
                  ->after('catatan_eskalasi')
                  ->constrained('operan')
                  ->nullOnDelete();

            // Waktu penerima pertama kali membaca/membuka notifikasi
            $table->timestamp('dibaca_pada')->nullable()->after('parent_operan_id');

            // Tambahkan tempat_tugas dan waktu_jaga jika belum ada
            if (!Schema::hasColumn('operan', 'tempat_tugas')) {
                $table->string('tempat_tugas')->nullable()->after('waktu');
            }
            if (!Schema::hasColumn('operan', 'waktu_jaga')) {
                $table->string('waktu_jaga')->nullable()->after('tempat_tugas');
            }
        });
    }

    public function down(): void
    {
        Schema::table('operan', function (Blueprint $table) {
            $table->dropForeign(['parent_operan_id']);
            $table->dropColumn([
                'tugas_items',
                'status_penyelesaian',
                'catatan_eskalasi',
                'parent_operan_id',
                'dibaca_pada',
            ]);
        });
    }
};
