<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $isMySql = DB::getDriverName() === 'mysql';

        if ($isMySql) {
            // Langkah 1: Sementara ubah ke VARCHAR agar tidak ada constraint enum saat migrasi data
            DB::statement("ALTER TABLE permintaan_barang MODIFY COLUMN status_request VARCHAR(30) NOT NULL DEFAULT 'menunggu_spv'");
        }

        // Langkah 2: Migrate data lama: 'pending' → 'menunggu_spv'
        DB::table('permintaan_barang')
            ->where('status_request', 'pending')
            ->update(['status_request' => 'menunggu_spv']);

        if ($isMySql) {
            // Langkah 3: Terapkan enum baru (setelah data sudah bersih)
            DB::statement("ALTER TABLE permintaan_barang MODIFY COLUMN status_request ENUM(
                'menunggu_spv',
                'disetujui_spv',
                'ditolak_spv',
                'disetujui',
                'ditolak'
            ) NOT NULL DEFAULT 'menunggu_spv'");
        }

        // Langkah 4: Tambah kolom supervisor
        Schema::table('permintaan_barang', function (Blueprint $table) {
            $table->foreignId('supervisor_id')
                ->nullable()
                ->after('user_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('waktu_approve_spv')->nullable()->after('waktu_approve');
            $table->text('catatan_spv')->nullable()->after('alasan_penolakan');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_barang', function (Blueprint $table) {
            $table->dropConstrainedForeignId('supervisor_id');
            $table->dropColumn(['waktu_approve_spv', 'catatan_spv']);
        });

        if (DB::getDriverName() === 'mysql') {
            // Kembalikan enum ke versi lama
            DB::statement("ALTER TABLE permintaan_barang MODIFY COLUMN status_request ENUM(
                'pending',
                'disetujui',
                'ditolak'
            ) NOT NULL DEFAULT 'pending'");
        }
    }
};
