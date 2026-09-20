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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'area_id')) {
                if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                    $table->dropForeign(['area_id']);
                }
                $table->dropColumn('area_id');
            }
            if (Schema::hasColumn('users', 'shift')) {
                $table->dropColumn('shift');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('shift', ['pagi', 'siang', 'malam'])->nullable()->after('peran_id');
            $table->foreignId('area_id')->nullable()->after('shift')->constrained('area');
        });
    }
};
