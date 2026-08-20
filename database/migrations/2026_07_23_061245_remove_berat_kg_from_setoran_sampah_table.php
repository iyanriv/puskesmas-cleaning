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
        Schema::table('setoran_sampah', function (Blueprint $table) {
            $table->dropColumn('berat_kg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setoran_sampah', function (Blueprint $table) {
            $table->decimal('berat_kg', 8, 2)->nullable();
        });
    }
};
