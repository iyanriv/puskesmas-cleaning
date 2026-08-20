@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f7f9fa; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%;
        margin: 0 auto;
        background-color: #f4f7f6;
        min-height: 100vh;
        padding-bottom: 40px;
    }
    .header-section {  
        background: linear-gradient(135deg, #12a65a, #10B981);
        border-radius: 0 0 30px 30px;
        padding: 1.5rem 1.5rem 2rem 1.5rem;
        color: white;
    }
    .content-area { padding: 1.25rem; }
    .foto-pair {
        display: grid; grid-template-columns: 1fr 1fr;
        gap: 0.75rem; margin-bottom: 1rem;
    }
    .foto-card {
        background: white; border-radius: 16px;
        overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .foto-card img {
        width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block;
    }
    .foto-label {
        padding: 6px 10px; font-size: 0.72rem; font-weight: 700;
        text-align: center; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .foto-label.before { background: #fef3c7; color: #d97706; }
    .foto-label.after  { background: #d1fae5; color: #059669; }
    .info-card {
        background: white; border-radius: 16px;
        padding: 1rem 1.2rem; margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .info-row {
        display: flex; align-items: center;
        gap: 0.6rem; padding: 0.4rem 0;
        border-bottom: 1px solid #f3f4f6; font-size: 0.84rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row i { color: #12a65a; width: 18px; text-align: center; }
    .badge-selesai {
        background: #d1fae5; color: #059669;
        padding: 4px 14px; border-radius: 20px;
        font-size: 0.77rem; font-weight: 700;
    }
    .skor-display {
        display: flex; gap: 4px;
    }
    .skor-display i { color: #f59e0b; font-size: 1rem; }
    .btn-kembali {
        width: 100%; background: #12a65a; color: white;
        border: none; border-radius: 16px; padding: 1rem;
        font-size: 1rem; font-weight: 600;
        box-shadow: 0 4px 15px rgba(18,166,90,0.3);
    }
    @media (min-width: 992px) { .content-area { max-width: 700px; margin: 0 auto; } }
</style>

<div class="mobile-container">
    <div class="header-section">
        <div class="d-flex align-items-center mb-1">
            <a href="{{ route('ceklis.index') }}" class="text-white me-2" style="font-size:1.2rem;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0">Detail Ceklis</h5>
                <p class="mb-0 text-white-50" style="font-size:0.82rem;">{{ $ceklis->area->nama_ruangan }}</p>
            </div>
        </div>
    </div>

    <div class="content-area">

        {{-- Foto Before & After --}}
        <div class="foto-pair">
            <div class="foto-card">
                @if($ceklis->foto_before)
                    <img src="{{ Storage::url($ceklis->foto_before) }}" alt="Foto Before">
                @else
                    <div class="d-flex align-items-center justify-content-center bg-light" style="aspect-ratio:4/3;"><i class="bi bi-image text-secondary fs-2"></i></div>
                @endif
                <div class="foto-label before">Before</div>
            </div>
            <div class="foto-card">
                @if($ceklis->foto_after)
                    <img src="{{ Storage::url($ceklis->foto_after) }}" alt="Foto After">
                @else
                    <div class="d-flex align-items-center justify-content-center bg-light" style="aspect-ratio:4/3;"><i class="bi bi-image text-secondary fs-2"></i></div>
                @endif
                <div class="foto-label after">After</div>
            </div>
        </div>

        {{-- Info ceklis --}}
        <div class="info-card">
            <div class="info-row">
                <i class="bi bi-building"></i>
                <span class="fw-semibold">{{ $ceklis->area->nama_ruangan }}</span>
                <span class="badge-selesai ms-auto">{{ ucfirst($ceklis->status) }}</span>
            </div>
            <div class="info-row">
                <i class="bi bi-person"></i>
                <span>{{ $ceklis->pengguna->name }}</span>
            </div>
            <div class="info-row">
                <i class="bi bi-calendar3"></i>
                <span>{{ $ceklis->tanggal->translatedFormat('d F Y') }}</span>
            </div>
            <div class="info-row">
                <i class="bi bi-clock"></i>
                <span>{{ $ceklis->waktu_mulai }} – {{ $ceklis->waktu_selesai ?? '...' }}</span>
            </div>
            @if($ceklis->lat_long)
            <div class="info-row">
                <i class="bi bi-geo-alt"></i>
                <span style="font-size:0.77rem;">{{ $ceklis->lat_long }}</span>
            </div>
            @endif
            @if($ceklis->skor)
            <div class="info-row">
                <i class="bi bi-star"></i>
                <div class="skor-display">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="bi {{ $i <= $ceklis->skor ? 'bi-star-fill' : 'bi-star' }}"></i>
                    @endfor
                </div>
                <span class="ms-2 text-secondary" style="font-size:0.8rem;">Skor Supervisor</span>
            </div>
            @endif
            @if($ceklis->catatan)
            <div class="info-row">
                <i class="bi bi-chat-text"></i>
                <span style="font-size:0.82rem;">{{ $ceklis->catatan }}</span>
            </div>
            @endif
        </div>

        <a href="{{ route('ceklis.index') }}" class="btn btn-kembali text-decoration-none d-block text-center">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Daftar Area
        </a>
    </div>
</div>
@endsection
