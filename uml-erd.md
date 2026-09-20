# UML, ERD & LRS — SIM Kebersihan Puskesmas Cempaka Putih

**Sistem:** Sistem Informasi Monitoring dan Penilaian Kinerja Cleaning Service  
**Versi:** 2.0 | **Tanggal:** September 2026  
**Dibuat berdasarkan:** Implementasi aktual (Laravel 10, MySQL)

---

## Daftar Isi

1. [ERD (Entity Relationship Diagram)](#1-erd)
2. [LRS (Logical Record Structure)](#2-lrs)
3. [Use Case Diagram](#3-use-case-diagram)
4. [Activity Diagram](#4-activity-diagram)
5. [Sequence Diagram](#5-sequence-diagram)
6. [Class Diagram](#6-class-diagram)

---

## 1. ERD

### 1.1 ERD Konseptual (Crow's Foot Notation)

```mermaid
erDiagram
    USERS {
        int id PK
        string name
        string nik UK
        string password
        int peran_id FK
        timestamp created_at
        timestamp updated_at
    }

    PERAN {
        int id PK
        string nama_peran
        timestamp created_at
        timestamp updated_at
    }

    AREA {
        int id PK
        string lantai
        timestamp created_at
        timestamp updated_at
    }

    CEKLIS {
        int id PK
        int user_id FK
        int area_id FK
        date tanggal
        time waktu_mulai
        time waktu_selesai
        string foto_before
        string foto_after
        string lat_long
        enum status
        int skor
        text catatan
        timestamp created_at
        timestamp updated_at
    }

    TUGAS_MINGGUAN {
        int id PK
        int user_id FK
        date tanggal
        time waktu_pelaporan
        text rincian_kegiatan
        json foto_sebelum
        string foto_setelah
        enum status
        text catatan_supervisor
        int diverifikasi_oleh FK
        datetime diverifikasi_pada
        timestamp created_at
        timestamp updated_at
    }

    OPERAN {
        int id PK
        int pengirim_id FK
        int penerima_id FK
        date tanggal
        time waktu
        string tempat_tugas
        string waktu_jaga
        json status_alat
        text catatan
        enum status_terima
        json tugas_items
        enum status_penyelesaian
        text catatan_eskalasi
        int parent_operan_id FK
        datetime dibaca_pada
        timestamp created_at
        timestamp updated_at
    }

    BARANG_INVENTORI {
        int id PK
        int produk_id FK
        string kode_barang UK
        string nama_barang
        text deskripsi
        string foto_barang
        int stok_saat_ini
        string satuan
        int stok_minimum
        timestamp created_at
        timestamp updated_at
    }

    PERMINTAAN_BARANG {
        int id PK
        int user_id FK
        int barang_id FK
        int jumlah
        enum status_request
        text alasan_penolakan
        datetime waktu_request
        datetime waktu_approve
        timestamp created_at
        timestamp updated_at
    }

    SETORAN_SAMPAH {
        int id PK
        int user_id FK
        json jenis_sampah
        string lokasi_setor
        string foto_timbangan
        text catatan
        date tanggal
        enum status_validasi
        text catatan_validasi
        int validator_id FK
        timestamp created_at
        timestamp updated_at
    }

    PRODUK_KEBERSIHAN {
        int id PK
        string kode_barang UK
        string nama_barang
        string satuan
        timestamp created_at
        timestamp updated_at
    }

    LAPORAN_STOK {
        int id PK
        int produk_id FK
        int periode_bulan
        int periode_tahun
        date tanggal
        int stok_masuk
        timestamp created_at
        timestamp updated_at
    }

    PEMAKAIAN_STOK {
        int id PK
        int laporan_stok_id FK
        int produk_id FK
        string unit
        int minggu
        int jumlah
        timestamp created_at
        timestamp updated_at
    }

    PENILAIAN_PJ_LANTAI {
        int id PK
        int petugas_cs_id FK
        string nama_petugas_cs
        string lokasi_tugas
        string nama_pj
        date tanggal_penilaian
        int skor_disiplin
        int skor_komunikasi
        int skor_sapu_pel
        int skor_lap_kaca_perabot
        int skor_tangga
        int skor_kontrol_kebersihan
        int skor_buang_sampah
        int skor_koordinasi
        int skor_tidak_tinggalkan_tugas
        int skor_toilet
        int skor_pelihara_sarana
        int skor_kerjasama
        int skor_kesopanan
        int skor_cekatan
        int skor_sop
        int skor_kepuasan
        text masukan_evaluasi
        json bukti_dokumentasi
        decimal rata_rata
        string kategori
        timestamp created_at
        timestamp updated_at
    }

    %% Relasi
    PERAN        ||--o{ USERS              : "memiliki"
    USERS        ||--o{ CEKLIS             : "membuat"
    AREA         ||--o{ CEKLIS             : "berlokasi di"
    USERS        ||--o{ TUGAS_MINGGUAN     : "membuat"
    USERS        ||--o{ TUGAS_MINGGUAN     : "memverifikasi (diverifikasi_oleh)"
    USERS        ||--o{ OPERAN             : "mengirim (pengirim_id)"
    USERS        ||--o{ OPERAN             : "menerima (penerima_id)"
    OPERAN       ||--o{ OPERAN             : "dieskalasi ke (parent_operan_id)"
    USERS        ||--o{ PERMINTAAN_BARANG  : "mengajukan"
    BARANG_INVENTORI ||--o{ PERMINTAAN_BARANG : "diminta"
    PRODUK_KEBERSIHAN ||--o{ BARANG_INVENTORI : "menjadi"
    PRODUK_KEBERSIHAN ||--o{ LAPORAN_STOK  : "dicatat di"
    LAPORAN_STOK ||--o{ PEMAKAIAN_STOK    : "dirinci di"
    PRODUK_KEBERSIHAN ||--o{ PEMAKAIAN_STOK : "dipakai"
    USERS        ||--o{ SETORAN_SAMPAH     : "menyetor"
    USERS        ||--o{ SETORAN_SAMPAH     : "memvalidasi (validator_id)"
    USERS        ||--o{ PENILAIAN_PJ_LANTAI : "dinilai (petugas_cs_id)"
```

---

### 1.2 Kardinalitas Relasi

| Entitas Asal | Kardinalitas | Entitas Tujuan | Keterangan |
|---|---|---|---|
| `peran` | 1 — N | `users` | Satu peran dimiliki banyak pengguna |
| `users` | 1 — N | `ceklis` | Satu CS membuat banyak ceklis |
| `area` | 1 — N | `ceklis` | Satu area punya banyak ceklis |
| `users` | 1 — N | `tugas_mingguan` | Satu CS punya banyak tugas |
| `users` | 1 — N | `operan` (pengirim) | Satu user kirim banyak operan |
| `users` | 1 — N | `operan` (penerima) | Satu user terima banyak operan |
| `operan` | 0..1 — N | `operan` (self) | Eskalasi: satu operan jadi induk banyak operan baru |
| `users` | 1 — N | `permintaan_barang` | Satu CS ajukan banyak permintaan |
| `barang_inventori` | 1 — N | `permintaan_barang` | Satu barang diminta berkali-kali |
| `produk_kebersihan` | 1 — 1 | `barang_inventori` | Satu produk → satu stok inventori |
| `produk_kebersihan` | 1 — N | `laporan_stok` | Satu produk dicatat di banyak periode |
| `laporan_stok` | 1 — N | `pemakaian_stok` | Satu laporan punya banyak pemakaian per unit |
| `users` | 1 — N | `setoran_sampah` | Satu CS setor banyak kali |
| `users` | 1 — N | `penilaian_pj_lantai` | Satu CS dinilai berkali-kali |

---

## 2. LRS

**LRS (Logical Record Structure)** menggambarkan struktur tabel relasional lengkap dengan tipe data, constraint, dan relasi foreign key.

---

### 2.1 Tabel `peran`

```
PERAN
├── id              : INT(11)        NOT NULL  AUTO_INCREMENT  [PK]
├── nama_peran      : VARCHAR(50)    NOT NULL  UNIQUE
├── created_at      : TIMESTAMP      NULL
└── updated_at      : TIMESTAMP      NULL

Nilai valid: admin | supervisor | pj_lantai | cs | gudang
```

---

### 2.2 Tabel `users`

```
USERS
├── id              : INT(11)        NOT NULL  AUTO_INCREMENT  [PK]
├── name            : VARCHAR(255)   NOT NULL
├── nik             : VARCHAR(50)    NOT NULL  UNIQUE
├── password        : VARCHAR(255)   NOT NULL
├── peran_id        : INT(11)        NOT NULL  [FK → peran.id]
├── remember_token  : VARCHAR(100)   NULL
├── created_at      : TIMESTAMP      NULL
└── updated_at      : TIMESTAMP      NULL

FK: peran_id → peran(id) ON DELETE RESTRICT
```

---

### 2.3 Tabel `area`

```
AREA
├── id              : INT(11)        NOT NULL  AUTO_INCREMENT  [PK]
├── lantai          : VARCHAR(100)   NOT NULL
├── created_at      : TIMESTAMP      NULL
└── updated_at      : TIMESTAMP      NULL

Contoh nilai lantai: "Lantai 1", "Lantai 2", ..., "CSSD", "Rawasari"
```

---

### 2.4 Tabel `ceklis`

```
CEKLIS
├── id              : INT(11)        NOT NULL  AUTO_INCREMENT  [PK]
├── user_id         : INT(11)        NOT NULL  [FK → users.id]
├── area_id         : INT(11)        NOT NULL  [FK → area.id]
├── tanggal         : DATE           NOT NULL
├── waktu_mulai     : TIME           NULL
├── waktu_selesai   : TIME           NULL
├── foto_before     : VARCHAR(255)   NULL      (path storage/public)
├── foto_after      : VARCHAR(255)   NULL      (path storage/public)
├── lat_long        : VARCHAR(100)   NULL      (format: "-6.123,106.456")
├── status          : ENUM           NOT NULL  DEFAULT 'belum'
│                     ['belum','proses','selesai']
├── skor            : INT(1)         NULL      (1–5, dari supervisor)
├── catatan         : TEXT           NULL      (catatan supervisor)
├── created_at      : TIMESTAMP      NULL
└── updated_at      : TIMESTAMP      NULL

FK: user_id → users(id) ON DELETE CASCADE
FK: area_id → area(id) ON DELETE RESTRICT
```

---

### 2.5 Tabel `tugas_mingguan`

```
TUGAS_MINGGUAN
├── id                  : INT(11)       NOT NULL  AUTO_INCREMENT  [PK]
├── user_id             : INT(11)       NOT NULL  [FK → users.id]
├── tanggal             : DATE          NOT NULL
├── waktu_pelaporan     : TIME          NOT NULL
├── rincian_kegiatan    : TEXT          NOT NULL
├── foto_sebelum        : JSON          NULL      (array path, maks 5)
├── foto_setelah        : VARCHAR(255)  NULL      (path storage/public)
├── status              : ENUM          NOT NULL  DEFAULT 'proses'
│                         ['proses','selesai','disetujui']
├── catatan_supervisor  : TEXT          NULL
├── diverifikasi_oleh   : INT(11)       NULL      [FK → users.id]
├── diverifikasi_pada   : DATETIME      NULL
├── created_at          : TIMESTAMP     NULL
└── updated_at          : TIMESTAMP     NULL

FK: user_id          → users(id) ON DELETE CASCADE
FK: diverifikasi_oleh → users(id) ON DELETE SET NULL
```

---

### 2.6 Tabel `operan`

```
OPERAN
├── id                  : INT(11)       NOT NULL  AUTO_INCREMENT  [PK]
├── pengirim_id         : INT(11)       NOT NULL  [FK → users.id]
├── penerima_id         : INT(11)       NOT NULL  [FK → users.id]
├── tanggal             : DATE          NOT NULL
├── waktu               : TIME          NOT NULL
├── tempat_tugas        : VARCHAR(150)  NOT NULL
├── waktu_jaga          : VARCHAR(100)  NOT NULL
├── status_alat         : JSON          NULL      (kondisi peralatan)
├── catatan             : TEXT          NOT NULL
├── status_terima       : ENUM          NOT NULL  DEFAULT 'menunggu'
│                         ['menunggu','diterima']
├── tugas_items         : JSON          NULL      (array string tugas belum selesai)
├── status_penyelesaian : ENUM          NOT NULL  DEFAULT 'belum'
│                         ['belum','selesai','dieskalasi']
├── catatan_eskalasi    : TEXT          NULL
├── parent_operan_id    : INT(11)       NULL      [FK → operan.id]  (self-ref)
├── dibaca_pada         : DATETIME      NULL
├── created_at          : TIMESTAMP     NULL
└── updated_at          : TIMESTAMP     NULL

FK: pengirim_id      → users(id) ON DELETE RESTRICT
FK: penerima_id      → users(id) ON DELETE RESTRICT
FK: parent_operan_id → operan(id) ON DELETE SET NULL
```

---

### 2.7 Tabel `produk_kebersihan`

```
PRODUK_KEBERSIHAN
├── id              : INT(11)       NOT NULL  AUTO_INCREMENT  [PK]
├── kode_barang     : VARCHAR(20)   NOT NULL  UNIQUE   (format: BRG-0001)
├── nama_barang     : VARCHAR(200)  NOT NULL
├── satuan          : VARCHAR(50)   NOT NULL
├── created_at      : TIMESTAMP     NULL
└── updated_at      : TIMESTAMP     NULL

Satuan valid: buah | botol | galon | pak | bungkus | pouch | roll | pasang | kaleng
```

---

### 2.8 Tabel `barang_inventori`

```
BARANG_INVENTORI
├── id              : INT(11)       NOT NULL  AUTO_INCREMENT  [PK]
├── produk_id       : INT(11)       NULL      [FK → produk_kebersihan.id]
├── kode_barang     : VARCHAR(20)   NULL      UNIQUE
├── nama_barang     : VARCHAR(200)  NOT NULL
├── deskripsi       : TEXT          NULL
├── foto_barang     : VARCHAR(255)  NULL
├── stok_saat_ini   : INT(11)       NOT NULL  DEFAULT 0
├── satuan          : VARCHAR(50)   NOT NULL
├── stok_minimum    : INT(11)       NOT NULL  DEFAULT 5
├── created_at      : TIMESTAMP     NULL
└── updated_at      : TIMESTAMP     NULL

FK: produk_id → produk_kebersihan(id) ON DELETE SET NULL
```

---

### 2.9 Tabel `permintaan_barang`

```
PERMINTAAN_BARANG
├── id                  : INT(11)       NOT NULL  AUTO_INCREMENT  [PK]
├── user_id             : INT(11)       NOT NULL  [FK → users.id]
├── barang_id           : INT(11)       NOT NULL  [FK → barang_inventori.id]
├── jumlah              : INT(11)       NOT NULL
├── status_request      : ENUM          NOT NULL  DEFAULT 'pending'
│                         ['pending','disetujui','ditolak']
├── alasan_penolakan    : TEXT          NULL
├── waktu_request       : DATETIME      NOT NULL
├── waktu_approve       : DATETIME      NULL
├── created_at          : TIMESTAMP     NULL
└── updated_at          : TIMESTAMP     NULL

FK: user_id   → users(id) ON DELETE CASCADE
FK: barang_id → barang_inventori(id) ON DELETE RESTRICT
```

---

### 2.10 Tabel `laporan_stok`

```
LAPORAN_STOK
├── id              : INT(11)       NOT NULL  AUTO_INCREMENT  [PK]
├── produk_id       : INT(11)       NOT NULL  [FK → produk_kebersihan.id]
├── periode_bulan   : INT(2)        NOT NULL  (1–12)
├── periode_tahun   : INT(4)        NOT NULL  (mis. 2026)
├── tanggal         : DATE          NOT NULL
├── stok_masuk      : INT(11)       NOT NULL  DEFAULT 0
├── created_at      : TIMESTAMP     NULL
└── updated_at      : TIMESTAMP     NULL

FK: produk_id → produk_kebersihan(id) ON DELETE CASCADE
INDEX: (produk_id, periode_bulan, periode_tahun) UNIQUE
```

---

### 2.11 Tabel `pemakaian_stok`

```
PEMAKAIAN_STOK
├── id                  : INT(11)       NOT NULL  AUTO_INCREMENT  [PK]
├── laporan_stok_id     : INT(11)       NOT NULL  [FK → laporan_stok.id]
├── produk_id           : INT(11)       NOT NULL  [FK → produk_kebersihan.id]
├── unit                : VARCHAR(100)  NOT NULL
├── minggu              : INT(1)        NOT NULL  (1–4)
├── jumlah              : INT(11)       NOT NULL  DEFAULT 0
├── created_at          : TIMESTAMP     NULL
└── updated_at          : TIMESTAMP     NULL

FK: laporan_stok_id → laporan_stok(id) ON DELETE CASCADE
FK: produk_id       → produk_kebersihan(id) ON DELETE CASCADE
INDEX: (laporan_stok_id, unit, minggu) UNIQUE
```

---

### 2.12 Tabel `setoran_sampah`

```
SETORAN_SAMPAH
├── id                  : INT(11)       NOT NULL  AUTO_INCREMENT  [PK]
├── user_id             : INT(11)       NOT NULL  [FK → users.id]
├── jenis_sampah        : JSON          NOT NULL  (array string)
├── lokasi_setor        : VARCHAR(150)  NOT NULL
├── foto_timbangan      : VARCHAR(255)  NULL
├── catatan             : TEXT          NOT NULL
├── tanggal             : DATE          NOT NULL
├── status_validasi     : ENUM          NOT NULL  DEFAULT 'menunggu'
│                         ['menunggu','valid','ditolak']
├── catatan_validasi    : TEXT          NULL
├── validator_id        : INT(11)       NULL      [FK → users.id]
├── created_at          : TIMESTAMP     NULL
└── updated_at          : TIMESTAMP     NULL

FK: user_id      → users(id) ON DELETE CASCADE
FK: validator_id → users(id) ON DELETE SET NULL

Jenis sampah valid: Botol Plastik | Kardus | Derigen | Kertas |
                    Duplex | Kaleng | Koran | Lainnya
```

---

### 2.13 Tabel `penilaian_pj_lantai`

```
PENILAIAN_PJ_LANTAI
├── id                          : INT(11)         NOT NULL  AUTO_INCREMENT  [PK]
├── petugas_cs_id               : INT(11)         NULL      [FK → users.id]
├── nama_petugas_cs             : VARCHAR(150)    NOT NULL
├── lokasi_tugas                : VARCHAR(150)    NOT NULL
├── nama_pj                     : VARCHAR(150)    NOT NULL
├── tanggal_penilaian           : DATE            NOT NULL
│
│   [Grup A: Kehadiran/Absensi]
├── skor_disiplin               : INT(3)          NOT NULL  (0–100)
├── skor_komunikasi             : INT(3)          NOT NULL  (0–100)
│
│   [Grup B: Kinerja/Pelayanan]
├── skor_sapu_pel               : INT(3)          NOT NULL  (0–100)
├── skor_lap_kaca_perabot       : INT(3)          NOT NULL  (0–100)
├── skor_tangga                 : INT(3)          NOT NULL  (0–100)
├── skor_kontrol_kebersihan     : INT(3)          NOT NULL  (0–100)
├── skor_buang_sampah           : INT(3)          NOT NULL  (0–100)
├── skor_koordinasi             : INT(3)          NOT NULL  (0–100)
├── skor_tidak_tinggalkan_tugas : INT(3)          NOT NULL  (0–100)
├── skor_toilet                 : INT(3)          NOT NULL  (0–100)
├── skor_pelihara_sarana        : INT(3)          NOT NULL  (0–100)
├── skor_kerjasama              : INT(3)          NOT NULL  (0–100)
├── skor_kesopanan              : INT(3)          NOT NULL  (0–100)
├── skor_cekatan                : INT(3)          NOT NULL  (0–100)
│
│   [Grup C: Mutu Pelayanan]
├── skor_sop                    : INT(3)          NOT NULL  (0–100)
├── skor_kepuasan               : INT(3)          NOT NULL  (0–100)
│
├── masukan_evaluasi            : TEXT            NOT NULL
├── bukti_dokumentasi           : JSON            NULL      (array metadata file)
├── rata_rata                   : DECIMAL(5,2)    NOT NULL  (avg 16 skor)
├── kategori                    : VARCHAR(50)     NOT NULL
├── created_at                  : TIMESTAMP       NULL
└── updated_at                  : TIMESTAMP       NULL

FK: petugas_cs_id → users(id) ON DELETE SET NULL

Kategori: ≥86=Sangat Baik | 76–85=Baik | 75=Cukup | 51–74=Kurang | ≤50=Sangat Kurang
```

---

### 2.14 Tabel `password_reset_tokens`

```
PASSWORD_RESET_TOKENS
├── email       : VARCHAR(255)  NOT NULL  [PK]
├── token       : VARCHAR(255)  NOT NULL
└── created_at  : TIMESTAMP     NULL
```

---

### 2.15 Tabel `failed_jobs`

```
FAILED_JOBS
├── id          : INT(11)    NOT NULL  AUTO_INCREMENT  [PK]
├── uuid        : VARCHAR    NOT NULL  UNIQUE
├── connection  : TEXT       NOT NULL
├── queue       : TEXT       NOT NULL
├── payload     : LONGTEXT   NOT NULL
├── exception   : LONGTEXT   NOT NULL
└── failed_at   : TIMESTAMP  NOT NULL  DEFAULT CURRENT_TIMESTAMP
```

---

## 3. Use Case Diagram

### 3.1 Use Case Keseluruhan Sistem

```mermaid
graph TD
    subgraph Publik["👤 Publik (Tanpa Login)"]
        UC_P1["Mengisi Formulir Penilaian PJ"]
    end

    subgraph CS["👷 Cleaning Service (CS)"]
        UC_C1["Login"]
        UC_C2["Lihat Dasbor CS"]
        UC_C3["Buat Tugas Mingguan"]
        UC_C4["Upload Foto BEFORE"]
        UC_C5["Upload Foto AFTER"]
        UC_C6["Kirim Operan Shift"]
        UC_C7["Konfirmasi Terima Operan"]
        UC_C8["Eskalasi Tugas"]
        UC_C9["Pilih Barang dari Katalog"]
        UC_C10["Kelola Keranjang Barang"]
        UC_C11["Kirim Permintaan Barang"]
        UC_C12["Setor Sampah"]
        UC_C13["Ganti Password"]
        UC_C14["Logout"]
    end

    subgraph SPV["👔 Supervisor / PJ Lantai"]
        UC_S1["Lihat Dasbor Supervisor"]
        UC_S2["Verifikasi Tugas Mingguan"]
        UC_S3["Nilai Ceklis (1–5 Bintang)"]
        UC_S4["Monitor Operan Shift"]
        UC_S5["Cetak Laporan Operan"]
        UC_S6["Validasi Setoran Sampah"]
        UC_S7["Lihat Rekap Bank Sampah"]
        UC_S8["Cetak Laporan Bank Sampah"]
        UC_S9["Lihat Laporan Terpadu"]
        UC_S10["Export PDF Laporan"]
        UC_S11["Export Excel Laporan"]
    end

    subgraph GDG["🏭 Gudang"]
        UC_G1["Lihat Dasbor Gudang"]
        UC_G2["Kelola Master Barang"]
        UC_G3["Lihat Permintaan Pending"]
        UC_G4["Setujui Permintaan"]
        UC_G5["Tolak Permintaan"]
        UC_G6["Kelola Laporan Stok"]
        UC_G7["Input Pemakaian per Unit"]
        UC_G8["Export Excel Stok"]
        UC_G9["Cetak PDF Stok"]
    end

    subgraph ADM["⚙️ Admin"]
        UC_A1["Lihat Dasbor Admin"]
        UC_A2["CRUD Pengguna"]
        UC_A3["CRUD Area"]
        UC_A4["CRUD Barang Inventori"]
        UC_A5["Lihat Penilaian PJ Lantai"]
        UC_A6["Cetak Lembar Penilaian"]
        UC_A7["Monitor Semua Modul"]
        UC_A8["Hapus Data Penilaian"]
    end

    %% Inheritance
    CS -->|extends| SPV
    SPV -->|extend admin| ADM
```

---

### 3.2 Use Case Detail — Modul Operan Shift

```mermaid
graph LR
    subgraph Actors
        CS["👷 CS / PJ Lantai"]
        SPV["👔 Supervisor / Admin"]
    end

    subgraph System["Sistem Operan Shift"]
        UC1["Kirim Operan\n(isi form + pilih penerima)"]
        UC2["Notifikasi Operan Masuk\n(badge + banner real-time)"]
        UC3["Konfirmasi Terima Operan"]
        UC4["Tandai Tugas Selesai"]
        UC5["Eskalasi ke Shift Berikutnya\n(buat operan baru dengan parent_id)"]
        UC6["Lihat Detail Operan"]
        UC7["Monitor Semua Operan\n(filter periode)"]
        UC8["Cetak Laporan Formal"]
    end

    CS --> UC1
    CS --> UC2
    CS --> UC3
    CS --> UC4
    CS --> UC5
    CS --> UC6
    SPV --> UC6
    SPV --> UC7
    SPV --> UC8
```

---

## 4. Activity Diagram

### 4.1 Alur Tugas Mingguan CS

```mermaid
flowchart TD
    A([Mulai]) --> B[CS buka menu Tugas Mingguan]
    B --> C[Tap 'Catat Tugas Mingguan Baru']
    C --> D[Isi tanggal, waktu, rincian kegiatan]
    D --> E[Kamera aktif — Live Preview]
    E --> F{GPS tersedia?}
    F -->|Ya| G[Tampilkan koordinat GPS di overlay]
    F -->|Tidak| H[Overlay tanpa GPS]
    G --> I[CS tap tombol Shutter]
    H --> I
    I --> J[Watermark di-burn ke canvas\nTanggal • Waktu WIB • GPS]
    J --> K{Foto cukup?\nmaks 5 foto}
    K -->|Belum| L[Tap 'Ambil Foto Lagi']
    L --> E
    K -->|Ya| M[Tap 'Simpan & Lanjut Bersihkan']
    M --> N{Ada foto setelah?}
    N -->|Tidak| O[Simpan status = PROSES]
    O --> P[Redirect ke form foto After]
    P --> Q[CS kerjakan tugas]
    Q --> R[Buka kamera untuk foto AFTER]
    R --> S[Watermark AFTER di-burn ke canvas]
    S --> T[Submit → status = SELESAI]
    N -->|Ya langsung| T
    T --> U[Supervisor menerima notifikasi]
    U --> V{Supervisor verifikasi?}
    V -->|Setujui| W[Status = DISETUJUI]
    V -->|Kembalikan| X[Status = PROSES + catatan]
    X --> D
    W --> Y([Selesai])
```

---

### 4.2 Alur Operan Shift

```mermaid
flowchart TD
    A([Mulai Shift]) --> B[CS login — dasbor muncul]
    B --> C{Ada operan\nmasuk menunggu?}
    C -->|Ya| D[Notifikasi badge + banner tampil]
    D --> E[CS buka halaman Operan]
    E --> F[Tap Konfirmasi Terima]
    F --> G{Ada tugas\nbelum selesai?}
    G -->|Ya| H[CS kerjakan tugas]
    H --> I{Bisa diselesaikan?}
    I -->|Ya| J[Tap Tandai Selesai]
    I -->|Tidak| K[Tap Eskalasi]
    K --> L[Pilih penerima eskalasi + tulis alasan]
    L --> M[Buat OperanShift baru\nparent_operan_id = operan asal]
    M --> N[Kirim notifikasi ke shift berikutnya]
    J --> O([Tugas Selesai])
    C -->|Tidak| P[CS siap shift baru]
    P --> Q[Akhir shift: Kirim Operan]
    Q --> R[Isi form: penerima, tempat, waktu jaga,\nuraian, daftar tugas belum selesai]
    R --> S[Submit → OperanShift tersimpan]
    S --> T[Notifikasi dikirim ke penerima]
    T --> U([Menunggu konfirmasi])
```

---

### 4.3 Alur Permintaan Barang

```mermaid
flowchart TD
    A([CS butuh barang]) --> B[Buka Katalog Barang]
    B --> C[Cari / scroll katalog\nFilter realtime JS]
    C --> D{Stok tersedia?}
    D -->|Habis| E[Tampilkan label HABIS\nTidak bisa dipilih]
    D -->|Tersedia| F[Tap Tambah → form expand]
    F --> G[Atur jumlah + + -]
    G --> H[Tap Masukkan ke Keranjang]
    H --> I[Item tersimpan di Session]
    I --> J{Masih ada\nbarang lain?}
    J -->|Ya| B
    J -->|Tidak| K[Buka Keranjang]
    K --> L[Review: update jumlah / hapus item]
    L --> M[Tap Kirim Permintaan ke Gudang]
    M --> N[Modal konfirmasi muncul]
    N --> O{Yakin?}
    O -->|Batal| L
    O -->|Kirim| P[DB Transaction:\nBuat semua PermintaanBarang\nKosongkan session keranjang]
    P --> Q[Gudang terima notifikasi]
    Q --> R{Gudang review}
    R -->|Setujui| S[DB Transaction:\nStok berkurang\nPemakaian dicatat\nStatus = disetujui]
    R -->|Tolak| T[Status = ditolak + alasan]
    S --> U[CS lihat status di tab Riwayat ✓]
    T --> V[CS lihat alasan penolakan ✗]
```

---

### 4.4 Alur Bank Sampah

```mermaid
flowchart TD
    A([CS punya sampah daur ulang]) --> B[Buka menu Bank Sampah]
    B --> C[Pilih satu/lebih jenis sampah\nBottol Plastik, Kardus, dll]
    C --> D[Pilih lokasi setor]
    D --> E[Upload foto bukti / timbangan]
    E --> F[Server kompres foto\nmax 1280px JPEG 80%]
    F --> G[Isi keterangan nama barang]
    G --> H[Submit → SetoraSampah tersimpan\nstatus_validasi = menunggu]
    H --> I[Supervisor buka Rekapan Bank Sampah]
    I --> J[Filter: Hari Ini / Minggu / Bulan]
    J --> K[Lihat detail setoran]
    K --> L{Validasi?}
    L -->|Valid| M[Update status = valid\nCatat validator_id]
    L -->|Tolak| N[Modal tolak — isi alasan]
    N --> O[Update status = ditolak + catatan_validasi]
    M --> P[Rekap jenis sampah ter-update]
    O --> P
    P --> Q{Perlu laporan?}
    Q -->|Ya| R[Tap Cetak Laporan]
    R --> S[View cetak formal:\nRingkasan, Rekap Jenis,\nRekap Petugas, Detail]
    S --> T[Browser Print Dialog]
    T --> U([Laporan tercetak])
    Q -->|Tidak| V([Selesai])
```

---

### 4.5 Alur Penilaian Kinerja PJ Lantai (Publik)

```mermaid
flowchart TD
    A([PJ Lantai buka URL publik]) --> B[Cek throttle per IP]
    B --> C{Sudah submit\n< 30 menit lalu?}
    C -->|Ya| D[Tampilkan pesan: tunggu 30 menit]
    D --> Z([Selesai — ditolak])
    C -->|Tidak| E[Form tampil]
    E --> F[Isi identitas: nama CS, lokasi, nama PJ, tanggal]
    F --> G[Isi 16 indikator skor 0–100\n3 grup: Kehadiran, Kinerja, Mutu]
    G --> H[Isi masukan evaluasi]
    H --> I[Upload bukti dokumentasi\nmaks 5 file]
    I --> J[Submit form]
    J --> K{Honeypot field\n'website' terisi?}
    K -->|Ya — bot| L[Redirect sukses tanpa simpan]
    K -->|Tidak — manusia| M[Validasi server]
    M --> N[Hitung rata-rata 16 skor]
    N --> O{rata-rata ≥ 86?}
    O -->|≥ 86| P["Kategori: Sangat Baik"]
    O -->|76–85| Q["Kategori: Baik"]
    O -->|75| R["Kategori: Cukup"]
    O -->|51–74| S["Kategori: Kurang"]
    O -->|≤ 50| T["Kategori: Sangat Kurang"]
    P & Q & R & S & T --> U[Simpan ke penilaian_pj_lantai]
    U --> V[Set Cache throttle 30 menit per IP]
    V --> W[Redirect ke halaman Sukses]
    W --> X([Penilaian berhasil dikirim])
```

---

## 5. Sequence Diagram

### 5.1 Alur Login dan Auto-Redirect

```mermaid
sequenceDiagram
    actor User
    participant Browser
    participant LoginController
    participant Middleware as CekPeran Middleware
    participant DB as Database

    User->>Browser: Buka / atau /login
    Browser->>LoginController: GET /login
    LoginController-->>Browser: Tampilkan form login

    User->>Browser: Input NIK + Password
    Browser->>LoginController: POST /login {nik, password}
    LoginController->>DB: SELECT * FROM users WHERE nik = ?
    DB-->>LoginController: User record
    LoginController->>LoginController: Hash::check(password)

    alt Password cocok
        LoginController->>DB: SELECT * FROM peran WHERE id = peran_id
        DB-->>LoginController: Peran record
        LoginController->>LoginController: ruteDasbor() → URL by peran
        LoginController-->>Browser: Redirect 302 ke URL dasbor
        Browser->>Middleware: GET /dasbor/{peran}
        Middleware->>Middleware: Cek session auth + cek peran
        Middleware-->>Browser: 200 OK — Tampilkan dasbor
    else Password salah
        LoginController-->>Browser: Redirect back + error "Kredensial tidak valid"
    end
```

---

### 5.2 Alur Upload Foto dengan Watermark dan Kompresi

```mermaid
sequenceDiagram
    actor CS
    participant Browser
    participant Canvas as HTML5 Canvas
    participant GPS as Geolocation API
    participant Controller as CeklisController
    participant Helper as KompresiFoto
    participant Storage as Storage Disk (public)

    CS->>Browser: Buka form ceklis
    Browser->>GPS: navigator.geolocation.getCurrentPosition()
    GPS-->>Browser: {latitude, longitude} atau error

    Browser->>Browser: Tampilkan koordinat di overlay kamera
    CS->>Browser: Tap tombol Shutter
    Browser->>Canvas: drawImage(video, 0, 0)
    Browser->>Canvas: Burn watermark (teks + GPS)
    Canvas->>Browser: canvas.toBlob(JPEG, 0.85)
    Browser->>Browser: new File([blob], 'before_timestamp.jpg')

    CS->>Browser: Tap Submit
    Browser->>Controller: POST /ceklis/simpan {foto_before, area_id, lat_long}

    Controller->>Helper: KompresiFoto::simpan(file, 'ceklis', 1280, 80)
    Helper->>Helper: imagecreatefromjpeg(tmpPath)
    Helper->>Helper: imagecopyresampled() — resize 1280px
    Helper->>Helper: imagejpeg($dst, path, 80)
    Helper-->>Controller: 'ceklis/uuid.jpg'

    Controller->>Storage: Simpan file hasil kompresi
    Controller->>DB: INSERT INTO ceklis (user_id, area_id, foto_before, lat_long, ...)
    DB-->>Controller: ID ceklis baru
    Controller-->>Browser: Redirect 302 ke /ceklis/{id}/after
    Browser-->>CS: Halaman foto AFTER
```

---

### 5.3 Alur Persetujuan Permintaan Barang (DB Transaction)

```mermaid
sequenceDiagram
    actor Gudang
    participant Browser
    participant PermintaanController
    participant DB as Database

    Gudang->>Browser: Buka halaman Kelola Permintaan
    Browser->>PermintaanController: GET /barang/gudang
    PermintaanController->>DB: SELECT permintaan WHERE status = pending
    DB-->>PermintaanController: Daftar permintaan
    PermintaanController-->>Browser: Tampilkan tabel permintaan

    Gudang->>Browser: Klik Setujui pada permintaan ID-X
    Browser->>PermintaanController: PATCH /barang/{id}/setujui

    PermintaanController->>DB: BEGIN TRANSACTION

    PermintaanController->>DB: SELECT * FROM permintaan_barang WHERE id = X (lock)
    DB-->>PermintaanController: Permintaan {barang_id, jumlah}

    PermintaanController->>DB: SELECT stok_saat_ini FROM barang_inventori WHERE id = barang_id

    alt Stok cukup
        PermintaanController->>DB: UPDATE barang_inventori\nSET stok_saat_ini = stok_saat_ini - jumlah
        PermintaanController->>DB: UPDATE permintaan_barang\nSET status_request = 'disetujui',\nwaktu_approve = NOW()
        PermintaanController->>DB: COMMIT
        PermintaanController-->>Browser: Redirect back + flash 'sukses'
    else Stok tidak cukup
        PermintaanController->>DB: ROLLBACK
        PermintaanController-->>Browser: Redirect back + flash 'gagal: stok tidak mencukupi'
    end
```

---

### 5.4 Alur Polling Notifikasi Operan (Real-time)

```mermaid
sequenceDiagram
    participant Browser as Browser (CS)
    participant JS as JavaScript Polling
    participant Route as GET /api/notifikasi/operan-masuk
    participant DB as Database

    Browser->>JS: DOMContentLoaded
    JS->>JS: setTimeout 5000ms

    loop Setiap 30 detik
        JS->>Route: fetch('/api/notifikasi/operan-masuk')
        Route->>DB: SELECT COUNT(*) FROM operan\nWHERE penerima_id = auth_user\nAND status_terima = 'menunggu'
        DB-->>Route: {jumlah: N}
        Route-->>JS: JSON {jumlah: N}

        alt N > 0
            JS->>Browser: badge-operan-nav.textContent = N (show)
            JS->>Browser: badge-operan-card.textContent = N (show)
            JS->>Browser: banner-operan.style.display = 'block'
            JS->>Browser: document.title = '(N) SIM Kebersihan - Dasbor'
        else N = 0
            JS->>Browser: Sembunyikan semua badge & banner
            JS->>Browser: document.title = 'SIM Kebersihan - Dasbor'
        end
    end
```

---

### 5.5 Alur Operan Shift + Eskalasi

```mermaid
sequenceDiagram
    actor CS1 as CS (Shift Pagi)
    actor CS2 as CS (Shift Siang)
    actor CS3 as CS (Shift Sore)
    participant Sys as Sistem

    CS1->>Sys: POST /operan/kirim\n{penerima: CS2, tugas: ["Kuras dispenser lt3"]}
    Sys->>Sys: Buat OperanShift (status_terima=menunggu)
    Sys-->>CS2: Notifikasi badge muncul (polling 30 detik)

    CS2->>Sys: PATCH /operan/{id}/terima
    Sys->>Sys: Update status_terima = 'diterima', dibaca_pada = now()
    Sys-->>CS2: Flash sukses "Operan diterima"

    CS2->>CS2: Mencoba kerjakan tugas...

    alt CS2 tidak bisa selesaikan
        CS2->>Sys: POST /operan/{id}/eskalasi\n{penerima_eskalasi: CS3, alasan: "Dispenser rusak"}
        Sys->>Sys: Update operan asal: status_penyelesaian = 'dieskalasi'
        Sys->>Sys: Buat OperanShift BARU:\nparent_operan_id = operan_asal.id\npengirim = CS2, penerima = CS3
        Sys-->>CS3: Notifikasi badge muncul
        CS3->>Sys: PATCH /operan/{id}/terima
        CS3->>Sys: PATCH /operan/{id}/selesaikan
        Sys->>Sys: Update status_penyelesaian = 'selesai'
        Sys-->>CS3: Flash sukses
    else CS2 berhasil selesaikan
        CS2->>Sys: PATCH /operan/{id}/selesaikan
        Sys->>Sys: Update status_penyelesaian = 'selesai'
        Sys-->>CS2: Flash sukses "Tugas selesai"
    end
```

---

## 6. Class Diagram

### 6.1 Class Diagram — Model Layer

```mermaid
classDiagram
    class User {
        +int id
        +string name
        +string nik
        +string password
        +int peran_id
        +peran() BelongsTo
        +ceklis() HasMany
        +permintaanBarang() HasMany
        +setoranSampah() HasMany
        +penilaianPj() HasMany
        +tugasMingguan() HasMany
        +ruteDasbor() string
    }

    class Peran {
        +int id
        +string nama_peran
        +pengguna() HasMany
    }

    class Area {
        +int id
        +string lantai
        +ceklis() HasMany
    }

    class CeklisKebersihan {
        +int id
        +int user_id
        +int area_id
        +date tanggal
        +time waktu_mulai
        +time waktu_selesai
        +string foto_before
        +string foto_after
        +string lat_long
        +string status
        +int skor
        +text catatan
        +pengguna() BelongsTo
        +area() BelongsTo
    }

    class TugasMingguan {
        +int id
        +int user_id
        +date tanggal
        +time waktu_pelaporan
        +text rincian_kegiatan
        +array foto_sebelum
        +string foto_setelah
        +string status
        +text catatan_supervisor
        +int diverifikasi_oleh
        +datetime diverifikasi_pada
        +user() BelongsTo
        +verifikator() BelongsTo
        +badgeStatus() string
    }

    class OperanShift {
        +int id
        +int pengirim_id
        +int penerima_id
        +date tanggal
        +time waktu
        +string tempat_tugas
        +string waktu_jaga
        +array status_alat
        +text catatan
        +string status_terima
        +array tugas_items
        +string status_penyelesaian
        +text catatan_eskalasi
        +int parent_operan_id
        +datetime dibaca_pada
        +pengirim() BelongsTo
        +penerima() BelongsTo
        +parentOperan() BelongsTo
        +eskalasiBerikutnya() HasOne
        +adaTugasBelumSelesai() bool
        +badgeTerima() string
        +badgePenyelesaian() string
    }

    class PermintaanBarang {
        +int id
        +int user_id
        +int barang_id
        +int jumlah
        +string status_request
        +text alasan_penolakan
        +datetime waktu_request
        +datetime waktu_approve
        +pengguna() BelongsTo
        +barang() BelongsTo
    }

    class BarangInventori {
        +int id
        +int produk_id
        +string kode_barang
        +string nama_barang
        +text deskripsi
        +string foto_barang
        +int stok_saat_ini
        +string satuan
        +int stok_minimum
        +permintaan() HasMany
        +produkKebersihan() BelongsTo
        +stokMenipis() bool
    }

    class ProdukKebersihan {
        +int id
        +string kode_barang
        +string nama_barang
        +string satuan
        +$ daftarSatuan[] static
        +$ daftarUnit[] static
        +laporanStok() HasMany
        +pemakaianStok() HasMany
        +buatKodeOtomatis()$ string static
    }

    class LaporanStok {
        +int id
        +int produk_id
        +int periode_bulan
        +int periode_tahun
        +date tanggal
        +int stok_masuk
        +produk() BelongsTo
        +pemakaian() HasMany
        +totalPemakaian() int
        +sisaStok() int
        +pemakaianPerUnit() Collection
        +pemakaianPerUnitMinggu() Collection
    }

    class PemakaianStok {
        +int id
        +int laporan_stok_id
        +int produk_id
        +string unit
        +int minggu
        +int jumlah
        +laporanStok() BelongsTo
        +produk() BelongsTo
    }

    class SetoranSampah {
        +int id
        +int user_id
        +array jenis_sampah
        +string lokasi_setor
        +string foto_timbangan
        +text catatan
        +date tanggal
        +string status_validasi
        +text catatan_validasi
        +int validator_id
        +pengguna() BelongsTo
        +validator() BelongsTo
        +jenisSampahTeks() string
    }

    class PenilaianPjLantai {
        +int id
        +int petugas_cs_id
        +string nama_petugas_cs
        +string lokasi_tugas
        +string nama_pj
        +date tanggal_penilaian
        +int skor_disiplin
        +int skor_komunikasi
        +int skor_sapu_pel
        +int skor_lap_kaca_perabot
        +int skor_tangga
        +int skor_kontrol_kebersihan
        +int skor_buang_sampah
        +int skor_koordinasi
        +int skor_tidak_tinggalkan_tugas
        +int skor_toilet
        +int skor_pelihara_sarana
        +int skor_kerjasama
        +int skor_kesopanan
        +int skor_cekatan
        +int skor_sop
        +int skor_kepuasan
        +text masukan_evaluasi
        +array bukti_dokumentasi
        +float rata_rata
        +string kategori
        +petugasCs() BelongsTo
        +hitungKategori()$ string static
        +badgeKategori() string
        +daftarIndikator()$ array static
    }

    class KompresiFoto {
        +simpan()$ string static
        +simpanBanyak()$ array static
    }

    %% Relasi
    User "N" --> "1" Peran : memiliki
    User "1" --> "N" CeklisKebersihan : membuat
    Area "1" --> "N" CeklisKebersihan : berlokasi di
    User "1" --> "N" TugasMingguan : membuat
    User "1" --> "N" OperanShift : mengirim
    User "1" --> "N" OperanShift : menerima
    OperanShift "0..1" --> "N" OperanShift : dieskalasi ke
    User "1" --> "N" PermintaanBarang : mengajukan
    BarangInventori "1" --> "N" PermintaanBarang : diminta
    ProdukKebersihan "1" --> "1" BarangInventori : terhubung ke
    ProdukKebersihan "1" --> "N" LaporanStok : dicatat di
    LaporanStok "1" --> "N" PemakaianStok : dirinci di
    ProdukKebersihan "1" --> "N" PemakaianStok : dipakai
    User "1" --> "N" SetoranSampah : menyetor
    User "1" --> "N" PenilaianPjLantai : dinilai
```

---

### 6.2 Class Diagram — Controller Layer

```mermaid
classDiagram
    class Controller {
        <<abstract>>
    }

    class DasborAdminController {
        +index() View
    }
    class DasborCsController {
        +index() View
    }
    class DasborSupervisorController {
        +index() View
    }
    class DasborGudangController {
        +index() View
    }

    class TugasMingguanController {
        +index(Request) View
        +buat() View
        +simpan(Request) Redirect
        +detail(id) View
        +isiAfter(id) View
        +simpanAfter(Request, id) Redirect
        +verifikasi(Request, id) Redirect
    }

    class CeklisKebersihanController {
        +index() View
        +buat(area_id) View
        +simpan(Request) Redirect
        +isiAfter(id) View
        +simpanAfter(Request, id) Redirect
        +detail(id) View
        +nilaiCeklis(Request, id) Redirect
    }

    class OperanShiftController {
        +index() View
        +kirim(Request) Redirect
        +terima(id) Redirect
        +selesaikan(id) Redirect
        +eskalasi(Request, id) Redirect
        +detail(id) View
        +cetakLaporan() View
        -monitoringOperan() View
    }

    class PermintaanBarangController {
        +katalog() View
        +addToCart(Request) Redirect
        +viewCart() View
        +updateCartItem(Request) Redirect
        +removeCartItem(Request) Redirect
        +clearCart() Redirect
        +submitCart() Redirect
        +daftarGudang() View
        +setujui(id) Redirect
        +tolak(Request, id) Redirect
        +showProof(id) Response
    }

    class SetoranSampahController {
        +buat() View
        +simpan(Request) Redirect
        +rekapan(Request) View
        +validasi(id) Redirect
        +tolak(Request, id) Redirect
        +cetakLaporan(Request) View
    }

    class LaporanController {
        +index(Request) View
        +cetakPdf(Request) Response
        +cetakExcel(Request) BinaryFileResponse
        -rentangWaktu(filter, bulan) array
    }

    class StokBarangController {
        +index(Request) View
        +simpanBarang(Request) Redirect
        +tambahKePeriode(Request) Redirect
        +updateLaporan(Request, id) Redirect
        +updatePemakaian(Request, id) JsonResponse
        +dataPemakaian(id) JsonResponse
        +hapusLaporan(id) Redirect
        +exportExcel(Request) BinaryFileResponse
        +cetakPdf(Request) View
        +kodeOtomatis() JsonResponse
        -namaBulan() array
    }

    class PenilaianPjPublicController {
        +index() View
        +store(Request) Redirect
        +sukses() View
    }

    class GantiPasswordController {
        +edit() View
        +update(Request) Redirect
    }

    Controller <|-- DasborAdminController
    Controller <|-- DasborCsController
    Controller <|-- DasborSupervisorController
    Controller <|-- DasborGudangController
    Controller <|-- TugasMingguanController
    Controller <|-- CeklisKebersihanController
    Controller <|-- OperanShiftController
    Controller <|-- PermintaanBarangController
    Controller <|-- SetoranSampahController
    Controller <|-- LaporanController
    Controller <|-- StokBarangController
    Controller <|-- PenilaianPjPublicController
    Controller <|-- GantiPasswordController
```

---

## Ringkasan Statistik Sistem

| Komponen | Jumlah |
|---|---|
| Tabel Database Aktif | 16 tabel |
| Model Eloquent | 13 model |
| Controller | 15 controller |
| Total Route | 96 route |
| Peran Pengguna | 5 peran + publik |
| Export Class | 2 class (PDF + Excel per modul) |
| Helper Class | 1 (`KompresiFoto`) |
| View Template | 40+ blade files |
| Layout | 2 (Sidebar + Mobile UI) |

---

*Dokumen ini dibuat otomatis berdasarkan implementasi aktual sistem per September 2026.*  
*Seluruh diagram menggunakan sintaks **Mermaid** — dapat dirender di GitHub, GitLab, Notion, Obsidian, dan VS Code (plugin Mermaid Preview).*
