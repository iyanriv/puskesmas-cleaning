<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operan', function (Blueprint $table) {
            // FR-015: Tambah kembali status_alat sebagai JSON array
            // berisi daftar peralatan beserta kondisinya (tersedia/rusak)
            $table->json('status_alat')->nullable()->after('waktu_jaga');
        });
    }

    public function down(): void
    {
        Schema::table('operan', function (Blueprint $table) {
            $table->dropColumn('status_alat');
        });
    }
};
