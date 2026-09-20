@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%; margin: 0 auto;
        background-color: #f4f7f6; min-height: 100vh;
        padding-bottom: 90px;
    }

    /* ── Header ── */
    .header-section {
        background: linear-gradient(135deg, #12a65a 0%, #0d8a4a 50%, #0a7040 100%);
        border-radius: 0 0 30px 30px;
        padding: 1.25rem 1.5rem 3.5rem 1.5rem; color: white;
    }

    /* ── Keranjang FAB (floating action button) ── */
    .fab-keranjang {
        position: fixed; bottom: 80px; right: 20px;
        background: #12a65a; color: white;
        border: none; border-radius: 50px;
        padding: 0.7rem 1.2rem;
        box-shadow: 0 6px 20px rgba(18,166,90,0.4);
        font-size: 0.85rem; font-weight: 700;
        display: flex; align-items: center; gap: 0.5rem;
        text-decoration: none; z-index: 1000;
        transition: all 0.2s;
    }
    .fab-keranjang:hover { background: #0d8a4a; color: white; transform: translateY(-2px); }
    .fab-keranjang .badge-count {
        background: white; color: #12a65a;
        border-radius: 20px; padding: 1px 8px;
        font-size: 0.78rem; font-weight: 800;
    }
    @media (min-width: 992px) {
        .fab-keranjang { bottom: 30px; right: 30px; }
    }

    /* ── Area konten overlap ── */
    .overlap-cards {
        margin-top: -2.5rem; padding: 0 1.2rem;
        position: relative; z-index: 10;
    }
    @media (min-width: 992px) { .overlap-cards { padding: 0 3rem; } }

    /* ── Tab navigasi ── */
    .tab-nav {
        background: white; border-radius: 16px;
        padding: 5px; display: flex; gap: 4px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        margin-bottom: 1.1rem;
    }
    .tab-btn {
        flex: 1; padding: 0.6rem;
        border: none; border-radius: 12px;
        background: transparent; font-size: 0.82rem;
        font-weight: 600; color: #6b7280; cursor: pointer;
        transition: all 0.2s;
    }
    .tab-btn.active { background: #12a65a; color: white; }

    /* ── Kartu barang ── */
    .barang-card {
        background: white; border-radius: 18px;
        padding: 1rem 1.1rem; margin-bottom: 0.75rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        transition: all 0.2s;
    }
    .barang-nama { font-weight: 700; font-size: 0.9rem; color: #1f2937; }
    .barang-deskripsi { font-size: 0.75rem; color: #9ca3af; margin-top: 2px; }
    .stok-badge {
        padding: 3px 10px; border-radius: 20px;
        font-size: 0.72rem; font-weight: 700;
    }
    .stok-aman    { background: #d1fae5; color: #059669; }
    .stok-menipis { background: #fef3c7; color: #d97706; }
    .stok-habis   { background: #fee2e2; color: #dc2626; }

    /* ── Ikon barang ── */
    .icon-barang {
        width: 44px; height: 44px; border-radius: 12px;
        background: #e8f6ef; color: #12a65a;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0; margin-right: 0.85rem;
    }

    /* ── Tombol tambah ke keranjang ── */
    .btn-keranjang-toggle {
        background: none; border: 1.5px solid #12a65a;
        color: #12a65a; border-radius: 10px; padding: 5px 13px;
        font-size: 0.78rem; font-weight: 700; cursor: pointer;
        transition: all 0.2s; flex-shrink: 0; white-space: nowrap;
    }
    .btn-keranjang-toggle:hover  { background: #e8f6ef; }
    .btn-keranjang-toggle.sudah  {
        background: #12a65a; color: white; border-color: #12a65a;
    }

    /* ── Form input jumlah (expand di bawah kartu) ── */
    .form-keranjang {
        display: none;
        margin-top: 0.75rem; padding-top: 0.75rem;
        border-top: 1px dashed #e5e7eb;
    }
    .form-keranjang.tampil { display: block; }

    .input-jumlah {
        display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.6rem;
    }
    .btn-kurang, .btn-tambah {
        width: 38px; height: 38px; border-radius: 10px;
        border: 1.5px solid #e5e7eb; background: #f9fafb;
        font-size: 1.1rem; font-weight: 700; color: #374151;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; flex-shrink: 0;
    }
    .btn-kurang:active, .btn-tambah:active { background: #e8f6ef; border-color: #12a65a; }
    .field-jumlah {
        flex: 1; text-align: center; border: 1.5px solid #e5e7eb;
        border-radius: 10px; padding: 0.45rem; font-size: 0.92rem; font-weight: 700;
    }
    .field-jumlah:focus { outline: none; border-color: #12a65a; }
    .btn-masuk-keranjang {
        width: 100%; background: #12a65a; color: white; border: none;
        border-radius: 12px; padding: 0.65rem;
        font-size: 0.85rem; font-weight: 700; transition: all 0.2s;
    }
    .btn-masuk-keranjang:active { transform: scale(0.98); }
    .btn-batal {
        width: 100%; background: #f3f4f6; color: #6b7280; border: none;
        border-radius: 12px; padding: 0.55rem; font-size: 0.8rem;
        margin-top: 0.4rem; cursor: pointer;
    }

    /* ── Status pill ── */
    .status-pill {
        padding: 3px 10px; border-radius: 20px;
        font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
    }
    .status-pending   { background: #fef3c7; color: #d97706; }
    .status-disetujui { background: #d1fae5; color: #059669; }
    .status-ditolak   { background: #fee2e2; color: #dc2626; }
    .status-keranjang { background: #e8f6ef; color: #059669; }

    /* ── Riwayat ── */
    .riwayat-item {
        background: white; border-radius: 14px;
        padding: 0.85rem 1rem; margin-bottom: 0.6rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        display: flex; align-items: center; gap: 0.75rem;
    }

    /* ── Label seksi ── */
    .section-label {
        font-size: 0.78rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.05em;
        margin-bottom: 0.65rem;
    }

    /* ── Toast keranjang (muncul sementara) ── */
    .toast-keranjang {
        position: fixed; top: 70px; left: 50%; transform: translateX(-50%);
        background: #1f2937; color: white; padding: 0.6rem 1.2rem;
        border-radius: 30px; font-size: 0.82rem; font-weight: 600;
        z-index: 2000; white-space: nowrap;
        opacity: 0; transition: opacity 0.25s ease;
        pointer-events: none;
    }
    .toast-keranjang.tampil { opacity: 1; }

    @media (min-width: 992px) {
        #konten-katalog { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; align-items: start; }
        .section-label { grid-column: 1 / -1; }
    }
</style>

{{-- Toast notif tambah keranjang --}}
<div class="toast-keranjang" id="toastKeranjang">
    <i class="bi bi-cart-check me-2"></i><span id="toastTeks">Ditambahkan ke keranjang</span>
</div>

<div class="mobile-container">

    {{-- Header --}}
    <div class="header-section">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('dasbor.cs') }}" class="text-white" style="font-size:1.2rem; line-height:1;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h5 class="fw-bold mb-0 text-white">Permintaan Barang</h5>
                    <p class="mb-0 text-white-50" style="font-size:0.8rem;">Pilih barang yang dibutuhkan</p>
                </div>
            </div>
            {{-- Ikon keranjang di header (badge jumlah item) --}}
            <a href="{{ route('barang.keranjang') }}" class="position-relative text-white" style="font-size:1.35rem; line-height:1;">
                <i class="bi bi-cart3"></i>
                @if($jumlahKeranjang > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark"
                          style="font-size:0.6rem; padding: 3px 6px;">{{ $jumlahKeranjang }}</span>
                @endif
            </a>
        </div>
    </div>

    <div class="overlap-cards">

        {{-- Flash sukses biasa --}}
        @if(session('sukses'))
            <div class="alert alert-success rounded-3 py-2 px-3 mb-3 alert-dismissible fade show border-0 shadow-sm" style="font-size:0.84rem;">
                <i class="bi bi-check-circle me-1"></i> {{ session('sukses') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('gagal'))
            <div class="alert alert-danger rounded-3 py-2 px-3 mb-3 alert-dismissible fade show border-0 shadow-sm" style="font-size:0.84rem;">
                <i class="bi bi-exclamation-circle me-1"></i> {{ session('gagal') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif
        {{-- Flash sukses keranjang (muncul sebentar via JS) --}}
        @if(session('sukses_keranjang'))
            <div id="dataFlashKeranjang" data-pesan="{{ session('sukses_keranjang') }}" style="display:none;"></div>
        @endif

        {{-- Banner keranjang tidak kosong --}}
        @if($jumlahKeranjang > 0)
            <a href="{{ route('barang.keranjang') }}"
               class="d-flex align-items-center justify-content-between bg-white rounded-3 p-3 mb-3 shadow-sm text-decoration-none"
               style="border: 1.5px solid #12a65a;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:10px;background:#e8f6ef;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-cart-check text-success" style="font-size:1.1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size:0.85rem;">Keranjang Anda</div>
                        <div class="text-muted" style="font-size:0.75rem;">{{ $jumlahKeranjang }} jenis barang siap dikirim</div>
                    </div>
                </div>
                <span class="text-success fw-bold" style="font-size:0.82rem;">Lihat & Kirim <i class="bi bi-arrow-right ms-1"></i></span>
            </a>
        @endif

        {{-- Tab Navigasi --}}
        <div class="tab-nav">
            <button class="tab-btn active" id="tab-katalog" onclick="gantiTab('katalog')">
                <i class="bi bi-box-seam me-1"></i> Katalog
            </button>
            <button class="tab-btn" id="tab-riwayat" onclick="gantiTab('riwayat')">
                <i class="bi bi-clock-history me-1"></i> Riwayat
                @php $pending = $riwayatPermintaan->where('status_request','pending')->count(); @endphp
                @if($pending > 0)
                    <span class="badge bg-warning text-dark ms-1" style="font-size:0.62rem;">{{ $pending }}</span>
                @endif
            </button>
        </div>

        {{-- ======================================== --}}
        {{-- TAB KATALOG --}}
        {{-- ======================================== --}}
        <div id="konten-katalog-wrapper">

            {{-- ID keranjang saat ini (untuk cek JS) --}}
            @php
                $idDiKeranjang = array_keys($keranjang);
            @endphp

            <div class="section-label d-flex justify-content-between">
                <span><i class="bi bi-grid me-1"></i> Daftar Barang ({{ $daftarBarang->count() }} item)</span>
            </div>

            {{-- Pencarian --}}
            <div class="mb-3 position-relative">
                <i class="bi bi-search position-absolute" style="left:1rem;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                <input type="text" id="inputPencarian" class="form-control"
                    placeholder="Cari nama barang..."
                    style="padding-left:2.5rem;border-radius:12px;height:45px;border:1.5px solid #e5e7eb;"
                    oninput="filterKatalog()">
            </div>

            <div id="konten-katalog">
            @forelse($daftarBarang as $barang)
                @php
                    if ($barang->stok_saat_ini == 0) {
                        $stokClass = 'stok-habis'; $stokTeks = 'Habis';
                    } elseif ($barang->stokMenipis()) {
                        $stokClass = 'stok-menipis'; $stokTeks = 'Menipis';
                    } else {
                        $stokClass = 'stok-aman'; $stokTeks = 'Tersedia';
                    }
                    $sudahPending   = $riwayatPermintaan->where('barang_id', $barang->id)->where('status_request','pending')->count() > 0;
                    $sudahDiKeranjang = in_array($barang->id, $idDiKeranjang);
                    $jumlahDiKeranjang = $keranjang[$barang->id]['jumlah'] ?? 0;
                @endphp

                <div class="barang-card katalog-item">
                    <div class="d-flex align-items-center">
                        {{-- Foto / ikon --}}
                        @if($barang->foto_barang)
                            <img src="{{ asset('storage/' . $barang->foto_barang) }}"
                                 alt="{{ $barang->nama_barang }}"
                                 class="icon-barang"
                                 style="object-fit:cover;border:1.5px solid #e5e7eb;background:#fff;">
                        @else
                            <div class="icon-barang"><i class="bi bi-box"></i></div>
                        @endif

                        {{-- Info barang --}}
                        <div class="flex-grow-1">
                            <div class="barang-nama">{{ $barang->nama_barang }}</div>
                            @if($barang->deskripsi)
                                <div class="barang-deskripsi">{{ $barang->deskripsi }}</div>
                            @endif
                            <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
                                <span class="stok-badge {{ $stokClass }}">
                                    {{ $barang->stok_saat_ini }} {{ $barang->satuan }} — {{ $stokTeks }}
                                </span>
                                @if($sudahDiKeranjang)
                                    <span class="stok-badge" style="background:#e8f6ef;color:#059669;">
                                        <i class="bi bi-cart-check me-1"></i>{{ $jumlahDiKeranjang }} di keranjang
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Tombol aksi --}}
                        @if($sudahPending)
                            <span class="status-pill status-pending ms-2">Menunggu</span>
                        @elseif($barang->stok_saat_ini == 0)
                            <span class="status-pill status-ditolak ms-2">Habis</span>
                        @else
                            <button class="btn-keranjang-toggle ms-2 {{ $sudahDiKeranjang ? 'sudah' : '' }}"
                                    id="btn-{{ $barang->id }}"
                                    onclick="toggleForm({{ $barang->id }})">
                                @if($sudahDiKeranjang)
                                    <i class="bi bi-cart-check me-1"></i>+{{ $jumlahDiKeranjang }}
                                @else
                                    <i class="bi bi-cart-plus me-1"></i>Tambah
                                @endif
                            </button>
                        @endif
                    </div>

                    {{-- Form input jumlah (expand) --}}
                    @if(!$sudahPending && $barang->stok_saat_ini > 0)
                    <div class="form-keranjang" id="form-{{ $barang->id }}">
                        <form action="{{ route('barang.keranjang.tambah') }}" method="POST">
                            @csrf
                            <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span style="font-size:0.78rem;font-weight:600;color:#374151;">
                                    Berapa yang dibutuhkan?
                                </span>
                                <span style="font-size:0.72rem;color:#9ca3af;">
                                    Stok tersedia: {{ $barang->stok_saat_ini }} {{ $barang->satuan }}
                                </span>
                            </div>
                            <div class="input-jumlah">
                                <button type="button" class="btn-kurang"
                                        onclick="ubahJumlah({{ $barang->id }}, -1)">−</button>
                                <input type="number" name="jumlah" id="jumlah-{{ $barang->id }}"
                                       class="field-jumlah"
                                       value="{{ $sudahDiKeranjang ? $jumlahDiKeranjang : 1 }}"
                                       min="1" max="{{ $barang->stok_saat_ini }}">
                                <button type="button" class="btn-tambah"
                                        onclick="ubahJumlah({{ $barang->id }}, 1, {{ $barang->stok_saat_ini }})">+</button>
                                <span style="font-size:0.78rem;color:#6b7280;">{{ $barang->satuan }}</span>
                            </div>
                            <button type="submit" class="btn-masuk-keranjang">
                                <i class="bi bi-cart-plus me-1"></i>
                                {{ $sudahDiKeranjang ? 'Perbarui di Keranjang' : 'Masukkan ke Keranjang' }}
                            </button>
                            <button type="button" class="btn-batal" onclick="toggleForm({{ $barang->id }})">
                                Batal
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-5 text-secondary w-100">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2">Belum ada barang terdaftar.</p>
                </div>
            @endforelse

            <div id="pesan-kosong" class="text-center py-5 text-secondary w-100" style="display:none;">
                <i class="bi bi-search fs-1"></i>
                <p class="mt-2">Barang tidak ditemukan.</p>
            </div>
            </div>{{-- /konten-katalog --}}
        </div>{{-- /konten-katalog-wrapper --}}

        {{-- ======================================== --}}
        {{-- TAB RIWAYAT --}}
        {{-- ======================================== --}}
        <div id="konten-riwayat" style="display:none;">
            <div class="section-label">
                <i class="bi bi-clock-history me-1"></i> Riwayat Permintaan (20 terakhir)
            </div>

            @forelse($riwayatPermintaan as $p)
                <div class="riwayat-item">
                    @if($p->barang->foto_barang)
                        <img src="{{ asset('storage/' . $p->barang->foto_barang) }}"
                             alt="{{ $p->barang->nama_barang }}"
                             class="icon-barang"
                             style="object-fit:cover;border:1.5px solid #e5e7eb;background:#fff;">
                    @else
                        <div class="icon-barang" style="background:#f9fafb;color:#6b7280;">
                            <i class="bi bi-box"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:0.88rem;">{{ $p->barang->nama_barang }}</div>
                        <div class="text-secondary" style="font-size:0.75rem;">
                            {{ $p->jumlah }} {{ $p->barang->satuan }}
                            &bull; {{ $p->waktu_request->diffForHumans() }}
                        </div>
                        @if($p->status_request === 'ditolak' && $p->alasan_penolakan)
                            <div class="text-danger mt-1" style="font-size:0.73rem;">
                                <i class="bi bi-x-circle me-1"></i>{{ $p->alasan_penolakan }}
                            </div>
                        @endif
                    </div>
                    <span class="status-pill status-{{ $p->status_request }}">
                        {{ match($p->status_request) {
                            'pending'   => 'Menunggu',
                            'disetujui' => 'Disetujui ✓',
                            'ditolak'   => 'Ditolak ✗',
                            default     => ucfirst($p->status_request),
                        } }}
                    </span>
                </div>
            @empty
                <div class="text-center py-5 text-secondary">
                    <i class="bi bi-clipboard-x fs-1"></i>
                    <p class="mt-2">Belum ada riwayat permintaan.</p>
                </div>
            @endforelse
        </div>

    </div>{{-- /overlap-cards --}}
</div>{{-- /mobile-container --}}

{{-- FAB Keranjang — tampil jika ada item --}}
@if($jumlahKeranjang > 0)
    <a href="{{ route('barang.keranjang') }}" class="fab-keranjang">
        <i class="bi bi-cart3" style="font-size:1.1rem;"></i>
        <span>Keranjang</span>
        <span class="badge-count">{{ $jumlahKeranjang }}</span>
    </a>
@endif

<script>
// ── Tab ─────────────────────────────────────────────────────────
function gantiTab(tab) {
    const isK = tab === 'katalog';
    document.getElementById('konten-katalog-wrapper').style.display = isK ? '' : 'none';
    document.getElementById('konten-riwayat').style.display         = isK ? 'none' : '';
    document.getElementById('tab-katalog').className = 'tab-btn' + (isK ? ' active' : '');
    document.getElementById('tab-riwayat').className = 'tab-btn' + (isK ? '' : ' active');
}

// ── Pencarian ───────────────────────────────────────────────────
function filterKatalog() {
    const q = document.getElementById('inputPencarian').value.toLowerCase();
    let vis = 0;
    document.querySelectorAll('.katalog-item').forEach(el => {
        const nama = el.querySelector('.barang-nama').textContent.toLowerCase();
        const show = nama.includes(q);
        el.style.display = show ? '' : 'none';
        if (show) vis++;
    });
    document.getElementById('pesan-kosong').style.display = (vis === 0 && q) ? '' : 'none';
}

// ── Toggle form input jumlah ─────────────────────────────────────
function toggleForm(id) {
    const el = document.getElementById('form-' + id);
    if (!el) return;
    // Tutup semua form lain
    document.querySelectorAll('.form-keranjang.tampil').forEach(f => {
        if (f.id !== 'form-' + id) f.classList.remove('tampil');
    });
    el.classList.toggle('tampil');
}

// ── Tombol +/− jumlah ───────────────────────────────────────────
function ubahJumlah(id, delta, maks) {
    const input = document.getElementById('jumlah-' + id);
    if (!input) return;
    let val = parseInt(input.value) + delta;
    val = Math.max(1, val);
    if (maks !== undefined) val = Math.min(maks, val);
    input.value = val;
}

// ── Toast notif keranjang ────────────────────────────────────────
function tampilkanToast(pesan) {
    const el   = document.getElementById('toastKeranjang');
    const teks = document.getElementById('toastTeks');
    teks.textContent = pesan;
    el.classList.add('tampil');
    setTimeout(() => el.classList.remove('tampil'), 2800);
}

// Jika ada flash sukses_keranjang, tampilkan toast
document.addEventListener('DOMContentLoaded', () => {
    const flash = document.getElementById('dataFlashKeranjang');
    if (flash) tampilkanToast(flash.dataset.pesan);
});
</script>
@endsection
