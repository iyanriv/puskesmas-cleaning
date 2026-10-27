@extends('tata-letak.aplikasi')

@section('title', 'Detail Operan Shift')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-arrow-left-right text-success me-2"></i>Detail Operan Shift
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">{{ $operan->tanggal->translatedFormat('d F Y') }}</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('operan.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Kolom Kiri: Info Utama --}}
        <div class="col-lg-8">

            {{-- Info Serah Terima --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-person-check me-2"></i>INFORMASI SERAH TERIMA
                    </h6>
                    <div class="d-flex gap-2 align-items-center">
                        @if($operan->status_terima === 'menunggu')
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                <i class="bi bi-hourglass-split me-1"></i> Menunggu
                            </span>
                        @else
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size:0.75rem;">
                                <i class="bi bi-check-circle-fill me-1"></i> Diterima
                            </span>
                        @endif
                        @if(!empty($operan->tugas_items))
                            {!! $operan->badgePenyelesaian() !!}
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                        <tbody>
                            <tr>
                                <td class="ps-4 text-muted" style="width:180px;">
                                    <i class="bi bi-person-fill text-success me-2"></i>Pengirim
                                </td>
                                <td class="fw-semibold text-dark">{{ $operan->pengirim->name }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted">
                                    <i class="bi bi-person-check-fill text-success me-2"></i>Penerima
                                </td>
                                <td class="fw-semibold text-dark">{{ $operan->penerima->name }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted">
                                    <i class="bi bi-geo-alt-fill text-success me-2"></i>Tempat Tugas
                                </td>
                                <td class="fw-semibold text-dark">{{ $operan->tempat_tugas ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted">
                                    <i class="bi bi-calendar-check text-success me-2"></i>Waktu Jaga
                                </td>
                                <td class="fw-semibold text-dark">{{ $operan->waktu_jaga ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted">
                                    <i class="bi bi-clock-fill text-success me-2"></i>Waktu Kirim
                                </td>
                                <td class="fw-semibold text-dark">{{ $operan->waktu }}</td>
                            </tr>
                            @if($operan->dibaca_pada)
                            <tr>
                                <td class="ps-4 text-muted">
                                    <i class="bi bi-eye-fill text-success me-2"></i>Dibaca/Diterima
                                </td>
                                <td class="fw-semibold text-dark">{{ $operan->dibaca_pada->translatedFormat('d M Y, H:i') }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Catatan Umum --}}
            @if($operan->catatan)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-chat-text me-2"></i>CATATAN UMUM
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 rounded-3 border-start border-4 border-success" style="background:#f0fdf4; font-size:0.88rem; line-height:1.7;">
                        {{ $operan->catatan }}
                    </div>
                </div>
            </div>
            @endif

            {{-- Daftar Item Tugas --}}
            @if(!empty($operan->tugas_items))
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-list-task me-2"></i>DAFTAR TUGAS YANG DIOPER
                    </h6>
                    <span class="badge bg-{{ $operan->status_penyelesaian === 'selesai' ? 'success' : ($operan->status_penyelesaian === 'dieskalasi' ? 'secondary' : 'danger') }}-subtle text-{{ $operan->status_penyelesaian === 'selesai' ? 'success' : ($operan->status_penyelesaian === 'dieskalasi' ? 'secondary' : 'danger') }} border border-{{ $operan->status_penyelesaian === 'selesai' ? 'success' : ($operan->status_penyelesaian === 'dieskalasi' ? 'secondary' : 'danger') }}-subtle" style="font-size:0.75rem;">
                        {{ $operan->status_penyelesaian === 'selesai' ? '✓ Selesai' : ($operan->status_penyelesaian === 'dieskalasi' ? '→ Diteruskan' : '● Belum Selesai') }}
                    </span>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush rounded-bottom-4">
                        @foreach($operan->tugas_items as $item)
                        <li class="list-group-item px-4 py-3 d-flex align-items-center gap-3" style="font-size:0.85rem;">
                            @if($operan->status_penyelesaian === 'selesai')
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:24px;height:24px;background:#d1fae5;color:#059669;">
                                    <i class="bi bi-check2" style="font-size:0.75rem;"></i>
                                </div>
                            @elseif($operan->status_penyelesaian === 'dieskalasi')
                                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width:24px;height:24px;background:#e0e7ff;color:#4f46e5;">
                                    <i class="bi bi-arrow-right" style="font-size:0.75rem;"></i>
                                </div>
                            @else
                                <div class="rounded-circle flex-shrink-0"
                                     style="width:24px;height:24px;border:2px solid #d1d5db;"></div>
                            @endif
                            <span>{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Status Alat --}}
            @if($operan->status_alat && count($operan->status_alat) > 0)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-tools me-2"></i>ALAT TERSEDIA & BAIK
                    </h6>
                </div>
                <div class="card-body p-4 d-flex flex-wrap gap-2">
                    @foreach($operan->status_alat as $alat)
                        <span class="badge rounded-pill px-3 py-2"
                              style="background:#d1fae5; color:#059669; border:1px solid #a7f3d0; font-size:0.78rem; font-weight:600;">
                            <i class="bi bi-check-circle-fill me-1"></i>{{ $alat }}
                        </span>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Catatan Eskalasi --}}
            @if($operan->status_penyelesaian === 'dieskalasi' && $operan->catatan_eskalasi)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <h6 class="fw-bold mb-0 text-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>ALASAN TUGAS DITERUSKAN
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="p-3 rounded-3 border-start border-4 border-warning" style="background:#fff7ed; font-size:0.88rem; line-height:1.7; color:#92400e;">
                        <i class="bi bi-chat-quote-fill me-1"></i>{{ $operan->catatan_eskalasi }}
                    </div>
                    @if($operan->eskalasiBerikutnya)
                        <div class="mt-3 d-flex align-items-center gap-2 text-muted" style="font-size:0.82rem;">
                            <i class="bi bi-arrow-right-circle-fill text-secondary"></i>
                            Diteruskan ke: <strong>{{ $operan->eskalasiBerikutnya->penerima->name }}</strong>
                            @if($operan->eskalasiBerikutnya->status_terima === 'diterima')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">Diterima ✓</span>
                            @else
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2">Menunggu</span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Riwayat Rantai Operan --}}
            @if($operan->parentOperan)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-diagram-2 me-2"></i>RIWAYAT RANTAI OPERAN
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex gap-3 py-3 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:36px;height:36px;background:#dbeafe;color:#2563eb;">
                            <i class="bi bi-send-fill"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Operan Asal</div>
                            <div class="text-muted" style="font-size:0.78rem;">
                                Dari <strong>{{ $operan->parentOperan->pengirim->name }}</strong>
                                → <strong>{{ $operan->parentOperan->penerima->name }}</strong>
                            </div>
                            <div class="text-muted" style="font-size:0.74rem;">
                                {{ $operan->parentOperan->tanggal->translatedFormat('d M Y') }}, {{ $operan->parentOperan->waktu }}
                            </div>
                            @if($operan->parentOperan->catatan_eskalasi)
                                <div class="mt-1 p-2 rounded-2 border-start border-3 border-warning" style="background:#fff7ed; font-size:0.78rem; color:#92400e;">
                                    Alasan diteruskan: {{ $operan->parentOperan->catatan_eskalasi }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-3 pt-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:36px;height:36px;background:#fef3c7;color:#d97706;">
                            <i class="bi bi-arrow-right"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.88rem;">Operan Ini (Diteruskan)</div>
                            <div class="text-muted" style="font-size:0.78rem;">
                                Dari <strong>{{ $operan->pengirim->name }}</strong>
                                → <strong>{{ $operan->penerima->name }}</strong>
                            </div>
                            <div class="text-muted" style="font-size:0.74rem;">
                                {{ $operan->tanggal->translatedFormat('d M Y') }}, {{ $operan->waktu }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- Kolom Kanan: Ringkasan & Aksi --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-info-circle me-2"></i>RINGKASAN
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background:#f8fffe;">
                        <div class="rounded-3 p-2 bg-success-subtle flex-shrink-0">
                            <i class="bi bi-calendar3 text-success fs-5"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem;" class="text-muted text-uppercase fw-semibold">Tanggal</div>
                            <div class="fw-semibold" style="font-size:0.88rem;">{{ $operan->tanggal->translatedFormat('d M Y') }}</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background:#f8fffe;">
                        <div class="rounded-3 p-2 bg-info-subtle flex-shrink-0">
                            <i class="bi bi-clock text-info fs-5"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem;" class="text-muted text-uppercase fw-semibold">Waktu</div>
                            <div class="fw-semibold" style="font-size:0.88rem;">{{ $operan->waktu }} WIB</div>
                        </div>
                    </div>
                    @if($operan->tempat_tugas)
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-2" style="background:#f8fffe;">
                        <div class="rounded-3 p-2 bg-danger-subtle flex-shrink-0">
                            <i class="bi bi-geo-alt text-danger fs-5"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem;" class="text-muted text-uppercase fw-semibold">Tempat Tugas</div>
                            <div class="fw-semibold" style="font-size:0.88rem;">{{ $operan->tempat_tugas }}</div>
                        </div>
                    </div>
                    @endif
                    @if($operan->waktu_jaga)
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#f8fffe;">
                        <div class="rounded-3 p-2 bg-primary-subtle flex-shrink-0">
                            <i class="bi bi-shield text-primary fs-5"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem;" class="text-muted text-uppercase fw-semibold">Waktu Jaga</div>
                            <div class="fw-semibold" style="font-size:0.88rem;">{{ $operan->waktu_jaga }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Aksi untuk penerima operan --}}
            @if(auth()->id() === $operan->penerima_id)
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="bi bi-gear me-2"></i>AKSI
                    </h6>
                </div>
                <div class="card-body p-3 d-grid gap-2">
                    @if($operan->status_terima === 'menunggu')
                        <form method="POST" action="{{ route('operan.terima', $operan->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-success btn-sm rounded-3 w-100 px-3"
                                    onclick="return confirm('Konfirmasi terima operan ini?')">
                                <i class="bi bi-check-circle me-1"></i> Konfirmasi Diterima
                            </button>
                        </form>
                    @endif
                    @if($operan->status_penyelesaian === 'belum' && $operan->status_terima === 'diterima')
                        <form method="POST" action="{{ route('operan.selesaikan', $operan->id) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn btn-outline-success btn-sm rounded-3 w-100 px-3"
                                    onclick="return confirm('Tandai tugas ini sebagai selesai?')">
                                <i class="bi bi-check2-all me-1"></i> Tandai Selesai
                            </button>
                        </form>
                        <button type="button" class="btn btn-outline-warning btn-sm rounded-3 w-100 px-3"
                                data-bs-toggle="modal" data-bs-target="#modalEskalasi">
                            <i class="bi bi-arrow-right-circle me-1"></i> Eskalasikan
                        </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Eskalasi --}}
<div class="modal fade" id="modalEskalasi" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-arrow-right-circle text-warning me-2"></i>Oper ke Shift Berikutnya
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('operan.eskalasi', $operan->id) }}">
                @csrf
                <div class="modal-body px-4 pb-0">
                    <div class="alert alert-warning rounded-3 d-flex gap-2 align-items-start mb-3" style="font-size:0.82rem;">
                        <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
                        <span>Tugas ini akan <strong>diteruskan</strong> ke rekan shift berikutnya. Pastikan Anda memberikan alasan yang jelas.</span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-uppercase" style="font-size:0.78rem; letter-spacing:.4px;">
                            Penerima Berikutnya <span class="text-danger">*</span>
                        </label>
                        <select name="penerima_eskalasi_id" class="form-select form-select-sm rounded-3" required>
                            <option value="">-- Pilih Rekan --</option>
                            @foreach($daftarPenerima as $rekan)
                                <option value="{{ $rekan->id }}">{{ $rekan->name }} ({{ $rekan->nik }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-uppercase" style="font-size:0.78rem; letter-spacing:.4px;">
                            Alasan Eskalasi <span class="text-danger">*</span>
                        </label>
                        <textarea name="catatan_eskalasi" class="form-control rounded-3" rows="3"
                                  placeholder="Jelaskan alasan mengapa tugas ini perlu diteruskan (min. 10 karakter)..."
                                  minlength="10" maxlength="500" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pt-0">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm rounded-3 px-3">
                        <i class="bi bi-arrow-right-circle me-1"></i> Konfirmasi & Teruskan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

