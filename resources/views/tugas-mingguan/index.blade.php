@extends('tata-letak.aplikasi')

@section('content')
@if($isCs)
    {{-- =========================================================================
         TAMPILAN PORTAL PETUGAS KEBERSIHAN (CS & PJ LANTAI)
         Tema selaras dengan modul Dasbor CS, Ceklis, Operan Shift, Katalog Barang
         ========================================================================= --}}
    <style>
        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; }
        .mobile-container {
            max-width: 100%;
            margin: 0 auto;
            background-color: #f4f7f6;
            min-height: 100vh;
            padding-bottom: 90px;
        }

        .header-section {
            background: linear-gradient(135deg, #31999E 0%, #267A7E 50%, #205F63 100%);
            border-radius: 0 0 30px 30px;
            padding: 1.5rem 1.5rem 3.5rem 1.5rem;
            color: white;
            box-shadow: 0 4px 20px rgba(49, 153, 158, 0.18);
        }
        @media (min-width: 992px) {
            .header-section {
                border-radius: 0 0 40px 40px;
                padding: 2.25rem 3rem 4.5rem 3rem;
            }
        }

        .stat-box {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            padding: 0.85rem 1rem;
            flex: 1;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            text-align: center;
        }

        /* ── Area Konten Overlap ── */
        .overlap-cards {
            margin-top: -2.5rem;
            padding: 0 1.2rem;
            position: relative;
            z-index: 10;
        }
        @media (min-width: 992px) {
            .overlap-cards { padding: 0 3rem; }
        }

        .btn-buat-cs {
            background: white;
            border-radius: 20px;
            padding: 1.15rem 1.25rem;
            display: block;
            box-shadow: 0 6px 20px rgba(49, 153, 158, 0.08);
            border: 1.5px solid var(--brand-light, #E6F4F3);
            transition: all 0.2s ease;
        }
        .btn-buat-cs:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(49, 153, 158, 0.15);
            border-color: var(--brand-primary, #31999E);
        }
        .btn-buat-cs:active { transform: scale(0.99); }

        .icon-plus-circle {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #31999E 0%, #267A7E 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(49, 153, 158, 0.30);
        }

        .section-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* ── Tugas Cards ── */
        .tugas-cs-grid {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }
        @media (min-width: 992px) {
            .tugas-cs-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
        }
        @media (min-width: 1300px) {
            .tugas-cs-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .tugas-cs-card {
            background: white;
            border-radius: 20px;
            padding: 1.25rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            border: 1px solid #f0f0f0;
            transition: all 0.2s ease;
            position: relative;
        }
        .tugas-cs-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.07);
        }

        .tugas-time-pill {
            font-size: 0.72rem;
            font-weight: 600;
            color: #6b7280;
            background: #f3f4f6;
            padding: 3px 9px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
        }

        .badge-status-cs {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 4px 11px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
        }
        .badge-status-cs.disetujui {
            background: #d1fae5;
            color: #059669;
        }
        .badge-status-cs.selesai {
            background: #dbeafe;
            color: #2563eb;
        }
        .badge-status-cs.proses {
            background: #fef3c7;
            color: #d97706;
        }

        .tugas-kegiatan {
            font-size: 0.92rem;
            font-weight: 600;
            color: #1f2937;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.8rem;
        }

        .doc-badge {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 4px 10px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
        }
        .doc-badge.sebelum {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
        }
        .doc-badge.setelah {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .doc-badge.belum {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        /* ── Empty Card ── */
        .empty-card {
            background: white;
            border-radius: 24px;
            padding: 3rem 1.5rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.03);
            border: 1px dashed #d1d5db;
        }
        .empty-icon-box {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #e6f4f5;
            color: #267A7E;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }
    </style>

    <div class="mobile-container">
        {{-- Header Section --}}
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('dasbor.cs') }}" class="text-white text-decoration-none me-1" style="font-size: 1.25rem;">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1 mb-1" style="font-size: 0.72rem; font-weight:600; letter-spacing: 0.3px;">
                            <i class="bi bi-calendar-check me-1"></i> TUGAS MINGGUAN CS
                        </span>
                        <h4 class="fw-bold mb-0 text-white" style="font-size: 1.35rem;">Tugas Mingguan</h4>
                        <p class="mb-0 text-white-50" style="font-size: 0.82rem;">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Stat Boxes --}}
            <div class="d-flex gap-2 gap-md-3 mt-3">
                <div class="stat-box">
                    <div class="fw-bold text-white" style="font-size: 1.4rem; line-height: 1.1;">{{ $totalTugas }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.9;">Total Tugas</div>
                </div>
                <div class="stat-box">
                    <div class="fw-bold text-white" style="font-size: 1.4rem; line-height: 1.1;">{{ $tugasSelesai }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.9;">Selesai</div>
                </div>
                <div class="stat-box">
                    <div class="fw-bold text-white" style="font-size: 1.4rem; line-height: 1.1;">{{ $tugasProses }}</div>
                    <div style="font-size: 0.75rem; opacity: 0.9;">Proses</div>
                </div>
            </div>
        </div>

        {{-- Overlap Cards --}}
        <div class="overlap-cards">
            {{-- Flash Notifications --}}
            @if(session('sukses'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 py-2 px-3 mb-3 border-0 shadow-sm" role="alert" style="font-size: 0.85rem;">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('sukses') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('gagal'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 py-2 px-3 mb-3 border-0 shadow-sm" role="alert" style="font-size: 0.85rem;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('gagal') }}
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- CTA Catat Tugas Baru --}}
            <a href="{{ route('tugas-mingguan.buat') }}" class="btn-buat-cs mb-3 text-decoration-none">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-plus-circle">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.95rem;">Catat Tugas Mingguan Baru</div>
                            <div class="text-secondary" style="font-size: 0.76rem;">Ambil foto sebelum & catat rincian pengerjaan</div>
                        </div>
                    </div>
                    <i class="bi bi-chevron-right text-success fs-5"></i>
                </div>
            </a>

            {{-- Section Title --}}
            <div class="d-flex align-items-center justify-content-between mb-2 px-1">
                <div class="section-label">Riwayat Tugas Saya</div>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                    {{ $daftarTugas->total() }} Tugas
                </span>
            </div>

            {{-- Task Card List --}}
            @if($daftarTugas->count() > 0)
                <div class="tugas-cs-grid">
                    @foreach($daftarTugas as $tugas)
                        @php
                            $jumlahSebelum = is_array($tugas->foto_sebelum) ? count($tugas->foto_sebelum) : 0;
                            $isDisetujui = $tugas->status === 'disetujui';
                            $isSelesai = $tugas->status === 'selesai';
                            $isProses = $tugas->status === 'proses';
                        @endphp
                        <div class="tugas-cs-card">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="tugas-time-pill">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $tugas->tanggal->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="tugas-time-pill">
                                        <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($tugas->waktu_pelaporan)->format('H:i') }} WIB
                                    </span>
                                </div>
                                <div>
                                    @if($isDisetujui)
                                        <span class="badge-status-cs disetujui"><i class="bi bi-check-circle-fill me-1"></i> Disetujui</span>
                                    @elseif($isSelesai)
                                        <span class="badge-status-cs selesai"><i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi</span>
                                    @else
                                        <span class="badge-status-cs proses"><i class="bi bi-hourglass-split me-1"></i> Dalam Proses</span>
                                    @endif
                                </div>
                            </div>

                            <div class="tugas-kegiatan mb-3">
                                {{ $tugas->rincian_kegiatan }}
                            </div>

                            {{-- Documentation Badges --}}
                            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap">
                                <span class="doc-badge sebelum">
                                    <i class="bi bi-camera-fill me-1"></i> {{ $jumlahSebelum }} Foto Sebelum
                                </span>
                                @if($tugas->foto_setelah)
                                    <span class="doc-badge setelah">
                                        <i class="bi bi-check-circle-fill me-1"></i> Foto Selesai Ada
                                    </span>
                                @else
                                    <span class="doc-badge belum">
                                        <i class="bi bi-exclamation-circle me-1"></i> Belum Ada Foto Selesai
                                    </span>
                                @endif
                            </div>

                            {{-- Action Buttons --}}
                            <div class="d-flex gap-2 pt-2 border-top">
                                @if($isProses)
                                    <a href="{{ route('tugas-mingguan.isi-after', $tugas->id) }}" class="btn btn-success btn-sm rounded-pill flex-grow-1 fw-bold py-2" style="font-size: 0.82rem;">
                                        <i class="bi bi-camera-fill me-1"></i> Unggah Foto Selesai
                                    </a>
                                    <a href="{{ route('tugas-mingguan.detail', $tugas->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 py-2 fw-semibold" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                @else
                                    <a href="{{ route('tugas-mingguan.detail', $tugas->id) }}" class="btn btn-light btn-sm rounded-pill w-100 fw-bold py-2 border text-dark" style="font-size: 0.82rem;">
                                        <i class="bi bi-eye me-1"></i> Lihat Detail Tugas
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($daftarTugas->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $daftarTugas->links() }}
                    </div>
                @endif
            @else
                <div class="empty-card text-center">
                    <div class="empty-icon-box mb-3">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Belum Ada Tugas Mingguan</h6>
                    <p class="text-secondary small mb-3">
                        Catat kegiatan kebersihan berkala mingguan Anda dengan mengunggah foto sebelum & sesudah pengerjaan.
                    </p>
                    <a href="{{ route('tugas-mingguan.buat') }}" class="btn btn-success rounded-pill px-4 py-2 fw-bold" style="font-size: 0.88rem;">
                        <i class="bi bi-plus-circle me-1"></i> Mulai Catat Tugas
                    </a>
                </div>
            @endif
        </div>
    </div>

@else
    @include('tugas-mingguan._admin_section')
@endif
@endsection
