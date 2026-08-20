@extends('tata-letak.aplikasi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold text-success"><i class="bi bi-gear"></i> Dasbor Administrator</h3>
        <p class="text-secondary">Kelola data master sistem kebersihan</p>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Total Pengguna', 'nilai' => $statistik['total_pengguna'], 'ikon' => 'bi-people', 'warna' => 'success'],
        ['label' => 'Total Area', 'nilai' => $statistik['total_area'], 'ikon' => 'bi-building', 'warna' => 'success'],
        ['label' => 'Total Barang', 'nilai' => $statistik['total_barang'], 'ikon' => 'bi-box', 'warna' => 'success'],
        ['label' => 'Ceklis Hari Ini', 'nilai' => $statistik['ceklis_hari_ini'], 'ikon' => 'bi-card-checklist', 'warna' => 'success'],
    ] as $item)
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 text-center">
                <i class="bi {{ $item['ikon'] }} fs-2 text-{{ $item['warna'] }}"></i>
                <div class="fs-3 fw-bold mt-2">{{ $item['nilai'] }}</div>
                <div class="text-secondary small">{{ $item['label'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white border-0 pt-3">
        <h5 class="fw-bold mb-0">Jalan Pintas Manajemen</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-success w-100 rounded-3 py-3">
                    <i class="bi bi-people d-block fs-3 mb-1"></i> Kelola Pengguna
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.area.index') }}" class="btn btn-outline-success w-100 rounded-3 py-3">
                    <i class="bi bi-building d-block fs-3 mb-1"></i> Kelola Area
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.barang.index') }}" class="btn btn-outline-success w-100 rounded-3 py-3">
                    <i class="bi bi-box d-block fs-3 mb-1"></i> Kelola Barang
                </a>
            </div>
            <div class="col-md-3">
                <a href="{{ route('laporan.index') }}" class="btn btn-outline-success w-100 rounded-3 py-3 text-dark">
                    <i class="bi bi-graph-up d-block fs-3 mb-1"></i> Laporan Terpadu
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
