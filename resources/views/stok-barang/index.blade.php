@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-clipboard2-data text-success me-2"></i>Laporan Stok Barang Kebersihan
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">{{ strtoupper($daftarBulan[$bulan]) }} {{ $tahun }}</span>
                @if($filterUnit)
                    &mdash; <span class="badge bg-success-subtle text-success border border-success-subtle">{{ $filterUnit }}</span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn btn-success btn-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                <i class="bi bi-plus-circle me-1"></i> Tambah Barang Baru
            </button>
            <button class="btn btn-outline-secondary btn-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#modalTambahPeriode">
                <i class="bi bi-list-check me-1"></i> Dari Master
            </button>
        </div>
    </div>

    {{-- ================================================================
         FLASH ALERT
         ================================================================ --}}
    @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('gagal'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('gagal') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================================================================
         PANEL FILTER — Periode + Unit + Minggu + Export
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('stok-barang.index') }}" id="formFilter">
                <div class="row g-2 align-items-end">

                    {{-- Bulan --}}
                    <div class="col-6 col-md-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">Bulan</label>
                        <select name="bulan" class="form-select form-select-sm rounded-3" style="min-width:120px;">
                            @foreach($daftarBulan as $no => $nama)
                                <option value="{{ $no }}" {{ $bulan == $no ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tahun --}}
                    <div class="col-6 col-md-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">Tahun</label>
                        <select name="tahun" class="form-select form-select-sm rounded-3" style="min-width:90px;">
                            @foreach($daftarTahun as $th)
                                <option value="{{ $th }}" {{ $tahun == $th ? 'selected' : '' }}>{{ $th }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Divider visual --}}
                    <div class="col-auto d-none d-md-flex align-items-end pb-1">
                        <div style="width:1px; height:28px; background:#dee2e6;"></div>
                    </div>

                    {{-- Filter Unit --}}
                    <div class="col-6 col-md-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-geo-alt text-success me-1"></i>Unit / Lokasi
                        </label>
                        <select name="unit" class="form-select form-select-sm rounded-3" style="min-width:130px;">
                            <option value="">— Semua Unit —</option>
                            @foreach($daftarUnit as $unit)
                                <option value="{{ $unit }}" {{ $filterUnit === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Minggu (hanya aktif jika unit dipilih) --}}
                    <div class="col-6 col-md-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-calendar-week text-success me-1"></i>Minggu
                        </label>
                        <select name="minggu" class="form-select form-select-sm rounded-3" style="min-width:110px;"
                                id="selectMinggu" {{ !$filterUnit ? 'disabled' : '' }}>
                            <option value="0" {{ $filterMinggu == 0 ? 'selected' : '' }}>— Semua —</option>
                            @foreach([1,2,3,4] as $w)
                                <option value="{{ $w }}" {{ $filterMinggu == $w ? 'selected' : '' }}>Week {{ $w }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Tombol Tampilkan --}}
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-3">
                            <i class="bi bi-funnel me-1"></i> Tampilkan
                        </button>
                    </div>

                    {{-- Tombol Reset --}}
                    @if($filterUnit || $filterMinggu)
                    <div class="col-auto">
                        <a href="{{ route('stok-barang.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                           class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                            <i class="bi bi-x-circle me-1"></i> Reset Filter
                        </a>
                    </div>
                    @endif

                    {{-- Export — push ke kanan --}}
                    <div class="col-auto ms-auto">
                        <div class="d-flex gap-2">
                            @php
                                $paramsExport = ['bulan' => $bulan, 'tahun' => $tahun];
                                if ($filterUnit) $paramsExport['unit'] = $filterUnit;
                                if ($filterMinggu) $paramsExport['minggu'] = $filterMinggu;
                            @endphp
                            <a href="{{ route('stok-barang.export-excel', $paramsExport) }}"
                               id="btnExportExcel"
                               class="btn btn-outline-success btn-sm rounded-3 px-3">
                                <i class="bi bi-file-earmark-excel me-1"></i> Excel
                            </a>
                            <a href="{{ route('stok-barang.cetak-pdf', $paramsExport) }}"
                               id="btnExportPdf"
                               target="_blank" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================
         STATISTIK RINGKAS
         ================================================================ --}}
    @php
        $totalBarang      = $laporan->count();
        $totalStokMasuk   = $laporan->sum('stok_masuk');
        // Pemakaian: jika ada filter unit, hitung hanya pemakaian unit tsb + minggu tsb
        $totalPemakaianAll = $laporan->sum(function ($l) use ($filterUnit, $filterMinggu, $daftarUnit) {
            if ($filterUnit && in_array($filterUnit, $daftarUnit)) {
                $perUnitMinggu = $l->pemakaianPerUnitMinggu();
                $mingguList = $filterMinggu ? [$filterMinggu] : [1,2,3,4];
                $total = 0;
                foreach ($mingguList as $w) {
                    $total += $perUnitMinggu[$filterUnit][$w] ?? 0;
                }
                return $total;
            }
            return $l->totalPemakaian();
        });
        $totalSisaAll = $totalStokMasuk - $laporan->sum(fn($l) => $l->totalPemakaian());
    @endphp
    @if($totalBarang > 0)
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success-subtle"><i class="bi bi-box-seam text-success fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $totalBarang }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Jenis Barang</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary-subtle"><i class="bi bi-arrow-down-circle text-primary fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ number_format($totalStokMasuk) }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Total Stok Masuk</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning-subtle"><i class="bi bi-arrow-up-circle text-warning fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ number_format($totalPemakaianAll) }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">
                            Pemakaian{{ $filterUnit ? ' '.$filterUnit : '' }}{{ $filterMinggu ? ' Wk'.$filterMinggu : '' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 {{ $totalSisaAll < 0 ? 'bg-danger-subtle' : 'bg-success-subtle' }}">
                        <i class="bi bi-stack {{ $totalSisaAll < 0 ? 'text-danger' : 'text-success' }} fs-5"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 {{ $totalSisaAll < 0 ? 'text-danger' : 'text-dark' }}">{{ number_format($totalSisaAll) }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Total Sisa Stok</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- ================================================================
         STATE KOSONG
         ================================================================ --}}
    @if($laporan->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="bi bi-inbox text-muted" style="font-size:3rem;"></i>
                <p class="text-muted mt-3 mb-1 fw-semibold">Belum ada data laporan untuk periode ini.</p>
                <p class="text-muted mb-3" style="font-size:0.85rem;">
                    Klik <strong>Tambah Barang Baru</strong> untuk mulai menginput stok.
                </p>
                <button class="btn btn-success btn-sm rounded-3" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Barang Baru
                </button>
            </div>
        </div>

    @elseif($barisTabel->isEmpty() && $filterUnit)
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="bi bi-search text-muted" style="font-size:3rem;"></i>
                <p class="text-muted mt-3 mb-1 fw-semibold">Tidak ada pemakaian untuk unit <strong>{{ $filterUnit }}</strong> di periode ini.</p>
                <a href="{{ route('stok-barang.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
                   class="btn btn-outline-secondary btn-sm rounded-3 mt-2">
                    <i class="bi bi-x-circle me-1"></i> Reset Filter
                </a>
            </div>
        </div>

    @else
    {{-- ================================================================
         TABEL UTAMA — RINGKAS & RESPONSIF
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
            <h6 class="fw-bold mb-0 text-success">
                <i class="bi bi-table me-2"></i>
                @if($filterUnit)
                    PEMAKAIAN — {{ $filterUnit }}{{ $filterMinggu ? ' · Week '.$filterMinggu : ' (Semua Minggu)' }}
                @else
                    RINGKASAN STOK BARANG
                @endif
            </h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                {{ $barisTabel->count() }} baris
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive table-scroll-container">
                <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-center" style="width:45px;">No</th>
                            <th style="min-width:180px;">Nama Barang</th>
                            <th style="width:80px;">Kode</th>
                            <th class="text-center" style="width:70px;">Satuan</th>
                            <th class="text-center" style="width:80px;">Tanggal</th>
                            <th class="text-center" style="width:95px;">Stok/Masuk</th>
                            @if($filterUnit)
                                <th class="text-center" style="width:90px;">Unit</th>
                                <th class="text-center" style="width:80px;">Minggu</th>
                                <th class="text-center" style="width:80px;">Pemakaian</th>
                            @else
                                <th class="text-center" style="width:110px;">Total Pemakaian</th>
                            @endif
                            <th class="text-center" style="width:85px;">Sisa Stok</th>
                            <th class="text-center" style="width:90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($barisTabel as $no => $baris)
                            <tr>
                                <td class="text-center text-muted fw-semibold ps-3">{{ $no + 1 }}</td>

                                {{-- Nama Barang --}}
                                <td>
                                    <div class="fw-semibold text-dark">{{ $baris['nama_barang'] }}</div>
                                    @if(!$filterUnit && isset($baris['per_unit']))
                                        {{-- Tooltip ringkasan per unit jika mode semua --}}
                                        @php
                                            $unitAktif = array_filter($baris['per_unit'], fn($v) => $v > 0);
                                        @endphp
                                        @if(count($unitAktif))
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach($unitAktif as $u => $v)
                                                    <span class="badge bg-light text-dark border" style="font-size:0.68rem; font-weight:500;">
                                                        {{ $u }}: {{ $v }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </td>

                                {{-- Kode --}}
                                <td class="text-muted" style="font-size:0.78rem;">{{ $baris['kode_barang'] }}</td>

                                {{-- Satuan --}}
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $baris['satuan'] }}</span>
                                </td>

                                {{-- Tanggal --}}
                                <td class="text-center text-muted" style="font-size:0.78rem;">
                                    {{ $baris['tanggal'] ? $baris['tanggal']->format('d/m/Y') : '—' }}
                                </td>

                                {{-- Stok Masuk --}}
                                <td class="text-center">
                                    <span class="fw-semibold text-primary">{{ number_format($baris['stok_masuk']) }}</span>
                                </td>

                                @if($filterUnit)
                                    {{-- Unit --}}
                                    <td class="text-center">
                                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                                            {{ $baris['unit'] }}
                                        </span>
                                    </td>
                                    {{-- Minggu --}}
                                    <td class="text-center text-muted" style="font-size:0.82rem;">{{ $baris['minggu'] }}</td>
                                    {{-- Pemakaian --}}
                                    <td class="text-center">
                                        <span class="fw-bold {{ $baris['jumlah'] > 0 ? 'text-warning' : 'text-muted' }}">
                                            {{ $baris['jumlah'] }}
                                        </span>
                                    </td>
                                @else
                                    {{-- Total Pemakaian --}}
                                    <td class="text-center">
                                        <span class="fw-bold {{ $baris['total_pemakaian'] > 0 ? 'text-dark' : 'text-muted' }}">
                                            {{ number_format($baris['total_pemakaian']) }}
                                        </span>
                                    </td>
                                @endif

                                {{-- Sisa Stok --}}
                                <td class="text-center">
                                    @php $sisa = $baris['sisa_stok']; @endphp
                                    <span class="fw-bold {{ $sisa < 0 ? 'text-danger' : ($sisa == 0 ? 'text-warning' : 'text-success') }}">
                                        {{ number_format($sisa) }}
                                    </span>
                                    @if($sisa < 0)
                                        <i class="bi bi-exclamation-triangle-fill text-danger ms-1" style="font-size:0.7rem;"
                                           title="Stok negatif!"></i>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        {{-- Tombol input pemakaian (buka modal) --}}
                                        <button class="btn btn-sm btn-outline-success rounded-2 px-2"
                                                onclick="bukaModalPemakaian({{ $baris['laporan_id'] }}, '{{ addslashes($baris['nama_barang']) }}')"
                                                title="Input pemakaian per unit">
                                            <i class="bi bi-pencil-square" style="font-size:0.8rem;"></i>
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm rounded-2 px-2"
                                                onclick="bukaModalEdit({{ $baris['laporan_id'] }}, '{{ addslashes($baris['produk_nama']) }}', '{{ $baris['produk_kode'] }}', '{{ $baris['produk_satuan'] }}', '{{ $baris['tanggal_raw'] }}', {{ $baris['stok_masuk_raw'] }})"
                                                title="Edit info barang">
                                            <i class="bi bi-gear" style="font-size:0.8rem;"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm rounded-2 px-2"
                                                onclick="konfirmasiHapus({{ $baris['laporan_id'] }}, '{{ addslashes($baris['nama_barang']) }}')"
                                                title="Hapus dari periode ini">
                                            <i class="bi bi-trash" style="font-size:0.8rem;"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================================================================
         TABEL REKAP PER UNIT — hanya tampil jika tidak ada filter unit
         (saat filter aktif, data sudah tampil di tabel atas)
         ================================================================ --}}
    @if(!$filterUnit)
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
            <h6 class="fw-bold mb-0 text-primary">
                <i class="bi bi-bar-chart-line me-2"></i>REKAP PEMAKAIAN PER UNIT
                <span class="text-muted fw-normal ms-1" style="font-size:0.75rem;">(otomatis)</span>
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:0.82rem;">
                    <thead class="table-primary">
                        <tr>
                            <th class="ps-3 text-center" style="width:45px;">No</th>
                            <th style="min-width:160px;">Nama Barang</th>
                            <th style="width:75px;">Kode</th>
                            <th class="text-center" style="width:65px;">Satuan</th>
                            @foreach($daftarUnit as $unit)
                                <th class="text-center" style="min-width:65px; white-space:nowrap;">{{ $unit }}</th>
                            @endforeach
                            <th class="text-center fw-bold" style="width:70px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporan as $no => $item)
                            @php
                                $perUnit    = $item->pemakaianPerUnit();
                                $totalRekap = array_sum($perUnit);
                            @endphp
                            <tr>
                                <td class="text-center text-muted ps-3">{{ $no + 1 }}</td>
                                <td class="fw-semibold text-dark">{{ $item->produk->nama_barang }}</td>
                                <td class="text-muted" style="font-size:0.75rem;">{{ $item->produk->kode_barang }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border">{{ $item->produk->satuan }}</span>
                                </td>
                                @foreach($daftarUnit as $unit)
                                    @php $val = $perUnit[$unit] ?? 0; @endphp
                                    <td class="text-center {{ $val > 0 ? 'fw-semibold text-dark' : 'text-muted' }}">
                                        {{ $val ?: '—' }}
                                    </td>
                                @endforeach
                                <td class="text-center fw-bold text-primary">{{ $totalRekap ?: '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    @endif {{-- end else --}}
</div>

{{-- ================================================================
     MODAL: INPUT PEMAKAIAN PER UNIT (menggantikan input inline di tabel)
     ================================================================ --}}
<div class="modal fade" id="modalPemakaian" tabindex="-1" aria-labelledby="labelPemakaian" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h5 class="modal-title fw-bold" id="labelPemakaian">
                        <i class="bi bi-pencil-square text-success me-2"></i>Input Pemakaian
                    </h5>
                    <div class="text-muted" id="subLabelPemakaian" style="font-size:0.82rem;"></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3" id="bodyModalPemakaian">
                {{-- Diisi oleh JS --}}
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- ================================================================
     MODAL: TAMBAH BARANG BARU
     ================================================================ --}}
<div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-labelledby="labelTambahBarang" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form method="POST" action="{{ route('stok-barang.simpan-barang', ['bulan' => $bulan, 'tahun' => $tahun]) }}">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="labelTambahBarang">
                        <i class="bi bi-plus-circle text-success me-2"></i>Tambah Barang Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control rounded-3"
                               placeholder="cth: Bayclin 1L" required maxlength="150">
                    </div>
                    <div class="row g-3">
                        <div class="col-8">
                            <label class="form-label fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" name="kode_barang" id="inputKodeBarang"
                                       class="form-control rounded-start-3"
                                       placeholder="BRG-0001" required maxlength="30">
                                <button type="button" class="btn btn-outline-secondary rounded-end-3"
                                        onclick="generateKode()" title="Generate otomatis">
                                    <i class="bi bi-magic"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan" class="form-select rounded-3" required>
                                <option value="">Pilih</option>
                                @foreach($daftarSatuan as $s)
                                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control rounded-3" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Stok / Masuk <span class="text-danger">*</span></label>
                            <input type="number" name="stok_masuk" class="form-control rounded-3" placeholder="0" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-3"><i class="bi bi-check-lg me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================================
     MODAL: TAMBAH DARI MASTER
     ================================================================ --}}
<div class="modal fade" id="modalTambahPeriode" tabindex="-1" aria-labelledby="labelTambahPeriode" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form method="POST" action="{{ route('stok-barang.tambah-ke-periode', ['bulan' => $bulan, 'tahun' => $tahun]) }}">
                @csrf
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="labelTambahPeriode">
                        <i class="bi bi-list-check text-primary me-2"></i>Tambah dari Master Barang
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Barang <span class="text-danger">*</span></label>
                        <select name="produk_id" class="form-select rounded-3" required>
                            <option value="">— Pilih Barang —</option>
                            @foreach($semuaProduk as $produk)
                                <option value="{{ $produk->id }}">{{ $produk->nama_barang }} ({{ $produk->kode_barang }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control rounded-3" value="{{ now()->toDateString() }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Stok / Masuk <span class="text-danger">*</span></label>
                            <input type="number" name="stok_masuk" class="form-control rounded-3" placeholder="0" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3"><i class="bi bi-check-lg me-1"></i> Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================================
     MODAL: EDIT INFO BARANG
     ================================================================ --}}
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="labelEdit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form method="POST" id="formEdit" action="">
                @csrf @method('PUT')
                <input type="hidden" name="bulan" value="{{ $bulan }}">
                <input type="hidden" name="tahun" value="{{ $tahun }}">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold" id="labelEdit">
                        <i class="bi bi-gear text-primary me-2"></i>Edit Info Barang
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" id="editNamaBarang" class="form-control rounded-3" required maxlength="150">
                    </div>
                    <div class="row g-3">
                        <div class="col-8">
                            <label class="form-label fw-semibold">Kode Barang <span class="text-danger">*</span></label>
                            <input type="text" name="kode_barang" id="editKodeBarang" class="form-control rounded-3" required maxlength="30">
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                            <select name="satuan" id="editSatuan" class="form-select rounded-3" required>
                                @foreach($daftarSatuan as $s)
                                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 mt-1">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="date" name="tanggal" id="editTanggal" class="form-control rounded-3">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Stok / Masuk <span class="text-danger">*</span></label>
                            <input type="number" name="stok_masuk" id="editStokMasuk" class="form-control rounded-3" min="0" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3"><i class="bi bi-check-lg me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================================
     MODAL: KONFIRMASI HAPUS
     ================================================================ --}}
<div class="modal fade" id="modalHapus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger"><i class="bi bi-trash me-2"></i>Hapus Barang</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-0">Hapus <strong id="namaBarangHapus"></strong> dari laporan periode ini? Data pemakaian juga akan terhapus.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3 btn-sm" data-bs-dismiss="modal">Batal</button>
                <form id="formHapus" method="POST" action="">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-3 btn-sm"><i class="bi bi-trash me-1"></i> Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Toast peringatan stok --}}
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100;">
    <div id="toastPeringatan" class="toast align-items-center text-bg-warning border-0 rounded-3 shadow" role="alert" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-semibold" id="toastPesanPeringatan">
                <i class="bi bi-exclamation-triangle me-2"></i>Peringatan stok.
            </div>
            <button type="button" class="btn-close btn-close-dark me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

{{-- ================================================================
     STYLES
     ================================================================ --}}
<style>
    .table > :not(caption) > * > * { padding: 0.5rem 0.65rem; }
    .table tbody tr:hover td { background-color: #f8fffe !important; }
</style>

{{-- ================================================================
     JAVASCRIPT
     ================================================================ --}}
<script>
const CSRF_TOKEN = '{{ csrf_token() }}';
// Gunakan route() helper — tidak fragile terhadap perubahan prefix
const ROUTE_PEMAKAIAN    = (id) => `{{ url('/') }}/stok-barang/laporan/${id}/pemakaian`;
const ROUTE_PEMAKAIAN_DATA = (id) => `{{ url('/') }}/stok-barang/laporan/${id}/pemakaian-data`;
const ROUTE_LAPORAN      = (id) => `{{ url('/') }}/stok-barang/laporan/${id}`;
const ROUTE_EXCEL_BASE   = '{{ route("stok-barang.export-excel") }}';
const ROUTE_PDF_BASE     = '{{ route("stok-barang.cetak-pdf") }}';
const DAFTAR_UNIT = @json($daftarUnit);

// ── Aktifkan/nonaktifkan select Minggu berdasarkan Unit ──────────
(() => {
    const selUnit   = document.querySelector('select[name="unit"]');
    const selMinggu = document.getElementById('selectMinggu');
    if (!selUnit || !selMinggu) return;
    selUnit.addEventListener('change', () => {
        selMinggu.disabled = (selUnit.value === '');
        if (selUnit.value === '') selMinggu.value = '0';
        syncExportLinks();
    });
    selMinggu.addEventListener('change', syncExportLinks);
})();

// ── Sinkronisasi link Export dengan nilai filter saat ini ────────
function syncExportLinks() {
    const form   = document.getElementById('formFilter');
    if (!form) return;
    const bulan  = form.querySelector('[name="bulan"]')?.value  || '{{ $bulan }}';
    const tahun  = form.querySelector('[name="tahun"]')?.value  || '{{ $tahun }}';
    const unit   = form.querySelector('[name="unit"]')?.value   || '';
    const minggu = form.querySelector('[name="minggu"]')?.value || '0';

    const params = new URLSearchParams({ bulan, tahun });
    if (unit)                    params.set('unit', unit);
    if (minggu && minggu !== '0') params.set('minggu', minggu);

    const qs = params.toString();
    const btnExcel = document.getElementById('btnExportExcel');
    const btnPdf   = document.getElementById('btnExportPdf');
    if (btnExcel) btnExcel.href = ROUTE_EXCEL_BASE + '?' + qs;
    if (btnPdf)   btnPdf.href   = ROUTE_PDF_BASE   + '?' + qs;
}

// Sinkronisasi awal saat halaman dimuat
syncExportLinks();

// ── Debounce ────────────────────────────────────────────────────
function debounce(fn, delay) {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
}

// ── Toast peringatan ─────────────────────────────────────────────
function tampilkanToast(pesan, tipe = 'warning') {
    const el = document.getElementById('toastPeringatan');
    el.className = `toast align-items-center border-0 rounded-3 shadow text-bg-${tipe}`;
    document.getElementById('toastPesanPeringatan').innerHTML =
        `<i class="bi bi-${tipe === 'danger' ? 'x-octagon' : 'exclamation-triangle'} me-2"></i>` + pesan;
    new bootstrap.Toast(el, { delay: 5000 }).show();
}

// ── AJAX simpan pemakaian ────────────────────────────────────────
const handlePemakaianChange = debounce(async function(input) {
    const laporanId = input.dataset.laporanId;
    const unit      = input.dataset.unit;
    const minggu    = input.dataset.minggu;
    const jumlah    = Math.max(0, parseInt(input.value) || 0);
    input.value = jumlah;
    input.classList.add('border-warning');
    input.classList.remove('border-danger', 'border-success', 'bg-danger-subtle');

    try {
        const resp = await fetch(ROUTE_PEMAKAIAN(laporanId), {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ unit, minggu, jumlah }),
        });
        const data = await resp.json();
        input.classList.remove('border-warning');

        if (resp.status === 422) {
            tampilkanToast(data.pesan || 'Pemakaian melebihi stok!');
            input.classList.add('border-danger', 'bg-danger-subtle');
            return;
        }
        if (data.status === 'sukses') {
            input.classList.add('border-success');
            setTimeout(() => input.classList.remove('border-success'), 1500);
        }
    } catch (e) {
        input.classList.remove('border-warning');
        input.classList.add('border-danger');
        tampilkanToast('Gagal menyimpan. Periksa koneksi.', 'danger');
    }
}, 600);

// ── MODAL PEMAKAIAN — bangun grid input per unit per minggu ──────
function bukaModalPemakaian(laporanId, namaBarang) {
    document.getElementById('subLabelPemakaian').textContent = namaBarang;
    const body = document.getElementById('bodyModalPemakaian');

    // Tampilkan spinner dulu
    body.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-success mb-2"></div>
            <div class="text-muted" style="font-size:0.82rem;">Memuat data pemakaian...</div>
        </div>`;

    new bootstrap.Modal(document.getElementById('modalPemakaian')).show();

    // Fetch data pemakaian yang sudah tersimpan DULU, baru render grid
    fetch(ROUTE_PEMAKAIAN_DATA(laporanId), {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF_TOKEN }
    })
    .then(r => {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(data => renderGridPemakaian(body, laporanId, data))
    .catch(err => {
        // Gagal load data — tetap tampilkan grid kosong dengan peringatan
        renderGridPemakaian(body, laporanId, {});
        body.insertAdjacentHTML('afterbegin',
            `<div class="alert alert-warning rounded-3 border-0 mb-3 py-2" style="font-size:0.82rem;">
                <i class="bi bi-exclamation-triangle me-1"></i>
                Gagal memuat data tersimpan. Data yang ada mungkin tidak tampil — input tetap bisa digunakan.
            </div>`
        );
    });
}

function renderGridPemakaian(body, laporanId, savedData) {
    let html = `<div class="table-responsive">
        <table class="table table-bordered table-sm align-middle mb-0" style="font-size:0.82rem;">
        <thead class="table-success text-center">
            <tr>
                <th class="text-start" style="min-width:90px;">Unit / Lokasi</th>
                <th>Week 1</th><th>Week 2</th><th>Week 3</th><th>Week 4</th>
                <th>Sub Total</th>
            </tr>
        </thead>
        <tbody>`;

    DAFTAR_UNIT.forEach(unit => {
        const safeUnit = unit.replace(/\s/g, '_');
        html += `<tr><td class="fw-semibold">${unit}</td>`;
        [1,2,3,4].forEach(w => {
            const val = savedData[unit]?.[w] ?? 0;
            html += `<td class="p-1 text-center">
                <input type="number" min="0" value="${val}"
                    class="form-control form-control-sm text-center rounded-2 input-pemakaian-modal"
                    style="width:62px; margin:auto;"
                    data-laporan-id="${laporanId}"
                    data-unit="${unit}"
                    data-minggu="${w}">
            </td>`;
        });
        html += `<td class="text-center fw-bold text-muted" id="sub-${laporanId}-${safeUnit}">0</td></tr>`;
    });

    html += `</tbody></table></div>
        <div class="mt-3 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
            <span class="text-muted" style="font-size:0.82rem;"><i class="bi bi-check-circle text-success me-1"></i>Perubahan tersimpan otomatis.</span>
            <span class="fw-bold text-dark">Total: <span id="grand-total-${laporanId}">0</span></span>
        </div>`;

    body.innerHTML = html;

    body.querySelectorAll('.input-pemakaian-modal').forEach(input => {
        input.addEventListener('input', () => {
            handlePemakaianChange(input);
            hitungSubtotalModal(laporanId);
        });
    });

    hitungSubtotalModal(laporanId);
}

function hitungSubtotalModal(laporanId) {
    let grandTotal = 0;
    DAFTAR_UNIT.forEach(unit => {
        let sub = 0;
        document.querySelectorAll(
            `.input-pemakaian-modal[data-laporan-id="${laporanId}"][data-unit="${unit}"]`
        ).forEach(i => { sub += parseInt(i.value) || 0; });
        grandTotal += sub;
        const safeUnit = unit.replace(/\s/g, '_');
        const el = document.getElementById(`sub-${laporanId}-${safeUnit}`);
        if (el) {
            el.textContent = sub;
            el.className = `text-center fw-bold ${sub > 0 ? 'text-success' : 'text-muted'}`;
        }
    });
    const gt = document.getElementById(`grand-total-${laporanId}`);
    if (gt) gt.textContent = grandTotal;
}

// ── MODAL EDIT ───────────────────────────────────────────────────
function bukaModalEdit(id, nama, kode, satuan, tanggal, stok) {
    document.getElementById('formEdit').action = ROUTE_LAPORAN(id);
    document.getElementById('editNamaBarang').value = nama;
    document.getElementById('editKodeBarang').value = kode;
    document.getElementById('editTanggal').value    = tanggal || '';
    document.getElementById('editStokMasuk').value  = stok;
    const sel = document.getElementById('editSatuan');
    for (let opt of sel.options) opt.selected = (opt.value === satuan);
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}

// ── MODAL HAPUS ──────────────────────────────────────────────────
function konfirmasiHapus(id, nama) {
    document.getElementById('namaBarangHapus').textContent = nama;
    document.getElementById('formHapus').action = ROUTE_LAPORAN(id);
    new bootstrap.Modal(document.getElementById('modalHapus')).show();
}

// ── GENERATE KODE OTOMATIS ───────────────────────────────────────
async function generateKode() {
    try {
        const resp = await fetch('{{ route("stok-barang.kode-otomatis") }}');
        const data = await resp.json();
        document.getElementById('inputKodeBarang').value = data.kode;
    } catch (e) {
        tampilkanToast('Gagal generate kode otomatis.', 'danger');
    }
}
</script>
@endsection
