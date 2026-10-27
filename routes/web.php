<?php

use App\Http\Controllers\Dasbor\DasborAdminController;
use App\Http\Controllers\Dasbor\DasborCsController;
use App\Http\Controllers\Dasbor\DasborGudangController;
use App\Http\Controllers\Dasbor\DasborSupervisorController;
use App\Http\Controllers\TugasMingguanController;
use App\Http\Controllers\OperanShiftController;
use App\Http\Controllers\PermintaanBarangController;
use App\Http\Controllers\SetoranSampahController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\StokMasukController;
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
        Route::get('/export-excel', [TugasMingguanController::class, 'exportExcel'])->name('export-excel')->middleware('peran:supervisor,admin');
        Route::get('/{id}', [TugasMingguanController::class, 'detail'])->name('detail');
        Route::get('/{id}/after', [TugasMingguanController::class, 'isiAfter'])->name('isi-after')->middleware('peran:cs,pj_lantai,admin');
        Route::patch('/{id}/after', [TugasMingguanController::class, 'simpanAfter'])->name('simpan-after')->middleware('peran:cs,pj_lantai,admin');
        Route::patch('/{id}/verifikasi', [TugasMingguanController::class, 'verifikasi'])->name('verifikasi')->middleware('peran:supervisor,admin');
    });

    // =============================================
    // MODUL 2: OPERAN SHIFT (CS + PJ Lantai + Admin)
    // =============================================
    Route::middleware('peran:cs,pj_lantai,admin,supervisor')->prefix('operan')->name('operan.')->group(function () {
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
        Route::get('/operan/export-excel', [OperanShiftController::class, 'exportExcel'])->name('operan.export-excel');
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
        // Pilih area/lantai untuk keranjang
        Route::post('/barang/keranjang/area', [PermintaanBarangController::class, 'pilihArea'])->name('barang.keranjang.area');
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

    // Sisi Supervisor: Persetujuan tahap pertama
    Route::middleware('peran:supervisor,admin')->group(function () {
        Route::get('/barang/supervisor', [PermintaanBarangController::class, 'daftarSupervisor'])->name('barang.supervisor');
        Route::patch('/barang/{id}/setujui-spv', [PermintaanBarangController::class, 'setujuiSupervisor'])->name('barang.setujui-spv');
        Route::patch('/barang/{id}/tolak-spv', [PermintaanBarangController::class, 'tolakSupervisor'])->name('barang.tolak-spv');
    });

    // Sisi Gudang: Kelola permintaan
    Route::middleware('peran:gudang,admin')->group(function () {
        Route::get('/barang/gudang', [PermintaanBarangController::class, 'daftarGudang'])->name('barang.gudang');
        Route::get('/barang/export-excel', [PermintaanBarangController::class, 'exportExcel'])->name('barang.export-excel');
        Route::get('/barang/rekapan-pemakaian', [PermintaanBarangController::class, 'rekapanPemakaian'])->name('barang.rekapan-pemakaian');
        Route::get('/barang/rekapan-pemakaian/export', [PermintaanBarangController::class, 'exportRekapan'])->name('barang.rekapan-pemakaian.export');
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
        Route::get('/sampah/export-excel', [SetoranSampahController::class, 'exportExcel'])->name('sampah.export-excel');
        Route::patch('/sampah/{id}/validasi', [SetoranSampahController::class, 'validasi'])->name('sampah.validasi');
        Route::patch('/sampah/{id}/tolak', [SetoranSampahController::class, 'tolak'])->name('sampah.tolak');
        Route::patch('/sampah/{id}/reset', [SetoranSampahController::class, 'resetStatus'])->name('sampah.reset');
        Route::delete('/sampah/{id}', [SetoranSampahController::class, 'destroy'])->name('sampah.destroy');
    });


    // =============================================
    // MODUL LAPORAN (FR-034 s/d FR-037)
    // Hanya supervisor, pj_lantai, dan admin
    // =============================================
    Route::middleware('peran:supervisor,pj_lantai,admin')->group(function () {
        Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/preview-pdf', [App\Http\Controllers\LaporanController::class, 'previewPdf'])->name('laporan.preview-pdf');
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

        // Penilaian Masuk dari PJ Lantai (Admin: hapus saja)
        Route::delete('/penilaian-pj/{id}', [App\Http\Controllers\Admin\PenilaianPjController::class, 'destroy'])->name('penilaian-pj.destroy');

        // Master Jenis Pekerjaan (Operan Shift & Tugas CS)
        Route::get('/jenis-pekerjaan', [App\Http\Controllers\Admin\JenisPekerjaanController::class, 'index'])->name('jenis-pekerjaan.index');
        Route::post('/jenis-pekerjaan', [App\Http\Controllers\Admin\JenisPekerjaanController::class, 'store'])->name('jenis-pekerjaan.store');
        Route::put('/jenis-pekerjaan/{id}', [App\Http\Controllers\Admin\JenisPekerjaanController::class, 'update'])->name('jenis-pekerjaan.update');
        Route::patch('/jenis-pekerjaan/{id}/toggle-status', [App\Http\Controllers\Admin\JenisPekerjaanController::class, 'toggleStatus'])->name('jenis-pekerjaan.toggle-status');
        Route::delete('/jenis-pekerjaan/{id}', [App\Http\Controllers\Admin\JenisPekerjaanController::class, 'destroy'])->name('jenis-pekerjaan.destroy');
    });

    // Penilaian PJ Lantai (baca) untuk Admin, Supervisor & PJ Lantai
    Route::middleware('peran:admin,supervisor,pj_lantai')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/penilaian-pj', [App\Http\Controllers\Admin\PenilaianPjController::class, 'index'])->name('penilaian-pj.index');
        Route::get('/penilaian-pj/export-excel', [App\Http\Controllers\Admin\PenilaianPjController::class, 'exportExcel'])->name('penilaian-pj.export-excel');
        Route::get('/penilaian-pj/{id}', [App\Http\Controllers\Admin\PenilaianPjController::class, 'show'])->name('penilaian-pj.show');
        Route::get('/penilaian-pj/{id}/cetak', [App\Http\Controllers\Admin\PenilaianPjController::class, 'cetak'])->name('penilaian-pj.cetak');
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
    // MODUL STOK BARANG KEBERSIHAN (Supervisor, Gudang, Admin)
    // =============================================
    Route::prefix('stok-barang')->name('stok-barang.')->group(function () {
        // Akses Read-Only: Supervisor, PJ Lantai, Gudang, Admin
        Route::middleware('peran:supervisor,pj_lantai,gudang,admin')->group(function () {
            Route::get('/', [App\Http\Controllers\StokBarangController::class, 'index'])->name('index');
            Route::get('/laporan/{id}/pemakaian-data', [App\Http\Controllers\StokBarangController::class, 'dataPemakaian'])->name('laporan.pemakaian-data');
            Route::get('/export-excel', [App\Http\Controllers\StokBarangController::class, 'exportExcel'])->name('export-excel');
            Route::get('/cetak-pdf', [App\Http\Controllers\StokBarangController::class, 'cetakPdf'])->name('cetak-pdf');
        });

        // Akses Mutasi / Tulis: Gudang & Admin saja
        Route::middleware('peran:gudang,admin')->group(function () {
            Route::post('/simpan-barang', [App\Http\Controllers\StokBarangController::class, 'simpanBarang'])->name('simpan-barang');
            Route::post('/tambah-ke-periode', [App\Http\Controllers\StokBarangController::class, 'tambahKePeriode'])->name('tambah-ke-periode');
            Route::put('/laporan/{id}', [App\Http\Controllers\StokBarangController::class, 'updateLaporan'])->name('laporan.update');
            Route::patch('/laporan/{id}/pemakaian', [App\Http\Controllers\StokBarangController::class, 'updatePemakaian'])->name('laporan.pemakaian');
            Route::delete('/laporan/{id}', [App\Http\Controllers\StokBarangController::class, 'hapusLaporan'])->name('laporan.hapus');
            Route::get('/kode-otomatis', [App\Http\Controllers\StokBarangController::class, 'kodeOtomatis'])->name('kode-otomatis');
        });
    });

    // =============================================
    // MODUL STOK MASUK / RESTOK (Gudang & Admin)
    // =============================================
    Route::middleware('peran:gudang,admin')->prefix('stok-masuk')->name('stok-masuk.')->group(function () {
        Route::get('/', [StokMasukController::class, 'index'])->name('index');
        Route::post('/simpan', [StokMasukController::class, 'store'])->name('store');
        Route::post('/penyesuaian', [StokMasukController::class, 'penyesuaian'])->name('penyesuaian');
    });

    // =============================================
    // MODUL STOK KELUAR / LAPORAN BARANG KELUAR (Gudang & Admin)
    // =============================================
    Route::middleware('peran:gudang,admin')->prefix('stok-keluar')->name('stok-keluar.')->group(function () {
        Route::get('/', [App\Http\Controllers\StokKeluarController::class, 'index'])->name('index');
        Route::post('/simpan', [App\Http\Controllers\StokKeluarController::class, 'store'])->name('store');
        Route::get('/export-excel', [App\Http\Controllers\StokKeluarController::class, 'exportExcel'])->name('export-excel');
        Route::get('/cetak-pdf', [App\Http\Controllers\StokKeluarController::class, 'cetakPdf'])->name('cetak-pdf');
        Route::get('/{id}/bukti', [App\Http\Controllers\StokKeluarController::class, 'showBukti'])->name('bukti');
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
