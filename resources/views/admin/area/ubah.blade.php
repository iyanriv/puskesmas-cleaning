@extends('tata-letak.aplikasi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Ubah Area</h3>
        <a href="{{ route('admin.area.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.area.update', $area->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama_ruangan" class="form-label">Nama Ruangan</label>
                        <input type="text" class="form-control rounded-pill @error('nama_ruangan') is-invalid @enderror" id="nama_ruangan" name="nama_ruangan" value="{{ old('nama_ruangan', $area->nama_ruangan) }}" required>
                        @error('nama_ruangan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="lantai" class="form-label">Lantai</label>
                        <input type="number" class="form-control rounded-pill @error('lantai') is-invalid @enderror" id="lantai" name="lantai" value="{{ old('lantai', $area->lantai) }}" required>
                        @error('lantai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-success rounded-pill px-4">Perbarui Area</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
