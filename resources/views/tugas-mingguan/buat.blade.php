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
        background: linear-gradient(135deg, #0d8a4a 0%, #076033 100%);
        border-radius: 0 0 28px 28px;
        padding: 1.5rem 1.5rem 2rem 1.5rem;
        color: white;
        box-shadow: 0 4px 15px rgba(13, 138, 74, 0.2);
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

    /* Kamera Box */
    .kamera-box {
        width: 100%;
        aspect-ratio: 4/3;
        background: #0f172a;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        margin-bottom: 1rem;
        border: 2px solid #0d8a4a;
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    }

    #video-stream, #preview-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    #preview-img { display: none; }

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
        border: 4px solid #0d8a4a;
        color: #0d8a4a;
        font-size: 1.6rem;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 0.75rem auto;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 6px 20px rgba(13, 138, 74, 0.35);
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
    .btn-action-small:hover { background: #f1f5f9; color: #0d8a4a; }

    .card-custom {
        background: white;
        border-radius: 18px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.03);
    }

    .form-control-custom {
        border-radius: 12px;
        border: 1.5px solid #cbd5e1;
        padding: 0.75rem 1rem;
        font-size: 0.92rem;
    }
    .form-control-custom:focus {
        border-color: #0d8a4a;
        box-shadow: 0 0 0 3px rgba(13, 138, 74, 0.15);
    }

    .btn-simpan {
        width: 100%;
        background: linear-gradient(135deg, #12a65a 0%, #0d8a4a 100%);
        color: white;
        border: none;
        border-radius: 16px;
        padding: 1rem;
        font-size: 1rem;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(13, 138, 74, 0.35);
        transition: all 0.2s;
    }
    .btn-simpan:disabled {
        background: #94a3b8;
        box-shadow: none;
        cursor: not-allowed;
    }

    .thumbnails-strip {
        display: flex;
        gap: 0.5rem;
        overflow-x: auto;
        padding: 0.5rem 0;
    }
    .thumb-item {
        width: 65px;
        height: 65px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #0d8a4a;
        flex-shrink: 0;
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
            <a href="{{ route('tugas-mingguan.index') }}" class="text-white me-3" style="font-size: 1.3rem;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <span class="step-badge">Langkah 1 &bull; Foto Sebelum</span>
                <h5 class="fw-bold mb-0 text-white">TUGAS MINGGUAN CS</h5>
            </div>
        </div>
        <p class="text-white-50 small mb-0" style="line-height: 1.4;">
            Pekerjaan CS di luar tugas rutin (menguras dispenser, bersihkan exhaust, ac, atas lemari, dll). Ambil foto kondisi ruangan <strong>SEBELUM</strong> pengerjaan.
        </p>
    </div>

    <div class="content-area">
        <!-- Form Utama -->
        <form id="formTugasMingguan" action="{{ route('tugas-mingguan.simpan') }}" method="POST">
            @csrf

            <!-- Card Informasi Petugas & Kegiatan -->
            <div class="card-custom">
                <div class="d-flex align-items-center gap-2 mb-3 pb-2 border-bottom">
                    <div style="width: 38px; height: 38px; border-radius: 50%; background: #ecfdf5; color: #0d8a4a; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $pengguna->name }}</div>
                        <div class="text-muted" style="font-size: 0.78rem;">Nomor Pegawai: {{ $pengguna->nik }}</div>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="fw-bold text-dark mb-1" style="font-size: 0.8rem;">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" class="form-control form-control-custom bg-light" value="{{ $hariIni }}" required>
                    </div>
                    <div class="col-6">
                        <label class="fw-bold text-dark mb-1" style="font-size: 0.8rem;">Waktu Pelaporan <span class="text-danger">*</span></label>
                        <input type="time" name="waktu_pelaporan" class="form-control form-control-custom bg-light" value="{{ $jamSekarang }}" required>
                    </div>
                </div>

                <div>
                    <label for="rincian_kegiatan" class="fw-bold text-dark mb-1" style="font-size: 0.82rem;">
                        Rincian Kegiatan <span class="text-danger">*</span>
                    </label>
                    <textarea id="rincian_kegiatan" name="rincian_kegiatan" rows="3"
                        class="form-control form-control-custom"
                        placeholder="Ceritakan apa yang akan dikerjakan secara singkat (contoh: Menguras dispenser dan membersihkan exhaust fan ruang poli)..."
                        required></textarea>
                </div>
            </div>

            <!-- Kamera Realtime Box -->
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-bold text-dark" style="font-size: 0.88rem;">
                        <i class="bi bi-camera-fill text-success me-1"></i> Kamera Real-Time (Foto BEFORE) <span class="text-danger">*</span>
                    </span>
                    <span class="badge bg-danger" style="font-size: 0.7rem;">
                        <i class="bi bi-dot"></i> Live Cam
                    </span>
                </div>
                <small class="text-muted d-block mb-3">
                    Foto diambil langsung secara realtime dengan cap waktu & koordinat lokasi (tanpa upload berkas galeri).
                </small>

                <div class="kamera-box">
                    <video id="video-stream" autoplay playsinline muted></video>
                    <img id="preview-img" alt="Foto Before">
                    <div class="overlay-info">
                        <div class="waktu" id="jam-sekarang">--:--:-- WIB</div>
                        <div id="teks-tanggal" style="opacity: 0.9;">{{ now()->translatedFormat('l, d F Y') }}</div>
                        <div id="teks-lokasi" class="lokasi-status loading mt-1">
                            <i class="bi bi-geo-alt"></i> Mengambil koordinat GPS...
                        </div>
                    </div>
                </div>

                <!-- Tombol Shutter Kamera -->
                <div class="text-center">
                    <button type="button" class="btn-kamera" id="btn-ambil-foto" onclick="ambilFotoRealtime()">
                        <i class="bi bi-camera" id="ikon-kamera"></i>
                    </button>
                    <div class="d-flex justify-content-center gap-2 mt-2">
                        <button type="button" class="btn-action-small" id="btn-ulangi" style="display: none;" onclick="ulangiFoto()">
                            <i class="bi bi-arrow-counterclockwise"></i> Ulangi
                        </button>
                        <button type="button" class="btn-action-small" id="btn-tambah" style="display: none;" onclick="tambahFotoLagi()">
                            <i class="bi bi-plus-circle"></i> Ambil Foto Lagi (<span id="count-foto">1</span>/5)
                        </button>
                    </div>
                </div>

                <!-- Strip Foto Terambil -->
                <div id="box-thumbnails" class="mt-3" style="display: none;">
                    <div class="fw-bold text-dark mb-1" style="font-size: 0.78rem;">Foto Terambil (Real-Time Watermarked):</div>
                    <div class="thumbnails-strip" id="thumbnails-list"></div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" class="btn-simpan" id="btn-simpan" disabled>
                <i class="bi bi-camera me-2"></i> Ambil Foto BEFORE Terlebih Dahulu
            </button>
        </form>
    </div>
</div>

<canvas id="canvas-foto" style="display: none;"></canvas>

<script>
    // 1. Live Realtime Clock
    function updateJamRealtime() {
        const sekarang = new Date();
        const pad = (n) => n.toString().padStart(2, '0');
        const waktuStr = `${pad(sekarang.getHours())}:${pad(sekarang.getMinutes())}:${pad(sekarang.getSeconds())} WIB`;
        const el = document.getElementById('jam-sekarang');
        if (el) el.textContent = waktuStr;
    }
    setInterval(updateJamRealtime, 1000);
    updateJamRealtime();

    // 2. Geolocation Tracker
    let koordinatSimpan = '';
    const elLokasi = document.getElementById('teks-lokasi');

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                const lat = pos.coords.latitude.toFixed(6);
                const lng = pos.coords.longitude.toFixed(6);
                koordinatSimpan = lat + ', ' + lng;
                elLokasi.className = 'lokasi-status berhasil';
                elLokasi.innerHTML = '<i class="bi bi-geo-alt-fill"></i> ' + koordinatSimpan;
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
    const video = document.getElementById('video-stream');
    const preview = document.getElementById('preview-img');
    const canvas = document.getElementById('canvas-foto');
    const btnSimpan = document.getElementById('btn-simpan');
    const btnAmbil = document.getElementById('btn-ambil-foto');
    const btnUlangi = document.getElementById('btn-ulangi');
    const btnTambah = document.getElementById('btn-tambah');
    const ikonKamera = document.getElementById('ikon-kamera');
    let stream = null;
    let daftarFileFoto = []; // Array of File objects (up to 5)

    async function bukaKamera() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 960 } },
                audio: false
            });
            video.srcObject = stream;
        } catch (err) {
            console.warn('Kamera tidak dapat diakses langsung:', err);
            // Fallback kamera HTML5 langsung capture environment
            const fileFallback = document.createElement('input');
            fileFallback.type = 'file';
            fileFallback.accept = 'image/*';
            fileFallback.capture = 'environment';
            fileFallback.onchange = function(e) {
                if (this.files && this.files[0]) {
                    processFallbackImage(this.files[0]);
                }
            };
            btnAmbil.onclick = () => fileFallback.click();
        }
    }

    // 4. Capture photo with Watermark Stamped on Canvas
    function ambilFotoRealtime() {
        if (!stream) {
            return;
        }

        canvas.width = video.videoWidth || 1280;
        canvas.height = video.videoHeight || 960;
        const ctx = canvas.getContext('2d');

        // Draw camera frame
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        // Burn Realtime Watermark Stamp
        const now = new Date();
        const dateStr = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        const timeStr = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }) + ' WIB';

        // Dark gradient bar at bottom
        const barHeight = 85;
        ctx.fillStyle = 'rgba(0, 0, 0, 0.72)';
        ctx.fillRect(0, canvas.height - barHeight, canvas.width, barHeight);

        // Watermark text
        ctx.fillStyle = '#22c55e';
        ctx.font = 'bold 22px sans-serif';
        ctx.fillText('● SIM KEBERSIHAN - PUSKESMAS CEMPAKA PUTIH', 25, canvas.height - 52);

        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 20px sans-serif';
        ctx.fillText(`[BEFORE] ${dateStr} • ${timeStr}`, 25, canvas.height - 24);

        if (koordinatSimpan) {
            ctx.fillStyle = '#94a3b8';
            ctx.font = '16px sans-serif';
            ctx.fillText(`GPS: ${koordinatSimpan}`, canvas.width - 280, canvas.height - 24);
        }

        // Convert to Blob & File
        canvas.toBlob(function(blob) {
            const namaFile = 'tugas_before_' + Date.now() + '.jpg';
            const file = new File([blob], namaFile, { type: 'image/jpeg' });
            
            daftarFileFoto.push(file);
            updateThumbnailsUI();

            // Preview last image
            preview.src = canvas.toDataURL('image/jpeg', 0.85);
            preview.style.display = 'block';
            video.style.display = 'none';

            btnAmbil.classList.add('captured');
            ikonKamera.className = 'bi bi-check-lg';
            btnUlangi.style.display = 'inline-block';

            if (daftarFileFoto.length < 5) {
                btnTambah.style.display = 'inline-block';
                document.getElementById('count-foto').textContent = daftarFileFoto.length;
            } else {
                btnTambah.style.display = 'none';
            }

            // Enable submit
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = `<i class="bi bi-arrow-right-circle-fill me-2"></i> Simpan (${daftarFileFoto.length} Foto) & Lanjut Bersihkan`;

            if (stream) {
                stream.getTracks().forEach(t => t.stop());
            }
        }, 'image/jpeg', 0.85);
    }

    function processFallbackImage(file) {
        const img = new Image();
        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);

            // Burn watermark
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
            ctx.fillText(`[BEFORE] ${dateStr} • ${timeStr}`, 25, canvas.height - 24);

            canvas.toBlob(blob => {
                const watermarkedFile = new File([blob], file.name, { type: 'image/jpeg' });
                daftarFileFoto.push(watermarkedFile);
                updateThumbnailsUI();

                preview.src = canvas.toDataURL('image/jpeg', 0.85);
                preview.style.display = 'block';
                video.style.display = 'none';

                btnAmbil.classList.add('captured');
                ikonKamera.className = 'bi bi-check-lg';
                btnUlangi.style.display = 'inline-block';
                btnSimpan.disabled = false;
                btnSimpan.innerHTML = `<i class="bi bi-arrow-right-circle-fill me-2"></i> Simpan (${daftarFileFoto.length} Foto) & Lanjut Bersihkan`;
            }, 'image/jpeg', 0.85);
        };
        img.src = URL.createObjectURL(file);
    }

    function updateThumbnailsUI() {
        const box = document.getElementById('box-thumbnails');
        const list = document.getElementById('thumbnails-list');
        list.innerHTML = '';
        if (daftarFileFoto.length > 0) {
            box.style.display = 'block';
            daftarFileFoto.forEach(file => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.className = 'thumb-item';
                list.appendChild(img);
            });
        } else {
            box.style.display = 'none';
        }
    }

    function ulangiFoto() {
        daftarFileFoto = [];
        updateThumbnailsUI();
        preview.style.display = 'none';
        video.style.display = 'block';

        btnAmbil.classList.remove('captured');
        ikonKamera.className = 'bi bi-camera';
        btnUlangi.style.display = 'none';
        btnTambah.style.display = 'none';

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<i class="bi bi-camera me-2"></i> Ambil Foto BEFORE Terlebih Dahulu';

        bukaKamera();
    }

    function tambahFotoLagi() {
        preview.style.display = 'none';
        video.style.display = 'block';
        btnAmbil.classList.remove('captured');
        ikonKamera.className = 'bi bi-camera';
        btnTambah.style.display = 'none';
        bukaKamera();
    }

    // 5. Submit Form with multipart data
    document.getElementById('formTugasMingguan').addEventListener('submit', function(e) {
        e.preventDefault();

        if (daftarFileFoto.length === 0) {
            alert('Harap ambil setidaknya 1 foto BEFORE secara realtime.');
            return;
        }

        const form = this;
        const fd = new FormData(form);

        // Attach each real-time captured file to foto_sebelum[]
        daftarFileFoto.forEach((file, index) => {
            fd.append('foto_sebelum[]', file, file.name);
        });

        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan Data & Foto...';

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

    // Start camera
    bukaKamera();
</script>
@endsection
