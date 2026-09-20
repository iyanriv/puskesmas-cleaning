<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Penilaian Kinerja CS - PKM Cempaka Putih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f2;
            color: #2d3748;
            padding-bottom: 3rem;
        }

        /* ── Top Header Banner ── */
        .form-hero-card {
            background: linear-gradient(135deg, #064e2b 0%, #0a6c3b 60%, #10B981 100%);
            border-radius: 20px;
            color: white;
            padding: 2.2rem 2rem;
            box-shadow: 0 8px 25px rgba(6, 78, 43, 0.15);
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .form-hero-card::after {
            content: '';
            position: absolute;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            right: -60px;
            bottom: -60px;
        }

        /* ── Form Section Card ── */
        .form-card {
            background: white;
            border-radius: 16px;
            border: 1px solid rgba(0,0,0,0.06);
            box-shadow: 0 4px 16px rgba(0,0,0,0.04);
            padding: 1.6rem;
            margin-bottom: 1.25rem;
        }

        .category-header {
            background: #f8faf9;
            border-left: 4px solid #0a6c3b;
            padding: 0.75rem 1rem;
            border-radius: 0 10px 10px 0;
            margin-bottom: 1.25rem;
        }

        .category-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #064e2b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
        }

        /* ── Score Badge Live Preview ── */
        .score-preview-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            display: inline-block;
            transition: all 0.2s;
        }
        .badge-sk { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .badge-k  { background: #ffedd5; color: #ea580c; border: 1px solid #fdba74; }
        .badge-c  { background: #e0f2fe; color: #0284c7; border: 1px solid #7dd3fc; }
        .badge-b  { background: #dbeafe; color: #2563eb; border: 1px solid #93c5fd; }
        .badge-sb { background: #d1fae5; color: #059669; border: 1px solid #6ee7b7; }

        /* ── Range Guide Box ── */
        .range-guide-box {
            background: #f9fbf9;
            border: 1.5px dashed #10B981;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin-top: 1rem;
        }

        .btn-quick-score {
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: white;
            color: #475569;
            cursor: pointer;
            transition: all 0.15s;
        }
        .btn-quick-score:hover {
            background: #0a6c3b;
            color: white;
            border-color: #0a6c3b;
        }

        .form-label-req {
            font-weight: 600;
            color: #1e293b;
            font-size: 0.95rem;
        }
        .text-required {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="container py-4" style="max-width: 820px;">

    {{-- ================================================================
         1. HERO BANNER & PANDUAN PENGISIAN
         ================================================================ --}}
    <div class="form-hero-card">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="bg-white p-2 rounded-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 38px; width: auto;" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Logo_Puskesmas.png/480px-Logo_Puskesmas.png'">
            </div>
            <div>
                <span class="badge px-3 py-1 rounded-pill mb-1 text-white" style="font-size: 0.75rem; background: rgba(255, 255, 255, 0.2); border: 1px solid rgba(255, 255, 255, 0.3);">
                    Formulir Evaluasi Resmi
                </span>
                <h3 class="fw-bold mb-0 text-white" style="font-size: 1.45rem;">
                    Formulir Penilaian Kinerja Cleaning Service
                </h3>
                <div class="text-white text-opacity-85 small">Puskesmas Kecamatan Cempaka Putih</div>
            </div>
        </div>

        <hr class="border-white border-opacity-25 my-3">

        {{-- Prosedur Pengisian --}}
        <div>
            <div class="fw-bold mb-1" style="font-size: 0.92rem;">
                <i class="bi bi-info-circle me-1"></i> Prosedur Pengisian (Kategori Penilaian):
            </div>
            <p class="mb-2 text-white text-opacity-90" style="font-size: 0.88rem;">
                Mohon untuk Bapak/Ibu mengisikan besar <strong>ANGKA SKOR</strong> pada masing-masing indikator penilaian yang ada dengan ketentuan range sbb:
            </p>
            <div class="d-flex flex-wrap gap-2 pt-1">
                <span class="badge bg-danger bg-opacity-90 px-3 py-2 rounded-pill" style="font-size:0.8rem;">
                    Sangat Kurang (SK) : 50
                </span>
                <span class="badge bg-warning text-dark bg-opacity-90 px-3 py-2 rounded-pill" style="font-size:0.8rem;">
                    Kurang (K) : &lt; 75
                </span>
                <span class="badge bg-info text-dark px-3 py-2 rounded-pill" style="font-size:0.8rem;">
                    Cukup (C) : 75
                </span>
                <span class="badge bg-primary px-3 py-2 rounded-pill" style="font-size:0.8rem;">
                    Baik : 76 - 85
                </span>
                <span class="badge bg-light text-success fw-bold px-3 py-2 rounded-pill" style="font-size:0.8rem;">
                    Sangat Baik : 86 - 95
                </span>
            </div>
        </div>
    </div>

    {{-- Alert Wajib Isi --}}
    <div class="alert alert-light border-0 shadow-sm rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2 text-muted" style="font-size: 0.85rem;">
        <i class="bi bi-asterisk text-danger"></i>
        <span>Tanda bintang (<span class="text-required">*</span>) menunjukkan pertanyaan yang <strong>wajib diisi</strong>.</span>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
            <h6 class="fw-bold mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> Mohon periksa kembali isian Anda:</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ================================================================
         FORM PENILAIAN
         ================================================================ --}}
    <form action="{{ route('penilaian-pj.simpan') }}" method="POST" enctype="multipart/form-data" id="formPenilaianPj">
        @csrf
        {{-- Honeypot: field ini harus kosong. Bot biasanya mengisi semua field. --}}
        <div style="display:none;" aria-hidden="true">
            <input type="text" name="website" value="" tabindex="-1" autocomplete="off">
        </div>
        {{-- Alert jika ada error throttle --}}
        @if(session('gagal'))
            <div class="alert alert-danger rounded-3 mx-4 mt-3 mb-0">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('gagal') }}
            </div>
        @endif

        {{-- ── SECTION 1: IDENTITAS ── --}}
        <div class="form-card">
            <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-success"></i> Identitas Petugas & Penilai
            </h5>

            {{-- Nama Petugas CS yang dinilai --}}
            <div class="mb-3">
                <label class="form-label form-label-req">
                    Nama Petugas CS yang dinilai <span class="text-required">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                    <select class="form-select" id="selectCs" onchange="handleSelectCs(this)" required>
                        <option value="">-- Pilih Petugas CS --</option>
                        @foreach($daftarCs as $cs)
                            <option value="{{ $cs->name }}" data-id="{{ $cs->id }}" {{ old('nama_petugas_cs') == $cs->name ? 'selected' : '' }}>
                                {{ $cs->name }} (CS)
                            </option>
                        @endforeach
                        <option value="__custom__">+ Ketik Nama Lainnya...</option>
                    </select>
                </div>
                <input type="hidden" name="petugas_cs_id" id="petugasCsId" value="{{ old('petugas_cs_id') }}">
                <div class="mt-2" id="wrapCustomCs" style="display: none;">
                    <input type="text" name="nama_petugas_cs" id="namaPetugasCs" class="form-control"
                           placeholder="Ketik nama lengkap petugas CS..." value="{{ old('nama_petugas_cs') }}">
                    <div class="form-text small">Ketik nama petugas CS bila tidak tercantum di daftar.</div>
                </div>
            </div>

            {{-- Lokasi CS bertugas --}}
            <div class="mb-3">
                <label class="form-label form-label-req">
                    Lokasi CS bertugas <span class="text-required">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                    <select class="form-select" id="selectLokasi" onchange="handleSelectLokasi(this)" required>
                        <option value="">-- Pilih Unit / Lantai --</option>
                        @foreach($daftarArea as $area)
                            <option value="{{ $area->lantai }}" {{ old('lokasi_tugas') == $area->lantai ? 'selected' : '' }}>
                                {{ $area->lantai }}
                            </option>
                        @endforeach
                        <option value="__custom__">+ Ketik Lokasi Lainnya...</option>
                    </select>
                </div>
                <div class="mt-2" id="wrapCustomLokasi" style="display: none;">
                    <input type="text" name="lokasi_tugas" id="lokasiTugas" class="form-control"
                           placeholder="Contoh: Lantai 3, RWS, CSSD, dsb..." value="{{ old('lokasi_tugas') }}">
                </div>
            </div>

            {{-- Nama PJ Kebersihan --}}
            <div class="mb-3">
                <label class="form-label form-label-req">
                    Nama PJ Kebersihan <span class="text-required">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person-badge"></i></span>
                    <input type="text" name="nama_pj" class="form-control" required
                           placeholder="Masukkan nama lengkap Bapak/Ibu PJ Kebersihan..." value="{{ old('nama_pj') }}">
                </div>
                <div class="form-text small">Penanggung Jawab Lantai / Ruangan yang mengisi penilaian.</div>
            </div>

            {{-- Waktu Penilaian (Tanggal) --}}
            <div class="mb-2">
                <label class="form-label form-label-req">
                    Waktu Penilaian (Tanggal) <span class="text-required">*</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-calendar-date"></i></span>
                    <input type="date" name="tanggal_penilaian" class="form-control" required
                           value="{{ old('tanggal_penilaian', date('Y-m-d')) }}">
                </div>
            </div>
        </div>

        {{-- ── SECTION 2: 16 INDIKATOR PENILAIAN ── --}}
        @foreach($indikator as $kategoriNama => $items)
            <div class="form-card">
                <div class="category-header d-flex align-items-center justify-content-between">
                    <h5 class="category-title">{{ $kategoriNama }}</h5>
                    <span class="badge bg-success bg-opacity-15 text-success small fw-semibold">
                        {{ count($items) }} Indikator
                    </span>
                </div>

                @foreach($items as $fieldKey => $info)
                    <div class="mb-4 pb-3 border-bottom">
                        <label class="form-label form-label-req mb-1 d-block">
                            {{ $loop->iteration }}. {{ $info['label'] }} <span class="text-required">*</span>
                        </label>
                        <p class="text-muted small mb-2">
                            {{ $info['deskripsi'] }}
                        </p>

                        <div class="row g-2 align-items-center">
                            <div class="col-sm-4 col-6">
                                <div class="input-group">
                                    <input type="number" name="{{ $fieldKey }}" id="{{ $fieldKey }}"
                                           class="form-control input-skor fw-bold"
                                           placeholder="50 - 95"
                                           min="0" max="100" required
                                           value="{{ old($fieldKey) }}"
                                           oninput="updateScorePreview(this, 'badge-{{ $fieldKey }}')">
                                    <span class="input-group-text text-muted" style="font-size:0.75rem;">Skor</span>
                                </div>
                            </div>
                            <div class="col-sm-4 col-6">
                                <span id="badge-{{ $fieldKey }}" class="score-preview-badge text-secondary border">
                                    Belum diisi
                                </span>
                            </div>
                            {{-- Preset Tombol Skor Cepat --}}
                            <div class="col-sm-4 col-12 d-flex gap-1 justify-content-sm-end">
                                <button type="button" class="btn-quick-score" onclick="setQuickScore('{{ $fieldKey }}', 50)">50</button>
                                <button type="button" class="btn-quick-score" onclick="setQuickScore('{{ $fieldKey }}', 75)">75</button>
                                <button type="button" class="btn-quick-score" onclick="setQuickScore('{{ $fieldKey }}', 80)">80</button>
                                <button type="button" class="btn-quick-score" onclick="setQuickScore('{{ $fieldKey }}', 90)">90</button>
                                <button type="button" class="btn-quick-score" onclick="setQuickScore('{{ $fieldKey }}', 95)">95</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach

        {{-- ── SECTION 3: MASUKAN / EVALUASI DARI PJ KEBERSIHAN ── --}}
        <div class="form-card">
            <h5 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                <i class="bi bi-chat-left-text-fill text-success"></i> Masukan / Evaluasi dari PJ Kebersihan
                <span class="text-required">*</span>
            </h5>
            <p class="text-muted small mb-3">
                Sampaikan catatan, evaluasi pekerjaan harian CS, atau saran perbaikan fasilitas ruangan.
            </p>
            <textarea name="masukan_evaluasi" class="form-control" rows="4" required
                      placeholder="Tuliskan masukan atau evaluasi Bapak/Ibu di sini...">{{ old('masukan_evaluasi') }}</textarea>
        </div>

        {{-- ── SECTION 4: BUKTI DOKUMENTASI KETIDAKSESUAIAN (JIKA ADA) ── --}}
        <div class="form-card">
            <h5 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-camera-fill text-success"></i> Bukti Dokumentasi Ketidaksesuaian (Jika Ada)
            </h5>
            <p class="text-muted small mb-3">
                Upload foto kondisi ruangan/toilet yang belum bersih atau bukti ketidaksesuaian lainnya. Maksimum 5 file (JPG, PNG, atau PDF. Maks 10 MB per file).
            </p>

            <div class="p-3 border rounded-3 bg-light bg-opacity-50">
                <input type="file" name="bukti_dokumentasi[]" class="form-control" multiple
                       accept="image/*,.pdf" id="inputFileBukti" onchange="previewFiles(this)">
                <div class="form-text small mt-2">
                    <i class="bi bi-info-circle me-1"></i> Anda dapat memilih hingga 5 file sekaligus dari galeri/kamera hp Anda.
                </div>

                {{-- File Preview Container --}}
                <div id="filePreviewList" class="mt-3 d-flex flex-wrap gap-2"></div>
            </div>
        </div>

        {{-- ── SUBMIT BUTTON ── --}}
        <div class="d-grid gap-2 mb-5">
            <button type="submit" id="btnSubmitForm" class="btn btn-success btn-lg rounded-3 fw-bold py-3 shadow-sm" style="background-color: #064e2b; border-color: #064e2b;">
                <i class="bi bi-send-fill me-2"></i> Kirim
            </button>
            <div class="text-center text-muted small mt-2">
                Jangan pernah mengirimkan sandi melalui Formulir ini.
            </div>
        </div>
    </form>

</div>

<script>
    // Inisialisasi dropdown CS & Lokasi
    function handleSelectCs(select) {
        const wrap = document.getElementById('wrapCustomCs');
        const inputCs = document.getElementById('namaPetugasCs');
        const hiddenId = document.getElementById('petugasCsId');

        if (select.value === '__custom__') {
            wrap.style.display = 'block';
            inputCs.value = '';
            inputCs.focus();
            hiddenId.value = '';
        } else if (select.value !== '') {
            wrap.style.display = 'none';
            inputCs.value = select.value;
            const selectedOpt = select.options[select.selectedIndex];
            hiddenId.value = selectedOpt.getAttribute('data-id') || '';
        } else {
            wrap.style.display = 'none';
            inputCs.value = '';
            hiddenId.value = '';
        }
    }

    function handleSelectLokasi(select) {
        const wrap = document.getElementById('wrapCustomLokasi');
        const inputLokasi = document.getElementById('lokasiTugas');

        if (select.value === '__custom__') {
            wrap.style.display = 'block';
            inputLokasi.value = '';
            inputLokasi.focus();
        } else if (select.value !== '') {
            wrap.style.display = 'none';
            inputLokasi.value = select.value;
        } else {
            wrap.style.display = 'none';
            inputLokasi.value = '';
        }
    }

    // Set nilai skor via tombol cepat
    function setQuickScore(fieldId, val) {
        const input = document.getElementById(fieldId);
        if (input) {
            input.value = val;
            updateScorePreview(input, 'badge-' + fieldId);
        }
    }

    // Live Badge Kategori Preview
    function updateScorePreview(input, badgeId) {
        const badge = document.getElementById(badgeId);
        if (!badge) return;

        const val = parseFloat(input.value);
        if (isNaN(val)) {
            badge.className = 'score-preview-badge text-secondary border';
            badge.innerText = 'Belum diisi';
            return;
        }

        if (val >= 86) {
            badge.className = 'score-preview-badge badge-sb';
            badge.innerText = 'Sangat Baik (' + val + ')';
        } else if (val >= 76) {
            badge.className = 'score-preview-badge badge-b';
            badge.innerText = 'Baik (' + val + ')';
        } else if (val >= 75) {
            badge.className = 'score-preview-badge badge-c';
            badge.innerText = 'Cukup (' + val + ')';
        } else if (val > 50) {
            badge.className = 'score-preview-badge badge-k';
            badge.innerText = 'Kurang (' + val + ')';
        } else {
            badge.className = 'score-preview-badge badge-sk';
            badge.innerText = 'Sangat Kurang (' + val + ')';
        }
    }

    // Preview File yang diunggah
    function previewFiles(input) {
        const container = document.getElementById('filePreviewList');
        container.innerHTML = '';

        if (!input.files || input.files.length === 0) return;

        if (input.files.length > 5) {
            alert('Maksimal 5 file yang diperbolehkan.');
            input.value = '';
            return;
        }

        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const sizeKb = Math.round(file.size / 1024);

            const item = document.createElement('div');
            item.className = 'p-2 rounded-3 bg-white border d-flex align-items-center gap-2 small';
            item.innerHTML = `
                <i class="bi bi-file-earmark-check text-success fs-5"></i>
                <div>
                    <div class="fw-semibold text-truncate" style="max-width:200px;">${file.name}</div>
                    <div class="text-muted" style="font-size:0.75rem;">${sizeKb} KB</div>
                </div>
            `;
            container.appendChild(item);
        }
    }

    // On Load: sinkronkan initial state jika old input ada
    document.addEventListener('DOMContentLoaded', function() {
        const selectCs = document.getElementById('selectCs');
        if (selectCs && selectCs.value) handleSelectCs(selectCs);

        const selectLokasi = document.getElementById('selectLokasi');
        if (selectLokasi && selectLokasi.value) handleSelectLokasi(selectLokasi);

        document.querySelectorAll('.input-skor').forEach(function(el) {
            if (el.value) {
                updateScorePreview(el, 'badge-' + el.id);
            }
        });

        const form = document.getElementById('formPenilaianPj');
        if (form) {
            form.addEventListener('submit', function() {
                const btn = document.getElementById('btnSubmitForm');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span> Mengirim...';
                }
            });
        }
    });
</script>

</body>
</html>
