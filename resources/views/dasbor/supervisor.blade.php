@extends('tata-letak.aplikasi')

@section('content')
<style>
    .spv-hero {
        background: linear-gradient(135deg, #205F63 0%, #267A7E 55%, #31999E 100%);
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
        border-left: 4px solid #31999E;
        height: 100%;
        transition: transform 0.2s;
    }
    .spv-stat-card:hover { transform: translateY(-2px); }
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
        background: #e6f4f5;
        border-color: #31999E;
        color: #205F63;
    }
    .spv-quick-action .icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .alert-badge {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fca5a5;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        font-size: 0.82rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
    }
    .bar-chart-wrap { display: flex; align-items: flex-end; gap: 6px; height: 60px; padding: 0 0.5rem; }
    .bar-chart-wrap .bar-col { display: flex; flex-direction: column; align-items: center; flex: 1; gap: 2px; }
    .bar-chart-wrap .bar { width: 100%; border-radius: 4px 4px 0 0; min-height: 4px; }
    .bar-chart-wrap .bar-label { font-size: 0.65rem; color: #9ca3af; font-weight: 600; }
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

    {{-- Alert jika ada eskalasi, menunggu verifikasi, atau stok menipis --}}
    @if($operanEskalasi > 0 || $tugasMenungguVerifikasi > 0 || ($stokMenipisCount ?? 0) > 0)
        <div class="d-flex flex-wrap gap-2 mb-4">
            @if($tugasMenungguVerifikasi > 0)
                <a href="{{ route('tugas-mingguan.index', ['status' => 'selesai']) }}" class="alert-badge text-decoration-none">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ $tugasMenungguVerifikasi }} tugas menunggu verifikasi Anda
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            @endif
            @if($operanEskalasi > 0)
                <div class="alert-badge" style="background:#fff7ed; color:#c2410c; border-color:#fed7aa;">
                    <i class="bi bi-arrow-repeat"></i>
                    {{ $operanEskalasi }} operan dieskalasi hari ini
                </div>
            @endif
            @if(($stokMenipisCount ?? 0) > 0)
                <a href="{{ route('stok-barang.index') }}" class="alert-badge text-decoration-none" style="background:#fef2f2; color:#b91c1c; border-color:#fecaca;">
                    <i class="bi bi-box-seam-fill"></i>
                    {{ $stokMenipisCount }} barang stok menipis (≤5 unit)
                    <i class="bi bi-arrow-right ms-1"></i>
                </a>
            @endif
        </div>
    @endif

    {{-- KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="spv-stat-card">
                <div class="angka {{ $tugasMenungguVerifikasi > 0 ? 'text-danger' : 'text-secondary' }}">
                    {{ $tugasMenungguVerifikasi }}
                </div>
                <div class="label">Menunggu Verifikasi</div>
                <div class="text-muted small mt-1">Perlu tindakan</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="spv-stat-card" style="border-left-color:#31999E;">
                <div class="angka text-success">{{ $tugasDisetujuiHariIni }}</div>
                <div class="label">Disetujui Hari Ini</div>
                <div class="text-muted small mt-1">dari {{ $totalTugasHariIni }} tugas masuk</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="spv-stat-card" style="border-left-color:#f59e0b;">
                <div class="angka" style="color:#f59e0b;">{{ $tugasProsesHariIni }}</div>
                <div class="label">Masih Dalam Proses</div>
                <div class="text-muted small mt-1">Foto after belum diunggah</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="spv-stat-card" style="border-left-color:#8b5cf6;">
                <div class="angka" style="color:#8b5cf6;">{{ $setoranMenunggu }}</div>
                <div class="label">Setoran Belum Divalidasi</div>
                <div class="text-muted small mt-1">Bank sampah CS</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kiri: Tugas Mingguan + Setoran --}}
        <div class="col-lg-8">

            {{-- Tren 7 Hari + Tugas Hari Ini --}}
            <div class="spv-panel mb-4">
                <div class="spv-panel-header">
                    <span><i class="bi bi-calendar-check text-success me-2"></i>Tugas Mingguan CS — Hari Ini</span>
                    <a href="{{ route('tugas-mingguan.index') }}" class="btn btn-sm btn-outline-success rounded-pill" style="font-size:0.75rem;">
                        Lihat Semua
                    </a>
                </div>

                {{-- Mini bar chart tren 7 hari --}}
                <div class="px-4 pt-3 pb-1">
                    <div class="d-flex justify-content-between align-items-end mb-1">
                        <div class="text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">Tren 7 Hari Terakhir</div>
                    </div>
                    <div class="bar-chart-wrap">
                        @php $maxBar = collect($tren)->max('total') ?: 1; @endphp
                        @foreach($tren as $t)
                            <div class="bar-col">
                                <div class="bar bg-success bg-opacity-25"
                                     style="height:{{ max(4, ($t['total']/$maxBar)*48) }}px;"></div>
                                <div class="bar-label">{{ $t['tanggal'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Daftar tugas hari ini --}}
                <div>
                    @forelse($tugasTerbaru as $tugas)
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                     style="width:36px;height:36px;background:#31999E;font-size:0.85rem;flex-shrink:0;">
                                    {{ strtoupper(substr($tugas->user->name ?? 'C', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size:0.9rem;">{{ $tugas->user->name ?? '-' }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">
                                        <i class="bi bi-clock me-1"></i>{{ $tugas->waktu_pelaporan }}
                                        &nbsp;•&nbsp;
                                        <span title="{{ $tugas->rincian_kegiatan }}">
                                            {{ Str::limit($tugas->rincian_kegiatan, 40) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                {!! $tugas->badgeStatus() !!}
                                @if($tugas->status === 'selesai')
                                    <a href="{{ route('tugas-mingguan.detail', $tugas->id) }}"
                                       class="btn btn-sm btn-success rounded-pill px-3"
                                       style="font-size:0.72rem;">
                                        Verifikasi
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2 opacity-50"></i>
                            Belum ada tugas masuk hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Setoran Sampah Terbaru --}}
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
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill
                                    {{ $sv === 'valid' ? 'bg-success' : ($sv === 'ditolak' ? 'bg-danger' : 'bg-warning text-dark') }}"
                                    style="font-size:0.72rem;">
                                    {{ $sv === 'valid' ? '✓ Valid' : ($sv === 'ditolak' ? '✗ Ditolak' : '⏳ Menunggu') }}
                                </span>
                                @if($sv === 'menunggu')
                                    <a href="{{ route('sampah.rekapan') }}" class="btn btn-sm btn-outline-success rounded-pill px-2" style="font-size:0.7rem;">
                                        Validasi
                                    </a>
                                @endif
                            </div>
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
                    <a href="{{ route('tugas-mingguan.index', ['status' => 'selesai']) }}" class="spv-quick-action">
                        <div class="icon" style="background:#e6f4f5; color:#267A7E;">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Verifikasi Tugas CS</div>
                            <div class="text-muted" style="font-size:0.76rem;">
                                @if($tugasMenungguVerifikasi > 0)
                                    <span class="text-danger fw-semibold">{{ $tugasMenungguVerifikasi }} menunggu</span>
                                @else
                                    Semua sudah diverifikasi ✓
                                @endif
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('operan.index') }}" class="spv-quick-action">
                        <div class="icon" style="background:#fef3c7; color:#d97706;">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Monitoring Operan Shift</div>
                            <div class="text-muted" style="font-size:0.76rem;">Serah terima antar shift
                                @if($operanEskalasi > 0)
                                    &nbsp;<span class="badge bg-danger" style="font-size:0.65rem;">{{ $operanEskalasi }} eskalasi</span>
                                @endif
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('admin.penilaian-pj.index') }}" class="spv-quick-action">
                        <div class="icon" style="background:#ede9fe; color:#7c3aed;">
                            <i class="bi bi-clipboard-check"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Penilaian PJ Lantai</div>
                            <div class="text-muted" style="font-size:0.76rem;">Evaluasi kinerja 16 indikator</div>
                        </div>
                    </a>
                    <a href="{{ route('sampah.rekapan') }}" class="spv-quick-action">
                        <div class="icon" style="background:#e6f4f5; color:#267A7E;">
                            <i class="bi bi-recycle"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Validasi Bank Sampah</div>
                            <div class="text-muted" style="font-size:0.76rem;">
                                @if($setoranMenunggu > 0)
                                    <span class="text-warning fw-semibold">{{ $setoranMenunggu }} belum divalidasi</span>
                                @else
                                    Setoran petugas CS
                                @endif
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('stok-barang.index') }}" class="spv-quick-action">
                        <div class="icon" style="background:#e0f2fe; color:#0284c7;">
                            <i class="bi bi-boxes"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Monitoring Stok Barang</div>
                            <div class="text-muted" style="font-size:0.76rem;">
                                @if(($stokMenipisCount ?? 0) > 0)
                                    <span class="text-danger fw-semibold">{{ $stokMenipisCount }} stok menipis</span>
                                @else
                                    Ketersediaan alat & bahan
                                @endif
                            </div>
                        </div>
                    </a>
                    <a href="{{ route('laporan.index') }}" class="spv-quick-action">
                        <div class="icon" style="background:#fce7f3; color:#be185d;">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.9rem;">Laporan Terpadu</div>
                            <div class="text-muted" style="font-size:0.76rem;">Ekspor PDF & Excel</div>
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
