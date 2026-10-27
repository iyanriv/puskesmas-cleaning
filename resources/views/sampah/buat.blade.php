@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f7f9fa; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%; margin: 0 auto;
        background-color: #ffffff; min-height: 100vh;
        padding-bottom: 90px;
    }

    .header-section {  
        background: linear-gradient(135deg, #31999E 0%, #205F63 100%);
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
    .lokasi-item i.bi-geo-alt { color: #9ca3af; font-size: 1rem; flex-shrink: 0; }
    .lokasi-item .check-icon { display: none; color: var(--brand-primary,#31999E); font-size: 1.05rem; margin-left: auto; flex-shrink: 0; }
    .lokasi-item.terpilih {
        border-color: var(--brand-primary,#31999E); background: var(--brand-pale,#F3FAF9); color: var(--brand-primary-dark,#205F63); font-weight: 600;
    }
    .lokasi-item.terpilih i.bi-geo-alt { color: var(--brand-primary,#31999E); }
    .lokasi-item.terpilih .check-icon { display: inline-block; }
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
    .jenis-item .emoji { font-size: 1.3rem; line-height: 1; flex-shrink: 0; }
    .jenis-item .check-icon { display: none; color: var(--brand-primary,#31999E); font-size: 1.05rem; margin-left: auto; flex-shrink: 0; }
    .jenis-item.terpilih {
        border-color: var(--brand-primary,#31999E); background: var(--brand-pale,#F3FAF9); color: var(--brand-primary-dark,#205F63); font-weight: 600;
    }
    .jenis-item.terpilih .check-icon { display: inline-block; }
    .jenis-item input[type="checkbox"] { display: none; }

    /* Area foto */
    .foto-box {
        border: 1.5px dashed #d1d5db; border-radius: 16px;
        background: #f3f4f6; cursor: pointer;
        transition: all 0.2s; margin-bottom: 1.25rem;
        position: relative; overflow: hidden;
    }
    .foto-box:hover { border-color: var(--brand-primary,#31999E); background: var(--brand-pale,#F3FAF9); }
    .foto-box.ada-foto { border-style: solid; border-color: var(--brand-primary,#31999E); }
    .foto-placeholder {
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
        padding: 2.2rem 1rem; text-align: center;
    }
    .foto-placeholder i { font-size: 2.2rem; color: #6b7280; margin-bottom: 0.4rem; }
    .foto-placeholder span { font-size: 0.85rem; font-weight: 600; color: #4b5563; }
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

    /* Ringkasan Laporan Box */
    .ringkasan-box {
        background: #eaf8f0;
        border: 1.5px solid #a3e6be;
        border-radius: 16px;
        padding: 0.9rem 1.15rem;
        margin-bottom: 1.25rem;
    }
    .ringkasan-title {
        font-size: 0.72rem;
        font-weight: 800;
        color: #059669;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 0.35rem;
    }
    .ringkasan-row {
        font-size: 0.84rem;
        color: #6b7280;
        margin-bottom: 0.15rem;
        line-height: 1.45;
    }
    .ringkasan-row:last-child {
        margin-bottom: 0;
    }
    .ringkasan-row strong {
        color: #111827;
        font-weight: 700;
    }

    /* Tombol kirim */
    .btn-kirim {
        width: 100%;
        background: linear-gradient(135deg, #74d8af 0%, #48c792 100%);
        color: white; border: none;
        border-radius: 16px; padding: 0.95rem 1rem;
        font-size: 0.92rem; font-weight: 800; letter-spacing: 0.04em;
        box-shadow: 0 4px 14px rgba(72, 199, 146, 0.35);
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
    .nav-item.active { color: #267A7E; background-color: #e6f4f5; }
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
                        <span>{{ $lok }}</span>
                        <i class="bi bi-check-circle-fill check-icon"></i>
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
                        <i class="bi bi-check-circle-fill check-icon"></i>
                    </label>
                @endforeach
            </div>
            @error('jenis_sampah')
                <div class="text-danger mb-2" style="font-size:0.78rem;margin-top:-0.75rem;">{{ $message }}</div>
            @enderror

            {{-- ============================== --}}
            {{-- 3. FOTO BUKTI SETOR --}}
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
            {{-- 4. RINGKASAN LAPORAN --}}
            {{-- ============================== --}}
            <div class="ringkasan-box" id="box-ringkasan">
                <div class="ringkasan-title">RINGKASAN LAPORAN</div>
                <div class="ringkasan-row">
                    Area: <strong id="ringkasan-area">—</strong>
                </div>
                <div class="ringkasan-row">
                    Sampah: <strong id="ringkasan-sampah">—</strong>
                </div>
            </div>

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
        <a href="{{ route('tugas-mingguan.index') }}" class="nav-item">
            <i class="bi bi-calendar2-check"></i><span>Tugas</span>
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
// Update Ringkasan Laporan Realtime
// ============================================================
function updateRingkasan() {
    // Area
    const checkedLok = document.querySelector('input[name="lokasi_setor"]:checked');
    const areaEl = document.getElementById('ringkasan-area');
    if (areaEl) {
        areaEl.textContent = checkedLok ? checkedLok.value : '—';
    }

    // Jenis Sampah
    const checkedJenis = Array.from(document.querySelectorAll('input[name="jenis_sampah[]"]:checked'))
                              .map(cb => cb.value);
    const sampahEl = document.getElementById('ringkasan-sampah');
    if (sampahEl) {
        sampahEl.textContent = checkedJenis.length > 0 ? checkedJenis.join(', ') : '—';
    }
}

// ============================================================
// Pilih lokasi (radio – satu pilihan)
// ============================================================
function pilihLokasi(idx) {
    document.querySelectorAll('.lokasi-item').forEach((el, i) => {
        el.classList.toggle('terpilih', i === idx);
    });
    updateRingkasan();
}

// ============================================================
// Toggle jenis sampah (checkbox – multi pilih)
// ============================================================
function toggleJenis(idx) {
    const label = document.getElementById('label-jenis-' + idx);
    const cb    = label.querySelector('input[type="checkbox"]');
    label.classList.toggle('terpilih', cb.checked);
    updateRingkasan();
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
    updateRingkasan();
});

// Submit feedback
document.getElementById('form-sampah').addEventListener('submit', function() {
    const btn = document.getElementById('btn-kirim');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengirim...';
});
</script>
@endsection
