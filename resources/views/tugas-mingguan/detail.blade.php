@extends('tata-letak.aplikasi')

@section('content')
<style>
    .detail-hero-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
        margin-top: 0.75rem;
    }

    .gallery-img-card {
        border-radius: 14px;
        overflow: hidden;
        border: 2px solid #e2e8f0;
        position: relative;
        background: #000;
        height: 140px;
    }

    .gallery-img-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
        cursor: pointer;
    }

    .gallery-img-card:hover img {
        transform: scale(1.05);
    }

    .after-photo-large {
        width: 100%;
        max-height: 360px;
        object-fit: cover;
        border-radius: 16px;
        border: 2px solid #12a65a;
        cursor: pointer;
    }
</style>

<div class="container py-3" style="max-width: 800px;">
    <!-- Top Nav -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('tugas-mingguan.index') }}" class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
        </a>
        <div>
            {!! $tugas->badgeStatus() !!}
        </div>
    </div>

    @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Detail Info Card -->
    <div class="detail-hero-card">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 border-bottom pb-3 mb-3">
            <div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill mb-1">
                    <i class="bi bi-calendar-check me-1"></i> Tugas Mingguan CS
                </span>
                <h4 class="fw-bold text-dark mb-1">{{ $tugas->user->name }}</h4>
                <div class="text-muted" style="font-size: 0.85rem;">
                    Nomor Pegawai: <span class="fw-semibold text-dark">{{ $tugas->user->nik }}</span>
                </div>
            </div>
            <div class="text-md-end text-muted" style="font-size: 0.82rem;">
                <div><i class="bi bi-calendar3 me-1"></i> {{ $tugas->tanggal->translatedFormat('l, d F Y') }}</div>
                <div><i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($tugas->waktu_pelaporan)->format('H:i') }} WIB</div>
            </div>
        </div>

        <!-- Rincian Kegiatan -->
        <div class="mb-4">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-journal-text text-success me-1"></i> Rincian Kegiatan:</h6>
            <div class="p-3 bg-light rounded-3 text-dark border" style="line-height: 1.6; font-size: 0.95rem; white-space: pre-line;">{{ $tugas->rincian_kegiatan }}</div>
        </div>

        <div class="row g-4">
            <!-- Galeri Foto Sebelum Pengerjaan -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-camera text-primary me-1"></i> Foto Sebelum
                    </h6>
                    <span class="badge bg-secondary">
                        {{ is_array($tugas->foto_sebelum) ? count($tugas->foto_sebelum) : 0 }} Berkas
                    </span>
                </div>
                <small class="text-muted d-block mb-2">Maksimum 5 file dokumentasi awal</small>

                @if(!empty($tugas->foto_sebelum) && is_array($tugas->foto_sebelum))
                    <div class="gallery-grid">
                        @foreach($tugas->foto_sebelum as $index => $foto)
                            <div class="gallery-img-card">
                                <a href="{{ asset('storage/' . $foto) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $foto) }}" alt="Foto Sebelum {{ $index + 1 }}">
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 text-center bg-light rounded-3 border text-muted">
                        <i class="bi bi-image fs-3 d-block mb-1"></i>
                        Tidak ada foto sebelum pengerjaan.
                    </div>
                @endif
            </div>

            <!-- Foto Setelah Pengerjaan -->
            <div class="col-md-6">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="bi bi-check-circle-fill text-success me-1"></i> Foto Setelah Selesai
                    </h6>
                    @if($tugas->foto_setelah)
                        <span class="badge bg-success">Tersedia</span>
                    @else
                        <span class="badge bg-warning text-dark">Belum Diunggah</span>
                    @endif
                </div>
                <small class="text-muted d-block mb-2">Dokumentasi hasil pengerjaan tuntas</small>

                @if($tugas->foto_setelah)
                    <a href="{{ asset('storage/' . $tugas->foto_setelah) }}" target="_blank">
                        <img src="{{ asset('storage/' . $tugas->foto_setelah) }}" alt="Foto Setelah Selesai" class="after-photo-large">
                    </a>
                @else
                    <div class="p-4 text-center bg-light rounded-3 border text-muted">
                        <i class="bi bi-hourglass-split fs-3 text-warning d-block mb-1"></i>
                        Pekerjaan sedang berlangsung atau foto selesai belum diunggah.
                        @if($tugas->user_id === auth()->id())
                            <div class="mt-3">
                                <a href="{{ route('tugas-mingguan.isi-after', $tugas->id) }}" class="btn btn-sm btn-success rounded-pill px-3">
                                    <i class="bi bi-upload me-1"></i> Unggah Foto Selesai Sekarang
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if(in_array(auth()->user()->peran?->nama_peran ?? '', ['supervisor', 'admin']))
            <div class="card border-0 bg-light-subtle rounded-4 p-3 border">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="bi bi-clipboard2-check text-success me-1"></i> Verifikasi Supervisor / Admin
                </h6>
                <form action="{{ route('tugas-mingguan.verifikasi', $tugas->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold" style="font-size: 0.85rem;">Status Validasi:</label>
                        <select name="status" id="status" class="form-select form-select-sm rounded-3">
                            <option value="disetujui" {{ $tugas->status === 'disetujui' ? 'selected' : '' }}>Disetujui (Tuntas & Sesuai Standar)</option>
                            <option value="proses" {{ $tugas->status === 'proses' ? 'selected' : '' }}>Perlu Perbaikan (Kembalikan ke Proses)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="catatan_supervisor" class="form-label fw-semibold" style="font-size: 0.85rem;">Catatan / Masukan:</label>
                        <textarea name="catatan_supervisor" id="catatan_supervisor" rows="2" class="form-control form-control-sm rounded-3" placeholder="Catatan supervisor terkait hasil pekerjaan...">{{ old('catatan_supervisor', $tugas->catatan_supervisor) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-4">
                        <i class="bi bi-check2 me-1"></i> Simpan Hasil Verifikasi
                    </button>
                </form>
            </div>
        @else
            @if($tugas->catatan_supervisor || $tugas->status === 'disetujui')
                <div class="p-3 bg-success-subtle border border-success-subtle rounded-3">
                    <div class="fw-bold text-success" style="font-size: 0.9rem;">
                        <i class="bi bi-patch-check-fill me-1"></i> Catatan Evaluasi Supervisor:
                    </div>
                    <p class="mb-0 text-dark mt-1" style="font-size: 0.88rem;">
                        {{ $tugas->catatan_supervisor ?: 'Pekerjaan telah diperiksa dan disetujui sesuai standar kebersihan Puskesmas.' }}
                    </p>
                    @if($tugas->verifikator)
                        <small class="text-muted d-block mt-2">Diverifikasi oleh: {{ $tugas->verifikator->name }} ({{ $tugas->diverifikasi_pada?->format('d/m/Y H:i') }})</small>
                    @endif
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
