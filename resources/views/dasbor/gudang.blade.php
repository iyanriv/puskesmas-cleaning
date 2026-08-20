@extends('tata-letak.aplikasi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold text-success"><i class="bi bi-box-seam"></i> Dasbor Gudang</h3>
        <p class="text-secondary">Kelola stok dan permintaan barang kebersihan</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-warning">
            <div class="text-secondary small">Stok Menipis</div>
            <div class="fs-2 fw-bold text-warning">{{ $stokMenipis->count() }} barang</div>
        </div>
    </div>
    <div class="col-md-6">
        <a href="{{ route('barang.gudang') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-success" style="cursor:pointer; transition:all 0.2s;">
                <div class="text-secondary small">Permintaan Pending</div>
                <div class="fs-2 fw-bold text-success">{{ $permintaanPending->count() }} permintaan</div>
                <div class="text-success small mt-1"><i class="bi bi-arrow-right"></i> Kelola sekarang</div>
            </div>
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="fw-bold mb-0">Peringatan Stok Menipis</h5>
            </div>
            <div class="card-body">
                @forelse($stokMenipis as $barang)
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span>{{ $barang->nama_barang }}</span>
                        <span class="badge bg-warning text-dark">{{ $barang->stok_saat_ini }} {{ $barang->satuan }}</span>
                    </div>
                @empty
                    <p class="text-secondary text-center py-3">Semua stok aman.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-3 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold mb-0">Permintaan Barang Pending</h5>
                @if($permintaanPending->count() > 0)
                    <a href="{{ route('barang.gudang') }}" class="btn btn-sm btn-success rounded-pill px-3">Proses Semua</a>
                @endif
            </div>
            <div class="card-body">
                @forelse($permintaanPending as $permintaan)
                    <div class="py-2 border-bottom">
                        <strong>{{ $permintaan->pengguna->name }}</strong>
                        <div class="text-secondary small">{{ $permintaan->barang->nama_barang }} — {{ $permintaan->jumlah }} {{ $permintaan->barang->satuan }}</div>
                    </div>
                @empty
                    <p class="text-secondary text-center py-3">Tidak ada permintaan pending.</p>
                @endforelse
                @if($permintaanPending->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('barang.gudang') }}" class="btn btn-outline-success btn-sm rounded-pill">
                            <i class="bi bi-arrow-right me-1"></i>Kelola Permintaan
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
