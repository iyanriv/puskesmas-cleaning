@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 pb-2 text-center">
                    <img src="{{ asset('images/logo-sikapbersih.png') }}" alt="Logo SIKAP Bersih"
                         style="height: 64px; width: auto; object-fit: contain;" class="mb-3">
                    <h4 class="fw-bold mb-1" style="color: #205F63; letter-spacing: -0.5px;">SIKAP Bersih</h4>
                    <p class="text-muted small mb-0" style="font-size: 0.82rem;">
                        Sistem Informasi Kebersihan dan Penilaian<br>
                        <span class="fst-italic text-secondary">"Pantau Kebersihan, Tingkatkan Kinerja."</span>
                    </p>
                </div>

                <div class="card-body p-4 pt-3">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold small text-secondary">Alamat Email / Nomor Karyawan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input id="email" type="email"
                                       class="form-control bg-light border-start-0 rounded-end-3 @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                                       placeholder="nama@puskesmas.id" style="font-size: 0.9rem;">
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold small text-secondary">Kata Sandi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted rounded-start-3">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input id="password" type="password"
                                       class="form-control bg-light border-start-0 rounded-end-3 @error('password') is-invalid @enderror"
                                       name="password" required autocomplete="current-password"
                                       placeholder="••••••••" style="font-size: 0.9rem;">
                                @error('password')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                       {{ old('remember') ? 'checked' : '' }} style="cursor: pointer;">
                                <label class="form-check-label small text-muted" for="remember" style="cursor: pointer;">
                                    Ingat Saya
                                </label>
                            </div>
                            @if (Route::has('password.request'))
                                <a class="small text-decoration-none" href="{{ route('password.request') }}" style="color: #31999E;">
                                    Lupa Kata Sandi?
                                </a>
                            @endif
                        </div>

                        <div class="d-grid mb-2">
                            <button type="submit" class="btn text-white fw-semibold py-2 rounded-3 shadow-sm"
                                    style="background: linear-gradient(135deg, #31999E 0%, #205F63 100%);">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light border-0 py-3 text-center">
                    <small class="text-muted" style="font-size: 0.75rem;">
                        &copy; {{ date('Y') }} Puskesmas Cempaka Putih. All rights reserved.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
