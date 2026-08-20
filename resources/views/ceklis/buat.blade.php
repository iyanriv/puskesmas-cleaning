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
        background-color: #12a65a;
        border-radius: 0 0 30px 30px;
        padding: 1.5rem 1.5rem 2rem 1.5rem;
        color: white;
    }
    .content-area { padding: 1.25rem; margin-top: -0.5rem; }
    .step-badge {
        background: rgba(255,255,255,0.2);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 0.5rem;
    }
    /* Area kamera */
    .kamera-box {
        width: 100%;
        aspect-ratio: 4/3;
        background: #1a1a2e;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        margin-bottom: 1rem;
    }
    #video-before, #preview-before {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    #preview-before { display: none; }
    .overlay-info {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: white;
        padding: 1.5rem 1rem 0.75rem;
        font-size: 0.75rem;
    }
    .overlay-info .waktu { font-size: 1rem; font-weight: 700; }
    .btn-kamera {
        width: 65px; height: 65px;
        border-radius: 50%;
        background: white;
        border: 4px solid #12a65a;
        color: #12a65a;
        font-size: 1.5rem;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem auto;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 15px rgba(18,166,90,0.3);
    }
    .btn-kamera:active { transform: scale(0.92); }
    .btn-kamera.captured { background: #10B981; border-color: #10B981; color: white; }
    .btn-ulangi {
        display: none;
        background: none; border: 1px solid #d1d5db;
        color: #6b7280; font-size: 0.82rem;
        padding: 6px 16px; border-radius: 20px;
        margin: 0 auto 0.75rem;
    }
    .info-card {
        background: white;
        border-radius: 16px;
        padding: 1rem 1.2rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .info-row { display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.4rem; font-size: 0.83rem; color: #374151; }
    .info-row:last-child { margin-bottom: 0; }
    .info-row i { color: #12a65a; font-size: 1rem; }
    .btn-simpan {
        width: 100%;
        background: #12a65a;
        color: white;
        border: none;
        border-radius: 16px;
        padding: 1rem;
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 0.02em;
        box-shadow: 0 4px 15px rgba(18,166,90,0.3);
        transition: all 0.2s;
    }
    .btn-simpan:disabled { background: #9ca3af; box-shadow: none; }
    .btn-simpan:not(:disabled):active { transform: scale(0.98); }
    .lokasi-status { font-size: 0.77rem; }
    .lokasi-status.berhasil { color: #10B981; }
    .lokasi-status.gagal { color: #ef4444; }
    .lokasi-status.loading { color: #f59e0b; }
    @media (min-width: 992px) { .content-area { max-width: 700px; margin: 0 auto; } }
</style>

<div class="mobile-container">
    {{-- Header --}}
    <div class="header-section">
        <div class="d-flex align-items-center mb-2">
            <a href="{{ route('ceklis.index') }}" class="text-white me-2" style="font-size:1.2rem;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <span class="step-badge">Langkah 1 dari 2</span>
                <h5 class="fw-bold mb-0">Foto BEFORE</h5>
                <p class="mb-0 text-white-50" style="font-size:0.82rem;">{{ $area->nama_ruangan }} — Lantai {{ $area->lantai }}</p>
            </div>
        </div>
        <p class="text-white-50 small mb-0">Ambil foto kondisi ruangan <strong class="text-white">SEBELUM</strong> dibersihkan.</p>
    </div>

    <div class="content-area">

        {{-- Kamera Box --}}
        <div class="kamera-box">
            <video id="video-before" autoplay playsinline muted></video>
            <img id="preview-before" alt="Foto Before">
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

        {{-- Info ruangan --}}
        <div class="info-card">
            <div class="info-row"><i class="bi bi-building"></i> <span>{{ $area->nama_ruangan }}</span></div>
            <div class="info-row"><i class="bi bi-layers"></i> <span>Lantai {{ $area->lantai }}</span></div>
            <div class="info-row"><i class="bi bi-person"></i> <span>{{ auth()->user()->name }}</span></div>
        </div>

        {{-- Form submit --}}
        <form id="form-ceklis-before" action="{{ route('ceklis.simpan') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="area_id" value="{{ $area->id }}">
            <input type="hidden" name="lat_long" id="input-lat-long">
            <input type="hidden" name="foto_before" id="input-foto-before">
            {{-- Foto sebenarnya disimpan sebagai file, bukan base64 --}}
            <input type="file" name="foto_before_file" id="input-file-asli" accept="image/*" capture="environment" style="display:none;">

            <button type="submit" class="btn-simpan" id="btn-simpan" disabled>
                <i class="bi bi-camera me-2"></i> Ambil Foto Dulu
            </button>
        </form>

    </div>
</div>

<canvas id="canvas-foto" style="display:none;"></canvas>

<script>
    // ============================================================
    // JAM REAL-TIME
    // ============================================================
    function updateJam() {
        const sekarang = new Date();
        document.getElementById('jam-sekarang').textContent =
            sekarang.toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
    }
    setInterval(updateJam, 1000);
    updateJam();

    // ============================================================
    // GEOLOKASI
    // ============================================================
    let koordinatSimpan = '';
    const elLokasi = document.getElementById('teks-lokasi');
    const elInputLatLong = document.getElementById('input-lat-long');

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);
                koordinatSimpan = lat + ',' + lng;
                elInputLatLong.value = koordinatSimpan;
                elLokasi.className = 'lokasi-status berhasil';
                elLokasi.innerHTML = '<i class="bi bi-geo-alt-fill"></i> ' + lat + ', ' + lng;
            },
            function(err) {
                elLokasi.className = 'lokasi-status gagal';
                elLokasi.innerHTML = '<i class="bi bi-geo-alt"></i> Lokasi tidak tersedia';
            }
        );
    } else {
        elLokasi.className = 'lokasi-status gagal';
        elLokasi.innerHTML = '<i class="bi bi-geo-alt"></i> Browser tidak mendukung geolokasi';
    }

    // ============================================================
    // KAMERA – buka stream
    // ============================================================
    const video    = document.getElementById('video-before');
    const preview  = document.getElementById('preview-before');
    const canvas   = document.getElementById('canvas-foto');
    const btnSimpan = document.getElementById('btn-simpan');
    let stream     = null;
    let sudahCapture = false;

    async function bukaKamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 960 } },
                audio: false
            });
            video.srcObject = stream;
        } catch (err) {
            // Jika kamera tidak bisa dibuka, gunakan input file biasa
            console.warn('Kamera tidak tersedia, fallback ke input file.', err);
            document.getElementById('btn-ambil-foto').onclick = function() {
                document.getElementById('input-file-asli').click();
            };
        }
    }

    // ============================================================
    // AMBIL FOTO dari video stream
    // ============================================================
    function ambilFoto() {
        if (!stream) {
            document.getElementById('input-file-asli').click();
            return;
        }
        canvas.width  = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        // Tampilkan preview
        preview.src = canvas.toDataURL('image/jpeg', 0.85);
        preview.style.display = 'block';
        video.style.display = 'none';

        // Convert canvas ke Blob lalu taruh di form sebagai file
        canvas.toBlob(function(blob) {
            const namaFile = 'before_' + Date.now() + '.jpg';
            const file = new File([blob], namaFile, { type: 'image/jpeg' });

            // Buat DataTransfer untuk simulasi file input
            const dt = new DataTransfer();
            dt.items.add(file);

            // Ganti target input dengan foto dari kamera
            const formData = new FormData(document.getElementById('form-ceklis-before'));
            // Kita akan mengirim via AJAX agar bisa attach blob
            // Simpan blob untuk di-submit
            window._fotoBefore = file;
        }, 'image/jpeg', 0.85);

        // Update UI
        sudahCapture = true;
        const btnAmbil = document.getElementById('btn-ambil-foto');
        btnAmbil.classList.add('captured');
        document.getElementById('ikon-kamera').className = 'bi bi-check-lg';
        document.getElementById('btn-ulangi').style.display = 'block';

        // Aktifkan tombol simpan
        btnSimpan.disabled = false;
        btnSimpan.innerHTML = '<i class="bi bi-send me-2"></i> Simpan & Lanjut Bersihkan';

        // Matikan stream kamera
        if (stream) { stream.getTracks().forEach(t => t.stop()); }
    }

    // ============================================================
    // ULANGI foto
    // ============================================================
    function ulangi() {
        preview.style.display = 'none';
        video.style.display = 'block';
        sudahCapture = false;

        const btnAmbil = document.getElementById('btn-ambil-foto');
        btnAmbil.classList.remove('captured');
        document.getElementById('ikon-kamera').className = 'bi bi-camera';
        document.getElementById('btn-ulangi').style.display = 'none';

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<i class="bi bi-camera me-2"></i> Ambil Foto Dulu';

        window._fotoBefore = null;
        bukaKamera();
    }

    // ============================================================
    // SUBMIT FORM – attach file dari kamera atau input biasa
    // ============================================================
    document.getElementById('form-ceklis-before').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const fd   = new FormData(form);

        // Jika foto dari kamera tersedia, ganti
        if (window._fotoBefore) {
            fd.delete('foto_before_file');
            fd.append('foto_before', window._fotoBefore, window._fotoBefore.name);
        } else {
            // Ambil dari file input biasa
            const fileInput = document.getElementById('input-file-asli');
            if (fileInput.files[0]) {
                fd.append('foto_before', fileInput.files[0], fileInput.files[0].name);
            } else {
                alert('Harap ambil foto BEFORE terlebih dahulu.');
                return;
            }
        }

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...';

        fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: fd
        })
        .then(r => { if (r.redirected) { window.location = r.url; } else { return r.text(); } })
        .catch(() => form.submit()); // fallback
    });

    // Fallback: jika user pilih foto dari galeri
    document.getElementById('input-file-asli').addEventListener('change', function() {
        if (this.files[0]) {
            const url = URL.createObjectURL(this.files[0]);
            preview.src = url;
            preview.style.display = 'block';
            video.style.display = 'none';

            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="bi bi-send me-2"></i> Simpan & Lanjut Bersihkan';

            const btnAmbil = document.getElementById('btn-ambil-foto');
            btnAmbil.classList.add('captured');
            document.getElementById('ikon-kamera').className = 'bi bi-check-lg';
            document.getElementById('btn-ulangi').style.display = 'block';
        }
    });

    // Buka kamera saat halaman dimuat
    bukaKamera();
</script>
@endsection
