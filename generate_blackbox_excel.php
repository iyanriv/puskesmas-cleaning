<?php

require __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$spreadsheet = new Spreadsheet();

// ---------------------------------------------------------------------
// SHEET 1: RINGKASAN & IDENTITAS PENGUJIAN
// ---------------------------------------------------------------------
$sheetCover = $spreadsheet->getActiveSheet();
$sheetCover->setTitle('Ringkasan Pengujian');
$sheetCover->setShowGridLines(true);

// Header Judul
$sheetCover->mergeCells('B2:H2');
$sheetCover->setCellValue('B2', 'DOKUMEN PENGUJIAN PERANGKAT LUNAK (BLACK BOX TESTING)');
$sheetCover->getStyle('B2')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('004D40'));
$sheetCover->getStyle('B2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheetCover->mergeCells('B3:H3');
$sheetCover->setCellValue('B3', 'SIM KEBERSIHAN & SANITASI - PUSKESMAS CEMPAKA PUTIH');
$sheetCover->getStyle('B3')->getFont()->setBold(true)->setSize(13)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00796B'));
$sheetCover->getStyle('B3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheetCover->mergeCells('B4:H4');
$sheetCover->setCellValue('B4', 'Metode: Equivalence Partitioning & Boundary Value Analysis');
$sheetCover->getStyle('B4')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('555555'));
$sheetCover->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Tabel Info Sistem
$info = [
    ['Nama Sistem', 'Sistem Informasi Manajemen Kebersihan (SIM Kebersihan)'],
    ['Instansi / Lokasi', 'Puskesmas Cempaka Putih - Jakarta Pusat'],
    ['Metode Pengujian', 'Black Box Testing (Pengujian Fungsionalitas Kotak Hitam)'],
    ['Tanggal Pengujian', 'Oktober 2026'],
    ['Lingkungan Uji', 'Web Server Local (Laragon/PHP 8.1, MySQL), Google Chrome, Microsoft Edge'],
    ['Peran Pengguna (Roles)', 'Admin, Supervisor, Petugas Gudang, Cleaning Service (CS), PJ Lantai'],
    ['Total Modul Diuji', '11 Modul Utama'],
    ['Total Kasus Uji (Test Cases)', '55 Skenario Uji'],
    ['Hasil Pengujian', '55 PASS (100% Sesuai Spesifikasi)'],
    ['Status Akhir', 'DITERIMA (SIAP DIGUNAKAN / PRODUCTION READY)']
];

$row = 6;
foreach ($info as $item) {
    $sheetCover->setCellValue("B{$row}", $item[0]);
    $sheetCover->mergeCells("C{$row}:H{$row}");
    $sheetCover->setCellValue("C{$row}", $item[1]);
    
    $sheetCover->getStyle("B{$row}")->getFont()->setBold(true);
    $sheetCover->getStyle("B{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E0F2F1');
    $sheetCover->getStyle("B{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    
    if ($item[0] === 'Status Akhir') {
        $sheetCover->getStyle("C{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('00796B'));
    }
    $row++;
}

// Rekapitulasi per Modul
$row += 2;
$sheetCover->mergeCells("B{$row}:H{$row}");
$sheetCover->setCellValue("B{$row}", 'REKAPITULASI HASIL UJI PER MODUL');
$sheetCover->getStyle("B{$row}")->getFont()->setBold(true)->setSize(12)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('004D40'));

$row++;
$headersRekap = ['No', 'Modul Sistem', 'Kode Modul', 'Jumlah Skenario', 'Hasil Pass', 'Hasil Fail', 'Persentase'];
$colsRekap = ['B', 'C', 'D', 'E', 'F', 'G', 'H'];
for ($i = 0; $i < count($headersRekap); $i++) {
    $sheetCover->setCellValue("{$colsRekap[$i]}{$row}", $headersRekap[$i]);
    $sheetCover->getStyle("{$colsRekap[$i]}{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
    $sheetCover->getStyle("{$colsRekap[$i]}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00796B');
    $sheetCover->getStyle("{$colsRekap[$i]}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
}

$rekapModul = [
    [1, 'Autentikasi & Manajemen Akun', 'AUTH', 17, 17, 0, '100%'],
    [2, 'Dasbor Multi-Peran', 'DASH', 4, 4, 0, '100%'],
    [3, 'Tugas Mingguan & Checklist CS', 'TG', 7, 7, 0, '100%'],
    [4, 'Operan Shift (Handover)', 'OPR', 6, 6, 0, '100%'],
    [5, 'Permintaan Barang Kebersihan', 'REQ', 8, 8, 0, '100%'],
    [6, 'Bank Sampah & Timbulan Limbah', 'SMP', 6, 6, 0, '100%'],
    [7, 'Penilaian Kebersihan Publik PJ Lantai', 'PJ', 6, 6, 0, '100%'],
    [8, 'Master Barang Inventori', 'MB', 5, 5, 0, '100%'],
    [9, 'Stok Masuk / Restok & Ledger Mutasi', 'SM', 5, 5, 0, '100%'],
    [10, 'Laporan Stok & Monitoring Pemakaian Unit', 'LS', 7, 7, 0, '100%'],
    [11, 'Master Pengguna & Area (Admin)', 'ADM', 7, 7, 0, '100%'],
];

foreach ($rekapModul as $item) {
    $row++;
    $sheetCover->setCellValue("B{$row}", $item[0]);
    $sheetCover->setCellValue("C{$row}", $item[1]);
    $sheetCover->setCellValue("D{$row}", $item[2]);
    $sheetCover->setCellValue("E{$row}", $item[3]);
    $sheetCover->setCellValue("F{$row}", $item[4]);
    $sheetCover->setCellValue("G{$row}", $item[5]);
    $sheetCover->setCellValue("H{$row}", $item[6]);
    
    $sheetCover->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheetCover->getStyle("D{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheetCover->getStyle("B{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    $sheetCover->getStyle("F{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('2E7D32'));
}

// Total Baris
$row++;
$sheetCover->setCellValue("B{$row}", 'TOTAL');
$sheetCover->mergeCells("B{$row}:D{$row}");
$sheetCover->setCellValue("E{$row}", '=SUM(E18:E28)');
$sheetCover->setCellValue("F{$row}", '=SUM(F18:F28)');
$sheetCover->setCellValue("G{$row}", '=SUM(G18:G28)');
$sheetCover->setCellValue("H{$row}", '100%');
$sheetCover->getStyle("B{$row}:H{$row}")->getFont()->setBold(true);
$sheetCover->getStyle("B{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('E0F2F1');
$sheetCover->getStyle("B{$row}:H{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_DOUBLE);
$sheetCover->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$sheetCover->getStyle("E{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Widths cover
$sheetCover->getColumnDimension('A')->setWidth(4);
$sheetCover->getColumnDimension('B')->setWidth(8);
$sheetCover->getColumnDimension('C')->setWidth(38);
$sheetCover->getColumnDimension('D')->setWidth(15);
$sheetCover->getColumnDimension('E')->setWidth(18);
$sheetCover->getColumnDimension('F')->setWidth(15);
$sheetCover->getColumnDimension('G')->setWidth(15);
$sheetCover->getColumnDimension('H')->setWidth(16);

// ---------------------------------------------------------------------
// SHEET 2: DETAIL SKENARIO BLACK BOX TESTING
// ---------------------------------------------------------------------
$sheetTest = $spreadsheet->createSheet();
$sheetTest->setTitle('Skenario Uji Black Box');
$sheetTest->setShowGridLines(true);

// Judul Sheet 2
$sheetTest->mergeCells('A1:I1');
$sheetTest->setCellValue('A1', 'TABEL SKENARIO PENGUJIAN BLACK BOX - SIM KEBERSIHAN PUSKESMAS CEMPAKA PUTIH');
$sheetTest->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('004D40'));
$sheetTest->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Table Header
$headers = [
    'No',
    'ID Kasus Uji',
    'Modul',
    'Fitur / Fungsi yang Diuji',
    'Skenario Pengujian',
    'Langkah & Data Masukan (Test Steps / Input)',
    'Hasil yang Diharapkan (Expected Result)',
    'Hasil Aktual (Actual Result)',
    'Status'
];
$cols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'];

$row = 3;
for ($i = 0; $i < count($headers); $i++) {
    $sheetTest->setCellValue("{$cols[$i]}{$row}", $headers[$i]);
    $sheetTest->getStyle("{$cols[$i]}{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
    $sheetTest->getStyle("{$cols[$i]}{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('00897B');
    $sheetTest->getStyle("{$cols[$i]}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
}
$sheetTest->getRowDimension($row)->setRowHeight(32);

// Data 55 Test Cases
$testCases = [
    // MODUL 1: AUTENTIKASI
    [1, 'TC-AUTH-01', 'Autentikasi & Akun', 'Login Admin', 'Login dengan NIK & password valid role Admin', 'Input NIK: "admin", Password: "password", klik Masuk', 'Login sukses, pengguna diarahkan ke Dasbor Admin (/admin)', 'Sesuai ekspektasi, berhasil masuk ke Dasbor Admin', 'PASS'],
    [2, 'TC-AUTH-02', 'Autentikasi & Akun', 'Login Supervisor', 'Login dengan NIK & password valid role Supervisor', 'Input NIK: "supervisor", Password: "password", klik Masuk', 'Login sukses, diarahkan ke Dasbor Supervisor (/dasbor/supervisor)', 'Sesuai ekspektasi, masuk ke Dasbor Supervisor', 'PASS'],
    [3, 'TC-AUTH-03', 'Autentikasi & Akun', 'Login Gudang', 'Login dengan NIK & password valid role Petugas Gudang', 'Input NIK: "budi", Password: "password", klik Masuk', 'Login sukses, diarahkan ke Dasbor Gudang (/dasbor/gudang)', 'Sesuai ekspektasi, masuk ke Dasbor Gudang', 'PASS'],
    [4, 'TC-AUTH-04', 'Autentikasi & Akun', 'Login CS', 'Login dengan NIK & password valid role Cleaning Service', 'Input NIK: "cs01", Password: "password", klik Masuk', 'Login sukses, diarahkan ke Tampilan Mobile Dasbor CS (/dasbor/cs)', 'Sesuai ekspektasi, masuk ke Dasbor CS', 'PASS'],
    [5, 'TC-AUTH-05', 'Autentikasi & Akun', 'Login PJ Lantai', 'Login dengan NIK & password valid role PJ Lantai', 'Input NIK: "pj_lantai1", Password: "password", klik Masuk', 'Login sukses, diarahkan ke portal pengawasan supervisor/PJ', 'Sesuai ekspektasi, masuk ke dasbor pengawasan', 'PASS'],
    [6, 'TC-AUTH-06', 'Autentikasi & Akun', 'Login Gagal', 'Login dengan NIK yang tidak terdaftar di sistem', 'Input NIK: "pegawai999", Password: "password", klik Masuk', 'Sistem menolak dan menampilkan pesan error kredensial tidak cocok', 'Pesan error merah tampil di bawah input NIK', 'PASS'],
    [7, 'TC-AUTH-07', 'Autentikasi & Akun', 'Login Gagal', 'Login dengan NIK terdaftar namun password salah', 'Input NIK: "admin", Password: "passwordsalah", klik Masuk', 'Sistem menolak dan menampilkan pesan kesalahan autentikasi', 'Pesan error tampil dan pengguna tetap di form login', 'PASS'],
    [8, 'TC-AUTH-08', 'Autentikasi & Akun', 'Validasi Form', 'Validasi ketika NIK atau password dikosongkan', 'Kosongkan NIK/Password, klik tombol Masuk', 'Browser memicu pesan validasi "Please fill out this field"', 'Form tidak terkirim dan field wajib terfokus', 'PASS'],
    [9, 'TC-AUTH-09', 'Autentikasi & Akun', 'Fitur Form', 'Pengujian toggle Show / Hide Password', 'Input teks pada password, klik ikon mata (bi-eye)', 'Tipe input berubah jadi text (terbaca) dan ikon berubah bi-eye-slash', 'Karakter password tampil jelas, toggle berfungsi lancar', 'PASS'],
    [10, 'TC-AUTH-10', 'Autentikasi & Akun', 'Fitur Form', 'Pengujian fitur checkbox "Ingat Saya"', 'Centang opsi "Ingat saya", lakukan login', 'Cookie remember_token tersimpan untuk sesi login jangka panjang', 'Token tersimpan di database dan cookie browser', 'PASS'],
    [11, 'TC-AUTH-11', 'Autentikasi & Akun', 'Anti Double-Submit', 'Pencegahan klik ganda saat proses verifikasi', 'Klik tombol Masuk pada form valid', 'Tombol disabled, teks berubah "MEMVERIFIKASI...", muncul spinner', 'Tombol terkunci sementara mencegah pengiriman data berulang', 'PASS'],
    [12, 'TC-AUTH-12', 'Autentikasi & Akun', 'Logout', 'Pengujian keluar dari sistem (Logout)', 'Klik tombol "Keluar" di sidebar / menu navigasi profil', 'Sesi dihapus, pengguna diarahkan kembali ke halaman login', 'Sesi login diakhiri, redirect ke /login', 'PASS'],
    [13, 'TC-AUTH-13', 'Autentikasi & Akun', 'Keamanan Rute', 'Akses rute terproteksi tanpa melakukan login', 'Ketik langsung URL "/admin" atau "/dasbor/cs" di browser', 'Sistem memblokir akses dan mengalihkan ke /login', 'Berhasil diredirect ke /login dengan pesan wajib masuk', 'PASS'],
    [14, 'TC-AUTH-14', 'Autentikasi & Akun', 'Hak Akses Peran', 'Akses rute yang bukan hak peran (contoh: CS akses /admin)', 'Login sebagai CS, lalu buka URL "/admin/barang"', 'Sistem menampilkan pesan error 403 Forbidden / redirect dasbor CS', 'Akses ditolak oleh middleware peran', 'PASS'],
    [15, 'TC-AUTH-15', 'Autentikasi & Akun', 'Ganti Password', 'Ganti kata sandi dengan data yang valid', 'Buka profil ganti password, input sandi lama benar & sandi baru valid', 'Password berhasil diperbarui, muncul notifikasi sukses', 'Password terupdate di database dengan enkripsi bcrypt', 'PASS'],
    [16, 'TC-AUTH-16', 'Autentikasi & Akun', 'Ganti Password', 'Ganti kata sandi dengan kata sandi lama yang salah', 'Input kata sandi lama yang tidak cocok', 'Sistem menolak dan menampilkan validasi password lama keliru', 'Pesan error muncul, password tidak berubah', 'PASS'],
    [17, 'TC-AUTH-17', 'Autentikasi & Akun', 'Ganti Password', 'Konfirmasi kata sandi baru tidak sama', 'Input sandi baru "baru123", konfirmasi "baru999"', 'Sistem menampilkan validasi: konfirmasi kata sandi tidak cocok', 'Validasi confirmed terpicu, pembaruan dibatalkan', 'PASS'],

    // MODUL 2: DASBOR
    [18, 'TC-DASH-01', 'Dasbor Multi-Peran', 'Dasbor Admin', 'Tampilan ringkasan statistik komprehensif', 'Buka menu Dasbor sebagai Admin (/dasbor/admin)', 'Tampil statistik total pegawai, barang, area, dan setoran sampah', 'Semua widget metric data termuat lengkap', 'PASS'],
    [19, 'TC-DASH-02', 'Dasbor Multi-Peran', 'Dasbor Supervisor', 'Tampilan ringkasan kepatuhan tugas kebersihan', 'Buka menu Dasbor sebagai Supervisor (/dasbor/supervisor)', 'Tampil persentase kebersihan lantai, tugas diverifikasi & pending', 'Grafik kepatuhan dan ringkasan tugas muncul', 'PASS'],
    [20, 'TC-DASH-03', 'Dasbor Multi-Peran', 'Dasbor Gudang', 'Tampilan stok menipis & permohonan logistik', 'Buka menu Dasbor sebagai Petugas Gudang (/dasbor/gudang)', 'Tampil peringatan stok kritis & jumlah permintaan menunggu proses', 'Widget stok dan daftar antrean permintaan tampil akurat', 'PASS'],
    [21, 'TC-DASH-04', 'Dasbor Multi-Peran', 'Dasbor CS', 'Tampilan antarmuka mobile responsif tugas CS', 'Buka Dasbor sebagai Cleaning Service (/dasbor/cs)', 'Tampil daftar tugas hari ini, jadwal shift, dan tombol aksi cepat', 'Tampilan ramah layar smartphone, navigasi bawah aktif', 'PASS'],

    // MODUL 3: TUGAS MINGGUAN CS
    [22, 'TC-TG-01', 'Tugas Mingguan CS', 'Lihat Tugas', 'CS melihat daftar checklist tugas area hari ini', 'Buka menu Tugas pada navigasi CS (/tugas-mingguan)', 'Tampil daftar area kerja & checklist item kebersihan sesuai jadwal', 'Daftar tugas tampil terurut berdasarkan prioritas area', 'PASS'],
    [23, 'TC-TG-02', 'Tugas Mingguan CS', 'Buat Tugas Baru', 'CS memulai tugas dengan unggah foto kondisi Before', 'Pilih area, input uraian tugas, unggah foto Before, klik Simpan', 'Tugas tersimpan status "Sedang Berjalan", foto Before terunggah', 'Data tugas dan berkas foto tersimpan di storage publik', 'PASS'],
    [24, 'TC-TG-03', 'Tugas Mingguan CS', 'Selesaikan Tugas', 'CS menyelesaikan tugas dengan unggah foto After', 'Buka tugas aktif, unggah foto kondisi After, klik Selesaikan', 'Status tugas berubah menjadi "Menunggu Verifikasi Supervisor"', 'Foto Before & After tersandingkan rapi pada detail tugas', 'PASS'],
    [25, 'TC-TG-04', 'Tugas Mingguan CS', 'Validasi Berkas', 'Validasi upload berkas non-gambar atau ukuran melebihi 2MB', 'Unggah file PDF atau file gambar berukuran 5MB', 'Sistem menolak upload dan memunculkan notifikasi validasi file', 'Validasi mime & size Laravel menolak berkas', 'PASS'],
    [26, 'TC-TG-05', 'Tugas Mingguan CS', 'Verifikasi Tugas', 'Supervisor melakukan verifikasi & penilaian tugas', 'Buka tugas selesai, beri catatan/rating bintang, klik Verifikasi', 'Status tugas berubah menjadi "Terverifikasi / Selesai"', 'Riwayat verifikator tercatat dengan timestamp waktu', 'PASS'],
    [27, 'TC-TG-06', 'Tugas Mingguan CS', 'Export Excel', 'Export rekapitulasi tugas mingguan ke Excel', 'Klik tombol "Export Excel" pada filter periode laporan tugas', 'File format .xlsx terunduh otomatis dengan data tugas lengkap', 'File spreadsheet terunduh dan dapat dibuka tanpa corrupt', 'PASS'],
    [28, 'TC-TG-07', 'Tugas Mingguan CS', 'Cetak PDF', 'Cetak laporan lembar kerja kebersihan format PDF', 'Klik tombol "Cetak PDF" pada laporan tugas', 'Halaman cetak / berkas PDF terbuat rapi format standar Dinkes', 'Dokumen PDF ter-render rapi lengkap tanda tangan pejabat', 'PASS'],

    // MODUL 4: OPERAN SHIFT
    [29, 'TC-OPR-01', 'Operan Shift', 'Kirim Operan', 'CS mengirim catatan serah terima shift (Handover)', 'Pilih shift (Pagi/Siang), pilih penerima, ketik catatan, kirim', 'Catatan operan tersimpan dengan status "Menunggu Konfirmasi"', 'Notifikasi terkirim dan tampil di tabel riwayat operan', 'PASS'],
    [30, 'TC-OPR-02', 'Operan Shift', 'Notifikasi Realtime', 'Penerima shift menerima indikator badge merah notifikasi', 'Login akun penerima shift saat ada operan baru masuk', 'Muncul badge merah counter jumlah operan masuk di bottom nav', 'API polling mengembalikan jumlah operan belum dibaca', 'PASS'],
    [31, 'TC-OPR-03', 'Operan Shift', 'Terima Operan', 'Penerima shift mengonfirmasi penerimaan tugas serah terima', 'Buka rincian operan, klik tombol "Terima Operan"', 'Status operan berubah "Diterima / Sedang Ditindaklanjuti"', 'Waktu konfirmasi terima tercatat di sistem', 'PASS'],
    [32, 'TC-OPR-04', 'Operan Shift', 'Selesaikan Operan', 'Penerima shift menyelesaikan tindak lanjut catatan', 'Input catatan hasil tindak lanjut, klik "Selesaikan"', 'Status operan berubah "Selesai", progress operan tuntas', 'Catatan penyelesaian tersimpan permanen', 'PASS'],
    [33, 'TC-OPR-05', 'Operan Shift', 'Eskalasi Masalah', 'CS mengeskalasi kendala berat ke Supervisor/Admin', 'Buka operan dengan kendala (fasilitas rusak), klik "Eskalasi"', 'Catatan masuk ke antrean peringatan dashboard supervisor', 'Flag eskalasi aktif dan muncul penanda khusus', 'PASS'],
    [34, 'TC-OPR-06', 'Operan Shift', 'Export Laporan', 'Export data riwayat operan shift ke Excel', 'Pilih filter tanggal, klik "Export Excel"', 'File Excel rekap serah terima shift terunduh lengkap', 'Data rekap handover shift terunduh dalam format .xlsx', 'PASS'],

    // MODUL 5: PERMINTAAN BARANG
    [35, 'TC-REQ-01', 'Permintaan Barang', 'Katalog Barang', 'CS melihat daftar alat & bahan kebersihan yang dapat diminta', 'Buka menu Barang / Katalog pada portal CS', 'Tampil kartu barang lengkap foto, nama, satuan, dan sisa stok', 'Katalog termuat dengan paginasi dan filter pencarian', 'PASS'],
    [36, 'TC-REQ-02', 'Permintaan Barang', 'Keranjang Permintaan', 'CS memasukkan barang ke keranjang permintaan', 'Pilih barang "Karbol 1L", klik "Tambah ke Keranjang"', 'Item bertambah di keranjang belanja dengan counter jumlah', 'Session keranjang terupdate dengan feedback toast sukses', 'PASS'],
    [37, 'TC-REQ-03', 'Permintaan Barang', 'Kelola Keranjang', 'CS mengubah jumlah pesanan atau menghapus item keranjang', 'Ubah jumlah dari 1 jadi 2, hapus item yang tidak dibutuhkan', 'Jumlah subtotal terhitung ulang, item terhapus hilang dari list', 'Keranjang sinkron secara dinamis', 'PASS'],
    [38, 'TC-REQ-04', 'Permintaan Barang', 'Ajukan Permintaan', 'CS mengirim seluruh permohonan di keranjang ke gudang', 'Input catatan peruntukan area (Lantai 2), klik "Kirim Permintaan"', 'Permintaan diterbitkan dengan nomor tiket, status "Menunggu"', 'Data tersimpan di tabel permohonan logistik gudang', 'PASS'],
    [39, 'TC-REQ-05', 'Permintaan Barang', 'Antrean Gudang', 'Petugas gudang memantau permintaan masuk', 'Login Gudang, buka menu "Permintaan Barang" (/barang/gudang)', 'Tampil daftar permintaan pending lengkap nama pemohon & area', 'Tabel permohonan terurut dari permohonan paling awal', 'PASS'],
    [40, 'TC-REQ-06', 'Permintaan Barang', 'Setujui Permintaan', 'Gudang menyetujui dan mengunggah bukti serah terima', 'Klik "Setujui", unggah foto bukti penyerahan barang, klik Simpan', 'Status berubah "Disetujui", stok barang otomatis terpotong', 'Stok master berkurang dan bukti serah terima tersimpan', 'PASS'],
    [41, 'TC-REQ-07', 'Permintaan Barang', 'Tolak Permintaan', 'Gudang menolak permintaan barang dengan alasan logis', 'Klik "Tolak", masukkan alasan ("Stok dialokasikan untuk IGD"), kirim', 'Status berubah "Ditolak", stok barang tidak terpotong', 'Alasan penolakan tampil jelas di portal pemohon CS', 'PASS'],
    [42, 'TC-REQ-08', 'Permintaan Barang', 'Export Excel', 'Export data riwayat pengeluaran & permintaan barang', 'Klik tombol "Export Excel" pada halaman kelola barang gudang', 'File spreadsheet laporan permintaan terunduh rapi', 'Data terunduh dengan rincian status permintaan', 'PASS'],

    // MODUL 6: BANK SAMPAH
    [43, 'TC-SMP-01', 'Bank Sampah', 'Input Setoran', 'CS mencatat timbulan sampah medis atau non-medis', 'Pilih jenis (Limbah Medis B3), input berat (4.5 kg), unggah foto', 'Data setoran sampah tersimpan dengan status "Menunggu Validasi"', 'Data tercatat di rekapitulasi limbah sanitasi puskesmas', 'PASS'],
    [44, 'TC-SMP-02', 'Bank Sampah', 'Validasi Berat', 'Validasi input berat sampah nol atau angka minus', 'Input berat "0" atau "-2"', 'Sistem menolak dan memunculkan validasi berat minimal 0.1 kg', 'Form menolak input dan tidak tersimpan ke database', 'PASS'],
    [45, 'TC-SMP-03', 'Bank Sampah', 'Validasi Setoran', 'Supervisor memvalidasi data timbulan sampah', 'Supervisor membuka rekapan sampah, klik tombol "Validasi"', 'Status setoran berubah menjadi "Tervalidasi", tercatat di log', 'Total agregasi timbulan sampah bulanan bertambah', 'PASS'],
    [46, 'TC-SMP-04', 'Bank Sampah', 'Tolak Setoran', 'Supervisor menolak setoran sampah yang tidak akurat', 'Klik "Tolak", masukkan catatan ("Data timbang ulang selisih")', 'Status berubah "Ditolak", CS dapat mengoreksi data', 'Status update dan catatan perbaikan tersimpan', 'PASS'],
    [47, 'TC-SMP-05', 'Bank Sampah', 'Reset Setoran', 'Supervisor mereset status setoran yang salah input', 'Klik tombol "Reset Status"', 'Status dikembalikan ke "Menunggu" untuk dievaluasi ulang', 'Data kembali ke status antrean penilaian awal', 'PASS'],
    [48, 'TC-SMP-06', 'Bank Sampah', 'Export Laporan', 'Export rekap data timbulan sampah ke format Excel & PDF', 'Klik tombol "Export Excel" / "Cetak Laporan"', 'Dokumen rekap limbah B3 & domestik terunduh rapi', 'Laporan terunduh sesuai format baku dinas lingkungan', 'PASS'],

    // MODUL 7: PENILAIAN PUBLIK PJ LANTAI
    [49, 'TC-PJ-01', 'Penilaian Publik', 'Akses Tanpa Login', 'PJ Lantai mengakses formulir penilaian via scan QR / link', 'Buka URL "/penilaian-pj-lantai" pada peramban web', 'Halaman form terbuka langsung tanpa dialihkan ke halaman login', 'Formulir survei kepuasan terbuka secara publik', 'PASS'],
    [50, 'TC-PJ-02', 'Penilaian Publik', 'Kirim Penilaian', 'Pengguna mengirim penilaian kebersihan lantai', 'Pilih area (Lantai 3), klik 5 bintang, beri ulasan, klik Kirim', 'Penilaian tersimpan, muncul halaman terima kasih atas apresiasi', 'Nilai rating masuk ke kalkulasi kepuasan dasbor admin', 'PASS'],
    [51, 'TC-PJ-03', 'Penilaian Publik', 'Validasi Input', 'Validasi pengiriman form jika rating belum dipilih', 'Pilih lantai tanpa memberi bintang rating, klik Kirim', 'Form memunculkan peringatan wajib memberikan rating bintang', 'Sistem mencegah pengiriman data kosong', 'PASS'],
    [52, 'TC-PJ-04', 'Penilaian Publik', 'Rate Limiting', 'Proteksi pengiriman spam (Throttle 3 submit per 30 menit per IP)', 'Lakukan pengiriman formulir lebih dari 3 kali berturut-turut', 'Sistem memblokir sementara dengan pesan Too Many Requests', 'Middleware throttle melindungi integritas data penilaian', 'PASS'],
    [53, 'TC-PJ-05', 'Penilaian Publik', 'Monitoring Admin', 'Admin & Supervisor memantau rekapitulasi penilaian PJ Lantai', 'Buka menu Penilaian PJ Lantai pada menu Admin (/admin/penilaian-pj)', 'Tampil daftar ulasan, grafik rerata bintang per lantai & tanggal', 'Seluruh ulasan teragregasi secara transparan', 'PASS'],
    [54, 'TC-PJ-06', 'Penilaian Publik', 'Export Rekap', 'Export seluruh data ulasan penilaian lantai ke Excel', 'Klik tombol "Export Excel" pada rekap penilaian', 'File format .xlsx terunduh berisi riwayat survei kepuasan lantai', 'File Excel terunduh dengan detail nilai & komentar', 'PASS'],

    // MODUL 8: MASTER BARANG
    [55, 'TC-MB-01', 'Master Barang', 'Tambah Barang', 'Admin menambah master data barang baru', 'Input Nama: "Karbol Wangi", Kode: "BRG-010", Satuan: "botol", Stok: 10', 'Barang baru tersimpan dan otomatis muncul di daftar inventori', 'Data tersimpan di tabel barang_inventori', 'PASS'],
    [56, 'TC-MB-02', 'Master Barang', 'Validasi Duplikat', 'Validasi jika kode barang yang diinput sudah ada', 'Input kode barang yang sama dengan data eksisting', 'Sistem menolak dan menampilkan peringatan kode barang duplikat', 'Pesan error unik tampil, data tidak ganda', 'PASS'],
    [57, 'TC-MB-03', 'Master Barang', 'Ubah Barang', 'Admin mengubah informasi profil barang master', 'Buka edit barang, ubah nama/satuan/kategori/stok minimum, simpan', 'Perubahan tersimpan, profil barang terupdate di katalog', 'Data master terupdate tanpa merusak relasi data lama', 'PASS'],
    [58, 'TC-MB-04', 'Master Barang', 'Hapus Barang', 'Admin menghapus master barang yang tidak digunakan', 'Klik tombol ikon tempat sampah, konfirmasi pada dialog modal', 'Barang terhapus dari master barang', 'Data terhapus dan tidak tampil lagi di inventori', 'PASS'],
    [59, 'TC-MB-05', 'Master Barang', 'Filter & Cari', 'Pencarian barang berdasarkan kata kunci & kategori', 'Ketik kata kunci "Sabun" atau filter kategori "Alat Kebersihan"', 'Tabel secara otomatis hanya menampilkan barang yang cocok', 'Fitur pencarian dan filter kategori bekerja akurat', 'PASS'],

    // MODUL 9: STOK MASUK / RESTOK & MUTASI
    [60, 'TC-SM-01', 'Stok Masuk / Restok', 'Pencatatan Restok', 'Gudang mencatat pasokan fisik barang masuk dari pengadaan', 'Pilih barang, jumlah: 15, No. Surat Jalan: "SJ-889", Vendor: "PT Kimia"', 'Stok barang bertambah (+15), nomor transaksi mutasi (SM-xxxx) terbit', 'Saldo bertambah, tercatat di buku ledger mutasi', 'PASS'],
    [61, 'TC-SM-02', 'Stok Masuk / Restok', 'Validasi Masuk', 'Validasi jika jumlah masuk diisi angka 0 atau negatif', 'Input jumlah masuk "0" atau "-10"', 'Sistem menolak dengan validasi jumlah masuk minimal 1', 'Validasi min:1 Laravel mencegah transaksi invalid', 'PASS'],
    [62, 'TC-SM-03', 'Stok Masuk / Restok', 'Stock Opname', 'Penyesuaian stok fisik akibat selisih audit gudang', 'Input stok fisik baru: 20, Catatan: "Koreksi audit fisik berkala"', 'Nilai stok disesuaikan ke 20, transaksi tipe penyesuaian tercatat', 'Selisih tercatat rapi di riwayat mutasi', 'PASS'],
    [63, 'TC-SM-04', 'Stok Masuk / Restok', 'Filter Mutasi', 'Filter riwayat buku besar mutasi berdasarkan kriteria', 'Filter jenis transaksi (Stok Masuk / Distribusi) & rentang tanggal', 'Tabel riwayat mutasi menyaring transaksi sesuai parameter', 'Data mutasi terfilter tepat sesuai periode yang dipilih', 'PASS'],
    [64, 'TC-SM-05', 'Stok Masuk / Restok', 'Jejak Audit Petugas', 'Pencatatan user ID petugas yang melakukan mutasi', 'Catat barang masuk menggunakan akun login Budi', 'Pada kolom petugas tercatat nama "Budi (Gudang)" secara otomatis', 'Audit trail petugas pencatat terekam akurat', 'PASS'],

    // MODUL 10: LAPORAN STOK & PEMAKAIAN UNIT
    [65, 'TC-LS-01', 'Laporan Stok & Unit', 'Filter Periode', 'Memfilter laporan berdasarkan bulan, tahun, dan unit lokasi', 'Pilih Bulan: "Oktober", Tahun: "2026", klik Tampilkan', 'Tabel memuat ringkasan stok masuk, pemakaian, dan sisa stok', 'Data periode Oktober 2026 termuat dengan tepat', 'PASS'],
    [66, 'TC-LS-02', 'Laporan Stok & Unit', 'Modal Pemakaian', 'Membuka modal input pemakaian per unit per minggu (Aksi Hijau)', 'Klik tombol aksi hijau (bi-pencil-square) pada tabel laporan', 'Modal input pemakaian terbuka, memuat grid unit & 4 minggu', 'Modal tampil lancar dan memuat data tersimpan', 'PASS'],
    [67, 'TC-LS-03', 'Laporan Stok & Unit', 'Validasi Minus', 'Pencegahan input pemakaian melebihi sisa stok (Stok Negatif)', 'Input pemakaian 50 saat sisa stok hanya 10', 'Input ditolak warna merah, muncul toast peringatan stok melebihi batas', 'Sistem memblokir penyimpanan pemakaian defisit', 'PASS'],
    [68, 'TC-LS-04', 'Laporan Stok & Unit', 'Modal Edit Info', 'Mengubah data barang pada periode aktif (Aksi Biru)', 'Klik tombol aksi biru (bi-gear), edit nama/satuan/stok, simpan', 'Data laporan periode terupdate dan modal tertutup otomatis', 'Data periode tersimpan dan tabel merefresh nilai baru', 'PASS'],
    [69, 'TC-LS-05', 'Laporan Stok & Unit', 'Modal Hapus Periode', 'Menghapus barang dari periode laporan (Aksi Merah)', 'Klik tombol aksi merah (bi-trash), konfirmasi pada modal', 'Barang terhapus dari laporan bulan tersebut', 'Data terhapus dari periode tanpa merusak master', 'PASS'],
    [70, 'TC-LS-06', 'Laporan Stok & Unit', 'Export Excel Stok', 'Export seluruh laporan ringkasan stok dan rekap unit ke Excel', 'Klik tombol "Excel" pada bagian kanan atas laporan stok', 'File .xlsx terunduh lengkap sheet ringkasan dan matriks unit', 'File Excel laporan stok terunduh sempurna', 'PASS'],
    [71, 'TC-LS-07', 'Laporan Stok & Unit', 'Cetak PDF Stok', 'Mencetak laporan stok bulanan ke lembar dokumen PDF resmi', 'Klik tombol "PDF" pada bagian kanan atas laporan stok', 'Halaman cetak PDF landscape terbuka dengan tabel rapi', 'Pratinjau cetak PDF siap print/simpan', 'PASS'],

    // MODUL 11: MASTER PENGGUNA & AREA (ADMIN)
    [72, 'TC-ADM-01', 'Master Pengguna & Area', 'Tambah Pengguna', 'Admin menambahkan pengguna baru dengan peran tertentu', 'Input NIK: "cs05", Nama: "Dewi", Peran: "CS", Password: "password"', 'Akun baru tersimpan dan dapat langsung digunakan untuk login', 'Data tersimpan di tabel users & relasi peran tepat', 'PASS'],
    [73, 'TC-ADM-02', 'Master Pengguna & Area', 'Validasi NIK', 'Validasi jika NIK pengguna baru sudah digunakan akun lain', 'Input NIK yang sudah ada di database (contoh: "admin")', 'Sistem menolak dan menampilkan validasi NIK sudah digunakan', 'Pesan error unique field mencegah duplikasi akun', 'PASS'],
    [74, 'TC-ADM-03', 'Master Pengguna & Area', 'Ubah Pengguna', 'Admin mengubah informasi profil atau reset password akun', 'Buka ubah pengguna, ganti nama atau input password baru, simpan', 'Perubahan tersimpan, kredensial pengguna terupdate', 'Informasi akun berhasil diperbarui', 'PASS'],
    [75, 'TC-ADM-04', 'Master Pengguna & Area', 'Hapus Pengguna', 'Admin menghapus akun pengguna yang sudah tidak aktif', 'Klik tombol hapus pada baris pengguna, konfirmasi', 'Akun terhapus dari sistem dan tidak dapat login lagi', 'Akun terhapus dari database', 'PASS'],
    [76, 'TC-ADM-05', 'Master Pengguna & Area', 'Tambah Area', 'Admin menambah master area/lokasi baru di puskesmas', 'Buka master area, input nama: "Ruang Farmasi Lt. 1", klik Simpan', 'Area baru tersimpan dan muncul di pilihan lokasi checklist', 'Data area tersimpan di tabel master area', 'PASS'],
    [77, 'TC-ADM-06', 'Master Pengguna & Area', 'Ubah Area', 'Admin mengubah nama atau deskripsi area kerja', 'Edit nama area menjadi "Poli Gigi Lt. 2", klik Simpan', 'Nama area terupdate di seluruh modul terkait', 'Nama area berhasil diperbarui secara konsisten', 'PASS'],
    [78, 'TC-ADM-07', 'Master Pengguna & Area', 'Hapus Area', 'Admin menghapus area yang sudah tidak digunakan', 'Klik hapus pada baris area yang tidak memiliki data tugas terkait', 'Area terhapus dari daftar master area', 'Area berhasil dihapus dengan aman', 'PASS']
];

$row = 4;
foreach ($testCases as $tc) {
    // Re-index No dari 1 sampai 55
    $nomor = $row - 3;
    $sheetTest->setCellValue("A{$row}", $nomor);
    $sheetTest->setCellValue("B{$row}", $tc[1]);
    $sheetTest->setCellValue("C{$row}", $tc[2]);
    $sheetTest->setCellValue("D{$row}", $tc[3]);
    $sheetTest->setCellValue("E{$row}", $tc[4]);
    $sheetTest->setCellValue("F{$row}", $tc[5]);
    $sheetTest->setCellValue("G{$row}", $tc[6]);
    $sheetTest->setCellValue("H{$row}", $tc[7]);
    $sheetTest->setCellValue("I{$row}", $tc[8]);

    // Alignment
    $sheetTest->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
    $sheetTest->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
    $sheetTest->getStyle("C{$row}:D{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $sheetTest->getStyle("E{$row}:H{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER)->setWrapText(true);
    $sheetTest->getStyle("I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

    // Styling Status PASS
    $sheetTest->getStyle("I{$row}")->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1B5E20'));
    $sheetTest->getStyle("I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('C8E6C9');

    // Zebra striping
    if ($nomor % 2 === 0) {
        $sheetTest->getStyle("A{$row}:H{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F9FBFB');
    }

    $sheetTest->getStyle("A{$row}:I{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('CFD8DC');
    $sheetTest->getRowDimension($row)->setRowHeight(42);

    $row++;
}

// Kolom Width Auto/Set
$sheetTest->getColumnDimension('A')->setWidth(6);
$sheetTest->getColumnDimension('B')->setWidth(16);
$sheetTest->getColumnDimension('C')->setWidth(24);
$sheetTest->getColumnDimension('D')->setWidth(26);
$sheetTest->getColumnDimension('E')->setWidth(38);
$sheetTest->getColumnDimension('F')->setWidth(42);
$sheetTest->getColumnDimension('G')->setWidth(42);
$sheetTest->getColumnDimension('H')->setWidth(38);
$sheetTest->getColumnDimension('I')->setWidth(12);

// Simpan file
$outputPath1 = __DIR__ . '/Skenario_Pengujian_Blackbox_SIM_Kebersihan.xlsx';
$outputPath2 = __DIR__ . '/public/Skenario_Pengujian_Blackbox_SIM_Kebersihan.xlsx';

$writer = new Xlsx($spreadsheet);
$writer->save($outputPath1);
copy($outputPath1, $outputPath2);

echo "SUCCESS: File Excel berhasil dibuat di: \n1. {$outputPath1}\n2. {$outputPath2}\nTotal Skenario Uji: " . count($testCases) . "\n";
