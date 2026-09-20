<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hapus kolom nama_ruangan dari tabel area, hanya gunakan kolom lantai.
     */
    public function up(): void
    {
        Schema::table('area', function (Blueprint $table) {
            if (Schema::hasColumn('area', 'nama_ruangan')) {
                $table->dropColumn('nama_ruangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('area', function (Blueprint $table) {
            $table->string('nama_ruangan')->nullable();
        });
    }
};
