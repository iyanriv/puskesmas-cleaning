@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-table text-success me-2"></i>Rekapan Pemakaian Barang Kebersihan
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">PEMAKAIAN PERBULAN TAHUN {{ $tahun }}</span>
                @if($kategori)
                    &mdash; <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $kategori }}</span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('barang.rekapan-pemakaian.export', array_filter(['tahun' => $tahun, 'kategori' => $kategori])) }}"
               class="btn btn-outline-success btn-sm rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('barang.gudang') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-box-seam me-1"></i> Permintaan Barang
            </a>
            <a href="{{ route('stok-barang.index') }}" class="btn btn-success btn-sm rounded-3 px-3">
                <i class="bi bi-clipboard2-data me-1"></i> Laporan Stok
            </a>
        </div>
    </div>

    {{-- ================================================================
         PANEL FILTER
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('barang.rekapan-pemakaian') }}" id="formFilter">
                <div class="row g-2 align-items-end">

                    {{-- Tahun --}}
                    <div class="col-6 col-md-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm rounded-3" style="min-width:100px;">
                            @foreach($daftarTahun as $t)
                                <option value="{{ $t }}" {{ $t == $tahun ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Kategori --}}
                    <div class="col-12 col-md-auto flex-md-grow-1">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">Kategori Barang</label>
                        <select name="kategori" class="form-select form-select-sm rounded-3" style="min-width:220px;">
                            <option value="">— Semua Kategori —</option>
                            @foreach($daftarKategori as $kat)
                                <option value="{{ $kat }}" {{ $kategori === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol --}}
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-4">
                            <i class="bi bi-search me-1"></i> Tampilkan
                        </button>
                    </div>
                    @if($tahun != now()->year || $kategori)
                        <div class="col-auto">
                            <a href="{{ route('barang.rekapan-pemakaian') }}" class="btn btn-outline-secondary btn-sm rounded-3">
                                <i class="bi bi-x-circle me-1"></i> Reset
                            </a>
                        </div>
                    @endif

                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================
         STATISTIK RINGKAS
         ================================================================ --}}
    @php
        $totalSemuaBarang  = count($rekapan);
        $bulanPalingBanyak = null;
        $maxBulan          = 0;
        for ($b = 1; $b <= 12; $b++) {
            if (($totalPerBulan[$b] ?? 0) > $maxBulan) {
                $maxBulan = $totalPerBulan[$b];
                $bulanPalingBanyak = $b;
            }
        }
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success-subtle">
                        <i class="bi bi-boxes text-success fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ number_format($totalSemuaBarang) }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Jenis Barang</div>
                        <div class="text-secondary" style="font-size:0.7rem;">yang tercatat digunakan</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning-subtle">
                        <i class="bi bi-bar-chart-line text-warning fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ number_format($grandTotal) }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Total Pemakaian</div>
                        <div class="text-secondary" style="font-size:0.7rem;">sepanjang tahun {{ $tahun }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-info-subtle">
                        <i class="bi bi-calculator text-info fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ number_format($grandRata, 1) }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Rata-rata/Bulan</div>
                        <div class="text-secondary" style="font-size:0.7rem;">semua jenis barang</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary-subtle">
                        <i class="bi bi-trophy text-primary fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">
                            {{ $bulanPalingBanyak ? $namaBulan[$bulanPalingBanyak] : '-' }}
                        </div>
                        <div class="text-muted" style="font-size:0.78rem;">Bulan Tertinggi</div>
                        <div class="text-secondary" style="font-size:0.7rem;">
                            {{ $bulanPalingBanyak ? number_format($totalPerBulan[$bulanPalingBanyak]) . ' unit' : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         TABEL REKAPAN MATRIX
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
            <div>
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-table text-success me-2"></i>
                    Rekapan Pemakaian Perbulan — Tahun {{ $tahun }}
                </h6>
                <small class="text-muted" style="font-size:0.75rem;">
                    Data dari permintaan barang yang telah <strong>disetujui gudang</strong>.
                    Kolom <strong>Rata²</strong> = Total ÷ 12 &nbsp;|&nbsp; <strong>Bulan</strong> = pembulatan rata-rata.
                </small>
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">
                {{ count($rekapan) }} jenis barang
            </span>
        </div>

        <div style="overflow-x: auto;">
            <table class="table table-sm table-hover mb-0 align-middle" style="min-width: 1200px; font-size: 0.82rem; white-space: nowrap;">
                <thead class="table-success">
                    <tr>
                        <th class="px-3" style="width:40px;">No</th>
                        <th style="min-width:220px; white-space:normal;">Nama Barang</th>
                        <th class="text-center" style="width:70px;">Kode</th>
                        <th class="text-center" style="width:65px;">Satuan</th>
                        @foreach($namaBulan as $noBulan => $singkatan)
                            <th class="text-center" style="width:52px;">{{ $singkatan }}</th>
                        @endforeach
                        <th class="text-center fw-bold" style="width:65px; background:#d1fae5; color:#065f46;">Total</th>
                        <th class="text-center" style="width:72px; background:#d1fae5; color:#065f46;">Rata²/Bln</th>
                        <th class="text-center" style="width:65px; background:#d1fae5; color:#065f46;">Bulan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rekapan as $i => $row)
                        <tr>
                            <td class="px-3 text-secondary" style="font-size:0.76rem;">{{ $i + 1 }}</td>
                            <td class="fw-semibold" style="white-space:normal;">
                                {{ $row['barang']->nama_barang }}
                                @if($row['barang']->kategori)
                                    <br><small class="text-muted fw-normal">{{ $row['barang']->kategori }}</small>
                                @endif
                            </td>
                            <td class="text-center text-secondary" style="font-family:monospace; font-size:0.74rem;">
                                {{ $row['barang']->kode_barang ?? '-' }}
                            </td>
                            <td class="text-center text-muted">{{ ucfirst($row['barang']->satuan) }}</td>
                            @foreach($namaBulan as $noBulan => $singkatan)
                                @php $val = $row['bulanan'][$noBulan] ?? 0; @endphp
                                <td class="text-center {{ $val > 0 ? 'fw-semibold text-dark' : 'text-secondary' }}">
                                    {{ $val > 0 ? $val : '—' }}
                                </td>
                            @endforeach
                            <td class="text-center fw-bold" style="background:#f0fdf4; color:#065f46;">
                                {{ $row['total'] > 0 ? $row['total'] : '—' }}
                            </td>
                            <td class="text-center text-secondary" style="background:#f0fdf4;">
                                {{ $row['rata_rata'] > 0 ? number_format($row['rata_rata'], 2) : '—' }}
                            </td>
                            <td class="text-center fw-bold" style="background:#f0fdf4; color:#059669;">
                                {{ $row['pembulatan'] > 0 ? $row['pembulatan'] : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="18" class="text-center py-5">
                                <div class="text-secondary">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                    <div class="fw-semibold mb-1">Tidak ada data pemakaian</div>
                                    <small>
                                        Belum ada permintaan barang yang disetujui gudang pada tahun {{ $tahun }}
                                        @if($kategori) di kategori "{{ $kategori }}" @endif.
                                    </small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Baris Total --}}
                @if(count($rekapan) > 0)
                <tfoot>
                    <tr class="table-success fw-bold">
                        <td colspan="4" class="px-3 text-success" style="font-size:0.82rem;">TOTAL</td>
                        @foreach($namaBulan as $noBulan => $singkatan)
                            <td class="text-center">
                                {{ ($totalPerBulan[$noBulan] ?? 0) > 0 ? $totalPerBulan[$noBulan] : '—' }}
                            </td>
                        @endforeach
                        <td class="text-center" style="background:#d1fae5; color:#065f46;">{{ number_format($grandTotal) }}</td>
                        <td class="text-center" style="background:#d1fae5; color:#065f46;">{{ number_format($grandRata, 2) }}</td>
                        <td class="text-center" style="background:#d1fae5; color:#065f46;">{{ (int) round($grandRata) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>

    </div>

</div>
@endsection
