@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4" style="max-width: 800px;">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-key-fill text-success me-2"></i>Ganti Password
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">KEAMANAN AKUN PENGGUNA</span>
            </p>
        </div>
    </div>

    {{-- FLASH ALERT --}}
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

    {{-- FORM CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
            <h6 class="fw-bold mb-0 text-success">
                <i class="bi bi-shield-lock me-2"></i>FORMULIR PEMBARUAN KATA SANDI
            </h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('profil.ganti-password.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="password_lama" class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                        Password Saat Ini <span class="text-danger">*</span>
                    </label>
                    <input type="password" class="form-control form-control-sm rounded-3 @error('password_lama') is-invalid @enderror"
                           id="password_lama" name="password_lama" placeholder="Masukkan password lama Anda" required>
                    @error('password_lama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_baru" class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                        Password Baru <span class="text-danger">*</span>
                    </label>
                    <input type="password" class="form-control form-control-sm rounded-3 @error('password_baru') is-invalid @enderror"
                           id="password_baru" name="password_baru" placeholder="Minimal 6 karakter" required>
                    @error('password_baru')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_baru_confirmation" class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                        Konfirmasi Password Baru <span class="text-danger">*</span>
                    </label>
                    <input type="password" class="form-control form-control-sm rounded-3"
                           id="password_baru_confirmation" name="password_baru_confirmation" placeholder="Ulangi password baru" required>
                </div>

                <div class="pt-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-success btn-sm rounded-3 px-4">
                        <i class="bi bi-check2-circle me-1"></i> Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
