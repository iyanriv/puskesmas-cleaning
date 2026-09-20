<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\PenilaianPjLantai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PenilaianPjPublicController extends Controller
{
    /**
     * Tampilkan formulir penilaian publik (tanpa login).
     */
    public function index()
    {
        $daftarCs = User::whereHas('peran', function ($q) {
            $q->where('nama_peran', 'cs');
        })->orderBy('name')->get();

        $daftarArea = Area::orderBy('lantai')->get();
        $indikator  = PenilaianPjLantai::daftarIndikator();

        return view('penilaian-pj.form', compact('daftarCs', 'daftarArea', 'indikator'));
    }

    /**
     * Simpan hasil pengisian formulir penilaian.
     */
    public function store(Request $request)
    {
        // Anti-spam: throttle 1 submit per 30 menit per IP
        $ip  = $request->ip();
        $key = 'penilaian_submit_' . md5($ip);
        if (\Illuminate\Support\Facades\Cache::has($key)) {
            return redirect()->route('penilaian-pj.form')
                ->with('gagal', 'Anda sudah mengirim penilaian baru-baru ini. Silakan tunggu 30 menit sebelum mengirim lagi.');
        }

        // Honeypot: field website harus kosong (bot biasanya mengisi semua field)
        if ($request->filled('website')) {
            return redirect()->route('penilaian-pj.sukses');
        }
        $request->validate([
            'nama_petugas_cs'            => 'required|string|max:150',
            'petugas_cs_id'              => 'nullable|exists:users,id',
            'lokasi_tugas'               => 'required|string|max:150',
            'nama_pj'                    => 'required|string|max:150',
            'tanggal_penilaian'          => 'required|date',

            // 16 Indikator Skor (0 - 100)
            'skor_disiplin'              => 'required|integer|min:0|max:100',
            'skor_komunikasi'            => 'required|integer|min:0|max:100',
            'skor_sapu_pel'              => 'required|integer|min:0|max:100',
            'skor_lap_kaca_perabot'      => 'required|integer|min:0|max:100',
            'skor_tangga'                => 'required|integer|min:0|max:100',
            'skor_kontrol_kebersihan'    => 'required|integer|min:0|max:100',
            'skor_buang_sampah'          => 'required|integer|min:0|max:100',
            'skor_koordinasi'            => 'required|integer|min:0|max:100',
            'skor_tidak_tinggalkan_tugas' => 'required|integer|min:0|max:100',
            'skor_toilet'                => 'required|integer|min:0|max:100',
            'skor_pelihara_sarana'       => 'required|integer|min:0|max:100',
            'skor_kerjasama'             => 'required|integer|min:0|max:100',
            'skor_kesopanan'             => 'required|integer|min:0|max:100',
            'skor_cekatan'               => 'required|integer|min:0|max:100',
            'skor_sop'                   => 'required|integer|min:0|max:100',
            'skor_kepuasan'              => 'required|integer|min:0|max:100',

            'masukan_evaluasi'           => 'required|string',
            'bukti_dokumentasi'          => 'nullable|array|max:5',
            'bukti_dokumentasi.*'        => 'file|mimes:jpg,jpeg,png,pdf,webp|max:10240',
        ], [
            'nama_petugas_cs.required'   => 'Nama Petugas CS wajib diisi.',
            'lokasi_tugas.required'      => 'Lokasi CS bertugas wajib diisi.',
            'nama_pj.required'           => 'Nama PJ Kebersihan wajib diisi.',
            'tanggal_penilaian.required' => 'Waktu Penilaian wajib diisi.',
            'masukan_evaluasi.required'  => 'Masukan / evaluasi dari PJ Kebersihan wajib diisi.',
            'bukti_dokumentasi.max'      => 'Maksimal 5 file bukti yang dapat diunggah.',
            'bukti_dokumentasi.*.max'    => 'Ukuran masing-masing file maksimal 10 MB.',
        ]);

        // Hitung rata-rata 16 indikator
        $daftarFieldSkor = [
            'skor_disiplin', 'skor_komunikasi',
            'skor_sapu_pel', 'skor_lap_kaca_perabot', 'skor_tangga',
            'skor_kontrol_kebersihan', 'skor_buang_sampah', 'skor_koordinasi',
            'skor_tidak_tinggalkan_tugas', 'skor_toilet', 'skor_pelihara_sarana',
            'skor_kerjasama', 'skor_kesopanan', 'skor_cekatan',
            'skor_sop', 'skor_kepuasan'
        ];

        $totalSkor = 0;
        foreach ($daftarFieldSkor as $field) {
            $totalSkor += (int) $request->input($field, 0);
        }
        $rataRata = round($totalSkor / count($daftarFieldSkor), 2);
        $kategori = PenilaianPjLantai::hitungKategori($rataRata);

        // Upload bukti file jika ada
        $filePaths = [];
        if ($request->hasFile('bukti_dokumentasi')) {
            foreach ($request->file('bukti_dokumentasi') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('penilaian_pj_bukti', 'public');
                    $filePaths[] = [
                        'nama_asli' => $file->getClientOriginalName(),
                        'path'      => $path,
                        'tipe'      => $file->getClientMimeType(),
                        'ukuran_kb' => round($file->getSize() / 1024),
                    ];
                }
            }
        }

        // Tentukan ID CS jika cocok
        $petugasCsId = $request->petugas_cs_id;
        if (!$petugasCsId) {
            $matchedUser = User::whereHas('peran', fn($q) => $q->where('nama_peran', 'cs'))
                ->where('name', 'like', '%' . trim($request->nama_petugas_cs) . '%')
                ->first();
            if ($matchedUser) {
                $petugasCsId = $matchedUser->id;
            }
        }

        $penilaian = PenilaianPjLantai::create([
            'petugas_cs_id'              => $petugasCsId,
            'nama_petugas_cs'            => $request->nama_petugas_cs,
            'lokasi_tugas'               => $request->lokasi_tugas,
            'nama_pj'                    => $request->nama_pj,
            'tanggal_penilaian'          => $request->tanggal_penilaian,
            'skor_disiplin'              => $request->skor_disiplin,
            'skor_komunikasi'            => $request->skor_komunikasi,
            'skor_sapu_pel'              => $request->skor_sapu_pel,
            'skor_lap_kaca_perabot'      => $request->skor_lap_kaca_perabot,
            'skor_tangga'                => $request->skor_tangga,
            'skor_kontrol_kebersihan'    => $request->skor_kontrol_kebersihan,
            'skor_buang_sampah'          => $request->skor_buang_sampah,
            'skor_koordinasi'            => $request->skor_koordinasi,
            'skor_tidak_tinggalkan_tugas' => $request->skor_tidak_tinggalkan_tugas,
            'skor_toilet'                => $request->skor_toilet,
            'skor_pelihara_sarana'       => $request->skor_pelihara_sarana,
            'skor_kerjasama'             => $request->skor_kerjasama,
            'skor_kesopanan'             => $request->skor_kesopanan,
            'skor_cekatan'               => $request->skor_cekatan,
            'skor_sop'                   => $request->skor_sop,
            'skor_kepuasan'              => $request->skor_kepuasan,
            'masukan_evaluasi'           => $request->masukan_evaluasi,
            'bukti_dokumentasi'          => count($filePaths) > 0 ? $filePaths : null,
            'rata_rata'                  => $rataRata,
            'kategori'                   => $kategori,
        ]);

        // Set throttle cookie: 1 kali per 30 menit per IP
        \Illuminate\Support\Facades\Cache::put($key, true, now()->addMinutes(30));

        return redirect()->route('penilaian-pj.sukses')->with([
            'nama_petugas' => $penilaian->nama_petugas_cs,
            'nama_pj'      => $penilaian->nama_pj,
            'rata_rata'    => $penilaian->rata_rata,
            'kategori'     => $penilaian->kategori,
        ]);
    }

    /**
     * Tampilan konfirmasi sukses pengiriman formulir.
     */
    public function sukses()
    {
        return view('penilaian-pj.sukses');
    }
}
