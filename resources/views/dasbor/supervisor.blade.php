@extends('tata-letak.aplikasi')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold text-success"><i class="bi bi-speedometer2"></i> Dasbor Supervisor</h3>
        <p class="text-secondary">Monitoring kebersihan Puskesmas Cempaka Putih</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="text-secondary small">Area Bersih Hari Ini</div>
            <div class="fs-2 fw-bold text-success">{{ $areaBersih }}/{{ $totalArea }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <div class="text-secondary small">Persentase Kebersihan</div>
            <div class="fs-2 fw-bold text-success">{{ $totalArea > 0 ? round(($areaBersih / $totalArea) * 100) : 0 }}%</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
            <div class="text-secondary small mb-2">Aksi Cepat</div>
            <div class="d-flex flex-column gap-2">
                <a href="{{ route('penilaian.index') }}" class="btn btn-outline-success btn-sm rounded-pill w-100 text-start">
                    <i class="bi bi-star-fill me-2"></i> Penilaian Kinerja
                </a>
                <a href="{{ route('laporan.index') }}" class="btn btn-outline-success btn-sm rounded-pill w-100 text-start">
                    <i class="bi bi-graph-up me-2"></i> Dasbor Laporan
                </a>
                <a href="{{ route('sampah.rekapan') }}" class="btn btn-outline-success btn-sm rounded-pill w-100 text-start">
                    <i class="bi bi-recycle me-2"></i> Validasi Bank Sampah
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="fw-bold mb-0">Ceklis Masuk Hari Ini</h5>
            </div>
            <div class="card-body">
                @forelse($ceklisBaru as $ceklis)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <strong>{{ $ceklis->pengguna->name }}</strong>
                            <div class="text-secondary small">{{ $ceklis->area->nama_ruangan ?? 'Area' }} — Lantai {{ $ceklis->area->lantai ?? '-' }}</div>
                        </div>
                        <span class="badge rounded-pill {{ $ceklis->status === 'selesai' ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($ceklis->status) }}
                        </span>
                    </div>
                @empty
                    <p class="text-secondary text-center py-3">Belum ada ceklis masuk hari ini.</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Setoran Sampah Terbaru</h5>
                <a href="{{ route('sampah.rekapan') }}" class="btn btn-sm btn-light rounded-pill" style="font-size:0.75rem;">Lihat Rekap</a>
            </div>
            <div class="card-body">
                @forelse($setoranTerbaru as $setoran)
                    <div class="py-2 border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $setoran->pengguna->name }}</strong>
                            <div class="text-secondary small">{{ $setoran->jenisSampahTeks() }}</div>
                            <div class="text-secondary" style="font-size:0.75rem;">
                                {{ $setoran->tanggal->translatedFormat('d M Y') }}
                            </div>
                        </div>
                        @php $sv = $setoran->status_validasi ?? 'menunggu'; @endphp
                        <span class="badge rounded-pill
                            {{ $sv === 'valid' ? 'bg-success' : ($sv === 'ditolak' ? 'bg-danger' : 'bg-warning text-dark') }}"
                            style="font-size:0.7rem;">
                            {{ $sv === 'valid' ? 'Valid' : ($sv === 'ditolak' ? 'Ditolak' : 'Menunggu') }}
                        </span>
                    </div>
                @empty
                    <p class="text-secondary text-center py-3">Belum ada data setoran terbaru.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
