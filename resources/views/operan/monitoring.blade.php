@extends('tata-letak.aplikasi')

@section('title', 'Monitoring Operan Shift')

@section('content')

<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid #10b981;
    }
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }
    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .filter-tabs {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }
    .filter-tab {
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        background: white;
        border: 2px solid #e5e7eb;
        color: #6b7280;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s;
    }
    .filter-tab:hover {
        border-color: #10b981;
        color: #10b981;
    }
    .filter-tab.active {
        background: #10b981;
        border-color: #10b981;
        color: white;
    }
    .operan-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-left: 4px solid #d1d5db;
    }
    .operan-card.menunggu {
        border-left-color: #f59e0b;
    }
    .operan-card.selesai {
        border-left-color: #10b981;
    }
    .operan-card.dieskalasi {
        border-left-color: #ef4444;
    }
    .badge-status {
        display: inline-block;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-menunggu {
        background: #fef3c7;
        color: #d97706;
    }
    .badge-diterima {
        background: #dbeafe;
        color: #2563eb;
    }
    .badge-selesai {
        background: #d1fae5;
        color: #059669;
    }
    .badge-dieskalasi {
        background: #fee2e2;
        color: #dc2626;
    }
    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
    }
</style>

<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Monitoring Operan Shift</h4>
            <p class="text-muted mb-0">Pantau serah terima tugas antar shift</p>
        </div>
        <div>
            <a href="{{ route('operan.cetak-laporan', ['periode' => $periode]) }}" 
               target="_blank"
               class="btn btn-outline-success">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
        </div>
    </div>

    {{-- Statistik Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number">{{ $totalOperan }}</div>
                <div class="stat-label">Total Operan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #f59e0b;">
                <div class="stat-number">{{ $menungguKonfirmasi }}</div>
                <div class="stat-label">Menunggu Konfirmasi</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #10b981;">
                <div class="stat-number">{{ $tugasSelesai }}</div>
                <div class="stat-label">Tugas Selesai</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="border-left-color: #ef4444;">
                <div class="stat-number">{{ $tugasDieskalasi }}</div>
                <div class="stat-label">Dieskalasi</div>
            </div>
        </div>
    </div>

    {{-- Filter Periode --}}
    <div class="filter-tabs">
        <a href="{{ route('operan.index', ['periode' => 'hari_ini']) }}" 
           class="filter-tab {{ $periode === 'hari_ini' ? 'active' : '' }}">
            Hari Ini
        </a>
        <a href="{{ route('operan.index', ['periode' => 'kemarin']) }}" 
           class="filter-tab {{ $periode === 'kemarin' ? 'active' : '' }}">
            Kemarin
        </a>
        <a href="{{ route('operan.index', ['periode' => 'minggu_ini']) }}" 
           class="filter-tab {{ $periode === 'minggu_ini' ? 'active' : '' }}">
            Minggu Ini
        </a>
        <a href="{{ route('operan.index', ['periode' => 'bulan_ini']) }}" 
           class="filter-tab {{ $periode === 'bulan_ini' ? 'active' : '' }}">
            Bulan Ini
        </a>
    </div>

    {{-- Periode Info --}}
    <div class="alert alert-info mb-4">
        <i class="bi bi-calendar3"></i>
        Menampilkan data periode: <strong>{{ \Carbon\Carbon::parse($dari)->format('d M Y') }}</strong> 
        sampai <strong>{{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</strong>
    </div>

    {{-- Daftar Operan --}}
    @forelse($operanList as $operan)
        <div class="operan-card {{ $operan->status_terima === 'menunggu' ? 'menunggu' : ($operan->status_penyelesaian === 'selesai' ? 'selesai' : ($operan->status_penyelesaian === 'dieskalasi' ? 'dieskalasi' : '')) }}">
            <div class="row align-items-start">
                {{-- Info Pengirim --}}
                <div class="col-md-3">
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1">Pengirim</small>
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ strtoupper(substr($operan->pengirim->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $operan->pengirim->name ?? 'Tidak Diketahui' }}</div>
                                <small class="text-muted">{{ $operan->pengirim->peran->nama_peran ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Arrow --}}
                <div class="col-md-1 text-center">
                    <i class="bi bi-arrow-right" style="font-size: 1.5rem; color: #9ca3af;"></i>
                </div>

                {{-- Info Penerima --}}
                <div class="col-md-3">
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1">Penerima</small>
                        <div class="user-info">
                            <div class="user-avatar" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                                {{ strtoupper(substr($operan->penerima->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-bold">{{ $operan->penerima->name ?? 'Tidak Diketahui' }}</div>
                                <small class="text-muted">{{ $operan->penerima->peran->nama_peran ?? '-' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Detail Operan --}}
                <div class="col-md-5">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <small class="text-muted">Tanggal & Waktu</small>
                            <div class="fw-bold">
                                {{ \Carbon\Carbon::parse($operan->tanggal)->format('d M Y') }} • 
                                {{ \Carbon\Carbon::parse($operan->waktu)->format('H:i') }}
                            </div>
                        </div>
                        <div class="text-end">
                            @if($operan->status_terima === 'menunggu')
                                <span class="badge-status badge-menunggu">Menunggu</span>
                            @elseif($operan->status_penyelesaian === 'selesai')
                                <span class="badge-status badge-selesai">Selesai</span>
                            @elseif($operan->status_penyelesaian === 'dieskalasi')
                                <span class="badge-status badge-dieskalasi">Dieskalasi</span>
                            @else
                                <span class="badge-status badge-diterima">Diterima</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-2">
                        <small class="text-muted">Tempat & Waktu Jaga</small>
                        <div><strong>{{ $operan->tempat_tugas }}</strong> • {{ $operan->waktu_jaga }}</div>
                    </div>

                    @if($operan->tugas_items && count($operan->tugas_items) > 0)
                        <div class="mb-2">
                            <small class="text-muted">Tugas yang Perlu Diselesaikan</small>
                            <ul class="mb-0 ps-3" style="font-size: 0.875rem;">
                                @foreach($operan->tugas_items as $tugas)
                                    <li>{{ $tugas }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($operan->catatan)
                        <div class="mb-2">
                            <small class="text-muted">Catatan</small>
                            <div style="font-size: 0.875rem;">{{ $operan->catatan }}</div>
                        </div>
                    @endif

                    @if($operan->parentOperan)
                        <div class="alert alert-warning py-2 px-3 mb-2" style="font-size: 0.8rem;">
                            <i class="bi bi-info-circle"></i> Eskalasi dari operan sebelumnya 
                            (Pengirim awal: {{ $operan->parentOperan->pengirim->name ?? '-' }})
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('operan.detail', $operan->id) }}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='1' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M9 11l3 3L22 4'/%3E%3Cpath d='M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11'/%3E%3C/svg%3E" 
                 alt="No Data" style="opacity: 0.5;" width="100">
            <h5 class="fw-bold text-dark mt-3 mb-2">Belum Ada Data Operan</h5>
            <p class="text-muted">Tidak ada operan shift pada periode ini.</p>
        </div>
    @endforelse

    {{-- Pagination --}}
    @if($operanList->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $operanList->links() }}
        </div>
    @endif
</div>

@endsection
