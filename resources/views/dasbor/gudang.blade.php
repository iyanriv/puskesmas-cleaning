@extends('tata-letak.aplikasi')

@section('content')
<div class="container py-4">

    {{-- ================================================================
         HERO BANNER — PORTAL GUDANG
         ================================================================ --}}
    <div class="gudang-hero mb-4 p-4 p-lg-5 text-white rounded-4 position-relative overflow-hidden shadow-sm">
        <div class="position-relative z-1">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="font-size: 0.8rem; background: rgba(255, 255, 255, 0.18); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); backdrop-filter: blur(4px);">
                        <i class="bi bi-shield-check"></i> Sistem Inventori & Logistik Puskesmas
                    </div>
                    <h2 class="fw-bold mb-1 text-white">Dasbor Logistik & Gudang</h2>
                    <p class="mb-0 text-white text-opacity-85" style="font-size: 0.95rem;">
                        Monitoring ketersediaan barang kebersihan, tren pemakaian unit, dan persetujuan permintaan.
                    </p>
                </div>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    {{-- Selector Periode --}}
                    <form method="GET" action="{{ route('dasbor.gudang') }}" class="d-flex gap-2 align-items-center p-2 rounded-3" style="background: rgba(255, 255, 255, 0.18); border: 1px solid rgba(255, 255, 255, 0.25); backdrop-filter: blur(6px);">
                        <select name="bulan" class="form-select form-select-sm bg-white border-0 fw-semibold text-dark rounded-2" onchange="this.form.submit()" style="min-width: 120px;">
                            @foreach($daftarBulan as $no => $nama)
                                <option value="{{ $no }}" {{ $bulan == $no ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                        <select name="tahun" class="form-select form-select-sm bg-white border-0 fw-semibold text-dark rounded-2" onchange="this.form.submit()" style="width: 85px;">
                            @foreach(range(now()->year - 2, now()->year + 1) as $th)
                                <option value="{{ $th }}" {{ $tahun == $th ? 'selected' : '' }}>{{ $th }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('stok-barang.index') }}" class="btn btn-light text-success fw-bold rounded-3 shadow-sm px-3">
                        <i class="bi bi-clipboard2-data me-1"></i> Laporan Stok
                    </a>
                </div>
            </div>
        </div>
        <div class="hero-decoration-circle"></div>
        <div class="hero-decoration-circle-2"></div>
    </div>

    {{-- ================================================================
         4 KPI METRIC CARDS
         ================================================================ --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Master Barang --}}
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card card border-0 shadow-sm rounded-4 h-100 p-3 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Master Barang</span>
                    <div class="kpi-icon-wrap" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                        <i class="bi bi-boxes fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold text-dark mb-1">{{ number_format($totalBarang) }}</div>
                <div class="d-flex align-items-center text-muted small">
                    <i class="bi bi-check-circle-fill text-success me-1"></i>
                    <span>{{ number_format($totalStokTersedia) }} unit fisik tersimpan</span>
                </div>
                <a href="{{ route('admin.barang.index') }}" class="stretched-link"></a>
            </div>
        </div>

        {{-- Card 2: Permintaan Pending --}}
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card card border-0 shadow-sm rounded-4 h-100 p-3 position-relative overflow-hidden {{ $permintaanPending->count() > 0 ? 'border-start border-5 border-warning shadow-warning' : '' }}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Permintaan Menunggu</span>
                    <div class="kpi-icon-wrap" style="background: {{ $permintaanPending->count() > 0 ? 'rgba(245, 158, 11, 0.15)' : 'rgba(16, 185, 129, 0.12)' }}; color: {{ $permintaanPending->count() > 0 ? '#d97706' : '#059669' }};">
                        <i class="bi {{ $permintaanPending->count() > 0 ? 'bi-hourglass-split' : 'bi-check2-all' }} fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="fs-2 fw-bold {{ $permintaanPending->count() > 0 ? 'text-warning' : 'text-dark' }}">
                        {{ number_format($permintaanPending->count()) }}
                    </div>
                    @if($permintaanPending->count() > 0)
                        <span class="badge bg-warning text-dark rounded-pill px-2 py-1 fs-8 pulse-badge">URGENT</span>
                    @endif
                </div>
                <div class="d-flex align-items-center small {{ $permintaanPending->count() > 0 ? 'text-warning fw-semibold' : 'text-muted' }}">
                    <i class="bi {{ $permintaanPending->count() > 0 ? 'bi-exclamation-circle-fill' : 'bi-check2-all' }} me-1"></i>
                    <span>{{ $permintaanPending->count() > 0 ? 'Perlu respon segera' : 'Semua permintaan diproses' }}</span>
                </div>
                <a href="{{ route('barang.gudang') }}" class="stretched-link"></a>
                @if($permintaanPending->count() > 0)
                    <div class="kpi-alert-dot"></div>
                @endif
            </div>
        </div>

        {{-- Card 3: Stok Kritis / Menipis --}}
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card card border-0 shadow-sm rounded-4 h-100 p-3 position-relative overflow-hidden {{ $stokMenipis->count() > 0 ? 'border-start border-5 border-danger shadow-danger' : '' }}">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Stok Kritis</span>
                    <div class="kpi-icon-wrap" style="background: {{ $stokMenipis->count() > 0 ? 'rgba(239, 68, 68, 0.12)' : 'rgba(16, 185, 129, 0.12)' }}; color: {{ $stokMenipis->count() > 0 ? '#dc2626' : '#059669' }};">
                        <i class="bi {{ $stokMenipis->count() > 0 ? 'bi-exclamation-triangle-fill' : 'bi-shield-check' }} fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <div class="fs-2 fw-bold {{ $stokMenipis->count() > 0 ? 'text-danger' : 'text-dark' }}">
                        {{ number_format($stokMenipis->count()) }}
                    </div>
                    @if($stokHabisCount > 0)
                        <span class="badge bg-danger text-white rounded-pill px-2 py-1 fs-8 pulse-badge">{{ $stokHabisCount }} HABIS</span>
                    @endif
                </div>
                <div class="d-flex align-items-center small {{ $stokHabisCount > 0 ? 'text-danger fw-bold' : ($stokMenipis->count() > 0 ? 'text-warning fw-semibold' : 'text-muted') }}">
                    <i class="bi {{ $stokHabisCount > 0 ? 'bi-x-circle-fill' : ($stokMenipis->count() > 0 ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill') }} me-1"></i>
                    <span>{{ $stokHabisCount > 0 ? 'Segera restok!' : ($stokMenipis->count() > 0 ? 'Mendekati batas minimum' : 'Semua stok aman') }}</span>
                </div>
                <a href="{{ route('admin.barang.index') }}" class="stretched-link"></a>
                @if($stokMenipis->count() > 0)
                    <div class="kpi-alert-dot"></div>
                @endif
            </div>
        </div>

        {{-- Card 4: Total Pemakaian Bulan Berjalan --}}
        <div class="col-sm-6 col-xl-3">
            <div class="kpi-card card border-0 shadow-sm rounded-4 h-100 p-3 position-relative overflow-hidden">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Pemakaian {{ $daftarBulan[$bulan] }}</span>
                    <div class="kpi-icon-wrap" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold text-dark mb-1">{{ number_format($totalPemakaianBulanIni) }}</div>
                <div class="d-flex align-items-center text-muted small">
                    <i class="bi bi-box-arrow-in-down text-primary me-1"></i>
                    <span>Stok masuk: {{ number_format($totalStokMasuk) }} unit</span>
                </div>
                <a href="{{ route('stok-barang.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    {{-- ================================================================
         SECTION GRAFIK & ANALITIK
         ================================================================ --}}
    <div class="row g-4 mb-4">
        {{-- Grafik 1: Tren Aktivitas Permintaan Barang 7 Hari Terakhir --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-activity text-success"></i> Aktivitas Permintaan 7 Hari Terakhir
                        </h5>
                        <p class="text-muted small mb-0">Frekuensi permintaan barang dari petugas cleaning service</p>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill">
                        Real-time
                    </span>
                </div>
                <div class="card-body p-4">
                    <div style="height: 280px; position: relative;">
                        <canvas id="chartTrenPermintaan"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik 2: Distribusi Pemakaian per Unit / Lokasi --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-pie-chart-fill text-primary"></i> Konsumsi per Unit
                        </h5>
                        <p class="text-muted small mb-0">Proporsi pemakaian barang periode {{ $daftarBulan[$bulan] }} {{ $tahun }}</p>
                    </div>
                </div>
                <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center">
                    @if($pemakaianPerUnit->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada data pemakaian tercatat pada periode ini.
                        </div>
                    @else
                        <div style="height: 210px; width: 100%; position: relative;" class="d-flex justify-content-center">
                            <canvas id="chartPemakaianUnit"></canvas>
                        </div>
                        <div class="w-100 mt-3 d-flex flex-wrap gap-2 justify-content-center" id="legendPemakaianUnit" style="font-size: 0.78rem;"></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Grafik 3: Top 5 Barang Paling Banyak Dipakai --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                    <div>
                        <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                            <i class="bi bi-bar-chart-fill text-warning"></i> Top 5 Barang Paling Banyak Digunakan
                        </h5>
                        <p class="text-muted small mb-0">Barang dengan tingkat pemakaian tertinggi bulan {{ $daftarBulan[$bulan] }} {{ $tahun }}</p>
                    </div>
                    <a href="{{ route('stok-barang.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        Lihat Seluruhnya <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body p-4">
                    @if($topBarang->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-bar-chart fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada pemakaian barang di periode ini.
                        </div>
                    @else
                        <div style="height: 220px; position: relative;">
                            <canvas id="chartTopBarang"></canvas>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         TABEL & WIDGET OPERASIONAL - PERINGATAN & NOTIFIKASI
         ================================================================ --}}
    <div class="row g-4">
        {{-- Widget Kiri: Daftar Peringatan Stok Menipis & Habis --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 {{ $stokMenipis->count() > 0 ? 'border-top border-danger border-3' : '' }}">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-3">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                <div class="alert-icon-box" style="background: {{ $stokMenipis->count() > 0 ? 'rgba(239, 68, 68, 0.12); color: #dc2626;' : 'rgba(16, 185, 129, 0.12); color: #059669;' }}">
                                    <i class="bi {{ $stokMenipis->count() > 0 ? 'bi-exclamation-triangle-fill text-danger' : 'bi-shield-check text-success' }} fs-5"></i>
                                </div>
                                Peringatan Stok Kritis
                            </h5>
                            <p class="text-muted small mb-0">Barang dengan stok mencapai atau di bawah batas minimum</p>
                        </div>
                        @if($stokMenipis->count() > 0)
                            <div class="position-relative">
                                <span class="badge bg-danger px-3 py-2 rounded-pill fs-6 fw-bold shadow-sm">
                                    {{ $stokMenipis->count() }} Item
                                </span>
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle pulse-animation">
                                    <span class="visually-hidden">Alert</span>
                                </span>
                            </div>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> Aman
                            </span>
                        @endif
                    </div>
                    
                    @if($stokMenipis->count() > 0)
                        <div class="alert alert-danger alert-dismissible fade show mb-0 mt-3 py-2" role="alert" style="font-size: 0.85rem;">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>{{ $stokHabisCount }} barang habis</strong> dan <strong>{{ $stokMenipis->count() - $stokHabisCount }} barang menipis</strong>. Segera lakukan restok!
                        </div>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-4 py-3" style="min-width: 200px;">Nama Barang</th>
                                    <th class="text-center py-3" style="min-width: 100px;">Stok Saat Ini</th>
                                    <th class="text-center py-3" style="min-width: 100px;">Batas Min.</th>
                                    <th class="text-center py-3">Status</th>
                                    <th class="text-end pe-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($stokMenipis as $barang)
                                    @php
                                        $habis = $barang->stok_saat_ini <= 0;
                                        $persentase = $barang->stok_minimum > 0 
                                            ? round(($barang->stok_saat_ini / $barang->stok_minimum) * 100) 
                                            : 0;
                                    @endphp
                                    <tr class="{{ $habis ? 'table-danger' : '' }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                @if($habis)
                                                    <i class="bi bi-exclamation-circle-fill text-danger fs-5"></i>
                                                @else
                                                    <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
                                                @endif
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $barang->nama_barang }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="bi bi-tag"></i> {{ $barang->kode_barang ?? 'Tanpa kode' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold {{ $habis ? 'text-danger' : 'text-warning' }} fs-5">
                                                {{ $barang->stok_saat_ini }}
                                            </div>
                                            <small class="text-muted">{{ $barang->satuan }}</small>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-semibold text-dark">{{ $barang->stok_minimum }}</div>
                                            <small class="text-muted">{{ $barang->satuan }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if($habis)
                                                <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
                                                    <i class="bi bi-x-circle me-1"></i> HABIS
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm">
                                                    <i class="bi bi-exclamation-triangle me-1"></i> {{ $persentase }}%
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.barang.edit', $barang->id) }}" 
                                               class="btn {{ $habis ? 'btn-danger' : 'btn-warning' }} btn-sm rounded-pill px-3 shadow-sm" 
                                               title="Perbarui stok barang">
                                                <i class="bi bi-plus-circle me-1"></i> Restok
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <div class="mb-3">
                                                    <i class="bi bi-shield-check text-success" style="font-size: 4rem; opacity: 0.3;"></i>
                                                </div>
                                                <h6 class="fw-bold text-success mb-1">Semua Stok Aman!</h6>
                                                <p class="text-muted small mb-0">Tidak ada barang yang stoknya di bawah batas minimum</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($stokMenipis->count() > 0)
                    <div class="card-footer bg-light border-0 py-3 px-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i> Klik tombol Restok untuk memperbarui stok
                            </small>
                            <a href="{{ route('admin.barang.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                Lihat Semua Barang <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Widget Kanan: Permintaan Barang Pending Masuk --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 {{ $permintaanPending->count() > 0 ? 'border-top border-warning border-3' : '' }}">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-3">
                    <div class="d-flex align-items-start justify-content-between mb-2">
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                                <div class="alert-icon-box" style="background: {{ $permintaanPending->count() > 0 ? 'rgba(245, 158, 11, 0.15); color: #d97706;' : 'rgba(16, 185, 129, 0.12); color: #059669;' }}">
                                    <i class="bi {{ $permintaanPending->count() > 0 ? 'bi-bell-fill text-warning' : 'bi-check2-circle text-success' }} fs-5"></i>
                                </div>
                                Permintaan Menunggu Persetujuan
                            </h5>
                            <p class="text-muted small mb-0">Permintaan barang dari petugas cleaning service</p>
                        </div>
                        @if($permintaanPending->count() > 0)
                            <div class="position-relative">
                                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fs-6 fw-bold shadow-sm">
                                    {{ $permintaanPending->count() }} Pending
                                </span>
                                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-warning border border-light rounded-circle pulse-animation">
                                    <span class="visually-hidden">Alert</span>
                                </span>
                            </div>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">
                                <i class="bi bi-check-circle-fill me-1"></i> Clear
                            </span>
                        @endif
                    </div>
                    
                    @if($permintaanPending->count() > 0)
                        <div class="alert alert-warning alert-dismissible fade show mb-0 mt-3 py-2 d-flex align-items-center justify-content-between" role="alert" style="font-size: 0.85rem;">
                            <div>
                                <i class="bi bi-hourglass-split me-2"></i>
                                <strong>{{ $permintaanPending->count() }} permintaan</strong> menunggu untuk diproses
                            </div>
                            <a href="{{ route('barang.gudang') }}" class="btn btn-warning btn-sm rounded-pill px-3 text-dark fw-bold">
                                <i class="bi bi-arrow-right-circle me-1"></i> Proses Sekarang
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-4 py-3" style="min-width: 160px;">Petugas</th>
                                    <th class="py-3" style="min-width: 180px;">Barang Diminta</th>
                                    <th class="text-center py-3">Jumlah</th>
                                    <th class="text-center py-3">Stok Tersedia</th>
                                    <th class="text-end pe-4 py-3">Waktu Request</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($permintaanPending as $p)
                                    @php
                                        $stokAda = $p->barang->stok_saat_ini ?? 0;
                                        $cukup = $stokAda >= $p->jumlah;
                                    @endphp
                                    <tr class="{{ !$cukup ? 'table-warning' : '' }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-circle-md fw-bold d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 50%; background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                                                    {{ strtoupper(substr($p->pengguna->name ?? 'U', 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark">{{ $p->pengguna->name }}</div>
                                                    <div class="text-muted" style="font-size: 0.75rem;">
                                                        <i class="bi bi-person-badge"></i> {{ $p->pengguna->peran->nama_peran ?? 'CS' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $p->barang->nama_barang }}</div>
                                            <div class="text-muted" style="font-size: 0.75rem;">
                                                <i class="bi bi-tag"></i> {{ $p->barang->kode_barang ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold text-dark fs-5">{{ $p->jumlah }}</div>
                                            <small class="text-muted">{{ $p->barang->satuan }}</small>
                                        </td>
                                        <td class="text-center">
                                            @if($cukup)
                                                <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm">
                                                    <i class="bi bi-check-circle-fill me-1"></i> {{ $stokAda }} tersedia
                                                </span>
                                            @else
                                                <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">
                                                    <i class="bi bi-x-circle-fill me-1"></i> Hanya {{ $stokAda }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="text-muted" style="font-size: 0.8rem;">
                                                <i class="bi bi-clock"></i> {{ $p->waktu_request ? $p->waktu_request->diffForHumans() : '-' }}
                                            </div>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                {{ $p->waktu_request ? $p->waktu_request->format('d/m/Y H:i') : '' }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <div class="mb-3">
                                                    <i class="bi bi-inbox text-success" style="font-size: 4rem; opacity: 0.3;"></i>
                                                </div>
                                                <h6 class="fw-bold text-success mb-1">Tidak Ada Permintaan Pending</h6>
                                                <p class="text-muted small mb-0">Semua permintaan barang sudah diproses</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($permintaanPending->count() > 0)
                    <div class="card-footer bg-light border-0 py-3 px-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i> Klik "Proses Sekarang" untuk menyetujui/menolak
                            </small>
                            <a href="{{ route('barang.gudang') }}" class="btn btn-sm btn-primary rounded-pill">
                                <i class="bi bi-box-seam me-1"></i> Portal Kelola Permintaan
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Styles Custom untuk Dasbor Gudang --}}
<style>
    .gudang-hero {
        background: linear-gradient(135deg, #0d5c3a 0%, #12a65a 50%, #17c964 100%);
    }
    .hero-decoration-circle {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }
    .hero-decoration-circle-2 {
        position: absolute;
        bottom: -80px;
        right: 140px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        pointer-events: none;
    }
    .kpi-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
    }
    .kpi-card.shadow-warning {
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2) !important;
    }
    .kpi-card.shadow-danger {
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
    }
    .kpi-alert-dot {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #ef4444;
        animation: pulse-dot 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-dot {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.5;
            transform: scale(1.1);
        }
    }
    .pulse-badge {
        animation: pulse-badge 1.5s ease-in-out infinite;
    }
    @keyframes pulse-badge {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.8;
            transform: scale(0.95);
        }
    }
    .fs-8 {
        font-size: 0.7rem;
    }
    .kpi-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .avatar-circle-sm {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }
    .alert-icon-box {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pulse-animation {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    .empty-state {
        padding: 2rem 1rem;
    }
    .table thead.sticky-top {
        position: sticky;
        top: 0;
        z-index: 10;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>

{{-- Load Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // -----------------------------------------------------------------
    // 1. CHART TREN PERMINTAAN 7 HARI TERAKHIR (Line Chart Spline)
    // -----------------------------------------------------------------
    const trenData = @json($trenPermintaan);
    const ctxTren = document.getElementById('chartTrenPermintaan');
    if (ctxTren && trenData.length > 0) {
        const labels = trenData.map(d => d.tanggal);
        const totals = trenData.map(d => d.total);
        const approved = trenData.map(d => d.disetujui);

        new Chart(ctxTren, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Diajukan',
                        data: totals,
                        borderColor: '#12a65a',
                        backgroundColor: 'rgba(18, 166, 90, 0.12)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#12a65a',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Disetujui',
                        data: approved,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.05)',
                        fill: false,
                        tension: 0.4,
                        borderWidth: 2,
                        borderDash: [5, 5],
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            font: { family: 'Inter', size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 12 }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { family: 'Inter', size: 11 }
                        },
                        grid: { color: '#f3f4f6' }
                    }
                }
            }
        });
    }

    // -----------------------------------------------------------------
    // 2. CHART PROPORSI PEMAKAIAN PER UNIT (Doughnut Chart)
    // -----------------------------------------------------------------
    const unitData = @json($pemakaianPerUnit);
    const ctxUnit = document.getElementById('chartPemakaianUnit');
    if (ctxUnit && unitData.length > 0) {
        const palette = [
            '#12a65a', '#3b82f6', '#f59e0b', '#8b5cf6',
            '#ec4899', '#06b6d4', '#10b981', '#f97316',
            '#6366f1', '#14b8a6'
        ];
        const labels = unitData.map(u => u.unit);
        const dataValues = unitData.map(u => u.total);

        new Chart(ctxUnit, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: palette.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        titleFont: { family: 'Inter', size: 13 },
                        bodyFont: { family: 'Inter', size: 12 },
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const val = context.raw || 0;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                return ` ${context.label}: ${val} unit (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        // Buat custom legend kecil di bawah chart
        const legendContainer = document.getElementById('legendPemakaianUnit');
        if (legendContainer) {
            unitData.forEach((u, i) => {
                const color = palette[i % palette.length];
                const pill = document.createElement('span');
                pill.className = 'badge bg-light text-dark border px-2 py-1 rounded-pill d-inline-flex align-items-center gap-1';
                pill.innerHTML = `<span style="width:8px;height:8px;border-radius:50%;background:${color};display:inline-block;"></span> ${u.unit}: <strong>${u.total}</strong>`;
                legendContainer.appendChild(pill);
            });
        }
    }

    // -----------------------------------------------------------------
    // 3. CHART TOP 5 BARANG PALING SERING DIGUNAKAN (Horizontal Bar)
    // -----------------------------------------------------------------
    const topBarangData = @json($topBarang);
    const ctxTop = document.getElementById('chartTopBarang');
    if (ctxTop && topBarangData.length > 0) {
        const topLabels = topBarangData.map(b => b.nama_barang);
        const topValues = topBarangData.map(b => b.total_pakai);

        new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: topLabels,
                datasets: [{
                    label: 'Total Pemakaian',
                    data: topValues,
                    backgroundColor: 'rgba(18, 166, 90, 0.85)',
                    hoverBackgroundColor: '#0d8a4a',
                    borderRadius: 6,
                    barPercentage: 0.65,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 10,
                        callbacks: {
                            label: function(ctx) {
                                const item = topBarangData[ctx.dataIndex];
                                return ` ${ctx.formattedValue} ${item.satuan || 'unit'}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: { font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11, weight: '500' } }
                    }
                }
            }
        });
    }
});
</script>
@endsection
