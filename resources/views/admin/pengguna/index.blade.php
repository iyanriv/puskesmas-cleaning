@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-people-fill text-success me-2"></i>Kelola Pengguna
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">MASTER DATA PENGGUNA SISTEM</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.pengguna.create') }}" class="btn btn-success btn-sm rounded-3 px-3">
                <i class="bi bi-plus-circle me-1"></i> Tambah Pengguna
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
            <form action="{{ route('admin.pengguna.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-5">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-search text-success me-1"></i>Cari Pengguna
                        </label>
                        <input type="text" name="cari" class="form-control form-control-sm rounded-3"
                               placeholder="Cari Nama Lengkap atau Nomor Karyawan..." value="{{ request('cari') }}">
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-shield-lock text-success me-1"></i>Peran / Role
                        </label>
                        <select name="peran_id" class="form-select form-select-sm rounded-3">
                            <option value="">— Semua Peran —</option>
                            @foreach($peran as $p)
                                <option value="{{ $p->id }}" {{ request('peran_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_peran }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-3">
                            <i class="bi bi-funnel me-1"></i> Tampilkan
                        </button>
                    </div>

                    @if(request('cari') || request('peran_id'))
                    <div class="col-auto">
                        <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
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
                <i class="bi bi-table me-2"></i>RINGKASAN DATA PENGGUNA
            </h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                {{ $pengguna->total() }} pengguna
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive table-scroll-container">
                <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-center" style="width:50px;">No</th>
                            <th>Nama Lengkap</th>
                            <th style="width:180px;">Nomor Karyawan</th>
                            <th class="text-center" style="width:160px;">Peran</th>
                            <th class="text-center" style="width:100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengguna as $key => $p)
                            <tr>
                                <td class="ps-3 text-center text-muted">{{ $pengguna->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                             style="width:34px; height:34px; background:#e6f4f5; color:#267A7E; font-size:0.85rem;">
                                            {{ strtoupper(substr($p->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-semibold text-dark">{{ $p->name }}</span>
                                            @if($p->id === auth()->id())
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle ms-1" style="font-size:0.68rem;">Anda</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><span class="font-monospace text-secondary">{{ $p->nik }}</span></td>
                                <td class="text-center">
                                    @php
                                        $peranNama = strtolower($p->peran ? $p->peran->nama_peran : '-');
                                        $badgeClass = match($peranNama) {
                                            'admin' => 'bg-danger-subtle text-danger border-danger-subtle',
                                            'supervisor' => 'bg-primary-subtle text-primary border-primary-subtle',
                                            'pj_lantai' => 'bg-info-subtle text-info border-info-subtle',
                                            'gudang' => 'bg-warning-subtle text-warning border-warning-subtle',
                                            default => 'bg-success-subtle text-success border-success-subtle',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }} border rounded-pill px-3 py-1" style="font-size:0.75rem;">
                                        {{ $p->peran ? $p->peran->nama_peran : '-' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('admin.pengguna.edit', $p->id) }}"
                                           class="btn btn-sm btn-outline-success rounded-2 px-2"
                                           title="Edit Pengguna">
                                            <i class="bi bi-pencil-square" style="font-size:0.8rem;"></i>
                                        </a>
                                        @if($p->id !== auth()->id())
                                        <form action="{{ route('admin.pengguna.destroy', $p->id) }}" method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus pengguna {{ addslashes($p->name) }}?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Hapus Pengguna">
                                                <i class="bi bi-trash" style="font-size:0.8rem;"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="bi bi-people text-muted" style="font-size:2.5rem;"></i>
                                    <p class="text-muted mt-2 mb-0">Data pengguna tidak ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pengguna->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $pengguna->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
