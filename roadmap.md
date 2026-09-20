# Roadmap Alur Kerja — SIM Kebersihan Puskesmas Cempaka Putih

**Perbandingan:** Sebelum Sistem (As-Is) vs Sesudah Sistem (To-Be)

---

## 1. Monitoring Kebersihan Harian

### Sebelum Sistem
```
Petugas CS selesai bersihkan ruangan
        ↓
Foto dikirim ke grup WhatsApp
        ↓
Supervisor scroll WA — cari foto manual
        ↓
Tidak tahu: siapa bersihkan apa? jam berapa? dimana?
        ↓
Rekap akhir bulan: tarik data dari WA ke Excel manual
        ↓
❌ Waktu lama, data sering hilang/tidak lengkap
```

### Sesudah Sistem
```
Petugas CS buka SIM Kebersihan di HP
        ↓
Tap menu → Kamera aktif otomatis
        ↓
Foto BEFORE diambil (watermark: nama sistem + tanggal + waktu WIB + GPS ter-burn otomatis)
        ↓
Petugas kerjakan kebersihan
        ↓
Foto AFTER diambil (watermark otomatis)
        ↓
Data tersimpan: siapa, area mana, jam berapa, koordinat GPS
        ↓
Supervisor pantau real-time di dasbor → nilai 1–5 bintang
        ↓
✅ Laporan otomatis, bukti foto terstempel waktu & lokasi
```

---

## 2. Serah Terima Shift (Operan)

### Sebelum Sistem
```
Shift pagi selesai
        ↓
Tulis catatan di buku serah terima
        ↓
Buku sering tertinggal / tidak dibaca
        ↓
Shift berikutnya tidak tahu tugas yang belum selesai
        ↓
Tugas terbengkalai, tidak ada yang bertanggung jawab
        ↓
❌ Tidak ada bukti, tidak ada eskalasi, tidak terlacak
```

### Sesudah Sistem
```
Shift pagi akan selesai
        ↓
Isi form operan: pilih penerima, tulis catatan, daftar tugas belum selesai
        ↓
Sistem kirim notifikasi ke penerima (badge + banner real-time)
        ↓
Shift siang tap "Terima Operan" — tercatat waktu konfirmasi
        ↓
Kerjakan tugas → tap "Selesai" ATAU "Eskalasi" ke shift berikutnya
        ↓
Jika dieskalasi: dibuat catatan baru dengan referensi ke operan asal (terlacak)
        ↓
Admin/Supervisor bisa monitor semua operan + cetak laporan formal
        ↓
✅ Tidak ada tugas yang hilang, semua terlacak dengan chain eskalasi
```

---

## 3. Permintaan Barang Kebersihan

### Sebelum Sistem
```
Petugas CS kehabisan sabun / pel
        ↓
Minta secara lisan ke supervisor atau gudang
        ↓
Gudang catat di buku besar
        ↓
Stok tidak update otomatis — sering tidak sinkron
        ↓
Barang habis tidak terprediksi
        ↓
❌ Tidak ada bukti permintaan, stok tidak akurat
```

### Sesudah Sistem
```
Petugas CS kehabisan barang
        ↓
Buka Katalog Barang → lihat stok real-time
        ↓
Pilih barang → masukkan ke keranjang (bisa pilih banyak sekaligus)
        ↓
Kirim permintaan → tersimpan di sistem dengan timestamp
        ↓
Gudang terima notifikasi → klik Setujui atau Tolak
        ↓
Jika disetujui: stok otomatis berkurang, pemakaian tercatat per unit per minggu
        ↓
Petugas CS lihat status di tab Riwayat (disetujui / ditolak + alasan)
        ↓
✅ Stok real-time, bukti digital, tidak ada permintaan yang hilang
```

---

## 4. Pengelolaan Stok Barang (Gudang)

### Sebelum Sistem
```
Gudang catat stok di buku besar / Excel terpisah
        ↓
Update manual setiap ada barang masuk / keluar
        ↓
Data sering terlambat diupdate atau tidak konsisten
        ↓
Laporan mutasi barang: hitung manual dari buku
        ↓
❌ Rentan salah hitung, tidak bisa lihat pemakaian per lokasi
```

### Sesudah Sistem
```
Gudang input stok masuk per produk per bulan
        ↓
CS minta barang → disetujui gudang → stok otomatis berkurang
        ↓
Input pemakaian per unit per minggu via grid (AJAX)
        ↓
Sistem hitung: stok masuk - total pemakaian = sisa stok otomatis
        ↓
Export laporan stok ke Excel (per unit, per minggu, total)
        ↓
Cetak laporan PDF formal (kop surat, tabel, TTD)
        ↓
✅ Stok akurat, laporan instan, pemakaian terlacak per lokasi
```

---

## 5. Bank Sampah

### Sebelum Sistem
```
Petugas CS kumpulkan sampah daur ulang
        ↓
Lapor ke supervisor secara lisan
        ↓
Supervisor catat di kertas / Excel
        ↓
Tidak ada bukti foto, tidak ada validasi formal
        ↓
Rekap bulanan: hitung manual
        ↓
❌ Data tidak terverifikasi, tidak ada rekap per jenis
```

### Sesudah Sistem
```
Petugas CS kumpulkan sampah
        ↓
Buka menu Bank Sampah → pilih jenis sampah (multi-pilih)
        ↓
Foto bukti diupload (dikompresi otomatis)
        ↓
Pilih lokasi → isi keterangan → submit
        ↓
Supervisor terima data → validasi atau tolak dengan catatan
        ↓
Rekap otomatis: per jenis sampah, per petugas, per periode
        ↓
Cetak laporan formal bank sampah
        ↓
✅ Tervalidasi, terdokumentasi, rekap otomatis
```

---

## 6. Penilaian Kinerja Petugas

### Sebelum Sistem
```
Supervisor atau PJ Lantai nilai petugas
        ↓
Isi form kertas / Google Form
        ↓
Nilai dikumpulkan manual → rekap di Excel
        ↓
Tidak ada standar penilaian yang jelas
        ↓
Proses subjektif, mudah dimanipulasi
        ↓
❌ Tidak transparan, lambat, tidak terdokumentasi formal
```

### Sesudah Sistem
```
PJ Lantai buka URL formulir publik (tanpa login)
        ↓
Pilih nama petugas CS dari daftar
        ↓
Nilai 16 indikator (0–100) dalam 3 kategori:
  - Kehadiran/Absensi (2 indikator)
  - Kinerja/Pelayanan (12 indikator)
  - Mutu Pelayanan (2 indikator)
        ↓
Sistem hitung rata-rata → kategorikan otomatis
  (Sangat Baik / Baik / Cukup / Kurang / Sangat Kurang)
        ↓
Upload bukti dokumentasi (opsional, maks 5 file)
        ↓
Admin lihat semua penilaian → cetak lembar penilaian formal
        ↓
✅ Standar 16 indikator, transparan, otomatis, terdokumentasi
```

---

## 7. Laporan Kinerja

### Sebelum Sistem
```
Akhir bulan tiba
        ↓
Supervisor kumpulkan data dari: WA grup, buku serah terima,
buku permintaan barang, catatan bank sampah, form penilaian
        ↓
Salin ke Excel manual — 2–3 hari kerja
        ↓
Kirim ke kepala puskesmas
        ↓
❌ Lambat, data sering tidak lengkap, format tidak konsisten
```

### Sesudah Sistem
```
Supervisor buka menu Laporan Terpadu
        ↓
Pilih filter: Hari Ini / Minggu Ini / Bulan Ini
        ↓
Sistem tampilkan ringkasan otomatis:
  - Jumlah ceklis & persentase kebersihan per area
  - Status permintaan barang
  - Rekap bank sampah
  - Nilai kinerja petugas & top performer
        ↓
Klik Export PDF  → laporan formal siap dengan kop surat
Klik Export Excel → data tabel siap untuk diolah lanjut
        ↓
✅ Laporan instan, format formal, data akurat dari satu sumber
```

---

## Ringkasan Perbandingan

| Aspek | Sebelum Sistem | Sesudah Sistem |
|---|---|---|
| **Media** | WA, buku, kertas, Google Form | Satu platform web |
| **Bukti Kerja** | Foto tanpa keterangan di WA | Foto + watermark waktu + GPS |
| **Operan Shift** | Buku tulis (sering terlewat) | Digital, notifikasi real-time, rantai eskalasi |
| **Permintaan Barang** | Lisan, buku manual | Keranjang digital, approval otomatis |
| **Stok Barang** | Buku besar / Excel terpisah | Real-time, sinkron otomatis |
| **Bank Sampah** | Laporan lisan / catatan manual | Foto bukti, validasi supervisor |
| **Penilaian Kinerja** | Form kertas, subjektif | 16 indikator standar, otomatis |
| **Pembuatan Laporan** | 2–3 hari manual | Instan, klik Export |
| **Akses Data** | Tersebar di banyak media | Terpusat, bisa diakses kapan saja |
| **Keterlacakan** | Rendah | Penuh — siapa, apa, kapan, dimana |
