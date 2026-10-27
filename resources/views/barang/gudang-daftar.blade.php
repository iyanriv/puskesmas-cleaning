@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-box-arrow-up-right text-success me-2"></i>Laporan Barang Keluar & Permintaan
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">MONITORING DISTRIBUSI & LAPORAN BARANG KELUAR GUDANG</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            @php
                $exportParams = ['status' => request('status') !== 'semua' ? request('status') : null];
                if (request('periode') === 'hari_ini') {
                    $exportParams['dari'] = today()->toDateString();
                    $exportParams['sampai'] = today()->toDateString();
                } elseif (request('periode') === 'minggu_ini') {
                    $exportParams['dari'] = now()->startOfWeek()->toDateString();
                    $exportParams['sampai'] = now()->endOfWeek()->toDateString();
                } elseif (request('periode') === 'kustom' && request('dari') && request('sampai')) {
                    $exportParams['dari'] = request('dari');
                    $exportParams['sampai'] = request('sampai');
                } else {
                    $exportParams['dari'] = now()->startOfMonth()->toDateString();
                    $exportParams['sampai'] = now()->endOfMonth()->toDateString();
                }
            @endphp
            <a href="{{ route('stok-keluar.index') }}" class="btn btn-outline-teal btn-sm rounded-3 px-3" style="border-color: var(--brand-primary); color: var(--brand-primary);">
                <i class="bi bi-box-arrow-up-right me-1"></i> Laporan Barang Keluar Lengkap
            </a>
            <a href="{{ route('barang.export-excel', array_filter($exportParams)) }}" class="btn btn-outline-success btn-sm rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('stok-barang.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-clipboard2-data me-1"></i> Laporan Stok
            </a>
            <a href="{{ route('admin.barang.index') }}" class="btn btn-success btn-sm rounded-3 px-3">
                <i class="bi bi-boxes me-1"></i> Kelola Master Barang
            </a>
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
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================================================================
         STATISTIK RINGKAS
         ================================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning-subtle"><i class="bi bi-hourglass-split text-warning fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $permintaanPending->count() }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Menunggu Persetujuan</div>
                        <div class="text-secondary" style="font-size:0.7rem;">Permintaan belum diproses</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success-subtle"><i class="bi bi-box-arrow-up-right text-success fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ number_format($totalBarangKeluarQty) }} Unit</div>
                        <div class="text-muted" style="font-size:0.78rem;">Barang Keluar (Disetujui)</div>
                        <div class="text-secondary" style="font-size:0.7rem;">{{ number_format($totalTransaksiDisetujui) }} transaksi ({{ ucwords(str_replace('_', ' ', $periode ?? 'bulan ini')) }})</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-danger-subtle"><i class="bi bi-exclamation-triangle text-danger fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-danger">{{ $semuaBarang->filter->stokMenipis()->count() }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Barang Stok Menipis</div>
                        <div class="text-secondary" style="font-size:0.7rem;">Perlu pengadaan / restok</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         KONTEN UTAMA: DUA KOLOM
         ================================================================ --}}
    <div class="row g-4">
        {{-- Kolom Kiri: Permintaan Pending & Riwayat Laporan Barang Keluar --}}
        <div class="col-lg-8">

            {{-- 1. Permintaan Pending --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                    <div>
                        <h6 class="fw-bold mb-0 text-info">
                            <i class="bi bi-shield-check me-2"></i>SUDAH DISETUJUI SUPERVISOR — SIAP DIPROSES GUDANG
                        </h6>
                        <small class="text-muted" style="font-size:0.75rem;">Permintaan berikut sudah disetujui supervisor, silakan proses pengeluaran barang.</small>
                    </div>
                    <span class="badge bg-info-subtle text-info border border-info-subtle" style="font-size:0.75rem;">
                        {{ $permintaanPending->count() }} permintaan
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($permintaanPending->count() > 0)
                    <div class="table-responsive table-scroll-container">
                        <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width:140px;">Pemohon</th>
                                    <th>Barang</th>
                                    <th>Area Bertugas</th>
                                    <th class="text-center" style="width:90px;">Jumlah</th>
                                    <th class="text-center" style="width:110px;">Stok Ada</th>
                                    <th class="text-center" style="width:120px;">Disetujui Spv</th>
                                    <th class="text-center" style="width:140px;">Aksi Gudang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permintaanPending as $p)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $p->pengguna->name }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $p->pengguna->peran->nama_peran ?? '-' }}</div>
                                        @if($p->kode_transaksi)
                                            <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.65rem;">{{ $p->kode_transaksi }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $p->barang->nama_barang }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Satuan: {{ $p->barang->satuan }}</div>
                                    </td>
                                    <td>
                                        @if($p->area)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $p->area->lantai }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size:0.75rem;">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-dark">{{ $p->jumlah }}</td>
                                    <td class="text-center">
                                        @php
                                            $stok = $p->barang->stok_saat_ini;
                                            $cukup = $stok >= $p->jumlah;
                                        @endphp
                                        <span class="badge {{ $cukup ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }} border rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                            {{ $stok }} {{ $cukup ? '✓' : '⚠' }}
                                        </span>
                                    </td>
                                    <td class="text-center" style="font-size:0.75rem;">
                                        <div class="text-info fw-semibold">
                                            <i class="bi bi-shield-check me-1"></i>{{ $p->supervisor?->name ?? 'Supervisor' }}
                                        </div>
                                        <small class="text-muted">{{ $p->waktu_approve_spv?->format('d/m H:i') ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <form action="{{ route('barang.setujui', $p->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return {{ $cukup ? 'true' : "confirm('Stok tidak mencukupi! Yakin ingin menyetujui?')" }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-success rounded-2 px-2" title="Setujui & Keluarkan Barang">
                                                    <i class="bi bi-check-lg" style="font-size:0.8rem;"></i> Proses
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Tolak"
                                                    onclick="bukaModalTolak({{ $p->id }}, '{{ addslashes($p->pengguna->name) }}', '{{ addslashes($p->barang->nama_barang) }}')">
                                                <i class="bi bi-x-lg" style="font-size:0.8rem;"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success opacity-50"></i>
                        <div class="fw-semibold">Tidak ada permintaan yang siap diproses.</div>
                        <small>Permintaan baru harus disetujui supervisor terlebih dahulu.</small>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 2. Riwayat Laporan Barang Keluar & Distribusi --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                    <div>
                        <h6 class="fw-bold mb-0 text-success">
                            <i class="bi bi-clock-history me-2"></i>LAPORAN BARANG KELUAR & DISTRIBUSI
                        </h6>
                        <small class="text-muted" style="font-size:0.75rem;">Data barang yang telah disetujui / diproses</small>
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                        {{ $sudahDiproses->total() }} total data
                    </span>
                </div>

                {{-- Filter & Search Form --}}
                <div class="p-3 bg-light border-bottom">
                    <form method="GET" action="{{ route('barang.gudang') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3 col-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Periode</label>
                                <select name="periode" id="select-periode" class="form-select form-select-sm rounded-3" onchange="toggleKustomTanggal(this.value)">
                                    <option value="bulan_ini" {{ ($periode ?? 'bulan_ini') === 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                                    <option value="hari_ini" {{ ($periode ?? '') === 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                                    <option value="minggu_ini" {{ ($periode ?? '') === 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                                    <option value="kustom" {{ ($periode ?? '') === 'kustom' ? 'selected' : '' }}>Rentang Kustom</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Status</label>
                                <select name="status" class="form-select form-select-sm rounded-3">
                                    <option value="semua" {{ request('status', 'semua') === 'semua' ? 'selected' : '' }}>Semua</option>
                                    <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-6 kustom-tanggal-wrap" style="{{ ($periode ?? '') === 'kustom' ? '' : 'display:none;' }}">
                                <label class="form-label text-muted small mb-1 fw-semibold">Dari</label>
                                <input type="date" name="dari" class="form-control form-control-sm rounded-3" value="{{ request('dari') }}">
                            </div>
                            <div class="col-md-2 col-6 kustom-tanggal-wrap" style="{{ ($periode ?? '') === 'kustom' ? '' : 'display:none;' }}">
                                <label class="form-label text-muted small mb-1 fw-semibold">Sampai</label>
                                <input type="date" name="sampai" class="form-control form-control-sm rounded-3" value="{{ request('sampai') }}">
                            </div>
                            <div class="col-md-3 col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Cari Barang / Pemohon</label>
                                <input type="text" name="cari" class="form-control form-control-sm rounded-3" placeholder="Nama / Kode..." value="{{ request('cari') }}">
                            </div>
                            <div class="col-12 col-md-auto ms-auto d-flex gap-2 justify-content-end pt-1">
                                <a href="{{ route('barang.gudang') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                </a>
                                <button type="submit" class="btn btn-sm btn-success rounded-3 px-3">
                                    <i class="bi bi-funnel me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    @if($sudahDiproses->count() > 0)
                    <div class="table-responsive table-scroll-container">
                        <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width:130px;">Kode / Pemohon</th>
                                    <th>Barang</th>
                                    <th>Area</th>
                                    <th class="text-center" style="width:90px;">Jumlah</th>
                                    <th class="text-center" style="width:110px;">Status</th>
                                    <th class="text-center" style="width:130px;">Waktu Approve</th>
                                    <th class="text-center" style="width:80px;">Bukti</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sudahDiproses as $p)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $p->pengguna->name }}</div>
                                        <div class="text-muted" style="font-size:0.72rem;">
                                            <span class="badge bg-light text-secondary border font-monospace">{{ $p->kode_transaksi ?: 'REQ-'.$p->id }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $p->barang->nama_barang }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Satuan: {{ $p->barang->satuan }}</div>
                                    </td>
                                    <td>
                                        @if($p->area)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size:0.74rem;">
                                                <i class="bi bi-geo-alt-fill me-1"></i>{{ $p->area->lantai }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size:0.75rem;">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-dark">{{ $p->jumlah }} {{ $p->barang->satuan }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $p->status_request === 'disetujui' ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }} border rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                            {{ $p->status_request === 'disetujui' ? 'Disetujui ✓' : 'Ditolak ✗' }}
                                        </span>
                                        @if($p->status_request === 'ditolak' && $p->alasan_penolakan)
                                            <div class="text-danger mt-1 text-truncate" style="font-size:0.7rem; max-width:140px;" title="{{ $p->alasan_penolakan }}">
                                                {{ $p->alasan_penolakan }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center text-muted" style="font-size:0.78rem;">
                                        <div>{{ $p->waktu_approve ? $p->waktu_approve->translatedFormat('d M Y') : '-' }}</div>
                                        <small class="text-secondary">{{ $p->waktu_approve ? $p->waktu_approve->format('H:i') . ' WIB' : '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('barang.bukti', $p->id) }}" target="_blank"
                                           class="btn btn-sm btn-outline-success rounded-2 px-2" title="Lihat Bukti PDF">
                                            <i class="bi bi-file-earmark-pdf" style="font-size:0.8rem;"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($sudahDiproses->hasPages())
                        <div class="card-footer bg-white border-top py-2 px-3 d-flex justify-content-end">
                            {{ $sudahDiproses->links() }}
                        </div>
                    @endif
                    @else
                    <div class="text-center py-5 text-muted" style="font-size:0.85rem;">
                        <i class="bi bi-clipboard2-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Tidak ada riwayat barang keluar sesuai filter yang dipilih.
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Status Stok Barang --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-boxes me-2"></i>STATUS STOK GUDANG
                    </h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                        {{ $semuaBarang->count() }} item
                    </span>
                </div>
                <div class="card-body p-3" style="max-height: 600px; overflow-y: auto;">
                    @foreach($semuaBarang as $barang)
                        @php
                            if ($barang->stok_saat_ini == 0) {
                                $kelas = 'danger'; $teks = 'Habis';
                            } elseif ($barang->stokMenipis()) {
                                $kelas = 'warning'; $teks = 'Menipis';
                            } else {
                                $kelas = 'success'; $teks = 'Aman';
                            }
                            $total = max($barang->stok_minimum * 4, $barang->stok_saat_ini);
                            $persen = $total > 0 ? min(100, ($barang->stok_saat_ini / $total) * 100) : 0;
                        @endphp
                        <div class="p-3 mb-2 rounded-3 border bg-white shadow-xs">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold text-dark" style="font-size:0.84rem;">{{ $barang->nama_barang }}</div>
                                    <div class="text-muted" style="font-size:0.72rem;">Min: {{ $barang->stok_minimum }} {{ $barang->satuan }}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold fs-6 text-dark">{{ $barang->stok_saat_ini }} <span class="text-muted fw-normal" style="font-size:0.75rem;">{{ $barang->satuan }}</span></div>
                                    <span class="badge bg-{{ $kelas }}-subtle text-{{ $kelas }} border border-{{ $kelas }}-subtle rounded-pill px-2 py-0" style="font-size:0.68rem;">
                                        {{ $teks }}
                                    </span>
                                </div>
                            </div>
                            <div class="progress mt-2" style="height: 5px;">
                                <div class="progress-bar bg-{{ $kelas }}" role="progressbar" style="width: {{ $persen }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tolak --}}
<div class="modal fade" id="modalTolakBootstrap" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-bottom py-3 px-4">
                <h6 class="modal-title fw-bold text-danger mb-0">
                    <i class="bi bi-x-circle me-2"></i>TOLAK PERMINTAAN BARANG
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="form-tolak" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body p-4">
                    <p class="text-muted mb-3" style="font-size:0.85rem;" id="modal-keterangan">-</p>

                    <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                        Alasan Penolakan <span class="text-danger">*</span>
                    </label>
                    <textarea name="alasan_penolakan" class="form-control form-control-sm rounded-3 mb-2" rows="3"
                        placeholder="Contoh: Stok sedang tidak mencukupi, harap koordinasikan dengan gudang..."
                        required maxlength="500" id="textarea-alasan"></textarea>
                </div>
                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger btn-sm rounded-3 px-4">
                        <i class="bi bi-x-circle me-1"></i> Tolak Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleKustomTanggal(val) {
        const wraps = document.querySelectorAll('.kustom-tanggal-wrap');
        wraps.forEach(w => {
            w.style.display = (val === 'kustom') ? 'block' : 'none';
        });
    }

    function bukaModalTolak(id, namaPemohon, namaBarang) {
        document.getElementById('modal-keterangan').textContent =
            'Tolak permintaan barang ' + namaBarang + ' dari ' + namaPemohon + '?';
        document.getElementById('form-tolak').action = '{{ url('barang') }}/' + id + '/tolak';
        document.getElementById('textarea-alasan').value = '';
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalTolakBootstrap')).show();
    }
</script>
@endsection
