@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-pencil-square text-success me-2"></i>Ubah Pengguna
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">{{ $pengguna->name }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- FORM CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
            <h6 class="fw-bold mb-0 text-success">
                <i class="bi bi-pencil-square me-2"></i>EDIT DATA PENGGUNA
            </h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.pengguna.update', $pengguna->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm rounded-3 @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $pengguna->name) }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="nik" class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Nomor Karyawan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm rounded-3 @error('nik') is-invalid @enderror"
                               id="nik" name="nik" value="{{ old('nik', $pengguna->nik) }}" required>
                        @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="password" class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Password <span class="text-muted fw-normal font-monospace" style="font-size:0.75rem;">(Kosongkan bila tidak diganti)</span>
                        </label>
                        <input type="password" class="form-control form-control-sm rounded-3 @error('password') is-invalid @enderror"
                               id="password" name="password" placeholder="••••••••">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="peran_id" class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Peran / Role Akses <span class="text-danger">*</span>
                        </label>
                        <select class="form-select form-select-sm rounded-3 @error('peran_id') is-invalid @enderror"
                                id="peran_id" name="peran_id" required>
                            <option value="">— Pilih Peran —</option>
                            @foreach($peran as $p)
                                <option value="{{ $p->id }}" {{ old('peran_id', $pengguna->peran_id) == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_peran }}
                                </option>
                            @endforeach
                        </select>
                        @error('peran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Batal</a>
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-4">
                            <i class="bi bi-check2-circle me-1"></i> Perbarui Pengguna
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
