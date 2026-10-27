<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function fkExists(string $table, string $fkName): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            return false;
        }

        return ! empty(DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?
        ", [$table, $fkName]));
    }

    private function indexExists(string $table, string $indexName): bool
    {
        if (DB::getDriverName() === 'sqlite') {
            return ! empty(DB::select("
                SELECT name FROM sqlite_master WHERE type = 'index' AND tbl_name = ? AND name = ?
            ", [$table, $indexName]));
        }

        return ! empty(DB::select("
            SELECT INDEX_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?
        ", [$table, $indexName]));
    }

    private function dropFkIfExists(string $table, string $fkName): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        if ($this->fkExists($table, $fkName)) {
            DB::statement("ALTER TABLE `$table` DROP FOREIGN KEY `$fkName`");
        }
    }

    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement("DROP INDEX IF EXISTS `$indexName`");
            return;
        }

        if ($this->indexExists($table, $indexName)) {
            DB::statement("ALTER TABLE `$table` DROP INDEX `$indexName`");
        }
    }

    /**
     * Unifikasi tabel master barang:
     * 1. Salin produk dari produk_kebersihan yang belum ada di barang_inventori.
     * 2. Alihkan foreign key laporan_stok dan pemakaian_stok ke barang_inventori(id).
     * 3. Hapus kolom produk_id di barang_inventori.
     * 4. Hapus tabel produk_kebersihan.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // 1. Pastikan semua produk_kebersihan ada di barang_inventori
        if (Schema::hasTable('produk_kebersihan')) {
            $semuaPk = DB::table('produk_kebersihan')->get();
            foreach ($semuaPk as $pk) {
                $barang = DB::table('barang_inventori')
                    ->where('produk_id', $pk->id)
                    ->orWhere('kode_barang', $pk->kode_barang)
                    ->first();

                if (! $barang) {
                    DB::table('barang_inventori')->insert([
                        'kode_barang'   => $pk->kode_barang,
                        'produk_id'     => $pk->id,
                        'nama_barang'   => $pk->nama_barang,
                        'satuan'        => $pk->satuan,
                        'stok_saat_ini' => 0,
                        'stok_minimum'  => 5,
                        'created_at'    => now(),
                        'updated_at'    => now(),
                    ]);
                } elseif (! $barang->produk_id) {
                    DB::table('barang_inventori')
                        ->where('id', $barang->id)
                        ->update(['produk_id' => $pk->id]);
                }
            }
        }

        // 2. Kolom barang_id pada laporan_stok
        if (Schema::hasTable('laporan_stok')) {
            if (! Schema::hasColumn('laporan_stok', 'barang_id')) {
                Schema::table('laporan_stok', function (Blueprint $table) {
                    $table->unsignedBigInteger('barang_id')->nullable()->after('id');
                });
            }

            // Map data dari produk_id ke barang_id
            if (Schema::hasColumn('laporan_stok', 'produk_id')) {
                $map = DB::table('barang_inventori')->whereNotNull('produk_id')->pluck('id', 'produk_id');
                foreach ($map as $pId => $bId) {
                    DB::table('laporan_stok')->where('produk_id', $pId)->update(['barang_id' => $bId]);
                }

                DB::table('laporan_stok')->whereNull('barang_id')->update([
                    'barang_id' => DB::raw('(SELECT id FROM barang_inventori ORDER BY id ASC LIMIT 1)')
                ]);

                $this->dropFkIfExists('laporan_stok', 'laporan_stok_produk_id_foreign');
                $this->dropIndexIfExists('laporan_stok', 'laporan_stok_produk_periode_unique');

                if (DB::getDriverName() !== 'sqlite') {
                    Schema::table('laporan_stok', function (Blueprint $table) {
                        $table->dropColumn('produk_id');
                    });
                }
            }

            // Tambah FK dan unique index jika belum ada
            if (! $this->indexExists('laporan_stok', 'laporan_stok_barang_periode_unique')) {
                Schema::table('laporan_stok', function (Blueprint $table) {
                    $table->unique(['barang_id', 'periode_bulan', 'periode_tahun'], 'laporan_stok_barang_periode_unique');
                });
            }

            if (! $this->fkExists('laporan_stok', 'laporan_stok_barang_id_foreign')) {
                Schema::table('laporan_stok', function (Blueprint $table) {
                    $table->foreign('barang_id')->references('id')->on('barang_inventori')->cascadeOnDelete();
                });
            }
        }

        // 3. Kolom barang_id pada pemakaian_stok
        if (Schema::hasTable('pemakaian_stok')) {
            if (! Schema::hasColumn('pemakaian_stok', 'barang_id')) {
                Schema::table('pemakaian_stok', function (Blueprint $table) {
                    $table->unsignedBigInteger('barang_id')->nullable()->after('laporan_stok_id');
                });
            }

            // Map data dari produk_id ke barang_id
            if (Schema::hasColumn('pemakaian_stok', 'produk_id')) {
                $map = DB::table('barang_inventori')->whereNotNull('produk_id')->pluck('id', 'produk_id');
                foreach ($map as $pId => $bId) {
                    DB::table('pemakaian_stok')->where('produk_id', $pId)->update(['barang_id' => $bId]);
                }

                DB::table('pemakaian_stok')->whereNull('barang_id')->update([
                    'barang_id' => DB::raw('(SELECT id FROM barang_inventori ORDER BY id ASC LIMIT 1)')
                ]);

                // Tambahkan index independen untuk laporan_stok_id jika belum ada
                if (! $this->indexExists('pemakaian_stok', 'pemakaian_stok_laporan_id_idx')) {
                    Schema::table('pemakaian_stok', function (Blueprint $table) {
                        $table->index('laporan_stok_id', 'pemakaian_stok_laporan_id_idx');
                    });
                }

                $this->dropFkIfExists('pemakaian_stok', 'pemakaian_stok_produk_id_foreign');
                $this->dropIndexIfExists('pemakaian_stok', 'pemakaian_stok_unique');

                if (DB::getDriverName() !== 'sqlite') {
                    Schema::table('pemakaian_stok', function (Blueprint $table) {
                        $table->dropColumn('produk_id');
                    });
                }
            }

            // Tambah FK dan unique index jika belum ada
            if (! $this->indexExists('pemakaian_stok', 'pemakaian_stok_unique')) {
                Schema::table('pemakaian_stok', function (Blueprint $table) {
                    $table->unique(['laporan_stok_id', 'barang_id', 'unit', 'minggu'], 'pemakaian_stok_unique');
                });
            }

            if (! $this->fkExists('pemakaian_stok', 'pemakaian_stok_barang_id_foreign')) {
                Schema::table('pemakaian_stok', function (Blueprint $table) {
                    $table->foreign('barang_id')->references('id')->on('barang_inventori')->cascadeOnDelete();
                });
            }
        }

        // 4. Hapus FK dan kolom produk_id dari barang_inventori
        if (Schema::hasTable('barang_inventori')) {
            $this->dropFkIfExists('barang_inventori', 'barang_inventori_produk_id_foreign');
            $this->dropIndexIfExists('barang_inventori', 'barang_inventori_produk_id_foreign');

            if (Schema::hasColumn('barang_inventori', 'produk_id')) {
                if (DB::getDriverName() !== 'sqlite') {
                    Schema::table('barang_inventori', function (Blueprint $table) {
                        $table->dropColumn('produk_id');
                    });
                }
            }
        }

        // 5. Hapus tabel produk_kebersihan
        Schema::dropIfExists('produk_kebersihan');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate produk_kebersihan
        Schema::create('produk_kebersihan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 30)->unique();
            $table->string('nama_barang', 150);
            $table->string('satuan', 30)->default('buah');
            $table->timestamps();
        });

        // Add back produk_id to barang_inventori
        Schema::table('barang_inventori', function (Blueprint $table) {
            $table->foreignId('produk_id')->nullable()->after('kode_barang')
                  ->constrained('produk_kebersihan')->nullOnDelete();
        });

        // Add back produk_id to laporan_stok
        Schema::table('laporan_stok', function (Blueprint $table) {
            $this->dropIndexIfExists('laporan_stok', 'laporan_stok_barang_periode_unique');
            $this->dropFkIfExists('laporan_stok', 'laporan_stok_barang_id_foreign');
            $table->foreignId('produk_id')->nullable()->after('id')
                  ->constrained('produk_kebersihan')->cascadeOnDelete();
            $table->unique(['produk_id', 'periode_bulan', 'periode_tahun'], 'laporan_stok_produk_periode_unique');
            $table->dropColumn('barang_id');
        });

        // Add back produk_id to pemakaian_stok
        Schema::table('pemakaian_stok', function (Blueprint $table) {
            $this->dropIndexIfExists('pemakaian_stok', 'pemakaian_stok_unique');
            $this->dropFkIfExists('pemakaian_stok', 'pemakaian_stok_barang_id_foreign');
            $table->foreignId('produk_id')->nullable()->after('laporan_stok_id')
                  ->constrained('produk_kebersihan')->cascadeOnDelete();
            $table->unique(['laporan_stok_id', 'produk_id', 'unit', 'minggu'], 'pemakaian_stok_unique');
            $table->dropColumn('barang_id');
        });
    }
};
