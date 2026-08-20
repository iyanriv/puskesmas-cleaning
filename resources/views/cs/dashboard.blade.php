@extends('layouts.app')

@section('content')
<style>
    /* Reset & Background App */
    body {
        background-color: #f7f9fa; /* Warna abu-abu sangat muda untuk background luar */
        font-family: 'Inter', sans-serif;
    }

    /* Container utama (mengunci ukuran seperti layar HP) */
    .mobile-container {
        max-width: 100%; /* Standar lebar layar HP */
        margin: 0 auto;
        background-color: #f4f7f6;
        min-height: 100vh;
        position: relative;
        padding-bottom: 80px; /* Ruang untuk bottom nav */
    }

    /* --- Bagian Header Hijau --- */
    .header-section {  
        background-color: #12a65a; /* Hijau segar sesuai gambar */
        border-radius: 0 0 30px 30px;
        padding: 2rem 1.5rem 4rem 1.5rem; /* Padding bawah lebih besar untuk efek tumpuk */
        color: white;
    }

    .stat-box {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        padding: 1rem;
        flex: 1;
    }

    /* --- Bagian Menu Bertumpuk (Overlap) --- */
    .overlap-menu {
        margin-top: -3.5rem; /* Menarik card ke atas agar bertumpuk dengan header */
        background-color: white;
        border-radius: 24px;
        padding: 0.5rem 1rem;
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
        position: relative;
        z-index: 10;
        margin-left: 1.5rem;
        margin-right: 1.5rem;
    }

    .menu-item {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        text-decoration: none;
        color: inherit;
        border-bottom: 1px solid #f0f0f0;
    }
    .menu-item:last-child {
        border-bottom: none;
    }
    .menu-item:active {
        background-color: #f8f9fa;
        border-radius: 12px;
    }

    .icon-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background-color: #e8f6ef; /* Hijau sangat muda */
        color: #12a65a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-right: 1rem;
    }

    /* --- Bagian Aktivitas Terakhir --- */
    .activity-section {
        padding: 1.5rem;
    }
    .activity-card {
        background-color: white;
        border-radius: 16px;
        padding: 1rem;
        margin-bottom: 0.8rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
    }
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 1rem;
    }
    .dot-green { background-color: #12a65a; }
    .dot-yellow { background-color: #f5b041; }

    /* --- Bottom Navigation --- */
    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100%;
        max-width: 414px;
        background-color: white;
        display: flex;
        justify-content: space-between;
        padding: 0.8rem 1.5rem;
        border-top: 1px solid #f0f0f0;
        z-index: 50;
    }
    .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #888;
        font-size: 0.75rem;
        font-weight: 500;
        padding: 0.5rem 1rem;
        border-radius: 16px;
    }
    .nav-item.active {
        color: #12a65a;
        background-color: #e8f6ef;
    }
    .nav-item i {
        font-size: 1.3rem;
        margin-bottom: 0.2rem;
    }
    @media (min-width: 992px) { 
        .overlap-menu { max-width: 700px; margin-left: auto; margin-right: auto; }
        .activity-section { max-width: 700px; margin-left: auto; margin-right: auto; }
    }
</style>

<div class="mobile-container">
    
    <!-- 1. Header Section -->
    <div class="header-section">
        <p class="mb-1 text-white-50" style="font-size: 0.85rem;">Puskesmas Cempaka Putih</p>
        <h2 class="fw-bold mb-1" style="font-size: 1.6rem;">Selamat Pagi, Semangat! 👋</h2>
        <p class="mb-4 text-white-50" style="font-size: 0.9rem;">Mulai hari yang bersih dan sehat</p>

        <!-- Stats Boxes -->
        <div class="d-flex gap-3">
            <div class="stat-box">
                <div class="fw-bold" style="font-size: 1.5rem;">12</div>
                <div style="font-size: 0.8rem; opacity: 0.9;">tugas</div>
            </div>
            <div class="stat-box">
                <div class="fw-bold" style="font-size: 1.5rem;">92%</div>
                <div style="font-size: 0.8rem; opacity: 0.9;">tercapai</div>
            </div>
        </div>
    </div>

    <!-- 2. Overlapping Menu Card -->
    <div class="overlap-menu">
        <a href="#" class="menu-item">
            <div class="icon-circle"><i class="bi bi-card-checklist"></i></div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-0 text-dark">Ceklis Kebersihan</h6>
                <small class="text-secondary">Catat aktivitas kebersihan</small>
            </div>
            <i class="bi bi-chevron-right text-secondary"></i>
        </a>
        <a href="#" class="menu-item">
            <div class="icon-circle"><i class="bi bi-arrow-left-right"></i></div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-0 text-dark">Operan Shift</h6>
                <small class="text-secondary">Koordinasi pergantian shift</small>
            </div>
            <i class="bi bi-chevron-right text-secondary"></i>
        </a>
        <a href="#" class="menu-item">
            <div class="icon-circle"><i class="bi bi-box-seam"></i></div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-0 text-dark">Minta Barang</h6>
                <small class="text-secondary">Permintaan barang kebersihan</small>
            </div>
            <i class="bi bi-chevron-right text-secondary"></i>
        </a>
        <a href="#" class="menu-item">
            <div class="icon-circle"><i class="bi bi-recycle"></i></div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-0 text-dark">Bank Sampah</h6>
                <small class="text-secondary">Setor sampah daur ulang</small>
            </div>
            <i class="bi bi-chevron-right text-secondary"></i>
        </a>
    </div>

    <!-- 3. Aktivitas Terakhir -->
    <div class="activity-section">
        <h5 class="fw-bold text-dark mb-3">Aktivitas Terakhir</h5>
        
        <div class="activity-card">
            <div class="dot dot-green"></div>
            <div>
                <div class="text-dark fw-medium" style="font-size: 0.9rem;">Ceklis Poli KIA selesai</div>
                <div class="text-secondary" style="font-size: 0.75rem;">08:30</div>
            </div>
        </div>

        <div class="activity-card">
            <div class="dot dot-yellow"></div>
            <div>
                <div class="text-dark fw-medium" style="font-size: 0.9rem;">Minta barang: Sabun Pel x3</div>
                <div class="text-secondary" style="font-size: 0.75rem;">09:15</div>
            </div>
        </div>

        <div class="activity-card">
            <div class="dot dot-green"></div>
            <div>
                <div class="text-dark fw-medium" style="font-size: 0.9rem;">Setor sampah: 2.5 KG Plastik</div>
                <div class="text-secondary" style="font-size: 0.75rem;">10:00</div>
            </div>
        </div>
    </div>

    <!-- 4. Bottom Navigation -->
    <div class="bottom-nav">
        <a href="#" class="nav-item active">
            <i class="bi bi-house-door"></i>
            <span>Beranda</span>
        </a>
        <a href="#" class="nav-item">
            <i class="bi bi-card-checklist"></i>
            <span>Ceklis</span>
        </a>
        <a href="#" class="nav-item">
            <i class="bi bi-box-seam"></i>
            <span>Barang</span>
        </a>
        <a href="#" class="nav-item">
            <i class="bi bi-recycle"></i>
            <span>Sampah</span>
        </a>
    </div>

</div>
@endsection