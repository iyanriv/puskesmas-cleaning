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
    .setoran-dot.menunggu { background: #f59e0b; }
    .setoran-dot.ditolak  { background: #ef4444; }
    .chip {
        background: #f0fdf4; color: #059669; border: 1px solid #bbf7d0;
        padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 600;
        display: inline-block; margin: 1px;
    }
    .badge-validasi {
        padding: 3px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 700;
        display: inline-flex; align-items: center; gap: 3px; white-space: nowrap;
    }
    .badge-validasi.menunggu { background: #fef3c7; color: #92400e; }
    .badge-validasi.valid    { background: #d1fae5; color: #065f46; }
    .badge-validasi.ditolak  { background: #fee2e2; color: #991b1b; }
    /* Modal tolak */
    .modal-tolak .modal-content { border-radius: 20px; border: none; }
    .modal-tolak .modal-header  { border-bottom: 1px solid #f3f4f6; padding: 1rem 1.25rem; }
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
            <div class="stat-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="stat-card biru">
                    <div class="angka">{{ $totalSetoran }}</div>
                    <div class="label">Total Laporan</div>
                </div>
                <div class="stat-card hijau">
                    <div class="angka">{{ $totalValid }}</div>
                    <div class="label">Sudah Divalidasi</div>
                </div>
                <div class="stat-card ungu">
                    <div class="angka">{{ $totalMenunggu }}</div>
                    <div class="label">Menunggu Validasi</div>
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

                            {{-- Alert flash --}}
                            @if(session('sukses'))
                                <div class="mx-3 mt-3 p-2 rounded-3 d-flex align-items-center gap-2"
                                     style="background:#d1fae5; color:#065f46; font-size:0.82rem;">
                                    <i class="bi bi-check-circle-fill"></i> {{ session('sukses') }}
                                </div>
                            @endif

                            @foreach($semuaSetoran as $setoran)
                                <div class="setoran-item">
                                    @php $sv = $setoran->status_validasi ?? 'menunggu'; @endphp
                                    <div class="setoran-dot {{ $sv }} mt-1"></div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="fw-semibold" style="font-size:0.88rem;">
                                                {{ $setoran->pengguna->name }}
                                            </span>
                                            <span class="badge-validasi {{ $sv }}">
                                                @if($sv === 'valid')    <i class="bi bi-check-circle-fill"></i> Tervalidasi
                                                @elseif($sv === 'ditolak') <i class="bi bi-x-circle-fill"></i> Ditolak
                                                @else <i class="bi bi-hourglass-split"></i> Menunggu
                                                @endif
                                            </span>
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
                                        @if($sv === 'ditolak' && $setoran->catatan_validasi)
                                            <div class="mt-1" style="font-size:0.75rem; color:#991b1b;">
                                                <i class="bi bi-chat-square-text me-1"></i>Alasan: {{ $setoran->catatan_validasi }}
                                            </div>
                                        @endif
                                        @if($sv === 'valid' && $setoran->validator)
                                            <div class="mt-1" style="font-size:0.72rem; color:#6b7280;">
                                                <i class="bi bi-person-check me-1"></i>Divalidasi oleh {{ $setoran->validator->name }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1" style="flex-shrink:0;">
                                        @if($setoran->foto_timbangan)
                                            <a href="{{ Storage::url($setoran->foto_timbangan) }}" target="_blank"
                                               class="btn btn-sm btn-light border" style="font-size:0.73rem;">
                                                <i class="bi bi-image me-1"></i>Foto
                                            </a>
                                        @endif
                                        {{-- Tombol validasi hanya untuk supervisor saat status menunggu --}}
                                        @if($sv === 'menunggu' && in_array(auth()->user()->peran?->nama_peran, ['supervisor','pj_lantai','admin']))
                                            <form action="{{ route('sampah.validasi', $setoran->id) }}" method="POST" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm"
                                                    style="background:#10B981;color:white;font-size:0.73rem;border-radius:8px;padding:3px 10px;"
                                                    onclick="return confirm('Validasi setoran ini?')">
                                                    <i class="bi bi-check-lg"></i> Validasi
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm"
                                                style="background:#fee2e2;color:#991b1b;font-size:0.73rem;border-radius:8px;padding:3px 10px;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalTolak{{ $setoran->id }}">
                                                <i class="bi bi-x-lg"></i> Tolak
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Modal Tolak --}}
                                @if($sv === 'menunggu' && in_array(auth()->user()->peran?->nama_peran, ['supervisor','pj_lantai','admin']))
                                    <div class="modal fade modal-tolak" id="modalTolak{{ $setoran->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold">
                                                        <i class="bi bi-x-circle text-danger me-1"></i>Tolak Setoran Sampah
                                                    </h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('sampah.tolak', $setoran->id) }}" method="POST">
                                                    @csrf @method('PATCH')
                                                    <div class="modal-body">
                                                        <p style="font-size:0.84rem; color:#374151;">
                                                            Setoran dari <strong>{{ $setoran->pengguna->name }}</strong> —
                                                            {{ $setoran->jenisSampahTeks() }}
                                                        </p>
                                                        <label class="form-label fw-semibold" style="font-size:0.82rem;">
                                                            Alasan Penolakan <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea name="catatan_validasi" class="form-control" rows="3"
                                                            style="border-radius:12px; font-size:0.84rem; resize:none;"
                                                            placeholder="Tuliskan alasan penolakan..."></textarea>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0">
                                                        <button type="button" class="btn btn-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger rounded-pill">
                                                            <i class="bi bi-x-circle me-1"></i> Tolak Setoran
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                </div>
            @endif

        </div>
    </div>
</div>
@endsection
