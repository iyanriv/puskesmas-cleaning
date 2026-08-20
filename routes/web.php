<?php

use App\Http\Controllers\Dasbor\DasborAdminController;
use App\Http\Controllers\Dasbor\DasborCsController;
use App\Http\Controllers\Dasbor\DasborGudangController;
use App\Http\Controllers\Dasbor\DasborSupervisorController;
use App\Http\Controllers\CeklisKebersihanController;
use App\Http\Controllers\OperanShiftController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\SetoranSampahController;
use App\Http\Controllers\PenilaianKinerjaController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect(Auth::user()->ruteDasbor())
        : redirect()->route('login');
});

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
    // MODUL 1: CEKLIS KEBERSIHAN (CS)
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

    // =============================================
    // MODUL 2: OPERAN SHIFT (CS + PJ Lantai)
    // =============================================
    Route::middleware('peran:cs,pj_lantai')->prefix('operan')->name('operan.')->group(function () {
        Route::get('/', [OperanShiftController::class, 'index'])->name('index');
        Route::post('/kirim', [OperanShiftController::class, 'kirim'])->name('kirim');
        Route::patch('/{id}/terima', [OperanShiftController::class, 'terima'])->name('terima');
        Route::get('/{id}/detail', [OperanShiftController::class, 'detail'])->name('detail');
    });

    // =============================================
    // MODUL 3: PERMINTAAN BARANG
    // =============================================

    // Sisi CS: Katalog & Ajukan
    Route::middleware('peran:cs,pj_lantai')->group(function () {
        Route::get('/barang/katalog', [PermintaanBarangController::class, 'katalog'])->name('barang.katalog');
        Route::post('/barang/ajukan', [PermintaanBarangController::class, 'ajukan'])->name('barang.ajukan');
    });

    // Sisi Gudang: Kelola permintaan
    Route::middleware('peran:gudang,admin')->group(function () {
        Route::get('/barang/gudang', [PermintaanBarangController::class, 'daftarGudang'])->name('barang.gudang');
        Route::patch('/barang/{id}/setujui', [PermintaanBarangController::class, 'setujui'])->name('barang.setujui');
        Route::patch('/barang/{id}/tolak', [PermintaanBarangController::class, 'tolak'])->name('barang.tolak');
    });

    // =============================================
    // MODUL 4: BANK SAMPAH
    // =============================================

    // Sisi CS: Form setor
    Route::middleware('peran:cs,pj_lantai')->group(function () {
        Route::get('/sampah/setor', [SetoranSampahController::class, 'buat'])->name('sampah.buat');
        Route::post('/sampah/simpan', [SetoranSampahController::class, 'simpan'])->name('sampah.simpan');
    });

    // Sisi Supervisor/Admin: Rekapan
    Route::middleware('peran:supervisor,pj_lantai,admin')->group(function () {
        Route::get('/sampah/rekapan', [SetoranSampahController::class, 'rekapan'])->name('sampah.rekapan');
    });

    // =============================================
    // MODUL 5: PENILAIAN KINERJA
    // =============================================
    Route::middleware('peran:supervisor,pj_lantai,admin')->prefix('penilaian')->name('penilaian.')->group(function () {
        Route::get('/', [PenilaianKinerjaController::class, 'index'])->name('index');
        Route::get('/buat/{id}', [PenilaianKinerjaController::class, 'buat'])->name('buat');
        Route::post('/simpan/{id}', [PenilaianKinerjaController::class, 'simpan'])->name('simpan');
        Route::get('/detail/{id}', [PenilaianKinerjaController::class, 'detail'])->name('detail');
        Route::get('/rekap', [PenilaianKinerjaController::class, 'rekap'])->name('rekap');
    });

    // =============================================
    // MODUL LAPORAN
    // =============================================
    Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak-pdf', [App\Http\Controllers\LaporanController::class, 'cetakPdf'])->name('laporan.cetak-pdf');
    Route::get('/laporan/cetak-excel', [App\Http\Controllers\LaporanController::class, 'cetakExcel'])->name('laporan.cetak-excel');

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
    // GANTI PASSWORD (Semua role)
    // =============================================
    Route::get('/profil/ganti-password', [App\Http\Controllers\GantiPasswordController::class, 'edit'])->name('profil.ganti-password');
    Route::put('/profil/ganti-password', [App\Http\Controllers\GantiPasswordController::class, 'update'])->name('profil.ganti-password.update');
});
