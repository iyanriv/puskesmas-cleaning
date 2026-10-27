@extends('tata-letak.aplikasi')

@section('title', 'Laporan Barang Keluar & Distribusi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center">
                <span class="p-2 rounded-3 me-2 text-white d-inline-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #205F63 0%, #31999E 100%); width: 38px; height: 38px;">
                    <i class="bi bi-box-arrow-up-right fs-6"></i>
                </span>
                Laporan Barang Keluar & Distribusi
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">MONITORING PENGELUARAN, DISTRIBUSI LOGISTIK & PEMAKAIAN UNIT</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-sm text-white rounded-3 px-3 shadow-sm fw-semibold" style="background-color: var(--brand-primary);" data-bs-toggle="modal" data-bs-target="#modalCatatKeluar">
                <i class="bi bi-plus-circle me-1"></i> Catat Barang Keluar
            </button>
            @php
                $queryParams = [
                    'periode'   => $periode,
                    'dari'      => $filterDari,
                    'sampai'    => $filterSampai,
                    'barang_id' => $filterBarangId,
                    'unit'      => $filterUnit,
                    'cari'      => $filterCari,
                ];
            @endphp
            <a href="{{ route('stok-keluar.export-excel', array_filter($queryParams)) }}" class="btn btn-outline-success btn-sm rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('stok-keluar.cetak-pdf', array_filter($queryParams)) }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                <i class="bi bi-printer me-1"></i> Cetak PDF
            </a>
            <a href="{{ route('stok-barang.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-clipboard2-data me-1"></i> Laporan Stok
            </a>
        </div>
    </div>

    {{-- Flash Alerts --}}
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
        {{-- Card 1: Total Unit Keluar --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="letter-spacing: 0.5px; font-size:0.75rem;">Total Unit Keluar</span>
                    <div class="p-2 rounded-3" style="background: rgba(49, 153, 158, 0.15); color: #205F63;">
                        <i class="bi bi-box-arrow-up-right fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold text-dark mb-1">{{ number_format($totalKeluarQty) }}</div>
                <div class="text-muted small">
                    <i class="bi bi-calendar-check text-success me-1"></i>
                    <span>Periode: {{ \Carbon\Carbon::parse($filterDari)->format('d M') }} - {{ \Carbon\Carbon::parse($filterSampai)->format('d M Y') }}</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Total Transaksi Keluar --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="letter-spacing: 0.5px; font-size:0.75rem;">Frekuensi Transaksi</span>
                    <div class="p-2 rounded-3" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                        <i class="bi bi-receipt fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold text-dark mb-1">{{ number_format($totalTransaksiKeluar) }}</div>
                <div class="text-muted small">
                    <i class="bi bi-arrow-repeat text-primary me-1"></i>
                    <span>Distribusi / pengeluaran logistik</span>
                </div>
            </div>
        </div>

        {{-- Card 3: Jumlah Jenis Barang --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="letter-spacing: 0.5px; font-size:0.75rem;">Jenis Barang Terdistribusi</span>
                    <div class="p-2 rounded-3" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                        <i class="bi bi-boxes fs-5"></i>
                    </div>
                </div>
                <div class="fs-2 fw-bold text-dark mb-1">{{ number_format($totalJenisBarangKeluar) }}</div>
                <div class="text-muted small">
                    <i class="bi bi-check2-circle text-success me-1"></i>
                    <span>Dari total {{ $daftarBarang->count() }} item master barang</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Top Barang Keluar --}}
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary fw-semibold small text-uppercase" style="letter-spacing: 0.5px; font-size:0.75rem;">Barang Paling Banyak Keluar</span>
                    <div class="p-2 rounded-3" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">
                        <i class="bi bi-trophy fs-5"></i>
                    </div>
                </div>
                @if($topBarangKeluar->isNotEmpty())
                    <div class="fs-5 fw-bold text-dark text-truncate mb-1" title="{{ $topBarangKeluar->first()->barang->nama_barang ?? '-' }}">
                        {{ $topBarangKeluar->first()->barang->nama_barang ?? '-' }}
                    </div>
                    <div class="text-muted small">
                        <span class="fw-bold text-warning">{{ number_format($topBarangKeluar->first()->total_keluar) }}</span>
                        {{ $topBarangKeluar->first()->barang->satuan ?? 'unit' }} keluar
                    </div>
                @else
                    <div class="fs-6 fw-bold text-muted mb-1">-</div>
                    <div class="text-muted small">Belum ada data keluar</div>
                @endif
            </div>
        </div>
    </div>

    {{-- ================================================================
         TABEL RIWAYAT & FILTER
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4">
        {{-- Card Header & Filter --}}
        <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-table text-teal" style="color: var(--brand-primary);"></i>
                        DAFTAR RIWAYAT PENGELUARAN BARANG
                    </h6>
                    <small class="text-muted" style="font-size:0.75rem;">Semua mutasi barang keluar ke ruangan, unit, atau petugas CS</small>
                </div>
                <span class="badge rounded-pill px-3 py-2 text-white" style="background: var(--brand-primary); font-size:0.75rem;">
                    {{ $riwayatKeluar->total() }} total mutasi
                </span>
            </div>
        </div>

        {{-- Filter Bar Form --}}
        <div class="p-3 bg-light border-bottom">
            <form method="GET" action="{{ route('stok-keluar.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-2 col-6">
                        <label class="form-label text-muted small mb-1 fw-semibold">Periode</label>
                        <select name="periode" id="filter-periode" class="form-select form-select-sm rounded-3" onchange="toggleKustomFilter(this.value)">
                            <option value="bulan_ini" {{ $periode === 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="hari_ini" {{ $periode === 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="minggu_ini" {{ $periode === 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                            <option value="kustom" {{ $periode === 'kustom' ? 'selected' : '' }}>Rentang Kustom</option>
                        </select>
                    </div>

                    <div class="col-md-2 col-6 wrap-kustom-tgl" style="{{ $periode === 'kustom' ? '' : 'display:none;' }}">
                        <label class="form-label text-muted small mb-1 fw-semibold">Dari Tanggal</label>
                        <input type="date" name="dari" class="form-control form-control-sm rounded-3" value="{{ $filterDari }}">
                    </div>

                    <div class="col-md-2 col-6 wrap-kustom-tgl" style="{{ $periode === 'kustom' ? '' : 'display:none;' }}">
                        <label class="form-label text-muted small mb-1 fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="sampai" class="form-control form-control-sm rounded-3" value="{{ $filterSampai }}">
                    </div>

                    <div class="col-md-3 col-6">
                        <label class="form-label text-muted small mb-1 fw-semibold">Pilih Barang</label>
                        <select name="barang_id" class="form-select form-select-sm rounded-3">
                            <option value="">-- Semua Barang --</option>
                            @foreach($daftarBarang as $b)
                                <option value="{{ $b->id }}" {{ $filterBarangId == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama_barang }} ({{ $b->satuan }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2 col-6">
                        <label class="form-label text-muted small mb-1 fw-semibold">Unit / Ruangan</label>
                        <select name="unit" class="form-select form-select-sm rounded-3">
                            <option value="">-- Semua Unit --</option>
                            @foreach($daftarUnit as $u)
                                <option value="{{ $u }}" {{ $filterUnit == $u ? 'selected' : '' }}>{{ $u }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 col-12">
                        <label class="form-label text-muted small mb-1 fw-semibold">Pencarian</label>
                        <input type="text" name="cari" class="form-control form-control-sm rounded-3" placeholder="No. Transaksi / Nama / Ket..." value="{{ $filterCari }}">
                    </div>

                    <div class="col-12 col-md-auto ms-auto d-flex gap-2 justify-content-end pt-1">
                        <a href="{{ route('stok-keluar.index') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                        </a>
                        <button type="submit" class="btn btn-sm text-white rounded-3 px-3 fw-semibold" style="background-color: var(--brand-primary);">
                            <i class="bi bi-funnel me-1"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table Content --}}
        <div class="card-body p-0">
            @if($riwayatKeluar->count() > 0)
            <div class="table-responsive table-scroll-container">
                <table class="table table-hover align-middle mb-0" style="font-size:0.86rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3" style="width:140px;">No. Transaksi</th>
                            <th style="width:105px;">Tanggal</th>
                            <th>Barang</th>
                            <th class="text-center" style="width:90px;">Jumlah Keluar</th>
                            <th class="text-center" style="width:120px;">Saldo</th>
                            <th style="width:140px;">Unit / Tujuan</th>
                            <th>Keterangan / Referensi</th>
                            <th style="width:120px;">Petugas</th>
                            <th class="text-center pe-3" style="width:80px;">Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($riwayatKeluar as $row)
                        <tr>
                            <td class="ps-3">
                                <span class="badge bg-light text-dark border font-monospace" style="font-size:0.75rem;">
                                    {{ $row->nomor_transaksi }}
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $row->tanggal ? $row->tanggal->format('d/m/Y') : '-' }}</div>
                                <small class="text-muted" style="font-size:0.72rem;">{{ $row->created_at ? $row->created_at->format('H:i') . ' WIB' : '' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $row->barang->nama_barang ?? '-' }}</div>
                                <small class="text-muted" style="font-size:0.72rem;">Kode: {{ $row->barang->kode_barang ?? '-' }} | Satuan: {{ $row->barang->satuan ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fs-6 fw-bold rounded-pill">
                                    -{{ number_format($row->jumlah_keluar) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="small text-muted">{{ $row->saldo_sebelumnya }} &rarr; <strong class="text-dark">{{ $row->saldo_setelahnya }}</strong></div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $row->unit ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <div class="text-dark small">{{ $row->keterangan ?: '-' }}</div>
                                @if($row->nomor_referensi)
                                    <small class="text-muted font-monospace" style="font-size:0.7rem;">Ref: {{ $row->nomor_referensi }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $row->petugas->name ?? 'Gudang' }}</div>
                                <small class="text-muted" style="font-size:0.7rem;">{{ ucfirst($row->petugas->peran->nama_peran ?? '-') }}</small>
                            </td>
                            <td class="text-center pe-3">
                                <a href="{{ route('stok-keluar.bukti', $row->id) }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Cetak Bukti Pengeluaran (PDF)">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($riwayatKeluar->hasPages())
                <div class="card-footer bg-white border-top py-2 px-3 d-flex justify-content-end">
                    {{ $riwayatKeluar->links() }}
                </div>
            @endif
            @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-box-arrow-up-right fs-1 d-block mb-2 text-secondary opacity-50"></i>
                Belum ada data pengeluaran barang pada periode ini.
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ================================================================
     MODAL CATAT BARANG KELUAR / DISTRIBUSI LANGSUNG
     ================================================================ --}}
<div class="modal fade" id="modalCatatKeluar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header text-white py-3 px-4 rounded-top-4" style="background: linear-gradient(135deg, #205F63 0%, #31999E 100%);">
                <h6 class="modal-title fw-bold mb-0">
                    <i class="bi bi-box-arrow-up-right me-2"></i>Catat Pengeluaran Barang / Distribusi
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('stok-keluar.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Pilih Barang <span class="text-danger">*</span></label>
                        <select name="barang_id" id="modal-barang-select" class="form-select rounded-3" required onchange="updateStokInfo(this)">
                            <option value="">-- Pilih Barang --</option>
                            @foreach($daftarBarang as $b)
                                <option value="{{ $b->id }}" data-stok="{{ $b->stok_saat_ini }}" data-satuan="{{ $b->satuan }}">
                                    {{ $b->nama_barang }} (Tersedia: {{ $b->stok_saat_ini }} {{ $b->satuan }})
                                </option>
                            @endforeach
                        </select>
                        <div id="info-stok-tersedia" class="form-text small mt-1 text-muted">
                            Pilih barang untuk melihat stok gudang saat ini.
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control rounded-3" value="{{ today()->toDateString() }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Jumlah Keluar <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_keluar" id="input-jumlah-keluar" class="form-control rounded-3" min="1" placeholder="Contoh: 5" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Unit / Ruangan Tujuan <span class="text-danger">*</span></label>
                        <select name="unit" class="form-select rounded-3" required>
                            <option value="">-- Pilih Unit / Lokasi --</option>
                            @foreach($daftarUnit as $u)
                                <option value="{{ $u }}">{{ $u }}</option>
                            @endforeach
                            <option value="Lainnya">Lainnya (Tulis di keterangan)</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Nama Penerima</label>
                            <input type="text" name="penerima" class="form-control rounded-3" placeholder="Nama staf / CS penerima">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">No. Referensi / SPB</label>
                            <input type="text" name="nomor_referensi" class="form-control rounded-3" placeholder="Opsional (SPB/DO)">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Keterangan / Catatan</label>
                        <textarea name="keterangan" class="form-control rounded-3" rows="2" placeholder="Catatan tambahan keperluan pengeluaran barang..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn text-white rounded-3 px-4 fw-semibold" style="background-color: var(--brand-primary);">
                        <i class="bi bi-check-lg me-1"></i> Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleKustomFilter(val) {
        const wraps = document.querySelectorAll('.wrap-kustom-tgl');
        wraps.forEach(w => {
            w.style.display = (val === 'kustom') ? 'block' : 'none';
        });
    }

    function updateStokInfo(select) {
        const option = select.options[select.selectedIndex];
        const stok = option.getAttribute('data-stok');
        const satuan = option.getAttribute('data-satuan');
        const infoDiv = document.getElementById('info-stok-tersedia');
        const inputJumlah = document.getElementById('input-jumlah-keluar');

        if (stok !== null && stok !== '') {
            infoDiv.innerHTML = `<span class="fw-semibold text-success"><i class="bi bi-box me-1"></i>Stok saat ini: ${stok} ${satuan}</span>`;
            inputJumlah.max = stok;
        } else {
            infoDiv.innerHTML = 'Pilih barang untuk melihat stok gudang saat ini.';
            inputJumlah.removeAttribute('max');
        }
    }
</script>
@endsection
