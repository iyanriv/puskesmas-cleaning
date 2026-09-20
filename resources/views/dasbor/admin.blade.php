@extends('tata-letak.aplikasi')

@section('content')
<style>
    /* ── Hero Banner ── */
    .admin-hero {
        background: linear-gradient(135deg, #064e2b 0%, #086838 55%, #0f9353 100%);
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        color: white;
    }
    .admin-hero::before {
        content: '';
        position: absolute;
        width: 320px;
        height: 320px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%);
        top: -100px;
        right: -60px;
        pointer-events: none;
    }
    .admin-hero::after {
        content: '';
        position: absolute;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, rgba(255,255,255,0) 70%);
        bottom: -60px;
        left: 30%;
        pointer-events: none;
    }

    /* ── KPI Metric Cards ── */
    .kpi-stat-card {
        background: white;
        border-radius: 20px;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        padding: 1.25rem 1.25rem;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
    }
    .kpi-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    }
    .kpi-icon-bubble {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    /* ── Quick Action Hub ── */
    .action-hub-card {
        background: white;
        border: 1.5px solid #edf2f7;
        border-radius: 18px;
        padding: 1.2rem 1rem;
        text-align: center;
        text-decoration: none;
        color: #1a202c;
        transition: all 0.22s ease-in-out;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
    .action-hub-card:hover {
        background: #f0fdf4;
        border-color: #10b981;
        color: #064e2b;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(16,185,129,0.12);
    }
    .action-hub-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: #f0fdf4;
        color: #059669;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }
    .action-hub-card:hover .action-hub-icon {
        background: #059669;
        color: white;
        transform: scale(1.08);
    }

    /* ── Badges & Progress ── */
    .progress-pill {
        height: 7px;
        border-radius: 10px;
        background: #e2e8f0;
        overflow: hidden;
    }
    .badge-pill-status {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
    }

    /* ── Chart Container Styling ── */
    .chart-panel-card {
        background: white;
        border-radius: 22px;
        border: 1px solid rgba(0,0,0,0.04);
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        padding: 1.5rem;
        height: 100%;
    }
</style>

<div class="container-fluid py-3 px-lg-4">

    {{-- ================================================================
         1. HERO BANNER — EXECUTIVE OVERVIEW
         ================================================================ --}}
    <div class="admin-hero mb-4 p-4 p-lg-5 shadow-sm">
        <div class="position-relative z-1">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="font-size: 0.8rem; background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(6px);">
                        <i class="bi bi-shield-check"></i> Administrator Portal &mdash; Puskesmas Cempaka Putih
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Dasbor Kontrol Utama</h2>
                    <p class="mb-0 text-white text-opacity-85" style="font-size: 0.95rem;">
                        Pantau integritas kebersihan seluruh ruangan, logistik barang, pergerakan tugas CS, dan kepatuhan fasilitas.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <div class="px-3 py-2 rounded-3 text-white d-flex align-items-center gap-2" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(6px); font-size: 0.85rem;">
                        <i class="bi bi-calendar-event"></i>
                        <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
                    </div>
                    <a href="{{ route('stok-barang.index') }}" class="btn btn-light text-success fw-bold rounded-3 shadow-sm px-3 py-2">
                        <i class="bi bi-clipboard2-data me-1"></i> Laporan Stok Barang
                    </a>
                    <a href="{{ route('laporan.index') }}" class="btn btn-outline-light fw-bold rounded-3 px-3 py-2">
                        <i class="bi bi-graph-up me-1"></i> Laporan Terpadu
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         2. ENHANCED KPI STAT METRICS (6 KARTU INFORMATIF)
         ================================================================ --}}
    <div class="row g-3 mb-4">
        {{-- KPI 1: Kepatuhan Ceklis Hari Ini --}}
        <div class="col-sm-6 col-xl-4 col-xxl-2">
            <div class="kpi-stat-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="font-size:0.75rem;">Kepatuhan Area</span>
                    <div class="kpi-icon-bubble" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline gap-2 mb-1">
                    <div class="fs-3 fw-bold text-dark">{{ $persenKebersihan }}%</div>
                    <span class="small text-muted">({{ $areaBersihCount }}/{{ $totalArea }} Area)</span>
                </div>
                <div class="progress-pill mb-2">
                    <div class="bg-success h-100" style="width: {{ $persenKebersihan }}%;"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small" style="font-size:0.75rem;">
                    <span>Bersih: <strong>{{ $areaBersihCount }}</strong></span>
                    <span>Sisa: <strong>{{ $areaBelumBersih }}</strong></span>
                </div>
                <a href="{{ route('laporan.index') }}" class="stretched-link"></a>
            </div>
        </div>

        {{-- KPI 2: Total Ceklis Hari Ini --}}
        <div class="col-sm-6 col-xl-4 col-xxl-2">
            <div class="kpi-stat-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="font-size:0.75rem;">Ceklis Hari Ini</span>
                    <div class="kpi-icon-bubble" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                        <i class="bi bi-card-checklist"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ number_format($totalCeklisHariIni) }}</div>
                <div class="d-flex align-items-center small text-muted">
                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                    <span><strong>{{ $ceklisSelesaiHariIni }}</strong> telah selesai diverifikasi</span>
                </div>
                <a href="{{ route('laporan.index') }}" class="stretched-link"></a>
            </div>
        </div>

        {{-- KPI 3: Stok Kritis / Menipis --}}
        <div class="col-sm-6 col-xl-4 col-xxl-2">
            <div class="kpi-stat-card h-100 {{ $totalStokKritis > 0 ? 'border-start border-4 border-danger' : '' }}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="font-size:0.75rem;">Stok Kritis</span>
                    <div class="kpi-icon-bubble" style="background: rgba(239, 68, 68, 0.12); color: #dc2626;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold {{ $totalStokKritis > 0 ? 'text-danger' : 'text-dark' }} mb-1">
                    {{ number_format($totalStokKritis) }}
                </div>
                <div class="small {{ $totalStokKritis > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">
                    <i class="bi {{ $totalStokKritis > 0 ? 'bi-bell-fill' : 'bi-shield-check text-success' }} me-1"></i>
                    <span>{{ $totalStokKritis > 0 ? 'Perlu restock segera' : 'Stok dalam batas aman' }}</span>
                </div>
                <a href="{{ route('admin.barang.index') }}" class="stretched-link"></a>
            </div>
        </div>

        {{-- KPI 4: Permintaan Barang Pending --}}
        <div class="col-sm-6 col-xl-4 col-xxl-2">
            <div class="kpi-stat-card h-100 {{ $permintaanPending > 0 ? 'border-start border-4 border-warning' : '' }}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="font-size:0.75rem;">Permintaan Pending</span>
                    <div class="kpi-icon-bubble" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold {{ $permintaanPending > 0 ? 'text-warning' : 'text-dark' }} mb-1">
                    {{ number_format($permintaanPending) }}
                </div>
                <div class="small {{ $permintaanPending > 0 ? 'text-warning fw-semibold' : 'text-muted' }}">
                    <i class="bi {{ $permintaanPending > 0 ? 'bi-exclamation-circle-fill' : 'bi-check2-all text-success' }} me-1"></i>
                    <span>{{ $permintaanPending > 0 ? 'Menunggu persetujuan' : 'Tidak ada antrean' }}</span>
                </div>
                <a href="{{ route('barang.gudang') }}" class="stretched-link"></a>
            </div>
        </div>

        {{-- KPI 5: Master Barang & Fisik --}}
        <div class="col-sm-6 col-xl-4 col-xxl-2">
            <div class="kpi-stat-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="font-size:0.75rem;">Inventori Barang</span>
                    <div class="kpi-icon-bubble" style="background: rgba(6, 182, 212, 0.12); color: #0891b2;">
                        <i class="bi bi-box-seam"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ number_format($totalBarang) }}</div>
                <div class="d-flex align-items-center small text-muted">
                    <i class="bi bi-layers me-1"></i>
                    <span>{{ number_format($totalStokFisik) }} unit fisik tersimpan</span>
                </div>
                <a href="{{ route('stok-barang.index') }}" class="stretched-link"></a>
            </div>
        </div>

        {{-- KPI 6: Total Pengguna & Tenaga CS --}}
        <div class="col-sm-6 col-xl-4 col-xxl-2">
            <div class="kpi-stat-card h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="font-size:0.75rem;">Pengguna Sistem</span>
                    <div class="kpi-icon-bubble" style="background: rgba(139, 92, 246, 0.12); color: #7c3aed;">
                        <i class="bi bi-people"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ number_format($totalPengguna) }}</div>
                <div class="d-flex align-items-center small text-muted">
                    <i class="bi bi-person-badge text-success me-1"></i>
                    <span><strong>{{ $totalCS }}</strong> petugas Cleaning Service</span>
                </div>
                <a href="{{ route('admin.pengguna.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    {{-- ================================================================
         3. SECTION DIAGRAM & ANALITIK VISUAL (3 CHARTS)
         ================================================================ --}}
    <div class="row g-4 mb-4">
        {{-- Diagram 1: Tren Ceklis Kebersihan 7 Hari Terakhir (Area Spline Chart) --}}
        <div class="col-xl-8">
            <div class="chart-panel-card">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-graph-up-arrow text-success"></i> Tren Ceklis Kebersihan 7 Hari Terakhir
                        </h5>
                        <p class="text-muted small mb-0">Perbandingan jumlah ceklis kebersihan harian yang diselesaikan petugas</p>
                    </div>
                    <div class="d-flex align-items-center gap-3" style="font-size:0.78rem;">
                        <div class="d-flex align-items-center gap-1">
                            <span class="d-inline-block rounded-circle" style="width:10px; height:10px; background:#10B981;"></span>
                            <span class="text-secondary">Selesai</span>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <span class="d-inline-block rounded-circle" style="width:10px; height:10px; background:#94a3b8;"></span>
                            <span class="text-secondary">Total Masuk</span>
                        </div>
                    </div>
                </div>
                <div style="height: 270px; position: relative;">
                    <canvas id="chartTrenCeklis"></canvas>
                </div>
            </div>
        </div>

        {{-- Diagram 2: Status Kesiapan Area Hari Ini (Donut Chart) --}}
        <div class="col-xl-4">
            <div class="chart-panel-card d-flex flex-column justify-content-between">
                <div>
                    <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-pie-chart-fill text-primary"></i> Kebersihan Ruangan
                    </h5>
                    <p class="text-muted small mb-3">Proporsi area puskesmas hari ini</p>
                </div>
                <div style="height: 200px; position: relative;" class="d-flex justify-content-center align-items-center">
                    <canvas id="chartStatusArea"></canvas>
                </div>
                <div class="mt-3 pt-2 border-top d-flex justify-content-around text-center" style="font-size:0.8rem;">
                    <div>
                        <div class="fw-bold text-success fs-5">{{ $areaBersihCount }}</div>
                        <div class="text-muted small">Area Bersih</div>
                    </div>
                    <div class="border-end"></div>
                    <div>
                        <div class="fw-bold text-secondary fs-5">{{ $areaBelumBersih }}</div>
                        <div class="text-muted small">Belum Selesai</div>
                    </div>
                    <div class="border-end"></div>
                    <div>
                        <div class="fw-bold text-dark fs-5">{{ $totalArea }}</div>
                        <div class="text-muted small">Total Area</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Diagram 3: Top 5 Barang Paling Banyak Diminta / Dipakai (Bar Chart) --}}
        <div class="col-12">
            <div class="chart-panel-card">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-bar-chart-fill text-warning"></i> 5 Barang Terbanyak Diminta / Dipakai
                        </h5>
                        <p class="text-muted small mb-0">Barang logistik kebersihan dengan volume permintaan atau pemakaian tertinggi</p>
                    </div>
                    <a href="{{ route('stok-barang.index') }}" class="btn btn-outline-success btn-sm rounded-pill px-3">
                        Lihat Laporan Stok Lengkap <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                @if(empty($labelTopBarang) || count($labelTopBarang) === 0)
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-box fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Belum ada riwayat permintaan atau pemakaian barang.
                    </div>
                @else
                    <div style="height: 200px; position: relative;">
                        <canvas id="chartTopBarangAdmin"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ================================================================
         4. FEED OPERASIONAL & PERINGATAN LOGISTIK
         ================================================================ --}}
    <div class="row g-4 mb-4">
        {{-- Widget Kiri: Ceklis Aktivitas Terkini Hari Ini --}}
        <div class="col-lg-7">
            <div class="chart-panel-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-primary"></i> Aktivitas Ceklis Terkini
                    </h5>
                    <a href="{{ route('laporan.index') }}" class="text-success text-decoration-none fw-semibold small">
                        Semua Laporan <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                @if($ceklisTerbaru->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-clipboard-x fs-2 d-block mb-2 opacity-50"></i>
                        Belum ada aktivitas ceklis kebersihan yang dicatat hari ini.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:0.87rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Petugas CS</th>
                                    <th>Area / Ruangan</th>
                                    <th>Waktu</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ceklisTerbaru as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold" style="width:32px; height:32px; font-size:0.8rem;">
                                                    {{ strtoupper(substr($item->pengguna->name ?? 'C', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $item->pengguna->name ?? 'Petugas CS' }}</div>
                                                    <div class="text-muted" style="font-size:0.75rem;">NIK: {{ $item->pengguna->nik ?? '-' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-semibold text-dark">{{ $item->area->lantai ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="text-secondary small">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $item->waktu_mulai ? \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') : '-' }} WIB
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->status === 'selesai')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1">
                                                    <i class="bi bi-check2-circle me-1"></i> Selesai
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1">
                                                    <i class="bi bi-hourglass-split me-1"></i> {{ ucfirst($item->status) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Widget Kanan: Peringatan Stok Kritis --}}
        <div class="col-lg-5">
            <div class="chart-panel-card">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-shield-exclamation text-danger"></i> Peringatan Stok Menipis
                    </h5>
                    <a href="{{ route('admin.barang.index') }}" class="text-danger text-decoration-none fw-semibold small">
                        Kelola Barang <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                @if($stokKritisList->isEmpty())
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-check-circle fs-2 d-block mb-2 text-success opacity-75"></i>
                        Seluruh stok barang berada di atas batas minimum.
                    </div>
                @else
                    <div class="d-flex flex-column gap-2">
                        @foreach($stokKritisList as $brg)
                            <div class="p-2 px-3 rounded-3 border d-flex align-items-center justify-content-between bg-light bg-opacity-50">
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size:0.87rem;">{{ $brg->nama_barang }}</div>
                                    <div class="text-muted small">Batas Min: {{ $brg->stok_minimum }} {{ $brg->satuan }}</div>
                                </div>
                                <div class="text-end">
                                    <span class="badge {{ $brg->stok_saat_ini == 0 ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill px-2 py-1">
                                        {{ $brg->stok_saat_ini }} {{ $brg->satuan }}
                                    </span>
                                    <div class="text-muted small mt-1" style="font-size:0.72rem;">
                                        {{ $brg->stok_saat_ini == 0 ? 'Habis (0)' : 'Menipis' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ================================================================
         5. JALAN PINTAS MANAJEMEN (MODERN QUICK ACTION HUB)
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-grid-fill text-success me-2"></i>Jalan Pintas Manajemen</h5>
            <p class="text-muted small mb-0">Akses cepat ke modul administrasi, master data, dan pelaporan</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.pengguna.index') }}" class="action-hub-card">
                        <div class="action-hub-icon"><i class="bi bi-people"></i></div>
                        <div class="fw-bold" style="font-size: 0.88rem;">Kelola Pengguna</div>
                        <div class="text-muted small">Akun CS, Spv & Staf</div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.area.index') }}" class="action-hub-card">
                        <div class="action-hub-icon"><i class="bi bi-building"></i></div>
                        <div class="fw-bold" style="font-size: 0.88rem;">Kelola Area</div>
                        <div class="text-muted small">Ruangan & Lantai</div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.barang.index') }}" class="action-hub-card">
                        <div class="action-hub-icon"><i class="bi bi-box"></i></div>
                        <div class="fw-bold" style="font-size: 0.88rem;">Kelola Barang</div>
                        <div class="text-muted small">Master Alat & Bahan</div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('stok-barang.index') }}" class="action-hub-card">
                        <div class="action-hub-icon"><i class="bi bi-clipboard2-data"></i></div>
                        <div class="fw-bold" style="font-size: 0.88rem;">Laporan Stok</div>
                        <div class="text-muted small">Pemakaian & Sisa</div>
                    </a>
                </div>

                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('admin.penilaian-pj.index') }}" class="action-hub-card">
                        <div class="action-hub-icon" style="background:#ecfdf5; color:#059669;"><i class="bi bi-clipboard-check"></i></div>
                        <div class="fw-bold" style="font-size: 0.88rem;">Penilaian PJ Lantai</div>
                        <div class="text-muted small">Respon Kuesioner Ruangan</div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('tugas-mingguan.index') }}" class="action-hub-card">
                        <div class="action-hub-icon" style="background:#dbeafe; color:#2563eb;"><i class="bi bi-calendar-check"></i></div>
                        <div class="fw-bold" style="font-size: 0.88rem;">Tugas Mingguan CS</div>
                        <div class="text-muted small">Monitoring Kegiatan</div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('sampah.rekapan') }}" class="action-hub-card">
                        <div class="action-hub-icon" style="background:#d1fae5; color:#059669;"><i class="bi bi-recycle"></i></div>
                        <div class="fw-bold" style="font-size: 0.88rem;">Bank Sampah</div>
                        <div class="text-muted small">Setoran & Validasi</div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('operan.index') }}" class="action-hub-card">
                        <div class="action-hub-icon" style="background:#fef3c7; color:#d97706;"><i class="bi bi-arrow-left-right"></i></div>
                        <div class="fw-bold" style="font-size: 0.88rem;">Operan Shift</div>
                        <div class="text-muted small">Serah Terima Tugas</div>
                    </a>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="{{ route('laporan.index') }}" class="action-hub-card">
                        <div class="action-hub-icon"><i class="bi bi-graph-up"></i></div>
                        <div class="fw-bold" style="font-size: 0.88rem;">Laporan Terpadu</div>
                        <div class="text-muted small">Analitik & PDF/Excel</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- ================================================================
     6. CHART.JS SCRIPT INITIALIZATION
     ================================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ── 1. CHART TREN CEKLIS 7 HARI TERAKHIR (SPLINE AREA) ──
    const ctxTren = document.getElementById('chartTrenCeklis');
    if (ctxTren) {
        new Chart(ctxTren, {
            type: 'line',
            data: {
                labels: {!! json_encode($labelHari) !!},
                datasets: [
                    {
                        label: 'Ceklis Selesai',
                        data: {!! json_encode($dataCeklisSelesai) !!},
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Total Masuk',
                        data: {!! json_encode($dataCeklisTotal) !!},
                        borderColor: '#94a3b8',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        fill: false,
                        tension: 0.35,
                        pointBackgroundColor: '#94a3b8',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            precision: 0,
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    // ── 2. CHART STATUS KESIAPAN AREA HARI INI (DOUGHNUT) ──
    const ctxArea = document.getElementById('chartStatusArea');
    if (ctxArea) {
        new Chart(ctxArea, {
            type: 'doughnut',
            data: {
                labels: ['Area Bersih Selesai', 'Belum Selesai'],
                datasets: [{
                    data: [{{ $areaBersihCount }}, {{ $areaBelumBersih }}],
                    backgroundColor: ['#10B981', '#e2e8f0'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = {{ $totalArea > 0 ? $totalArea : 1 }};
                                const val = context.parsed || 0;
                                const pct = Math.round((val / total) * 100);
                                return ` ${context.label}: ${val} area (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // ── 3. CHART TOP 5 BARANG SERING DIMINTA (HORIZONTAL BAR) ──
    const ctxTop = document.getElementById('chartTopBarangAdmin');
    if (ctxTop) {
        new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: {!! json_encode($labelTopBarang) !!},
                datasets: [{
                    label: 'Jumlah Diminta',
                    data: {!! json_encode($dataTopBarang) !!},
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    hoverBackgroundColor: '#059669',
                    borderRadius: 8,
                    barPercentage: 0.55
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { precision: 0, font: { size: 11 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    }
});
</script>
@endsection

