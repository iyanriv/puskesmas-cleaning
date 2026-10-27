@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-clipboard-check-fill text-success me-2"></i>Penilaian PJ Lantai
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">MONITORING RESPON FORMULIR EVALUASI KEBERSIHAN</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.penilaian-pj.export-excel', array_filter(['bulan' => $bulan, 'tahun' => $tahun, 'cari' => $cari])) }}"
               class="btn btn-outline-success btn-sm rounded-3 px-3">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
            <a href="{{ route('penilaian-pj.form') }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Form Publik
            </a>
            <button type="button" class="btn btn-success btn-sm rounded-3 px-3" onclick="salinLinkForm()">
                <i class="bi bi-link-45deg me-1"></i> <span id="textTombolSalinTop">Salin Link Formulir</span>
            </button>
        </div>
    </div>

    {{-- Banner Berbagi Tautan Form Publik --}}
    <div class="card border-0 rounded-4 shadow-sm mb-4 bg-success bg-opacity-10 border-start border-4 border-success">
        <div class="card-body p-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-success text-white p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; font-size: 1.1rem;">
                    <i class="bi bi-share-fill"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark" style="font-size: 0.92rem;">Tautan Formulir Publik untuk PJ Lantai</div>
                    <div class="text-muted" style="font-size: 0.8rem;">
                        Formulir dapat diakses oleh PJ Lantai langsung melalui HP atau browser tanpa perlu login ke sistem.
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 w-100 w-md-auto">
                <input type="text" class="form-control form-control-sm bg-white rounded-3 font-monospace" id="inputUrlPublik" value="{{ $urlFormPublik }}" readonly style="max-width: 380px; font-size:0.82rem;">
                <button class="btn btn-success btn-sm rounded-3 px-3 text-nowrap" onclick="salinLinkForm()">
                    <i class="bi bi-clipboard me-1"></i> <span id="textTombolSalin">Salin</span>
                </button>
            </div>
        </div>
    </div>

    {{-- FLASH ALERT --}}
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
         STATISTIK RINGKAS
         ================================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-primary-subtle"><i class="bi bi-file-earmark-text text-primary fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ number_format($totalMasuk) }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Total Penilaian</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-success-subtle"><i class="bi bi-calendar-check text-success fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ number_format($totalBulanIni) }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Bulan Ini</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning-subtle"><i class="bi bi-star-half text-warning fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $rataRataSemua }} <span class="fs-6 text-muted fw-normal">/ 95</span></div>
                        <div class="text-muted" style="font-size:0.78rem;">Rata-rata Skor</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-info-subtle"><i class="bi bi-award text-info fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $kategoriUmum }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Predikat Umum</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         PANEL FILTER
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.penilaian-pj.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-search text-success me-1"></i>Cari Petugas / PJ / Lokasi
                        </label>
                        <input type="text" name="cari" class="form-control form-control-sm rounded-3"
                               placeholder="Ketik nama petugas atau PJ..." value="{{ $cari }}">
                    </div>

                    <div class="col-6 col-md-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            Bulan
                        </label>
                        <select name="bulan" class="form-select form-select-sm rounded-3" style="min-width:130px;">
                            <option value="">— Semua Bulan —</option>
                            @foreach($daftarBulan as $no => $nama)
                                <option value="{{ $no }}" {{ $bulan == $no ? 'selected' : '' }}>{{ $nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-auto">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            Tahun
                        </label>
                        <select name="tahun" class="form-select form-select-sm rounded-3" style="min-width:90px;">
                            @foreach($daftarTahun as $th)
                                <option value="{{ $th }}" {{ $tahun == $th ? 'selected' : '' }}>{{ $th }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-3">
                            <i class="bi bi-funnel me-1"></i> Tampilkan
                        </button>
                    </div>

                    @if($cari || $bulan)
                    <div class="col-auto">
                        <a href="{{ route('admin.penilaian-pj.index', ['tahun' => $tahun]) }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
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
                <i class="bi bi-table me-2"></i>RINGKASAN PENILAIAN PJ LANTAI
            </h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                {{ $penilaian->total() }} penilaian
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
                            <th>Lokasi Penugasan</th>
                            <th>Nama PJ Kebersihan</th>
                            <th class="text-center" style="width:110px;">Rata-rata Skor</th>
                            <th class="text-center" style="width:110px;">Kategori</th>
                            <th class="text-center" style="width:80px;">Bukti</th>
                            <th class="text-center" style="width:120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($penilaian as $key => $p)
                            <tr>
                                <td class="ps-3 text-center text-muted">{{ $penilaian->firstItem() + $key }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $p->tanggal_penilaian->translatedFormat('d M Y') }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ $p->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $p->nama_petugas_cs }}</div>
                                    @if($p->petugasCs)
                                        <div class="text-muted" style="font-size:0.75rem;">Nomor Karyawan: {{ $p->petugasCs->nik ?? '-' }}</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-dark">{{ $p->lokasi_tugas }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-secondary">{{ $p->nama_pj }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold text-dark">{{ $p->rata_rata }}</span>
                                    <span class="text-muted" style="font-size:0.75rem;">/ 95</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $p->badgeKategori() }} rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                        {{ $p->kategori }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @php $jmlBukti = !empty($p->bukti_dokumentasi) ? count($p->bukti_dokumentasi) : 0; @endphp
                                    @if($jmlBukti > 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size:0.72rem;">
                                            <i class="bi bi-paperclip me-1"></i>{{ $jmlBukti }}
                                        </span>
                                    @else
                                        <span class="text-muted" style="font-size:0.75rem;">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="{{ route('admin.penilaian-pj.show', $p->id) }}"
                                           class="btn btn-outline-success btn-sm rounded-2 px-2"
                                           title="Lihat Detail">
                                            <i class="bi bi-eye" style="font-size:0.8rem;"></i>
                                        </a>
                                        <a href="{{ route('admin.penilaian-pj.cetak', $p->id) }}" target="_blank"
                                           class="btn btn-outline-secondary btn-sm rounded-2 px-2"
                                           title="Cetak PDF">
                                            <i class="bi bi-printer" style="font-size:0.8rem;"></i>
                                        </a>
                                        @if(auth()->user()->peran?->nama_peran === 'admin')
                                        <form action="{{ route('admin.penilaian-pj.destroy', $p->id) }}" method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data penilaian ini?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-2 px-2" title="Hapus">
                                                <i class="bi bi-trash" style="font-size:0.8rem;"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="bi bi-clipboard-x text-muted" style="font-size:2.5rem;"></i>
                                    <p class="text-muted mt-2 mb-0">Belum ada data penilaian dari PJ Lantai pada kriteria ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($penilaian->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $penilaian->withQueryString()->links() }}
                </div>
            @endif
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
            const btnTextTop = document.getElementById('textTombolSalinTop');
            if (btnText) {
                btnText.innerText = 'Tersalin!';
                setTimeout(() => { btnText.innerText = 'Salin'; }, 2000);
            }
            if (btnTextTop) {
                btnTextTop.innerText = 'Tersalin!';
                setTimeout(() => { btnTextTop.innerText = 'Salin Link Formulir'; }, 2000);
            }
            alert('Tautan formulir berhasil disalin ke clipboard:\n' + inputUrl.value);
        }).catch(function(err) {
            alert('Gagal menyalin otomatis. Silakan salin manual tautan berikut:\n' + inputUrl.value);
        });
    }
</script>
@endsection
