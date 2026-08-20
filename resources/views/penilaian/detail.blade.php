@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { font-family: 'Inter', sans-serif; background: #f4f6f9; }

    .profil-header {
        background: linear-gradient(135deg, #12a65a, #0d8a4a);
        border-radius: 20px; padding: 1.5rem; color: white; margin-bottom: 1.5rem;
    }
    .avatar-besar {
        width: 56px; height: 56px; border-radius: 16px;
        background: rgba(255,255,255,0.2); color: white;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; font-weight: 800; flex-shrink: 0;
    }

    /* Radar / aspek summary */
    .aspek-summary {
        background: white; border-radius: 18px; padding: 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 1.5rem;
    }
    .aspek-row { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem; }
    .aspek-row:last-child { margin-bottom: 0; }
    .aspek-icon { width: 32px; height: 32px; border-radius: 9px; background: #eff6ff; color: #12a65a; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; flex-shrink: 0; }
    .aspek-nama { font-size: 0.82rem; font-weight: 600; color: #374151; width: 120px; flex-shrink: 0; }
    .progress-wrap { flex: 1; height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 4px; background: linear-gradient(to right, #12a65a, #0d8a4a); }
    .aspek-nilai { font-weight: 800; font-size: 0.9rem; color: #12a65a; width: 35px; text-align: right; flex-shrink: 0; }

    /* Riwayat */
    .riwayat-card {
        background: white; border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); overflow: hidden;
    }
    .riwayat-header {
        padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6;
        font-weight: 700; font-size: 0.92rem;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .riwayat-item {
        padding: 1rem 1.25rem; border-bottom: 1px solid #f9fafb;
        display: grid; grid-template-columns: auto 1fr auto; gap: 0.85rem; align-items: start;
    }
    .riwayat-item:last-child { border-bottom: none; }

    .riwayat-tanggal {
        background: #f9fafb; border-radius: 10px; padding: 0.4rem 0.6rem;
        text-align: center; min-width: 48px;
    }
    .tgl-hari { font-size: 1.1rem; font-weight: 800; color: #111827; line-height: 1; }
    .tgl-bulan { font-size: 0.65rem; color: #9ca3af; text-transform: uppercase; }

    .bintang-fill { color: #f59e0b; }
    .bintang-empty { color: #e5e7eb; }

    .detail-aspek { display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.4rem; }
    .chip-aspek {
        font-size: 0.68rem; padding: 2px 8px; border-radius: 8px;
        background: #eff6ff; color: #12a65a; font-weight: 600;
    }

    .grade-besar {
        font-size: 1.3rem; font-weight: 800; color: #12a65a;
        display: block; text-align: right;
    }
    .grade-label {
        font-size: 0.7rem; color: #9ca3af; text-align: right; display: block;
    }

    .kosong { text-align: center; padding: 3rem; color: #9ca3af; }
    .kosong i { font-size: 2rem; display: block; margin-bottom: 0.75rem; }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            {{-- Header profil --}}
            <div class="profil-header">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <a href="{{ route('penilaian.index') }}" class="text-white" style="font-size:1.1rem;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div class="avatar-besar">{{ strtoupper(substr($dinilai->name, 0, 1)) }}</div>
                    <div>
                        <div class="fw-bold" style="font-size:1rem;">{{ $dinilai->name }}</div>
                        <div style="opacity:0.75; font-size:0.8rem;">
                            {{ $dinilai->area?->nama_ruangan ?? '-' }}
                            @if($dinilai->shift) · Shift {{ ucfirst($dinilai->shift) }} @endif
                        </div>
                    </div>
                </div>

                {{-- Ringkasan statistik --}}
                <div class="d-flex gap-3">
                    <div class="text-center">
                        <div style="font-size:1.4rem; font-weight:800;">{{ $penilaian->count() }}</div>
                        <div style="font-size:0.72rem; opacity:0.75;">Total Penilaian</div>
                    </div>
                    @if($penilaian->count() > 0)
                        @php
                            $grandAvg = round(collect($rataAspek)->avg(), 1);
                        @endphp
                        <div class="text-center">
                            <div style="font-size:1.4rem; font-weight:800;">⭐ {{ $grandAvg }}</div>
                            <div style="font-size:0.72rem; opacity:0.75;">Rata-rata Keseluruhan</div>
                        </div>
                        <div class="text-center">
                            <div style="font-size:1.4rem; font-weight:800;">
                                {{ match(true) { $grandAvg >= 4.5 => '🏆', $grandAvg >= 3.5 => '👍', $grandAvg >= 2.5 => '😐', default => '⚠️' } }}
                            </div>
                            <div style="font-size:0.72rem; opacity:0.75;">
                                {{ match(true) { $grandAvg >= 4.5 => 'Sangat Baik', $grandAvg >= 3.5 => 'Baik', $grandAvg >= 2.5 => 'Cukup', default => 'Perlu Perhatian' } }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Rata-rata per aspek --}}
            @if($penilaian->count() > 0)
            <div class="aspek-summary mb-4">
                <div class="fw-bold mb-3" style="font-size:0.88rem;">Rata-rata per Aspek</div>
                @php
                    $aspekConfig = [
                        'kebersihan'   => ['icon' => 'bi-stars',     'label' => 'Kebersihan'],
                        'kedisiplinan' => ['icon' => 'bi-clock',     'label' => 'Kedisiplinan'],
                        'kerjasama'    => ['icon' => 'bi-people',    'label' => 'Kerja Sama'],
                        'inisiatif'    => ['icon' => 'bi-lightning', 'label' => 'Inisiatif'],
                    ];
                @endphp
                @foreach($aspekConfig as $key => $conf)
                    <div class="aspek-row">
                        <div class="aspek-icon"><i class="bi {{ $conf['icon'] }}"></i></div>
                        <div class="aspek-nama">{{ $conf['label'] }}</div>
                        <div class="progress-wrap">
                            <div class="progress-fill" style="width:{{ ($rataAspek[$key]/5)*100 }}%"></div>
                        </div>
                        <div class="aspek-nilai">{{ $rataAspek[$key] }}</div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Riwayat penilaian --}}
            <div class="riwayat-card">
                <div class="riwayat-header">
                    <i class="bi bi-clock-history text-success"></i>
                    Riwayat Penilaian ({{ $penilaian->count() }})
                </div>

                @forelse($penilaian as $p)
                    <div class="riwayat-item">
                        <div class="riwayat-tanggal">
                            <div class="tgl-hari">{{ $p->tanggal->format('d') }}</div>
                            <div class="tgl-bulan">{{ $p->tanggal->format('M') }}</div>
                        </div>

                        <div>
                            <div class="fw-semibold" style="font-size:0.85rem;">
                                oleh {{ $p->penilai->name }}
                            </div>
                            <div class="detail-aspek mt-1">
                                <span class="chip-aspek">🧹 Kebersihan: {{ $p->nilai_kebersihan }}/5</span>
                                <span class="chip-aspek">⏰ Disiplin: {{ $p->nilai_kedisiplinan }}/5</span>
                                <span class="chip-aspek">🤝 Kerja Sama: {{ $p->nilai_kerjasama }}/5</span>
                                <span class="chip-aspek">⚡ Inisiatif: {{ $p->nilai_inisiatif }}/5</span>
                            </div>
                            @if($p->catatan)
                                <div class="text-secondary mt-1" style="font-size:0.75rem; font-style:italic;">
                                    "{{ $p->catatan }}"
                                </div>
                            @endif
                        </div>

                        <div>
                            <span class="grade-besar">{{ $p->rataRata() }}</span>
                            <span class="grade-label">{{ $p->grade() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="kosong">
                        <i class="bi bi-clipboard-x"></i>
                        Belum ada penilaian untuk petugas ini.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection
