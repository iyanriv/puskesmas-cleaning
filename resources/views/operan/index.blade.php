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
        background: linear-gradient(135deg, #12a65a 0%, #0a7040 100%);
        border-radius: 0 0 30px 30px;
        padding: 1.5rem 1.5rem 2.5rem 1.5rem;
        color: white;
    }
    .content-area { padding: 1.25rem; margin-top: -1.5rem; }

    /* Notifikasi masuk */
    .notif-masuk {
        background: white;
        border-radius: 20px;
        padding: 1rem 1.2rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 20px rgba(18,166,90,0.12);
        border-left: 4px solid #12a65a;
    }
    .notif-badge {
        background: #fee2e2; color: #dc2626;
        font-size: 0.7rem; font-weight: 700;
        padding: 3px 10px; border-radius: 20px;
        animation: pulse 1.5s infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; } 50% { opacity: 0.6; }
    }
    .btn-terima {
        background: #10B981; color: white; border: none;
        border-radius: 12px; padding: 8px 18px;
        font-size: 0.83rem; font-weight: 600;
        transition: all 0.2s;
    }
    .btn-terima:active { transform: scale(0.96); }

    /* Form kirim operan */
    .form-card {
        background: white;
        border-radius: 20px;
        padding: 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        margin-bottom: 1rem;
    }
    .form-card h6 {
        font-weight: 700; font-size: 0.9rem;
        color: #12a65a; margin-bottom: 1rem;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .form-select, .form-control {
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        padding: 0.65rem 0.85rem;
        font-size: 0.88rem;
    }
    .form-select:focus, .form-control:focus {
        border-color: #12a65a;
        box-shadow: 0 0 0 3px rgba(18,166,90,0.1);
    }
    .form-label { font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 0.4rem; }

    /* Checkbox alat */
    .alat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; }
    .alat-item {
        display: flex; align-items: center;
        background: #f9fafb; border: 1.5px solid #e5e7eb;
        border-radius: 12px; padding: 0.6rem 0.75rem;
        cursor: pointer; transition: all 0.2s;
        font-size: 0.82rem;
    }
    .alat-item input[type="checkbox"] { margin-right: 0.5rem; accent-color: #12a65a; }
    .alat-item:has(input:checked) {
        background: #eff6ff; border-color: #12a65a; color: #12a65a; font-weight: 600;
    }

    /* Tombol kirim */
    .btn-kirim {
        width: 100%; background: #12a65a; color: white; border: none;
        border-radius: 14px; padding: 0.9rem;
        font-size: 0.95rem; font-weight: 700;
        box-shadow: 0 4px 14px rgba(18,166,90,0.3);
        transition: all 0.2s;
    }
    .btn-kirim:active { transform: scale(0.98); }

    /* Riwayat */
    .riwayat-card {
        background: white; border-radius: 16px;
        padding: 0.9rem 1.1rem; margin-bottom: 0.6rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        display: flex; align-items: center; gap: 0.75rem;
    }
    .avatar-circle {
        width: 40px; height: 40px; border-radius: 50%;
        background: #eff6ff; color: #12a65a;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; font-weight: 700; flex-shrink: 0;
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
    }
    @media (min-width: 992px) {
        .content-area { max-width: 800px; margin-left: auto; margin-right: auto; }
        .alat-grid { grid-template-columns: repeat(4, 1fr); }
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
                <h5 class="fw-bold mb-0">Operan Shift</h5>
                <p class="mb-0 text-white-50" style="font-size:0.82rem;">{{ now()->translatedFormat('l, d F Y') }}</p>
            </div>
            {{-- Badge notif masuk --}}
            @if($operanMasuk->count() > 0)
                <span class="notif-badge ms-auto">{{ $operanMasuk->count() }} masuk</span>
            @endif
        </div>
    </div>

    <div class="content-area">

        {{-- Alert --}}
        @if(session('sukses'))
            <div class="alert alert-success rounded-3 py-2 px-3 mb-3" style="font-size:0.84rem;">
                <i class="bi bi-check-circle me-1"></i> {{ session('sukses') }}
            </div>
        @endif
        @if(session('gagal'))
            <div class="alert alert-danger rounded-3 py-2 px-3 mb-3" style="font-size:0.84rem;">
                <i class="bi bi-exclamation-circle me-1"></i> {{ session('gagal') }}
            </div>
        @endif
        @if(session('info'))
            <div class="alert alert-info rounded-3 py-2 px-3 mb-3" style="font-size:0.84rem;">
                <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
            </div>
        @endif

        {{-- ======================================== --}}
        {{-- OPERAN MASUK (perlu dikonfirmasi) --}}
        {{-- ======================================== --}}
        @if($operanMasuk->count() > 0)
            <div class="section-label"><i class="bi bi-bell-fill text-danger me-1"></i> Operan Masuk</div>
            @foreach($operanMasuk as $masuk)
                <div class="notif-masuk">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="fw-bold" style="font-size:0.9rem;">{{ $masuk->pengirim->name }}</div>
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

                    {{-- Info Tugas --}}
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        @if($masuk->tempat_tugas)
                            <span class="badge bg-light text-dark" style="font-size:0.72rem; border:1px solid #e5e7eb;">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $masuk->tempat_tugas }}
                            </span>
                        @endif
                        @if($masuk->waktu_jaga)
                            <span class="badge bg-light text-dark" style="font-size:0.72rem; border:1px solid #e5e7eb;">
                                <i class="bi bi-calendar-check-fill text-primary me-1"></i>{{ $masuk->waktu_jaga }}
                            </span>
                        @endif
                    </div>

                    {{-- Catatan --}}
                    @if($masuk->catatan)
                        <div class="bg-light rounded-3 p-2" style="font-size:0.82rem; color:#374151;">
                            <i class="bi bi-chat-text text-success me-1"></i>
                            {{ $masuk->catatan }}
                        </div>
                    @endif

                    {{-- FR-015: Status alat --}}
                    @if($masuk->status_alat && count($masuk->status_alat) > 0)
                        <div class="mt-2">
                            <div style="font-size:0.72rem; font-weight:600; color:#6b7280; margin-bottom:4px;">
                                <i class="bi bi-tools me-1"></i>Alat Tersedia & Baik:
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($masuk->status_alat as $alat)
                                    <span style="background:#d1fae5; color:#059669; border:1px solid #a7f3d0;
                                                 padding:2px 8px; border-radius:10px; font-size:0.7rem; font-weight:600;">
                                        ✓ {{ $alat }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif

        {{-- ======================================== --}}
        {{-- FORM KIRIM OPERAN --}}
        {{-- ======================================== --}}
        <div class="section-label"><i class="bi bi-send me-1"></i> Kirim Operan</div>
        <div class="form-card">
            <h6><i class="bi bi-arrow-left-right"></i> Serah Terima Tugas</h6>

            <form action="{{ route('operan.kirim') }}" method="POST">
                @csrf

                {{-- Pilih penerima --}}
                <div class="mb-3">
                    <label class="form-label">Rekan Penerima Operan <span class="text-danger">*</span></label>
                    <select name="penerima_id" class="form-select" required>
                        <option value="">-- Pilih Rekan --</option>
                        @foreach($daftarPenerima as $rekan)
                            <option value="{{ $rekan->id }}" {{ old('penerima_id') == $rekan->id ? 'selected' : '' }}>
                                {{ $rekan->name }}
                                @if($rekan->shift) (Shift {{ ucfirst($rekan->shift) }}) @endif
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
                        <option value="Lantai 1" {{ old('tempat_tugas') == 'Lantai 1' ? 'selected' : '' }}>Lantai 1</option>
                        <option value="Lantai 2" {{ old('tempat_tugas') == 'Lantai 2' ? 'selected' : '' }}>Lantai 2</option>
                        <option value="Lantai 3" {{ old('tempat_tugas') == 'Lantai 3' ? 'selected' : '' }}>Lantai 3</option>
                        <option value="Lantai 4" {{ old('tempat_tugas') == 'Lantai 4' ? 'selected' : '' }}>Lantai 4</option>
                        <option value="Lantai 5" {{ old('tempat_tugas') == 'Lantai 5' ? 'selected' : '' }}>Lantai 5</option>
                        <option value="Pustu Cempaka Putih Barat" {{ old('tempat_tugas') == 'Pustu Cempaka Putih Barat' ? 'selected' : '' }}>Pustu Cempaka Putih Barat</option>
                        <option value="Pustu Cempaka Putih Timur" {{ old('tempat_tugas') == 'Pustu Cempaka Putih Timur' ? 'selected' : '' }}>Pustu Cempaka Putih Timur</option>
                        <option value="Pustu Rawasari" {{ old('tempat_tugas') == 'Pustu Rawasari' ? 'selected' : '' }}>Pustu Rawasari</option>
                        <option value="Yang lain" {{ old('tempat_tugas') == 'Yang lain' ? 'selected' : '' }}>Yang lain</option>
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
                        <option value="Shift Pagi" {{ old('waktu_jaga') == 'Shift Pagi' ? 'selected' : '' }}>Shift Pagi</option>
                        <option value="Shift Siang" {{ old('waktu_jaga') == 'Shift Siang' ? 'selected' : '' }}>Shift Siang</option>
                        <option value="Shift Malam" {{ old('waktu_jaga') == 'Shift Malam' ? 'selected' : '' }}>Shift Malam</option>
                        <option value="Non Shift" {{ old('waktu_jaga') == 'Non Shift' ? 'selected' : '' }}>Non Shift</option>
                        <option value="Yang lain" {{ old('waktu_jaga') == 'Yang lain' ? 'selected' : '' }}>Yang lain</option>
                    </select>
                    @error('waktu_jaga')
                        <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                {{-- FR-015: Status Peralatan --}}
                <div class="mb-3">
                    <label class="form-label">
                        Kondisi Peralatan
                        <span class="text-secondary fw-normal" style="font-size:0.75rem;">(centang yang tersedia & baik)</span>
                    </label>
                    <div class="alat-grid">
                        @foreach($daftarAlat as $namaAlat => $ikonAlat)
                            @php
                                $oldAlat = old('status_alat', []);
                                $diceklis = in_array($namaAlat, $oldAlat);
                            @endphp
                            <label class="alat-item">
                                <input type="checkbox"
                                       name="status_alat[]"
                                       value="{{ $namaAlat }}"
                                       {{ $diceklis ? 'checked' : '' }}>
                                <i class="bi {{ $ikonAlat }} me-1" style="font-size:0.9rem;"></i>
                                {{ $namaAlat }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Catatan (Uraian Kegiatan) --}}
                <div class="mb-3">
                    <label class="form-label">Uraian Kegiatan Yang Dioper <span class="text-danger">*</span></label>
                    <textarea name="catatan" class="form-control" rows="3" required
                        placeholder="Isi point-point tugas yang akan dioperkan..."
                        maxlength="1000">{{ old('catatan') }}</textarea>
                    <div class="text-secondary text-end" style="font-size:0.72rem;" id="hitung-karakter">0/1000</div>
                    @error('catatan')
                        <div class="text-danger mt-1" style="font-size:0.78rem;">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-kirim">
                    <i class="bi bi-send me-2"></i> Kirim Operan Sekarang
                </button>
            </form>
        </div>

        {{-- ======================================== --}}
        {{-- RIWAYAT HARI INI --}}
        {{-- ======================================== --}}
        @if($operanDikirim->count() > 0 || $operanDiterima->count() > 0)
            <div class="section-label"><i class="bi bi-clock-history me-1"></i> Riwayat Hari Ini</div>

            @foreach($operanDikirim as $dikirim)
                <div class="riwayat-card">
                    <div class="avatar-circle">{{ strtoupper(substr($dikirim->penerima->name, 0, 1)) }}</div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:0.85rem;">→ {{ $dikirim->penerima->name }}</div>
                        <div class="text-secondary" style="font-size:0.75rem;">Dikirim {{ $dikirim->waktu }}</div>
                    </div>
                    <span class="status-pill status-{{ $dikirim->status_terima }}">
                        {{ $dikirim->status_terima === 'diterima' ? 'Diterima ✓' : 'Menunggu' }}
                    </span>
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
                    <span class="status-pill status-diterima">Diterima ✓</span>
                </div>
            @endforeach
        @endif

    </div>
</div>

<script>
    // Hitung karakter textarea
    const textarea = document.querySelector('textarea[name="catatan"]');
    const hitungEl = document.getElementById('hitung-karakter');
    if (textarea && hitungEl) {
        textarea.addEventListener('input', function() {
            hitungEl.textContent = this.value.length + '/1000';
        });
    }
</script>
@endsection
