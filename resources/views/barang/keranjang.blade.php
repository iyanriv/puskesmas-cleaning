@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%; margin: 0 auto;
        background-color: #f4f7f6; min-height: 100vh;
        padding-bottom: 100px;
    }

    /* ── Header ── */
    .header-section {
        background: linear-gradient(135deg, #12a65a 0%, #0d8a4a 50%, #0a7040 100%);
        border-radius: 0 0 30px 30px;
        padding: 1.25rem 1.5rem 3.5rem 1.5rem;
        color: white;
    }

    /* ── Area konten overlap ── */
    .overlap-cards {
        margin-top: -2.5rem; padding: 0 1.2rem;
        position: relative; z-index: 10;
    }
    @media (min-width: 992px) { .overlap-cards { padding: 0 3rem; } }

    /* ── Kartu item keranjang ── */
    .item-card {
        background: white; border-radius: 18px;
        padding: 1rem 1.1rem; margin-bottom: 0.75rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }

    .item-nama { font-weight: 700; font-size: 0.92rem; color: #1f2937; }
    .item-satuan { font-size: 0.75rem; color: #9ca3af; margin-top: 1px; }

    /* ── Ikon barang ── */
    .icon-barang {
        width: 48px; height: 48px; border-radius: 12px;
        background: #e8f6ef; color: #12a65a;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; flex-shrink: 0;
    }

    /* ── Kontrol jumlah ── */
    .kontrol-jumlah {
        display: flex; align-items: center; gap: 0.4rem;
    }
    .btn-k, .btn-t {
        width: 32px; height: 32px; border-radius: 8px;
        border: 1.5px solid #e5e7eb; background: #f9fafb;
        font-size: 1rem; font-weight: 700; color: #374151;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all 0.15s; flex-shrink: 0;
        padding: 0;
    }
    .btn-k:hover, .btn-t:hover { border-color: #12a65a; background: #e8f6ef; color: #12a65a; }
    .field-jumlah {
        width: 46px; text-align: center;
        border: 1.5px solid #e5e7eb; border-radius: 8px;
        padding: 4px 2px; font-size: 0.88rem; font-weight: 700;
        color: #1f2937;
    }
    .field-jumlah:focus { outline: none; border-color: #12a65a; }

    /* ── Tombol hapus ── */
    .btn-hapus {
        background: none; border: none; cursor: pointer;
        color: #e5e7eb; padding: 4px; border-radius: 8px;
        transition: all 0.15s; line-height: 1;
    }
    .btn-hapus:hover { color: #ef4444; background: #fee2e2; }

    /* ── Tombol simpan perubahan jumlah ── */
    .btn-simpan-jumlah {
        display: none; /* muncul saat jumlah berubah */
        background: #12a65a; color: white;
        border: none; border-radius: 8px;
        padding: 4px 12px; font-size: 0.75rem;
        font-weight: 700; cursor: pointer;
        margin-left: 0.35rem; white-space: nowrap;
    }

    /* ── Kartu ringkasan pesanan ── */
    .ringkasan-card {
        background: white; border-radius: 20px;
        padding: 1.25rem; box-shadow: 0 4px 16px rgba(0,0,0,0.06);
        margin-bottom: 1rem;
    }
    .ringkasan-baris {
        display: flex; justify-content: space-between;
        align-items: center; padding: 0.45rem 0;
        font-size: 0.85rem; color: #374151;
    }
    .ringkasan-baris + .ringkasan-baris { border-top: 1px solid #f3f4f6; }
    .ringkasan-baris .nama { font-weight: 600; }
    .ringkasan-baris .jumlah-val {
        font-weight: 700; color: #12a65a;
        background: #e8f6ef; padding: 2px 10px;
        border-radius: 20px; font-size: 0.78rem;
    }

    /* ── Tombol kirim ── */
    .btn-kirim {
        width: 100%; background: #12a65a; color: white;
        border: none; border-radius: 16px;
        padding: 1rem; font-size: 0.95rem; font-weight: 800;
        box-shadow: 0 6px 20px rgba(18,166,90,0.35);
        cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    }
    .btn-kirim:hover  { background: #0d8a4a; transform: translateY(-1px); }
    .btn-kirim:active { transform: scale(0.98); }
    .btn-kirim:disabled {
        background: #9ca3af; box-shadow: none;
        transform: none; cursor: not-allowed;
    }

    /* ── Tombol kembali ke katalog ── */
    .btn-lanjut-belanja {
        width: 100%; background: white; color: #12a65a;
        border: 1.5px solid #12a65a; border-radius: 16px;
        padding: 0.8rem; font-size: 0.88rem; font-weight: 700;
        cursor: pointer; transition: all 0.2s; text-decoration: none;
        display: flex; align-items: center; justify-content: center; gap: 0.4rem;
        margin-bottom: 0.75rem;
    }
    .btn-lanjut-belanja:hover { background: #e8f6ef; color: #12a65a; }

    /* ── State kosong ── */
    .empty-state {
        background: white; border-radius: 20px;
        padding: 3.5rem 1.5rem; text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.04);
    }

    /* ── Info stok berubah ── */
    .stok-warning {
        font-size: 0.72rem; color: #d97706;
        background: #fef3c7; padding: 2px 8px;
        border-radius: 6px; display: inline-block; margin-top: 2px;
    }

    /* ── Sticky footer kirim di mobile ── */
    .sticky-footer {
        position: fixed; bottom: 0; left: 0; right: 0;
        background: white; padding: 0.85rem 1.2rem;
        border-top: 1px solid #f0f0f0;
        box-shadow: 0 -4px 16px rgba(0,0,0,0.06);
        z-index: 1000;
    }
    @media (min-width: 992px) {
        .sticky-footer { display: none !important; }
        .btn-kirim-desktop { display: block !important; }
    }
    .btn-kirim-desktop { display: none; }

    @media (min-width: 992px) {
        .grid-keranjang {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 1.5rem;
            align-items: start;
        }
    }
</style>

<div class="mobile-container">

    {{-- ── Header ── --}}
    <div class="header-section">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('barang.katalog') }}" class="text-white" style="font-size:1.2rem;line-height:1;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0 text-white">Keranjang Saya</h5>
                <p class="mb-0 text-white-50" style="font-size:0.8rem;">
                    Periksa sebelum mengirim permintaan
                </p>
            </div>
        </div>
    </div>

    <div class="overlap-cards">

        {{-- Flash --}}
        @if(session('sukses'))
            <div class="alert alert-success rounded-3 py-2 px-3 mb-3 alert-dismissible fade show border-0 shadow-sm"
                 style="font-size:0.84rem;" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('sukses') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('gagal'))
            <div class="alert alert-danger rounded-3 py-2 px-3 mb-3 alert-dismissible fade show border-0 shadow-sm"
                 style="font-size:0.84rem;" role="alert">
                <i class="bi bi-exclamation-circle-fill me-1"></i> {{ session('gagal') }}
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(empty($keranjang))
        {{-- ── STATE KOSONG ── --}}
        <div class="empty-state">
            <div style="font-size:3.5rem;color:#d1d5db;margin-bottom:0.75rem;">
                <i class="bi bi-cart-x"></i>
            </div>
            <h6 class="fw-bold text-dark mb-1">Keranjang Masih Kosong</h6>
            <p class="text-muted mb-4" style="font-size:0.85rem;">
                Belum ada barang yang dipilih. Kembali ke katalog untuk memilih barang.
            </p>
            <a href="{{ route('barang.katalog') }}"
               class="btn btn-success rounded-pill px-4 py-2 fw-bold"
               style="font-size:0.85rem;">
                <i class="bi bi-arrow-left me-2"></i>Lihat Katalog Barang
            </a>
        </div>

        @else
        {{-- ── ISI KERANJANG ── --}}
        <div class="grid-keranjang">

            {{-- Kolom kiri: daftar item --}}
            <div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div style="font-size:0.78rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;">
                        <i class="bi bi-cart3 me-1"></i>{{ count($keranjang) }} Jenis Barang
                    </div>
                    {{-- Hapus semua --}}
                    <form action="{{ route('barang.keranjang.hapus-semua') }}" method="POST"
                          onsubmit="return confirm('Kosongkan seluruh keranjang?')">
                        @csrf
                        <button type="submit"
                                class="btn btn-link text-danger p-0 fw-semibold"
                                style="font-size:0.78rem;text-decoration:none;">
                            <i class="bi bi-trash me-1"></i>Kosongkan
                        </button>
                    </form>
                </div>

                @foreach($keranjang as $id => $item)
                @php
                    $stokOk    = $item['stok_tersedia'] > 0;
                    $stokCukup = $item['stok_tersedia'] >= $item['jumlah'];
                    $kurangDari = !$stokCukup && $stokOk; // stok ada tapi kurang dari jumlah diminta
                @endphp
                <div class="item-card" id="item-card-{{ $id }}">
                    <div class="d-flex align-items-start gap-3">

                        {{-- Foto / ikon --}}
                        @if(!empty($item['foto']))
                            <img src="{{ asset('storage/' . $item['foto']) }}"
                                 alt="{{ $item['nama_barang'] }}"
                                 class="icon-barang"
                                 style="object-fit:cover;border:1.5px solid #e5e7eb;background:#fff;">
                        @else
                            <div class="icon-barang"><i class="bi bi-box"></i></div>
                        @endif

                        {{-- Info & kontrol --}}
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1 me-2">
                                    <div class="item-nama text-truncate">{{ $item['nama_barang'] }}</div>
                                    <div class="item-satuan">
                                        Stok tersedia: {{ $item['stok_tersedia'] }} {{ $item['satuan'] }}
                                    </div>
                                    @if($kurangDari)
                                        <span class="stok-warning">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            Stok hanya {{ $item['stok_tersedia'] }}, jumlah disesuaikan
                                        </span>
                                    @elseif(!$stokOk)
                                        <span class="stok-warning" style="background:#fee2e2;color:#dc2626;">
                                            <i class="bi bi-x-circle me-1"></i>Stok habis
                                        </span>
                                    @endif
                                </div>
                                {{-- Tombol hapus --}}
                                <form action="{{ route('barang.keranjang.hapus') }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    <input type="hidden" name="barang_id" value="{{ $id }}">
                                    <button type="submit" class="btn-hapus" title="Hapus dari keranjang">
                                        <i class="bi bi-trash" style="font-size:1rem;"></i>
                                    </button>
                                </form>
                            </div>

                            {{-- Baris bawah: kontrol jumlah + simpan --}}
                            <div class="d-flex align-items-center justify-content-between mt-2">
                                <form action="{{ route('barang.keranjang.update') }}" method="POST"
                                      class="d-flex align-items-center"
                                      id="form-update-{{ $id }}">
                                    @csrf
                                    <input type="hidden" name="barang_id" value="{{ $id }}">
                                    <div class="kontrol-jumlah">
                                        <button type="button" class="btn-k"
                                                onclick="ubahJumlah({{ $id }}, -1, {{ $item['stok_tersedia'] }})">−</button>
                                        <input type="number"
                                               name="jumlah"
                                               id="jumlah-{{ $id }}"
                                               class="field-jumlah"
                                               value="{{ $item['jumlah'] }}"
                                               min="1"
                                               max="{{ $item['stok_tersedia'] }}"
                                               oninput="tandaiPerubahan({{ $id }})">
                                        <button type="button" class="btn-t"
                                                onclick="ubahJumlah({{ $id }}, 1, {{ $item['stok_tersedia'] }})">+</button>
                                        <span style="font-size:0.75rem;color:#9ca3af;margin-left:2px;">
                                            {{ $item['satuan'] }}
                                        </span>
                                    </div>
                                    <button type="submit"
                                            class="btn-simpan-jumlah"
                                            id="btn-simpan-{{ $id }}">
                                        Simpan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Kolom kanan: ringkasan pesanan --}}
            <div>
                <div class="ringkasan-card">
                    <div class="fw-bold text-dark mb-3" style="font-size:0.9rem;">
                        <i class="bi bi-receipt me-2 text-success"></i>Ringkasan Permintaan
                    </div>

                    @foreach($keranjang as $id => $item)
                        <div class="ringkasan-baris">
                            <span class="nama text-truncate me-2"
                                  style="max-width:170px;" title="{{ $item['nama_barang'] }}">
                                {{ $item['nama_barang'] }}
                            </span>
                            <span class="jumlah-val" id="ringkasan-{{ $id }}">
                                {{ $item['jumlah'] }} {{ $item['satuan'] }}
                            </span>
                        </div>
                    @endforeach

                    {{-- Total jenis --}}
                    <div class="d-flex justify-content-between align-items-center mt-3 pt-3"
                         style="border-top:2px solid #f0f0f0;">
                        <span class="fw-bold text-dark" style="font-size:0.88rem;">Total Jenis Barang</span>
                        <span class="fw-black text-success" style="font-size:1.1rem;">
                            {{ count($keranjang) }}
                        </span>
                    </div>

                    <div class="text-muted mt-2" style="font-size:0.73rem; line-height:1.4;">
                        <i class="bi bi-info-circle me-1"></i>
                        Permintaan akan dikirim ke gudang untuk disetujui. Stok dikurangi saat disetujui.
                    </div>
                </div>

                {{-- Tombol aksi (desktop) --}}
                <a href="{{ route('barang.katalog') }}" class="btn-lanjut-belanja">
                    <i class="bi bi-plus-circle"></i>Tambah Barang Lagi
                </a>

                <form action="{{ route('barang.keranjang.kirim') }}" method="POST"
                      class="btn-kirim-desktop" id="formKirimDesktop"
                      onsubmit="return konfirmasiKirim(this)">
                    @csrf
                    <button type="submit" class="btn-kirim">
                        <i class="bi bi-send-fill"></i>
                        Kirim Permintaan ke Gudang
                    </button>
                </form>
            </div>

        </div>{{-- /grid-keranjang --}}
        @endif

    </div>{{-- /overlap-cards --}}
</div>{{-- /mobile-container --}}

{{-- ── Sticky footer (mobile only) ── --}}
@if(!empty($keranjang))
<div class="sticky-footer">
    <form action="{{ route('barang.keranjang.kirim') }}" method="POST"
          id="formKirimMobile"
          onsubmit="return konfirmasiKirim(this)">
        @csrf
        <button type="submit" class="btn-kirim">
            <i class="bi bi-send-fill"></i>
            Kirim Permintaan ke Gudang
            <span style="background:rgba(255,255,255,0.25);border-radius:20px;padding:1px 10px;font-size:0.8rem;">
                {{ count($keranjang) }} item
            </span>
        </button>
    </form>
</div>
@endif

{{-- ── Modal konfirmasi kirim ── --}}
<div class="modal fade" id="modalKonfirmasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-send text-success me-2"></i>Kirim Permintaan?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="text-muted mb-0" style="font-size:0.88rem;">
                    <strong>{{ count($keranjang ?? []) }} jenis barang</strong> akan dikirim
                    ke gudang untuk mendapat persetujuan.
                    <br><br>
                    Pastikan jumlah sudah sesuai sebelum mengirim.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0 gap-2">
                <button type="button" class="btn btn-light rounded-3 flex-fill"
                        data-bs-dismiss="modal">Periksa Lagi</button>
                <button type="button" class="btn btn-success rounded-3 flex-fill fw-bold"
                        id="btnKonfirmasiYa">
                    <i class="bi bi-send-fill me-1"></i>Ya, Kirim!
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let formKirimTarget = null;

// ── Konfirmasi sebelum kirim ─────────────────────────────────────
function konfirmasiKirim(form) {
    formKirimTarget = form;
    const modal = new bootstrap.Modal(document.getElementById('modalKonfirmasi'));
    modal.show();
    return false; // cegah submit langsung
}

document.getElementById('btnKonfirmasiYa')?.addEventListener('click', () => {
    if (formKirimTarget) {
        // Disable tombol agar tidak double submit
        document.getElementById('btnKonfirmasiYa').disabled = true;
        document.getElementById('btnKonfirmasiYa').innerHTML =
            '<span class="spinner-border spinner-border-sm me-1"></span>Mengirim...';
        formKirimTarget.submit();
    }
});

// ── +/- jumlah ──────────────────────────────────────────────────
function ubahJumlah(id, delta, maks) {
    const input = document.getElementById('jumlah-' + id);
    if (!input) return;
    let val = parseInt(input.value) + delta;
    val = Math.max(1, val);
    if (maks) val = Math.min(maks, val);
    input.value = val;
    tandaiPerubahan(id);
}

// ── Tampilkan tombol "Simpan" saat jumlah berubah ────────────────
function tandaiPerubahan(id) {
    const btn = document.getElementById('btn-simpan-' + id);
    if (btn) btn.style.display = 'inline-block';
}

// ── Auto-submit form update saat tekan Enter di input jumlah ─────
document.querySelectorAll('.field-jumlah').forEach(input => {
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            const idVal = input.id.replace('jumlah-', '');
            document.getElementById('form-update-' + idVal)?.submit();
        }
    });
});
</script>
@endsection
