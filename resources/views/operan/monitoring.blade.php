@extends('tata-letak.aplikasi')

@section('title', 'Monitoring Operan Shift')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-arrow-left-right text-success me-2"></i>Monitoring Operan Shift
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">SERAH TERIMA TUGAS DAN LOG KERJA ANTAR SHIFT</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('operan.export-excel', ['periode' => $periode]) }}"
               class="btn btn-outline-success btn-sm rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Excel
            </a>
            <a href="{{ route('operan.cetak-laporan', ['periode' => $periode]) }}" 
               target="_blank" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
            </a>
        </div>
    </div>

    {{-- ================================================================
         PANEL FILTER
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-semibold text-muted text-uppercase" style="font-size:0.78rem; letter-spacing:.4px;">
                        <i class="bi bi-funnel text-success me-1"></i>Periode:
                    </span>
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ route('operan.index', ['periode' => 'hari_ini']) }}" 
                           class="btn {{ $periode === 'hari_ini' ? 'btn-success' : 'btn-outline-secondary' }} rounded-start-3 px-3">
                            Hari Ini
                        </a>
                        <a href="{{ route('operan.index', ['periode' => 'kemarin']) }}" 
                           class="btn {{ $periode === 'kemarin' ? 'btn-success' : 'btn-outline-secondary' }} px-3">
                            Kemarin
                        </a>
                        <a href="{{ route('operan.index', ['periode' => 'minggu_ini']) }}" 
                           class="btn {{ $periode === 'minggu_ini' ? 'btn-success' : 'btn-outline-secondary' }} px-3">
                            Minggu Ini
                        </a>
                        <a href="{{ route('operan.index', ['periode' => 'bulan_ini']) }}" 
                           class="btn {{ $periode === 'bulan_ini' ? 'btn-success' : 'btn-outline-secondary' }} rounded-end-3 px-3">
                            Bulan Ini
                        </a>
                    </div>
                </div>

                <div>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-3" style="font-size:0.8rem;">
                        <i class="bi bi-calendar3 me-1 text-success"></i>
                        {{ \Carbon\Carbon::parse($dari)->translatedFormat('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($sampai)->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         STATISTIK RINGKAS
         ================================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary-subtle"><i class="bi bi-arrow-left-right text-primary fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $totalOperan }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Total Operan</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning-subtle"><i class="bi bi-hourglass-split text-warning fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $menungguKonfirmasi }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Menunggu Konfirmasi</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success-subtle"><i class="bi bi-check2-circle text-success fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $tugasSelesai }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Tugas Selesai</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-danger-subtle"><i class="bi bi-exclamation-triangle text-danger fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $tugasDieskalasi }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Dieskalasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         TABEL UTAMA OPERAN
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
            <h6 class="fw-bold mb-0 text-success">
                <i class="bi bi-table me-2"></i>DAFTAR OPERAN SHIFT
            </h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                {{ $operanList->total() }} operan
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive table-scroll-container">
                <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-center" style="width:45px;">No</th>
                            <th style="width:120px;">Waktu</th>
                            <th>Pengirim (Shift Asal)</th>
                            <th>Penerima (Shift Lanjutan)</th>
                            <th>Tempat & Jaga</th>
                            <th class="text-center" style="width:120px;">Status</th>
                            <th class="text-center" style="width:80px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($operanList as $key => $operan)
                            <tr>
                                <td class="ps-3 text-center text-muted">{{ $operanList->firstItem() + $key }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($operan->tanggal)->translatedFormat('d M Y') }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ \Carbon\Carbon::parse($operan->waktu)->format('H:i') }} WIB</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $operan->pengirim->name ?? 'Tidak Diketahui' }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ $operan->pengirim->peran->nama_peran ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $operan->penerima->name ?? 'Tidak Diketahui' }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ $operan->penerima->peran->nama_peran ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="text-dark"><i class="bi bi-geo-alt text-success me-1"></i>{{ $operan->tempat_tugas }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ $operan->waktu_jaga }}</div>
                                </td>
                                <td class="text-center">
                                    @if($operan->status_terima === 'menunggu')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                            <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                        </span>
                                    @elseif($operan->status_penyelesaian === 'selesai')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                            <i class="bi bi-check-circle-fill me-1"></i> Selesai
                                        </span>
                                    @elseif($operan->status_penyelesaian === 'dieskalasi')
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                            <i class="bi bi-arrow-up-right-circle me-1"></i> Dieskalasi
                                        </span>
                                    @else
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                            <i class="bi bi-envelope-open me-1"></i> Diterima
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('operan.detail', $operan->id) }}"
                                       class="btn btn-sm btn-outline-success rounded-2 px-2" title="Lihat Detail Operan">
                                        <i class="bi bi-eye" style="font-size:0.8rem;"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-inbox text-muted" style="font-size:2.5rem;"></i>
                                    <p class="text-muted mt-2 mb-0">Tidak ada operan shift pada periode ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($operanList->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $operanList->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
