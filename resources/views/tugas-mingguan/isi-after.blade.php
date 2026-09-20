@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f7f9fa; font-family: 'Inter', sans-serif; }

    .mobile-container {
        max-width: 100%;
        margin: 0 auto;
        background-color: #f4f7f6;
        min-height: 100vh;
        padding-bottom: 50px;
    }

    .header-section {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 0 0 28px 28px;
        padding: 1.5rem 1.5rem 2rem 1.5rem;
        color: white;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
    }

    .content-area {
        padding: 1.25rem;
        margin-top: -1rem;
    }

    .step-badge {
        background: rgba(255,255,255,0.2);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 0.78rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 0.35rem;
    }

    .kamera-box {
        width: 100%;
        aspect-ratio: 4/3;
        background: #0f172a;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        margin-bottom: 1rem;
        border: 2px solid #10b981;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    #video-after, #preview-after {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    #preview-after { display: none; }

    .overlay-info {
        position: absolute;
        bottom: 0; left: 0; right: 0;
        background: linear-gradient(transparent, rgba(0,0,0,0.85));
        color: white;
        padding: 1.5rem 1rem 0.75rem;
        font-size: 0.75rem;
        pointer-events: none;
    }

    .overlay-info .waktu {
        font-size: 1.15rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }

    .btn-kamera {
        width: 68px; height: 68px;
        border-radius: 50%;
        background: white;
        border: 4px solid #10b981;
        color: #10b981;
        font-size: 1.6rem;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 0.75rem auto;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
    }

    .btn-kamera:active { transform: scale(0.92); }
    .btn-kamera.captured { background: #10B981; border-color: #10B981; color: white; }

    .btn-action-small {
        background: white;
        border: 1px solid #cbd5e1;
        color: #475569;
        font-size: 0.82rem;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-action-small:hover { background: #f1f5f9; color: #10b981; }

    .card-custom {
        background: white;
        border-radius: 18px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }

    .btn-simpan-selesai {
        width: 100%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 1rem;
        font-size: 1rem;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
        transition: all 0.2s;
    }
    .btn-simpan-selesai:disabled {
        background: #94a3b8;
        box-shadow: none;
        cursor: not-allowed;
    }

    .lokasi-status { font-size: 0.75rem; }
    .lokasi-status.berhasil { color: #86efac; }
    .lokasi-status.gagal { color: #fca5a5; }
    .lokasi-status.loading { color: #fde047; }

    @media (min-width: 992px) {
        .content-area { max-width: 680px; margin: -1rem auto 0; }
    }
</style>

<div class="mobile-container">
    <!-- Header -->
    <div class="header-section">
        <div class="d-flex align-items-center mb-2">
            <a href="{{ route('tugas-mingguan.detail', $tugas->id) }}" class="text-white me-3" style="font-size: 1.3rem;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <span class="step-badge">Langkah 2 &bull; Foto Selesai</span>
                <h5 class="fw-bold mb-0 text-white">FOTO SETELAH PENGERJAAN</h5>
            </div>
        </div>
        <p class="text-white-50 small mb-0" style="line-height: 1.4;">
            Pekerjaan telah tuntas? Ambil foto hasil akhir kondisi ruangan <strong>SETELAH</strong> dibersihkan.
        </p>
    </div>

    <div class="content-area">
        <!-- Rincian Tugas Ringkas -->
        <div class="card-custom">
            <span class="badge bg-success-subtle text-success border border-success-subtle mb-1">
                Tugas Mingguan Sedang Berjalan
            </span>
            <div class="fw-bold text-dark fs-6">{{ $tugas->rincian_kegiatan }}</div>
            <div class="text-muted mt-1" style="font-size: 0.78rem;">
                <i class="bi bi-calendar3 me-1"></i> {{ $tugas->tanggal->translatedFormat('d F Y') }} &bull;
                <i class="bi bi-person me-1"></i> {{ $tugas->user->name }}
            </div>
        </div>

        <!-- Realtime Camera Box (AFTER) -->
        <div class="card-custom">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-bold text-dark" style="font-size: 0.88rem;">
                    <i class="bi bi-camera-fill text-success me-1"></i> Kamera Real-Time (Foto AFTER) <span class="text-danger">*</span>
                </span>
                <span class="badge bg-danger" style="font-size: 0.7rem;">
                    <i class="bi bi-dot"></i> Live Cam
                </span>
            </div>
            <small class="text-muted d-block mb-3">
                Ambil foto langsung melalui kamera dengan cap waktu dan koordinat lokasi realtime.
            </small>

            <div class="kamera-box">
                <video id="video-after" autoplay playsinline muted></video>
                <img id="preview-after" alt="Foto After">
                <div class="overlay-info">
                    <div class="waktu" id="jam-sekarang-after">--:--:-- WIB</div>
                    <div id="teks-tanggal-after" style="opacity: 0.9;">{{ now()->translatedFormat('l, d F Y') }}</div>
                    <div id="teks-lokasi-after" class="lokasi-status loading mt-1">
                        <i class="bi bi-geo-alt"></i> Mengambil koordinat GPS...
                    </div>
                </div>
            </div>

            <!-- Tombol Shutter -->
            <div class="text-center">
                <button type="button" class="btn-kamera" id="btn-ambil-after" onclick="ambilFotoAfterRealtime()">
                    <i class="bi bi-camera" id="ikon-kamera-after"></i>
                </button>
                <div class="mt-2">
                    <button type="button" class="btn-action-small" id="btn-ulangi-after" style="display: none;" onclick="ulangiFotoAfter()">
                        <i class="bi bi-arrow-counterclockwise"></i> Ulangi Foto
                    </button>
                </div>
            </div>
        </div>

        <!-- Form Submit -->
        <form id="formTugasAfter" action="{{ route('tugas-mingguan.simpan-after', $tugas->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <button type="submit" class="btn-simpan-selesai" id="btn-simpan-after" disabled>
                <i class="bi bi-camera me-2"></i> Ambil Foto AFTER Terlebih Dahulu
            </button>
        </form>
    </div>
</div>

<canvas id="canvas-after" style="display: none;"></canvas>

<script>
    // 1. Live Clock
    function updateJamAfter() {
        const sekarang = new Date();
        const pad = (n) => n.toString().padStart(2, '0');
        const waktuStr = `${pad(sekarang.getHours())}:${pad(sekarang.getMinutes())}:${pad(sekarang.getSeconds())} WIB`;
        const el = document.getElementById('jam-sekarang-after');
        if (el) el.textContent = waktuStr;
    }
    setInterval(updateJamAfter, 1000);
    updateJamAfter();

    // 2. Geolocation Tracker
    let koordinatSimpanAfter = '';
    const elLokasi = document.getElementById('teks-lokasi-after');

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);
                koordinatSimpanAfter = lat + ', ' + lng;
                elLokasi.className = 'lokasi-status berhasil';
                elLokasi.innerHTML = '<i class="bi bi-geo-alt-fill"></i> ' + koordinatSimpanAfter;
            },
            function(err) {
                elLokasi.className = 'lokasi-status gagal';
                elLokasi.innerHTML = '<i class="bi bi-geo-alt"></i> GPS Aktif (Puskesmas Cempaka Putih)';
            },
            { enableHighAccuracy: true, timeout: 5000 }
        );
    } else {
        elLokasi.className = 'lokasi-status gagal';
        elLokasi.innerHTML = '<i class="bi bi-geo-alt"></i> Puskesmas Cempaka Putih';
    }

    // 3. Camera Live Stream
    const video = document.getElementById('video-after');
    const preview = document.getElementById('preview-after');
    const canvas = document.getElementById('canvas-after');
    const btnSimpan = document.getElementById('btn-simpan-after');
    const btnAmbil = document.getElementById('btn-ambil-after');
    const btnUlangi = document.getElementById('btn-ulangi-after');
    const ikonKamera = document.getElementById('ikon-kamera-after');
    let stream = null;
    let fileFotoAfter = null;

    async function bukaKamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 960 } },
                audio: false
            });
            video.srcObject = stream;
        } catch (err) {
            console.warn('Kamera tidak dapat diakses langsung:', err);
            const fileFallback = document.createElement('input');
            fileFallback.type = 'file';
            fileFallback.accept = 'image/*';
            fileFallback.capture = 'environment';
            fileFallback.onchange = function(e) {
                if (this.files && this.files[0]) {
                    processFallbackAfter(this.files[0]);
                }
            };
            btnAmbil.onclick = () => fileFallback.click();
        }
    }

    // 4. Capture photo with Watermark Stamped on Canvas
    function ambilFotoAfterRealtime() {
        if (!stream) return;

        canvas.width = video.videoWidth || 1280;
        canvas.height = video.videoHeight || 960;
        const ctx = canvas.getContext('2d');

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Burn Realtime Watermark
        const now = new Date();
        const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';

        const barHeight = 85;
        ctx.fillStyle = 'rgba(0, 0, 0, 0.72)';
        ctx.fillRect(0, canvas.height - barHeight, canvas.width, barHeight);

        ctx.fillStyle = '#22c55e';
        ctx.font = 'bold 22px sans-serif';
        ctx.fillText('● SIM KEBERSIHAN - PUSKESMAS CEMPAKA PUTIH', 25, canvas.height - 52);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 20px sans-serif';
        ctx.fillText(`[AFTER - SELESAI] ${dateStr} • ${timeStr}`, 25, canvas.height - 24);

        if (koordinatSimpanAfter) {
            ctx.fillStyle = '#94a3b8';
            ctx.font = '16px sans-serif';
            ctx.fillText(`GPS: ${koordinatSimpanAfter}`, canvas.width - 280, canvas.height - 24);
        }

        canvas.toBlob(function(blob) {
            const namaFile = 'tugas_after_' + Date.now() + '.jpg';
            fileFotoAfter = new File([blob], namaFile, { type: 'image/jpeg' });

            preview.src = canvas.toDataURL('image/jpeg', 0.85);
            preview.style.display = 'block';
            video.style.display = 'none';

            btnAmbil.classList.add('captured');
            ikonKamera.className = 'bi bi-check-lg';
            btnUlangi.style.display = 'inline-block';

            btnSimpan.disabled = false;
            btnSimpan.innerHTML = '<i class="bi bi-check2-circle me-2"></i> Konfirmasi & Selesaikan Tugas';

            if (stream) {
                stream.getTracks().forEach(t => t.stop());
            }
        }, 'image/jpeg', 0.85);
    }

    function processFallbackAfter(file) {
        const img = new Image();
        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            const now = new Date();
            const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
            const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';

            ctx.fillStyle = 'rgba(0, 0, 0, 0.72)';
            ctx.fillRect(0, canvas.height - 85, canvas.width, 85);
            ctx.fillStyle = '#22c55e';
            ctx.font = 'bold 22px sans-serif';
            ctx.fillText('● SIM KEBERSIHAN - PUSKESMAS CEMPAKA PUTIH', 25, canvas.height - 52);
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 20px sans-serif';
            ctx.fillText(`[AFTER - SELESAI] ${dateStr} • ${timeStr}`, 25, canvas.height - 24);

            canvas.toBlob(blob => {
                fileFotoAfter = new File([blob], file.name, { type: 'image/jpeg' });
                preview.src = canvas.toDataURL('image/jpeg', 0.85);
                preview.style.display = 'block';
                video.style.display = 'none';

                btnAmbil.classList.add('captured');
                ikonKamera.className = 'bi bi-check-lg';
                btnUlangi.style.display = 'inline-block';
                btnSimpan.disabled = false;
                btnSimpan.innerHTML = '<i class="bi bi-check2-circle me-2"></i> Konfirmasi & Selesaikan Tugas';
            }, 'image/jpeg', 0.85);
        };
        img.src = URL.createObjectURL(file);
    }

    function ulangiFotoAfter() {
        fileFotoAfter = null;
        preview.style.display = 'none';
        video.style.display = 'block';

        btnAmbil.classList.remove('captured');
        ikonKamera.className = 'bi bi-camera';
        btnUlangi.style.display = 'none';

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<i class="bi bi-camera me-2"></i> Ambil Foto AFTER Terlebih Dahulu';

        bukaKamera();
    }

    // 5. Submit form
    document.getElementById('formTugasAfter').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!fileFotoAfter) {
            alert('Harap ambil foto AFTER secara realtime.');
            return;
        }

        const form = this;
        const fd = new FormData(form);
        fd.append('foto_setelah', fileFotoAfter, fileFotoAfter.name);

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan Foto & Menyelesaikan Tugas...';

        fetch(form.action, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: fd
        })
        .then(response => {
            if (response.redirected) {
                window.location = response.url;
            } else {
                return response.text();
            }
        })
        .catch(err => {
            console.error(err);
            form.submit();
        });
    });

    bukaKamera();
</script>
@endsection
