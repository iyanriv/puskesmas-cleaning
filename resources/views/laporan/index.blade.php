@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { font-family: 'Inter', sans-serif; background: #f0f2f5; }

    /* ── Header ── */
    .laporan-header {
        background: linear-gradient(135deg, #064e2b 0%, #086838 100%);
        border-radius: 24px; padding: 2rem; color: white; margin-bottom: 2rem;
        position: relative; overflow: hidden;
    }
    .laporan-header::before {
        content: ''; position: absolute; right: -40px; top: -40px;
        width: 200px; height: 200px; border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .laporan-header h4 { font-weight: 800; font-size: 1.3rem; margin: 0 0 0.3rem; }
    .laporan-header p  { margin: 0; opacity: 0.65; font-size: 0.85rem; }

    /* ── Filter bar ── */
    .filter-bar {
        background: white; border-radius: 16px; padding: 0.9rem 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 1.75rem;
        display: flex; align-items: center; flex-wrap: wrap; gap: 0.6rem;
    }
    .btn-filter {
        padding: 7px 18px; border-radius: 20px; font-size: 0.8rem; font-weight: 700;
        border: 1.5px solid #e5e7eb; background: white; color: #6b7280;
        text-decoration: none; transition: all 0.15s;
    }
    .btn-filter.active { background: #064e2b; border-color: #064e2b; color: white; }
    .select-bulan {
        border: 1.5px solid #e5e7eb; border-radius: 20px; padding: 6px 14px;
        font-size: 0.8rem; font-weight: 600; background: white; color: #374151;
    }
    .select-bulan:focus { outline: none; border-color: #064e2b; }

    /* ── KPI Cards ── */
    .kpi-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
    @media(min-width:768px) { .kpi-grid { grid-template-columns: repeat(4, 1fr); } }

    .kpi-card {
        background: white; border-radius: 18px; padding: 1.2rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); position: relative; overflow: hidden;
    }
    .kpi-card::after {
        content: ''; position: absolute; right: -15px; bottom: -15px;
        width: 70px; height: 70px; border-radius: 50%; opacity: 0.06;
    }
    .kpi-card.hijau  { border-top: 3px solid #10B981; } .kpi-card.hijau::after  { background: #10B981; }
    .kpi-card.biru   { border-top: 3px solid #12a65a; } .kpi-card.biru::after   { background: #12a65a; }
    .kpi-card.kuning { border-top: 3px solid #f59e0b; } .kpi-card.kuning::after { background: #f59e0b; }
    .kpi-card.ungu   { border-top: 3px solid #7c3aed; } .kpi-card.ungu::after   { background: #7c3aed; }

    .kpi-ikon { font-size: 1.4rem; margin-bottom: 0.5rem; display: block; }
    .kpi-angka { font-size: 2rem; font-weight: 900; line-height: 1; margin-bottom: 0.2rem; }
    .kpi-label { font-size: 0.75rem; color: #9ca3af; font-weight: 600; }
    .kpi-sub   { font-size: 0.73rem; color: #6b7280; margin-top: 0.3rem; }
    .kpi-card.hijau  .kpi-angka { color: #059669; }
    .kpi-card.biru   .kpi-angka { color: #12a65a; }
    .kpi-card.kuning .kpi-angka { color: #d97706; }
    .kpi-card.ungu   .kpi-angka { color: #7c3aed; }

    /* ── Grafik ── */
    .grafik-card {
        background: white; border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .grafik-card-title { font-weight: 700; font-size: 0.92rem; margin-bottom: 1.25rem; color: #111827; }

    /* Bar chart custom CSS */
    .bar-chart { display: flex; align-items: flex-end; gap: 0.5rem; height: 140px; }
    .bar-col   { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 0.3rem; }
    .bar-wrap  { width: 100%; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; flex: 1; position: relative; }
    .bar {
        width: 100%; border-radius: 8px 8px 0 0;
        min-height: 4px; transition: height 0.4s;
        position: relative;
    }
    .bar.selesai { background: linear-gradient(to top, #10B981, #34d399); }
    .bar.sisa    { background: #e5e7eb; }
    .bar-tgl     { font-size: 0.65rem; color: #9ca3af; font-weight: 600; }
    .bar-val     { font-size: 0.68rem; font-weight: 700; color: #374151; }

    /* ── Detail section ── */
    .detail-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }
    @media(min-width:768px) { .detail-grid { grid-template-columns: 1fr 1fr; } }

    .detail-card {
        background: white; border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); overflow: hidden;
    }
    .detail-header {
        padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6;
        font-weight: 700; font-size: 0.88rem; display: flex; align-items: center; gap: 0.5rem;
    }
    .detail-row {
        padding: 0.75rem 1.25rem; border-bottom: 1px solid #f9fafb;
        display: flex; align-items: center; justify-content: space-between; font-size: 0.84rem;
    }
    .detail-row:last-child { border-bottom: none; }

    .area-bar { height: 6px; border-radius: 3px; background: #f3f4f6; margin-top: 4px; overflow: hidden; }
    .area-bar-fill { height: 100%; border-radius: 3px; background: linear-gradient(to right, #12a65a, #0d8a4a); }

    /* ── Top performer ── */
    .top-card {
        background: linear-gradient(135deg, #059669, #10B981);
        border-radius: 18px; padding: 1.25rem; color: white; margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 1rem;
    }
    .top-avatar {
        width: 52px; height: 52px; border-radius: 14px;
        background: rgba(255,255,255,0.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; font-weight: 800; flex-shrink: 0;
    }
    .progress-ring {
        width: 60px; height: 60px; flex-shrink: 0;
        border-radius: 50%;
        background: conic-gradient(
            #10B981 calc(var(--persen) * 1%),
            rgba(255,255,255,0.2) 0%
        );
        display: flex; align-items: center; justify-content: center;
    }
    .progress-ring-inner {
        width: 46px; height: 46px; border-radius: 50%;
        background: #059669;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; font-weight: 800; color: white;
    }

    /* Link laporan modul */
    .modul-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
    .modul-link-card {
        background: white; border-radius: 16px; padding: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04); text-decoration: none; color: inherit;
        display: flex; align-items: center; gap: 0.75rem; transition: all 0.2s;
        border: 1.5px solid transparent;
    }
    .modul-link-card:hover { border-color: #12a65a; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(18,166,90,0.1); }
    .modul-link-icon {
        width: 42px; height: 42px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;
    }
    .modul-link-nama { font-weight: 700; font-size: 0.82rem; color: #111827; }
    .modul-link-sub  { font-size: 0.7rem; color: #9ca3af; }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-10">

            {{-- Header --}}
            <div class="laporan-header">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4><i class="bi bi-graph-up me-2"></i>Dashboard Laporan</h4>
                        <p>{{ $judul }}</p>
                    </div>
                    <div class="d-flex gap-2 position-relative" style="z-index: 1;">
                        <a href="{{ route('laporan.cetak-pdf', request()->all()) }}" class="btn btn-light rounded-pill px-4" target="_blank">
                            <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> Cetak PDF
                        </a>
                        <a href="{{ route('laporan.cetak-excel', request()->all()) }}" class="btn btn-light rounded-pill px-4" target="_blank">
                            <i class="bi bi-file-earmark-excel-fill text-success me-1"></i> Cetak Excel
                        </a>
                    </div>
                </div>
            </div>

            {{-- Filter --}}
            <form method="GET" action="{{ route('laporan.index') }}" class="filter-bar" id="form-filter">
                <span class="fw-semibold" style="font-size:0.82rem; color:#374151;">Periode:</span>
                <div class="d-flex gap-1">
                    <a href="{{ route('laporan.index', ['filter'=>'hari']) }}"
                       class="btn-filter {{ $filter==='hari' ? 'active' : '' }}">Hari Ini</a>
                    <a href="{{ route('laporan.index', ['filter'=>'minggu']) }}"
                       class="btn-filter {{ $filter==='minggu' ? 'active' : '' }}">Minggu Ini</a>
                    <a href="{{ route('laporan.index', ['filter'=>'bulan', 'bulan'=>$bulan]) }}"
                       class="btn-filter {{ $filter==='bulan' ? 'active' : '' }}">Bulan Tertentu</a>
                </div>
                
                <input type="hidden" name="filter" value="{{ $filter }}">
                
                @if($filter === 'bulan')
                    <div class="d-flex align-items-center ms-2">
                        <input type="month" name="bulan" class="select-bulan" value="{{ $bulan }}" onchange="this.form.submit()">
                    </div>
                @endif
            </form>

            {{-- ── KPI Cards ── --}}
            <div class="kpi-grid">
                <div class="kpi-card hijau">
                    <span class="kpi-ikon">✅</span>
                    <div class="kpi-angka">{{ $ceklisPersen }}%</div>
                    <div class="kpi-label">Ceklis Selesai</div>
                    <div class="kpi-sub">{{ $ceklisSelesai }}/{{ $totalCeklis }} area</div>
                </div>
                <div class="kpi-card biru">
                    <span class="kpi-ikon">📦</span>
                    <div class="kpi-angka">{{ $totalPermintaan }}</div>
                    <div class="kpi-label">Permintaan Barang</div>
                    <div class="kpi-sub">✓ {{ $permintaanDisetujui }} disetujui · ✗ {{ $permintaanDitolak }} ditolak</div>
                </div>
                <div class="kpi-card kuning">
                    <span class="kpi-ikon">♻️</span>
                    <div class="kpi-angka">{{ number_format($totalKgSampah, 1) }}</div>
                    <div class="kpi-label">Total Sampah (kg)</div>
                    <div class="kpi-sub">{{ $totalSetoran }} kali setoran</div>
                </div>
                <div class="kpi-card ungu">
                    <span class="kpi-ikon">⭐</span>
                    <div class="kpi-angka">{{ $rataKinerja ?? '—' }}</div>
                    <div class="kpi-label">Rata-rata Kinerja</div>
                    <div class="kpi-sub">dari 5 nilai maksimal</div>
                </div>
            </div>

            {{-- Top Performer --}}
            @if($topPerformer)
                <div class="top-card">
                    <span style="font-size:2rem;">🏆</span>
                    <div class="top-avatar">{{ strtoupper(substr($topPerformer['nama'], 0, 1)) }}</div>
                    <div class="flex-grow-1">
                        <div style="font-size:0.72rem; opacity:0.8; font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Top Performer Periode Ini</div>
                        <div class="fw-bold" style="font-size:1rem;">{{ $topPerformer['nama'] }}</div>
                        <div style="font-size:0.8rem; opacity:0.85;">Rata-rata: ⭐ {{ $topPerformer['rata'] }}/5</div>
                    </div>
                    @php $persen = ($topPerformer['rata'] / 5) * 100; @endphp
                    <div class="progress-ring" style="--persen:{{ $persen }}">
                        <div class="progress-ring-inner">{{ $topPerformer['rata'] }}</div>
                    </div>
                </div>
            @endif

            {{-- ── Grafik Ceklis 7 Hari ── --}}
            <div class="grafik-card">
                <div class="grafik-card-title">
                    <i class="bi bi-bar-chart-line-fill text-success me-2"></i>Ceklis Kebersihan — 7 Hari Terakhir
                </div>
                <div class="bar-chart">
                    @php $maxBar = $grafikCeklis->max('total') ?: 1; @endphp
                    @foreach($grafikCeklis as $g)
                        <div class="bar-col">
                            <div class="bar-val">{{ $g['selesai'] }}/{{ $g['total'] }}</div>
                            <div class="bar-wrap">
                                {{-- Bar total (abu) --}}
                                <div class="bar sisa" style="height:{{ ($g['total']/$maxBar)*120 }}px; position:relative;">
                                    {{-- Bar selesai (hijau) di atas --}}
                                    @if($g['total'] > 0)
                                        <div class="bar selesai"
                                             style="height:{{ ($g['selesai']/$g['total'])*100 }}%; position:absolute; bottom:0; left:0; right:0; border-radius:6px 6px 0 0;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="bar-tgl">{{ $g['tgl'] }}</div>
                        </div>
                    @endforeach
                </div>
                <div class="d-flex gap-3 mt-2" style="font-size:0.72rem;">
                    <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#10B981;margin-right:4px;"></span>Selesai</span>
                    <span><span style="display:inline-block;width:12px;height:12px;border-radius:3px;background:#e5e7eb;margin-right:4px;"></span>Total</span>
                </div>
            </div>

            {{-- ── Detail ── --}}
            <div class="detail-grid">

                {{-- Ceklis per area --}}
                <div class="detail-card">
                    <div class="detail-header">
                        <i class="bi bi-map text-success"></i> Ceklis per Area
                    </div>
                    @forelse($ceklisPerArea as $a)
                        <div class="detail-row" style="flex-direction:column; align-items:flex-start;">
                            <div class="d-flex justify-content-between w-100">
                                <span class="fw-semibold">{{ $a['nama'] }}</span>
                                <span class="{{ $a['selesai'] === $a['total'] ? 'text-success' : 'text-warning' }} fw-bold" style="font-size:0.8rem;">
                                    {{ $a['selesai'] }}/{{ $a['total'] }}
                                </span>
                            </div>
                            <div class="area-bar w-100 mt-1">
                                <div class="area-bar-fill"
                                     style="width:{{ $a['total'] > 0 ? ($a['selesai']/$a['total'])*100 : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-secondary" style="font-size:0.84rem;">Tidak ada data.</div>
                    @endforelse
                </div>

                {{-- Permintaan barang & sampah --}}
                <div class="detail-card">
                    <div class="detail-header">
                        <i class="bi bi-clipboard-data text-warning"></i> Ringkasan Permintaan & Sampah
                    </div>
                    <div class="detail-row">
                        <span class="text-secondary">Total Permintaan Barang</span>
                        <span class="fw-bold">{{ $totalPermintaan }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="text-secondary">Disetujui</span>
                        <span class="fw-bold text-success">{{ $permintaanDisetujui }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="text-secondary">Ditolak</span>
                        <span class="fw-bold text-danger">{{ $permintaanDitolak }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="text-secondary">Pending</span>
                        <span class="fw-bold text-warning">{{ $totalPermintaan - $permintaanDisetujui - $permintaanDitolak }}</span>
                    </div>
                    <div class="detail-row" style="background:#f0fdf4; border-radius:0 0 18px 18px;">
                        <span class="text-secondary">♻️ Total Sampah Disetor</span>
                        <span class="fw-bold text-success">{{ number_format($totalKgSampah, 1) }} kg</span>
                    </div>
                </div>
            </div>

            {{-- ── Link cepat ke laporan modul ── --}}
            <div class="fw-bold mb-2" style="font-size:0.8rem; color:#6b7280; text-transform:uppercase; letter-spacing:0.06em;">
                Laporan Detail per Modul
            </div>
            <div class="modul-links">
                <a href="{{ route('sampah.rekapan') }}" class="modul-link-card">
                    <div class="modul-link-icon" style="background:#d1fae5; color:#059669;">♻️</div>
                    <div>
                        <div class="modul-link-nama">Bank Sampah</div>
                        <div class="modul-link-sub">Rekap per jenis & petugas</div>
                    </div>
                </a>
                <a href="{{ route('penilaian.rekap') }}" class="modul-link-card">
                    <div class="modul-link-icon" style="background:#ede9fe; color:#7c3aed;">⭐</div>
                    <div>
                        <div class="modul-link-nama">Penilaian Kinerja</div>
                        <div class="modul-link-sub">Peringkat & detail aspek</div>
                    </div>
                </a>
                <a href="{{ route('barang.gudang') }}" class="modul-link-card">
                    <div class="modul-link-icon" style="background:#dbeafe; color:#12a65a;">📦</div>
                    <div>
                        <div class="modul-link-nama">Permintaan Barang</div>
                        <div class="modul-link-sub">Stok & riwayat permintaan</div>
                    </div>
                </a>
                <a href="{{ route('penilaian.index') }}" class="modul-link-card">
                    <div class="modul-link-icon" style="background:#fef3c7; color:#d97706;">📋</div>
                    <div>
                        <div class="modul-link-nama">Input Penilaian</div>
                        <div class="modul-link-sub">Nilai petugas bulan ini</div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection
