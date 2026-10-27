@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-geo-alt-fill text-success me-2"></i>Kelola Area & Unit
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">MASTER DATA LOKASI, LANTAI & RUANGAN</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.area.create') }}" class="btn btn-success btn-sm rounded-3 px-3">
                <i class="bi bi-plus-circle me-1"></i> Tambah Area
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
            <form action="{{ route('admin.area.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-search text-success me-1"></i>Cari Unit / Lantai
                        </label>
                        <input type="text" name="cari" class="form-control form-control-sm rounded-3"
                               placeholder="Cari Unit, Lantai, atau Ruangan..." value="{{ request('cari') }}">
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-3">
                            <i class="bi bi-funnel me-1"></i> Tampilkan
                        </button>
                    </div>

                    @if(request('cari'))
                    <div class="col-auto">
                        <a href="{{ route('admin.area.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
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
                <i class="bi bi-table me-2"></i>RINGKASAN DATA AREA / RUANGAN
            </h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                {{ $area->total() }} area
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive table-scroll-container">
                <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-center" style="width:50px;">No</th>
                            <th>Nama Unit / Lantai / Ruangan</th>
                            <th class="text-center" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($area as $key => $a)
                            <tr>
                                <td class="ps-3 text-center text-muted">{{ $area->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-3 p-1 px-2 bg-success-subtle text-success" style="font-size:0.85rem;">
                                            <i class="bi bi-geo-alt"></i>
                                        </div>
                                        <span class="fw-semibold text-dark">{{ $a->lantai }}</span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('admin.area.edit', $a->id) }}"
                                           class="btn btn-sm btn-outline-success rounded-2 px-2"
                                           title="Edit Area">
                                            <i class="bi bi-pencil-square" style="font-size:0.8rem;"></i>
                                        </a>
                                        <form action="{{ route('admin.area.destroy', $a->id) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus area {{ addslashes($a->lantai) }}?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Hapus Area">
                                                <i class="bi bi-trash" style="font-size:0.8rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <i class="bi bi-geo-alt text-muted" style="font-size:2.5rem;"></i>
                                    <p class="text-muted mt-2 mb-0">Data area tidak ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($area->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $area->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
