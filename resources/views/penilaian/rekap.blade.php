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

    /* Filter bulan */
    .filter-bar {
        background: white; border-radius: 14px; padding: 0.85rem 1.25rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 1.5rem;
        display: flex; align-items: center; gap: 1rem;
    }
    .select-bulan {
        border: 1.5px solid #e5e7eb; border-radius: 20px; padding: 5px 14px;
        font-size: 0.82rem; font-weight: 600; background: white; color: #374151;
    }
    .select-bulan:focus { outline: none; border-color: #12a65a; }

    /* Tabel rekap */
    .rekap-card {
        background: white; border-radius: 18px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .rekap-card-header {
        padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6;
        font-weight: 700; font-size: 0.92rem; color: #111827;
        display: flex; align-items: center; gap: 0.5rem;
    }
    .tabel { width: 100%; border-collapse: collapse; }
    .tabel thead tr { background: #f9fafb; }
    .tabel th {
        padding: 0.6rem 1rem; font-size: 0.72rem; font-weight: 700;
        color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em;
        text-align: left; border-bottom: 1px solid #f3f4f6;
    }
    .tabel td { padding: 0.9rem 1rem; font-size: 0.84rem; border-bottom: 1px solid #f9fafb; vertical-align: middle; }
    .tabel tr:last-child td { border-bottom: none; }
    .tabel tr:hover td { background: #fafafa; }

    .peringkat { width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.82rem; flex-shrink: 0; }
    .peringkat-1 { background: #fef3c7; color: #d97706; }
    .peringkat-2 { background: #f3f4f6; color: #6b7280; }
    .peringkat-3 { background: #fef3c7; color: #92400e; }
    .peringkat-n { background: #f9fafb; color: #9ca3af; }

    .mini-bar { height: 6px; background: #f3f4f6; border-radius: 3px; width: 60px; overflow: hidden; display: inline-block; vertical-align: middle; margin-left: 4px; }
    .mini-bar-fill { height: 100%; border-radius: 3px; background: #12a65a; }

    .grade-pill { padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700; }
    .grade-sangat-baik { background: #d1fae5; color: #059669; }
    .grade-baik        { background: #dbeafe; color: #12a65a; }
    .grade-cukup       { background: #fef3c7; color: #d97706; }
    .grade-kurang      { background: #fee2e2; color: #dc2626; }

    .bintang { color: #f59e0b; font-size: 0.8rem; }

    .btn-detail {
        background: none; border: 1.5px solid #e5e7eb; color: #374151;
        border-radius: 10px; padding: 4px 10px; font-size: 0.75rem; font-weight: 600;
        text-decoration: none; transition: all 0.15s;
    }
    .btn-detail:hover { border-color: #12a65a; color: #12a65a; }

    .kosong { text-align: center; padding: 3rem; color: #9ca3af; }
    .kosong i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="page-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4><i class="bi bi-bar-chart-fill me-2"></i>Rekap Penilaian Kinerja</h4>
                        <p style="margin:0; opacity:0.8; font-size:0.85rem;">
                            {{ \Carbon\Carbon::parse($bulan . '-01')->translatedFormat('F Y') }}
                        </p>
                    </div>
                    <a href="{{ route('penilaian.index') }}" class="btn btn-sm btn-light rounded-pill px-3">
                        ← Kembali
                    </a>
                </div>
            </div>

            {{-- Filter Bulan --}}
            <form method="GET" action="{{ route('penilaian.rekap') }}" class="filter-bar">
                <label class="fw-semibold" style="font-size:0.82rem; color:#374151;">Periode:</label>
                <select name="bulan" class="select-bulan" onchange="this.form.submit()">
                    @foreach($daftarBulan as $b)
                        <option value="{{ $b }}" {{ $bulan === $b ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::parse($b . '-01')->translatedFormat('F Y') }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if($rekapPerPetugas->isEmpty())
                <div class="rekap-card">
                    <div class="kosong">
                        <i class="bi bi-inbox"></i>
                        Belum ada penilaian pada periode ini.
                    </div>
                </div>
            @else
                <div class="rekap-card">
                    <div class="rekap-card-header">
                        <i class="bi bi-trophy-fill text-warning"></i>
                        Peringkat Kinerja — {{ $rekapPerPetugas->count() }} Petugas
                    </div>
                    <div class="table-responsive">
                        <table class="tabel">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Petugas</th>
                                    <th class="text-center">Kebersihan</th>
                                    <th class="text-center">Disiplin</th>
                                    <th class="text-center">Kerja Sama</th>
                                    <th class="text-center">Inisiatif</th>
                                    <th class="text-center">Rata-rata</th>
                                    <th>Grade</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapPerPetugas as $idx => $data)
                                    @php
                                        $rank   = $loop->iteration;
                                        $rankClass = match($rank) { 1 => 'peringkat-1', 2 => 'peringkat-2', 3 => 'peringkat-3', default => 'peringkat-n' };
                                        $gradeClass = match(true) {
                                            $data['rata_rata'] >= 4.5 => 'grade-sangat-baik',
                                            $data['rata_rata'] >= 3.5 => 'grade-baik',
                                            $data['rata_rata'] >= 2.5 => 'grade-cukup',
                                            default                    => 'grade-kurang',
                                        };
                                        $gradeLabel = match(true) {
                                            $data['rata_rata'] >= 4.5 => 'Sangat Baik',
                                            $data['rata_rata'] >= 3.5 => 'Baik',
                                            $data['rata_rata'] >= 2.5 => 'Cukup',
                                            default                    => 'Kurang',
                                        };
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="peringkat {{ $rankClass }}">{{ $rank }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $data['nama'] }}</div>
                                            <div class="text-secondary" style="font-size:0.73rem;">{{ $data['area'] }}</div>
                                        </td>
                                        @foreach(['kebersihan','kedisiplinan','kerjasama','inisiatif'] as $aspek)
                                            <td class="text-center">
                                                <span class="bintang">★</span>
                                                <span class="fw-bold">{{ $data[$aspek] }}</span>
                                                <div class="mini-bar">
                                                    <div class="mini-bar-fill" style="width:{{ ($data[$aspek]/5)*100 }}%"></div>
                                                </div>
                                            </td>
                                        @endforeach
                                        <td class="text-center">
                                            <span class="fw-bold fs-6">{{ $data['rata_rata'] }}</span>
                                            <span class="text-secondary">/5</span>
                                        </td>
                                        <td>
                                            <span class="grade-pill {{ $gradeClass }}">{{ $gradeLabel }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $userId = $rekapPerPetugas->keys()[$loop->index];
                                            @endphp
                                            <a href="{{ route('penilaian.detail', $userId) }}" class="btn-detail">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
