@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f7f9fa; font-family: 'Inter', sans-serif; }
    .mobile-container {
        max-width: 100%; margin: 0 auto;
        background: #f4f6f9; min-height: 100vh;
        padding-bottom: 40px;
    }
    .header-bar {
        
        background: linear-gradient(135deg, #12a65a, #0d8a4a);
        padding: 1.5rem 1.25rem 2.5rem 1.25rem; color: white;
        border-radius: 0 0 28px 28px;
    }
    .content-area { padding: 1.25rem; margin-top: -1.5rem; }

    /* Profil petugas */
    .profil-card {
        background: white; border-radius: 20px;
        padding: 1.25rem; text-align: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07); margin-bottom: 1.25rem;
    }
    .avatar-besar {
        width: 70px; height: 70px; border-radius: 20px;
        background: #eff6ff; color: #12a65a;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem; font-weight: 800;
        margin: 0 auto 0.75rem;
    }

    /* Rating bintang */
    .aspek-card {
        background: white; border-radius: 20px;
        padding: 1.25rem; margin-bottom: 1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    .aspek-card h6 { font-weight: 700; font-size: 0.88rem; color: #111827; margin-bottom: 1rem; }

    .aspek-item { margin-bottom: 1.1rem; }
    .aspek-item:last-child { margin-bottom: 0; }
    .aspek-label {
        font-size: 0.8rem; font-weight: 600; color: #374151;
        display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem;
    }
    .aspek-label i { color: #12a65a; }

    /* Bintang interaktif */
    .bintang-group { display: flex; gap: 0.4rem; }
    .bintang-label {
        cursor: pointer; font-size: 1.8rem; color: #e5e7eb;
        transition: all 0.1s; line-height: 1;
        user-select: none;
    }
    .bintang-label:hover,
    .bintang-label.aktif { color: #f59e0b; transform: scale(1.1); }
    .bintang-input { display: none; }
    .deskripsi-nilai {
        font-size: 0.72rem; color: #9ca3af; margin-top: 0.35rem;
        min-height: 1rem;
    }

    /* Textarea catatan */
    .textarea-catatan {
        width: 100%; border: 1.5px solid #e5e7eb; border-radius: 14px;
        padding: 0.75rem 1rem; font-size: 0.85rem; resize: none;
        transition: border-color 0.2s;
    }
    .textarea-catatan:focus { outline: none; border-color: #12a65a; box-shadow: 0 0 0 3px rgba(18,166,90,0.08); }

    /* Tombol simpan */
    .btn-simpan {
        width: 100%; background: #12a65a; color: white; border: none;
        border-radius: 16px; padding: 0.95rem;
        font-size: 0.95rem; font-weight: 700;
        box-shadow: 0 4px 16px rgba(18,166,90,0.3);
        transition: all 0.2s;
    }
    .btn-simpan:active { transform: scale(0.98); }

    /* Alert sudah dinilai */
    .card-sudah {
        background: #d1fae5; border-radius: 16px; padding: 1rem 1.25rem;
        display: flex; align-items: center; gap: 0.75rem;
        margin-bottom: 1rem;
    }

    /* Riwayat */
    .riwayat-mini {
        background: #f9fafb; border-radius: 14px; padding: 0.75rem 1rem;
        margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.75rem;
    }
    .bintang-kecil { color: #f59e0b; font-size: 0.75rem; }
    @media (min-width: 992px) { .content-area { max-width: 700px; margin: 0 auto; } }
</style>

<div class="mobile-container">
    <div class="header-bar">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('penilaian.index') }}" class="text-white" style="font-size:1.2rem;">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div>
                <h5 class="fw-bold mb-0">Form Penilaian</h5>
                <p class="mb-0 text-white-50" style="font-size:0.8rem;">{{ now()->translatedFormat('F Y') }}</p>
            </div>
        </div>
    </div>

    <div class="content-area">

        {{-- Profil yang dinilai --}}
        <div class="profil-card">
            <div class="avatar-besar">{{ strtoupper(substr($dinilai->name, 0, 1)) }}</div>
            <div class="fw-bold" style="font-size:1rem;">{{ $dinilai->name }}</div>
            <div class="text-secondary mt-1" style="font-size:0.8rem;">
                {{ $dinilai->area?->nama_ruangan ?? 'Belum ada area' }}
                @if($dinilai->shift) · Shift {{ ucfirst($dinilai->shift) }} @endif
            </div>
        </div>

        {{-- Sudah dinilai bulan ini --}}
        @if($sudah)
            <div class="card-sudah">
                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                <div>
                    <div class="fw-bold" style="font-size:0.88rem;">Sudah Dinilai Bulan Ini</div>
                    <div class="text-secondary" style="font-size:0.75rem;">
                        Rata-rata: ⭐ {{ $sudah->rataRata() }}/5 · {{ $sudah->grade() }}
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Penilaian --}}
        @if(!$sudah)
        <form action="{{ route('penilaian.simpan', $dinilai->id) }}" method="POST" id="form-nilai">
            @csrf
            <div class="aspek-card">
                <h6><i class="bi bi-star-fill text-warning me-1"></i> Berikan Penilaian</h6>

                @php
                    $aspekList = [
                        ['key' => 'kebersihan',   'label' => 'Kebersihan Area',      'icon' => 'bi-stars',      'deskripsi' => ['Sangat kotor','Kurang bersih','Cukup bersih','Bersih','Sangat bersih & rapi']],
                        ['key' => 'kedisiplinan', 'label' => 'Kedisiplinan',         'icon' => 'bi-clock',      'deskripsi' => ['Sangat tidak disiplin','Kurang disiplin','Cukup disiplin','Disiplin','Sangat disiplin']],
                        ['key' => 'kerjasama',    'label' => 'Kerjasama Tim',        'icon' => 'bi-people',     'deskripsi' => ['Tidak kooperatif','Kurang kooperatif','Cukup kooperatif','Kooperatif','Sangat kooperatif']],
                        ['key' => 'inisiatif',    'label' => 'Inisiatif & Tanggap',  'icon' => 'bi-lightning',  'deskripsi' => ['Tidak ada inisiatif','Kurang inisiatif','Cukup inisiatif','Inisiatif baik','Sangat inisiatif']],
                    ];
                @endphp

                @foreach($aspekList as $aspek)
                    <div class="aspek-item">
                        <div class="aspek-label">
                            <i class="bi {{ $aspek['icon'] }}"></i>
                            {{ $aspek['label'] }}
                        </div>
                        <div class="bintang-group" id="grup-{{ $aspek['key'] }}">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="bintang-label"
                                       data-aspek="{{ $aspek['key'] }}"
                                       data-nilai="{{ $i }}"
                                       data-deskripsi="{{ $aspek['deskripsi'][$i-1] }}"
                                       onclick="pilihBintang('{{ $aspek['key'] }}', {{ $i }}, this)">
                                    ★
                                    <input type="radio" class="bintang-input"
                                           name="nilai_{{ $aspek['key'] }}"
                                           value="{{ $i }}"
                                           {{ old('nilai_' . $aspek['key']) == $i ? 'checked' : '' }}>
                                </label>
                            @endfor
                        </div>
                        <div class="deskripsi-nilai" id="deskripsi-{{ $aspek['key'] }}">
                            Sentuh bintang untuk memberi nilai
                        </div>
                        @error('nilai_' . $aspek['key'])
                            <div class="text-danger" style="font-size:0.75rem;">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="aspek-card">
                <h6><i class="bi bi-chat-text me-1 text-success"></i> Catatan Tambahan <span class="fw-normal text-secondary">(opsional)</span></h6>
                <textarea name="catatan" class="textarea-catatan" rows="3"
                    placeholder="Tuliskan catatan, saran, atau apresiasi untuk petugas ini..."
                    maxlength="1000">{{ old('catatan') }}</textarea>
            </div>

            <button type="submit" class="btn-simpan" id="btn-simpan">
                <i class="bi bi-check-circle me-2"></i> Simpan Penilaian
            </button>
        </form>
        @endif

        {{-- Riwayat penilaian sebelumnya --}}
        @if($riwayat->count() > 0)
            <div class="mt-4">
                <div style="font-size:0.78rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.6rem;">
                    Riwayat Penilaian Sebelumnya
                </div>
                @foreach($riwayat as $r)
                    <div class="riwayat-mini">
                        <div style="font-size:1.2rem;">⭐</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size:0.83rem;">
                                Rata-rata: {{ $r->rataRata() }}/5 — <span class="text-success">{{ $r->grade() }}</span>
                            </div>
                            <div class="text-secondary" style="font-size:0.73rem;">
                                {{ $r->tanggal->translatedFormat('d F Y') }} · oleh {{ $r->penilai->name }}
                            </div>
                        </div>
                        <div>
                            <span class="bintang-kecil">
                                @for($i=0; $i < round($r->rataRata()); $i++) ★ @endfor
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</div>

<script>
    const deskripsiMap = {};

    function pilihBintang(aspek, nilai, elLabel) {
        const grup = document.getElementById('grup-' + aspek);
        const labels = grup.querySelectorAll('.bintang-label');

        // Aktifkan bintang 1 s/d nilai yang diklik
        labels.forEach((lbl, idx) => {
            lbl.classList.toggle('aktif', idx < nilai);
            const radio = lbl.querySelector('input[type="radio"]');
            if (radio) radio.checked = (idx + 1 === nilai);
        });

        // Tampilkan deskripsi
        const deskrip = document.getElementById('deskripsi-' + aspek);
        if (deskrip) deskrip.textContent = '⭐ ' + nilai + '/5 — ' + elLabel.getAttribute('data-deskripsi');
    }

    // Restore dari old() jika ada
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.bintang-input:checked').forEach(function(radio) {
            const label = radio.closest('.bintang-label');
            if (label) {
                const aspek = label.getAttribute('data-aspek');
                const nilai = parseInt(label.getAttribute('data-nilai'));
                pilihBintang(aspek, nilai, label);
            }
        });
    });

    // Submit guard
    const formNilai = document.getElementById('form-nilai');
    if (formNilai) {
        formNilai.addEventListener('submit', function() {
            const btn = document.getElementById('btn-simpan');
            if (btn) { btn.disabled = true; btn.textContent = 'Menyimpan...'; }
        });
    }
</script>
@endsection
