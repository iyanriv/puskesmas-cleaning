@extends('tata-letak.aplikasi')

@section('content')
<style>
    .spv-hero {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border-radius: 20px;
        color: white;
        padding: 2rem 2.5rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
    }
    .spv-hero::after {
        content: '';
        position: absolute;
        width: 260px; height: 260px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
        top: -80px; right: -60px;
    }
    .spv-stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        border-left: 4px solid #0d6efd;
        height: 100%;
    }
    .spv-stat-card .angka {
        font-size: 2.25rem;
        font-weight: 800;
        color: #1f2937;
        line-height: 1;
    }
    .spv-stat-card .label {
        font-size: 0.78rem;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        margin-top: 0.25rem;
    }
    .spv-panel {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .spv-panel-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        font-weight: 700;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .spv-quick-action {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.9rem 1.25rem;
        border-radius: 12px;
        background: #f9fafb;
        border: 1.5px solid #e5e7eb;
        text-decoration: none;
        color: #1f2937;
        transition: all 0.2s;
        margin-bottom: 0.75rem;
    }
    .spv-quick-action:hover {
        background: #eff6ff;
        border-color: #3b82f6;
        color: #1d4ed8;
    }
    .spv-quick-action .icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
</style>

<div class="container-fluid py-3 px-lg-4">

    {{-- Hero Banner --}}
    <div class="spv-hero">
        <div class="position-relative" style="z-index:1;">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2"
                 style="background:rgba(255,255,255,0.18); font-size:0.8rem; border:1px solid rgba(255,255,255,0.3);">
                <i class="bi bi-eye"></i> Portal Supervisor
            </div>
            <h2 class="fw-bold mb-1">Dasbor Supervisor</h2>
            <p class="mb-0 text-white" style="opacity:0.85; font-size:0.9rem;">
                Monitoring kebersihan, tugas petugas, dan kinerja harian — Puskesmas Cempaka Putih
            </p>
            <div class="mt-2" style="font-size:0.85rem; opacity:0.8;">
                <i class="bi bi-calendar3 me-1"></i>
                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
            </div>
        </div>
    </div>

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-3">
            <div class="spv-stat-card">
                <div class="angka text-success">{{ $areaBersih }}</div>
                <div class="label">Area Bersih Hari Ini</div>
                <div class="text-muted small mt-1">dari {{ $totalArea }} total area</div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="spv-stat-card" style="border-left-color:#10b981;">
                <div class="angka" style="color:#10b981;">
                    {{ $totalArea > 0 ? round(($areaBersih / $totalArea) * 100) : 0 }}%
                </div>
                <div class="label">Tingkat Kebersihan</div>
                <div class="mt-2" style="height:6px; background:#e5e7eb; border-radius:4px;">
                    <div style="height:100%; width:{{ $totalArea > 0 ? round(($areaBersih/$totalArea)*100) : 0 }}%; background:#10b981; border-radius:4px;"></div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="spv-stat-card" style="border-left-color:#f59e0b;">
                <div class="angka" style="color:#f59e0b;">{{ $ceklisBaru->count() }}</div>
                <div class="label">Ceklis Masuk Hari Ini</div>
                <div class="text-muted small mt-1">
                    {{ $ceklisBaru->where('status','selesai')->count() }} selesai
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-3">
            <div class="spv-stat-card" style="border-left-color:#8b5cf6;">
                <div class="angka" style="color:#8b5cf6;">{{ $setoranTerbaru->count() }}</div>
                <div class="label">Setoran Sampah Terbaru</div>
                <div class="text-muted small mt-1">
                    {{ $setoranTerbaru->where('status_validasi','menunggu')->count() }} menunggu validasi
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kiri: Ceklis + Setoran --}}
        <div class="col-lg-8">

            {{-- Ceklis Hari Ini --}}
            <div class="spv-panel mb-4">
                <div class="spv-panel-header">
                    <span><i class="bi bi-clipboard-check text-primary me-2"></i>Ceklis Masuk Hari Ini</span>
                    <a href="{{ route('laporan.index') }}" class="btn btn-sm btn-outline-primary rounded-pill" style="font-size:0.75rem;">
                        Lihat Semua
                    </a>
                </div>
                <div>
                    @forelse($ceklisBaru as $ceklis)
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                     style="width:36px;height:36px;background:#0d6efd;font-size:0.85rem;flex-shrink:0;">
                                    {{ strtoupper(substr($ceklis->pengguna->name ?? 'C', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:0.9rem;">{{ $ceklis->pengguna->name ?? '-' }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        <i class="bi bi-building me-1"></i>{{ $ceklis->area->lantai ?? '-' }}
                                        @if($ceklis->waktu_mulai)
                                            &nbsp;•&nbsp;<i class="bi bi-clock me-1"></i>
                                            {{ \Carbon\Carbon::parse($ceklis->waktu_mulai)->format('H:i') }} WIB
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <span class="badge rounded-pill {{ $ceklis->status === 'selesai' ? 'bg-success' : 'bg-warning text-dark' }}"
                                  style="font-size:0.72rem;">
                                {{ $ceklis->status === 'selesai' ? '✓ Selesai' : ucfirst($ceklis->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clipboard-x fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada ceklis masuk hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Setoran Sampah --}}
            <div class="spv-panel">
                <div class="spv-panel-header">
                    <span><i class="bi bi-recycle text-success me-2"></i>Setoran Sampah Terbaru</span>
                    <a href="{{ route('sampah.rekapan') }}" class="btn btn-sm btn-outline-success rounded-pill" style="font-size:0.75rem;">
                        Lihat Rekap
                    </a>
                </div>
                <div>
                    @forelse($setoranTerbaru as $setoran)
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                            <div>
                                <div class="fw-semibold" style="font-size:0.9rem;">{{ $setoran->pengguna->name ?? '-' }}</div>
                                <div class="text-muted" style="font-size:0.75rem;">
                                    {{ $setoran->jenisSampahTeks() }} &nbsp;•&nbsp;
                                    {{ $setoran->tanggal->translatedFormat('d M Y') }}
                                </div>
                            </div>
                            @php $sv = $setoran->status_validasi ?? 'menunggu'; @endphp
                            <span class="badge rounded-pill
                                {{ $sv === 'valid' ? 'bg-success' : ($sv === 'ditolak' ? 'bg-danger' : 'bg-warning text-dark') }}"
                                style="font-size:0.72rem;">
                                {{ $sv === 'valid' ? '✓ Valid' : ($sv === 'ditolak' ? '✗ Ditolak' : '⏳ Menunggu') }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada data setoran sampah.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        {{-- Kanan: Aksi Cepat --}}
        <div class="col-lg-4">
            <div class="spv-panel">
                <div class="spv-panel-header">
                    <span><i class="bi bi-grid text-secondary me-2"></i>Aksi Cepat</span>
                </div>
                <div class="p-3">
                    <a href="{{ route('laporan.index') }}" class="spv-quick-action">
                        <div class="icon" style="background:#dbeafe; color:#1d4ed8;">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Laporan Terpadu</div>
                            <div class="text-muted" style="font-size:0.76rem;">Ceklis, Sampah, Kinerja</div>
                        </div>
                    </a>
                    <a href="{{ route('tugas-mingguan.index') }}" class="spv-quick-action">
                        <div class="icon" style="background:#d1fae5; color:#059669;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Tugas Mingguan CS</div>
                            <div class="text-muted" style="font-size:0.76rem;">Verifikasi & monitoring</div>
                        </div>
                    </a>
                    <a href="{{ route('sampah.rekapan') }}" class="spv-quick-action">
                        <div class="icon" style="background:#d1fae5; color:#059669;">
                            <i class="bi bi-recycle"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Validasi Bank Sampah</div>
                            <div class="text-muted" style="font-size:0.76rem;">Setoran petugas CS</div>
                        </div>
                    </a>
                    <a href="{{ route('operan.index') }}" class="spv-quick-action">
                        <div class="icon" style="background:#fef3c7; color:#d97706;">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Monitoring Operan Shift</div>
                            <div class="text-muted" style="font-size:0.76rem;">Serah terima antar shift</div>
                        </div>
                    </a>
                    <a href="{{ route('profil.ganti-password') }}" class="spv-quick-action">
                        <div class="icon" style="background:#f3f4f6; color:#6b7280;">
                            <i class="bi bi-key"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Ganti Password</div>
                            <div class="text-muted" style="font-size:0.76rem;">Keamanan akun</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
