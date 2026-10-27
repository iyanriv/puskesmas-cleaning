@extends('tata-letak.aplikasi')

@section('content')
<style>
    /* =========================================================
       SIKAP Bersih - JAKSEHAT INSPIRED SPLIT SCREEN LOGIN
       ========================================================= */
    :root {
        --teal-primary: #31999E;
        --teal-hover:   #267A7E;
        --teal-dark:    #205F63;
        --wave-bg:      #205F63;
        --text-heading: #263746;
        --text-muted:   #64748B;
        --input-bg:     #F3FAF9;
        --input-border: #DFE7EF;
    }

    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background-color: #ffffff;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        overflow-x: hidden;
    }

    .split-container {
        display: flex;
        min-height: 100vh;
        width: 100vw;
        position: relative;
    }

    /* ── SISI KIRI: ILUSTRASI & WAVE ORGANIK ── */
    .split-visual {
        flex: 1.1;
        position: relative;
        background: linear-gradient(155deg, #31999E 0%, #267A7E 45%, #205F63 100%);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 2.5rem 3rem 2rem;
        color: #ffffff;
    }

    /* Gelombang kurva organik khas JakSehat */
    .split-visual::after {
        content: '';
        position: absolute;
        top: -10%;
        right: -80px;
        width: 220px;
        height: 120%;
        background: #ffffff;
        border-radius: 50% 0 0 50% / 50%;
        transform: rotate(3deg);
        z-index: 2;
        pointer-events: none;
    }

    /* Awan & elemen dekoratif di background */
    .deco-cloud {
        position: absolute;
        background: rgba(255, 255, 255, 0.22);
        border-radius: 50px;
        backdrop-filter: blur(4px);
        z-index: 1;
    }
    .deco-cloud-1 {
        width: 130px;
        height: 38px;
        top: 12%;
        left: 8%;
    }
    .deco-cloud-2 {
        width: 160px;
        height: 44px;
        top: 22%;
        left: 32%;
    }
    .deco-cloud-3 {
        width: 110px;
        height: 34px;
        top: 36%;
        right: 18%;
    }

    /* Header di sisi kiri */
    .visual-header {
        position: relative;
        z-index: 3;
    }
    .visual-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: #ffffff;
        padding: 0.45rem 1rem;
        border-radius: 50px;
        font-size: 0.82rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    }

    /* Karakter Petugas Kebersihan di sisi kiri */
    .visual-character-wrap {
        position: relative;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 1;
        margin: 1rem 0;
    }

    .visual-character-card {
        max-width: 440px;
        width: 100%;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 28px;
        padding: 1rem;
        box-shadow: 0 20px 40px -15px rgba(6, 78, 59, 0.35),
                    0 0 0 1px rgba(255, 255, 255, 0.4);
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    .visual-character-card:hover {
        transform: translateY(-4px);
    }

    .visual-character-card img {
        width: 100%;
        height: auto;
        max-height: 520px;
        object-fit: cover;
        border-radius: 20px;
        display: block;
    }

    /* Footer di sisi kiri */
    .visual-footer {
        position: relative;
        z-index: 10;
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.9);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-right: 160px;
    }

    /* ── SISI KANAN: FORM LOGIN BERSIH (JAKSEHAT STYLE) ── */
    .split-form {
        flex: 1;
        background-color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 3rem 4rem;
        position: relative;
        z-index: 3;
    }

    .form-inner {
        width: 100%;
        max-width: 420px;
    }

    /* Brand Logo & Title di Sisi Kanan */
    .brand-section {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2.5rem;
    }

    .brand-logo-box {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        background: var(--input-bg);
        border: 1.5px solid #C8E9E8;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        box-shadow: 0 4px 14px rgba(49, 153, 158, 0.15);
    }
    .brand-logo-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .brand-text h1 {
        font-size: 1.45rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.3px;
    }
    .brand-text h1 span {
        color: var(--teal-primary);
    }
    .brand-text p {
        font-size: 0.82rem;
        color: #64748b;
        margin: 0;
        font-weight: 500;
    }

    /* Heading Form */
    .form-heading {
        margin-bottom: 2rem;
    }
    .form-heading h2 {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 0.35rem;
        letter-spacing: -0.5px;
    }
    .form-heading p {
        font-size: 0.88rem;
        color: #64748b;
        margin: 0;
    }

    /* Group Input */
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
        left: 1.15rem;
        color: #94a3b8;
        font-size: 1.15rem;
        pointer-events: none;
        transition: color 0.2s;
    }

    .input-field {
        width: 100%;
        height: 52px;
        padding: 0.65rem 2.85rem 0.65rem 2.95rem;
        border: 1.5px solid var(--input-border);
        border-radius: 14px;
        font-size: 0.95rem;
        background: var(--input-bg);
        color: #0f172a;
        transition: all 0.25s ease;
    }
    .input-field::placeholder {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    .input-field:focus {
        outline: none;
        border-color: var(--teal-primary);
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(49, 153, 158, 0.15);
    }
    .input-field:focus ~ i.input-icon {
        color: var(--teal-primary);
    }

    .password-toggle {
        position: absolute;
        right: 1rem;
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 0.35rem;
        font-size: 1.15rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s;
    }
    .password-toggle:hover {
        color: var(--teal-primary);
    }

    /* Pilihan Remember */
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.6rem;
        font-size: 0.85rem;
        color: #64748b;
    }
    .form-options input[type="checkbox"] {
        cursor: pointer;
        accent-color: var(--teal-primary);
        width: 16px;
        height: 16px;
    }

    /* Tombol Masuk (JakSehat Style) */
    .btn-submit {
        width: 100%;
        height: 52px;
        background: var(--teal-primary);
        border: none;
        border-radius: 14px;
        color: #ffffff;
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        box-shadow: 0 8px 18px -4px rgba(0, 168, 132, 0.38);
        cursor: pointer;
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .btn-submit:hover {
        background: var(--teal-hover);
        transform: translateY(-2px);
        box-shadow: 0 12px 22px -4px rgba(49, 153, 158, 0.45);
    }
    .btn-submit:active {
        transform: translateY(0);
    }

    /* Footer Info Bantuan */
    .form-footer-help {
        margin-top: 2rem;
        text-align: center;
        font-size: 0.82rem;
        color: #64748b;
        line-height: 1.6;
    }
    .form-footer-help a {
        color: var(--teal-primary);
        text-decoration: none;
        font-weight: 600;
    }
    .form-footer-help a:hover {
        text-decoration: underline;
    }

    .form-copyright {
        margin-top: 1.5rem;
        text-align: center;
        font-size: 0.75rem;
        color: #94a3b8;
    }

    /* ── RESPONSIVE MOBILE / TABLET ── */
    @media (max-width: 991.98px) {
        .split-container {
            flex-direction: column;
        }
        .split-visual {
            flex: none;
            padding: 2rem 1.5rem 1.5rem;
            min-height: auto;
        }
        .split-visual::after {
            display: none;
        }
        .visual-character-wrap {
            margin: 0.75rem 0;
        }
        .visual-character-card {
            max-width: 320px;
        }
        .visual-footer {
            display: none;
        }
        .split-form {
            flex: 1;
            padding: 2.25rem 1.5rem;
        }
    }
</style>

<div class="split-container">
    <!-- =========================================================
         SISI KIRI: VISUAL ILUSTRASI & BRANDING PUSKESMAS
         ========================================================= -->
    <div class="split-visual">
        <!-- Awan dekoratif melayang -->
        <div class="deco-cloud deco-cloud-1"></div>
        <div class="deco-cloud deco-cloud-2"></div>
        <div class="deco-cloud deco-cloud-3"></div>

        <!-- Header Sisi Kiri -->
        <div class="visual-header">
            <div class="visual-badge">
                <i class="bi bi-shield-check"></i>
                <span>SIKAP Bersih &mdash; Sistem Informasi Kebersihan dan Penilaian</span>
            </div>
        </div>

        <!-- Karakter Petugas Kebersihan Puskesmas -->
        <div class="visual-character-wrap">
            <div class="visual-character-card">
                <img src="{{ asset('images/cleaning-staff-hero.jpg') }}" alt="Petugas Kebersihan Puskesmas Cempaka Putih">
            </div>
        </div>

        <!-- Footer Sisi Kiri -->
        <div class="visual-footer">
            <span><i class="bi bi-geo-alt-fill me-1"></i>Puskesmas Cempaka Putih</span>
            <span>Dinas Kesehatan DKI Jakarta</span>
        </div>
    </div>

    <!-- =========================================================
         SISI KANAN: FORM LOGIN BERSIH (JAKSEHAT STYLE)
         ========================================================= -->
    <div class="split-form">
        <div class="form-inner">
            <!-- Brand Logo & Header -->
            <div class="brand-section">
                <div class="brand-logo-box">
                    <img src="{{ asset('images/logo-sikapbersih.png') }}" alt="Logo SIKAP Bersih">
                </div>
                <div class="brand-text">
                    <h1>SIKAP <span>Bersih</span></h1>
                    <p>Pantau Kebersihan, Tingkatkan Kinerja.</p>
                </div>
            </div>

            <!-- Form Heading -->
            <div class="form-heading">
                <h2>Masuk</h2>
                <p>Silakan masukkan nomor karyawan dan kata sandi Anda.</p>
            </div>

            <!-- Form Login -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <!-- Field Nomor Karyawan -->
                <div class="form-group-custom">
                    <label for="nik" class="form-label-custom">Nomor Karyawan</label>
                    <div class="input-box">
                        <i class="bi bi-person-badge input-icon"></i>
                        <input id="nik" type="text"
                            class="input-field @error('nik') is-invalid @enderror"
                            name="nik" value="{{ old('nik') }}"
                            placeholder="cth: 317101... atau username"
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

                <!-- Tombol Submit MASUK -->
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span>MASUK</span>
                    <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <!-- Informasi Bantuan Petugas -->
            <div class="form-footer-help">
                <div>Kendala masuk akun atau lupa kata sandi?</div>
                <div class="mt-1">
                    Silakan hubungi <strong>Supervisor Sanitasi</strong> atau <strong>Admin Puskesmas</strong>
                </div>
            </div>

            <!-- Copyright Meta -->
            <div class="form-copyright">
                &copy; {{ date('Y') }} Puskesmas Cempaka Putih &bull; Dinas Kesehatan Provinsi DKI Jakarta
            </div>
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
            btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> MEMVERIFIKASI...`;
            btnSubmit.disabled = true;
            setTimeout(() => {
                btnSubmit.disabled = false;
            }, 4000);
        });
    }
</script>
@endsection
