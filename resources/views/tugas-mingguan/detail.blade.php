@extends('tata-letak.aplikasi')

@section('content')
<style>
    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 0.75rem;
        margin-top: 0.75rem;
    }

    .gallery-img-card {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        position: relative;
        background: #000;
        height: 130px;
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
        border-radius: 14px;
        border: 2px solid #10b981;
        cursor: pointer;
    }
</style>

<div class="container-fluid px-3 px-lg-4 py-4" style="max-width: 900px;">
    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-calendar-check-fill text-success me-2"></i>Rincian Tugas Mingguan
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">{{ $tugas->user->name }}</span>
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('tugas-mingguan.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
            <div>
                {!! $tugas->badgeStatus() !!}
            </div>
        </div>
    </div>

    @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Detail Info Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center fw-bold"
                     style="width:36px; height:36px; background:#e6f4f5; color:#267A7E; font-size:0.9rem;">
                    {{ strtoupper(substr($tugas->user->name, 0, 1)) }}
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">{{ $tugas->user->name }}</h6>
                    <div class="text-muted" style="font-size: 0.75rem;">Nomor Karyawan: {{ $tugas->user->nik ?? '-' }}</div>
                </div>
            </div>
            <div class="text-end text-muted" style="font-size: 0.8rem;">
                <div><i class="bi bi-calendar3 me-1 text-success"></i> {{ $tugas->tanggal->translatedFormat('l, d F Y') }}</div>
                <div><i class="bi bi-clock me-1 text-success"></i> {{ \Carbon\Carbon::parse($tugas->waktu_pelaporan)->format('H:i') }} WIB</div>
            </div>
        </div>
        <div class="card-body p-4">

            {{-- Rincian Kegiatan --}}
            <div class="mb-4">
                <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                    <i class="bi bi-journal-text text-success me-1"></i>Rincian Kegiatan
                </label>
                <div class="p-3 bg-light rounded-3 text-dark border" style="line-height: 1.6; font-size: 0.9rem; white-space: pre-line;">{{ $tugas->rincian_kegiatan }}</div>
            </div>

            <div class="row g-4 mb-4">
                {{-- Galeri Foto Sebelum Pengerjaan --}}
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-semibold mb-0" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-camera text-primary me-1"></i>Foto Sebelum
                        </label>
                        <span class="badge bg-light text-muted border" style="font-size:0.75rem;">
                            {{ is_array($tugas->foto_sebelum) ? count($tugas->foto_sebelum) : 0 }} Foto
                        </span>
                    </div>

                    @if(is_array($tugas->foto_sebelum) && count($tugas->foto_sebelum) > 0)
                        <div class="gallery-grid">
                            @foreach($tugas->foto_sebelum as $foto)
                                <div class="gallery-img-card shadow-xs">
                                    <a href="{{ asset('storage/' . $foto) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $foto) }}" alt="Foto Sebelum">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-4 text-center bg-light rounded-3 border text-muted" style="font-size:0.85rem;">
                            Tidak ada foto sebelum pengerjaan.
                        </div>
                    @endif
                </div>

                {{-- Foto Sesudah Pengerjaan --}}
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label fw-semibold mb-0" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-check-circle text-success me-1"></i>Foto Sesudah (Hasil)
                        </label>
                        @if($tugas->foto_setelah)
                            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">Tuntas</span>
                        @endif
                    </div>

                    @if($tugas->foto_setelah)
                        <div class="mt-2 shadow-xs rounded-3 overflow-hidden">
                            <a href="{{ asset('storage/' . $tugas->foto_setelah) }}" target="_blank">
                                <img src="{{ asset('storage/' . $tugas->foto_setelah) }}" alt="Foto Setelah Selesai" class="after-photo-large">
                            </a>
                        </div>
                    @else
                        <div class="p-4 text-center bg-light rounded-3 border text-muted" style="font-size:0.85rem;">
                            <i class="bi bi-hourglass-split fs-3 text-warning d-block mb-1"></i>
                            Pekerjaan sedang berlangsung atau foto selesai belum diunggah.
                            @if($tugas->user_id === auth()->id())
                                <div class="mt-3">
                                    <a href="{{ route('tugas-mingguan.isi-after', $tugas->id) }}" class="btn btn-sm btn-success rounded-3 px-3">
                                        <i class="bi bi-upload me-1"></i> Unggah Foto Selesai
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Form Verifikasi Supervisor / Admin --}}
            @if(in_array(auth()->user()->peran?->nama_peran ?? '', ['supervisor', 'admin']))
                <div class="card border rounded-4 p-3 bg-light bg-opacity-50">
                    <h6 class="fw-bold text-success mb-3">
                        <i class="bi bi-clipboard2-check me-2"></i>Verifikasi Supervisor / Admin
                    </h6>
                    <form action="{{ route('tugas-mingguan.verifikasi', $tugas->id) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold mb-1" style="font-size: 0.82rem; text-transform:uppercase; letter-spacing:.4px;">Status Validasi <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select form-select-sm rounded-3">
                                <option value="disetujui" {{ $tugas->status === 'disetujui' ? 'selected' : '' }}>Disetujui (Tuntas & Sesuai Standar)</option>
                                <option value="proses" {{ $tugas->status === 'proses' ? 'selected' : '' }}>Perlu Perbaikan (Kembalikan ke Proses)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="catatan_supervisor" class="form-label fw-semibold mb-1" style="font-size: 0.82rem; text-transform:uppercase; letter-spacing:.4px;">Catatan / Masukan</label>
                            <textarea name="catatan_supervisor" id="catatan_supervisor" rows="2" class="form-control form-control-sm rounded-3" placeholder="Catatan evaluasi terkait hasil pekerjaan...">{{ old('catatan_supervisor', $tugas->catatan_supervisor) }}</textarea>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success btn-sm rounded-3 px-4">
                                <i class="bi bi-check2 me-1"></i> Simpan Hasil Verifikasi
                            </button>
                        </div>
                    </form>
                </div>
            @else
                @if($tugas->catatan_supervisor || $tugas->status === 'disetujui')
                    <div class="p-3 bg-success-subtle border border-success-subtle rounded-3">
                        <div class="fw-bold text-success" style="font-size: 0.88rem;">
                            <i class="bi bi-patch-check-fill me-1"></i> Catatan Evaluasi Supervisor:
                        </div>
                        <p class="mb-0 text-dark mt-1" style="font-size: 0.85rem;">
                            {{ $tugas->catatan_supervisor ?: 'Pekerjaan telah diperiksa dan disetujui sesuai standar kebersihan Puskesmas.' }}
                        </p>
                        @if($tugas->verifikator)
                            <small class="text-muted d-block mt-2" style="font-size:0.75rem;">Diverifikasi oleh: {{ $tugas->verifikator->name }} ({{ $tugas->diverifikasi_pada?->format('d/m/Y H:i') }})</small>
                        @endif
                    </div>
                @endif
            @endif

        </div>
    </div>
</div>
@endsection
