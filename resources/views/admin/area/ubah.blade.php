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
                        <label for="lantai" class="form-label fw-semibold">Unit / Lantai</label>
                        <select class="form-select rounded-pill @error('lantai') is-invalid @enderror" id="lantai" name="lantai" required>
                            <option value="" disabled>-- Pilih Unit / Lantai --</option>
                            @php
                                $units = $daftarUnit ?? ['Lantai 1','Lantai 2','Lantai 3','Lantai 4','Lantai 5','Lantai 6','CSSD','CPB','CPT','RWS'];
                            @endphp
                            @foreach($units as $unit)
                                <option value="{{ $unit }}" {{ old('lantai', $area->lantai) == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                            @endforeach
                        </select>
                        @error('lantai') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text text-muted small mt-2">Pilih unit kerja atau lantai yang baru.</div>
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
