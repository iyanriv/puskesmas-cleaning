@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-recycle text-success me-2"></i>Rekapan Bank Sampah
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">{{ strtoupper($judul) }}</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('sampah.export-excel', ['filter' => $filter, 'bulan' => $bulan]) }}"
               class="btn btn-outline-success btn-sm rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Excel
            </a>
            <a href="{{ route('sampah.cetak-laporan', ['filter' => $filter, 'bulan' => $bulan]) }}"
               target="_blank" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
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
            <form method="GET" action="{{ route('sampah.rekapan') }}" id="form-filter">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-calendar3 text-success me-1"></i>Periode Laporan
                        </label>
                        <div class="btn-group btn-group-sm d-flex" role="group">
                            <a href="{{ route('sampah.rekapan', ['filter' => 'hari', 'bulan' => $bulan]) }}"
                               class="btn {{ $filter === 'hari' ? 'btn-success' : 'btn-outline-secondary' }} rounded-start-3 px-3">
                                Hari Ini
                            </a>
                            <a href="{{ route('sampah.rekapan', ['filter' => 'minggu', 'bulan' => $bulan]) }}"
                               class="btn {{ $filter === 'minggu' ? 'btn-success' : 'btn-outline-secondary' }} px-3">
                                Minggu Ini
                            </a>
                            <a href="{{ route('sampah.rekapan', ['filter' => 'bulan', 'bulan' => $bulan]) }}"
                               class="btn {{ $filter === 'bulan' ? 'btn-success' : 'btn-outline-secondary' }} rounded-end-3 px-3">
                                Bulanan
                            </a>
                        </div>
                    </div>

                    @if($filter === 'bulan')
                    <div class="col-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            Pilih Bulan
                        </label>
                        <select name="bulan" class="form-select form-select-sm rounded-3" onchange="this.form.submit()" style="min-width:160px;">
                            @foreach($daftarBulan as $b)
                                <option value="{{ $b }}" {{ $bulan === $b ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($b . '-01')->translatedFormat('F Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <input type="hidden" name="filter" value="{{ $filter }}">
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================
         STATISTIK RINGKAS
         ================================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary-subtle"><i class="bi bi-recycle text-primary fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $totalSetoran }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Total Laporan Setoran</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success-subtle"><i class="bi bi-check2-circle text-success fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $totalValid }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Sudah Divalidasi</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning-subtle"><i class="bi bi-hourglass-split text-warning fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $totalMenunggu }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Menunggu Validasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($totalSetoran === 0)
        <div class="card border-0 shadow-sm rounded-4 text-center py-5">
            <div class="card-body">
                <i class="bi bi-inbox text-muted" style="font-size:3rem;"></i>
                <p class="text-muted mt-3 mb-0 fw-semibold">Belum ada data setoran sampah pada periode ini.</p>
            </div>
        </div>
    @else
        {{-- ================================================================
             RINGKASAN PER JENIS & PETUGAS
             ================================================================ --}}
        <div class="row g-4 mb-4">
            {{-- Rekap per Jenis --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                        <h6 class="fw-bold mb-0 text-success">
                            <i class="bi bi-pie-chart me-2"></i>REKAP PER JENIS SAMPAH
                        </h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                            {{ count($rekapJenis) }} jenis
                        </span>
                    </div>
                    <div class="card-body p-4">
                        @php $maxSetor = collect($rekapJenis)->max('jumlah_setor') ?: 1; @endphp
                        @foreach($rekapJenis as $jenis => $data)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold text-dark" style="font-size:0.85rem;">{{ $jenis }}</span>
                                    <span class="fw-bold text-success" style="font-size:0.85rem;">{{ $data['jumlah_setor'] }} laporan</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ ($data['jumlah_setor'] / $maxSetor) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Rekap per Petugas --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                        <h6 class="fw-bold mb-0 text-success">
                            <i class="bi bi-people me-2"></i>REKAP PER PETUGAS CS
                        </h6>
                        <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                            {{ count($rekapPertugas) }} petugas
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive table-scroll-container">
                            <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Nama Petugas</th>
                                        <th class="text-end pe-4" style="width:140px;">Jumlah Setor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapPertugas as $data)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                         style="width:32px; height:32px; background:#e6f4f5; color:#267A7E; font-size:0.8rem;">
                                                        {{ strtoupper(substr($data['nama'], 0, 1)) }}
                                                    </div>
                                                    <span class="fw-semibold text-dark">{{ $data['nama'] }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4 fw-bold text-success">
                                                {{ $data['jumlah_setor'] }} kali
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================
             DETAIL SEMUA SETORAN
             ================================================================ --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                <h6 class="fw-bold mb-0 text-success">
                    <i class="bi bi-table me-2"></i>DAFTAR DETAIL SETORAN SAMPAH
                </h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                    {{ $totalSetoran }} setoran
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive table-scroll-container">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-center" style="width:45px;">No</th>
                                <th style="width:120px;">Tanggal</th>
                                <th>Petugas CS</th>
                                <th>Jenis Sampah</th>
                                <th>Lokasi Setor</th>
                                <th class="text-center" style="width:130px;">Status</th>
                                <th class="text-center" style="width:70px;">Foto</th>
                                <th class="text-center" style="width:140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($semuaSetoran as $idx => $setoran)
                                @php $sv = $setoran->status_validasi ?? 'menunggu'; @endphp
                                <tr>
                                    <td class="ps-3 text-center text-muted">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $setoran->tanggal->translatedFormat('d M Y') }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ $setoran->created_at->format('H:i') }} WIB</div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $setoran->pengguna->name }}</div>
                                        @if($setoran->catatan)
                                            <div class="text-muted fst-italic" style="font-size:0.75rem;">"{{ $setoran->catatan }}"</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach((array) $setoran->jenis_sampah as $j)
                                                <span class="badge bg-light text-dark border rounded-pill px-2 py-1" style="font-size:0.72rem;">{{ $j }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark"><i class="bi bi-geo-alt text-success me-1"></i>{{ $setoran->lokasi_setor }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($sv === 'valid')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                                <i class="bi bi-check-circle-fill me-1"></i> Tervalidasi
                                            </span>
                                            @if($setoran->validator)
                                                <div class="text-muted" style="font-size:0.7rem;">Oleh {{ $setoran->validator->name }}</div>
                                            @endif
                                        @elseif($sv === 'ditolak')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                                <i class="bi bi-x-circle-fill me-1"></i> Ditolak
                                            </span>
                                            @if($setoran->catatan_validasi)
                                                <div class="text-danger" style="font-size:0.7rem;">{{ $setoran->catatan_validasi }}</div>
                                            @endif
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                                <i class="bi bi-hourglass-split me-1"></i> Menunggu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($setoran->foto_timbangan)
                                            <a href="{{ asset('storage/' . $setoran->foto_timbangan) }}" target="_blank"
                                               class="btn btn-sm btn-outline-success rounded-2 px-2" title="Lihat Foto Bukti">
                                                <i class="bi bi-image" style="font-size:0.8rem;"></i>
                                            </a>
                                        @else
                                            <span class="text-muted" style="font-size:0.75rem;">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($sv === 'menunggu' && in_array(auth()->user()->peran?->nama_peran, ['supervisor','pj_lantai','admin']))
                                            <div class="d-flex gap-1 justify-content-center">
                                                <form action="{{ route('sampah.validasi', $setoran->id) }}" method="POST" class="d-inline">
                                                    @csrf @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-success rounded-2 px-2"
                                                            onclick="return confirm('Validasi setoran ini?')" title="Validasi">
                                                        <i class="bi bi-check-lg" style="font-size:0.8rem;"></i> Validasi
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2"
                                                        data-bs-toggle="modal" data-bs-target="#modalTolak{{ $setoran->id }}" title="Tolak">
                                                    <i class="bi bi-x-lg" style="font-size:0.8rem;"></i> Tolak
                                                </button>
                                            </div>

                                            {{-- Modal Tolak --}}
                                            <div class="modal fade" id="modalTolak{{ $setoran->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content rounded-4 border-0 shadow">
                                                        <div class="modal-header border-bottom py-3 px-4">
                                                            <h6 class="modal-title fw-bold text-danger mb-0">
                                                                <i class="bi bi-x-circle me-2"></i>TOLAK SETORAN SAMPAH
                                                            </h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="{{ route('sampah.tolak', $setoran->id) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <div class="modal-body p-4 text-start">
                                                                <p class="text-muted mb-3" style="font-size:0.85rem;">
                                                                    Setoran dari <strong>{{ $setoran->pengguna->name }}</strong> &mdash; {{ $setoran->jenisSampahTeks() }}
                                                                </p>
                                                                <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                                                                    Alasan Penolakan <span class="text-danger">*</span>
                                                                </label>
                                                                <textarea name="catatan_validasi" class="form-control form-control-sm rounded-3" rows="3"
                                                                    placeholder="Tuliskan alasan penolakan setoran..." required></textarea>
                                                            </div>
                                                            <div class="modal-footer border-top py-3 px-4">
                                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger btn-sm rounded-3 px-4">
                                                                    <i class="bi bi-x-circle me-1"></i> Tolak Setoran
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @elseif(in_array(auth()->user()->peran?->nama_peran, ['supervisor','pj_lantai','admin']))
                                            <div class="d-flex gap-1 justify-content-center">
                                                <form action="{{ route('sampah.reset', $setoran->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Kembalikan status setoran ini ke Menunggu?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm btn-outline-warning rounded-2 px-2" title="Kembalikan ke status Menunggu">
                                                        <i class="bi bi-arrow-counterclockwise" style="font-size:0.8rem;"></i> Reset
                                                    </button>
                                                </form>
                                                @if(in_array(auth()->user()->peran?->nama_peran, ['admin', 'supervisor']))
                                                <form action="{{ route('sampah.destroy', $setoran->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Hapus riwayat setoran ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Hapus Setoran">
                                                        <i class="bi bi-trash" style="font-size:0.8rem;"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted" style="font-size:0.75rem;">— Selesai —</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
