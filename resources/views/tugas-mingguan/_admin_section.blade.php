    <div class="container-fluid px-3 px-lg-4 py-4">

        {{-- ================================================================
             HEADER
             ================================================================ --}}
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-calendar-check-fill text-success me-2"></i>Tugas Mingguan CS
                </h4>
                <p class="text-muted mb-0" style="font-size:0.84rem;">
                    PUSKESMAS CEMPAKA PUTIH &mdash;
                    <span class="fw-semibold text-dark">{{ strtoupper(now()->translatedFormat('d F Y')) }}</span>
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @if(in_array(auth()->user()->peran?->nama_peran, ['admin', 'supervisor']))
                    <a href="{{ route('tugas-mingguan.export-excel', array_filter([
                            'petugas_id' => request('petugas_id'),
                            'status'     => request('status'),
                            'dari'       => request('dari'),
                            'sampai'     => request('sampai'),
                        ])) }}"
                       class="btn btn-outline-success btn-sm rounded-3 px-3">
                        <i class="bi bi-file-earmark-excel me-1"></i> Excel
                    </a>
                    <a href="{{ route('tugas-mingguan.cetak-laporan', array_filter([
                            'petugas_id' => request('petugas_id'),
                            'status'     => request('status'),
                        ])) }}"
                       target="_blank" class="btn btn-outline-danger btn-sm rounded-3 px-3">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Cetak PDF
                    </a>
                @endif
            </div>
        </div>

        {{-- ================================================================
             FLASH ALERT
             ================================================================ --}}
        @if(session('sukses'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('gagal'))
            <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('gagal') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ================================================================
             PANEL FILTER
             ================================================================ --}}
        @if($daftarCs->count() > 0)
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <form action="{{ route('tugas-mingguan.index') }}" method="GET">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                                <i class="bi bi-person text-success me-1"></i>Petugas CS
                            </label>
                            <select name="petugas_id" class="form-select form-select-sm rounded-3">
                                <option value="">— Semua Petugas —</option>
                                @foreach($daftarCs as $cs)
                                    <option value="{{ $cs->id }}" {{ request('petugas_id') == $cs->id ? 'selected' : '' }}>
                                        {{ $cs->name }} ({{ $cs->nik }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                                <i class="bi bi-check-circle text-success me-1"></i>Status Tugas
                            </label>
                            <select name="status" class="form-select form-select-sm rounded-3">
                                <option value="">— Semua Status —</option>
                                <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Dalam Proses</option>
                                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            </select>
                        </div>

                        <div class="col-auto">
                            <button type="submit" class="btn btn-success btn-sm rounded-3 px-3">
                                <i class="bi bi-funnel me-1"></i> Tampilkan
                            </button>
                        </div>

                        @if(request()->hasAny(['petugas_id', 'status']))
                        <div class="col-auto">
                            <a href="{{ route('tugas-mingguan.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                                <i class="bi bi-x-circle me-1"></i> Reset Filter
                            </a>
                        </div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- ================================================================
             STATISTIK RINGKAS
             ================================================================ --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-primary-subtle"><i class="bi bi-clipboard2-check text-primary fs-5"></i></div>
                        <div>
                            <div class="fw-bold fs-5 text-dark">{{ $totalTugas }}</div>
                            <div class="text-muted" style="font-size:0.78rem;">Total Tugas Mingguan</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-success-subtle"><i class="bi bi-check2-circle text-success fs-5"></i></div>
                        <div>
                            <div class="fw-bold fs-5 text-dark">{{ $tugasSelesai }}</div>
                            <div class="text-muted" style="font-size:0.78rem;">Tugas Selesai / Terverifikasi</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-3 d-flex align-items-center gap-3">
                        <div class="rounded-3 p-2 bg-warning-subtle"><i class="bi bi-hourglass-split text-warning fs-5"></i></div>
                        <div>
                            <div class="fw-bold fs-5 text-dark">{{ $tugasProses }}</div>
                            <div class="text-muted" style="font-size:0.78rem;">Dalam Proses / Menunggu</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================================
             TABEL UTAMA TUGAS MINGGUAN
             ================================================================ --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                <h6 class="fw-bold mb-0 text-success">
                    <i class="bi bi-table me-2"></i>DAFTAR TUGAS MINGGUAN CS
                </h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                    {{ $daftarTugas->total() }} tugas
                </span>
            </div>
            <div class="card-body p-0">
                @if($daftarTugas->count() > 0)
                <div class="table-responsive table-scroll-container">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3 text-center" style="width:45px;">No</th>
                                <th>Petugas CS</th>
                                <th style="width:130px;">Tanggal & Waktu</th>
                                <th>Rincian Kegiatan</th>
                                <th style="width:170px;">Dokumentasi</th>
                                <th class="text-center" style="width:130px;">Status</th>
                                <th class="text-center" style="width:130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daftarTugas as $index => $tugas)
                                @php
                                    $jumlahSebelum = is_array($tugas->foto_sebelum) ? count($tugas->foto_sebelum) : 0;
                                @endphp
                                <tr>
                                    <td class="ps-3 text-center text-muted">{{ $daftarTugas->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                                 style="width:34px; height:34px; background:#e6f4f5; color:#267A7E; font-size:0.85rem;">
                                                {{ strtoupper(substr($tugas->user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $tugas->user->name }}</div>
                                                <div class="text-muted" style="font-size:0.75rem;">Nomor Karyawan: {{ $tugas->user->nik ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $tugas->tanggal->translatedFormat('d M Y') }}</div>
                                        <div class="text-muted" style="font-size:0.75rem;">{{ \Carbon\Carbon::parse($tugas->waktu_pelaporan)->format('H:i') }} WIB</div>
                                    </td>
                                    <td>
                                        <div class="text-dark" style="line-height:1.5;">
                                            {{ Str::limit($tugas->rincian_kegiatan, 90) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-light text-secondary border rounded-pill px-2 py-1" style="font-size:0.72rem; width:fit-content;">
                                                <i class="bi bi-camera me-1"></i>{{ $jumlahSebelum }} Foto Sebelum
                                            </span>
                                            @if($tugas->foto_setelah)
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size:0.72rem; width:fit-content;">
                                                    <i class="bi bi-check-circle me-1"></i>Foto Selesai
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1" style="font-size:0.72rem; width:fit-content;">
                                                    <i class="bi bi-hourglass me-1"></i>Belum Upload
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">{!! $tugas->badgeStatus() !!}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('tugas-mingguan.detail', $tugas->id) }}"
                                               class="btn btn-sm btn-outline-success rounded-2 px-2"
                                               title="Lihat Detail">
                                                <i class="bi bi-eye" style="font-size:0.8rem;"></i> Detail
                                            </a>
                                            @if(in_array(auth()->user()->peran?->nama_peran ?? '', ['supervisor', 'admin']) && $tugas->status !== 'disetujui' && $tugas->foto_setelah)
                                                <form action="{{ route('tugas-mingguan.verifikasi', $tugas->id) }}" method="POST" class="d-inline"
                                                      onsubmit="return confirm('Verifikasi & setujui tugas dari {{ addslashes($tugas->user->name) }}?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="disetujui">
                                                    <input type="hidden" name="catatan_supervisor" value="Disetujui via monitoring.">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-2 px-2" title="Setujui Langsung">
                                                        <i class="bi bi-check-lg" style="font-size:0.8rem;"></i> Setujui
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                    Belum ada laporan tugas mingguan yang tersimpan dalam sistem.
                </div>
                @endif
            </div>

            @if($daftarTugas->hasPages())
                <div class="p-3 border-top d-flex justify-content-end">
                    {{ $daftarTugas->withQueryString()->links() }}
                </div>
            @endif
        </div>

    </div>
