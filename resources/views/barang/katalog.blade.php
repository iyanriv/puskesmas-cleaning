@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f7f9fa; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%; margin: 0 auto;
        background-color: #f4f7f6; min-height: 100vh;
        padding-bottom: 40px;
    }
    .header-section {  
        background: linear-gradient(135deg, #12a65a 0%, #0a7040 100%);
        border-radius: 0 0 30px 30px;
        padding: 1.5rem 1.5rem 3rem 1.5rem; color: white;
    }
    .content-area { padding: 1.25rem; margin-top: -1.8rem; }

    /* Tab navigasi */
    .tab-nav {
        background: white; border-radius: 16px;
        padding: 6px; display: flex; gap: 4px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        margin-bottom: 1.25rem;
    }
    .tab-btn {
        flex: 1; padding: 0.6rem;
        border: none; border-radius: 12px;
        background: transparent; font-size: 0.82rem;
        font-weight: 600; color: #6b7280; cursor: pointer;
        transition: all 0.2s;
    }
    .tab-btn.active { background: #12a65a; color: white; }

    /* Katalog barang */
    .barang-card {
        background: white; border-radius: 18px;
        padding: 1rem 1.1rem; margin-bottom: 0.75rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        transition: all 0.2s;
    }
    .barang-card:active { transform: scale(0.99); }
    .barang-nama { font-weight: 700; font-size: 0.9rem; color: #1f2937; }
    .barang-deskripsi { font-size: 0.75rem; color: #9ca3af; margin-top: 2px; }
    .stok-badge {
        padding: 4px 12px; border-radius: 20px;
        font-size: 0.75rem; font-weight: 700;
    }
    .stok-aman   { background: #d1fae5; color: #059669; }
    .stok-menipis { background: #fef3c7; color: #d97706; }
    .stok-habis  { background: #fee2e2; color: #dc2626; }

    /* Form minta barang (inline di bawah kartu) */
    .form-minta {
        display: none; /* toggle via JS */
        margin-top: 0.75rem; padding-top: 0.75rem;
        border-top: 1px dashed #e5e7eb;
    }
    .form-minta.tampil { display: block; }
    .input-jumlah {
        display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.6rem;
    }
    .btn-kurang, .btn-tambah {
        width: 36px; height: 36px; border-radius: 10px;
        border: 1.5px solid #e5e7eb; background: #f9fafb;
        font-size: 1.1rem; font-weight: 700; color: #374151;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s;
    }
    .btn-kurang:active, .btn-tambah:active { background: #eff6ff; border-color: #12a65a; }
    .field-jumlah {
        flex: 1; text-align: center; border: 1.5px solid #e5e7eb;
        border-radius: 10px; padding: 0.4rem; font-size: 0.9rem; font-weight: 700;
    }
    .field-jumlah:focus { outline: none; border-color: #12a65a; }
    .btn-ajukan {
        width: 100%; background: #12a65a; color: white; border: none;
        border-radius: 12px; padding: 0.65rem;
        font-size: 0.85rem; font-weight: 700;
        transition: all 0.2s;
    }
    .btn-ajukan:active { transform: scale(0.98); }
    .btn-batal-minta {
        width: 100%; background: #f3f4f6; color: #6b7280; border: none;
        border-radius: 12px; padding: 0.55rem; font-size: 0.8rem;
        margin-top: 0.4rem;
    }
    .icon-barang {
        width: 44px; height: 44px; border-radius: 12px;
        background: #eff6ff; color: #12a65a;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0; margin-right: 0.85rem;
    }
    .btn-minta-toggle {
        background: none; border: 1.5px solid #12a65a;
        color: #12a65a; border-radius: 10px; padding: 4px 12px;
        font-size: 0.78rem; font-weight: 700; cursor: pointer;
        transition: all 0.2s; flex-shrink: 0;
    }
    .btn-minta-toggle:hover { background: #eff6ff; }

    /* Riwayat */
    .riwayat-item {
        background: white; border-radius: 14px;
        padding: 0.85rem 1rem; margin-bottom: 0.6rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        display: flex; align-items: center; gap: 0.75rem;
    }
    .status-pill {
        padding: 3px 10px; border-radius: 20px;
        font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
    }
    .status-pending   { background: #fef3c7; color: #d97706; }
    .status-disetujui { background: #d1fae5; color: #059669; }
    .status-ditolak   { background: #fee2e2; color: #dc2626; }

    .section-label {
        font-size: 0.78rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.05em;
        margin-bottom: 0.6rem;
    }
    @media (min-width: 992px) {
        .content-area { max-width: 900px; margin-left: auto; margin-right: auto; }
        #konten-katalog { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; align-items: start; }
        .section-label { grid-column: 1 / -1; }
    }
</style>

<div class="mobile-container">
    {{-- Header --}}
    <div class="header-section">
        <div class="d-flex align-items-center">
            <a href="{{ route('dasbor.cs') }}" class="text-white me-3" style="font-size:1.2rem;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0">Permintaan Barang</h5>
                <p class="mb-0 text-white-50" style="font-size:0.82rem;">Pilih barang yang dibutuhkan</p>
            </div>
        </div>
    </div>

    <div class="content-area">

        {{-- Alert --}}
        @if(session('sukses'))
            <div class="alert alert-success rounded-3 py-2 px-3 mb-3 alert-dismissible fade show" style="font-size:0.84rem;">
                <i class="bi bi-check-circle me-1"></i> {{ session('sukses') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('gagal'))
            <div class="alert alert-danger rounded-3 py-2 px-3 mb-3 alert-dismissible fade show" style="font-size:0.84rem;">
                <i class="bi bi-exclamation-circle me-1"></i> {{ session('gagal') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
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
                    <span class="badge bg-warning text-dark ms-1" style="font-size:0.65rem;">{{ $pending }}</span>
                @endif
            </button>
        </div>

        {{-- ======================================== --}}
        {{-- TAB KATALOG --}}
        {{-- ======================================== --}}
        <div id="konten-katalog-wrapper">
            <div class="section-label d-flex justify-content-between align-items-center">
                <span><i class="bi bi-grid me-1"></i> Daftar Barang ({{ $daftarBarang->count() }} item)</span>
            </div>

            {{-- Kolom Pencarian --}}
            <div class="mb-3 position-relative">
                <i class="bi bi-search position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                <input type="text" id="inputPencarian" class="form-control" 
                    placeholder="Cari nama barang (mis. sapu, sabun, plastik)..." 
                    style="padding-left: 2.5rem; border-radius: 12px; height: 45px; border: 1.5px solid #e5e7eb;"
                    onkeyup="filterKatalog()">
            </div>

            <div id="konten-katalog">

            @forelse($daftarBarang as $barang)
                @php
                    if ($barang->stok_saat_ini == 0) {
                        $stokClass = 'stok-habis';
                        $stokTeks  = 'Habis';
                    } elseif ($barang->stokMenipis()) {
                        $stokClass = 'stok-menipis';
                        $stokTeks  = 'Menipis';
                    } else {
                        $stokClass = 'stok-aman';
                        $stokTeks  = 'Tersedia';
                    }
                    // Cek apakah sudah ada permintaan pending untuk barang ini
                    $sudahPending = $riwayatPermintaan->where('barang_id', $barang->id)->where('status_request','pending')->count() > 0;
                @endphp

                <div class="barang-card katalog-item">
                    <div class="d-flex align-items-center">
                        @if($barang->foto_barang)
                            <img src="{{ asset('storage/' . $barang->foto_barang) }}" alt="{{ $barang->nama_barang }}" class="icon-barang" style="object-fit: cover; border: 1.5px solid #e5e7eb; background: #fff;">
                        @else
                            <div class="icon-barang">
                                <i class="bi bi-box"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <div class="barang-nama">{{ $barang->nama_barang }}</div>
                            <div class="barang-deskripsi">{{ $barang->deskripsi }}</div>
                            <div class="mt-1">
                                <span class="stok-badge {{ $stokClass }}">
                                    {{ $barang->stok_saat_ini }} {{ $barang->satuan }} — {{ $stokTeks }}
                                </span>
                            </div>
                        </div>
                        @if($barang->stok_saat_ini > 0 && !$sudahPending)
                            <button class="btn-minta-toggle ms-2" onclick="toggleForm({{ $barang->id }})">
                                + Minta
                            </button>
                        @elseif($sudahPending)
                            <span class="status-pill status-pending ms-2">Menunggu</span>
                        @else
                            <span class="status-pill status-ditolak ms-2">Habis</span>
                        @endif
                    </div>

                    {{-- Form minta barang (toggle) --}}
                    @if($barang->stok_saat_ini > 0 && !$sudahPending)
                    <div class="form-minta" id="form-{{ $barang->id }}">
                        <form action="{{ route('barang.ajukan') }}" method="POST">
                            @csrf
                            <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                            <label class="form-label mb-1" style="font-size:0.78rem; font-weight:600; color:#374151;">
                                Jumlah yang diminta (maks. stok: {{ $barang->stok_saat_ini }})
                            </label>
                            <div class="input-jumlah">
                                <button type="button" class="btn-kurang" onclick="ubahJumlah({{ $barang->id }}, -1)">−</button>
                                <input type="number" name="jumlah" id="jumlah-{{ $barang->id }}"
                                    class="field-jumlah" value="1" min="1" max="{{ $barang->stok_saat_ini }}">
                                <button type="button" class="btn-tambah" onclick="ubahJumlah({{ $barang->id }}, 1, {{ $barang->stok_saat_ini }})">+</button>
                                <span style="font-size:0.78rem; color:#6b7280;">{{ $barang->satuan }}</span>
                            </div>
                            <button type="submit" class="btn-ajukan">
                                <i class="bi bi-send me-1"></i> Kirim Permintaan
                            </button>
                            <button type="button" class="btn-batal-minta" onclick="toggleForm({{ $barang->id }})">
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
            
            {{-- Pesan jika pencarian tidak ditemukan --}}
            <div id="pesan-kosong" class="text-center py-5 text-secondary w-100" style="display: none;">
                <i class="bi bi-search fs-1"></i>
                <p class="mt-2">Barang yang dicari tidak ditemukan.</p>
            </div>
        </div>
        </div>

        {{-- ======================================== --}}
        {{-- TAB RIWAYAT --}}
        {{-- ======================================== --}}
        <div id="konten-riwayat" style="display:none;">
            <div class="section-label"><i class="bi bi-clock-history me-1"></i> Riwayat Permintaan (10 terakhir)</div>

            @forelse($riwayatPermintaan as $permintaan)
                <div class="riwayat-item">
                    @if($permintaan->barang->foto_barang)
                        <img src="{{ asset('storage/' . $permintaan->barang->foto_barang) }}" alt="{{ $permintaan->barang->nama_barang }}" class="icon-barang" style="object-fit: cover; border: 1.5px solid #e5e7eb; background: #fff;">
                    @else
                        <div class="icon-barang" style="background:#f9fafb; color:#6b7280;">
                            <i class="bi bi-box"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:0.88rem;">{{ $permintaan->barang->nama_barang }}</div>
                        <div class="text-secondary" style="font-size:0.75rem;">
                            {{ $permintaan->jumlah }} {{ $permintaan->barang->satuan }}
                            · {{ $permintaan->waktu_request->diffForHumans() }}
                        </div>
                        @if($permintaan->status_request === 'ditolak' && $permintaan->alasan_penolakan)
                            <div class="text-danger mt-1" style="font-size:0.73rem;">
                                <i class="bi bi-x-circle me-1"></i>{{ $permintaan->alasan_penolakan }}
                            </div>
                        @endif
                    </div>
                    <span class="status-pill status-{{ $permintaan->status_request }}">
                        {{ match($permintaan->status_request) {
                            'pending'   => 'Menunggu',
                            'disetujui' => 'Disetujui ✓',
                            'ditolak'   => 'Ditolak ✗',
                            default     => ucfirst($permintaan->status_request)
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

    </div>
</div>

<script>
    // Ganti tab
    function gantiTab(tab) {
        const isKatalog = tab === 'katalog';
        document.getElementById('konten-katalog-wrapper').style.display = isKatalog ? 'block' : 'none';
        document.getElementById('konten-riwayat').style.display = isKatalog ? 'none'  : 'block';
        document.getElementById('tab-katalog').className = 'tab-btn' + (isKatalog ? ' active' : '');
        document.getElementById('tab-riwayat').className = 'tab-btn' + (!isKatalog ? ' active' : '');
    }

    // Filter Pencarian
    function filterKatalog() {
        let input = document.getElementById('inputPencarian').value.toLowerCase();
        let items = document.querySelectorAll('.katalog-item');
        let visibleCount = 0;

        items.forEach(function(item) {
            let namaBarang = item.querySelector('.barang-nama').textContent.toLowerCase();
            if (namaBarang.includes(input)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Tampilkan pesan jika kosong
        let pesanKosong = document.getElementById('pesan-kosong');
        if(visibleCount === 0 && input !== '') {
            pesanKosong.style.display = 'block';
        } else {
            pesanKosong.style.display = 'none';
        }
    }

    // Toggle form minta barang
    function toggleForm(barangId) {
        const el = document.getElementById('form-' + barangId);
        if (!el) return;
        el.classList.toggle('tampil');
        // Tutup form lain yang terbuka
        document.querySelectorAll('.form-minta.tampil').forEach(f => {
            if (f.id !== 'form-' + barangId) f.classList.remove('tampil');
        });
    }

    // Tombol +/-
    function ubahJumlah(barangId, delta, maks) {
        const input = document.getElementById('jumlah-' + barangId);
        if (!input) return;
        let val = parseInt(input.value) + delta;
        val = Math.max(1, val);
        if (maks) val = Math.min(maks, val);
        input.value = val;
    }
</script>
@endsection
