@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-pencil-square text-success me-2"></i>Ubah Area & Unit
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">{{ $area->lantai }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('admin.area.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    {{-- FORM CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
            <h6 class="fw-bold mb-0 text-success">
                <i class="bi bi-pencil-square me-2"></i>EDIT DATA AREA
            </h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.area.update', $area->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-7">
                        <label for="lantai" class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Nama Unit / Lantai / Ruangan <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control form-control-sm rounded-3 @error('lantai') is-invalid @enderror" 
                               id="lantai" 
                               name="lantai" 
                               value="{{ old('lantai', $area->lantai) }}" 
                               placeholder="Contoh: Lantai 1, CSSD, Ruang IGD, Poli Gigi" 
                               required 
                               autofocus>
                        @error('lantai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text text-muted small mt-2">Ketikkan nama unit kerja, ruangan, atau lantai yang baru.</div>
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.area.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Batal</a>
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-4">
                            <i class="bi bi-check2-circle me-1"></i> Perbarui Area
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
