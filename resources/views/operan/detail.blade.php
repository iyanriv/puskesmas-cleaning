@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f7f9fa; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%; margin: 0 auto;
        background-color: #f4f7f6; min-height: 100vh;
        padding-bottom: 40px;
    }
    .header-section {  
        background: linear-gradient(135deg, #12a65a, #0a7040);
        border-radius: 0 0 30px 30px;
        padding: 1.5rem 1.5rem 2rem 1.5rem; color: white;
    }
    .content-area { padding: 1.25rem; }
    .detail-card {
        background: white; border-radius: 20px;
        padding: 1.25rem; box-shadow: 0 4px 20px rgba(18,166,90,0.05);
        margin-bottom: 1rem;
    }
    .info-row {
        display: flex; align-items: flex-start;
        gap: 0.75rem; padding: 0.6rem 0;
        border-bottom: 1px solid #f3f4f6; font-size: 0.85rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row i { color: #12a65a; width: 18px; text-align: center; margin-top: 1px; flex-shrink: 0; }
    .status-pill {
        padding: 4px 14px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 700;
    }
    .status-menunggu { background: #fef3c7; color: #d97706; }
    .status-diterima { background: #d1fae5; color: #059669; }
    .alat-grid { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .alat-badge {
        background: #eff6ff; color: #12a65a; font-size: 0.75rem;
        padding: 4px 12px; border-radius: 20px; font-weight: 600;
        border: 1px solid #bfdbfe;
    }
    .catatan-box {
        background: #f9fafb; border-radius: 12px;
        padding: 0.85rem; font-size: 0.84rem;
        color: #374151; border-left: 3px solid #12a65a;
        line-height: 1.6;
    }
    .btn-kembali {
        width: 100%; background: #12a65a; color: white; border: none;
        border-radius: 14px; padding: 0.9rem;
        font-size: 0.95rem; font-weight: 600;
        box-shadow: 0 4px 14px rgba(18,166,90,0.25);
    }
    .section-title {
        font-size: 0.78rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    @media (min-width: 992px) { .content-area { max-width: 700px; margin: 0 auto; } }
</style>

<div class="mobile-container">
    <div class="header-section">
        <div class="d-flex align-items-center">
            <a href="{{ route('operan.index') }}" class="text-white me-3" style="font-size:1.2rem;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0">Detail Operan</h5>
                <p class="mb-0 text-white-50" style="font-size:0.82rem;">
                    {{ $operan->tanggal->translatedFormat('d F Y') }}
                </p>
            </div>
            <span class="status-pill status-{{ $operan->status_terima }} ms-auto">
                {{ $operan->status_terima === 'diterima' ? 'Diterima ✓' : 'Menunggu' }}
            </span>
        </div>
    </div>

    <div class="content-area">

        {{-- Info Utama --}}
        <div class="detail-card">
            <div class="info-row">
                <i class="bi bi-person-fill"></i>
                <div>
                    <div class="text-secondary" style="font-size:0.72rem;">Pengirim</div>
                    <div class="fw-semibold">{{ $operan->pengirim->name }}</div>
                </div>
            </div>
            <div class="info-row">
                <i class="bi bi-person-check-fill"></i>
                <div>
                    <div class="text-secondary" style="font-size:0.72rem;">Penerima</div>
                    <div class="fw-semibold">{{ $operan->penerima->name }}</div>
                </div>
            </div>
            <div class="info-row">
                <i class="bi bi-clock"></i>
                <div>
                    <div class="text-secondary" style="font-size:0.72rem;">Waktu Kirim</div>
                    <div class="fw-semibold">{{ $operan->waktu }}</div>
                </div>
            </div>
        </div>

        {{-- Info Tugas --}}
        <div class="detail-card">
            <div class="section-title"><i class="bi bi-briefcase-fill me-1"></i> Info Tugas</div>
            <div class="alat-grid">
                @if($operan->tempat_tugas)
                    <span class="alat-badge border-danger text-danger bg-white">
                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $operan->tempat_tugas }}
                    </span>
                @endif
                @if($operan->waktu_jaga)
                    <span class="alat-badge border-primary text-primary bg-white">
                        <i class="bi bi-calendar-check-fill me-1"></i>{{ $operan->waktu_jaga }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Catatan --}}
        @if($operan->catatan)
        <div class="detail-card">
            <div class="section-title"><i class="bi bi-chat-text me-1"></i> Catatan Pekerjaan</div>
            <div class="catatan-box">{{ $operan->catatan }}</div>
        </div>
        @endif

        <a href="{{ route('operan.index') }}" class="btn btn-kembali text-decoration-none d-block text-center">
            <i class="bi bi-arrow-left me-2"></i> Kembali ke Operan Shift
        </a>
    </div>
</div>
@endsection
