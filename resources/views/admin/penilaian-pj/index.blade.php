@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid py-4 px-lg-4">

    {{-- ================================================================
         HEADER & LINK SHARING BOX
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-clipboard-check text-success"></i> Penilaian Kinerja CS dari PJ Lantai
            </h4>
            <p class="text-muted small mb-0">
                Monitoring respon formulir penilaian kebersihan yang diisi langsung oleh para Penanggung Jawab Lantai / Ruangan.
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('penilaian-pj.form') }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Form Publik
            </a>
            <button type="button" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm" onclick="salinLinkForm()">
                <i class="bi bi-link-45deg me-1"></i> Salin Link Formulir
            </button>
        </div>
    </div>

    {{-- Banner Berbagi Tautan Form Publik --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 bg-success bg-opacity-10 border-start border-4 border-success">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; font-size: 1.25rem;">
                    <i class="bi bi-share-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size: 0.95rem;">Tautan Formulir Publik untuk PJ Lantai</div>
                    <div class="text-muted small">
                        Formulir dapat diakses oleh PJ Lantai langsung melalui HP atau browser tanpa perlu login ke sistem.
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                <input type="text" class="form-control form-control-sm bg-white" id="inputUrlPublik" value="{{ $urlFormPublik }}" readonly style="max-width: 380px;">
                <button class="btn btn-success btn-sm rounded-3 px-3 text-nowrap" onclick="salinLinkForm()">
                    <i class="bi bi-clipboard me-1"></i> <span id="textTombolSalin">Salin</span>
                </button>
            </div>
        </div>
    </div>

    @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================================================================
         STATISTIK RINGKAS
         ================================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary small fw-semibold text-uppercase">Total Penilaian</span>
                    <div class="p-2 rounded-3" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">
                        <i class="bi bi-file-earmark-text fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ number_format($totalMasuk) }}</div>
                <div class="text-muted small">Seluruh respon masuk</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary small fw-semibold text-uppercase">Bulan Ini</span>
                    <div class="p-2 rounded-3" style="background: rgba(16, 185, 129, 0.12); color: #059669;">
                        <i class="bi bi-calendar-check fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ number_format($totalBulanIni) }}</div>
                <div class="text-muted small">Penilaian masuk bulan {{ now()->translatedFormat('F Y') }}</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary small fw-semibold text-uppercase">Rata-rata Skor</span>
                    <div class="p-2 rounded-3" style="background: rgba(245, 158, 11, 0.15); color: #d97706;">
                        <i class="bi bi-star-half fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ $rataRataSemua }} <span class="fs-6 text-muted fw-normal">/ 95</span></div>
                <div class="text-muted small">Rata-rata skor seluruh penilaian</div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-secondary small fw-semibold text-uppercase">Predikat Umum</span>
                    <div class="p-2 rounded-3" style="background: rgba(6, 182, 212, 0.12); color: #0891b2;">
                        <i class="bi bi-award fs-5"></i>
                    </div>
                </div>
                <div class="fs-3 fw-bold text-dark mb-1">{{ $kategoriUmum }}</div>
                <div class="text-muted small">Standar kategori kepuasan</div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         FILTER & TABEL DATA
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">

            {{-- Form Filter --}}
            <form method="GET" action="{{ route('admin.penilaian-pj.index') }}" class="row g-2 align-items-end mb-4">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">CARI PETUGAS / PJ / LOKASI</label>
                    <input type="text" name="cari" class="form-control form-control-sm rounded-3"
                           placeholder="Ketik nama petugas atau PJ..." value="{{ $cari }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">BULAN</label>
                    <select name="bulan" class="form-select form-select-sm rounded-3">
                        <option value="">-- Semua Bulan --</option>
                        @foreach($daftarBulan as $no => $nama)
                            <option value="{{ $no }}" {{ $bulan == $no ? 'selected' : '' }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold text-muted mb-1">TAHUN</label>
                    <select name="tahun" class="form-select form-select-sm rounded-3">
                        @foreach($daftarTahun as $th)
                            <option value="{{ $th }}" {{ $tahun == $th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success btn-sm rounded-3 px-3">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    @if($cari || $bulan)
                        <a href="{{ route('admin.penilaian-pj.index', ['tahun' => $tahun]) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-2">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Tabel Respons Penilaian --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>Tanggal</th>
                            <th>Petugas CS</th>
                            <th>Lokasi Penugasan</th>
                            <th>Nama PJ Kebersihan</th>
                            <th class="text-center">Rata-rata Skor</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Bukti</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penilaian as $key => $p)
                            <tr>
                                <td>{{ $penilaian->firstItem() + $key }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $p->tanggal_penilaian->translatedFormat('d M Y') }}</div>
                                    <div class="text-muted small">{{ $p->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                            {{ strtoupper(substr($p->nama_petugas_cs, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $p->nama_petugas_cs }}</div>
                                            @if($p->petugasCs)
                                                <div class="text-muted small">CS Terdaftar (NIK: {{ $p->petugasCs->nik ?? '-' }})</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-dark">{{ $p->lokasi_tugas }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary">{{ $p->nama_pj }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fs-6 fw-bold text-dark">{{ $p->rata_rata }}</span>
                                    <span class="text-muted small">/ 95</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $p->badgeKategori() }} rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                        {{ $p->kategori }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php $jmlBukti = !empty($p->bukti_dokumentasi) ? count($p->bukti_dokumentasi) : 0; @endphp
                                    @if($jmlBukti > 0)
                                        <span class="badge bg-light text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                            <i class="bi bi-paperclip me-1"></i>{{ $jmlBukti }} File
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.penilaian-pj.show', $p->id) }}" class="btn btn-outline-success btn-sm rounded-pill px-2 py-1" title="Lihat Lembar Jawaban">
                                            <i class="bi bi-eye me-1"></i> Detail
                                        </a>
                                        <form action="{{ route('admin.penilaian-pj.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penilaian ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                    Belum ada data penilaian dari PJ Lantai pada kriteria ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $penilaian->withQueryString()->links() }}
            </div>
        </div>
    </div>

</div>

<script>
    function salinLinkForm() {
        const inputUrl = document.getElementById('inputUrlPublik');
        inputUrl.select();
        inputUrl.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(inputUrl.value).then(function() {
            const btnText = document.getElementById('textTombolSalin');
            if (btnText) {
                btnText.innerText = 'Tersalin!';
                setTimeout(() => { btnText.innerText = 'Salin'; }, 2000);
            }
            alert('Tautan formulir berhasil disalin ke clipboard:\n' + inputUrl.value);
        }).catch(function(err) {
            alert('Gagal menyalin otomatis. Silakan salin manual tautan berikut:\n' + inputUrl.value);
        });
    }
</script>
@endsection
