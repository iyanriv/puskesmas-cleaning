<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('operan', function (Blueprint $table) {
            $table->dropColumn('status_alat');
            $table->string('tempat_tugas')->nullable()->after('waktu');
            $table->string('waktu_jaga')->nullable()->after('tempat_tugas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operan', function (Blueprint $table) {
            $table->json('status_alat')->nullable();
            $table->dropColumn(['tempat_tugas', 'waktu_jaga']);
        });
    }
};
