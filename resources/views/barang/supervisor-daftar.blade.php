@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-shield-check text-primary me-2"></i>Persetujuan Permintaan Barang
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">APPROVAL SUPERVISOR — PERMINTAAN BARANG CS</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('dasbor.supervisor') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dasbor
            </a>
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
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- STATISTIK RINGKAS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-warning-subtle"><i class="bi bi-hourglass-split text-warning fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $permintaanMenunggu->count() }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Menunggu Persetujuan Anda</div>
                        <div class="text-secondary" style="font-size:0.7rem;">Belum diproses supervisor</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-info-subtle"><i class="bi bi-send-check text-info fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-dark">{{ $sudahDiproses->where('status_request', 'disetujui_spv')->count() + $sudahDiproses->where('status_request', 'disetujui')->count() }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Diteruskan ke Gudang</div>
                        <div class="text-secondary" style="font-size:0.7rem;">{{ ucwords(str_replace('_', ' ', $periode ?? 'bulan ini')) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 p-2 bg-danger-subtle"><i class="bi bi-x-circle text-danger fs-5"></i></div>
                    <div>
                        <div class="fw-bold fs-5 text-danger">{{ $sudahDiproses->where('status_request', 'ditolak_spv')->count() }}</div>
                        <div class="text-muted" style="font-size:0.78rem;">Ditolak Supervisor</div>
                        <div class="text-secondary" style="font-size:0.7rem;">{{ ucwords(str_replace('_', ' ', $periode ?? 'bulan ini')) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ALUR PERSETUJUAN INFO --}}
    <div class="alert alert-primary border-0 shadow-sm rounded-3 d-flex align-items-center gap-3 mb-4 py-3">
        <i class="bi bi-diagram-3-fill fs-4 text-primary flex-shrink-0"></i>
        <div style="font-size:0.84rem;">
            <strong>Alur Persetujuan:</strong>
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle ms-1">1. CS Ajukan</span>
            <i class="bi bi-arrow-right mx-1 text-muted"></i>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">2. Supervisor Setujui ← Anda di sini</span>
            <i class="bi bi-arrow-right mx-1 text-muted"></i>
            <span class="badge bg-success-subtle text-success border border-success-subtle">3. Gudang Proses & Keluarkan Barang</span>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">

            {{-- 1. ANTRIAN MENUNGGU PERSETUJUAN SUPERVISOR --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                    <h6 class="fw-bold mb-0 text-warning">
                        <i class="bi bi-hourglass-split me-2"></i>ANTRIAN PERMINTAAN — MENUNGGU PERSETUJUAN SUPERVISOR
                    </h6>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle" style="font-size:0.75rem;">
                        {{ $permintaanMenunggu->count() }} permintaan
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($permintaanMenunggu->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width:150px;">Pemohon</th>
                                    <th>Barang Diminta</th>
                                    <th class="text-center" style="width:90px;">Jumlah</th>
                                    <th class="text-center" style="width:110px;">Stok Gudang</th>
                                    <th class="text-center" style="width:120px;">Waktu Ajukan</th>
                                    <th class="text-center" style="width:160px;">Aksi Supervisor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($permintaanMenunggu as $p)
                                @php
                                    $stok  = $p->barang->stok_saat_ini;
                                    $cukup = $stok >= $p->jumlah;
                                @endphp
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $p->pengguna->name }}</div>
                                        <div class="text-muted" style="font-size:0.72rem;">{{ $p->pengguna->peran->nama_peran ?? '-' }}</div>
                                        @if($p->kode_transaksi)
                                            <span class="badge bg-light text-secondary border font-monospace" style="font-size:0.65rem;">{{ $p->kode_transaksi }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $p->barang->nama_barang }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Satuan: {{ $p->barang->satuan }}</div>
                                    </td>
                                    <td class="text-center fw-bold text-dark">{{ $p->jumlah }}</td>
                                    <td class="text-center">
                                        <span class="badge {{ $cukup ? 'bg-success-subtle text-success border-success-subtle' : 'bg-danger-subtle text-danger border-danger-subtle' }} border rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                            {{ $stok }} {{ $p->barang->satuan }} {{ $cukup ? '✓' : '⚠' }}
                                        </span>
                                    </td>
                                    <td class="text-center text-muted" style="font-size:0.78rem;">
                                        <div>{{ $p->waktu_request->translatedFormat('d M Y') }}</div>
                                        <small>{{ $p->waktu_request->format('H:i') }} WIB</small>
                                        <div class="text-secondary" style="font-size:0.7rem;">{{ $p->waktu_request->diffForHumans() }}</div>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            {{-- Tombol Setujui --}}
                                            <form action="{{ route('barang.setujui-spv', $p->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('Setujui permintaan {{ addslashes($p->pengguna->name) }} untuk {{ addslashes($p->barang->nama_barang) }}?\nPermintaan akan diteruskan ke gudang.')">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-primary rounded-2 px-2" title="Setujui & Teruskan ke Gudang">
                                                    <i class="bi bi-check-lg" style="font-size:0.8rem;"></i> Setujui
                                                </button>
                                            </form>
                                            {{-- Tombol Tolak --}}
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-2 px-2" title="Tolak"
                                                    onclick="bukaModalTolakSpv({{ $p->id }}, '{{ addslashes($p->pengguna->name) }}', '{{ addslashes($p->barang->nama_barang) }}')">
                                                <i class="bi bi-x-lg" style="font-size:0.8rem;"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle fs-1 d-block mb-2 text-success opacity-50"></i>
                        <div class="fw-semibold">Tidak ada permintaan yang menunggu persetujuan.</div>
                        <small>Semua permintaan sudah diproses.</small>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 2. RIWAYAT YANG SUDAH DIPROSES SUPERVISOR --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                    <div>
                        <h6 class="fw-bold mb-0 text-secondary">
                            <i class="bi bi-clock-history me-2"></i>RIWAYAT PERSETUJUAN SUPERVISOR
                        </h6>
                        <small class="text-muted" style="font-size:0.75rem;">Semua permintaan yang sudah Anda proses</small>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle" style="font-size:0.75rem;">
                        {{ $sudahDiproses->total() }} total data
                    </span>
                </div>

                {{-- Filter --}}
                <div class="p-3 bg-light border-bottom">
                    <form method="GET" action="{{ route('barang.supervisor') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3 col-6">
                                <label class="form-label text-muted small mb-1 fw-semibold">Periode</label>
                                <select name="periode" id="select-periode" class="form-select form-select-sm rounded-3" onchange="toggleKustomTanggal(this.value)">
                                    <option value="bulan_ini" {{ ($periode ?? 'bulan_ini') === 'bulan_ini' ? 'selected' : '' }}>Bulan Ini</option>
                                    <option value="hari_ini" {{ ($periode ?? '') === 'hari_ini' ? 'selected' : '' }}>Hari Ini</option>
                                    <option value="minggu_ini" {{ ($periode ?? '') === 'minggu_ini' ? 'selected' : '' }}>Minggu Ini</option>
                                    <option value="kustom" {{ ($periode ?? '') === 'kustom' ? 'selected' : '' }}>Rentang Kustom</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-6 kustom-tanggal-wrap" style="{{ ($periode ?? '') === 'kustom' ? '' : 'display:none;' }}">
                                <label class="form-label text-muted small mb-1 fw-semibold">Dari</label>
                                <input type="date" name="dari" class="form-control form-control-sm rounded-3" value="{{ request('dari') }}">
                            </div>
                            <div class="col-md-2 col-6 kustom-tanggal-wrap" style="{{ ($periode ?? '') === 'kustom' ? '' : 'display:none;' }}">
                                <label class="form-label text-muted small mb-1 fw-semibold">Sampai</label>
                                <input type="date" name="sampai" class="form-control form-control-sm rounded-3" value="{{ request('sampai') }}">
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="form-label text-muted small mb-1 fw-semibold">Cari Barang / Pemohon</label>
                                <input type="text" name="cari" class="form-control form-control-sm rounded-3" placeholder="Nama / Kode..." value="{{ request('cari') }}">
                            </div>
                            <div class="col-12 col-md-auto ms-auto d-flex gap-2 justify-content-end pt-1">
                                <a href="{{ route('barang.supervisor') }}" class="btn btn-sm btn-outline-secondary rounded-3 px-3">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                                </a>
                                <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3">
                                    <i class="bi bi-funnel me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    @if($sudahDiproses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3" style="width:150px;">Pemohon</th>
                                    <th>Barang</th>
                                    <th class="text-center" style="width:90px;">Jumlah</th>
                                    <th class="text-center" style="width:130px;">Status</th>
                                    <th class="text-center" style="width:130px;">Waktu Diproses</th>
                                    <th style="width:180px;">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sudahDiproses as $p)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $p->pengguna->name }}</div>
                                        <div class="text-muted" style="font-size:0.72rem;">
                                            <span class="badge bg-light text-secondary border font-monospace">{{ $p->kode_transaksi ?: 'REQ-'.$p->id }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $p->barang->nama_barang }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">Satuan: {{ $p->barang->satuan }}</div>
                                    </td>
                                    <td class="text-center fw-bold text-dark">{{ $p->jumlah }} {{ $p->barang->satuan }}</td>
                                    <td class="text-center">
                                        @php
                                            $badgeClass = match($p->status_request) {
                                                'disetujui_spv' => 'bg-info-subtle text-info border-info-subtle',
                                                'ditolak_spv'   => 'bg-danger-subtle text-danger border-danger-subtle',
                                                'disetujui'     => 'bg-success-subtle text-success border-success-subtle',
                                                'ditolak'       => 'bg-danger-subtle text-danger border-danger-subtle',
                                                default         => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                            };
                                            $labelStatus = match($p->status_request) {
                                                'disetujui_spv' => '→ Menunggu Gudang',
                                                'ditolak_spv'   => '✗ Ditolak Spv',
                                                'disetujui'     => '✓ Selesai',
                                                'ditolak'       => '✗ Ditolak Gudang',
                                                default         => $p->status_request,
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} border rounded-pill px-2 py-1" style="font-size:0.72rem;">
                                            {{ $labelStatus }}
                                        </span>
                                    </td>
                                    <td class="text-center text-muted" style="font-size:0.78rem;">
                                        @if($p->waktu_approve_spv)
                                            <div>{{ $p->waktu_approve_spv->translatedFormat('d M Y') }}</div>
                                            <small>{{ $p->waktu_approve_spv->format('H:i') }} WIB</small>
                                        @else
                                            <span class="text-secondary">-</span>
                                        @endif
                                    </td>
                                    <td style="font-size:0.75rem;">
                                        @if($p->catatan_spv)
                                            <span class="text-danger" title="{{ $p->catatan_spv }}">
                                                <i class="bi bi-chat-square-text me-1"></i>
                                                {{ Str::limit($p->catatan_spv, 60) }}
                                            </span>
                                        @elseif($p->status_request === 'disetujui_spv' || $p->status_request === 'disetujui')
                                            <span class="text-success"><i class="bi bi-check-circle me-1"></i>Disetujui supervisor</span>
                                        @else
                                            <span class="text-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($sudahDiproses->hasPages())
                    <div class="d-flex justify-content-center py-3">
                        {{ $sudahDiproses->links() }}
                    </div>
                    @endif
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                        Belum ada riwayat persetujuan pada periode ini.
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

{{-- MODAL TOLAK SUPERVISOR --}}
<div class="modal fade" id="modalTolakSpv" tabindex="-1" aria-labelledby="modalTolakSpvLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="modalTolakSpvLabel">
                    <i class="bi bi-x-circle-fill me-2"></i>Tolak Permintaan Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted mb-3" style="font-size:0.875rem;">
                    Anda akan menolak permintaan dari <strong id="spvTolakNamaPemohon"></strong>
                    untuk <strong id="spvTolakNamaBarang"></strong>. CS dapat mengajukan ulang setelah ditolak.
                </p>
                <form id="formTolakSpv" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label for="catatan_spv_input" class="form-label fw-semibold small text-uppercase" style="letter-spacing:.4px;">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="catatan_spv" id="catatan_spv_input" rows="3"
                            class="form-control rounded-3"
                            placeholder="Masukkan alasan penolakan permintaan ini..." required maxlength="500"></textarea>
                        <div class="form-text">Maks. 500 karakter. Alasan ini akan terlihat di riwayat permintaan CS.</div>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-3 px-4">
                            <i class="bi bi-x-lg me-1"></i>Tolak Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function bukaModalTolakSpv(id, namaPemohon, namaBarang) {
        document.getElementById('spvTolakNamaPemohon').textContent = namaPemohon;
        document.getElementById('spvTolakNamaBarang').textContent  = namaBarang;
        document.getElementById('catatan_spv_input').value = '';
        document.getElementById('formTolakSpv').action = '/barang/' + id + '/tolak-spv';

        // Update method PATCH
        let methodInput = document.querySelector('#formTolakSpv input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type  = 'hidden';
            methodInput.name  = '_method';
            document.getElementById('formTolakSpv').appendChild(methodInput);
        }
        methodInput.value = 'PATCH';

        new bootstrap.Modal(document.getElementById('modalTolakSpv')).show();
    }

    function toggleKustomTanggal(val) {
        document.querySelectorAll('.kustom-tanggal-wrap').forEach(el => {
            el.style.display = val === 'kustom' ? '' : 'none';
        });
    }
</script>
@endpush
