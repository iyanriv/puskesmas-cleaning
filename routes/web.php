<?php

use App\Http\Controllers\Dasbor\DasborAdminController;
use App\Http\Controllers\Dasbor\DasborCsController;
use App\Http\Controllers\Dasbor\DasborGudangController;
use App\Http\Controllers\Dasbor\DasborSupervisorController;
use App\Http\Controllers\CeklisKebersihanController;
use App\Http\Controllers\TugasMingguanController;
use App\Http\Controllers\OperanShiftController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\SetoranSampahController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect(Auth::user()->ruteDasbor())
        : redirect()->route('login');
});

// =======================================================
// FORMULIR PENILAIAN PUBLIK PJ LANTAI (TANPA LOGIN)
// =======================================================
Route::get('/penilaian-pj-lantai', [App\Http\Controllers\PenilaianPjPublicController::class, 'index'])->name('penilaian-pj.form');
Route::post('/penilaian-pj-lantai', [App\Http\Controllers\PenilaianPjPublicController::class, 'store'])
    ->name('penilaian-pj.simpan')
    ->middleware('throttle:3,30'); // max 3 submit per 30 menit per IP
Route::get('/penilaian-pj-lantai/sukses', [App\Http\Controllers\PenilaianPjPublicController::class, 'sukses'])->name('penilaian-pj.sukses');

Auth::routes(['register' => false]);

Route::middleware('auth')->group(function () {

    // =============================================
    // DASBOR
    // =============================================
    Route::get('/dasbor/cs', [DasborCsController::class, 'index'])
        ->name('dasbor.cs')
        ->middleware('peran:cs');

    Route::get('/dasbor/supervisor', [DasborSupervisorController::class, 'index'])
        ->name('dasbor.supervisor')
        ->middleware('peran:supervisor,pj_lantai');

    Route::get('/dasbor/gudang', [DasborGudangController::class, 'index'])
        ->name('dasbor.gudang')
        ->middleware('peran:gudang');

    Route::get('/dasbor/admin', [DasborAdminController::class, 'index'])
        ->name('dasbor.admin')
        ->middleware('peran:admin');

    // =============================================
    // MODUL 1: TUGAS MINGGUAN CS (Pengganti Ceklis di Portal CS)
    // =============================================
    Route::middleware('peran:cs,pj_lantai,supervisor,admin')->prefix('tugas-mingguan')->name('tugas-mingguan.')->group(function () {
        Route::get('/', [TugasMingguanController::class, 'index'])->name('index');
        Route::get('/buat', [TugasMingguanController::class, 'buat'])->name('buat')->middleware('peran:cs,pj_lantai,admin');
        Route::post('/simpan', [TugasMingguanController::class, 'simpan'])->name('simpan')->middleware('peran:cs,pj_lantai,admin');
        Route::get('/cetak-laporan', [TugasMingguanController::class, 'cetakLaporan'])->name('cetak-laporan')->middleware('peran:supervisor,admin');
        Route::get('/{id}', [TugasMingguanController::class, 'detail'])->name('detail');
        Route::get('/{id}/after', [TugasMingguanController::class, 'isiAfter'])->name('isi-after')->middleware('peran:cs,pj_lantai,admin');
        Route::patch('/{id}/after', [TugasMingguanController::class, 'simpanAfter'])->name('simpan-after')->middleware('peran:cs,pj_lantai,admin');
        Route::patch('/{id}/verifikasi', [TugasMingguanController::class, 'verifikasi'])->name('verifikasi')->middleware('peran:supervisor,admin');
    });

    // =============================================
    // MODUL LEGACY: CEKLIS KEBERSIHAN (Tersedia untuk riwayat)
    // =============================================
    Route::middleware('peran:cs,pj_lantai')->prefix('ceklis')->name('ceklis.')->group(function () {
        Route::get('/', [CeklisKebersihanController::class, 'index'])->name('index');
        Route::get('/area/{area_id}', [CeklisKebersihanController::class, 'buat'])->name('buat');
        Route::post('/simpan', [CeklisKebersihanController::class, 'simpan'])->name('simpan');
        Route::get('/{id}/after', [CeklisKebersihanController::class, 'isiAfter'])->name('isi-after');
        Route::patch('/{id}/after', [CeklisKebersihanController::class, 'simpanAfter'])->name('simpan-after');
    });

    // Rute ceklis.detail dapat diakses oleh semua peran (CS, PJ, Spv, Admin)
    Route::middleware('peran:cs,pj_lantai,supervisor,admin')->prefix('ceklis')->name('ceklis.')->group(function () {
        Route::get('/{id}/detail', [CeklisKebersihanController::class, 'detail'])->name('detail');
    });

    // Penilaian ceklis oleh supervisor (FR-029)
    Route::middleware('peran:supervisor,pj_lantai,admin')->prefix('ceklis')->name('ceklis.')->group(function () {
        Route::patch('/{id}/nilai', [CeklisKebersihanController::class, 'nilaiCeklis'])->name('nilai');
    });

    // =============================================
    // MODUL 2: OPERAN SHIFT (CS + PJ Lantai + Admin)
    // =============================================
    Route::middleware('peran:cs,pj_lantai,admin')->prefix('operan')->name('operan.')->group(function () {
        Route::get('/', [OperanShiftController::class, 'index'])->name('index');
        Route::post('/kirim', [OperanShiftController::class, 'kirim'])->name('kirim');
        Route::patch('/{id}/terima', [OperanShiftController::class, 'terima'])->name('terima');
        Route::patch('/{id}/selesaikan', [OperanShiftController::class, 'selesaikan'])->name('selesaikan');
        Route::post('/{id}/eskalasi', [OperanShiftController::class, 'eskalasi'])->name('eskalasi');
        Route::get('/{id}/detail', [OperanShiftController::class, 'detail'])->name('detail');
    });

    // Cetak laporan operan (Admin/Supervisor only)
    Route::middleware('peran:admin,supervisor,pj_lantai')->group(function () {
        Route::get('/operan/cetak-laporan', [OperanShiftController::class, 'cetakLaporan'])->name('operan.cetak-laporan');
    });

    // =============================================
    // MODUL 3: PERMINTAAN BARANG
    // =============================================

    // Sisi CS: Katalog & Ajukan
    // Sisi CS: Katalog & Cart Management
    Route::middleware('peran:cs,pj_lantai')->group(function () {
        Route::get('/barang/katalog', [PermintaanBarangController::class, 'katalog'])->name('barang.katalog');
        // Tambah ke keranjang (session based)
        Route::post('/barang/keranjang/tambah', [PermintaanBarangController::class, 'addToCart'])->name('barang.keranjang.tambah');
        // Lihat keranjang
        Route::get('/barang/keranjang', [PermintaanBarangController::class, 'viewCart'])->name('barang.keranjang');
        // Update item quantity in keranjang
        Route::post('/barang/keranjang/update', [PermintaanBarangController::class, 'updateCartItem'])->name('barang.keranjang.update');
        // Hapus item dari keranjang
        Route::post('/barang/keranjang/hapus', [PermintaanBarangController::class, 'removeCartItem'])->name('barang.keranjang.hapus');
        // Kosongkan seluruh keranjang
        Route::post('/barang/keranjang/hapus-semua', [PermintaanBarangController::class, 'clearCart'])->name('barang.keranjang.hapus-semua');
        // Kirim semua item di keranjang sebagai permintaan
        Route::post('/barang/keranjang/kirim', [PermintaanBarangController::class, 'submitCart'])->name('barang.keranjang.kirim');
        // Ajukan langsung (fallback tetap ada)
        Route::post('/barang/ajukan', [PermintaanBarangController::class, 'ajukan'])->name('barang.ajukan');
    });

    // Sisi Gudang: Kelola permintaan
    Route::middleware('peran:gudang,admin')->group(function () {
        Route::get('/barang/gudang', [PermintaanBarangController::class, 'daftarGudang'])->name('barang.gudang');
        Route::patch('/barang/{id}/setujui', [PermintaanBarangController::class, 'setujui'])->name('barang.setujui');
        Route::patch('/barang/{id}/tolak', [PermintaanBarangController::class, 'tolak'])->name('barang.tolak');
        Route::get('/barang/{id}/bukti', [PermintaanBarangController::class, 'showProof'])->name('barang.bukti');
    });

    // =============================================
    // MODUL 4: BANK SAMPAH
    // =============================================

    // Sisi CS: Form setor
    Route::middleware('peran:cs,pj_lantai')->group(function () {
        Route::get('/sampah/setor', [SetoranSampahController::class, 'buat'])->name('sampah.buat');
        Route::post('/sampah/simpan', [SetoranSampahController::class, 'simpan'])->name('sampah.simpan');
    });

    // Sisi Supervisor/Admin: Rekapan & Validasi
    Route::middleware('peran:supervisor,pj_lantai,admin')->group(function () {
        Route::get('/sampah/rekapan', [SetoranSampahController::class, 'rekapan'])->name('sampah.rekapan');
        Route::get('/sampah/cetak-laporan', [SetoranSampahController::class, 'cetakLaporan'])->name('sampah.cetak-laporan');
        Route::patch('/sampah/{id}/validasi', [SetoranSampahController::class, 'validasi'])->name('sampah.validasi');
        Route::patch('/sampah/{id}/tolak', [SetoranSampahController::class, 'tolak'])->name('sampah.tolak');
    });


    // =============================================
    // MODUL LAPORAN (FR-034 s/d FR-037)
    // Hanya supervisor, pj_lantai, dan admin
    // =============================================
    Route::middleware('peran:supervisor,pj_lantai,admin')->group(function () {
        Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/cetak-pdf', [App\Http\Controllers\LaporanController::class, 'cetakPdf'])->name('laporan.cetak-pdf');
        Route::get('/laporan/cetak-excel', [App\Http\Controllers\LaporanController::class, 'cetakExcel'])->name('laporan.cetak-excel');
    });

    // =============================================
    // MODUL ADMIN: CRUD MASTER DATA
    // =============================================
    Route::middleware('peran:admin')->prefix('admin')->name('admin.')->group(function () {
        // Pengguna
        Route::get('/pengguna', [App\Http\Controllers\Admin\PenggunaController::class, 'index'])->name('pengguna.index');
        Route::get('/pengguna/buat', [App\Http\Controllers\Admin\PenggunaController::class, 'create'])->name('pengguna.create');
        Route::post('/pengguna', [App\Http\Controllers\Admin\PenggunaController::class, 'store'])->name('pengguna.store');
        Route::get('/pengguna/{id}/ubah', [App\Http\Controllers\Admin\PenggunaController::class, 'edit'])->name('pengguna.edit');
        Route::put('/pengguna/{id}', [App\Http\Controllers\Admin\PenggunaController::class, 'update'])->name('pengguna.update');
        Route::delete('/pengguna/{id}', [App\Http\Controllers\Admin\PenggunaController::class, 'destroy'])->name('pengguna.destroy');

        // Area
        Route::get('/area', [App\Http\Controllers\Admin\AreaController::class, 'index'])->name('area.index');
        Route::get('/area/buat', [App\Http\Controllers\Admin\AreaController::class, 'create'])->name('area.create');
        Route::post('/area', [App\Http\Controllers\Admin\AreaController::class, 'store'])->name('area.store');
        Route::get('/area/{id}/ubah', [App\Http\Controllers\Admin\AreaController::class, 'edit'])->name('area.edit');
        Route::put('/area/{id}', [App\Http\Controllers\Admin\AreaController::class, 'update'])->name('area.update');
        Route::delete('/area/{id}', [App\Http\Controllers\Admin\AreaController::class, 'destroy'])->name('area.destroy');

        // Penilaian Masuk dari PJ Lantai
        Route::get('/penilaian-pj', [App\Http\Controllers\Admin\PenilaianPjController::class, 'index'])->name('penilaian-pj.index');
        Route::get('/penilaian-pj/{id}', [App\Http\Controllers\Admin\PenilaianPjController::class, 'show'])->name('penilaian-pj.show');
        Route::get('/penilaian-pj/{id}/cetak', [App\Http\Controllers\Admin\PenilaianPjController::class, 'cetak'])->name('penilaian-pj.cetak');
        Route::delete('/penilaian-pj/{id}', [App\Http\Controllers\Admin\PenilaianPjController::class, 'destroy'])->name('penilaian-pj.destroy');
    });

    // =============================================
    // KELOLA BARANG (Admin & Gudang)
    // =============================================
    Route::middleware('peran:admin,gudang')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/barang', [App\Http\Controllers\Admin\BarangController::class, 'index'])->name('barang.index');
        Route::get('/barang/buat', [App\Http\Controllers\Admin\BarangController::class, 'create'])->name('barang.create');
        Route::post('/barang', [App\Http\Controllers\Admin\BarangController::class, 'store'])->name('barang.store');
        Route::get('/barang/{id}/ubah', [App\Http\Controllers\Admin\BarangController::class, 'edit'])->name('barang.edit');
        Route::put('/barang/{id}', [App\Http\Controllers\Admin\BarangController::class, 'update'])->name('barang.update');
        Route::delete('/barang/{id}', [App\Http\Controllers\Admin\BarangController::class, 'destroy'])->name('barang.destroy');
    });

    // =============================================
    // MODUL STOK BARANG KEBERSIHAN (Gudang & Admin)
    // =============================================
    Route::middleware('peran:gudang,admin')->prefix('stok-barang')->name('stok-barang.')->group(function () {
        Route::get('/', [App\Http\Controllers\StokBarangController::class, 'index'])->name('index');

        // Tambah barang baru sekaligus laporan stok periode
        Route::post('/simpan-barang', [App\Http\Controllers\StokBarangController::class, 'simpanBarang'])->name('simpan-barang');

        // Tambah produk yang sudah ada ke periode tertentu
        Route::post('/tambah-ke-periode', [App\Http\Controllers\StokBarangController::class, 'tambahKePeriode'])->name('tambah-ke-periode');

        // Update info barang + stok masuk pada suatu laporan
        Route::put('/laporan/{id}', [App\Http\Controllers\StokBarangController::class, 'updateLaporan'])->name('laporan.update');

        // Update pemakaian per unit per minggu (AJAX)
        Route::patch('/laporan/{id}/pemakaian', [App\Http\Controllers\StokBarangController::class, 'updatePemakaian'])->name('laporan.pemakaian');

        // Ambil data pemakaian yang sudah tersimpan (AJAX — untuk modal)
        Route::get('/laporan/{id}/pemakaian-data', [App\Http\Controllers\StokBarangController::class, 'dataPemakaian'])->name('laporan.pemakaian-data');

        // Hapus barang dari laporan periode
        Route::delete('/laporan/{id}', [App\Http\Controllers\StokBarangController::class, 'hapusLaporan'])->name('laporan.hapus');

        // Export
        Route::get('/export-excel', [App\Http\Controllers\StokBarangController::class, 'exportExcel'])->name('export-excel');
        Route::get('/cetak-pdf', [App\Http\Controllers\StokBarangController::class, 'cetakPdf'])->name('cetak-pdf');

        // API: generate kode barang otomatis
        Route::get('/kode-otomatis', [App\Http\Controllers\StokBarangController::class, 'kodeOtomatis'])->name('kode-otomatis');
    });

    // =============================================
    // GANTI PASSWORD (Semua role)
    // =============================================
    Route::get('/profil/ganti-password', [App\Http\Controllers\GantiPasswordController::class, 'edit'])->name('profil.ganti-password');
    Route::put('/profil/ganti-password', [App\Http\Controllers\GantiPasswordController::class, 'update'])->name('profil.ganti-password.update');

    // =============================================
    // API POLLING: Notifikasi real-time (CS)
    // =============================================
    Route::get('/api/notifikasi/operan-masuk', function () {
        $jumlah = \App\Models\OperanShift::where('penerima_id', auth()->id())
            ->where('status_terima', 'menunggu')
            ->count();
        return response()->json(['jumlah' => $jumlah]);
    })->name('api.notifikasi.operan')->middleware('peran:cs,pj_lantai');
});
