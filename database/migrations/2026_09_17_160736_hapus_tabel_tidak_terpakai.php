<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus tabel yang sudah tidak digunakan:
     *
     * 1. password_resets     — duplikat dari password_reset_tokens (Laravel 10+)
     * 2. penilaian_kinerja   — sistem lama (legacy), sudah diganti penilaian_pj_lantai
     * 3. personal_access_tokens — Sanctum tidak dipakai di sistem ini
     */
    public function up(): void
    {
        // 1. Tabel password reset lama (sudah ada password_reset_tokens)
        Schema::dropIfExists('password_resets');

        // 2. Tabel penilaian kinerja legacy (0 baris, sudah diganti penilaian_pj_lantai)
        Schema::dropIfExists('penilaian_kinerja');

        // 3. Tabel Sanctum API tokens (tidak digunakan, sistem pakai session-based auth)
        Schema::dropIfExists('personal_access_tokens');
    }

    /**
     * Kembalikan tabel jika di-rollback.
     */
    public function down(): void
    {
        // password_resets
        Schema::create('password_resets', function ($table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // penilaian_kinerja
        Schema::create('penilaian_kinerja', function ($table) {
            $table->id();
            $table->foreignId('penilai_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('dinilai_id')->constrained('users')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('nilai_kebersihan');
            $table->integer('nilai_kedisiplinan');
            $table->integer('nilai_kerjasama');
            $table->integer('nilai_inisiatif');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // personal_access_tokens
        Schema::create('personal_access_tokens', function ($table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }
};
