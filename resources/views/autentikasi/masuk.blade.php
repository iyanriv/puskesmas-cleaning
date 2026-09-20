@extends('tata-letak.aplikasi')

@section('content')
<style>
    /* =========================================================
       SIM KEBERSIHAN - CLEAN GLASS TRANSPARENT LOGIN
       ========================================================= */
    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background: linear-gradient(135deg, rgba(6, 46, 26, 0.78) 0%, rgba(13, 85, 48, 0.72) 50%, rgba(15, 23, 42, 0.82) 100%),
                    url('{{ asset('images/cleaning-illustration.jpg') }}') center center / cover no-repeat fixed;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .login-wrapper {
        min-height: 100vh;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        position: relative;
    }

    /* Glassmorphic Transparent Card Box */
    .glass-card {
        width: 100%;
        max-width: 440px;
        background: rgba(255, 255, 255, 0.88);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1.5px solid rgba(255, 255, 255, 0.75);
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35),
                    0 0 0 1px rgba(255, 255, 255, 0.2);
        padding: 2.75rem 2.25rem 2.25rem;
        position: relative;
        z-index: 2;
    }

    /* Header Logo & Branding */
    .brand-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .logo-container {
        width: 82px;
        height: 82px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        margin: 0 auto 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid rgba(18, 166, 90, 0.15);
        padding: 10px;
    }

    .logo-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .brand-header h1 {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.25rem;
        letter-spacing: -0.5px;
    }

    .brand-header .subtitle {
        color: #0d8a4a;
        font-size: 0.88rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin: 0 0 0.5rem;
    }

    .brand-header .desc {
        color: #64748b;
        font-size: 0.86rem;
        margin: 0;
    }

    /* Input Styling */
    .form-group-custom {
        margin-bottom: 1.35rem;
    }

    .form-label-custom {
        display: block;
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.45rem;
    }

    .input-box {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-box i.input-icon {
        position: absolute;
        left: 1.1rem;
        color: #94a3b8;
        font-size: 1.15rem;
        pointer-events: none;
        transition: color 0.2s;
    }

    .input-field {
        width: 100%;
        height: 50px;
        padding: 0.65rem 2.8rem 0.65rem 2.85rem;
        border: 1.5px solid #cbd5e1;
        border-radius: 14px;
        font-size: 0.95rem;
        background: rgba(255, 255, 255, 0.9);
        color: #0f172a;
        transition: all 0.25s ease;
    }

    .input-field:focus {
        outline: none;
        border-color: #0d8a4a;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(13, 138, 74, 0.15);
    }

    .input-field:focus ~ i.input-icon {
        color: #0d8a4a;
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 0.25rem;
        font-size: 1.15rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }

    .password-toggle:hover {
        color: #0d8a4a;
    }

    /* Options & Button */
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        font-size: 0.85rem;
        color: #64748b;
    }

    .form-options input[type="checkbox"] {
        cursor: pointer;
        accent-color: #0d8a4a;
    }

    .btn-submit {
        width: 100%;
        height: 50px;
        background: linear-gradient(135deg, #12a65a 0%, #0d8a4a 100%);
        border: none;
        border-radius: 14px;
        color: #ffffff;
        font-weight: 700;
        font-size: 1rem;
        letter-spacing: 0.3px;
        box-shadow: 0 10px 20px -5px rgba(13, 138, 74, 0.4);
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-submit:hover {
        background: linear-gradient(135deg, #15b764 0%, #0e9651 100%);
        transform: translateY(-2px);
        box-shadow: 0 14px 25px -5px rgba(13, 138, 74, 0.48);
    }

    .btn-submit:active {
        transform: translateY(0);
    }

    /* Bottom Copyright */
    .card-footer-meta {
        margin-top: 2rem;
        text-align: center;
        font-size: 0.76rem;
        color: #94a3b8;
    }

    @media (max-width: 576.98px) {
        .glass-card {
            padding: 2rem 1.5rem;
        }
        .logo-container {
            width: 70px;
            height: 70px;
            margin-bottom: 1rem;
        }
    }
</style>

<div class="login-wrapper">
    <!-- Transparent Glass Login Card Box -->
    <div class="glass-card">
        <!-- Logo & Branding -->
        <div class="brand-header">
            <div class="logo-container">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Puskesmas" onerror="this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Logo_Puskesmas.png/480px-Logo_Puskesmas.png'">
            </div>
            <h1>SIM Kebersihan</h1>
            <div class="subtitle">Puskesmas Cempaka Putih</div>
            <p class="desc">Sistem Informasi Pengelolaan & Pemantauan Sanitasi</p>
        </div>

        <!-- Form Login -->
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <!-- Field Nomor Pegawai -->
            <div class="form-group-custom">
                <label for="nik" class="form-label-custom">Nomor Pegawai</label>

                <div class="input-box">
                    <i class="bi bi-person-badge input-icon"></i>
                    <input id="nik" type="text"
                        class="input-field @error('nik') is-invalid @enderror"
                        name="nik" value="{{ old('nik') }}"
                        placeholder="Masukkan nomor pegawai Anda..."
                        required autofocus autocomplete="username">
                </div>
                @error('nik')
                    <div class="text-danger mt-1 ms-1 fw-semibold" style="font-size: 0.8rem;">
                        <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Field Kata Sandi -->
            <div class="form-group-custom">
                <label for="password" class="form-label-custom">Kata Sandi (Password)</label>
                <div class="input-box">
                    <i class="bi bi-lock input-icon"></i>
                    <input id="password" type="password"
                        class="input-field @error('password') is-invalid @enderror"
                        name="password"
                        placeholder="Masukkan kata sandi..."
                        required autocomplete="current-password">
                    <button type="button" class="password-toggle" id="togglePasswordBtn" title="Lihat/Sembunyikan Kata Sandi">
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
                @error('password')
                    <div class="text-danger mt-1 ms-1 fw-semibold" style="font-size: 0.8rem;">
                        <i class="bi bi-exclamation-circle-fill me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="form-options">
                <label class="d-flex align-items-center gap-2 user-select-none" style="cursor: pointer;">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <span>Ingat saya</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn-submit" id="btnSubmit">
                <i class="bi bi-box-arrow-in-right fs-5"></i>
                <span>Masuk ke Sistem</span>
            </button>
        </form>

        <!-- Footer Meta -->
        <div class="card-footer-meta">
            <span>&copy; {{ date('Y') }} Puskesmas Cempaka Putih &bull; SIM Kebersihan</span>
        </div>
    </div>
</div>

<script>
    // Toggle Password Visibility
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const passwordField = document.getElementById('password');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');

    if (togglePasswordBtn && passwordField && togglePasswordIcon) {
        togglePasswordBtn.addEventListener('click', function () {
            const isPassword = passwordField.type === 'password';
            passwordField.type = isPassword ? 'text' : 'password';
            togglePasswordIcon.className = isPassword ? 'bi bi-eye-slash text-success' : 'bi bi-eye';
        });
    }

    // Form Submit Progress State
    const loginForm = document.getElementById('loginForm');
    const btnSubmit = document.getElementById('btnSubmit');
    if (loginForm && btnSubmit) {
        loginForm.addEventListener('submit', function () {
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Memverifikasi...`;
            btnSubmit.disabled = true;
            setTimeout(() => {
                btnSubmit.disabled = false;
            }, 4000);
        });
    }
</script>
@endsection
