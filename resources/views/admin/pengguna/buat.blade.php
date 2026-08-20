@extends('tata-letak.aplikasi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Tambah Pengguna</h3>
        <a href="{{ route('admin.pengguna.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.pengguna.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control rounded-pill @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nik" class="form-label">NIK</label>
                        <input type="text" class="form-control rounded-pill @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}" required>
                        @error('nik') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control rounded-pill @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="peran_id" class="form-label">Peran</label>
                        <select class="form-select rounded-pill @error('peran_id') is-invalid @enderror" id="peran_id" name="peran_id" required>
                            <option value="">Pilih Peran</option>
                            @foreach($peran as $p)
                                <option value="{{ $p->id }}" {{ old('peran_id') == $p->id ? 'selected' : '' }}>{{ $p->nama_peran }}</option>
                            @endforeach
                        </select>
                        @error('peran_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="shift" class="form-label">Shift (Opsional)</label>
                        <select class="form-select rounded-pill @error('shift') is-invalid @enderror" id="shift" name="shift">
                            <option value="">Pilih Shift</option>
                            <option value="pagi" {{ old('shift') == 'pagi' ? 'selected' : '' }}>Pagi</option>
                            <option value="siang" {{ old('shift') == 'siang' ? 'selected' : '' }}>Siang</option>
                            <option value="malam" {{ old('shift') == 'malam' ? 'selected' : '' }}>Malam</option>
                        </select>
                        @error('shift') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="area_id" class="form-label">Area (Opsional)</label>
                        <select class="form-select rounded-pill @error('area_id') is-invalid @enderror" id="area_id" name="area_id">
                            <option value="">Pilih Area</option>
                            @foreach($area as $a)
                                <option value="{{ $a->id }}" {{ old('area_id') == $a->id ? 'selected' : '' }}>{{ $a->nama_ruangan }}</option>
                            @endforeach
                        </select>
                        @error('area_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-success rounded-pill px-4">Simpan Pengguna</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
