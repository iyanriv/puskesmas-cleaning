@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f4f6f9; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%; margin: 0 auto;
        background-color: #f4f6f9; min-height: 100vh;
        padding-bottom: 40px;
    }
    .header-section {
        background: linear-gradient(135deg, #12a65a, #0a7040);
        border-radius: 0 0 28px 28px;
        padding: 1.5rem 1.5rem 2rem 1.5rem; color: white;
    }
    .content-area { padding: 1.25rem; }

    .detail-card {
        background: white; border-radius: 20px;
        padding: 1.25rem; box-shadow: 0 4px 20px rgba(18,166,90,0.05);
        margin-bottom: 0.85rem;
    }
    .section-title {
        font-size: 0.78rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
        display: flex; align-items: center; gap: 0.4rem;
    }
    .info-row {
        display: flex; align-items: flex-start;
        gap: 0.75rem; padding: 0.6rem 0;
        border-bottom: 1px solid #f3f4f6; font-size: 0.86rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row i { color: #12a65a; width: 18px; text-align: center; margin-top: 2px; flex-shrink: 0; }
    .info-label { font-size: 0.72rem; color: #9ca3af; margin-bottom: 1px; }

    /* Status pills */
    .status-pill {
        padding: 4px 14px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 700;
    }
    .status-menunggu { background: #fef3c7; color: #d97706; }
    .status-diterima { background: #d1fae5; color: #059669; }

    /* Checklist tugas */
    .tugas-list {
        list-style: none; padding: 0; margin: 0;
    }
    .tugas-list li {
        display: flex; align-items: flex-start; gap: 0.65rem;
        padding: 0.6rem 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 0.85rem; color: #374151;
    }
    .tugas-list li:last-child { border-bottom: none; }
    .tugas-icon-selesai {
        width: 22px; height: 22px; border-radius: 50%;
        background: #d1fae5; color: #059669;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; flex-shrink: 0; margin-top: 1px;
    }
    .tugas-icon-pending {
        width: 22px; height: 22px; border-radius: 50%;
        border: 2px solid #d1d5db;
        flex-shrink: 0; margin-top: 1px;
    }
    .tugas-icon-eskalasi {
        width: 22px; height: 22px; border-radius: 50%;
        background: #e0e7ff; color: #4f46e5;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem; flex-shrink: 0; margin-top: 1px;
    }

    /* Catatan box */
    .catatan-box {
        background: #f9fafb; border-radius: 12px;
        padding: 0.85rem; font-size: 0.84rem;
        color: #374151; border-left: 3px solid #12a65a;
        line-height: 1.6;
    }
    .catatan-eskalasi-box {
        background: #fff7ed; border-radius: 12px;
        padding: 0.85rem; font-size: 0.84rem;
        color: #92400e; border-left: 3px solid #f59e0b;
        line-height: 1.6;
    }

    /* Timeline eskalasi */
    .timeline-item {
        display: flex; gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
        position: relative;
    }
    .timeline-item:last-child { border-bottom: none; }
    .timeline-dot {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; flex-shrink: 0;
    }
    .dot-asal  { background: #dbeafe; color: #2563eb; }
    .dot-oper  { background: #fef3c7; color: #d97706; }
    .dot-selesai { background: #d1fae5; color: #059669; }

    /* Tombol kembali */
    .btn-kembali {
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        width: 100%; background: #12a65a; color: white; border: none;
        border-radius: 14px; padding: 0.9rem;
        font-size: 0.95rem; font-weight: 600; text-decoration: none;
        box-shadow: 0 4px 14px rgba(18,166,90,0.25);
        transition: all 0.2s;
    }
    .btn-kembali:hover { background: #0a7040; color: white; }

    @media (min-width: 992px) { .content-area { max-width: 700px; margin: 0 auto; } }
</style>

<div class="mobile-container">

    {{-- ===== HEADER ===== --}}
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
            <div class="ms-auto d-flex flex-column align-items-end gap-1">
                <span class="status-pill status-{{ $operan->status_terima }}">
                    {{ $operan->status_terima === 'diterima' ? 'Diterima ✓' : 'Menunggu' }}
                </span>
                @if(!empty($operan->tugas_items))
                    {!! $operan->badgePenyelesaian() !!}
                @endif
            </div>
        </div>
    </div>

    <div class="content-area">

        {{-- ===== INFO UTAMA ===== --}}
        <div class="detail-card">
            <div class="section-title"><i class="bi bi-person-fill text-success"></i> Informasi Serah Terima</div>
            <div class="info-row">
                <i class="bi bi-person-fill"></i>
                <div>
                    <div class="info-label">Pengirim</div>
                    <div class="fw-semibold">{{ $operan->pengirim->name }}</div>
                </div>
            </div>
            <div class="info-row">
                <i class="bi bi-person-check-fill"></i>
                <div>
                    <div class="info-label">Penerima</div>
                    <div class="fw-semibold">{{ $operan->penerima->name }}</div>
                </div>
            </div>
            <div class="info-row">
                <i class="bi bi-clock-fill"></i>
                <div>
                    <div class="info-label">Waktu Kirim</div>
                    <div class="fw-semibold">{{ $operan->waktu }}</div>
                </div>
            </div>
            @if($operan->dibaca_pada)
            <div class="info-row">
                <i class="bi bi-eye-fill" style="color:#8b5cf6;"></i>
                <div>
                    <div class="info-label">Dibaca/Diterima pada</div>
                    <div class="fw-semibold">{{ $operan->dibaca_pada->translatedFormat('d M Y, H:i') }}</div>
                </div>
            </div>
            @endif
        </div>

        {{-- ===== INFO TUGAS ===== --}}
        <div class="detail-card">
            <div class="section-title"><i class="bi bi-briefcase-fill text-success"></i> Info Tugas</div>
            <div class="d-flex flex-wrap gap-1">
                @if($operan->tempat_tugas)
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                        <i class="bi bi-geo-alt-fill me-1"></i>{{ $operan->tempat_tugas }}
                    </span>
                @endif
                @if($operan->waktu_jaga)
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                        <i class="bi bi-calendar-check-fill me-1"></i>{{ $operan->waktu_jaga }}
                    </span>
                @endif
            </div>
        </div>

        {{-- ===== CATATAN UMUM ===== --}}
        @if($operan->catatan)
        <div class="detail-card">
            <div class="section-title"><i class="bi bi-chat-text"></i> Catatan Umum</div>
            <div class="catatan-box">{{ $operan->catatan }}</div>
        </div>
        @endif

        {{-- ===== DAFTAR ITEM TUGAS ===== --}}
        @if(!empty($operan->tugas_items))
        <div class="detail-card">
            <div class="section-title">
                <i class="bi bi-list-task text-danger"></i> Daftar Tugas Yang Dioper
                <span class="ms-auto badge bg-{{ $operan->status_penyelesaian === 'selesai' ? 'success' : ($operan->status_penyelesaian === 'dieskalasi' ? 'secondary' : 'danger') }}"
                    style="font-size:0.68rem; text-transform: none; font-weight: 600; letter-spacing: 0;">
                    {{ $operan->status_penyelesaian === 'selesai' ? '✓ Selesai' : ($operan->status_penyelesaian === 'dieskalasi' ? '→ Diteruskan' : '● Belum Selesai') }}
                </span>
            </div>
            <ul class="tugas-list">
                @foreach($operan->tugas_items as $item)
                    <li>
                        @if($operan->status_penyelesaian === 'selesai')
                            <div class="tugas-icon-selesai"><i class="bi bi-check2"></i></div>
                        @elseif($operan->status_penyelesaian === 'dieskalasi')
                            <div class="tugas-icon-eskalasi"><i class="bi bi-arrow-right"></i></div>
                        @else
                            <div class="tugas-icon-pending"></div>
                        @endif
                        <span>{{ $item }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- ===== STATUS ALAT ===== --}}
        @if($operan->status_alat && count($operan->status_alat) > 0)
        <div class="detail-card">
            <div class="section-title"><i class="bi bi-tools text-success"></i> Alat Tersedia & Baik</div>
            <div class="d-flex flex-wrap gap-1">
                @foreach($operan->status_alat as $alat)
                    <span class="badge" style="background:#d1fae5; color:#059669; border:1px solid #a7f3d0; font-size:0.78rem; font-weight:600;">
                        ✓ {{ $alat }}
                    </span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ===== CATATAN ESKALASI (jika ada) ===== --}}
        @if($operan->status_penyelesaian === 'dieskalasi' && $operan->catatan_eskalasi)
        <div class="detail-card">
            <div class="section-title"><i class="bi bi-exclamation-triangle-fill text-warning"></i> Alasan Tugas Diteruskan</div>
            <div class="catatan-eskalasi-box">
                <i class="bi bi-chat-quote-fill me-1"></i>{{ $operan->catatan_eskalasi }}
            </div>
            @if($operan->eskalasiBerikutnya)
                <div class="mt-2 d-flex align-items-center gap-2" style="font-size:0.8rem; color:#6b7280;">
                    <i class="bi bi-arrow-right-circle-fill text-secondary"></i>
                    Diteruskan ke: <strong>{{ $operan->eskalasiBerikutnya->penerima->name }}</strong>
                    <span class="status-pill status-{{ $operan->eskalasiBerikutnya->status_terima }}">
                        {{ $operan->eskalasiBerikutnya->status_terima === 'diterima' ? 'Diterima ✓' : 'Menunggu' }}
                    </span>
                </div>
            @endif
        </div>
        @endif

        {{-- ===== RIWAYAT ESKALASI / ASAL OPERAN ===== --}}
        @if($operan->parentOperan)
        <div class="detail-card">
            <div class="section-title"><i class="bi bi-diagram-2 text-secondary"></i> Riwayat Rantai Operan</div>
            <div class="timeline-item">
                <div class="timeline-dot dot-asal"><i class="bi bi-send-fill"></i></div>
                <div>
                    <div class="fw-semibold" style="font-size:0.84rem;">Operan Asal</div>
                    <div class="text-secondary" style="font-size:0.76rem;">
                        Dari <strong>{{ $operan->parentOperan->pengirim->name }}</strong>
                        → <strong>{{ $operan->parentOperan->penerima->name }}</strong>
                    </div>
                    <div class="text-secondary" style="font-size:0.72rem;">
                        {{ $operan->parentOperan->tanggal->translatedFormat('d M Y') }}, {{ $operan->parentOperan->waktu }}
                    </div>
                    @if($operan->parentOperan->catatan_eskalasi)
                        <div class="catatan-eskalasi-box mt-1" style="font-size:0.78rem;">
                            Alasan diteruskan: {{ $operan->parentOperan->catatan_eskalasi }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-dot dot-oper"><i class="bi bi-arrow-right"></i></div>
                <div>
                    <div class="fw-semibold" style="font-size:0.84rem;">Operan Ini (Diteruskan)</div>
                    <div class="text-secondary" style="font-size:0.76rem;">
                        Dari <strong>{{ $operan->pengirim->name }}</strong>
                        → <strong>{{ $operan->penerima->name }}</strong>
                    </div>
                    <div class="text-secondary" style="font-size:0.72rem;">
                        {{ $operan->tanggal->translatedFormat('d M Y') }}, {{ $operan->waktu }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ===== TOMBOL KEMBALI ===== --}}
        <a href="{{ route('operan.index') }}" class="btn-kembali">
            <i class="bi bi-arrow-left"></i> Kembali ke Operan Shift
        </a>
    </div>
</div>
@endsection
