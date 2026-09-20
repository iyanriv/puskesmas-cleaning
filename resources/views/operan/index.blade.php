@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f4f6f9; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%;
        margin: 0 auto;
        background-color: #f4f6f9;
        min-height: 100vh;
        padding-bottom: 60px;
    }

    /* ============================================================
       HEADER
    ============================================================ */
    .header-section {
        background: linear-gradient(135deg, #12a65a 0%, #0a7040 100%);
        border-radius: 0 0 28px 28px;
        padding: 1.5rem 1.5rem 2.5rem;
        color: white;
        position: relative;
    }
    .header-section h5 { font-size: 1.15rem; letter-spacing: -0.3px; }
    .content-area { padding: 1.25rem; margin-top: -1.5rem; }

    /* ============================================================
       BANNER TUGAS AKTIF (dari shift sebelumnya)
    ============================================================ */
    .banner-tugas-aktif {
        background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%);
        border-radius: 20px;
        padding: 1.1rem 1.25rem;
        color: white;
        margin-bottom: 1rem;
        box-shadow: 0 8px 24px rgba(153, 27, 27, 0.25);
        border: 1px solid rgba(255,255,255,0.1);
        position: relative;
        overflow: hidden;
    }
    .banner-tugas-aktif::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 100px; height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .banner-tugas-aktif .badge-urgent {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        font-size: 0.68rem;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 700;
        letter-spacing: 0.5px;
        animation: pulse-badge 1.5s infinite;
    }
    @keyframes pulse-badge {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.65; }
    }
    .tugas-checklist {
        background: rgba(0,0,0,0.2);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        margin: 0.75rem 0;
    }
    .tugas-checklist .item {
        font-size: 0.84rem;
        padding: 4px 0;
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .tugas-checklist .item:last-child { border-bottom: none; }
    .tugas-checklist .item::before { content: '◆'; font-size: 0.55rem; margin-top: 3px; flex-shrink: 0; }

    .btn-selesai-tugas {
        background: white;
        color: #15803d;
        border: none;
        border-radius: 12px;
        padding: 8px 18px;
        font-size: 0.83rem;
        font-weight: 700;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .btn-selesai-tugas:active { transform: scale(0.96); }
    .btn-eskalasi-tugas {
        background: rgba(255,255,255,0.15);
        color: white;
        border: 1px solid rgba(255,255,255,0.35);
        border-radius: 12px;
        padding: 8px 18px;
        font-size: 0.83rem;
        font-weight: 700;
        transition: all 0.2s;
    }
    .btn-eskalasi-tugas:hover { background: rgba(255,255,255,0.25); }

    /* ============================================================
       OPERAN MASUK (menunggu konfirmasi)
    ============================================================ */
    .notif-masuk {
        background: white;
        border-radius: 20px;
        padding: 1rem 1.2rem;
        margin-bottom: 0.85rem;
        box-shadow: 0 4px 20px rgba(18,166,90,0.1);
        border-left: 4px solid #12a65a;
        transition: all 0.2s;
    }
    .notif-masuk:hover { box-shadow: 0 6px 24px rgba(18,166,90,0.15); }
    .notif-badge {
        background: #fee2e2;
        color: #dc2626;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        animation: pulse-badge 1.5s infinite;
    }
    .btn-terima {
        background: linear-gradient(135deg, #10B981, #059669);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 8px 18px;
        font-size: 0.83rem;
        font-weight: 700;
        transition: all 0.2s;
        box-shadow: 0 3px 10px rgba(16,185,129,0.3);
    }
    .btn-terima:active { transform: scale(0.96); }

    /* ============================================================
       FORM KIRIM OPERAN
    ============================================================ */
    .form-card {
        background: white;
        border-radius: 20px;
        padding: 1.35rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        margin-bottom: 1rem;
    }
    .form-card-title {
        font-weight: 700;
        font-size: 0.88rem;
        color: #12a65a;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .form-select, .form-control {
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        padding: 0.65rem 0.85rem;
        font-size: 0.88rem;
        transition: all 0.2s;
    }
    .form-select:focus, .form-control:focus {
        border-color: #12a65a;
        box-shadow: 0 0 0 3px rgba(18,166,90,0.1);
    }
    .form-label { font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }

    /* Dinamis item tugas */
    .tugas-item-row {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    .tugas-item-row input {
        flex: 1;
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        padding: 0.55rem 0.75rem;
        font-size: 0.85rem;
    }
    .tugas-item-row input:focus {
        outline: none;
        border-color: #12a65a;
        box-shadow: 0 0 0 2px rgba(18,166,90,0.1);
    }
    .btn-hapus-item {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: 8px;
        width: 32px; height: 32px;
        font-size: 0.9rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-hapus-item:hover { background: #fecaca; }
    .btn-tambah-item {
        background: #ecfdf5;
        color: #12a65a;
        border: 1.5px dashed #12a65a;
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-size: 0.82rem;
        font-weight: 600;
        width: 100%;
        margin-top: 0.25rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .btn-tambah-item:hover { background: #d1fae5; }

    /* Checkbox alat */
    .alat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
    .alat-item {
        display: flex; align-items: center;
        background: #f9fafb; border: 1.5px solid #e5e7eb;
        border-radius: 12px; padding: 0.55rem 0.75rem;
        cursor: pointer; transition: all 0.2s;
        font-size: 0.82rem;
    }
    .alat-item input[type="checkbox"] { margin-right: 0.5rem; accent-color: #12a65a; }
    .alat-item:has(input:checked) {
        background: #ecfdf5; border-color: #12a65a; color: #12a65a; font-weight: 600;
    }

    /* Tombol kirim */
    .btn-kirim {
        width: 100%;
        background: linear-gradient(135deg, #12a65a, #0a7040);
        color: white; border: none;
        border-radius: 14px; padding: 0.9rem;
        font-size: 0.95rem; font-weight: 700;
        box-shadow: 0 4px 14px rgba(18,166,90,0.3);
        transition: all 0.2s;
    }
    .btn-kirim:active { transform: scale(0.98); }

    /* ============================================================
       RIWAYAT
    ============================================================ */
    .riwayat-card {
        background: white; border-radius: 16px;
        padding: 0.9rem 1.1rem; margin-bottom: 0.6rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        display: flex; align-items: center; gap: 0.75rem;
        transition: all 0.2s;
    }
    .riwayat-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,0.06); }
    .avatar-circle {
        width: 40px; height: 40px; border-radius: 50%;
        background: #ecfdf5; color: #12a65a;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.95rem; font-weight: 700; flex-shrink: 0;
    }
    .status-pill {
        padding: 3px 10px; border-radius: 20px;
        font-size: 0.7rem; font-weight: 700; flex-shrink: 0;
    }
    .status-menunggu { background: #fef3c7; color: #d97706; }
    .status-diterima { background: #d1fae5; color: #059669; }
    .section-label {
        font-size: 0.78rem; font-weight: 700; color: #6b7280;
        margin: 1.25rem 0 0.6rem 0;
        display: flex; align-items: center; gap: 0.4rem;
    }

    /* ============================================================
       MODAL ESKALASI
    ============================================================ */
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .modal-header {
        background: linear-gradient(135deg, #7f1d1d, #991b1b);
        color: white;
        border-radius: 20px 20px 0 0;
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }
    .modal-header .btn-close { filter: invert(1) brightness(2); }

    @media (min-width: 992px) {
        .content-area { max-width: 800px; margin-left: auto; margin-right: auto; }
        .alat-grid { grid-template-columns: repeat(4, 1fr); }
    }
</style>

<div class="mobile-container">
    {{-- ================================ HEADER ================================ --}}
    <div class="header-section">
        <div class="d-flex align-items-center">
            <a href="{{ route('dasbor.cs') }}" class="text-white me-3" style="font-size:1.2rem;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0">Operan Shift</h5>
                <p class="mb-0 text-white-50" style="font-size:0.82rem;">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            {{-- Badge notif --}}
            @php
                $totalNotif = $operanMasuk->count() + $tugasAktif->count();
            @endphp
            @if($totalNotif > 0)
                <span class="ms-auto notif-badge">{{ $totalNotif }} baru</span>
            @endif
        </div>
    </div>

    <div class="content-area">

        {{-- ====================================================== --}}
        {{-- ALERT FLASH                                           --}}
        {{-- ====================================================== --}}
        @if(session('sukses'))
            <div class="alert alert-success rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:0.84rem;">
                <i class="bi bi-check-circle-fill text-success"></i> {{ session('sukses') }}
            </div>
        @endif
        @if(session('gagal'))
            <div class="alert alert-danger rounded-3 py-2 px-3 mb-3 d-flex align-items-center gap-2" style="font-size:0.84rem;">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('gagal') }}
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info rounded-3 py-2 px-3 mb-3" style="font-size:0.84rem;">
                <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
            </div>
        @endif

        {{-- ====================================================== --}}
        {{-- SECTION 1: BANNER TUGAS AKTIF (prioritas TERTINGGI)  --}}
        {{-- ====================================================== --}}
        @if($tugasAktif->count() > 0)
            <div class="section-label"><i class="bi bi-exclamation-triangle-fill text-danger"></i> Tugas Aktif Dari Shift Sebelumnya</div>

            @foreach($tugasAktif as $tugas)
                <div class="banner-tugas-aktif">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge-urgent">⚡ HARUS DISELESAIKAN</span>
                            <div class="fw-bold mt-1" style="font-size:0.95rem;">
                                Dari: {{ $tugas->pengirim->name }}
                                @if($tugas->parentOperan)
                                    <span style="font-size:0.75rem; opacity:0.8;"> (diteruskan dari {{ $tugas->parentOperan->pengirim->name }})</span>
                                @endif
                            </div>
                            <div style="font-size:0.76rem; opacity:0.8;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $tugas->tanggal->translatedFormat('d M Y') }}
                                @if($tugas->tempat_tugas) &bull; {{ $tugas->tempat_tugas }} @endif
                            </div>
                        </div>
                    </div>

                    {{-- Daftar item tugas --}}
                    @if(!empty($tugas->tugas_items))
                        <div class="tugas-checklist">
                            @foreach($tugas->tugas_items as $item)
                                <div class="item">{{ $item }}</div>
                            @endforeach
                        </div>
                    @elseif($tugas->catatan)
                        <div class="tugas-checklist">
                            <div style="font-size:0.82rem;">{{ $tugas->catatan }}</div>
                        </div>
                    @endif

                    {{-- Aksi --}}
                    <div class="d-flex gap-2 flex-wrap mt-1">
                        {{-- Tombol Tandai Selesai --}}
                        <form action="{{ route('operan.selesaikan', $tugas->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-selesai-tugas">
                                <i class="bi bi-check2-circle me-1"></i> Selesai Dikerjakan
                            </button>
                        </form>

                        {{-- Tombol Eskalasi --}}
                        <button type="button" class="btn-eskalasi-tugas"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEskalasi"
                            data-operan-id="{{ $tugas->id }}"
                            data-pengirim="{{ $tugas->pengirim->name }}"
                            data-action="{{ route('operan.eskalasi', $tugas->id) }}">
                            <i class="bi bi-arrow-right-circle me-1"></i> Oper ke Shift Berikutnya
                        </button>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- ====================================================== --}}
        {{-- SECTION 2: OPERAN MASUK (menunggu konfirmasi)         --}}
        {{-- ====================================================== --}}
        @if($operanMasuk->count() > 0)
            <div class="section-label"><i class="bi bi-bell-fill text-danger"></i> Operan Masuk — Perlu Konfirmasi</div>

            @foreach($operanMasuk as $masuk)
                <div class="notif-masuk">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold" style="font-size:0.9rem;">
                                {{ $masuk->pengirim->name }}
                                @if($masuk->parentOperan)
                                    <span class="badge bg-secondary ms-1" style="font-size:0.65rem;">Diteruskan</span>
                                @endif
                            </div>
                            <div class="text-secondary" style="font-size:0.77rem;">
                                <i class="bi bi-clock me-1"></i>{{ $masuk->waktu }} — {{ $masuk->tanggal->translatedFormat('d M Y') }}
                            </div>
                        </div>
                        <form action="{{ route('operan.terima', $masuk->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-terima">
                                <i class="bi bi-check2"></i> Terima
                            </button>
                        </form>
                    </div>

                    {{-- Tags info --}}
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @if($masuk->tempat_tugas)
                            <span class="badge bg-light text-dark border" style="font-size:0.72rem;">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $masuk->tempat_tugas }}
                            </span>
                        @endif
                        @if($masuk->waktu_jaga)
                            <span class="badge bg-light text-dark border" style="font-size:0.72rem;">
                                <i class="bi bi-calendar-check-fill text-primary me-1"></i>{{ $masuk->waktu_jaga }}
                            </span>
                        @endif
                        @if(!empty($masuk->tugas_items))
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:0.72rem;">
                                <i class="bi bi-list-task me-1"></i>{{ count($masuk->tugas_items) }} Item Tugas
                            </span>
                        @endif
                    </div>

                    {{-- Catatan --}}
                    @if($masuk->catatan)
                        <div class="bg-light rounded-3 p-2" style="font-size:0.82rem; color:#374151;">
                            <i class="bi bi-chat-text text-success me-1"></i>{{ $masuk->catatan }}
                        </div>
                    @endif

                    {{-- Daftar item tugas (preview) --}}
                    @if(!empty($masuk->tugas_items))
                        <div class="mt-2 p-2 rounded-3 border border-danger-subtle bg-light">
                            <div class="fw-bold text-danger mb-1" style="font-size:0.75rem;"><i class="bi bi-list-task me-1"></i>Tugas yang Perlu Diselesaikan:</div>
                            @foreach($masuk->tugas_items as $item)
                                <div class="text-secondary" style="font-size:0.8rem; padding: 2px 0;">• {{ $item }}</div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Status alat --}}
                    @if($masuk->status_alat && count($masuk->status_alat) > 0)
                        <div class="mt-2">
                            <div style="font-size:0.72rem; font-weight:600; color:#6b7280; margin-bottom:4px;"><i class="bi bi-tools me-1"></i>Alat Tersedia & Baik:</div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($masuk->status_alat as $alat)
                                    <span style="background:#d1fae5; color:#059669; border:1px solid #a7f3d0; padding:2px 8px; border-radius:10px; font-size:0.7rem; font-weight:600;">✓ {{ $alat }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif

        {{-- ====================================================== --}}
        {{-- SECTION 3: FORM KIRIM OPERAN BARU                     --}}
        {{-- ====================================================== --}}
        <div class="section-label"><i class="bi bi-send"></i> Kirim Operan Ke Shift Berikutnya</div>
        <div class="form-card">
            <div class="form-card-title"><i class="bi bi-arrow-left-right"></i> Form Serah Terima Tugas</div>

            <form action="{{ route('operan.kirim') }}" method="POST" id="formKirimOperan">
                @csrf

                {{-- Pilih penerima --}}
                <div class="mb-3">
                    <label class="form-label">Rekan Penerima Operan <span class="text-danger">*</span></label>
                    <select name="penerima_id" class="form-select" required>
                        <option value="">-- Pilih Rekan --</option>
                        @foreach($daftarPenerima as $rekan)
                            <option value="{{ $rekan->id }}" {{ old('penerima_id') == $rekan->id ? 'selected' : '' }}>
                                {{ $rekan->name }} ({{ $rekan->nik }})
                            </option>
                        @endforeach
                    </select>
                    @error('penerima_id')
                        <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tempat Tugas --}}
                <div class="mb-3">
                    <label class="form-label">Tempat Tugas Pelapor <span class="text-danger">*</span></label>
                    <select name="tempat_tugas" class="form-select" required>
                        <option value="">-- Pilih Tempat Tugas --</option>
                        @foreach($daftarArea as $area)
                            <option value="{{ $area }}" {{ old('tempat_tugas') == $area ? 'selected' : '' }}>{{ $area }}</option>
                        @endforeach
                    </select>
                    @error('tempat_tugas')
                        <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Waktu Jaga --}}
                <div class="mb-3">
                    <label class="form-label">Waktu Jaga <span class="text-danger">*</span></label>
                    <select name="waktu_jaga" class="form-select" required>
                        <option value="">-- Pilih Waktu Jaga --</option>
                        <option value="Shift Pagi"   {{ old('waktu_jaga') == 'Shift Pagi'   ? 'selected' : '' }}>Shift Pagi</option>
                        <option value="Shift Siang"  {{ old('waktu_jaga') == 'Shift Siang'  ? 'selected' : '' }}>Shift Siang</option>
                        <option value="Shift Malam"  {{ old('waktu_jaga') == 'Shift Malam'  ? 'selected' : '' }}>Shift Malam</option>
                        <option value="Non Shift"    {{ old('waktu_jaga') == 'Non Shift'    ? 'selected' : '' }}>Non Shift</option>
                        <option value="Yang lain"    {{ old('waktu_jaga') == 'Yang lain'    ? 'selected' : '' }}>Yang lain</option>
                    </select>
                    @error('waktu_jaga')
                        <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Catatan Umum --}}
                <div class="mb-3">
                    <label class="form-label">Uraian Kegiatan <span class="text-danger">*</span></label>
                    <textarea name="catatan" class="form-control" rows="3" required
                        placeholder="Isi ringkasan kondisi & situasi shift yang berjalan..."
                        maxlength="1000">{{ old('catatan') }}</textarea>
                    <div class="text-secondary text-end" style="font-size:0.72rem;" id="hitung-karakter">0/1000</div>
                    @error('catatan')
                        <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Item Tugas yang Harus Diselesaikan --}}
                <div class="mb-3">
                    <label class="form-label d-flex align-items-center gap-1">
                        <i class="bi bi-list-task text-danger"></i>
                        Tugas yang Perlu Diselesaikan Shift Berikutnya
                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-1" style="font-size:0.65rem;">Opsional</span>
                    </label>
                    <small class="text-muted d-block mb-2" style="font-size:0.78rem;">
                        Isi jika ada tugas yang belum selesai dan harus dilanjutkan oleh shift berikutnya.
                    </small>
                    <div id="container-tugas-items">
                        <div class="tugas-item-row" id="baris-1">
                            <input type="text" name="tugas_items[]"
                                placeholder="Contoh: Menguras dispenser di ruang poli umum..."
                                maxlength="300">
                            <button type="button" class="btn-hapus-item" onclick="hapusBaris(this)" style="display:none;">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn-tambah-item" onclick="tambahBarisItem()">
                        <i class="bi bi-plus-circle me-1"></i> + Tambah Item Tugas
                    </button>
                </div>

                <button type="submit" class="btn-kirim">
                    <i class="bi bi-send me-2"></i> Kirim Operan Sekarang
                </button>
            </form>
        </div>

        {{-- ====================================================== --}}
        {{-- SECTION 4: RIWAYAT HARI INI                           --}}
        {{-- ====================================================== --}}
        @if($operanDikirim->count() > 0 || $operanDiterima->count() > 0)
            <div class="section-label"><i class="bi bi-clock-history"></i> Riwayat Hari Ini</div>

            @foreach($operanDikirim as $dikirim)
                <div class="riwayat-card">
                    <div class="avatar-circle">{{ strtoupper(substr($dikirim->penerima->name, 0, 1)) }}</div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:0.85rem;">→ {{ $dikirim->penerima->name }}</div>
                        <div class="text-secondary" style="font-size:0.75rem;">
                            Dikirim {{ $dikirim->waktu }}
                            @if($dikirim->eskalasiBerikutnya)
                                <span class="badge bg-secondary ms-1" style="font-size:0.65rem;">Diteruskan ke {{ $dikirim->eskalasiBerikutnya->penerima->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <span class="status-pill status-{{ $dikirim->status_terima }}">
                            {{ $dikirim->status_terima === 'diterima' ? 'Diterima ✓' : 'Menunggu' }}
                        </span>
                        @if(!empty($dikirim->tugas_items))
                            {!! $dikirim->badgePenyelesaian() !!}
                        @endif
                    </div>
                </div>
            @endforeach

            @foreach($operanDiterima as $diterima)
                <div class="riwayat-card" style="border-left: 3px solid #10B981;">
                    <div class="avatar-circle" style="background:#d1fae5; color:#059669;">
                        {{ strtoupper(substr($diterima->pengirim->name, 0, 1)) }}
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:0.85rem;">← {{ $diterima->pengirim->name }}</div>
                        <div class="text-secondary" style="font-size:0.75rem;">Diterima {{ $diterima->waktu }}</div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-1">
                        <span class="status-pill status-diterima">Diterima ✓</span>
                        @if(!empty($diterima->tugas_items))
                            {!! $diterima->badgePenyelesaian() !!}
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

    </div>{{-- /content-area --}}
</div>

{{-- ====================================================== --}}
{{-- MODAL ESKALASI                                         --}}
{{-- ====================================================== --}}
<div class="modal fade" id="modalEskalasi" tabindex="-1" aria-labelledby="labelModalEskalasi" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="labelModalEskalasi">
                        <i class="bi bi-arrow-right-circle me-2"></i>Oper ke Shift Berikutnya
                    </h5>
                    <small style="opacity:0.8;">Tugas dari: <span id="modal-nama-pengirim">—</span></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form id="formEskalasi" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-warning rounded-3 d-flex gap-2 align-items-start mb-3" style="font-size:0.82rem;">
                        <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                        <span>Tugas ini akan <strong>diteruskan</strong> ke rekan shift berikutnya. Pastikan Anda memberikan alasan yang jelas agar rekan dapat memahami situasinya.</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Penerima Berikutnya <span class="text-danger">*</span></label>
                        <select name="penerima_eskalasi_id" class="form-select" required>
                            <option value="">-- Pilih Rekan --</option>
                            @foreach($daftarPenerima as $rekan)
                                <option value="{{ $rekan->id }}">
                                    {{ $rekan->name }} ({{ $rekan->nik }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-1">
                        <label class="form-label">Alasan Tidak Bisa Mengerjakan <span class="text-danger">*</span></label>
                        <textarea name="catatan_eskalasi" class="form-control" rows="4" required
                            minlength="10" maxlength="500"
                            placeholder="Ceritakan alasan kenapa tugas ini tidak dapat diselesaikan pada shift ini (min. 10 karakter)..."></textarea>
                        <div class="text-muted text-end mt-1" style="font-size:0.72rem;" id="hitung-eskalasi">0/500</div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 fw-bold px-4">
                        <i class="bi bi-arrow-right-circle me-1"></i> Konfirmasi & Teruskan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// ─────────────────────────────────────────────────────
// 1. Hitung karakter textarea utama
// ─────────────────────────────────────────────────────
const catatanEl = document.querySelector('textarea[name="catatan"]');
const hitungEl  = document.getElementById('hitung-karakter');
if (catatanEl && hitungEl) {
    catatanEl.addEventListener('input', function() {
        hitungEl.textContent = this.value.length + '/1000';
    });
}

// ─────────────────────────────────────────────────────
// 2. Dinamis Item Tugas (tambah/hapus baris)
// ─────────────────────────────────────────────────────
let jumlahBaris = 1;

function tambahBarisItem() {
    if (jumlahBaris >= 10) return;
    jumlahBaris++;
    const container = document.getElementById('container-tugas-items');
    const div = document.createElement('div');
    div.className = 'tugas-item-row';
    div.id = 'baris-' + jumlahBaris;
    div.innerHTML = `
        <input type="text" name="tugas_items[]"
            placeholder="Tambahkan item tugas..."
            maxlength="300">
        <button type="button" class="btn-hapus-item" onclick="hapusBaris(this)">
            <i class="bi bi-x"></i>
        </button>
    `;
    container.appendChild(div);
    div.querySelector('input').focus();
    perbarauiTombolHapus();
}

function hapusBaris(btn) {
    btn.closest('.tugas-item-row').remove();
    jumlahBaris--;
    perbarauiTombolHapus();
}

function perbarauiTombolHapus() {
    const semua = document.querySelectorAll('.tugas-item-row');
    semua.forEach((baris, index) => {
        const tombolHapus = baris.querySelector('.btn-hapus-item');
        if (tombolHapus) {
            tombolHapus.style.display = semua.length > 1 ? 'flex' : 'none';
        }
    });
}

// ─────────────────────────────────────────────────────
// 3. Modal Eskalasi — isi action & nama pengirim
// ─────────────────────────────────────────────────────
const modalEskalasi = document.getElementById('modalEskalasi');
if (modalEskalasi) {
    modalEskalasi.addEventListener('show.bs.modal', function(event) {
        const tombol    = event.relatedTarget;
        const action    = tombol.dataset.action;
        const pengirim  = tombol.dataset.pengirim;
        document.getElementById('formEskalasi').action = action;
        document.getElementById('modal-nama-pengirim').textContent = pengirim;
    });
}

// Hitung karakter textarea eskalasi
const eskalasiTextarea = document.querySelector('textarea[name="catatan_eskalasi"]');
const hitungEskalasi   = document.getElementById('hitung-eskalasi');
if (eskalasiTextarea && hitungEskalasi) {
    eskalasiTextarea.addEventListener('input', function() {
        hitungEskalasi.textContent = this.value.length + '/500';
    });
}
</script>
@endsection
