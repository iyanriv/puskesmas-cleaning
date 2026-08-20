<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nik', 20)->unique()->after('id');
            $table->foreignId('peran_id')->nullable()->after('password')->constrained('peran');
            $table->enum('shift', ['pagi', 'siang', 'malam'])->nullable()->after('peran_id');
            $table->foreignId('area_id')->nullable()->after('shift')->constrained('area');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email', 'email_verified_at']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->unique()->after('name');
            $table->timestamp('email_verified_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['peran_id']);
            $table->dropForeign(['area_id']);
            $table->dropColumn(['nik', 'peran_id', 'shift', 'area_id']);
        });
    }
};
