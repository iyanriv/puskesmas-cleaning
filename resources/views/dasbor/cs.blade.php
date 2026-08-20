@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f7f9fa; font-family: 'Inter', sans-serif; }

    .stat-box {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 1rem;
        flex: 1;
        backdrop-filter: blur(5px);
    }

    .menu-item {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s ease;
    }
    .menu-item:last-child { border-bottom: none; }
    .menu-item:hover { background-color: #f8fdf9; padding-left: 0.5rem; }

    .icon-circle {
        width: 45px; height: 45px;
        border-radius: 50%;
        background-color: #e8f6ef;
        color: #12a65a;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .activity-section { padding: 1.5rem; }

    .activity-card {
        background-color: white;
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 0.8rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        transition: transform 0.2s ease;
    }
    .activity-card:hover { transform: translateX(4px); }

    .dot { width: 10px; height: 10px; border-radius: 50%; margin-right: 1rem; flex-shrink: 0; }
    .dot-hijau { background-color: #12a65a; }
    .dot-kuning { background-color: #f5b041; }

    @keyframes pulse {
        0%, 100% { opacity: 1; } 50% { opacity: 0.55; }
    }

    /* Desktop: grid menu & aktivitas */
    @media (min-width: 992px) {
        .activity-section { padding: 1.5rem 3rem; }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .menu-grid .menu-item {
            border: 1px solid #f0f0f0;
            border-radius: 16px;
            padding: 1.25rem;
        }
        .menu-grid .menu-item:hover {
            border-color: #12a65a;
            background-color: #f0fdf4;
        }

        .activity-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.8rem;
        }
        .activity-grid .activity-card {
            margin-bottom: 0;
        }
    }

    @media (min-width: 1200px) {
        .menu-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>

<div class="mobile-container">
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <p class="mb-1 text-white-50" style="font-size: 0.85rem;">Puskesmas Cempaka Putih</p>
                <h2 class="fw-bold mb-1" style="font-size: 1.6rem;">Halo, {{ explode(' ', $pengguna->name)[0] }}! 👋</h2>
                <p class="mb-0 text-white-50" style="font-size: 0.9rem;">Shift {{ ucfirst($pengguna->shift ?? '-') }} — Semangat bersih!</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-light rounded-pill"><i class="bi bi-box-arrow-right"></i></button>
            </form>
        </div>
        <div class="d-flex gap-3 mt-3">
            <div class="stat-box">
                <div class="fw-bold" style="font-size: 1.5rem;">{{ $selesaiCeklis }}/{{ max($totalCeklis, 1) }}</div>
                <div style="font-size: 0.8rem; opacity: 0.9;">tugas hari ini</div>
            </div>
            <div class="stat-box">
                <div class="fw-bold" style="font-size: 1.5rem;">{{ $persentase }}%</div>
                <div style="font-size: 0.8rem; opacity: 0.9;">tercapai</div>
            </div>
        </div>
    </div>

    <div class="overlap-menu">
        <div class="menu-grid">
            <a href="{{ route('ceklis.index') }}" class="menu-item">
                <div class="icon-circle"><i class="bi bi-card-checklist"></i></div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0 text-dark">Ceklis Kebersihan</h6>
                    <small class="text-secondary">Catat aktivitas kebersihan</small>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
            </a>
            <a href="{{ route('operan.index') }}" class="menu-item" style="position:relative;">
                <div class="icon-circle"><i class="bi bi-arrow-left-right"></i></div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0 text-dark">Operan Shift</h6>
                    <small class="text-secondary">Koordinasi pergantian shift</small>
                </div>
                @if($operanMenunggu->count() > 0)
                    <span class="badge rounded-pill bg-danger" style="font-size:0.68rem;">
                        {{ $operanMenunggu->count() }}
                    </span>
                @endif
                <i class="bi bi-chevron-right text-secondary ms-1"></i>
            </a>
            <a href="{{ route('barang.katalog') }}" class="menu-item">
                <div class="icon-circle"><i class="bi bi-box-seam"></i></div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0 text-dark">Minta Barang</h6>
                    <small class="text-secondary">Permintaan barang kebersihan</small>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
            </a>
            <a href="{{ route('sampah.buat') }}" class="menu-item">
                <div class="icon-circle"><i class="bi bi-recycle"></i></div>
                <div class="flex-grow-1">
                    <h6 class="fw-bold mb-0 text-dark">Bank Sampah</h6>
                    <small class="text-secondary">Setor sampah daur ulang</small>
                </div>
                <i class="bi bi-chevron-right text-secondary"></i>
            </a>
        </div>
    </div>

    {{-- FR-017: Notifikasi operan masuk — muncul di dasbor jika ada operan menunggu --}}
    @if($operanMenunggu->count() > 0)
    <div style="padding: 0 1.25rem; margin-top: 0.75rem;">
        @foreach($operanMenunggu as $op)
        <a href="{{ route('operan.index') }}" class="d-flex align-items-center gap-3 text-decoration-none mb-2"
           style="background:#fff8e1; border:1.5px solid #fbbf24; border-radius:16px; padding:0.85rem 1rem;">
            <div style="width:40px;height:40px;border-radius:50%;background:#fef3c7;color:#d97706;
                        display:flex;align-items:center;justify-content:center;font-size:1.1rem;flex-shrink:0;">
                <i class="bi bi-bell-fill"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold text-dark" style="font-size:0.88rem;">
                    Operan dari {{ $op->pengirim->name }}
                </div>
                <div class="text-secondary" style="font-size:0.75rem;">
                    Tap untuk melihat & konfirmasi penerimaan
                </div>
            </div>
            <span style="background:#fbbf24;color:white;font-size:0.68rem;font-weight:700;
                         padding:3px 8px;border-radius:20px;animation:pulse 1.5s infinite;flex-shrink:0;">
                BARU
            </span>
        </a>
        @endforeach
    </div>
    @endif

    <div class="activity-section">
        <h5 class="fw-bold text-dark mb-3">Aktivitas Terakhir</h5>
        <div class="activity-grid">
            @forelse($aktivitasTerakhir as $aktivitas)
                <div class="activity-card">
                    <div class="dot dot-{{ $aktivitas['warna'] }}"></div>
                    <div>
                        <div class="text-dark fw-medium" style="font-size: 0.9rem;">{{ $aktivitas['teks'] }}</div>
                        <div class="text-secondary" style="font-size: 0.75rem;">{{ $aktivitas['waktu'] }}</div>
                    </div>
                </div>
            @empty
                <p class="text-secondary text-center py-3">Belum ada aktivitas hari ini.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
