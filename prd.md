# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## 1. Document Information
**Nama Project:** Rancang Bangun Sistem Informasi Monitoring dan Penilaian Kinerja Cleaning Service Berbasis Web Dengan Metode Prototype
**Client:** Puskesmas Cempaka Putih
**Penulis:** Dian Rifan Abdulah
**Tanggal:** 15 Juli 2026
**Versi Dokumen:** 1.0

---

## 2. Background
Pengelolaan kebersihan di fasilitas kesehatan seperti Puskesmas Cempaka Putih memerlukan standar yang tinggi dan pemantauan yang ketat. Saat ini, proses monitoring kinerja tenaga kebersihan (PJLP) dan operasional masih menggunakan metode semi-manual dan Google Form. Hal ini memicu berbagai kendala, mulai dari sulitnya supervisor melakukan pemantauan area secara *real-time*, hilangnya informasi saat operan shift, pengelolaan stok inventori alat kebersihan yang tidak sinkron, hingga pencatatan bank sampah yang terpisah. Ketiadaan sebuah *dashboard* terpusat membuat pembuatan laporan kinerja bulanan menjadi lambat dan rentan terhadap kesalahan data.

---

## 3. Problem Statement
*   **Data Tersebar:** Penggunaan media yang berbeda (Google Form, buku manual, WhatsApp) membuat data sulit dilacak.
*   **Monitoring Sulit:** Supervisor tidak memiliki visibilitas *real-time* mengenai area mana yang sudah dibersihkan.
*   **Rekapitulasi Manual:** Pembuatan laporan bulanan memakan waktu lama karena harus menarik dan menggabungkan data dari berbagai sumber.
*   **Bukti Kerja Lemah:** Foto pekerjaan sebelum dan sesudah (*Before/After*) tidak terintegrasi langsung dengan waktu (timestamp) dan lokasi yang valid.
*   **Stok Tidak Real-time:** Permintaan dan pemakaian barang tidak tercatat otomatis, menyebabkan kehabisan stok yang tidak terprediksi.
*   **Operan Shift Tidak Jelas:** Instruksi pekerjaan yang belum selesai sering terputus antar pergantian shift.
*   **Penilaian Kinerja Subjektif:** Proses penilaian (KPI) masih manual dan berpotensi tidak transparan.

---

## 4. Goals
*   **Digitalisasi Monitoring:** Memastikan setiap area terpantau status kebersihannya secara *real-time*.
*   **Digitalisasi Operan:** Memastikan kelancaran transisi tugas antar shift kerja.
*   **Digitalisasi Inventori:** Memantau ketersediaan alat dan bahan pembersih secara akurat.
*   **Digitalisasi Bank Sampah:** Mengelola pencatatan setoran sampah daur ulang secara sistematis.
*   **Mempermudah Supervisor:** Mengotomatiskan validasi foto kerja (*Before/After*) dengan fitur *geolocation* dan *timestamp*.
*   **Mempercepat Laporan:** Menghasilkan laporan kinerja dan operasional secara otomatis dalam format PDF dan Excel.

---

## 5. Scope
**In Scope**
*   Manajemen User dan Autentikasi (Role-based access).
*   Checklist Harian & Mingguan (dengan upload foto, waktu, dan lokasi).
*   Sistem Operan Shift.
*   Manajemen Permintaan & Stok Barang (Inventori).
*   Pencatatan Bank Sampah.
*   Dashboard Monitoring (Sesuai Role).
*   Penilaian Kinerja (*Manual Input* oleh Supervisor).
*   Laporan Export (PDF & Excel).

**Out of Scope**
*   Modul Absensi dan Payroll.
*   Integrasi Mesin Presensi Fingerprint.
*   Perhitungan KPI Full Otomatis tanpa intervensi.
*   Integrasi ke SIMPUS (Sistem Informasi Manajemen Puskesmas).
*   Pembuatan Aplikasi Mobile Native (Android/iOS) — digantikan dengan web responsif.

---

## 6. Stakeholder
*   **Administrator:** Mengelola data master (user, area, barang, kategori sampah).
*   **Supervisor:** Memantau operasional, melakukan validasi operan, dan memberikan penilaian kinerja.
*   **PJ Lantai (Penanggung Jawab):** Mengkoordinasi tugas per lantai dan memvalidasi ceklis sementara.
*   **Cleaning Service (CS):** Mengisi ceklis kebersihan, melakukan operan, dan meminta barang.
*   **Petugas Gudang:** Memvalidasi permintaan barang dan mengelola stok fisik.

---

## 7. User Persona

**1. Cleaning Service (Bpk. Ahmad)**
*   **Usia:** 45 - 60 tahun.
*   **Karakteristik:** Tidak terbiasa menggunakan komputer/PC. Hanya familiar dengan *smartphone* untuk membuka WhatsApp. Penglihatan mulai menurun.
*   **Kebutuhan Sistem:** UI sangat sederhana, warna kontras tinggi, tombol besar, tulisan besar, minim ketikan, dan alur kerja sekali klik (seperti langsung buka kamera).

**2. Supervisor (Ibu Rina)**
*   **Usia:** 30 - 50 tahun.
*   **Karakteristik:** Memiliki mobilitas tinggi keliling puskesmas. Cukup mahir menggunakan *smartphone* dan tablet/laptop.
*   **Kebutuhan Sistem:** Visualisasi data yang cepat, sistem persetujuan (*approval*) yang ringkas, dan fitur ekspor laporan yang mudah diakses.

---

## 8. Business Process

| Modul | Proses As Is | Proses To Be |
| :--- | :--- | :--- |
| **Checklist** | Petugas mengisi kertas form atau Google Form di akhir shift; foto dikirim via grup WA. | Petugas mengisi form langsung di sistem dengan tombol kamera. Sistem otomatis menyimpan waktu dan lokasi. |
| **Operan** | Dicatat di buku serah terima, sering terlewat dibaca oleh shift berikutnya. | Input langsung di sistem. Shift berikutnya wajib menekan tombol "Terima Operan" sebelum mulai kerja. |
| **Inventori** | Petugas meminta barang secara lisan, gudang mencatat di buku besar. | Petugas *request* via sistem. Gudang klik *approve*, stok otomatis berkurang. |
| **Laporan** | Supervisor merekap manual dari Google Sheet dan WA ke Excel baru setiap akhir bulan. | Supervisor memilih rentang waktu di sistem dan klik "Export PDF/Excel", laporan langsung jadi. |

---

## 9. Functional Requirement

**Modul Autentikasi & Akun**
*   FR-001: Sistem harus memiliki form login dengan NIK dan Password.
*   FR-002: Sistem harus membedakan akses berdasarkan Role (Admin, Supervisor, PJ Lantai, CS, Gudang).
*   FR-003: Sistem harus memungkinkan pengguna mengubah kata sandi.

**Modul Dashboard**
*   FR-004: Dashboard menampilkan ringkasan tugas selesai vs total tugas harian.
*   FR-005: Dashboard menampilkan metrik persentase kebersihan.
*   FR-006: Dashboard menampilkan tombol pintasan (*shortcut*) fitur utama.

**Modul Checklist Kebersihan**
*   FR-007: Sistem menampilkan daftar area yang harus dibersihkan sesuai shift login.
*   FR-008: Sistem wajib menangkap foto kondisi sebelum (*Before*).
*   FR-009: Sistem wajib menangkap foto kondisi sesudah (*After*).
*   FR-010: Sistem secara otomatis melampirkan metadata HTML5 Geolocation (Latitude & Longitude) pada setiap foto.
*   FR-011: Sistem secara otomatis melampirkan *timestamp* pada setiap proses upload.
*   FR-012: Sistem menyimpan foto ke dalam Laravel Storage.
*   FR-013: Sistem membatasi ukuran maksimal upload foto (misal: 2MB) dengan kompresi.

**Modul Operan Shift**
*   FR-014: CS shift aktif dapat memilih nama petugas pengganti.
*   FR-015: CS dapat mencentang status ketersediaan/kerusakan peralatan (misal: sapu, pel).
*   FR-016: CS dapat menambahkan catatan teks terkait pekerjaan yang tertunda.
*   FR-017: Sistem memberikan notifikasi kepada penerima operan di halaman utama mereka.
*   FR-018: Penerima operan harus melakukan konfirmasi penerimaan operan.

**Modul Inventori Gudang**
*   FR-019: Sistem menampilkan katalog barang (sabun, tisu, dll.) beserta stok *real-time*.
*   FR-020: CS dapat membuat form *request* barang dengan memasukkan jumlah yang dibutuhkan.
*   FR-021: Petugas gudang dapat melihat daftar permintaan barang berstatus *Pending*.
*   FR-022: Petugas gudang dapat melakukan *Approve* atau *Reject* beserta alasan.
*   FR-023: Sistem otomatis memotong stok barang jika permintaan di-*approve*.

**Modul Bank Sampah**
*   FR-024: CS dapat memilih kategori sampah (Plastik, Kardus, Logam, dll).
*   FR-025: CS dapat memasukkan berat sampah dalam kilogram.
*   FR-026: Sistem dapat mengambil foto timbangan sebagai bukti.
*   FR-027: Supervisor dapat memvalidasi data setoran sampah.

**Modul Penilaian Kinerja & Monitoring**
*   FR-028: Supervisor dapat melihat seluruh data ceklis yang masuk pada hari tersebut.
*   FR-029: Supervisor dapat memberikan skor penilaian pada tiap hasil ceklis (Bintang 1-5 atau nilai angka).
*   FR-030: Supervisor dapat menambahkan catatan evaluasi.

**Modul Manajemen Master Data (Admin)**
*   FR-031: Admin dapat melakukan Create, Read, Update, Delete (CRUD) data User.
*   FR-032: Admin dapat melakukan CRUD master Area Puskesmas.
*   FR-033: Admin dapat melakukan CRUD master Barang Inventori.

**Modul Laporan**
*   FR-034: Sistem menyediakan fitur *filter* laporan berdasarkan tanggal mulai dan tanggal akhir.
*   FR-035: Sistem dapat mengekspor laporan Ceklis dalam bentuk PDF (menggunakan DomPDF).
*   FR-036: Sistem dapat mengekspor laporan Ceklis dalam bentuk Excel (menggunakan Laravel Excel).
*   FR-037: Sistem dapat mengekspor riwayat pemakaian barang.

---

## 10. Non Functional Requirement
*   **Performance:** Halaman harus dimuat di bawah 3 detik. Penggunaan AJAX (jQuery) untuk form tanpa *reload*.
*   **Availability:** Sistem dapat diakses 24/7 melalui server internal/eksternal puskesmas.
*   **Security:** Menggunakan perlindungan CSRF, enkripsi *password* menggunakan Bcrypt, dan validasi form ketat oleh Laravel.
*   **Responsiveness:** Antarmuka harus 100% responsif dan berfungsi optimal di *smartphone* lanskap/potret.
*   **Usability:** Menu navigasi dikurangi maksimal 3 tingkat klik untuk mencapai fungsi utama.

---

## 11. Use Case List
*   UC-01: Melakukan Login
*   UC-02: Melakukan Logout
*   UC-03: Melihat Dashboard Area Pribadi
*   UC-04: Mengelola Data Area (Admin)
*   UC-05: Mengelola Data User (Admin)
*   UC-06: Memilih Area Kerja (CS)
*   UC-07: Mengunggah Foto Before (CS)
*   UC-08: Mengunggah Foto After (CS)
*   UC-09: Menginput Data Operan (CS)
*   UC-10: Menerima Operan (CS)
*   UC-11: Melihat Katalog Barang (CS)
*   UC-12: Mengajukan Permintaan Barang (CS)
*   UC-13: Menyetujui Permintaan Barang (Gudang)
*   UC-14: Menginput Setoran Sampah (CS)
*   UC-15: Memvalidasi Setoran Sampah (Supervisor)
*   UC-16: Memberikan Nilai Kinerja (Supervisor)
*   UC-17: Melihat Rekap Kinerja (Supervisor)
*   UC-18: Mengekspor Laporan Ceklis (Supervisor/Admin)
*   UC-19: Mengekspor Laporan Inventori (Gudang/Admin)

---

## 12. Workflow (Contoh: Workflow Checklist)
1.  **Mulai:** CS menekan tombol "Isi Checklist" di Dashboard.
2.  **Pemilihan Area:** CS memilih area dari daftar ruangan yang menjadi tanggung jawabnya.
3.  **Proses Before:** CS menekan tombol kamera "Before". Browser meminta izin akses lokasi. Kamera terbuka, foto diambil, sistem mencatat koordinat dan waktu.
4.  **Pekerjaan:** CS membersihkan ruangan.
5.  **Proses After:** CS menekan tombol kamera "After". Foto diambil, koordinat dan waktu dicatat kembali.
6.  **Selesai:** CS menekan "Simpan Data". Data masuk ke tabel `checklist` beserta jalur file di folder storage. Sistem mengembalikan CS ke Dashboard.

---

## 13. UI Requirement
*   **Warna Utama:** Biru Laut (#1E40AF) untuk identitas profesional, Hijau (#10B981) untuk indikator positif/berhasil, Abu-abu Muda (#F8F9FA) untuk latar belakang.
*   **Typography:** Inter atau Roboto (Bawaan Bootstrap/Google Fonts) dengan ukuran *font* minimum 16px.
*   **Button:** Tombol berukuran minimal 48x48px (Touch-friendly), bersudut melengkung (*rounded-pill* atau *rounded-3*).
*   **Card:** Memiliki bayangan lembut (`box-shadow`), tanpa garis tepi tebal, berjarak yang cukup antar elemen (`padding`).
*   **Icon:** Menggunakan Bootstrap Icons (`bi-camera`, `bi-recycle`, dll.) dengan ukuran besar.
*   **Layout:** Menerapkan pembatasan lebar grid pada *desktop* (`col-md-6 col-lg-4`) agar selalu terpusat dan menyerupai dimensi layar *handphone*.

---

## 14. Dashboard Requirement
*   **Dashboard CS:** Berfokus pada kartu metrik kebersihan pribadi harian dan menu grid 2x2 berukuran besar (Checklist, Operan, Barang, Sampah). Tanpa navigasi *header/sidebar* yang mengganggu.
*   **Dashboard Supervisor:** Berisi ringkasan grafis (Chart) jumlah area bersih vs kotor secara keseluruhan gedung, serta notifikasi *real-time* ceklis yang baru masuk.
*   **Dashboard Gudang:** Menampilkan angka stok yang akan habis (*low stock alert*) dan jumlah *pending request* barang dari CS.
*   **Dashboard Admin:** Menampilkan total pengguna aktif, statistik keseluruhan, dan jalan pintas ke manajemen *database*.

---

## 15. Database Requirement
Sistem menggunakan MySQL. Struktur tabel utama meliputi:
*   `users` (id, nama, nik, password, role_id, shift, area_id)
*   `roles` (id, nama_role)
*   `areas` (id, nama_ruangan, lantai)
*   `checklists` (id, user_id, area_id, tanggal, waktu_mulai, waktu_selesai, foto_before, foto_after, lat_long, status, skor, catatan)
*   `operans` (id, pengirim_id, penerima_id, tanggal, waktu, status_alat, catatan, status_terima)
*   `inventories` (id, nama_barang, deskripsi, foto_barang, stok_saat_ini, satuan)
*   `inventory_requests` (id, user_id, inventory_id, jumlah, status_request, waktu_request, waktu_approve)
*   `waste_banks` (id, user_id, jenis_sampah, berat_kg, foto_timbangan, tanggal)

---

## 16. Reporting
*   **Laporan Checklist:** Memuat nama CS, area, waktu kedatangan, waktu selesai, tautan foto bukti, dan skor dari supervisor.
*   **Laporan Operan:** Memuat catatan perpindahan tugas harian.
*   **Laporan Inventori:** Memuat mutasi masuk/keluar barang bulanan.
*   **Laporan Bank Sampah:** Memuat rekapitulasi total kilogram sampah daur ulang per kategori dalam sebulan.

---

## 17. Future Development (Roadmap)
*   **KPI Otomatis:** Perhitungan insentif/skor langsung berdasarkan kecepatan penyelesaian tugas.
*   **Integrasi SIMPUS:** Menarik data jadwal poli untuk prioritas pembersihan otomatis.
*   **Notifikasi WhatsApp:** Menggunakan API pihak ketiga agar notifikasi operan langsung masuk ke WhatsApp.
*   **QR Code:** CS cukup memindai stiker QR Code di pintu ruangan untuk membuka form ceklis.

---

## 18. Appendix
*   **Tech Stack:** PHP 8.x, Laravel 12 (MVC), MySQL, HTML5/CSS3, Bootstrap 5, jQuery/ES6, Git, Laragon, Cursor AI.
*   **Metodologi:** Prototype (Komunikasi - Desain Cepat - Pembuatan Prototipe - Evaluasi User - Refinement - Implementasi Final).