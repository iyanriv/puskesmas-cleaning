@extends('tata-letak.aplikasi')

@section('title', 'Stok Masuk / Restok')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-box-arrow-in-down text-success me-2"></i>Stok Masuk / Restok
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                Catat setiap pengiriman barang dari pasokan logistik &mdash; stok gudang akan otomatis bertambah.
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('stok-keluar.index') }}" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> Laporan Barang Keluar
            </a>
            <a href="{{ route('stok-barang.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-clipboard2-data me-1"></i> Laporan Stok
            </a>
        </div>
    </div>

    @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('gagal'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('gagal') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================================================================
         STATISTIK RINGKAS
         ================================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 text-dark">{{ number_format($totalJenisBarang) }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Jenis Barang</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 text-primary">{{ number_format($totalStok) }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Total Stok Gudang</div>
                </div>
            </div>
        </div>
        <div class="col-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 text-success">{{ $stokAman }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Stok Aman</div>
                </div>
            </div>
        </div>
        <div class="col-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 text-warning">{{ $stokMenipis }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Stok Menipis</div>
                </div>
            </div>
        </div>
        <div class="col-4 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 text-danger">{{ $stokHabis }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Stok Habis</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-2">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 text-center">
                    <div class="fw-bold fs-4 text-success">+{{ number_format($totalMasuk) }}</div>
                    <div class="text-muted" style="font-size:0.75rem;">Masuk (Periode)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- ================================================================
             KOLOM KIRI: FORM STOK MASUK + PENYESUAIAN
             ================================================================ --}}
        <div class="col-lg-4">

            {{-- Form Stok Masuk / Restok --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-success text-white py-3 px-4 rounded-top-4">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-box-arrow-in-down me-2"></i>Catat Stok Masuk / Restok
                    </h6>
                    <div style="font-size:0.75rem; opacity:0.85;" class="mt-1">
                        Stok barang akan otomatis bertambah
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('stok-masuk.store') }}" method="POST" id="formStokMasuk">
                        @csrf
                        {{-- Pilih Barang (Dropdown dengan Pencarian di Dalam) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.85rem;">
                                <i class="bi bi-box me-1 text-success"></i>Barang <span class="text-danger">*</span>
                            </label>

                            <div class="dropdown custom-barang-dropdown position-relative" id="dropdownBarang">
                                <input type="hidden" name="barang_id" id="smBarangId" value="{{ old('barang_id') }}" required>

                                <button type="button" 
                                        class="form-select form-select-sm rounded-3 text-start @error('barang_id') is-invalid @enderror" 
                                        id="btnDropdownBarang" 
                                        data-bs-toggle="dropdown" 
                                        data-bs-auto-close="true"
                                        aria-expanded="false">
                                    <span id="selectedBarangNama" class="text-truncate">— Pilih Barang —</span>
                                </button>

                                <div class="dropdown-menu p-2 shadow rounded-3 w-100" style="min-width: 100%; max-height: 360px;" aria-labelledby="btnDropdownBarang">
                                    {{-- Input Pencarian di dalam dropdown --}}
                                    <div class="p-1 mb-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0 text-muted">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" id="cariBarangInput" 
                                                   class="form-control form-control-sm border-start-0" 
                                                   placeholder="Ketik untuk mencari barang..." 
                                                   autocomplete="off">
                                        </div>
                                    </div>

                                    {{-- List Barang di dalam dropdown --}}
                                    <div id="listBarangContainer" class="list-group list-group-flush" style="max-height: 240px; overflow-y: auto;">
                                        @foreach($daftarBarang as $b)
                                            @php
                                                $status = $b->statusStok();
                                                $statusLabel = $status === 'habis' ? 'HABIS' : ($status === 'menipis' ? 'Menipis: '.$b->stok_saat_ini : 'Stok: '.$b->stok_saat_ini);
                                                $badgeClass = $status === 'habis' ? 'bg-danger-subtle text-danger border border-danger-subtle' : ($status === 'menipis' ? 'bg-warning-subtle text-warning border border-warning-subtle' : 'bg-success-subtle text-success border border-success-subtle');
                                            @endphp
                                            <button type="button" 
                                                    class="list-group-item list-group-item-action py-2 px-2 border-0 rounded-2 item-barang d-flex justify-content-between align-items-center"
                                                    data-id="{{ $b->id }}"
                                                    data-nama="{{ $b->nama_barang }}"
                                                    data-stok="{{ $b->stok_saat_ini }}"
                                                    data-satuan="{{ $b->satuan }}"
                                                    data-status="{{ $status }}"
                                                    data-search="{{ strtolower($b->nama_barang . ' ' . $b->kategori . ' ' . $b->satuan) }}">
                                                <div class="text-truncate me-2 text-start">
                                                    <span class="fw-semibold text-dark">{{ $b->nama_barang }}</span>
                                                    <small class="text-muted d-block" style="font-size:0.75rem;">{{ $b->satuan }}</small>
                                                </div>
                                                <span class="badge {{ $badgeClass }} flex-shrink-0" style="font-size:0.75rem;">
                                                    {{ $statusLabel }}
                                                </span>
                                            </button>
                                        @endforeach
                                        <div id="pesanTidakAdaBarang" class="text-center py-3 text-muted d-none" style="font-size:0.8rem;">
                                            <i class="bi bi-info-circle me-1"></i>Barang tidak ditemukan
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('barang_id')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
                            <div id="invalidBarangMsg" class="text-danger mt-1 d-none" style="font-size:0.8rem;">Pilih barang yang akan direstok.</div>

                            {{-- Info stok saat ini --}}
                            <div id="infoStokSaatIni" class="mt-2 d-none">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-light">
                                    <i class="bi bi-info-circle text-secondary"></i>
                                    <span id="labelStokSaatIni" style="font-size:0.8rem;"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Tanggal --}}
                        <div class="mb-3">
                            <label for="smTanggal" class="form-label fw-semibold" style="font-size:0.85rem;">
                                <i class="bi bi-calendar me-1 text-success"></i>Tanggal Masuk <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal" id="smTanggal"
                                   class="form-control form-control-sm rounded-3 @error('tanggal') is-invalid @enderror"
                                   value="{{ old('tanggal', now()->toDateString()) }}" required>
                            @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- Jumlah Masuk --}}
                        <div class="mb-3">
                            <label for="smJumlah" class="form-label fw-semibold" style="font-size:0.85rem;">
                                <i class="bi bi-123 me-1 text-success"></i>Jumlah Masuk <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="jumlah_masuk" id="smJumlah"
                                       class="form-control rounded-start-3 @error('jumlah_masuk') is-invalid @enderror"
                                       value="{{ old('jumlah_masuk', 1) }}" min="1" required>
                                <span class="input-group-text rounded-end-3 fw-semibold" id="spanSatuan" style="min-width:55px;">—</span>
                            </div>
                            @error('jumlah_masuk')<div class="text-danger mt-1" style="font-size:0.8rem;">{{ $message }}</div>@enderror
                        </div>

                        {{-- Keterangan --}}
                        <div class="mb-4">
                            <label for="smKeterangan" class="form-label fw-semibold" style="font-size:0.85rem;">
                                <i class="bi bi-chat-left-text me-1 text-success"></i>Keterangan
                            </label>
                            <textarea name="keterangan" id="smKeterangan" rows="2"
                                      class="form-control form-control-sm rounded-3"
                                      placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100 rounded-3 fw-semibold">
                            <i class="bi bi-box-arrow-in-down me-2"></i>Simpan Stok Masuk
                        </button>
                    </form>
                </div>
            </div>

            {{-- Form Penyesuaian Stok (Admin & Gudang) --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <h6 class="fw-bold mb-0 text-warning">
                        <i class="bi bi-sliders me-2"></i>Penyesuaian Stok
                    </h6>
                    <div class="text-muted mt-1" style="font-size:0.75rem;">
                        Koreksi stok karena selisih opname fisik
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('stok-masuk.penyesuaian') }}" method="POST" id="formPenyesuaian">
                        @csrf

                        {{-- Pilih Barang (sama dengan form restok — dengan pencarian) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.85rem;">
                                <i class="bi bi-box me-1 text-warning"></i>Barang <span class="text-danger">*</span>
                            </label>

                            <div class="dropdown position-relative" id="dropdownPenyesuaian">
                                <input type="hidden" name="barang_id" id="psBarangId" required>

                                <button type="button"
                                        class="form-select form-select-sm rounded-3 text-start"
                                        id="btnDropdownPs"
                                        data-bs-auto-close="true"
                                        aria-expanded="false">
                                    <span id="selectedPsNama" class="text-truncate">— Pilih Barang —</span>
                                </button>

                                <div class="dropdown-menu p-2 shadow rounded-3 w-100"
                                     id="menuPs"
                                     style="min-width:100%; max-height:360px; display:none;">
                                    <div class="p-1 mb-2">
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-white border-end-0 text-muted">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input type="text" id="cariPsInput"
                                                   class="form-control form-control-sm border-start-0"
                                                   placeholder="Ketik untuk mencari barang..."
                                                   autocomplete="off">
                                        </div>
                                    </div>
                                    <div id="listPsContainer" style="max-height:240px; overflow-y:auto;">
                                        @foreach($daftarBarang as $b)
                                            @php
                                                $st = $b->statusStok();
                                                $slbl = $st === 'habis' ? 'HABIS' : ($st === 'menipis' ? 'Menipis: '.$b->stok_saat_ini : 'Stok: '.$b->stok_saat_ini);
                                                $scls = $st === 'habis' ? 'bg-danger-subtle text-danger border-danger-subtle' : ($st === 'menipis' ? 'bg-warning-subtle text-warning border-warning-subtle' : 'bg-success-subtle text-success border-success-subtle');
                                            @endphp
                                            <button type="button"
                                                    class="list-group-item list-group-item-action py-2 px-2 border-0 rounded-2 item-ps-barang d-flex justify-content-between align-items-center"
                                                    data-id="{{ $b->id }}"
                                                    data-nama="{{ $b->nama_barang }}"
                                                    data-stok="{{ $b->stok_saat_ini }}"
                                                    data-satuan="{{ $b->satuan }}"
                                                    data-search="{{ strtolower($b->nama_barang . ' ' . $b->kategori . ' ' . $b->satuan) }}">
                                                <div class="text-truncate me-2 text-start">
                                                    <span class="fw-semibold text-dark">{{ $b->nama_barang }}</span>
                                                    <small class="text-muted d-block" style="font-size:0.75rem;">{{ $b->satuan }}</small>
                                                </div>
                                                <span class="badge {{ $scls }} border flex-shrink-0" style="font-size:0.75rem;">{{ $slbl }}</span>
                                            </button>
                                        @endforeach
                                        <div id="psTidakAda" class="text-center py-3 text-muted d-none" style="font-size:0.8rem;">
                                            <i class="bi bi-info-circle me-1"></i>Barang tidak ditemukan
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Info stok saat ini --}}
                            <div id="infoPsStok" class="mt-2 d-none">
                                <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-light">
                                    <i class="bi bi-info-circle text-secondary"></i>
                                    <span id="labelPsStok" style="font-size:0.8rem;"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.85rem;">
                                <i class="bi bi-calendar me-1 text-warning"></i>Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal" class="form-control form-control-sm rounded-3"
                                   value="{{ now()->toDateString() }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size:0.85rem;">
                                <i class="bi bi-123 me-1 text-warning"></i>Stok Baru (Hasil Opname) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="stok_baru" id="psStokBaru"
                                       class="form-control rounded-start-3"
                                       min="0" placeholder="Masukkan angka stok hasil opname fisik" required>
                                <span class="input-group-text rounded-end-3 fw-semibold" id="spanPsSatuan" style="min-width:55px;">—</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size:0.85rem;">
                                <i class="bi bi-chat-left-text me-1 text-warning"></i>Keterangan <span class="text-danger">*</span>
                            </label>
                            <textarea name="keterangan" rows="2" class="form-control form-control-sm rounded-3"
                                      placeholder="Alasan penyesuaian / hasil opname tanggal..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 rounded-3 fw-semibold"
                                onclick="return confirm('Yakin ingin menyesuaikan stok? Perubahan ini akan tercatat di riwayat mutasi.')">
                            <i class="bi bi-sliders me-2"></i>Simpan Penyesuaian
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================================================================
             KOLOM KANAN: RIWAYAT MUTASI STOK
             ================================================================ --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-journal-text me-2 text-primary"></i>Riwayat Mutasi Stok
                        </h6>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:0.75rem;">
                            {{ $riwayat->total() }} transaksi
                        </span>
                    </div>

                    {{-- Filter --}}
                    <form method="GET" class="mt-3 row g-2">
                        <div class="col-12 col-md-4">
                            <select name="barang_id" class="form-select form-select-sm rounded-3">
                                <option value="">Semua Barang</option>
                                @foreach($daftarBarang as $b)
                                    <option value="{{ $b->id }}" {{ $filterBarangId == $b->id ? 'selected' : '' }}>
                                        {{ $b->nama_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <select name="jenis_transaksi" class="form-select form-select-sm rounded-3">
                                <option value="">Semua Jenis</option>
                                @foreach($jenisTransaksi as $val => $label)
                                    <option value="{{ $val }}" {{ $filterJenis === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <input type="date" name="dari" class="form-control form-control-sm rounded-3" value="{{ $filterDari }}">
                        </div>
                        <div class="col-6 col-md-2">
                            <input type="date" name="sampai" class="form-control form-control-sm rounded-3" value="{{ $filterSampai }}">
                        </div>
                        <div class="col-6 col-md-2 d-flex gap-1">
                            <button type="submit" class="btn btn-sm btn-primary rounded-3 flex-fill">
                                <i class="bi bi-funnel"></i>
                            </button>
                            <a href="{{ route('stok-masuk.index') }}" class="btn btn-sm btn-outline-secondary rounded-3">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive table-scroll-container">
                        <table class="table table-hover align-middle mb-0" style="font-size:0.82rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="min-width:110px;">No. Transaksi</th>
                                    <th style="min-width:120px;">Tanggal</th>
                                    <th style="min-width:150px;">Barang</th>
                                    <th class="text-center" style="width:110px;">Jenis</th>
                                    <th class="text-center" style="width:70px;">Masuk</th>
                                    <th class="text-center" style="width:70px;">Keluar</th>
                                    <th class="text-center" style="width:70px;">Saldo</th>
                                    <th style="min-width:120px;">Keterangan</th>
                                    <th style="min-width:90px;">Petugas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayat as $m)
                                    <tr>
                                        <td class="ps-3">
                                            <span class="font-monospace text-secondary" style="font-size:0.78rem;">{{ $m->nomor_transaksi }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $m->tanggal->format('d/m/Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $m->barang?->nama_barang ?? '-' }}</div>
                                            <div class="text-muted" style="font-size:0.74rem;">{{ $m->barang?->satuan ?? '' }}</div>
                                        </td>
                                        <td class="text-center">{!! $m->badgeJenisTransaksi() !!}</td>
                                        <td class="text-center">
                                            @if($m->jumlah_masuk > 0)
                                                <span class="fw-bold text-success">+{{ number_format($m->jumlah_masuk) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($m->jumlah_keluar > 0)
                                                <span class="fw-bold text-danger">-{{ number_format($m->jumlah_keluar) }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-bold text-dark">{{ number_format($m->saldo_setelahnya) }}</span>
                                        </td>
                                        <td>
                                            <div style="max-width:200px; font-size:0.78rem;" class="text-truncate" title="{{ $m->keterangan }}">
                                                {{ $m->keterangan ?: ($m->sumber_pasokan ? 'Pasokan: ' . $m->sumber_pasokan : '—') }}
                                            </div>
                                            @if($m->nomor_referensi)
                                                <div class="text-muted font-monospace" style="font-size:0.73rem;">{{ $m->nomor_referensi }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-size:0.78rem;" class="text-dark">{{ $m->petugas?->name ?? '-' }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="bi bi-journal-x text-muted" style="font-size:2.5rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Belum ada riwayat mutasi stok pada periode ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($riwayat->hasPages())
                        <div class="p-3 border-top d-flex justify-content-end">
                            {{ $riwayat->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.custom-barang-dropdown .item-barang:hover {
    background-color: #f0fdf4 !important;
}
.custom-barang-dropdown .dropdown-menu {
    border-color: #e5e7eb;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const dropdownEl    = document.getElementById('dropdownBarang');
    const btnDropdown   = document.getElementById('btnDropdownBarang');
    const dropdownMenu  = dropdownEl ? dropdownEl.querySelector('.dropdown-menu') : null;
    const cariInput     = document.getElementById('cariBarangInput');
    const smBarangId    = document.getElementById('smBarangId');
    const selectedLabel = document.getElementById('selectedBarangNama');
    const spanSatuan    = document.getElementById('spanSatuan');
    const infoStok      = document.getElementById('infoStokSaatIni');
    const labelStok     = document.getElementById('labelStokSaatIni');
    const itemsBarang   = document.querySelectorAll('.item-barang');
    const pesanTidakAda = document.getElementById('pesanTidakAdaBarang');
    const formStokMasuk = document.getElementById('formStokMasuk');
    const invalidMsg    = document.getElementById('invalidBarangMsg');

    if (!dropdownEl || !btnDropdown || !dropdownMenu) return;

    function bukaDropdown() {
        dropdownMenu.classList.add('show');
        btnDropdown.classList.add('show');
        btnDropdown.setAttribute('aria-expanded', 'true');
        setTimeout(() => {
            if (cariInput) {
                cariInput.focus();
                cariInput.select();
            }
        }, 50);
    }

    function tutupDropdown() {
        dropdownMenu.classList.remove('show');
        btnDropdown.classList.remove('show');
        btnDropdown.setAttribute('aria-expanded', 'false');
        if (cariInput) {
            cariInput.value = '';
        }
        itemsBarang.forEach(item => item.classList.remove('d-none'));
        if (pesanTidakAda) pesanTidakAda.classList.add('d-none');
    }

    btnDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
        if (dropdownMenu.classList.contains('show')) {
            tutupDropdown();
        } else {
            bukaDropdown();
        }
    });

    document.addEventListener('click', function (e) {
        if (!dropdownEl.contains(e.target)) {
            tutupDropdown();
        }
    });

    function pilihBarang(item) {
        if (!item) return;
        const id     = item.dataset.id;
        const nama   = item.dataset.nama;
        const stok   = item.dataset.stok;
        const satuan = item.dataset.satuan;
        const status = item.dataset.status;

        smBarangId.value = id;
        selectedLabel.innerHTML = `<span class="fw-semibold text-dark">${nama}</span> <span class="text-muted" style="font-size:0.78rem;">(Stok: ${stok} ${satuan})</span>`;
        if (spanSatuan) spanSatuan.textContent = satuan || '—';

        if (labelStok && infoStok) {
            const warna = status === 'habis' ? 'text-danger' : (status === 'menipis' ? 'text-warning' : 'text-success');
            const ikon  = status === 'habis' ? 'bi-x-circle' : (status === 'menipis' ? 'bi-exclamation-triangle' : 'bi-check-circle');
            labelStok.innerHTML = `Stok saat ini: <strong class="${warna}"><i class="bi ${ikon} me-1"></i>${stok} ${satuan}</strong>`;
            infoStok.classList.remove('d-none');
        }

        // Tandai item terpilih
        itemsBarang.forEach(i => i.classList.remove('active', 'bg-light'));
        item.classList.add('bg-light');

        if (btnDropdown) btnDropdown.classList.remove('is-invalid');
        if (invalidMsg) invalidMsg.classList.add('d-none');

        tutupDropdown();
    }

    // Klik pada item barang
    itemsBarang.forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            pilihBarang(this);
        });
    });

    if (cariInput) {
        // Mencegah dropdown tertutup saat mengklik input search
        cariInput.addEventListener('click', function (e) {
            e.stopPropagation();
        });

        // Filter real-time di dalam dropdown
        cariInput.addEventListener('input', function () {
            const q = this.value.trim().toLowerCase();
            let adaCocok = 0;

            itemsBarang.forEach(item => {
                const searchData = (item.dataset.search || '').toLowerCase();
                if (!q || searchData.includes(q)) {
                    item.classList.remove('d-none');
                    adaCocok++;
                } else {
                    item.classList.add('d-none');
                }
            });

            if (pesanTidakAda) {
                if (adaCocok === 0) {
                    pesanTidakAda.classList.remove('d-none');
                } else {
                    pesanTidakAda.classList.add('d-none');
                }
            }
        });

        // Tekan Enter untuk memilih hasil teratas dan pindah ke input jumlah
        cariInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const visibleItem = document.querySelector('.item-barang:not(.d-none)');
                if (visibleItem) {
                    pilihBarang(visibleItem);
                    const smJumlah = document.getElementById('smJumlah');
                    if (smJumlah) {
                        smJumlah.focus();
                        smJumlah.select();
                    }
                }
            } else if (e.key === 'Escape') {
                tutupDropdown();
            }
        });
    }

    // Validasi form saat submit
    if (formStokMasuk) {
        formStokMasuk.addEventListener('submit', function (e) {
            if (!smBarangId || !smBarangId.value) {
                e.preventDefault();
                if (btnDropdown) {
                    btnDropdown.classList.add('is-invalid');
                    btnDropdown.focus();
                }
                if (invalidMsg) invalidMsg.classList.remove('d-none');
            }
        });
    }

    // Inisialisasi awal jika ada old('barang_id')
    if (smBarangId && smBarangId.value) {
        const defaultItem = document.querySelector(`.item-barang[data-id="${smBarangId.value}"]`);
        if (defaultItem) pilihBarang(defaultItem);
    }
});

// ================================================================
// DROPDOWN PENYESUAIAN STOK — logic terpisah
// ================================================================
(function () {
    const btnPs     = document.getElementById('btnDropdownPs');
    const menuPs    = document.getElementById('menuPs');
    const cariPs    = document.getElementById('cariPsInput');
    const psId      = document.getElementById('psBarangId');
    const psLabel   = document.getElementById('selectedPsNama');
    const spanPsSat = document.getElementById('spanPsSatuan');
    const infoPsStok= document.getElementById('infoPsStok');
    const labelPsStok = document.getElementById('labelPsStok');
    const itemsPs   = document.querySelectorAll('.item-ps-barang');
    const psTidakAda = document.getElementById('psTidakAda');

    if (!btnPs || !menuPs) return;

    function bukaPs() {
        menuPs.style.display = 'block';
        btnPs.setAttribute('aria-expanded', 'true');
        setTimeout(() => { if (cariPs) { cariPs.focus(); cariPs.select(); } }, 50);
    }

    function tutupPs() {
        menuPs.style.display = 'none';
        btnPs.setAttribute('aria-expanded', 'false');
        if (cariPs) cariPs.value = '';
        itemsPs.forEach(i => i.classList.remove('d-none'));
        if (psTidakAda) psTidakAda.classList.add('d-none');
    }

    btnPs.addEventListener('click', function (e) {
        e.stopPropagation();
        menuPs.style.display === 'block' ? tutupPs() : bukaPs();
    });

    document.addEventListener('click', function (e) {
        const wrap = document.getElementById('dropdownPenyesuaian');
        if (wrap && !wrap.contains(e.target)) tutupPs();
    });

    function pilihPs(item) {
        if (!item) return;
        const id = item.dataset.id, nama = item.dataset.nama,
              stok = item.dataset.stok, satuan = item.dataset.satuan,
              status = item.dataset.status ?? 'aman';

        psId.value = id;
        psLabel.innerHTML = `<span class="fw-semibold text-dark">${nama}</span> <span class="text-muted" style="font-size:0.78rem;">(Stok: ${stok} ${satuan})</span>`;
        if (spanPsSat) spanPsSat.textContent = satuan || '—';

        // Set stok baru default ke stok saat ini (opname biasanya mirip)
        const inputStokBaru = document.getElementById('psStokBaru');
        if (inputStokBaru && !inputStokBaru.value) inputStokBaru.value = stok;

        if (labelPsStok && infoPsStok) {
            const warna = status === 'habis' ? 'text-danger' : (status === 'menipis' ? 'text-warning' : 'text-success');
            const ikon  = status === 'habis' ? 'bi-x-circle' : (status === 'menipis' ? 'bi-exclamation-triangle' : 'bi-check-circle');
            labelPsStok.innerHTML = `Stok saat ini: <strong class="${warna}"><i class="bi ${ikon} me-1"></i>${stok} ${satuan}</strong>`;
            infoPsStok.classList.remove('d-none');
        }

        itemsPs.forEach(i => i.classList.remove('bg-light'));
        item.classList.add('bg-light');
        tutupPs();
    }

    itemsPs.forEach(item => {
        item.addEventListener('click', () => pilihPs(item));
    });

    if (cariPs) {
        cariPs.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            let ada = false;
            itemsPs.forEach(item => {
                const cocok = item.dataset.search.includes(q);
                item.classList.toggle('d-none', !cocok);
                if (cocok) ada = true;
            });
            if (psTidakAda) psTidakAda.classList.toggle('d-none', ada);
        });

        cariPs.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const visible = [...itemsPs].find(i => !i.classList.contains('d-none'));
                if (visible) {
                    pilihPs(visible);
                    const inp = document.getElementById('psStokBaru');
                    if (inp) { inp.focus(); inp.select(); }
                }
            } else if (e.key === 'Escape') {
                tutupPs();
            }
        });
    }
})();
</script>
@endpush
@endsection
