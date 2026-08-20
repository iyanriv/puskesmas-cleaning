@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { font-family: 'Inter', sans-serif; background: #f4f6f9; }

    .page-header {
        background: linear-gradient(135deg, #12a65a, #0d8a4a);
        border-radius: 20px; padding: 1.5rem 1.75rem;
        color: white; margin-bottom: 1.75rem;
    }
    .page-header h4 { font-weight: 700; margin: 0 0 0.2rem; }
    .page-header p  { margin: 0; opacity: 0.8; font-size: 0.85rem; }

    .petugas-card {
        background: white; border-radius: 18px;
        padding: 1rem 1.25rem; margin-bottom: 0.85rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        display: flex; align-items: center; gap: 1rem;
        transition: all 0.2s;
    }
    .petugas-card:hover { box-shadow: 0 4px 20px rgba(18,166,90,0.12); transform: translateY(-1px); }

    .avatar {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; font-weight: 800; flex-shrink: 0;
    }
    .avatar.biru { background: #eff6ff; color: #12a65a; }
    .avatar.hijau { background: #f0fdf4; color: #059669; }

    .petugas-info .nama { font-weight: 700; font-size: 0.92rem; color: #111827; }
    .petugas-info .meta { font-size: 0.75rem; color: #9ca3af; margin-top: 2px; }

    .badge-area {
        background: #eff6ff; color: #12a65a;
        padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 600;
    }
    .badge-sudah {
        background: #d1fae5; color: #059669;
        padding: 3px 10px; border-radius: 20px; font-size: 0.72rem; font-weight: 600;
    }

    .btn-nilai {
        background: #12a65a; color: white; border: none;
        border-radius: 12px; padding: 7px 16px;
        font-size: 0.8rem; font-weight: 700;
        text-decoration: none; white-space: nowrap;
        transition: all 0.2s; display: inline-block;
    }
    .btn-nilai:hover { background: #0d8a4a; color: white; }
    .btn-nilai.sudah {
        background: #f3f4f6; color: #6b7280; pointer-events: none;
    }
    .btn-lihat {
        background: none; border: 1.5px solid #e5e7eb; color: #374151;
        border-radius: 10px; padding: 5px 12px;
        font-size: 0.75rem; font-weight: 600;
        text-decoration: none; margin-left: 4px;
        transition: all 0.2s; display: inline-block;
    }
    .btn-lihat:hover { border-color: #12a65a; color: #12a65a; }

    .section-label {
        font-size: 0.75rem; font-weight: 700; color: #6b7280;
        text-transform: uppercase; letter-spacing: 0.06em;
        margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.5rem;
    }
    .bulan-info {
        background: white; border-radius: 14px; padding: 0.75rem 1.25rem;
        font-size: 0.82rem; color: #374151; margin-bottom: 1.25rem;
        box-shadow: 0 1px 6px rgba(0,0,0,0.04);
        display: flex; align-items: center; justify-content: space-between;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="page-header">
                <h4><i class="bi bi-star-fill me-2"></i>Penilaian Kinerja</h4>
                <p>Berikan penilaian untuk petugas CS — {{ now()->translatedFormat('F Y') }}</p>
            </div>

            @if(session('sukses'))
                <div class="alert alert-success rounded-3 mb-3 alert-dismissible fade show" style="font-size:0.84rem;">
                    <i class="bi bi-check-circle me-1"></i> {{ session('sukses') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info rounded-3 mb-3" style="font-size:0.84rem;">
                    <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
                </div>
            @endif

            <div class="bulan-info">
                <span><i class="bi bi-calendar3 me-2 text-success"></i>Periode Penilaian: <strong>{{ now()->translatedFormat('F Y') }}</strong></span>
                <a href="{{ route('penilaian.rekap') }}" class="text-success fw-bold" style="font-size:0.8rem;">
                    Lihat Rekap <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="section-label">
                <i class="bi bi-people-fill text-success"></i>
                Daftar Petugas ({{ $daftarPetugas->count() }})
            </div>

            @forelse($daftarPetugas as $petugas)
                @php $sudah = in_array($petugas->id, $sudahDinilai); @endphp
                <div class="petugas-card">
                    <div class="avatar {{ $sudah ? 'hijau' : 'biru' }}">
                        {{ strtoupper(substr($petugas->name, 0, 1)) }}
                    </div>
                    <div class="petugas-info flex-grow-1">
                        <div class="nama">{{ $petugas->name }}</div>
                        <div class="meta d-flex align-items-center gap-1 mt-1 flex-wrap">
                            <span class="badge-area">
                                <i class="bi bi-map me-1"></i>{{ $petugas->area?->nama_ruangan ?? 'Belum ada area' }}
                            </span>
                            <span class="text-secondary">· {{ ucfirst(str_replace('_', ' ', $petugas->peran?->nama_peran ?? '')) }}</span>
                            @if($petugas->shift)
                                <span class="text-secondary">· Shift {{ ucfirst($petugas->shift) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end" style="flex-shrink:0;">
                        @if($sudah)
                            <span class="badge-sudah d-block mb-1">✓ Sudah dinilai</span>
                        @endif
                        <a href="{{ route('penilaian.buat', $petugas->id) }}"
                           class="btn-nilai {{ $sudah ? 'sudah' : '' }}">
                            {{ $sudah ? 'Dinilai ✓' : '⭐ Nilai' }}
                        </a>
                        <a href="{{ route('penilaian.detail', $petugas->id) }}" class="btn-lihat">
                            Riwayat
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-secondary">
                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                    Tidak ada petugas CS yang terdaftar.
                </div>
            @endforelse

        </div>
    </div>
</div>
@endsection
