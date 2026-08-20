@extends('tata-letak.aplikasi')

@section('content')
<style>
    body { background-color: #f0fdf4; }

    .login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: stretch;
    }

    /* ===== Panel Kiri: Branding ===== */
    .login-branding {
        flex: 1;
        background: linear-gradient(135deg, #12a65a 0%, #0d8a4a 40%, #086838 100%);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 3rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .login-branding::before {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 400px;
        height: 400px;
        border-radius: 50%;
        background: rgba(255,255,255,0.05);
    }

    .login-branding::after {
        content: '';
        position: absolute;
        bottom: -150px;
        left: -100px;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: rgba(255,255,255,0.03);
    }

    .branding-content {
        position: relative;
        z-index: 2;
        text-align: center;
        max-width: 450px;
    }

    .branding-icon {
        width: 100px;
        height: 100px;
        border-radius: 28px;
        background: rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        font-size: 3rem;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .branding-content h1 {
        font-size: 2.2rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .branding-content .subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 2.5rem;
        font-weight: 300;
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
        text-align: left;
    }

    .feature-list li {
        display: flex;
        align-items: center;
        padding: 0.75rem 0;
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .feature-list li i {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1rem;
        flex-shrink: 0;
    }

    /* ===== Panel Kanan: Form Login ===== */
    .login-form-panel {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 3rem;
        background-color: #ffffff;
        max-width: 600px;
    }

    .login-form-container {
        width: 100%;
        max-width: 420px;
    }

    .login-form-container h2 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 0.5rem;
    }

    .login-form-container .lead-text {
        color: #6b7280;
        font-size: 0.95rem;
        margin-bottom: 2rem;
    }

    .form-floating-custom {
        position: relative;
        margin-bottom: 1.25rem;
    }

    .form-floating-custom .form-control {
        height: 56px;
        padding: 1rem 1rem 1rem 3rem;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f9fafb;
    }

    .form-floating-custom .form-control:focus {
        border-color: #12a65a;
        background-color: #fff;
        box-shadow: 0 0 0 4px rgba(18, 166, 90, 0.1);
    }

    .form-floating-custom .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        font-size: 1.1rem;
        z-index: 5;
    }

    .form-floating-custom .form-control:focus ~ .input-icon {
        color: #12a65a;
    }

    .btn-login {
        height: 52px;
        border-radius: 14px;
        font-size: 1rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(18, 166, 90, 0.3);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .login-footer {
        margin-top: 2rem;
        text-align: center;
        color: #9ca3af;
        font-size: 0.85rem;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 991.98px) {
        .login-wrapper {
            flex-direction: column;
        }

        .login-branding {
            flex: none;
            padding: 2.5rem 2rem 4rem;
            min-height: auto;
        }

        .branding-content h1 { font-size: 1.6rem; }
        .branding-content .subtitle { font-size: 0.95rem; margin-bottom: 1.5rem; }
        .feature-list { display: none; }

        .branding-icon {
            width: 72px;
            height: 72px;
            font-size: 2.2rem;
            margin-bottom: 1.2rem;
            border-radius: 20px;
        }

        .login-form-panel {
            flex: 1;
            max-width: 100%;
            padding: 2rem 1.5rem;
            margin-top: -2rem;
            border-radius: 24px 24px 0 0;
            position: relative;
            z-index: 10;
        }

        .login-form-container h2 { font-size: 1.4rem; }
    }

    @media (max-width: 575.98px) {
        .login-branding {
            padding: 2rem 1.5rem 3.5rem;
        }
    }
</style>

<div class="login-wrapper">
    {{-- Panel Kiri: Branding --}}
    <div class="login-branding">
        <div class="branding-content">
            <div class="branding-icon bg-white">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Puskesmas" style="width: 100%; height: 100%; object-fit: contain; padding: 10px; border-radius: 28px;">
            </div>
            <h1>SIM Kebersihan</h1>
            <p class="subtitle">Puskesmas Cempaka Putih</p>

            <ul class="feature-list">
                <li>
                    <i class="bi bi-card-checklist"></i>
                    <span>Monitoring kebersihan terintegrasi real-time</span>
                </li>
                <li>
                    <i class="bi bi-camera"></i>
                    <span>Bukti foto Before/After dengan timestamp & GPS</span>
                </li>
                <li>
                    <i class="bi bi-arrow-left-right"></i>
                    <span>Operan shift digital antar petugas</span>
                </li>
                <li>
                    <i class="bi bi-box-seam"></i>
                    <span>Manajemen inventori & permintaan barang</span>
                </li>
                <li>
                    <i class="bi bi-graph-up"></i>
                    <span>Laporan kinerja otomatis PDF & Excel</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- Panel Kanan: Form Login --}}
    <div class="login-form-panel">
        <div class="login-form-container">
            <h2>Masuk ke Sistem</h2>
            <p class="lead-text">Gunakan NIK dan password yang diberikan oleh Administrator.</p>
            
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-floating-custom">
                    <i class="bi bi-person-badge input-icon"></i>
                    <input id="nik" type="text"
                        class="form-control @error('nik') is-invalid @enderror"
                        name="nik" value="{{ old('nik') }}"
                        placeholder="NIK"
                        required autofocus>
                    @error('nik')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-floating-custom">
                    <i class="bi bi-lock input-icon"></i>
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password" placeholder="Password" required>
                    @error('password')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mb-4 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label text-secondary" for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn btn-success btn-login w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                </button>
            </form>

            <div class="login-footer">
                <i class="bi bi-shield-lock me-1"></i>
                Dilindungi dengan enkripsi SSL & CSRF protection
            </div>
        </div>
    </div>
</div>
@endsection
