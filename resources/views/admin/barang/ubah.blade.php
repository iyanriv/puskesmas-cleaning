@extends('tata-letak.aplikasi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Ubah Barang</h3>
        <a href="{{ route('admin.barang.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nama_barang" class="form-label">Nama Barang</label>
                        <input type="text" class="form-control rounded-pill @error('nama_barang') is-invalid @enderror" id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" required>
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="stok_saat_ini" class="form-label">Stok</label>
                        <input type="number" class="form-control rounded-pill @error('stok_saat_ini') is-invalid @enderror" id="stok_saat_ini" name="stok_saat_ini" value="{{ old('stok_saat_ini', $barang->stok_saat_ini) }}" min="0" required>
                        @error('stok_saat_ini') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="satuan" class="form-label">Satuan</label>
                        <input type="text" class="form-control rounded-pill @error('satuan') is-invalid @enderror" id="satuan" name="satuan" value="{{ old('satuan', $barang->satuan) }}" required>
                        @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="foto_barang" class="form-label">Foto Barang (Biarkan kosong jika tidak ingin mengubah)</label>
                        <input type="file" class="form-control rounded-pill @error('foto_barang') is-invalid @enderror" id="foto_barang" name="foto_barang" accept="image/*">
                        @error('foto_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if($barang->foto_barang)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $barang->foto_barang) }}" alt="Foto Barang" width="100" class="img-thumbnail rounded">
                            </div>
                        @endif
                    </div>
                    <div class="col-12">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control rounded-4 @error('deskripsi') is-invalid @enderror" id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-success rounded-pill px-4">Perbarui Barang</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
