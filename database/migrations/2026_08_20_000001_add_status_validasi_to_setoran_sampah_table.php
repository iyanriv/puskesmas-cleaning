<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('setoran_sampah', function (Blueprint $table) {
            $table->enum('status_validasi', ['menunggu', 'valid', 'ditolak'])
                  ->default('menunggu')
                  ->after('tanggal');
            $table->text('catatan_validasi')->nullable()->after('status_validasi');
            $table->foreignId('validator_id')->nullable()
                  ->constrained('users')->nullOnDelete()
                  ->after('catatan_validasi');
        });
    }

    public function down(): void
    {
        Schema::table('setoran_sampah', function (Blueprint $table) {
            $table->dropForeign(['validator_id']);
            $table->dropColumn(['status_validasi', 'catatan_validasi', 'validator_id']);
        });
    }
};
