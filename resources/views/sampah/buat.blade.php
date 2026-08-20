@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f7f9fa; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%; margin: 0 auto;
        background-color: #ffffff; min-height: 100vh;
        padding-bottom: 90px;
    }

    /* Header (Updated to Green) */
    .header-section {  
        background: linear-gradient(135deg, #12a65a 0%, #0a7040 100%);
        border-radius: 0 0 30px 30px;
        padding: 1.5rem 1.5rem 3rem 1.5rem; color: white;
    }
    .content-area { padding: 1.25rem; margin-top: -1.8rem; background: white; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }

    /* Form body */
    .form-body { padding: 1.1rem 1.25rem; }

    /* Section label */
    .section-label {
        font-size: 0.82rem; font-weight: 700; color: #1f2937;
        margin-bottom: 0.65rem; display: block;
    }
    .section-sub {
        font-size: 0.72rem; color: #6b7280; margin-top: -0.5rem; margin-bottom: 0.65rem;
    }

    /* Grid lokasi */
    .lokasi-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.55rem; margin-bottom: 1.25rem; }
    .lokasi-item {
        border: 1.5px solid #e5e7eb; border-radius: 14px;
        padding: 0.65rem 0.75rem;
        display: flex; align-items: center; gap: 0.5rem;
        cursor: pointer; transition: all 0.15s;
        background: white; font-size: 0.84rem; color: #374151;
    }
    .lokasi-item i { color: #9ca3af; font-size: 1rem; }
    .lokasi-item.terpilih {
        border-color: #12a65a; background: #f0fdf4; color: #059669; font-weight: 600;
    }
    .lokasi-item.terpilih i { color: #12a65a; }
    .lokasi-item input[type="radio"] { display: none; }

    /* Grid jenis sampah */
    .jenis-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.55rem; margin-bottom: 1.25rem; }
    .jenis-item {
        border: 1.5px solid #e5e7eb; border-radius: 14px;
        padding: 0.7rem 0.75rem;
        display: flex; align-items: center; gap: 0.6rem;
        cursor: pointer; transition: all 0.15s;
        background: white; font-size: 0.84rem; color: #374151;
        grid-column: span 1;
    }
    .jenis-item.lainnya { grid-column: span 2; }
    .jenis-item .emoji { font-size: 1.3rem; line-height: 1; }
    .jenis-item.terpilih {
        border-color: #12a65a; background: #f0fdf4; color: #059669; font-weight: 600;
    }
    .jenis-item input[type="checkbox"] { display: none; }

    /* Berat */
    .berat-wrapper {
        display: flex; align-items: center;
        border: 1.5px solid #e5e7eb; border-radius: 14px;
        overflow: hidden; margin-bottom: 1.25rem;
    }
    .berat-wrapper:focus-within { border-color: #12a65a; }
    .berat-input {
        flex: 1; border: none; outline: none;
        padding: 0.75rem 1rem; font-size: 1.1rem; font-weight: 700;
        color: #111827; text-align: center; background: transparent;
    }
    .berat-unit {
        padding: 0.75rem 1rem; background: #f9fafb;
        font-size: 0.85rem; font-weight: 700; color: #6b7280;
        border-left: 1.5px solid #e5e7eb;
    }

    /* Area foto */
    .foto-box {
        border: 2px dashed #d1d5db; border-radius: 16px;
        background: #f9fafb; cursor: pointer;
        transition: all 0.2s; margin-bottom: 1.25rem;
        position: relative; overflow: hidden;
    }
    .foto-box:hover { border-color: #12a65a; background: #f0fdf4; }
    .foto-box.ada-foto { border-style: solid; border-color: #12a65a; }
    .foto-placeholder {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 2rem; text-align: center;
    }
    .foto-placeholder i { font-size: 2rem; color: #9ca3af; margin-bottom: 0.5rem; }
    .foto-placeholder span { font-size: 0.82rem; color: #9ca3af; }
    .foto-preview-img {
        width: 100%; max-height: 200px; object-fit: cover;
        border-radius: 14px; display: none;
    }
    .foto-hapus {
        position: absolute; top: 8px; right: 8px;
        background: #ef4444; color: white; border: none;
        width: 28px; height: 28px; border-radius: 50%;
        font-size: 0.75rem; display: none;
        align-items: center; justify-content: center; cursor: pointer;
        z-index: 5;
    }

    /* Tombol kirim */
    .btn-kirim {
        width: 100%; background: #12a65a; color: white; border: none;
        border-radius: 16px; padding: 1rem;
        font-size: 0.95rem; font-weight: 700; letter-spacing: 0.03em;
        box-shadow: 0 4px 16px rgba(18,166,90,0.35);
        transition: all 0.2s; display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    }
    .btn-kirim:active { transform: scale(0.98); }
    .btn-kirim:disabled { background: #9ca3af; box-shadow: none; }

    /* Alert */
    .alert-sukses {
        background: #d1fae5; color: #065f46; border: none;
        border-radius: 14px; padding: 0.85rem 1rem;
        font-size: 0.84rem; margin-bottom: 1rem;
        display: flex; align-items: center; gap: 0.5rem;
    }

    /* Riwayat */
    .riwayat-item {
        background: #f9fafb; border-radius: 14px;
        padding: 0.75rem 1rem; margin-bottom: 0.5rem;
        display: flex; align-items: center; gap: 0.75rem;
    }
    .riwayat-icon {
        width: 38px; height: 38px; border-radius: 10px;
        background: #d1fae5; color: #059669;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; flex-shrink: 0;
    }

    /* Bottom nav */
    .bottom-nav {
        position: fixed; bottom: 0; left: 50%; transform: translateX(-50%);
        width: 100%; max-width: 414px; background: white;
        display: flex; justify-content: space-between;
        padding: 0.8rem 1.5rem;
        border-top: 1px solid #f0f0f0; z-index: 50;
    }
    .nav-item {
        display: flex; flex-direction: column; align-items: center;
        text-decoration: none; color: #888;
        font-size: 0.72rem; font-weight: 500;
        padding: 0.4rem 0.75rem; border-radius: 14px;
    }
    .nav-item.active { color: #12a65a; background-color: #e8f6ef; }
    .nav-item i { font-size: 1.25rem; margin-bottom: 0.2rem; }
    @media (min-width: 992px) { 
        .form-body { max-width: 700px; margin: 0 auto; } 
        .bottom-nav { max-width: 100%; }
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
                <h5 class="fw-bold mb-0">Setor Bank Sampah</h5>
                <p class="mb-0 text-white-50" style="font-size:0.82rem;">Lapor pembuangan sampah</p>
            </div>
        </div>
    </div>

    <div class="form-body content-area mx-3 mx-lg-auto">

        {{-- Alert sukses --}}
        @if(session('sukses'))
            <div class="alert-sukses">
                <i class="bi bi-check-circle-fill fs-5"></i>
                {{ session('sukses') }}
            </div>
        @endif

        <form action="{{ route('sampah.simpan') }}" method="POST" enctype="multipart/form-data" id="form-sampah">
            @csrf

            {{-- ============================== --}}
            {{-- 1. AREA / LOKASI SETOR --}}
            {{-- ============================== --}}
            <span class="section-label">Area / Lokasi Setor</span>
            <div class="lokasi-grid">
                @foreach($lokasiSetor as $lok)
                    <label class="lokasi-item" id="label-lok-{{ $loop->index }}">
                        <input type="radio" name="lokasi_setor" value="{{ $lok }}"
                            onchange="pilihLokasi({{ $loop->index }})"
                            {{ old('lokasi_setor') === $lok ? 'checked' : '' }}>
                        <i class="bi bi-geo-alt"></i>
                        {{ $lok }}
                    </label>
                @endforeach
            </div>
            @error('lokasi_setor')
                <div class="text-danger mb-2" style="font-size:0.78rem;margin-top:-0.75rem;">{{ $message }}</div>
            @enderror

            {{-- ============================== --}}
            {{-- 2. JENIS SAMPAH (multi-pilih) --}}
            {{-- ============================== --}}
            <span class="section-label">Jenis Sampah Disetor</span>
            <span class="section-sub">Bisa pilih lebih dari satu</span>
            <div class="jenis-grid">
                @foreach($jenisSampah as $nama => $emoji)
                    @php $idx = $loop->index; @endphp
                    <label class="jenis-item {{ $nama === 'Lainnya' ? 'lainnya' : '' }}"
                           id="label-jenis-{{ $idx }}">
                        <input type="checkbox" name="jenis_sampah[]" value="{{ $nama }}"
                            onchange="toggleJenis({{ $idx }})"
                            {{ in_array($nama, old('jenis_sampah', [])) ? 'checked' : '' }}>
                        <span class="emoji">{{ $emoji }}</span>
                        <span>{{ $nama }}</span>
                    </label>
                @endforeach
            </div>
            @error('jenis_sampah')
                <div class="text-danger mb-2" style="font-size:0.78rem;margin-top:-0.75rem;">{{ $message }}</div>
            @enderror

            {{-- ============================== --}}
            {{-- 4. FOTO BUKTI SETOR --}}
            {{-- ============================== --}}
            <span class="section-label">Foto Bukti Setor <span class="text-danger">*</span></span>
            <div class="foto-box" id="foto-box" onclick="document.getElementById('input-foto').click()">
                <button type="button" class="foto-hapus" id="btn-hapus-foto" onclick="hapusFoto(event)">
                    <i class="bi bi-x"></i>
                </button>
                <div class="foto-placeholder" id="foto-placeholder">
                    <i class="bi bi-camera"></i>
                    <span>Ambil Foto Bukti</span>
                </div>
                <img src="" alt="Preview Foto" class="foto-preview-img" id="foto-preview">
            </div>
            <input type="file" name="foto_timbangan" id="input-foto"
                accept="image/*" capture="environment" style="display:none;"
                onchange="previewFoto(this)">
            @error('foto_timbangan')
                <div class="text-danger mb-3" style="font-size:0.78rem;margin-top:-0.75rem;">{{ $message }}</div>
            @enderror

            {{-- ============================== --}}
            {{-- 5. CATATAN (opsional) --}}
            {{-- ============================== --}}
            <span class="section-label">Catatan / Nama Barang <span class="text-danger">*</span></span>
            <textarea name="catatan" class="form-control mb-2" rows="2"
                style="border-radius:14px; border:1.5px solid #e5e7eb; font-size:0.85rem; resize:none;"
                placeholder="Tuliskan nama barang atau keterangan tambahan di sini...">{{ old('catatan') }}</textarea>
            @error('catatan')
                <div class="text-danger mb-4" style="font-size:0.78rem;margin-top:-0.2rem;">{{ $message }}</div>
            @enderror

            {{-- Tombol Kirim --}}
            <button type="submit" class="btn-kirim" id="btn-kirim">
                <i class="bi bi-send-fill"></i> KIRIM LAPORAN
            </button>
        </form>

        {{-- ============================== --}}
        {{-- RIWAYAT SETORAN --}}
        {{-- ============================== --}}
        @if($riwayat->count() > 0)
            <div class="mt-4">
                <span class="section-label">Riwayat Setoran Terakhir</span>
                @foreach($riwayat as $item)
                    <div class="riwayat-item">
                        <div class="riwayat-icon"><i class="bi bi-recycle"></i></div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:0.85rem;">
                                {{ $item->jenisSampahTeks() }}
                            </div>
                            <div class="text-secondary" style="font-size:0.75rem;">
                                {{ $item->lokasi_setor }}
                                · {{ $item->tanggal->translatedFormat('d M Y') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    {{-- Bottom Nav --}}
    <div class="bottom-nav">
        <a href="{{ route('dasbor.cs') }}" class="nav-item">
            <i class="bi bi-house-door"></i><span>Beranda</span>
        </a>
        <a href="{{ route('ceklis.index') }}" class="nav-item">
            <i class="bi bi-card-checklist"></i><span>Ceklis</span>
        </a>
        <a href="{{ route('barang.katalog') }}" class="nav-item">
            <i class="bi bi-box-seam"></i><span>Barang</span>
        </a>
        <a href="{{ route('sampah.buat') }}" class="nav-item active">
            <i class="bi bi-recycle"></i><span>Sampah</span>
        </a>
    </div>
</div>

<script>
// ============================================================
// Pilih lokasi (radio – satu pilihan)
// ============================================================
function pilihLokasi(idx) {
    document.querySelectorAll('.lokasi-item').forEach((el, i) => {
        el.classList.toggle('terpilih', i === idx);
    });
}

// ============================================================
// Toggle jenis sampah (checkbox – multi pilih)
// ============================================================
function toggleJenis(idx) {
    const label = document.getElementById('label-jenis-' + idx);
    const cb    = label.querySelector('input[type="checkbox"]');
    label.classList.toggle('terpilih', cb.checked);
}

// ============================================================
// Preview foto
// ============================================================
function previewFoto(input) {
    if (!input.files[0]) return;
    const url = URL.createObjectURL(input.files[0]);
    document.getElementById('foto-preview').src = url;
    document.getElementById('foto-preview').style.display  = 'block';
    document.getElementById('foto-placeholder').style.display = 'none';
    document.getElementById('btn-hapus-foto').style.display = 'flex';
    document.getElementById('foto-box').classList.add('ada-foto');
}

function hapusFoto(e) {
    e.stopPropagation();
    document.getElementById('input-foto').value = '';
    document.getElementById('foto-preview').src = '';
    document.getElementById('foto-preview').style.display  = 'none';
    document.getElementById('foto-placeholder').style.display = 'flex';
    document.getElementById('btn-hapus-foto').style.display = 'none';
    document.getElementById('foto-box').classList.remove('ada-foto');
}

// ============================================================
// Restore state jika ada old() dari validasi gagal
// ============================================================
document.addEventListener('DOMContentLoaded', function() {
    // Restore lokasi terpilih
    document.querySelectorAll('input[name="lokasi_setor"]').forEach((radio, i) => {
        if (radio.checked) pilihLokasi(i);
    });
    // Restore jenis sampah terpilih
    document.querySelectorAll('input[name="jenis_sampah[]"]').forEach((cb, i) => {
        if (cb.checked) {
            document.getElementById('label-jenis-' + i)?.classList.add('terpilih');
        }
    });
});

// Submit feedback
document.getElementById('form-sampah').addEventListener('submit', function() {
    const btn = document.getElementById('btn-kirim');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...';
});
</script>
@endsection
