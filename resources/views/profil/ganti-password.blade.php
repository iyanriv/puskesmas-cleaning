@extends('tata-letak.aplikasi')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Ganti Password</h3>
            </div>

            @if(session('sukses'))
                <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
                    {{ session('sukses') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('profil.ganti-password.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="password_lama" class="form-label">Password Lama</label>
                            <input type="password" class="form-control rounded-pill @error('password_lama') is-invalid @enderror" id="password_lama" name="password_lama" required>
                            @error('password_lama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password_baru" class="form-label">Password Baru</label>
                            <input type="password" class="form-control rounded-pill @error('password_baru') is-invalid @enderror" id="password_baru" name="password_baru" required>
                            @error('password_baru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_baru_confirmation" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control rounded-pill" id="password_baru_confirmation" name="password_baru_confirmation" required>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-save me-1"></i> Simpan Password Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
