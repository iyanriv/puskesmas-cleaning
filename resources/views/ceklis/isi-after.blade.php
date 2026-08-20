@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f7f9fa; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%;
        margin: 0 auto;
        background-color: #f4f7f6;
        min-height: 100vh;
        padding-bottom: 40px;
    }
    .header-section {  
        background-color: #10B981;
        border-radius: 0 0 30px 30px;
        padding: 1.5rem 1.5rem 2rem 1.5rem;
        color: white;
    }
    .step-badge {
        background: rgba(255,255,255,0.2);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    .content-area { padding: 1.25rem; }
    .kamera-box {
        width: 100%;
        aspect-ratio: 4/3;
        background: #1a1a2e;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        margin-bottom: 1rem;
    }
    #video-after, #preview-after {
        width: 100%; height: 100%; object-fit: cover;
    }
    #preview-after { display: none; }
    .overlay-info {
        position: absolute; bottom: 0; left: 0; right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: white; padding: 1.5rem 1rem 0.75rem; font-size: 0.75rem;
    }
    .overlay-info .waktu { font-size: 1rem; font-weight: 700; }
    .before-mini {
        background: white; border-radius: 16px;
        padding: 0.85rem 1rem; margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        display: flex; align-items: center; gap: 0.75rem;
    }
    .before-mini img {
        width: 56px; height: 42px;
        object-fit: cover; border-radius: 8px;
    }
    .btn-kamera {
        width: 65px; height: 65px; border-radius: 50%;
        background: white; border: 4px solid #10B981;
        color: #10B981; font-size: 1.5rem;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem auto; cursor: pointer; transition: all 0.2s;
        box-shadow: 0 4px 15px rgba(16,185,129,0.3);
    }
    .btn-kamera:active { transform: scale(0.92); }
    .btn-kamera.captured { background: #10B981; border-color: #10B981; color: white; }
    .btn-ulangi {
        background: none; border: 1px solid #d1d5db;
        color: #6b7280; font-size: 0.82rem;
        padding: 6px 16px; border-radius: 20px;
        margin: 0 auto 0.75rem; display: none;
    }
    .btn-simpan {
        width: 100%;
        background: #10B981; color: white; border: none;
        border-radius: 16px; padding: 1rem;
        font-size: 1rem; font-weight: 600;
        box-shadow: 0 4px 15px rgba(16,185,129,0.35);
        transition: all 0.2s;
    }
    .btn-simpan:disabled { background: #9ca3af; box-shadow: none; }
    .btn-simpan:not(:disabled):active { transform: scale(0.98); }
    .lokasi-status { font-size: 0.77rem; }
    .lokasi-status.berhasil { color: #10B981; }
    .lokasi-status.gagal    { color: #ef4444; }
    .lokasi-status.loading  { color: #f59e0b; }
    @media (min-width: 992px) { .content-area { max-width: 700px; margin: 0 auto; } }
</style>

<div class="mobile-container">
    {{-- Header --}}
    <div class="header-section">
        <div class="d-flex align-items-center mb-2">
            <div>
                <span class="step-badge">Langkah 2 dari 2</span>
                <h5 class="fw-bold mb-0">Foto AFTER</h5>
                <p class="mb-0 text-white-50" style="font-size:0.82rem;">{{ $ceklis->area->nama_ruangan }} — Lantai {{ $ceklis->area->lantai }}</p>
            </div>
        </div>
        <p class="text-white-50 small mb-0">Ambil foto kondisi ruangan <strong class="text-white">SETELAH</strong> dibersihkan.</p>
    </div>

    <div class="content-area">

        {{-- Tampilkan foto before sebagai referensi --}}
        @if($ceklis->foto_before)
        <div class="before-mini">
            <img src="{{ Storage::url($ceklis->foto_before) }}" alt="Foto Before">
            <div>
                <div class="fw-semibold" style="font-size:0.82rem;">Foto BEFORE tersimpan ✓</div>
                <div class="text-secondary" style="font-size:0.75rem;">Mulai: {{ $ceklis->waktu_mulai }}</div>
            </div>
        </div>
        @endif

        {{-- Kamera Box --}}
        <div class="kamera-box">
            <video id="video-after" autoplay playsinline muted></video>
            <img id="preview-after" alt="Foto After">
            <div class="overlay-info">
                <div class="waktu" id="jam-sekarang">--:--:--</div>
                <div id="teks-lokasi" class="lokasi-status loading"><i class="bi bi-geo-alt"></i> Mengambil lokasi...</div>
            </div>
        </div>

        {{-- Tombol kamera --}}
        <div class="text-center">
            <button type="button" class="btn-kamera" id="btn-ambil-foto" onclick="ambilFoto()">
                <i class="bi bi-camera" id="ikon-kamera"></i>
            </button>
            <button type="button" class="btn-ulangi d-block mx-auto" id="btn-ulangi" onclick="ulangi()">
                <i class="bi bi-arrow-counterclockwise"></i> Ulangi Foto
            </button>
        </div>

        {{-- Form submit --}}
        <form id="form-ceklis-after" action="{{ route('ceklis.simpan-after', $ceklis->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')
            <input type="hidden" name="lat_long" id="input-lat-long">
            <input type="file" name="foto_after_file" id="input-file-asli" accept="image/*" capture="environment" style="display:none;">

            <button type="submit" class="btn-simpan" id="btn-simpan" disabled>
                <i class="bi bi-camera me-2"></i> Ambil Foto Dulu
            </button>
        </form>

    </div>
</div>

<canvas id="canvas-foto" style="display:none;"></canvas>

<script>
    // Jam real-time
    function updateJam() {
        document.getElementById('jam-sekarang').textContent =
            new Date().toLocaleTimeString('id-ID', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
    }
    setInterval(updateJam, 1000); updateJam();

    // Geolokasi
    const elLokasi = document.getElementById('teks-lokasi');
    const elLatLong = document.getElementById('input-lat-long');
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            p => {
                const lat = p.coords.latitude.toFixed(6);
                const lng = p.coords.longitude.toFixed(6);
                elLatLong.value = lat + ',' + lng;
                elLokasi.className = 'lokasi-status berhasil';
                elLokasi.innerHTML = '<i class="bi bi-geo-alt-fill"></i> ' + lat + ', ' + lng;
            },
            () => {
                elLokasi.className = 'lokasi-status gagal';
                elLokasi.innerHTML = '<i class="bi bi-geo-alt"></i> Lokasi tidak tersedia';
            }
        );
    }

    // Kamera
    const video   = document.getElementById('video-after');
    const preview = document.getElementById('preview-after');
    const canvas  = document.getElementById('canvas-foto');
    const btnSimpan = document.getElementById('btn-simpan');
    let stream = null;

    async function bukaKamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 960 } },
                audio: false
            });
            video.srcObject = stream;
        } catch (err) {
            document.getElementById('btn-ambil-foto').onclick = () => document.getElementById('input-file-asli').click();
        }
    }

    function ambilFoto() {
        if (!stream) { document.getElementById('input-file-asli').click(); return; }
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        preview.src = canvas.toDataURL('image/jpeg', 0.85);
        preview.style.display = 'block';
        video.style.display   = 'none';

        canvas.toBlob(blob => {
            window._fotoAfter = new File([blob], 'after_' + Date.now() + '.jpg', {type:'image/jpeg'});
        }, 'image/jpeg', 0.85);

        const btnAmbil = document.getElementById('btn-ambil-foto');
        btnAmbil.classList.add('captured');
        document.getElementById('ikon-kamera').className = 'bi bi-check-lg';
        document.getElementById('btn-ulangi').style.display = 'block';

        btnSimpan.disabled = false;
        btnSimpan.innerHTML = '<i class="bi bi-check2-circle me-2"></i> Selesai & Simpan Ceklis';

        if (stream) stream.getTracks().forEach(t => t.stop());
    }

    function ulangi() {
        preview.style.display = 'none';
        video.style.display   = 'block';
        window._fotoAfter = null;

        const btnAmbil = document.getElementById('btn-ambil-foto');
        btnAmbil.classList.remove('captured');
        document.getElementById('ikon-kamera').className = 'bi bi-camera';
        document.getElementById('btn-ulangi').style.display = 'none';

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<i class="bi bi-camera me-2"></i> Ambil Foto Dulu';
        bukaKamera();
    }

    document.getElementById('form-ceklis-after').addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(this);

        if (window._fotoAfter) {
            fd.delete('foto_after_file');
            fd.append('foto_after', window._fotoAfter, window._fotoAfter.name);
        } else {
            const fi = document.getElementById('input-file-asli');
            if (fi.files[0]) {
                fd.append('foto_after', fi.files[0], fi.files[0].name);
            } else {
                alert('Harap ambil foto AFTER terlebih dahulu.');
                return;
            }
        }

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';

        fetch(this.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: fd
        })
        .then(r => { if (r.redirected) window.location = r.url; else return r.text(); })
        .catch(() => this.submit());
    });

    document.getElementById('input-file-asli').addEventListener('change', function() {
        if (this.files[0]) {
            preview.src = URL.createObjectURL(this.files[0]);
            preview.style.display = 'block';
            video.style.display   = 'none';

            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="bi bi-check2-circle me-2"></i> Selesai & Simpan Ceklis';

            document.getElementById('btn-ambil-foto').classList.add('captured');
            document.getElementById('ikon-kamera').className = 'bi bi-check-lg';
            document.getElementById('btn-ulangi').style.display = 'block';
        }
    });

    bukaKamera();
</script>
@endsection
