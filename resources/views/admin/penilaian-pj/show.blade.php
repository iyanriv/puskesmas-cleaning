@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid py-4 px-lg-4" style="max-width: 1000px;">

    {{-- Breadcrumb & Back --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('admin.penilaian-pj.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 mb-2">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
            <h4 class="fw-bold text-dark mb-0">Rincian Lembar Penilaian PJ Lantai</h4>
        </div>
        <a href="{{ route('admin.penilaian-pj.cetak', $penilaian->id) }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen
        </a>
    </div>

    {{-- ================================================================
         KARTU RINGKASAN EVALUASI
         ================================================================ --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="p-4 text-white" style="background: linear-gradient(135deg, #064e2b 0%, #0a6c3b 60%, #10B981 100%);">
            <div class="row align-items-center g-3">
                <div class="col-md-8">
                    <span class="badge rounded-pill px-3 py-1 mb-2 text-white" style="font-size:0.75rem; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3);">
                        <i class="bi bi-calendar-check me-1"></i> Tanggal Penilaian: {{ $penilaian->tanggal_penilaian->translatedFormat('l, d F Y') }}
                    </span>
                    <h3 class="fw-bold text-white mb-1">{{ $penilaian->nama_petugas_cs }}</h3>
                    <div class="text-white text-opacity-90">
                        <i class="bi bi-geo-alt me-1"></i> Penugasan: <strong>{{ $penilaian->lokasi_tugas }}</strong>
                    </div>
                    <div class="text-white text-opacity-80 small mt-1">
                        <i class="bi bi-person-badge me-1"></i> Dinilai oleh: <strong>{{ $penilaian->nama_pj }}</strong> (PJ Kebersihan)
                    </div>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="p-3 rounded-4 d-inline-block text-center text-white" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(6px); min-width: 170px;">
                        <div class="text-white text-opacity-85 small text-uppercase fw-semibold" style="letter-spacing:0.5px;">Rata-rata Skor</div>
                        <div class="fs-1 fw-bold text-white lh-1 my-1">{{ $penilaian->rata_rata }}</div>
                        <span class="badge bg-white text-success fw-bold px-3 py-1 rounded-pill" style="font-size:0.82rem;">
                            {{ $penilaian->kategori }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         RINCIAN 16 INDIKATOR PENILAIAN
         ================================================================ --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-dark mb-1">
                <i class="bi bi-list-check text-success me-2"></i>Rincian Nilai per Indikator
            </h5>
            <p class="text-muted small mb-0">Rincian skor yang diberikan oleh PJ Kebersihan berdasarkan kuesioner resmi</p>
        </div>
        <div class="card-body p-4">

            @foreach($indikator as $kategoriNama => $items)
                <div class="mb-4">
                    <div class="bg-light p-2 px-3 rounded-3 fw-bold text-success text-uppercase mb-3" style="font-size:0.82rem; letter-spacing:0.5px;">
                        {{ $kategoriNama }}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px;">No</th>
                                    <th>Indikator Penilaian</th>
                                    <th class="text-center" style="width: 100px;">Skor</th>
                                    <th class="text-center" style="width: 130px;">Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $fieldKey => $info)
                                    @php
                                        $skor = (int) $penilaian->{$fieldKey};
                                        $kat = \App\Models\PenilaianPjLantai::hitungKategori($skor);
                                        $badgeClass = match($kat) {
                                            'Sangat Baik'   => 'bg-success text-white',
                                            'Baik'          => 'bg-primary text-white',
                                            'Cukup'         => 'bg-info text-dark',
                                            'Kurang'        => 'bg-warning text-dark',
                                            'Sangat Kurang' => 'bg-danger text-white',
                                            default         => 'bg-secondary text-white',
                                        };
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $info['label'] }}</div>
                                            <div class="text-muted small">{{ $info['deskripsi'] }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="fs-6 fw-bold text-dark">{{ $skor }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $badgeClass }} rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                                {{ $kat }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    {{-- ================================================================
         MASUKAN / EVALUASI DARI PJ KEBERSIHAN
         ================================================================ --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-chat-left-quote text-success"></i> Masukan / Evaluasi dari PJ Kebersihan
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="p-3 bg-light rounded-3 border-start border-4 border-success">
                <p class="mb-0 text-dark" style="white-space: pre-wrap; font-size: 0.95rem; line-height: 1.6;">{{ $penilaian->masukan_evaluasi }}</p>
            </div>
        </div>
    </div>

    {{-- ================================================================
         BUKTI DOKUMENTASI KETIDAKSESUAIAN
         ================================================================ --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-images text-success"></i> Bukti Dokumentasi Ketidaksesuaian
            </h5>
            <p class="text-muted small mb-0">Lampiran foto atau dokumen pendukung yang diunggah oleh penilai</p>
        </div>
        <div class="card-body p-4">
            @if(empty($penilaian->bukti_dokumentasi) || count($penilaian->bukti_dokumentasi) === 0)
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-file-earmark-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    Tidak ada file bukti dokumentasi yang dilampirkan pada penilaian ini.
                </div>
            @else
                <div class="row g-3">
                    @foreach($penilaian->bukti_dokumentasi as $file)
                        @php
                            $isImage = isset($file['tipe']) && str_starts_with($file['tipe'], 'image/');
                            $fileUrl = asset('storage/' . $file['path']);
                        @endphp
                        <div class="col-sm-6 col-md-4">
                            <div class="card h-100 border rounded-3 overflow-hidden shadow-sm">
                                @if($isImage)
                                    <a href="{{ $fileUrl }}" target="_blank" title="Klik untuk memperbesar">
                                        <img src="{{ $fileUrl }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="Bukti Foto">
                                    </a>
                                @else
                                    <div class="p-4 text-center bg-light" style="height: 180px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-1 mb-2"></i>
                                        <span class="small text-muted">Dokumen PDF</span>
                                    </div>
                                @endif
                                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                                    <div class="text-truncate small fw-semibold me-2" title="{{ $file['nama_asli'] ?? 'Dokumen' }}">
                                        {{ $file['nama_asli'] ?? 'Dokumen Bukti' }}
                                    </div>
                                    <a href="{{ $fileUrl }}" target="_blank" class="btn btn-outline-success btn-sm rounded-circle p-1" style="width:28px; height:28px;" title="Unduh/Buka">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
