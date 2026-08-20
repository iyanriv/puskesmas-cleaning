@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { font-family: 'Inter', sans-serif; background: #f4f6f9; }

    .rekap-header {
        background: linear-gradient(135deg, #059669, #10B981);
        border-radius: 20px; padding: 1.5rem;
        color: white; margin-bottom: 1.5rem;
    }
    .rekap-header h4 { font-weight: 700; margin: 0 0 0.25rem; }
    .rekap-header p  { margin: 0; opacity: 0.85; font-size: 0.85rem; }

    /* Filter bar */
    .filter-bar {
        background: white; border-radius: 16px; padding: 1rem 1.25rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 1.5rem;
        display: flex; align-items: center; flex-wrap: wrap; gap: 0.75rem;
    }
    .filter-group { display: flex; gap: 0.4rem; }
    .btn-filter {
        padding: 6px 16px; border-radius: 20px; font-size: 0.8rem; font-weight: 600;
        border: 1.5px solid #e5e7eb; background: white; color: #6b7280; cursor: pointer;
        transition: all 0.15s; text-decoration: none; display: inline-block;
    }
    .btn-filter.active { background: #10B981; border-color: #10B981; color: white; }
    .select-bulan {
        border: 1.5px solid #e5e7eb; border-radius: 20px; padding: 5px 14px;
        font-size: 0.8rem; font-weight: 600; color: #374151; background: white;
        cursor: pointer;
    }
    .select-bulan:focus { outline: none; border-color: #10B981; }

    /* Stat cards */
    .stat-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
    .stat-card {
        background: white; border-radius: 16px; padding: 1rem;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); text-align: center;
    }
    .stat-card .angka { font-size: 1.6rem; font-weight: 800; }
    .stat-card .label { font-size: 0.72rem; color: #9ca3af; margin-top: 2px; }
    .stat-card.hijau .angka { color: #059669; }
    .stat-card.biru  .angka { color: #1E40AF; }
    .stat-card.ungu  .angka { color: #7c3aed; }

    /* Card putih */
    .rekap-card {
        background: white; border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 1.5rem; overflow: hidden;
    }
    .rekap-card-header {
        padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6;
        font-weight: 700; font-size: 0.92rem; display: flex; align-items: center; gap: 0.5rem;
    }

    /* Jenis sampah bar */
    .jenis-row { padding: 0.75rem 1.25rem; border-bottom: 1px solid #f9fafb; }
    .jenis-row:last-child { border-bottom: none; }
    .jenis-nama { font-size: 0.85rem; font-weight: 600; color: #1f2937; margin-bottom: 4px; }
    .jenis-info { font-size: 0.75rem; color: #9ca3af; margin-bottom: 6px; }
    .progress-bar-wrap {
        height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden;
    }
    .progress-bar-fill {
        height: 100%; background: linear-gradient(to right, #10B981, #059669);
        border-radius: 4px; transition: width 0.4s;
    }

    /* Tabel petugas */
    .tabel { width: 100%; border-collapse: collapse; }
    .tabel th { padding: 0.65rem 1.25rem; font-size: 0.73rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em; border-bottom: 1px solid #f3f4f6; background: #fafafa; text-align: left; }
    .tabel td { padding: 0.8rem 1.25rem; font-size: 0.84rem; border-bottom: 1px solid #f9fafb; vertical-align: middle; }
    .tabel tr:last-child td { border-bottom: none; }
    .avatar-circle { width: 34px; height: 34px; border-radius: 50%; background: #d1fae5; color: #059669; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; }

    /* Detail setoran */
    .setoran-item { padding: 0.8rem 1.25rem; border-bottom: 1px solid #f9fafb; display: flex; align-items: flex-start; gap: 0.85rem; }
    .setoran-item:last-child { border-bottom: none; }
    .setoran-dot { width: 10px; height: 10px; border-radius: 50%; background: #10B981; flex-shrink: 0; margin-top: 5px; }
    .chip {
        background: #f0fdf4; color: #059669; border: 1px solid #bbf7d0;
        padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 600;
        display: inline-block; margin: 1px;
    }
    .empty-state { text-align: center; padding: 3rem 1rem; color: #9ca3af; }
    .empty-state i { font-size: 2.5rem; display: block; margin-bottom: 0.75rem; }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">

            {{-- Header --}}
            <div class="rekap-header">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4><i class="bi bi-recycle me-2"></i>Rekapan Bank Sampah</h4>
                        <p>{{ $judul }}</p>
                    </div>
                    <div class="text-end">
                        <div style="font-size: 2rem; font-weight: 800;">{{ $totalSetoran }}</div>
                        <div style="font-size: 0.8rem; opacity: 0.85;">Total Laporan</div>
                    </div>
                </div>
            </div>

            {{-- Filter --}}
            <form method="GET" action="{{ route('sampah.rekapan') }}" id="form-filter">
                <div class="filter-bar">
                    <div class="filter-group">
                        <a href="{{ route('sampah.rekapan', ['filter' => 'hari', 'bulan' => $bulan]) }}"
                           class="btn-filter {{ $filter === 'hari' ? 'active' : '' }}">Hari Ini</a>
                        <a href="{{ route('sampah.rekapan', ['filter' => 'minggu', 'bulan' => $bulan]) }}"
                           class="btn-filter {{ $filter === 'minggu' ? 'active' : '' }}">Minggu Ini</a>
                        <a href="{{ route('sampah.rekapan', ['filter' => 'bulan', 'bulan' => $bulan]) }}"
                           class="btn-filter {{ $filter === 'bulan' ? 'active' : '' }}">Bulanan</a>
                    </div>
                    @if($filter === 'bulan')
                        <select name="bulan" class="select-bulan" onchange="this.form.submit()">
                            <input type="hidden" name="filter" value="bulan">
                            @foreach($daftarBulan as $b)
                                <option value="{{ $b }}" {{ $bulan === $b ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($b . '-01')->translatedFormat('F Y') }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                    <input type="hidden" name="filter" value="{{ $filter }}">
                </div>
            </form>

            {{-- Statistik ringkas --}}
            <div class="stat-grid" style="grid-template-columns: repeat(2, 1fr);">
                <div class="stat-card biru">
                    <div class="angka">{{ $totalSetoran }}</div>
                    <div class="label">Total Laporan</div>
                </div>
                <div class="stat-card ungu">
                    <div class="angka">{{ count($rekapJenis) }}</div>
                    <div class="label">Kategori Sampah</div>
                </div>
            </div>

            @if($totalSetoran === 0)
                <div class="rekap-card">
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        Belum ada setoran pada periode ini.
                    </div>
                </div>
            @else
                <div class="row g-4">

                    {{-- Rekap per jenis --}}
                    <div class="col-md-6">
                        <div class="rekap-card">
                            <div class="rekap-card-header">
                                <i class="bi bi-bar-chart-fill text-success"></i> Rekap per Jenis Sampah
                            </div>
                            @php $maxSetor = collect($rekapJenis)->max('jumlah_setor') ?: 1; @endphp
                            @foreach($rekapJenis as $jenis => $data)
                                <div class="jenis-row">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="jenis-nama">{{ $jenis }}</span>
                                        <span class="fw-bold text-success">{{ $data['jumlah_setor'] }} laporan</span>
                                    </div>
                                    <div class="progress-bar-wrap mt-2">
                                        <div class="progress-bar-fill"
                                             style="width: {{ ($data['jumlah_setor'] / $maxSetor) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Rekap per petugas --}}
                    <div class="col-md-6">
                        <div class="rekap-card">
                            <div class="rekap-card-header">
                                <i class="bi bi-people-fill text-success"></i> Rekap per Petugas
                            </div>
                            <table class="tabel">
                                <thead>
                                    <tr>
                                        <th>Petugas</th>
                                        <th class="text-end">Jumlah Laporan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rekapPertugas as $data)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-circle">
                                                        {{ strtoupper(substr($data['nama'], 0, 1)) }}
                                                    </div>
                                                    <span class="fw-semibold">{{ $data['nama'] }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold text-success">
                                                {{ $data['jumlah_setor'] }} kali
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Detail semua setoran --}}
                    <div class="col-12">
                        <div class="rekap-card">
                            <div class="rekap-card-header">
                                <i class="bi bi-list-ul text-secondary"></i>
                                Detail Setoran ({{ $totalSetoran }} data)
                            </div>
                            @foreach($semuaSetoran as $setoran)
                                <div class="setoran-item">
                                    <div class="setoran-dot mt-1"></div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold" style="font-size:0.88rem;">
                                            {{ $setoran->pengguna->name }}
                                        </div>
                                        <div class="mt-1">
                                            @foreach((array) $setoran->jenis_sampah as $j)
                                                <span class="chip">{{ $j }}</span>
                                            @endforeach
                                        </div>
                                        <div class="text-secondary mt-1" style="font-size:0.75rem;">
                                            <i class="bi bi-geo-alt me-1"></i>{{ $setoran->lokasi_setor }}
                                            · {{ $setoran->tanggal->translatedFormat('d F Y') }}
                                        </div>
                                        @if($setoran->catatan)
                                            <div class="text-secondary mt-1" style="font-size:0.75rem; font-style:italic;">
                                                "{{ $setoran->catatan }}"
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-end" style="flex-shrink:0;">
                                        @if($setoran->foto_timbangan)
                                            <a href="{{ Storage::url($setoran->foto_timbangan) }}" target="_blank"
                                               class="btn btn-sm btn-light border" style="font-size:0.75rem;">
                                                <i class="bi bi-image me-1"></i>Lihat Foto
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
</div>
@endsection
