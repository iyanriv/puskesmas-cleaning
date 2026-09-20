<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ubah kolom lantai dari integer ke string agar bisa menyimpan
     * unit seperti "Lantai 1", "CSSD", "CPB", "CPT", "RWS"
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Ubah tipe kolom dulu ke string, baru bisa update data
        Schema::table('area', function (Blueprint $table) {
            $table->string('lantai', 20)->change();
        });

        // Konversi data lama angka → "Lantai X"
        DB::statement("UPDATE area SET lantai = CONCAT('Lantai ', lantai) WHERE lantai REGEXP '^[0-9]+$'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // Kembalikan ke integer (hanya ambil angka jika bisa)
        Schema::table('area', function (Blueprint $table) {
            $table->unsignedTinyInteger('lantai')->change();
        });
    }
};
