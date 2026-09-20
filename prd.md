# PRODUCT REQUIREMENTS DOCUMENT (PRD)

## 1. Informasi Dokumen
| | |
|---|---|
| **Nama Sistem** | SIM Kebersihan — Sistem Informasi Monitoring dan Penilaian Kinerja Cleaning Service |
| **Klien** | Puskesmas Kecamatan Cempaka Putih, Jakarta Pusat |
| **Penulis** | Dian Rifan Abdulah |
| **Tanggal Awal** | 15 Juli 2026 |
| **Tanggal Update PRD** | 19 September 2026 |
| **Versi Dokumen** | 2.0 (Updated — sesuai implementasi aktual) |
| **Status** | ✅ Production-ready |

---

## 2. Latar Belakang

Pengelolaan kebersihan di Puskesmas Kecamatan Cempaka Putih sebelumnya mengandalkan Google Form, buku manual, dan grup WhatsApp. Proses ini menyulitkan supervisor dalam memantau area secara real-time, mengelola operan shift, dan membuat laporan kinerja bulanan.

Sistem ini dibangun untuk mendigitalisasi seluruh proses operasional kebersihan: monitoring area, serah terima shift, pengelolaan inventori, bank sampah, penilaian kinerja, dan pelaporan — dalam satu platform web yang dapat diakses via smartphone.

---

## 3. Tech Stack Aktual

| Layer | Teknologi |
|---|---|
| **Backend** | PHP 8.1+, Laravel 10.x (MVC) |
| **Frontend** | Bootstrap 5, Bootstrap Icons, Vanilla JS (ES6) |
| **Database** | MySQL |
| **Auth** | Laravel UI (session-based), Middleware `CekPeran` (role-based) |
| **PDF** | barryvdh/laravel-dompdf ^3.1 |
| **Excel** | maatwebsite/excel (FromView + FromArray) |
| **Image Processing** | PHP GD (built-in) via `App\Helpers\KompresiFoto` |
| **Development** | Laragon, Vite, PHP Artisan |

---

## 4. Peran Pengguna (Role)

| Peran | Deskripsi | Portal |
|---|---|---|
| `admin` | Mengelola seluruh data master, melihat semua modul | Sidebar (Desktop) |
| `supervisor` | Memantau, memverifikasi, dan membuat laporan | Sidebar (Desktop) |
| `pj_lantai` | Koordinasi lantai, akses supervisi terbatas | Sidebar (Desktop) |
| `cs` | Petugas kebersihan, input data operasional | Mobile UI (Bottom Nav) |
| `gudang` | Kelola inventori dan permintaan barang | Sidebar (Desktop) |
| Publik | Mengisi formulir penilaian PJ Lantai tanpa login | Form Publik |

**Logika Redirect Otomatis (`User::ruteDasbor()`):**
- `admin` → `/dasbor/admin`
- `supervisor`, `pj_lantai` → `/dasbor/supervisor`
- `cs` → `/dasbor/cs`
- `gudang` → `/dasbor/gudang`

---

## 5. Arsitektur Layout

Sistem menggunakan dua layout berbeda berdasarkan peran dan route:

### Sidebar Layout (Admin, Supervisor, PJ Lantai, Gudang)
- Sidebar fixed kiri (260px)
- Header mobile sticky (hamburger menu)
- Responsive hingga breakpoint 992px
- Konfirmasi logout via modal Bootstrap

### Mobile UI (CS, PJ Lantai di halaman tertentu)
- Full-screen tanpa sidebar
- Bottom Navigation Bar (5 menu: Beranda, Tugas, Operan, Barang, Sampah)
- Header gradient hijau dengan overlap card
- Badge notifikasi operan masuk di bottom nav (polling AJAX setiap 30 detik)
- Konfirmasi logout via modal di dasbor CS

---

## 6. Database — Tabel Aktif

| Tabel | Model | Keterangan |
|---|---|---|
| `users` | `User` | Akun pengguna, field: name, nik, password, peran_id |
| `peran` | `Peran` | Role: admin, supervisor, pj_lantai, cs, gudang |
| `area` | `Area` | Daftar lantai/ruangan yang dibersihkan |
| `ceklis` | `CeklisKebersihan` | Ceklis harian (foto before+after, GPS, skor) |
| `tugas_mingguan` | `TugasMingguan` | Tugas berkala CS di luar rutinitas (multi-foto) |
| `operan` | `OperanShift` | Serah terima shift (eskalasi self-referential) |
| `permintaan_barang` | `PermintaanBarang` | Request barang CS ke gudang |
| `barang_inventori` | `BarangInventori` | Master stok fisik barang |
| `produk_kebersihan` | `ProdukKebersihan` | Master produk untuk laporan stok |
| `laporan_stok` | `LaporanStok` | Laporan stok per produk per periode |
| `pemakaian_stok` | `PemakaianStok` | Pemakaian per unit per minggu |
| `setoran_sampah` | `SetoranSampah` | Setoran sampah daur ulang CS |
| `penilaian_pj_lantai` | `PenilaianPjLantai` | Penilaian kinerja 16 indikator |
| `password_reset_tokens` | — | Reset password (Laravel 10 standard) |
| `failed_jobs` | — | Antrian job gagal (sistem queue) |
| `migrations` | — | Tracking migrasi database |

> **Catatan:** Tabel `penilaian_kinerja` (sistem lama), `password_resets` (Laravel <10), dan `personal_access_tokens` (Sanctum) telah **dihapus** karena sudah tidak digunakan.

---

## 7. Modul Sistem

### 7.1 Autentikasi
- Login dengan NIK dan password
- Register dinonaktifkan (admin yang buat akun)
- Semua pengguna dapat ganti password sendiri (`/profil/ganti-password`)
- Logout dengan konfirmasi modal (tidak bisa tidak sengaja logout)

---

### 7.2 Dasbor (Per Peran)

**Dasbor CS** (`/dasbor/cs`) — Mobile UI
- Stat: Total tugas mingguan / selesai / persentase
- 4 menu utama: Tugas Mingguan, Operan Shift, Minta Barang, Bank Sampah
- Notifikasi operan masuk (banner + badge, real-time polling 30 detik)
- Feed aktivitas terakhir

**Dasbor Supervisor** (`/dasbor/supervisor`) — Sidebar
- Hero banner biru dengan tanggal
- 4 KPI card: Area Bersih, Tingkat Kebersihan (progress bar), Ceklis hari ini, Setoran sampah
- Panel ceklis masuk hari ini + panel setoran sampah terbaru
- Panel aksi cepat: Laporan, Tugas Mingguan, Bank Sampah, Operan, Ganti Password

**Dasbor Gudang** (`/dasbor/gudang`) — Sidebar
- Statistik stok, permintaan pending, total barang
- Grafik stok dan permintaan
- Permintaan masuk terkini

**Dasbor Admin** (`/dasbor/admin`) — Sidebar
- 6 KPI card: area, ceklis, stok kritis, permintaan pending, inventori, pengguna
- Grafik tren ceklis 7 hari
- Grafik proporsi kebersihan (pie chart)
- Feed aktivitas sistem + panel aksi cepat ke semua modul

---

### 7.3 Tugas Mingguan CS

**Fungsi:** Pencatatan tugas berkala di luar rutinitas harian (menguras dispenser, membersihkan exhaust fan, polishing lantai, dll).

**Alur CS:**
1. Buat tugas → isi tanggal, waktu, rincian kegiatan
2. Ambil foto **BEFORE** realtime via kamera (dengan watermark: nama sistem, tanggal, waktu WIB, koordinat GPS)
3. Bisa ambil hingga 5 foto sebelum
4. Simpan → status `proses`
5. Ambil foto **AFTER** → status `selesai`

**Alur Supervisor:**
- Verifikasi tugas: setujui (status `disetujui`) atau kembalikan (status `proses`) dengan catatan

**Fitur teknis:**
- Foto dikompresi server-side (max 1280px, JPEG quality 80) via `KompresiFoto::simpan()`
- Watermark di-burn ke canvas dengan GPS koordinat
- CS tidak perlu login ulang — tidak ada tombol logout di halaman tugas mingguan

**Akses:**
| Aksi | Peran |
|---|---|
| Buat tugas | cs, pj_lantai, admin |
| Lihat semua | supervisor, admin |
| Verifikasi | supervisor, admin |

---

### 7.4 Ceklis Kebersihan (Legacy)

**Status:** Tersedia untuk riwayat, bukan modul utama aktif.

**Alur:**
1. CS pilih area dari daftar
2. Ambil foto BEFORE (kamera realtime + watermark GPS)
3. Bersihkan area
4. Ambil foto AFTER (watermark berubah: `[AFTER - SELESAI]`)
5. Ceklis selesai

**Fitur:**
- Watermark diburn ke canvas: `● SIM KEBERSIHAN - PUSKESMAS CEMPAKA PUTIH` + tanggal + waktu WIB + GPS pojok kanan
- Foto dikompresi server-side
- Supervisor/Admin bisa nilai 1–5 bintang + catatan evaluasi
- View detail menampilkan foto before/after dengan overlay watermark CSS

---

### 7.5 Operan Shift

**Dual view berdasarkan peran:**

**Portal CS/PJ Lantai** (Mobile UI):
- Form kirim operan: pilih penerima, tempat tugas, waktu jaga, uraian kegiatan, daftar tugas belum selesai
- Notifikasi operan masuk (badge + banner)
- Konfirmasi "Terima Operan"
- Tandai tugas selesai atau eskalasi ke shift berikutnya

**Portal Admin/Supervisor** (Read-only Monitoring):
- Filter periode: Hari Ini / Kemarin / Minggu Ini / Bulan Ini
- 4 statistik: Total, Menunggu, Selesai, Dieskalasi
- Card detail per operan dengan indikator eskalasi
- Tombol cetak laporan formal

**Eskalasi:** Saat tugas tidak bisa dikerjakan, dibuat `OperanShift` baru dengan `parent_operan_id` menunjuk ke operan asal.

**Cetak Laporan:** Format resmi A4 dengan kop surat, nomor surat, paragraf formal, 2 section (ringkasan + rincian), TTD, tembusan.

---

### 7.6 Permintaan Barang

**Sistem Keranjang (Session-based):**
1. CS buka katalog, cari barang (filter realtime via JS)
2. Tambah ke keranjang dengan jumlah
3. Review keranjang: update/hapus item, cek stok real-time
4. Kirim semua sekaligus ke gudang (DB transaction)
5. Gudang: setujui (stok otomatis berkurang + catat pemakaian) atau tolak dengan alasan
6. CS lihat status di tab Riwayat

**Fitur katalog:**
- Foto barang
- Badge stok: Tersedia (hijau) / Menipis (kuning) / Habis (merah)
- Badge "di keranjang" dengan jumlah
- Form expand in-card (tidak buka halaman baru)
- FAB (Floating Action Button) keranjang dengan badge count

---

### 7.7 Bank Sampah

**CS:** Form setor sampah dengan:
- Multi-pilih jenis sampah (Botol Plastik, Kardus, Derigen, Kertas, Duplex, Kaleng, Koran, Lainnya)
- Pilih lokasi/area setor
- Upload foto bukti (dikompresi)
- Keterangan/nama barang
- Riwayat 7 setoran terakhir

**Supervisor/Admin:** Rekapan dengan:
- Filter: Hari Ini / Minggu Ini / Bulanan (dengan pilih bulan)
- 3 statistik: Total, Tervalidasi, Menunggu
- Rekap per jenis sampah (progress bar)
- Rekap per petugas
- Detail semua setoran dengan tombol Validasi / Tolak (modal)
- Tombol cetak laporan formal

**Laporan cetak:** 4 section — Ringkasan, Rekap per Jenis, Rekap per Petugas, Rincian Detail.

---

### 7.8 Penilaian Kinerja PJ Lantai

**Formulir Publik** (tanpa login, `/penilaian-pj-lantai`):
- Diisi oleh PJ Lantai untuk menilai kinerja petugas CS
- 16 indikator dalam 3 kelompok:
  - **Kehadiran/Absensi:** Disiplin, Komunikasi
  - **Kinerja/Pelayanan:** 12 indikator (sapu-pel, kaca-perabot, tangga, kontrol, sampah, koordinasi, tidak meninggalkan tugas, toilet, sarana, kerjasama, kesopanan, cekatan)
  - **Mutu Pelayanan:** Kepatuhan SOP, Kepuasan pelanggan
- Skor per indikator: 0–100
- Upload bukti dokumentasi (maks 5 file)
- Auto-hitung rata-rata dan kategori

**Anti-spam:**
- Throttle middleware: max 3 submit per 30 menit per IP
- Cache throttle controller: 1 submit per 30 menit per IP
- Honeypot field tersembunyi (bot trap)

**Kategori Hasil:**
| Skor | Kategori |
|---|---|
| ≥ 86 | Sangat Baik |
| 76–85 | Baik |
| 75 | Cukup |
| 51–74 | Kurang |
| ≤ 50 | Sangat Kurang |

**Portal Admin:** Lihat semua penilaian, filter bulan/tahun/nama, cetak lembar penilaian formal per entri.

**Cetak Lembar Penilaian:** Format resmi A4 — identitas petugas, tabel 16 indikator dengan deskripsi dan skor, ringkasan hasil, catatan, TTD dua pihak, tembusan.

---

### 7.9 Laporan Stok Barang

**Gudang/Admin** (`/stok-barang`):
- Tambah produk baru ke periode atau tambah produk existing ke periode
- Input stok masuk per produk
- Input pemakaian per unit per minggu (AJAX grid, upsert)
- Filter: bulan, tahun, unit/lokasi, minggu
- Kode produk di-generate otomatis (format `BRG-0001`)

**Export:**
- **Excel:** `LaporanStokExport` — tabel stok masuk + grid pemakaian unit×minggu, rekap per unit, format landscape A4/A3, styled dengan header merge, zebra striping
- **PDF:** View cetak formal — kop surat, ringkasan (stok masuk, pemakaian, sisa), tabel rincian per produk, TTD, tembusan

---

### 7.10 Laporan Terpadu

**Supervisor/Admin** (`/laporan`):
- Filter: Hari Ini / Minggu Ini / Bulanan (pilih bulan)
- Grafik tren ceklis 7 hari (Chart.js)
- Ringkasan 4 modul: Ceklis, Permintaan Barang, Bank Sampah, Penilaian Kinerja
- Top Performer petugas

**Export:**
- **PDF:** Format resmi — kop surat, ringkasan eksekutif, rincian ceklis per area, ringkasan permintaan barang, TTD, tembusan
- **Excel:** Format rapi — kop instansi, 3 section bernomor (Ringkasan, Ceklis per Area, Permintaan Barang), baris total, TTD

---

### 7.11 Manajemen Master Data (Admin)

| Master | Operasi | URL |
|---|---|---|
| **Pengguna** | CRUD | `/admin/pengguna` |
| **Area/Lantai** | CRUD | `/admin/area` |
| **Barang Inventori** | CRUD | `/admin/barang` |

---

## 8. Fitur Teknis Cross-Cutting

### Kompresi Foto (`App\Helpers\KompresiFoto`)
- Semua upload foto diproses server-side
- Resize: max lebar 1280px (proporsional)
- Format output: JPEG, quality 80%
- Library: PHP GD (built-in, tanpa dependency eksternal)
- Digunakan di: Ceklis (before/after), Tugas Mingguan (hingga 5 foto sebelum + 1 sesudah), Bank Sampah (foto timbangan)

### Watermark Foto (Client-side Canvas)
Foto kamera realtime di-burn watermark sebelum upload:
```
● SIM KEBERSIHAN - PUSKESMAS CEMPAKA PUTIH   ← hijau (#22c55e)
[BEFORE/AFTER] Rabu, 17 September 2026 • 14:23 WIB   ← putih
                              GPS: -6.173619, 106.870314   ← abu
```
- Gradient hitam di bagian bawah foto
- GPS dari `navigator.geolocation.getCurrentPosition()`
- Jika GPS tidak tersedia, baris GPS tidak ditampilkan

### Notifikasi Real-time (Polling)
- Endpoint: `GET /api/notifikasi/operan-masuk` → `{"jumlah": N}`
- Poll pertama: 5 detik setelah halaman dimuat
- Interval: setiap 30 detik
- Update: badge bottom nav, badge card menu, banner notifikasi, judul tab browser
- Akses: cs, pj_lantai saja

### Format Laporan Cetak (Formal)
Semua laporan menggunakan template `layouts/laporan-formal.blade.php`:
- Font: Times New Roman (serif formal)
- Kop surat: Logo + nama instansi + alamat, border hitam 3px
- Struktur: Judul + Nomor Surat → Pembuka → Info Periode → Section I, II, ... → Penutup → TTD → Tembusan
- Optimasi print: `@page { size: A4; margin: 2cm 2.5cm; }`
- Tombol cetak (tidak muncul saat print)

Laporan yang mendukung cetak:
| Laporan | Route | Format |
|---|---|---|
| Operan Shift | `operan.cetak-laporan` | HTML → Print |
| Bank Sampah | `sampah.cetak-laporan` | HTML → Print |
| Penilaian PJ Lantai | `admin.penilaian-pj.cetak` | HTML → Print |
| Laporan Terpadu | `laporan.cetak-pdf` | DomPDF stream |
| Stok Barang | `stok-barang.cetak-pdf` | HTML → Print |

---

## 9. API Internal

| Endpoint | Method | Fungsi | Akses |
|---|---|---|---|
| `/api/notifikasi/operan-masuk` | GET | Jumlah operan menunggu (JSON) | cs, pj_lantai |
| `/stok-barang/kode-otomatis` | GET | Generate kode BRG-XXXX otomatis | gudang, admin |
| `/stok-barang/laporan/{id}/pemakaian` | PATCH | Upsert pemakaian per unit/minggu | gudang, admin |
| `/stok-barang/laporan/{id}/pemakaian-data` | GET | Data grid pemakaian untuk modal | gudang, admin |

---

## 10. Keamanan

| Fitur | Implementasi |
|---|---|
| Autentikasi | Session-based (Laravel Auth) |
| Otorisasi | Middleware `CekPeran` (role check per route) |
| CSRF Protection | Token di semua form POST/PUT/PATCH/DELETE |
| Password | Bcrypt (cast `hashed` di model User) |
| Upload Validasi | Mime type, ukuran, dan ekstensi dicek di controller |
| Anti-spam Form Publik | Throttle middleware (3×/30mnt/IP) + Cache lock + Honeypot field |
| Logout | Konfirmasi modal (mencegah klik tidak sengaja) |

---

## 11. Struktur Route Lengkap

### Publik (tanpa auth)
| Route | URL | Keterangan |
|---|---|---|
| `penilaian-pj.form` | GET `/penilaian-pj-lantai` | Form penilaian publik |
| `penilaian-pj.simpan` | POST `/penilaian-pj-lantai` | Simpan penilaian (throttle 3/30mnt) |
| `penilaian-pj.sukses` | GET `/penilaian-pj-lantai/sukses` | Halaman konfirmasi sukses |
| `login` | GET/POST `/login` | Form login |
| `logout` | POST `/logout` | Logout |

### Dasbor
| Route | URL | Peran |
|---|---|---|
| `dasbor.cs` | `/dasbor/cs` | cs |
| `dasbor.supervisor` | `/dasbor/supervisor` | supervisor, pj_lantai |
| `dasbor.gudang` | `/dasbor/gudang` | gudang |
| `dasbor.admin` | `/dasbor/admin` | admin |

### Tugas Mingguan (`/tugas-mingguan`)
| Route | Aksi | Peran |
|---|---|---|
| `tugas-mingguan.index` | GET / | cs, pj_lantai, supervisor, admin |
| `tugas-mingguan.buat` | GET /buat | cs, pj_lantai, admin |
| `tugas-mingguan.simpan` | POST /simpan | cs, pj_lantai, admin |
| `tugas-mingguan.detail` | GET /{id} | semua |
| `tugas-mingguan.isi-after` | GET /{id}/after | cs, pj_lantai, admin |
| `tugas-mingguan.simpan-after` | PATCH /{id}/after | cs, pj_lantai, admin |
| `tugas-mingguan.verifikasi` | PATCH /{id}/verifikasi | supervisor, admin |

### Ceklis Legacy (`/ceklis`)
| Route | Aksi | Peran |
|---|---|---|
| `ceklis.index` | GET / | cs, pj_lantai |
| `ceklis.buat` | GET /area/{area_id} | cs, pj_lantai |
| `ceklis.simpan` | POST /simpan | cs, pj_lantai |
| `ceklis.isi-after` | GET /{id}/after | cs, pj_lantai |
| `ceklis.simpan-after` | PATCH /{id}/after | cs, pj_lantai |
| `ceklis.detail` | GET /{id}/detail | cs, pj_lantai, supervisor, admin |
| `ceklis.nilai` | PATCH /{id}/nilai | supervisor, pj_lantai, admin |

### Operan Shift (`/operan`)
| Route | Aksi | Peran |
|---|---|---|
| `operan.index` | GET / | cs, pj_lantai, admin → dual view |
| `operan.kirim` | POST /kirim | cs, pj_lantai, admin |
| `operan.terima` | PATCH /{id}/terima | cs, pj_lantai, admin |
| `operan.selesaikan` | PATCH /{id}/selesaikan | cs, pj_lantai, admin |
| `operan.eskalasi` | POST /{id}/eskalasi | cs, pj_lantai, admin |
| `operan.detail` | GET /{id}/detail | cs, pj_lantai, admin |
| `operan.cetak-laporan` | GET /cetak-laporan | admin, supervisor, pj_lantai |

### Permintaan Barang (`/barang`)
| Route | Aksi | Peran |
|---|---|---|
| `barang.katalog` | GET /katalog | cs, pj_lantai |
| `barang.keranjang` | GET /keranjang | cs, pj_lantai |
| `barang.keranjang.tambah` | POST /keranjang/tambah | cs, pj_lantai |
| `barang.keranjang.update` | POST /keranjang/update | cs, pj_lantai |
| `barang.keranjang.hapus` | POST /keranjang/hapus | cs, pj_lantai |
| `barang.keranjang.hapus-semua` | POST /keranjang/hapus-semua | cs, pj_lantai |
| `barang.keranjang.kirim` | POST /keranjang/kirim | cs, pj_lantai |
| `barang.gudang` | GET /gudang | gudang, admin |
| `barang.setujui` | PATCH /{id}/setujui | gudang, admin |
| `barang.tolak` | PATCH /{id}/tolak | gudang, admin |

### Bank Sampah (`/sampah`)
| Route | Aksi | Peran |
|---|---|---|
| `sampah.buat` | GET /setor | cs, pj_lantai |
| `sampah.simpan` | POST /simpan | cs, pj_lantai |
| `sampah.rekapan` | GET /rekapan | supervisor, pj_lantai, admin |
| `sampah.cetak-laporan` | GET /cetak-laporan | supervisor, pj_lantai, admin |
| `sampah.validasi` | PATCH /{id}/validasi | supervisor, pj_lantai, admin |
| `sampah.tolak` | PATCH /{id}/tolak | supervisor, pj_lantai, admin |

### Laporan Umum (`/laporan`)
| Route | Aksi | Peran |
|---|---|---|
| `laporan.index` | GET / | supervisor, pj_lantai, admin |
| `laporan.cetak-pdf` | GET /cetak-pdf | supervisor, pj_lantai, admin |
| `laporan.cetak-excel` | GET /cetak-excel | supervisor, pj_lantai, admin |

### Admin Master Data (`/admin`)
| Route | Aksi | Peran |
|---|---|---|
| `admin.pengguna.*` | CRUD | admin |
| `admin.area.*` | CRUD | admin |
| `admin.barang.*` | CRUD | admin, gudang |
| `admin.penilaian-pj.index` | GET /penilaian-pj | admin |
| `admin.penilaian-pj.show` | GET /penilaian-pj/{id} | admin |
| `admin.penilaian-pj.cetak` | GET /penilaian-pj/{id}/cetak | admin |
| `admin.penilaian-pj.destroy` | DELETE /penilaian-pj/{id} | admin |

### Stok Barang (`/stok-barang`)
| Route | Aksi | Peran |
|---|---|---|
| `stok-barang.index` | GET / | gudang, admin |
| `stok-barang.simpan-barang` | POST /simpan-barang | gudang, admin |
| `stok-barang.tambah-ke-periode` | POST /tambah-ke-periode | gudang, admin |
| `stok-barang.laporan.update` | PUT /laporan/{id} | gudang, admin |
| `stok-barang.laporan.pemakaian` | PATCH /laporan/{id}/pemakaian | gudang, admin |
| `stok-barang.laporan.pemakaian-data` | GET /laporan/{id}/pemakaian-data | gudang, admin |
| `stok-barang.laporan.hapus` | DELETE /laporan/{id} | gudang, admin |
| `stok-barang.export-excel` | GET /export-excel | gudang, admin |
| `stok-barang.cetak-pdf` | GET /cetak-pdf | gudang, admin |
| `stok-barang.kode-otomatis` | GET /kode-otomatis | gudang, admin |

### Profil & API
| Route | Aksi | Peran |
|---|---|---|
| `profil.ganti-password` | GET/PUT /profil/ganti-password | semua |
| `api.notifikasi.operan` | GET /api/notifikasi/operan-masuk | cs, pj_lantai |

---

## 12. Roadmap (Future Development)

Fitur-fitur berikut belum diimplementasikan dan dapat dikembangkan di versi berikutnya:

- **Notifikasi WhatsApp:** Integrasi API pihak ketiga untuk kirim notifikasi operan langsung ke WhatsApp petugas
- **QR Code Area:** Stiker QR Code di pintu ruangan — CS scan untuk langsung membuka form ceklis area tersebut
- **KPI Otomatis:** Perhitungan skor kinerja berdasarkan kecepatan penyelesaian tugas, kehadiran, dan konsistensi
- **Absensi Digital:** Modul presensi terintegrasi (saat ini Out of Scope)
- **Integrasi SIMPUS:** Tarik data jadwal poli untuk prioritas pembersihan otomatis
- **Aplikasi Mobile Native:** Android/iOS (saat ini digantikan dengan web responsif)
- **Push Notification:** Browser push notification sebagai alternatif polling AJAX

---

## 13. Catatan Implementasi Penting

1. **Ceklis vs Tugas Mingguan:** Modul Ceklis Kebersihan adalah sistem lama (legacy) yang masih tersedia untuk melihat riwayat. Modul utama sekarang adalah Tugas Mingguan CS yang lebih fleksibel (multi-foto, verifikasi supervisor, tidak terbatas area).

2. **Operan Shift Dual View:** Route `/operan` menampilkan UI berbeda berdasarkan peran — CS mendapat form interaktif, Admin/Supervisor mendapat monitoring read-only.

3. **Keranjang Belanja Session-based:** Permintaan barang menggunakan session Laravel sebagai "keranjang" sebelum dikirim, memungkinkan CS memilih banyak barang sekaligus.

4. **Berat Sampah Dihapus:** Kolom `berat_kg` di tabel `setoran_sampah` telah dihapus. Metrik bank sampah kini berdasarkan jumlah laporan/setoran, bukan kilogram.

5. **Foto Compression:** Semua foto dikompresi dua kali — di client (canvas `toBlob` quality 0.85) dan di server (`KompresiFoto` quality 80, max 1280px). Ini memastikan tidak ada foto besar yang masuk ke storage.

6. **Tabel Dihapus:** `penilaian_kinerja` (sistem penilaian lama), `password_resets` (duplikat), dan `personal_access_tokens` (Sanctum tidak digunakan) telah dihapus dari database melalui migration.
