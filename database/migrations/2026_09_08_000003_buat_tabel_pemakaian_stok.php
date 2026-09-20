<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemakaian_stok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_stok_id')->constrained('laporan_stok')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produk_kebersihan')->cascadeOnDelete();
            $table->string('unit', 30)
                  ->comment('Lantai 1|Lantai 2|Lantai 3|Lantai 4|Lantai 5|Lantai 6|Rawasari|CPB|CPT|CSSD');
            $table->unsignedTinyInteger('minggu')->comment('1, 2, 3, atau 4');
            $table->unsignedInteger('jumlah')->default(0);
            $table->timestamps();

            // Kombinasi unik: satu record per laporan + produk + unit + minggu
            $table->unique(
                ['laporan_stok_id', 'produk_id', 'unit', 'minggu'],
                'pemakaian_stok_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemakaian_stok');
    }
};
