<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenilaianPjLantai extends Model
{
    protected $table = 'penilaian_pj_lantai';

    protected $fillable = [
        'petugas_cs_id',
        'nama_petugas_cs',
        'lokasi_tugas',
        'nama_pj',
        'tanggal_penilaian',
        'skor_disiplin',
        'skor_komunikasi',
        'skor_sapu_pel',
        'skor_lap_kaca_perabot',
        'skor_tangga',
        'skor_kontrol_kebersihan',
        'skor_buang_sampah',
        'skor_koordinasi',
        'skor_tidak_tinggalkan_tugas',
        'skor_toilet',
        'skor_pelihara_sarana',
        'skor_kerjasama',
        'skor_kesopanan',
        'skor_cekatan',
        'skor_sop',
        'skor_kepuasan',
        'masukan_evaluasi',
        'bukti_dokumentasi',
        'rata_rata',
        'kategori',
    ];

    protected $casts = [
        'tanggal_penilaian'          => 'date',
        'bukti_dokumentasi'          => 'array',
        'rata_rata'                  => 'float',
        'skor_disiplin'              => 'integer',
        'skor_komunikasi'            => 'integer',
        'skor_sapu_pel'              => 'integer',
        'skor_lap_kaca_perabot'      => 'integer',
        'skor_tangga'                => 'integer',
        'skor_kontrol_kebersihan'    => 'integer',
        'skor_buang_sampah'          => 'integer',
        'skor_koordinasi'            => 'integer',
        'skor_tidak_tinggalkan_tugas' => 'integer',
        'skor_toilet'                => 'integer',
        'skor_pelihara_sarana'       => 'integer',
        'skor_kerjasama'             => 'integer',
        'skor_kesopanan'             => 'integer',
        'skor_cekatan'               => 'integer',
        'skor_sop'                   => 'integer',
        'skor_kepuasan'              => 'integer',
    ];

    public function petugasCs(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_cs_id');
    }

    /**
     * Hitung kategori berdasarkan range skor kuesioner PKM Cempaka Putih:
     * - Sangat Kurang / SK : 50
     * - Kurang / K : < 75
     * - Cukup / C : 75
     * - Baik : 76 - 85
     * - Sangat Baik : 86 - 95
     */
    public static function hitungKategori(float $skor): string
    {
        if ($skor >= 86) {
            return 'Sangat Baik';
        } elseif ($skor >= 76) {
            return 'Baik';
        } elseif ($skor >= 75) {
            return 'Cukup';
        } elseif ($skor > 50) {
            return 'Kurang';
        } else {
            return 'Sangat Kurang';
        }
    }

    /**
     * Badge CSS class untuk kategori.
     */
    public function badgeKategori(): string
    {
        return match ($this->kategori) {
            'Sangat Baik'   => 'bg-success text-white',
            'Baik'          => 'bg-primary text-white',
            'Cukup'         => 'bg-info text-dark',
            'Kurang'        => 'bg-warning text-dark',
            'Sangat Kurang' => 'bg-danger text-white',
            default         => 'bg-secondary text-white',
        };
    }

    /**
     * Daftar 16 indikator lengkap yang dikelompokkan berdasarkan kategori.
     */
    public static function daftarIndikator(): array
    {
        return [
            'Kehadiran / Absensi' => [
                'skor_disiplin' => [
                    'label'     => 'Disiplin',
                    'deskripsi' => 'Dapat dilihat dari kedisiplinan CS dalam bekerja, suka meninggalkan tempat tugas pada jam kerja/tidak',
                ],
                'skor_komunikasi' => [
                    'label'     => 'Komunikasi',
                    'deskripsi' => 'Komunikatif dengan pasien/rekan kerja/karyawan',
                ],
            ],
            'Kinerja / Pelayanan' => [
                'skor_sapu_pel' => [
                    'label'     => 'Menyapu, mengepel dan membersihkan setiap ruangan',
                    'deskripsi' => 'Kebersihan lantai, sudut ruangan, dan kerapihan area kerja',
                ],
                'skor_lap_kaca_perabot' => [
                    'label'     => 'Membersihkan dan mengelap kaca, perabot kerja, pintu/jendela kayu, hiasan dinding, teralis, tirai',
                    'deskripsi' => 'Bebas dari debu, bercak, dan kotoran pada perabot dan kaca',
                ],
                'skor_tangga' => [
                    'label'     => 'Mengelap dan mengepel setiap tangga',
                    'deskripsi' => 'Kebersihan anak tangga, pegangan tangga (railing), dan bordes',
                ],
                'skor_kontrol_kebersihan' => [
                    'label'     => 'Selalu menjaga dan mengontrol kebersihan di lingkungan Puskesmas',
                    'deskripsi' => 'Inisiatif mengontrol kebersihan area secara berkala',
                ],
                'skor_buang_sampah' => [
                    'label'     => 'Membuang sampah di tempat yang telah ditentukan',
                    'deskripsi' => 'Ketepatan pemilahan sampah dan waktu pengosongan tempat sampah',
                ],
                'skor_koordinasi' => [
                    'label'     => 'Saling koordinasi dengan petugas kebersihan lainnya',
                    'deskripsi' => 'Kerjasama dan komunikasi antar sesama tim kebersihan',
                ],
                'skor_tidak_tinggalkan_tugas' => [
                    'label'     => 'Tidak meninggalkan tempat bekerja selama jam kerja tanpa ijin',
                    'deskripsi' => 'Standby di area penugasan selama jam operasional',
                ],
                'skor_toilet' => [
                    'label'     => 'Membersihkan toilet dengan kriteria tetap kering, tidak berbau dan bersih',
                    'deskripsi' => 'Lantai toilet kering, kloset bersih, ketersediaan air/sabun, tidak beraroma tidak sedap',
                ],
                'skor_pelihara_sarana' => [
                    'label'     => 'Memelihara sarana dan fasilitas ruangan',
                    'deskripsi' => 'Menjaga dan merawat perlengkapan sarana yang ada di ruangan',
                ],
                'skor_kerjasama' => [
                    'label'     => 'Dapat bekerjasama dengan rekan sejawat dan atasan',
                    'deskripsi' => 'Sikap kooperatif dan responsif saat diarahkan oleh atasan / PJ',
                ],
                'skor_kesopanan' => [
                    'label'     => 'Menjaga kesopanan saat berada di lingkungan kerja (Hospitality)',
                    'deskripsi' => 'Senyum, sapa, salam, ramah dan santun terhadap pasien dan karyawan',
                ],
                'skor_cekatan' => [
                    'label'     => 'Cekatan dalam menyelesaikan tugas',
                    'deskripsi' => 'Cepat tanggap, efisien, dan tuntas dalam pengerjaan pembersihan',
                ],
            ],
            'Mutu Pelayanan' => [
                'skor_sop' => [
                    'label'     => 'Kepatuhan terhadap SOP',
                    'deskripsi' => 'Menjalankan prosedur kebersihan sesuai standar operasional yang berlaku',
                ],
                'skor_kepuasan' => [
                    'label'     => 'Kepuasan pelanggan internal dan eksternal',
                    'deskripsi' => 'Tingkat kepuasan karyawan puskesmas dan pengunjung/pasien terhadap hasil kerja CS',
                ],
            ],
        ];
    }
}
