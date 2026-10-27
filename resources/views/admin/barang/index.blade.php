@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-box-seam-fill text-success me-2"></i>Kelola Barang
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">MASTER DATA INVENTORI & BARANG KEBERSIHAN</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('stok-masuk.index') }}" class="btn btn-outline-primary btn-sm rounded-3 px-3">
                <i class="bi bi-box-arrow-in-down me-1"></i> Stok Masuk / Restok
            </a>
            <a href="{{ route('stok-barang.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-clipboard2-data me-1"></i> Laporan Stok
            </a>
            <a href="{{ route('admin.barang.create') }}" class="btn btn-success btn-sm rounded-3 px-3">
                <i class="bi bi-plus-circle me-1"></i> Tambah Barang Baru
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

    {{-- ================================================================
         PANEL FILTER
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.barang.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-search text-success me-1"></i>Cari Nama Barang
                        </label>
                        <input type="text" name="cari" class="form-control form-control-sm rounded-3"
                               placeholder="Ketik nama atau kode barang..." value="{{ request('cari') }}">
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-3">
                            <i class="bi bi-funnel me-1"></i> Tampilkan
                        </button>
                    </div>

                    @if(request('cari'))
                    <div class="col-auto">
                        <a href="{{ route('admin.barang.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                            <i class="bi bi-x-circle me-1"></i> Reset Filter
                        </a>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================
         TABEL UTAMA
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
            <h6 class="fw-bold mb-0 text-success">
                <i class="bi bi-table me-2"></i>RINGKASAN MASTER BARANG
            </h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                {{ $barang->total() }} barang
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive table-scroll-container">
                <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-center" style="width:50px;">No</th>
                            <th class="text-center" style="width:70px;">Foto</th>
                            <th>Nama Barang</th>
                            <th style="width:110px;">Kode</th>
                            <th class="text-center" style="width:80px;">Satuan</th>
                            <th class="text-center" style="width:80px;">Stok</th>
                            <th class="text-center" style="width:80px;">Min.</th>
                            <th class="text-center" style="width:100px;">Status</th>
                            <th class="text-center" style="width:130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barang as $key => $b)
                            <tr>
                                <td class="ps-3 text-center text-muted">{{ $barang->firstItem() + $key }}</td>
                                <td class="text-center">
                                    @if($b->foto_barang)
                                        <img src="{{ asset('storage/' . $b->foto_barang) }}" alt="{{ $b->nama_barang }}"
                                             class="rounded-3 object-fit-cover shadow-sm" width="40" height="40">
                                    @else
                                        <div class="rounded-3 bg-light text-muted d-inline-flex align-items-center justify-content-center"
                                             style="width:40px; height:40px;">
                                            <i class="bi bi-image" style="font-size:1.1rem;"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $b->nama_barang }}</div>
                                    @if($b->kategori)
                                        <div class="text-muted" style="font-size:0.74rem;"><i class="bi bi-tag me-1"></i>{{ $b->kategori }}</div>
                                    @endif
                                    @if($b->deskripsi)
                                        <div class="text-muted text-truncate" style="font-size:0.73rem; max-width:260px;">{{ $b->deskripsi }}</div>
                                    @endif
                                </td>
                                <td><span class="font-monospace text-secondary">{{ $b->kode_barang ?? '-' }}</span></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                        {{ $b->satuan }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold {{ $b->statusStok() === 'habis' ? 'text-danger' : ($b->statusStok() === 'menipis' ? 'text-warning' : 'text-success') }}" style="font-size:0.95rem;">
                                        {{ number_format($b->stok_saat_ini) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="text-muted" style="font-size:0.8rem;">{{ $b->stok_minimum ?? 5 }}</span>
                                </td>
                                <td class="text-center">
                                    {!! $b->badgeStatusStok() !!}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('stok-masuk.index') }}?barang_id={{ $b->id }}"
                                           class="btn btn-sm btn-outline-primary rounded-2 px-2"
                                           title="Lihat Mutasi Stok">
                                            <i class="bi bi-journal-text" style="font-size:0.8rem;"></i>
                                        </a>
                                        <a href="{{ route('admin.barang.edit', $b->id) }}"
                                           class="btn btn-sm btn-outline-success rounded-2 px-2"
                                           title="Edit Info Barang">
                                            <i class="bi bi-pencil-square" style="font-size:0.8rem;"></i>
                                        </a>
                                        <form action="{{ route('admin.barang.destroy', $b->id) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus barang {{ addslashes($b->nama_barang) }}?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Hapus Barang">
                                                <i class="bi bi-trash" style="font-size:0.8rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-box-seam text-muted" style="font-size:2.5rem;"></i>
                                    <p class="text-muted mt-2 mb-0">Data barang tidak ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($barang->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $barang->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
