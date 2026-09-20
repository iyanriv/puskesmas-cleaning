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

        /* ── Header ── */
        .header-section {
            background: linear-gradient(135deg, #12a65a 0%, #0d8a4a 50%, #0a7040 100%);
            border-radius: 0 0 30px 30px;
            padding: 1.5rem 1.5rem 3.5rem 1.5rem;
            color: white;
            box-shadow: 0 4px 20px rgba(18, 166, 90, 0.18);
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

        /* ── Tombol Tambah Tugas CS ── */
        .btn-buat-cs {
            background: white;
            border-radius: 20px;
            padding: 1.15rem 1.25rem;
            display: block;
            box-shadow: 0 6px 20px rgba(18, 166, 90, 0.08);
            border: 1.5px solid #e8f6ef;
            transition: all 0.2s ease;
        }
        .btn-buat-cs:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(18, 166, 90, 0.15);
            border-color: #12a65a;
        }
        .btn-buat-cs:active { transform: scale(0.99); }

        .icon-plus-circle {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: linear-gradient(135deg, #12a65a 0%, #0d8a4a 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(18, 166, 90, 0.3);
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
            background: #e8f6ef;
            color: #12a65a;
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
    {{-- =========================================================================
         TAMPILAN PORTAL ADMIN & SUPERVISOR (DESKTOP & SIDEBAR)
         ========================================================================= --}}
    <style>
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }

        /* ── Page Header ── */
        .page-header {
            background: linear-gradient(135deg, #12a65a 0%, #0d8a4a 100%);
            border-radius: 20px;
            padding: 1.75rem 2rem;
            color: white;
            margin-bottom: 1.75rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(18, 166, 90, 0.15);
        }
        .page-header::before {
            content: '';
            position: absolute;
            right: -50px;
            top: -50px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.05);
        }
        .page-header h4 {
            font-weight: 800;
            font-size: 1.35rem;
            margin: 0 0 0.35rem;
            position: relative;
            z-index: 1;
        }
        .page-header p {
            margin: 0;
            opacity: 0.75;
            font-size: 0.88rem;
            position: relative;
            z-index: 1;
        }

        /* ── KPI Stats ── */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.75rem;
        }
        .kpi-card {
            background: white;
            border-radius: 16px;
            padding: 1.35rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            border-top: 3px solid;
            position: relative;
            overflow: hidden;
        }
        .kpi-card::after {
            content: '';
            position: absolute;
            right: -15px;
            bottom: -15px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            opacity: 0.06;
        }
        .kpi-card.primary {
            border-top-color: #3b82f6;
        }
        .kpi-card.primary::after {
            background: #3b82f6;
        }
        .kpi-card.success {
            border-top-color: #10b981;
        }
        .kpi-card.success::after {
            background: #10b981;
        }
        .kpi-card.warning {
            border-top-color: #f59e0b;
        }
        .kpi-card.warning::after {
            background: #f59e0b;
        }
        .kpi-icon {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }
        .kpi-number {
            font-size: 2.2rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 0.25rem;
        }
        .kpi-card.primary .kpi-number {
            color: #3b82f6;
        }
        .kpi-card.success .kpi-number {
            color: #10b981;
        }
        .kpi-card.warning .kpi-number {
            color: #f59e0b;
        }
        .kpi-label {
            font-size: 0.78rem;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        /* ── Filter Section ── */
        .filter-section {
            background: white;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.75rem;
        }
        .filter-title {
            font-weight: 700;
            font-size: 0.9rem;
            color: #111827;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .filter-title i {
            color: #12a65a;
        }

        /* ── Content Section ── */
        .content-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .section-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 2px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .section-title {
            font-weight: 700;
            font-size: 1rem;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-grow: 1;
        }
        .section-title i {
            color: #12a65a;
            font-size: 1.1rem;
        }
        .section-count {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 600;
            padding: 0.35rem 0.85rem;
            background: #f3f4f6;
            border-radius: 20px;
        }

        /* ── Tugas Table ── */
        .tugas-table {
            width: 100%;
        }
        .tugas-table thead th {
            background: #f9fafb;
            padding: 1rem 1.25rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 2px solid #f3f4f6;
        }
        .tugas-table tbody tr {
            border-bottom: 1px solid #f3f4f6;
            transition: background-color 0.15s;
        }
        .tugas-table tbody tr:hover {
            background-color: #f9fafb;
        }
        .tugas-table tbody td {
            padding: 1.15rem 1.25rem;
            font-size: 0.88rem;
            color: #374151;
        }
        .tugas-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, #e8f6ef 0%, #d1f0e0 100%);
            color: #0d8a4a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
            border: 2px solid #12a65a;
            flex-shrink: 0;
        }
        .user-info h6 {
            font-size: 0.9rem;
            font-weight: 700;
            color: #111827;
            margin: 0 0 0.15rem;
        }
        .user-info .meta {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        .foto-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid;
        }
        .foto-badge.sebelum {
            background: #f3f4f6;
            border-color: #e5e7eb;
            color: #6b7280;
        }
        .foto-badge.setelah {
            background: #d1fae5;
            border-color: #10b981;
            color: #047857;
        }
        .foto-badge.belum {
            background: #fef3c7;
            border-color: #f59e0b;
            color: #b45309;
        }

        /* ── Action Buttons ── */
        .btn-action {
            padding: 0.45rem 1.15rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.15s;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }
        .btn-action.btn-primary {
            background: #12a65a;
            color: white;
        }
        .btn-action.btn-primary:hover {
            background: #0d8a4a;
        }
        .btn-action.btn-warning {
            background: #f59e0b;
            color: white;
        }
        .btn-action.btn-warning:hover {
            background: #d97706;
        }
        .btn-action.btn-outline {
            background: white;
            border: 1.5px solid #e5e7eb;
            color: #6b7280;
        }
        .btn-action.btn-outline:hover {
            border-color: #12a65a;
            color: #12a65a;
        }

        /* ── Empty State ── */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }
        .empty-state-icon {
            font-size: 4.5rem;
            color: #d1d5db;
            margin-bottom: 1.5rem;
        }
        .empty-state h6 {
            font-weight: 700;
            color: #111827;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        .empty-state p {
            color: #6b7280;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-11">

                {{-- Page Header --}}
                <div class="page-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h4><i class="bi bi-calendar-check-fill me-2"></i>Tugas Mingguan CS</h4>
                            <p>PUSKESMAS CEMPAKA PUTIH — {{ now()->translatedFormat('d F Y') }}</p>
                        </div>
                        {{-- Tombol Cetak Laporan (hanya admin/supervisor) --}}
                        @if(in_array(auth()->user()->peran?->nama_peran, ['admin', 'supervisor']))
                            <a href="{{ route('tugas-mingguan.cetak-laporan', array_filter([
                                    'petugas_id' => request('petugas_id'),
                                    'status'     => request('status'),
                                ])) }}"
                               target="_blank"
                               class="btn btn-light fw-bold rounded-pill px-4 shadow-sm"
                               style="position: relative; z-index: 1;">
                                <i class="bi bi-printer-fill me-2"></i>Cetak Laporan
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Flash Messages --}}
                @if(session('sukses'))
                    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i><strong>Berhasil!</strong> {{ session('sukses') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('gagal'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4 border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Gagal!</strong> {{ session('gagal') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- KPI Statistics --}}
                <div class="kpi-grid">
                    <div class="kpi-card primary">
                        <span class="kpi-icon">📋</span>
                        <div class="kpi-number">{{ $totalTugas }}</div>
                        <div class="kpi-label">Total Tugas</div>
                    </div>
                    <div class="kpi-card success">
                        <span class="kpi-icon">✅</span>
                        <div class="kpi-number">{{ $tugasSelesai }}</div>
                        <div class="kpi-label">Selesai</div>
                    </div>
                    <div class="kpi-card warning">
                        <span class="kpi-icon">⏳</span>
                        <div class="kpi-number">{{ $tugasProses }}</div>
                        <div class="kpi-label">Dalam Proses</div>
                    </div>
                </div>

                {{-- Filter Section (Supervisor / Admin Only) --}}
                @if($daftarCs->count() > 0)
                    <div class="filter-section">
                        <div class="filter-title">
                            <i class="bi bi-funnel-fill"></i>
                            Filter & Pencarian
                        </div>
                        <form action="{{ route('tugas-mingguan.index') }}" method="GET">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-semibold text-secondary mb-2">Petugas CS</label>
                                    <select name="petugas_id" class="form-select">
                                        <option value="">— Semua Petugas —</option>
                                        @foreach($daftarCs as $cs)
                                            <option value="{{ $cs->id }}" {{ request('petugas_id') == $cs->id ? 'selected' : '' }}>
                                                {{ $cs->name }} ({{ $cs->nik }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-semibold text-secondary mb-2">Status Tugas</label>
                                    <select name="status" class="form-select">
                                        <option value="">— Semua Status —</option>
                                        <option value="proses" {{ request('status') == 'proses' ? 'selected' : '' }}>Dalam Proses</option>
                                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    </select>
                                </div>
                                <div class="col-md-5 d-flex align-items-end">
                                    <div class="d-flex gap-2 w-100">
                                        <button type="submit" class="btn btn-primary px-4 flex-grow-1">
                                            <i class="bi bi-search me-2"></i>Cari
                                        </button>
                                        @if(request()->hasAny(['petugas_id', 'status']))
                                            <a href="{{ route('tugas-mingguan.index') }}" class="btn btn-outline-secondary px-4">
                                                <i class="bi bi-arrow-clockwise me-2"></i>Reset
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Daftar Tugas Table --}}
                <div class="content-section">
                    <div class="section-header">
                        <div class="section-title">
                            <i class="bi bi-list-ul"></i>
                            <span>Daftar Tugas Mingguan</span>
                        </div>
                        <div class="section-count">{{ $daftarTugas->total() }} Tugas</div>
                    </div>

                    @if($daftarTugas->count() > 0)
                        <div class="table-responsive">
                            <table class="tugas-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Petugas</th>
                                        <th>Tanggal & Waktu</th>
                                        <th>Rincian Kegiatan</th>
                                        <th style="width: 200px;">Dokumentasi</th>
                                        <th style="width: 120px;">Status</th>
                                        <th style="width: 150px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($daftarTugas as $index => $tugas)
                                        @php
                                            $jumlahSebelum = is_array($tugas->foto_sebelum) ? count($tugas->foto_sebelum) : 0;
                                        @endphp
                                        <tr>
                                            <td class="text-center fw-bold text-muted">{{ $daftarTugas->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="tugas-avatar">
                                                        {{ strtoupper(substr($tugas->user->name, 0, 2)) }}
                                                    </div>
                                                    <div class="user-info">
                                                        <h6>{{ $tugas->user->name }}</h6>
                                                        <div class="meta">NIK: {{ $tugas->user->nik ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold text-dark mb-1">
                                                    <i class="bi bi-calendar3 me-1 text-primary"></i>
                                                    {{ $tugas->tanggal->translatedFormat('d F Y') }}
                                                </div>
                                                <div class="small text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($tugas->waktu_pelaporan)->format('H:i') }} WIB
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-dark" style="line-height: 1.6;">
                                                    {{ Str::limit($tugas->rincian_kegiatan, 100) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-2">
                                                    <span class="foto-badge sebelum">
                                                        <i class="bi bi-camera-fill"></i> {{ $jumlahSebelum }} Foto Sebelum
                                                    </span>
                                                    @if($tugas->foto_setelah)
                                                        <span class="foto-badge setelah">
                                                            <i class="bi bi-check-circle-fill"></i> Foto Selesai
                                                        </span>
                                                    @else
                                                        <span class="foto-badge belum">
                                                            <i class="bi bi-hourglass-split"></i> Belum Upload
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{!! $tugas->badgeStatus() !!}</td>
                                            <td class="text-center">
                                                <div class="d-flex gap-1 justify-content-center">
                                                    <a href="{{ route('tugas-mingguan.detail', $tugas->id) }}"
                                                       class="btn-action btn-primary"
                                                       title="Lihat Detail">
                                                        <i class="bi bi-eye-fill"></i> Detail
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-journal-x"></i>
                            </div>
                            <h6>Belum Ada Tugas Mingguan</h6>
                            <p>Belum ada laporan tugas mingguan yang tersimpan dalam sistem.</p>
                        </div>
                    @endif
                </div>

                {{-- Pagination --}}
                @if($daftarTugas->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $daftarTugas->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endif
@endsection
