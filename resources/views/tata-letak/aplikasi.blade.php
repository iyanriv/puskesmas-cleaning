<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIM Kebersihan - Puskesmas Cempaka Putih</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }

        /* ================================================
           SIDEBAR LAYOUT (Admin, Supervisor, Gudang)
           ================================================ */
        .sidebar-layout .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1040;
            transition: transform 0.3s ease;
            background-color: white;
            box-shadow: 2px 0 10px rgba(0,0,0,0.03);
        }
        .sidebar-header { padding: 1.25rem 1.5rem; }
        .sidebar-body { flex: 1; overflow-y: auto; padding: 1rem 0.75rem; }
        .sidebar-footer { padding: 1.25rem 1.5rem; background-color: #fcfcfc; }
        
        .sidebar-nav .nav-item { margin-bottom: 0.25rem; }
        .sidebar-nav .nav-link {
            color: #4b5563;
            padding: 0.75rem 1rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.2s;
            display: flex; align-items: center;
        }
        .sidebar-nav .nav-link i { font-size: 1.1rem; margin-right: 0.75rem; width: 20px; text-align: center; }
        .sidebar-nav .nav-link:hover {
            background-color: #f8f9fa;
            color: #12a65a;
        }
        .sidebar-nav .nav-link.active {
            background-color: #e8f6ef;
            color: #12a65a;
            font-weight: 600;
        }
        
        .main-content-wrapper {
            margin-left: 260px;
            min-height: 100vh;
        }
        
        .mobile-header {
            display: none;
            padding: 0.75rem 1rem;
            align-items: center;
            justify-content: space-between;
        }
        
        .avatar-sm { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; background-color: #e8f6ef; color: #12a65a; flex-shrink: 0; }
        .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.4); z-index: 1030; backdrop-filter: blur(2px); }

        /* Responsive Sidebar */
        @media (max-width: 991.98px) {
            .sidebar-layout .sidebar { transform: translateX(-100%); }
            .sidebar-layout .sidebar.show { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .main-content-wrapper { margin-left: 0; }
            .mobile-header { display: flex !important; }
        }

        /* ================================================
           MOBILE CONTAINER (CS & Mobile UI)
           ================================================ */
        .mobile-container {
            margin: 0 auto;
            background-color: #f7f9fa;
            min-height: 100vh;
            position: relative;
            padding-bottom: 90px;
            overflow-x: hidden;
        }
        @media (min-width: 992px) {
            .mobile-container { max-width: 100%; box-shadow: none; padding-bottom: 2rem; }
        }
        @media (min-width: 768px) and (max-width: 991.98px) {
            .mobile-container { max-width: 720px; box-shadow: 0 0 20px rgba(0,0,0,0.05); }
        }
        @media (max-width: 767.98px) {
            .mobile-container { max-width: 100%; box-shadow: none; }
        }

        .header-section {  
            background: linear-gradient(135deg, #12a65a 0%, #0d8a4a 50%, #0a7040 100%);
            padding: 2rem 1.5rem 4rem 1.5rem;
            color: white;
        }
        @media (min-width: 992px) {
            .header-section {   border-radius: 0 0 40px 40px; padding: 2.5rem 3rem 5rem 3rem; }
        }
        @media (max-width: 991.98px) {
            .header-section {   border-radius: 0 0 30px 30px; }
        }

        .overlap-menu {
            margin-top: -3.5rem;
            background-color: white;
            border-radius: 24px;
            padding: 1.5rem;
            box-shadow: 0 8px 24px rgba(0,0,0,0.04);
            position: relative;
            z-index: 10;
            margin-left: 1.5rem;
            margin-right: 1.5rem;
        }
        @media (min-width: 992px) {
            .overlap-menu { margin-left: 3rem; margin-right: 3rem; padding: 2rem; }
        }

        .bottom-nav {
            position: fixed; bottom: 0; left: 50%; transform: translateX(-50%);
            width: 100%; max-width: 100%;
            background-color: white;
            display: flex; justify-content: space-between;
            padding: 0.6rem 0.5rem;
            border-top: 1px solid #f0f0f0;
            z-index: 1050;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        @media (min-width: 992px) {
            .bottom-nav { display: none !important; }
        }
        .bottom-nav .nav-item {
            display: flex; flex-direction: column; align-items: center;
            flex: 1;
            text-decoration: none; color: #888;
            font-size: 0.7rem; font-weight: 500;
            padding: 0.4rem 0; border-radius: 12px;
            transition: all 0.2s ease;
            position: relative;
        }
        .bottom-nav .nav-item.active { color: #12a65a; background-color: #e8f6ef; }
        .bottom-nav .nav-item i { font-size: 1.3rem; margin-bottom: 0.2rem; }
        
        .has-bottom-nav { padding-bottom: 90px !important; }
        @media (min-width: 992px) { .has-bottom-nav { padding-bottom: 2rem !important; } }
    </style>
</head>
<body>
    <div id="app">
        @php
            $isLoginPage = request()->routeIs('login');
            $isMobileUI = request()->routeIs('dasbor.cs', 'ceklis.*', 'operan.*', 'barang.katalog', 'barang.ajukan', 'sampah.buat');
            $showBottomNav = auth()->check() && in_array(auth()->user()->peran->nama_peran ?? '', ['cs', 'pj_lantai']) && $isMobileUI;
            $useSidebar = auth()->check() && !$isMobileUI && !$isLoginPage;
        @endphp

        @if($useSidebar)
            {{-- ==========================================
                 LAYOUT SIDEBAR (Admin, Supervisor, Gudang)
                 ========================================== --}}
            <div class="sidebar-layout d-flex">
                <!-- Overlay for Mobile -->
                <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

                <!-- Sidebar -->
                <nav class="sidebar border-end" id="sidebar">
                    <div class="sidebar-header border-bottom">
                        <a class="text-success text-decoration-none fs-5 fw-bold d-flex align-items-center" href="{{ auth()->user()->ruteDasbor() }}">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 32px; width: auto; margin-right: 10px;"> SIM Kebersihan
                        </a>
                    </div>
                    
                    <div class="sidebar-body">
                        <div class="text-uppercase text-secondary fw-bold mb-2 ms-2" style="font-size: 0.7rem; letter-spacing: 0.5px;">Menu Navigasi</div>
                        <ul class="nav flex-column sidebar-nav">
                            @if(auth()->user()->peran->nama_peran == 'admin')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dasbor.admin') ? 'active' : '' }}" href="{{ route('dasbor.admin') }}">
                                        <i class="bi bi-speedometer2"></i> Dasbor
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.pengguna.*') ? 'active' : '' }}" href="{{ route('admin.pengguna.index') }}">
                                        <i class="bi bi-people"></i> Kelola Pengguna
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.area.*') ? 'active' : '' }}" href="{{ route('admin.area.index') }}">
                                        <i class="bi bi-building"></i> Kelola Area
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.barang.*') ? 'active' : '' }}" href="{{ route('admin.barang.index') }}">
                                        <i class="bi bi-box"></i> Kelola Barang
                                    </a>
                                </li>
                                <li class="nav-item mt-3">
                                    <a class="nav-link {{ request()->routeIs('penilaian.*') ? 'active' : '' }}" href="{{ route('penilaian.index') }}">
                                        <i class="bi bi-star"></i> Penilaian Kinerja
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                                        <i class="bi bi-graph-up"></i> Laporan Terpadu
                                    </a>
                                </li>
                            @elseif(auth()->user()->peran->nama_peran == 'supervisor' || auth()->user()->peran->nama_peran == 'pj_lantai')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dasbor.supervisor') ? 'active' : '' }}" href="{{ route('dasbor.supervisor') }}">
                                        <i class="bi bi-speedometer2"></i> Dasbor
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('penilaian.*') ? 'active' : '' }}" href="{{ route('penilaian.index') }}">
                                        <i class="bi bi-star"></i> Penilaian Kinerja
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('sampah.rekapan') ? 'active' : '' }}" href="{{ route('sampah.rekapan') }}">
                                        <i class="bi bi-recycle"></i> Rekapan Sampah
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                                        <i class="bi bi-graph-up"></i> Laporan Terpadu
                                    </a>
                                </li>
                            @elseif(auth()->user()->peran->nama_peran == 'gudang')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('dasbor.gudang') ? 'active' : '' }}" href="{{ route('dasbor.gudang') }}">
                                        <i class="bi bi-speedometer2"></i> Dasbor
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('barang.gudang') ? 'active' : '' }}" href="{{ route('barang.gudang') }}">
                                        <i class="bi bi-box-seam"></i> Kelola Permintaan
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.barang.*') ? 'active' : '' }}" href="{{ route('admin.barang.index') }}">
                                        <i class="bi bi-box"></i> Master Barang
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    
                    <div class="sidebar-footer border-top">
                        <div class="d-flex align-items-center mb-3">
                            <div class="avatar-sm"><i class="bi bi-person-fill"></i></div>
                            <div class="ms-2">
                                <div class="fw-bold text-dark text-truncate" style="max-width: 140px; font-size: 0.9rem;">{{ auth()->user()->name }}</div>
                                <div class="text-secondary" style="font-size: 0.75rem;">{{ ucfirst(auth()->user()->peran->nama_peran) }}</div>
                            </div>
                        </div>
                        <a href="{{ route('profil.ganti-password') }}" class="btn btn-light btn-sm w-100 text-start rounded-3 mb-2 text-dark">
                            <i class="bi bi-key me-2 text-secondary"></i> Ganti Password
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100 text-start rounded-3">
                                <i class="bi bi-box-arrow-right me-2"></i> Keluar
                            </button>
                        </form>
                    </div>
                </nav>

                <!-- Main Content -->
                <div class="main-content-wrapper flex-grow-1">
                    <!-- Mobile Header (Visible only on lg and down) -->
                    <div class="mobile-header bg-white shadow-sm border-bottom">
                        <button class="btn btn-light border-0 px-2 py-1 rounded-3" type="button" onclick="toggleSidebar()">
                            <i class="bi bi-list fs-3 text-dark"></i>
                        </button>
                        <div class="fw-bold text-success fs-5 d-flex align-items-center">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 24px; margin-right: 8px;"> SIM Kebersihan
                        </div>
                        <div style="width: 42px;"></div> <!-- Spacer for centering -->
                    </div>

                    <main class="container-fluid px-md-4 py-4">
                        @yield('content')
                    </main>
                </div>
            </div>

            <script>
                function toggleSidebar() {
                    document.getElementById('sidebar').classList.toggle('show');
                    document.getElementById('sidebarOverlay').classList.toggle('show');
                }
            </script>

        @else
            {{-- ==========================================
                 LAYOUT MOBILE UI (Login, Dasbor CS, dll)
                 ========================================== --}}
            @if($isLoginPage)
                @yield('content')
            @else
                <main class="@unless($isMobileUI) container-fluid px-md-4 pb-5 @endunless @if($showBottomNav) has-bottom-nav @endif">
                    @yield('content')
                </main>
            @endif

            @if($showBottomNav)
                <div class="bottom-nav">
                    <a href="{{ route('dasbor.cs') }}" class="nav-item {{ request()->routeIs('dasbor.cs') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i><span>Beranda</span>
                    </a>
                    <a href="{{ route('ceklis.index') }}" class="nav-item {{ request()->is('ceklis*') ? 'active' : '' }}">
                        <i class="bi bi-card-checklist"></i><span>Ceklis</span>
                    </a>
                    <a href="{{ route('operan.index') }}" class="nav-item {{ request()->is('operan*') ? 'active' : '' }}" style="position:relative;">
                        <i class="bi bi-arrow-left-right"></i>
                        @php
                            $jumlahOperanNav = \App\Models\OperanShift::where('penerima_id', auth()->id())
                                ->where('status_terima', 'menunggu')->count();
                        @endphp
                        @if($jumlahOperanNav > 0)
                            <span style="position:absolute;top:4px;right:10px;background:#ef4444;color:white;
                                         font-size:0.6rem;font-weight:700;width:16px;height:16px;border-radius:50%;
                                         display:flex;align-items:center;justify-content:center;line-height:1;">
                                {{ $jumlahOperanNav }}
                            </span>
                        @endif
                        <span>Operan</span>
                    </a>
                    <a href="{{ route('barang.katalog') }}" class="nav-item {{ request()->is('barang*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam"></i><span>Barang</span>
                    </a>
                    <a href="{{ route('sampah.buat') }}" class="nav-item {{ request()->is('sampah*') ? 'active' : '' }}">
                        <i class="bi bi-recycle"></i><span>Sampah</span>
                    </a>
                </div>
            @endif
        @endif
    </div>
</body>
</html>
