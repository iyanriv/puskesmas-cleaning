@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f7f9fa; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%;
        margin: 0 auto;
        background-color: #f4f7f6;
        min-height: 100vh;
        position: relative;
        padding-bottom: 30px;
    }
    .header-section {  
        background: linear-gradient(135deg, #12a65a 0%, #0d8a4a 50%, #0a7040 100%);
        border-radius: 0 0 30px 30px;
        padding: 1.5rem 1.5rem 3.5rem 1.5rem;
        color: white;
    }
    .area-card {
        background-color: white;
        border-radius: 16px;
        padding: 1rem 1.2rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s ease;
    }
    .area-card:active { transform: scale(0.98); }
    .area-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
    .status-dot {
        width: 12px; height: 12px;
        border-radius: 50%;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }
    .dot-belum  { background-color: #d1d5db; }
    .dot-proses { background-color: #f59e0b; }
    .dot-selesai { background-color: #10B981; }
    .badge-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 600;
    }
    .badge-belum  { background: #f3f4f6; color: #6b7280; }
    .badge-proses { background: #fef3c7; color: #d97706; }
    .badge-selesai { background: #d1fae5; color: #059669; }
    .overlap-cards {
        margin-top: -2.5rem;
        padding: 0 1.2rem;
        position: relative;
        z-index: 10;
    }
    .summary-card {
        background: white;
        border-radius: 20px;
        padding: 1rem 1.25rem;
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        margin-bottom: 1.25rem;
        display: flex;
        gap: 1rem;
    }
    .summary-item {
        flex: 1;
        text-align: center;
        padding: 0.5rem;
    }
    .summary-item .angka { font-size: 1.6rem; font-weight: 700; }
    .summary-item .label { font-size: 0.72rem; color: #9ca3af; margin-top: 2px; }
    .area-list-header {
        font-size: 0.8rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.6rem;
        margin-top: 0.5rem;
    }
    .icon-area {
        width: 42px; height: 42px;
        border-radius: 12px;
        background: #e8f6ef;
        color: #12a65a;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        margin-right: 0.85rem;
        flex-shrink: 0;
    }
    @media (min-width: 992px) {
        .overlap-cards { padding: 0 3rem; }
        .area-list-desktop { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
    }
    @media (min-width: 1200px) {
        .area-list-desktop { grid-template-columns: repeat(3, 1fr); }
    }
</style>

<div class="mobile-container">
    {{-- Header --}}
    <div class="header-section">
        <div class="d-flex align-items-center mb-1">
            <a href="{{ route('dasbor.cs') }}" class="text-white me-2" style="font-size:1.2rem;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0">Ceklis Kebersihan</h5>
                <p class="mb-0 text-white-50" style="font-size:0.82rem;">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
        </div>
    </div>

    <div class="overlap-cards">

        {{-- Alert sukses / info --}}
        @if(session('sukses'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 py-2 px-3 mb-3" role="alert" style="font-size:0.85rem;">
                <i class="bi bi-check-circle me-1"></i> {{ session('sukses') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show rounded-3 py-2 px-3 mb-3" role="alert" style="font-size:0.85rem;">
                <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Summary card --}}
        @php
            $selesai = collect($daftarArea)->where('status','selesai')->count();
            $proses  = collect($daftarArea)->where('status','proses')->count();
            $belum   = collect($daftarArea)->where('status','belum')->count();
            $total   = count($daftarArea);
        @endphp
        <div class="summary-card">
            <div class="summary-item">
                <div class="angka text-success">{{ $selesai }}</div>
                <div class="label">Selesai</div>
            </div>
            <div class="summary-item" style="border-left:1px solid #f3f4f6;border-right:1px solid #f3f4f6;">
                <div class="angka text-warning">{{ $proses }}</div>
                <div class="label">Proses</div>
            </div>
            <div class="summary-item">
                <div class="angka text-secondary">{{ $belum }}</div>
                <div class="label">Belum</div>
            </div>
        </div>

        {{-- Daftar Area --}}
        <div class="area-list-header">Daftar Area ({{ $total }} ruangan)</div>

        <div class="area-list-desktop">
        @forelse($daftarArea as $item)
            @php
                $area   = $item['area'];
                $status = $item['status'];
                $ceklis = $item['ceklis'];

                // Tentukan href berdasarkan status
                if ($status === 'belum') {
                    $href = route('ceklis.buat', $area->id);
                } elseif ($status === 'proses') {
                    $href = route('ceklis.isi-after', $ceklis->id);
                } else {
                    $href = route('ceklis.detail', $ceklis->id);
                }

                $labelStatus = match($status) {
                    'selesai' => 'Selesai ✅',
                    'proses'  => 'Lanjut After →',
                    default   => 'Mulai →',
                };
            @endphp

            <a href="{{ $href }}" class="area-card">
                <div class="icon-area">
                    <i class="bi {{ $status === 'selesai' ? 'bi-check2-circle' : ($status === 'proses' ? 'bi-camera' : 'bi-door-open') }}"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="fw-semibold text-dark" style="font-size:0.92rem;">{{ $area->nama_ruangan }}</div>
                    <div class="text-secondary" style="font-size:0.77rem;">Lantai {{ $area->lantai }}</div>
                </div>
                <span class="badge-status badge-{{ $status }}">{{ $labelStatus }}</span>
            </a>
        @empty
            <div class="text-center py-5 text-secondary">
                <i class="bi bi-inbox fs-1"></i>
                <p class="mt-2">Belum ada area terdaftar.</p>
            </div>
        @endforelse
        </div>

    </div>
</div>
@endsection
