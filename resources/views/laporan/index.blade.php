@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-graph-up text-success me-2"></i>Dashboard Laporan Kinerja
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">{{ strtoupper($judul) }}</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('laporan.cetak-excel', request()->all()) }}" target="_blank" class="btn btn-outline-success btn-sm rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Excel
            </a>
            <a href="{{ route('laporan.preview-pdf', request()->all()) }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
            </a>
        </div>
    </div>

    {{-- ================================================================
         PANEL FILTER
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('laporan.index') }}" id="form-filter">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-calendar3 text-success me-1"></i>Periode Laporan
                        </label>
                        <div class="btn-group btn-group-sm d-flex" role="group">
                            <a href="{{ route('laporan.index', ['filter'=>'hari']) }}"
                               class="btn {{ $filter==='hari' ? 'btn-success' : 'btn-outline-secondary' }} rounded-start-3 px-3">
                                Hari Ini
                            </a>
                            <a href="{{ route('laporan.index', ['filter'=>'minggu']) }}"
                               class="btn {{ $filter==='minggu' ? 'btn-success' : 'btn-outline-secondary' }} px-3">
                                Minggu Ini
                            </a>
                            <a href="{{ route('laporan.index', ['filter'=>'bulan', 'bulan'=>$bulan]) }}"
                               class="btn {{ $filter==='bulan' ? 'btn-success' : 'btn-outline-secondary' }} rounded-end-3 px-3">
                                Bulanan
                            </a>
                        </div>
                    </div>

                    @if($filter === 'bulan')
                    <div class="col-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            Pilih Bulan
                        </label>
                        <input type="month" name="bulan" class="form-control form-control-sm rounded-3" value="{{ $bulan }}" onchange="this.form.submit()" style="min-width:150px;">
                    </div>
                    @endif
                    <input type="hidden" name="filter" value="{{ $filter }}">
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================
         STATISTIK RINGKAS (KPI)
         ================================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success-subtle"><i class="bi bi-check2-circle text-success fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $ceklisPersen }}%</div>
                        <div class="text-muted" style="font-size:0.78rem;">Tugas CS Selesai</div>
                        <div class="text-secondary" style="font-size:0.72rem;">{{ $ceklisSelesai }}/{{ $totalCeklis }} area</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary-subtle"><i class="bi bi-box-seam text-primary fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $totalPermintaan }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Permintaan Barang</div>
                        <div class="text-secondary" style="font-size:0.72rem;">✓ {{ $permintaanDisetujui }} disetujui · ✗ {{ $permintaanDitolak }} ditolak</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning-subtle"><i class="bi bi-recycle text-warning fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $totalSetoran }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Laporan Bank Sampah</div>
                        <div class="text-secondary" style="font-size:0.72rem;">{{ $totalSetoran }} kali setoran</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-info-subtle"><i class="bi bi-award text-info fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $rataKinerja ?? '—' }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Rata-rata Kinerja</div>
                        <div class="text-secondary" style="font-size:0.72rem;">{{ ($rataKinerja && $rataKinerja > 5) ? 'skor max 100' : 'skor max 5' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         TOP PERFORMER BANNER (JIKA ADA)
         ================================================================ --}}
    @if($topPerformer)
        @php
            $isSkor100 = $topPerformer['rata'] > 5;
            $persen = $isSkor100 ? min(100, max(0, $topPerformer['rata'])) : min(100, max(0, ($topPerformer['rata'] / 5) * 100));
            $skorMax = $isSkor100 ? 100 : 5;
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-success bg-opacity-10 border-start border-4 border-success">
            <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center fw-bold bg-success text-white"
                         style="width:48px; height:48px; font-size:1.2rem;">
                        {{ strtoupper(substr($topPerformer['nama'], 0, 1)) }}
                    </div>
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 mb-1" style="font-size:0.7rem;">
                            <i class="bi bi-trophy-fill me-1"></i>TOP PERFORMER PERIODE INI
                        </span>
                        <h6 class="fw-bold text-dark mb-0">{{ $topPerformer['nama'] }}</h6>
                        <div class="text-muted" style="font-size:0.8rem;">
                            Rata-rata Skor: <strong class="text-success">{{ $topPerformer['rata'] }} / {{ $skorMax }}</strong>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-bold fs-5 text-success">{{ round($persen) }}%</div>
                        <div class="text-muted" style="font-size:0.72rem;">Efektivitas</div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ================================================================
         GRAFIK CEKLIS 7 HARI TERAKHIR
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
            <h6 class="fw-bold mb-0 text-success">
                <i class="bi bi-bar-chart-line me-2"></i>TUGAS MINGGUAN CS &mdash; 8 MINGGU TERAKHIR
            </h6>
            <div class="d-flex gap-3" style="font-size:0.75rem;">
                <span class="d-flex align-items-center gap-1">
                    <span style="width:10px; height:10px; border-radius:3px; background:#10B981; display:inline-block;"></span> Selesai
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span style="width:10px; height:10px; border-radius:3px; background:#e5e7eb; display:inline-block;"></span> Total Tugas
                </span>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="d-flex align-items-flex-end gap-2" style="height: 140px;">
                @php $maxBar = $grafikCeklis->max('total') ?: 1; @endphp
                @foreach($grafikCeklis as $g)
                    <div class="flex-grow-1 d-flex flex-column align-items-center gap-1 h-100 justify-content-end">
                        <div class="fw-bold text-muted" style="font-size:0.7rem;">{{ $g['selesai'] }}/{{ $g['total'] }}</div>
                        <div class="w-100 d-flex flex-column align-items-center justify-content-end" style="height:100px;">
                            <div class="w-100 position-relative rounded-top" style="height:{{ max(6, ($g['total']/$maxBar)*90) }}px; background:#f1f5f9;">
                                @if($g['total'] > 0)
                                    <div class="position-absolute bottom-0 start-0 end-0 bg-success rounded-top"
                                         style="height:{{ ($g['selesai']/$g['total'])*100 }}%;"></div>
                                @endif
                            </div>
                        </div>
                        <div class="text-muted fw-semibold" style="font-size:0.7rem;">{{ $g['tgl'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ================================================================
         DETAIL SECTION: DUA KOLOM
         ================================================================ --}}
    <div class="row g-4 mb-4">
        {{-- Kolom Kiri: Ceklis per Area --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-map me-2"></i>CEKLIS PER AREA PENUGASAN
                    </h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                        {{ count($ceklisPerArea) }} area
                    </span>
                </div>
                <div class="card-body p-3" style="max-height: 380px; overflow-y: auto;">
                    @forelse($ceklisPerArea as $a)
                        <div class="p-2 px-3 mb-2 rounded-3 border bg-light bg-opacity-50">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold text-dark" style="font-size:0.84rem;">{{ $a['nama'] }}</span>
                                <span class="{{ $a['selesai'] === $a['total'] ? 'text-success' : 'text-warning' }} fw-bold" style="font-size:0.8rem;">
                                    {{ $a['selesai'] }}/{{ $a['total'] }} Selesai
                                </span>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $a['total'] > 0 ? ($a['selesai']/$a['total'])*100 : 0 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted" style="font-size:0.84rem;">Tidak ada data area.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Ringkasan Permintaan & Sampah --}}
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-clipboard-data me-2"></i>RINGKASAN LOGISTIK & BANK SAMPAH
                    </h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush rounded-bottom-4" style="font-size:0.85rem;">
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                            <span class="text-muted"><i class="bi bi-boxes me-2"></i>Total Permintaan Barang</span>
                            <span class="fw-bold text-dark fs-6">{{ $totalPermintaan }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                            <span class="text-muted"><i class="bi bi-check-circle text-success me-2"></i>Permintaan Disetujui</span>
                            <span class="fw-bold text-success">{{ $permintaanDisetujui }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                            <span class="text-muted"><i class="bi bi-x-circle text-danger me-2"></i>Permintaan Ditolak</span>
                            <span class="fw-bold text-danger">{{ $permintaanDitolak }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                            <span class="text-muted"><i class="bi bi-hourglass-split text-warning me-2"></i>Permintaan Menunggu</span>
                            <span class="fw-bold text-warning">{{ $totalPermintaan - $permintaanDisetujui - $permintaanDitolak }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 bg-success bg-opacity-10">
                            <span class="fw-semibold text-dark"><i class="bi bi-recycle text-success me-2"></i>Total Laporan Bank Sampah</span>
                            <span class="fw-bold text-success fs-6">{{ $totalSetoran }} laporan</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         LINK DETAIL PER MODUL
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
            <h6 class="fw-bold mb-0 text-success">
                <i class="bi bi-link-45deg me-2"></i>AKSES CEPAT LAPORAN MODUL TERKAIT
            </h6>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('sampah.rekapan') }}" class="card border rounded-3 p-3 text-decoration-none h-100 hover-shadow transition">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 bg-success-subtle text-success fs-4">
                                <i class="bi bi-recycle"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size:0.88rem;">Bank Sampah</div>
                                <div class="text-muted" style="font-size:0.75rem;">Rekap per jenis & petugas</div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('barang.gudang') }}" class="card border rounded-3 p-3 text-decoration-none h-100 hover-shadow transition">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 bg-primary-subtle text-primary fs-4">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size:0.88rem;">Permintaan Barang</div>
                                <div class="text-muted" style="font-size:0.75rem;">Stok & riwayat permintaan</div>
                            </div>
                        </div>
                    </a>
                </div>

                @if(in_array(auth()->user()->peran->nama_peran ?? '', ['admin', 'gudang']))
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('stok-barang.index') }}" class="card border rounded-3 p-3 text-decoration-none h-100 hover-shadow transition">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 bg-info-subtle text-info fs-4">
                                <i class="bi bi-clipboard2-data"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size:0.88rem;">Laporan Stok Barang</div>
                                <div class="text-muted" style="font-size:0.75rem;">Stok masuk & pemakaian</div>
                            </div>
                        </div>
                    </a>
                </div>
                @endif

                @if((auth()->user()->peran->nama_peran ?? '') === 'admin')
                <div class="col-sm-6 col-lg-3">
                    <a href="{{ route('admin.penilaian-pj.index') }}" class="card border rounded-3 p-3 text-decoration-none h-100 hover-shadow transition">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 p-2 bg-warning-subtle text-warning fs-4">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark" style="font-size:0.88rem;">Penilaian PJ Lantai</div>
                                <div class="text-muted" style="font-size:0.75rem;">Monitoring hasil evaluasi</div>
                            </div>
                        </div>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
