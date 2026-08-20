@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { font-family: 'Inter', sans-serif; background: #f4f6f9; }

    /* Header statistik */
    .stat-card {
        background: white; border-radius: 16px;
        padding: 1rem 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        border-left: 4px solid;
    }
    .stat-card.pending  { border-color: #f59e0b; }
    .stat-card.aman     { border-color: #10B981; }
    .stat-card.menipis  { border-color: #ef4444; }

    /* Tabel permintaan */
    .permintaan-card {
        background: white; border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        overflow: hidden; margin-bottom: 1.5rem;
    }
    .permintaan-card .card-header-custom {
        background: white; padding: 1rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex; align-items: center; justify-content: space-between;
    }
    .permintaan-card .card-header-custom h6 {
        font-weight: 700; margin: 0; font-size: 0.95rem;
    }
    .tabel-permintaan { width: 100%; border-collapse: collapse; }
    .tabel-permintaan thead tr { background: #f9fafb; }
    .tabel-permintaan th {
        padding: 0.65rem 1rem; font-size: 0.75rem;
        font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.04em;
        text-align: left; border-bottom: 1px solid #f3f4f6;
    }
    .tabel-permintaan td {
        padding: 0.85rem 1rem; font-size: 0.85rem;
        border-bottom: 1px solid #f3f4f6; vertical-align: middle;
    }
    .tabel-permintaan tr:last-child td { border-bottom: none; }
    .tabel-permintaan tr:hover td { background: #f9fafb; }

    /* Status pills */
    .pill {
        padding: 4px 12px; border-radius: 20px;
        font-size: 0.72rem; font-weight: 700; white-space: nowrap;
    }
    .pill-pending   { background: #fef3c7; color: #d97706; }
    .pill-disetujui { background: #d1fae5; color: #059669; }
    .pill-ditolak   { background: #fee2e2; color: #dc2626; }
    .pill-menipis   { background: #fef3c7; color: #d97706; }
    .pill-aman      { background: #d1fae5; color: #059669; }
    .pill-habis     { background: #fee2e2; color: #dc2626; }

    /* Tombol aksi */
    .btn-setujui {
        background: #10B981; color: white; border: none;
        border-radius: 10px; padding: 6px 14px;
        font-size: 0.78rem; font-weight: 700;
        transition: all 0.2s; cursor: pointer;
    }
    .btn-setujui:hover { background: #059669; }
    .btn-tolak {
        background: #fee2e2; color: #dc2626; border: none;
        border-radius: 10px; padding: 6px 14px;
        font-size: 0.78rem; font-weight: 700;
        transition: all 0.2s; cursor: pointer; margin-left: 4px;
    }
    .btn-tolak:hover { background: #fecaca; }

    /* Modal tolak */
    .modal-konten {
        background: white; border-radius: 20px;
        padding: 1.5rem; max-width: 420px; width: 100%;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(0,0,0,0.5); z-index: 1000;
        align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-overlay.tampil { display: flex; }
    .form-control-custom {
        width: 100%; border: 1.5px solid #e5e7eb;
        border-radius: 12px; padding: 0.65rem 0.85rem;
        font-size: 0.85rem; resize: none;
    }
    .form-control-custom:focus {
        outline: none; border-color: #1E40AF;
        box-shadow: 0 0 0 3px rgba(30,64,175,0.1);
    }

    /* Stok barang */
    .stok-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 0.75rem; }
    .stok-item {
        background: white; border-radius: 14px;
        padding: 0.85rem 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border-left: 3px solid;
    }
    .stok-item.aman    { border-color: #10B981; }
    .stok-item.menipis { border-color: #f59e0b; }
    .stok-item.habis   { border-color: #ef4444; }

    /* Nama pemohon */
    .nama-cs { font-weight: 700; font-size: 0.88rem; }
    .waktu-cs { font-size: 0.72rem; color: #9ca3af; }
</style>

<div class="container-fluid py-4">

    {{-- Page title --}}
    <div class="d-flex align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0"><i class="bi bi-box-seam text-success me-2"></i>Manajemen Permintaan Barang</h4>
            <p class="text-secondary mb-0" style="font-size:0.85rem;">Kelola permintaan dari petugas cleaning service</p>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('sukses'))
        <div class="alert alert-success rounded-3 alert-dismissible fade show mb-4" style="font-size:0.85rem;">
            <i class="bi bi-check-circle me-1"></i> {{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('gagal'))
        <div class="alert alert-danger rounded-3 alert-dismissible fade show mb-4" style="font-size:0.85rem;">
            <i class="bi bi-exclamation-circle me-1"></i> {{ session('gagal') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info rounded-3 alert-dismissible fade show mb-4" style="font-size:0.85rem;">
            <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4 col-6">
            <div class="stat-card pending">
                <div class="text-secondary small mb-1">Menunggu Persetujuan</div>
                <div class="fs-2 fw-bold text-warning">{{ $permintaanPending->count() }}</div>
                <div class="text-secondary" style="font-size:0.75rem;">permintaan</div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="stat-card aman">
                <div class="text-secondary small mb-1">Diproses Hari Ini</div>
                <div class="fs-2 fw-bold text-success">{{ $sudahDiproses->count() }}</div>
                <div class="text-secondary" style="font-size:0.75rem;">permintaan</div>
            </div>
        </div>
        <div class="col-md-4 col-6">
            <div class="stat-card menipis">
                <div class="text-secondary small mb-1">Stok Menipis</div>
                <div class="fs-2 fw-bold text-danger">{{ $semuaBarang->filter->stokMenipis()->count() }}</div>
                <div class="text-secondary" style="font-size:0.75rem;">jenis barang</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kolom kiri: Permintaan Pending --}}
        <div class="col-lg-8">

            {{-- Permintaan Pending --}}
            <div class="permintaan-card mb-4">
                <div class="card-header-custom">
                    <h6><i class="bi bi-hourglass-split text-warning me-2"></i>Permintaan Menunggu Persetujuan</h6>
                    @if($permintaanPending->count() > 0)
                        <span class="pill pill-pending">{{ $permintaanPending->count() }} pending</span>
                    @endif
                </div>
                @if($permintaanPending->count() > 0)
                <div class="table-responsive">
                    <table class="tabel-permintaan">
                        <thead>
                            <tr>
                                <th>Pemohon</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Stok Ada</th>
                                <th>Waktu</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permintaanPending as $p)
                            <tr>
                                <td>
                                    <div class="nama-cs">{{ $p->pengguna->name }}</div>
                                    <div class="waktu-cs">{{ $p->pengguna->peran->nama_peran ?? '-' }}</div>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $p->barang->nama_barang }}</div>
                                    <div class="waktu-cs">{{ $p->barang->satuan }}</div>
                                </td>
                                <td class="fw-bold">{{ $p->jumlah }} {{ $p->barang->satuan }}</td>
                                <td>
                                    @php
                                        $stok = $p->barang->stok_saat_ini;
                                        $cukup = $stok >= $p->jumlah;
                                    @endphp
                                    <span class="pill {{ $cukup ? 'pill-aman' : 'pill-habis' }}">
                                        {{ $stok }} {{ $p->barang->satuan }}
                                        {{ $cukup ? '✓' : '⚠' }}
                                    </span>
                                </td>
                                <td class="waktu-cs">{{ $p->waktu_request->diffForHumans() }}</td>
                                <td>
                                    {{-- Tombol Setujui --}}
                                    <form action="{{ route('barang.setujui', $p->id) }}" method="POST" class="d-inline"
                                          onsubmit="return {{ $cukup ? 'true' : "confirm('Stok tidak mencukupi! Yakin ingin menyetujui?')" }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-setujui">
                                            <i class="bi bi-check2"></i> Setujui
                                        </button>
                                    </form>
                                    {{-- Tombol Tolak --}}
                                    <button type="button" class="btn-tolak"
                                            onclick="bukaModalTolak({{ $p->id }}, '{{ addslashes($p->pengguna->name) }}', '{{ addslashes($p->barang->nama_barang) }}')">
                                        <i class="bi bi-x"></i> Tolak
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-secondary">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Tidak ada permintaan yang menunggu persetujuan.
                </div>
                @endif
            </div>

            {{-- Sudah diproses hari ini --}}
            <div class="permintaan-card">
                <div class="card-header-custom">
                    <h6><i class="bi bi-clock-history text-secondary me-2"></i>Diproses Hari Ini</h6>
                </div>
                @if($sudahDiproses->count() > 0)
                <div class="table-responsive">
                    <table class="tabel-permintaan">
                        <thead>
                            <tr>
                                <th>Pemohon</th>
                                <th>Barang</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sudahDiproses as $p)
                            <tr>
                                <td><div class="fw-semibold">{{ $p->pengguna->name }}</div></td>
                                <td>{{ $p->barang->nama_barang }}</td>
                                <td>{{ $p->jumlah }} {{ $p->barang->satuan }}</td>
                                <td>
                                    <span class="pill pill-{{ $p->status_request }}">
                                        {{ $p->status_request === 'disetujui' ? 'Disetujui ✓' : 'Ditolak ✗' }}
                                    </span>
                                    @if($p->status_request === 'ditolak' && $p->alasan_penolakan)
                                        <div class="text-danger mt-1" style="font-size:0.7rem;">{{ $p->alasan_penolakan }}</div>
                                    @endif
                                </td>
                                <td class="waktu-cs">{{ $p->waktu_approve?->format('H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4 text-secondary" style="font-size:0.85rem;">
                    Belum ada permintaan yang diproses hari ini.
                </div>
                @endif
            </div>
        </div>

        {{-- Kolom kanan: Stok Barang --}}
        <div class="col-lg-4">
            <div class="permintaan-card">
                <div class="card-header-custom">
                    <h6><i class="bi bi-archive text-success me-2"></i>Stok Barang</h6>
                </div>
                <div class="p-3">
                    @foreach($semuaBarang as $barang)
                        @php
                            if ($barang->stok_saat_ini == 0) {
                                $kelas = 'habis'; $teks = 'Habis';
                            } elseif ($barang->stokMenipis()) {
                                $kelas = 'menipis'; $teks = 'Menipis';
                            } else {
                                $kelas = 'aman'; $teks = 'Aman';
                            }
                        @endphp
                        <div class="stok-item {{ $kelas }} mb-2">
                            <div class="fw-semibold" style="font-size:0.85rem;">{{ $barang->nama_barang }}</div>
                            <div class="d-flex justify-content-between align-items-center mt-1">
                                <span class="fw-bold fs-5">{{ $barang->stok_saat_ini }}</span>
                                <div class="text-end">
                                    <div style="font-size:0.72rem; color:#9ca3af;">{{ $barang->satuan }}</div>
                                    <span class="pill pill-{{ $kelas === 'aman' ? 'aman' : ($kelas === 'menipis' ? 'menipis' : 'ditolak') }}" style="font-size:0.65rem;">
                                        {{ $teks }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-1" style="background:#f3f4f6; border-radius:4px; height:4px; overflow:hidden;">
                                @php
                                    $total = max($barang->stok_minimum * 4, $barang->stok_saat_ini);
                                    $persen = $total > 0 ? min(100, ($barang->stok_saat_ini / $total) * 100) : 0;
                                    $warnaProg = $kelas === 'aman' ? '#10B981' : ($kelas === 'menipis' ? '#f59e0b' : '#ef4444');
                                @endphp
                                <div style="width:{{ $persen }}%; background:{{ $warnaProg }}; height:100%; border-radius:4px; transition:width 0.3s;"></div>
                            </div>
                            <div class="text-secondary mt-1" style="font-size:0.68rem;">Min: {{ $barang->stok_minimum }} {{ $barang->satuan }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tolak --}}
<div class="modal-overlay" id="modal-tolak">
    <div class="modal-konten">
        <h6 class="fw-bold mb-1">Tolak Permintaan</h6>
        <p class="text-secondary mb-3" style="font-size:0.82rem;" id="modal-keterangan">-</p>

        <form id="form-tolak" method="POST">
            @csrf
            @method('PATCH')
            <label style="font-size:0.82rem; font-weight:600; color:#374151;" class="mb-1 d-block">
                Alasan Penolakan <span class="text-danger">*</span>
            </label>
            <textarea name="alasan_penolakan" class="form-control-custom mb-3" rows="3"
                placeholder="Contoh: Stok tidak mencukupi, silakan ambil dari gudang B..."
                required maxlength="500" id="textarea-alasan"></textarea>

            <div class="d-flex gap-2">
                <button type="submit" class="btn-setujui" style="flex:1; background:#dc2626;">
                    <i class="bi bi-x-circle me-1"></i> Tolak Permintaan
                </button>
                <button type="button" class="btn-batal" onclick="tutupModal()"
                    style="flex:1; background:#f3f4f6; border:none; border-radius:10px; padding:6px; font-size:0.82rem; font-weight:600; color:#374151;">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTolak(id, namaPemohon, namaBarang) {
        document.getElementById('modal-keterangan').textContent =
            'Tolak permintaan ' + namaBarang + ' dari ' + namaPemohon + '?';
        document.getElementById('form-tolak').action = '/barang/' + id + '/tolak';
        document.getElementById('textarea-alasan').value = '';
        document.getElementById('modal-tolak').classList.add('tampil');
    }

    function tutupModal() {
        document.getElementById('modal-tolak').classList.remove('tampil');
    }

    // Tutup modal jika klik di luar
    document.getElementById('modal-tolak').addEventListener('click', function(e) {
        if (e.target === this) tutupModal();
    });
</script>
@endsection
