<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_barang', function (Blueprint $table) {
            // Area/lantai tempat petugas bertugas saat mengajukan permintaan
            $table->foreignId('area_id')
                ->nullable()
                ->after('barang_id')
                ->constrained('area')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_barang', function (Blueprint $table) {
            $table->dropConstrainedForeignId('area_id');
        });
    }
};
