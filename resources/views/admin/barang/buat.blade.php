@extends('tata-letak.aplikasi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0 fw-bold">Tambah Barang</h3>
            <p class="text-muted mb-0" style="font-size:0.85rem;">
                Barang baru akan otomatis dicatat ke Laporan Stok bulan berjalan jika terhubung ke produk kebersihan.
            </p>
        </div>
        <a href="{{ route('admin.barang.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-3">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.barang.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">

                    {{-- Nama Barang --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3 @error('nama_barang') is-invalid @enderror"
                               name="nama_barang" value="{{ old('nama_barang') }}" required maxlength="150">
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Kode Barang --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Kode Barang
                            <span class="text-muted fw-normal" style="font-size:0.8rem;">(disamakan dengan laporan stok)</span>
                        </label>
                        <div class="input-group">
                            <input type="text" class="form-control rounded-start-3 @error('kode_barang') is-invalid @enderror"
                                   name="kode_barang" id="inputKodeBarang" value="{{ old('kode_barang') }}"
                                   maxlength="30" placeholder="Pilih dari daftar atau isi manual">
                            <button type="button" class="btn btn-outline-secondary rounded-end-3"
                                    data-bs-toggle="modal" data-bs-target="#modalPilihProduk">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        @error('kode_barang') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        <div class="form-text">Klik <i class="bi bi-search"></i> untuk pilih dari master produk kebersihan.</div>
                        <div id="infoNamaProduk" class="text-success fw-semibold mt-1" style="font-size:0.8rem; display:none;"></div>
                    </div>

                    {{-- Satuan --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Satuan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control rounded-3 @error('satuan') is-invalid @enderror"
                               name="satuan" id="inputSatuan" value="{{ old('satuan') }}"
                               placeholder="pcs, botol, galon ..." required maxlength="30">
                        @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Stok --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Stok / Masuk <span class="text-danger">*</span></label>
                        <input type="number" class="form-control rounded-3 @error('stok_saat_ini') is-invalid @enderror"
                               name="stok_saat_ini" value="{{ old('stok_saat_ini', 0) }}" min="0" required>
                        @error('stok_saat_ini') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text text-success"><i class="bi bi-info-circle"></i> Otomatis dicatat ke laporan stok bulan ini.</div>
                    </div>

                    {{-- Stok Minimum --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Stok Minimum Alert</label>
                        <input type="number" class="form-control rounded-3 @error('stok_minimum') is-invalid @enderror"
                               name="stok_minimum" value="{{ old('stok_minimum', 5) }}" min="0">
                        @error('stok_minimum') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Foto --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Foto Barang</label>
                        <input type="file" class="form-control rounded-3 @error('foto_barang') is-invalid @enderror"
                               name="foto_barang" accept="image/*">
                        @error('foto_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea class="form-control rounded-3 @error('deskripsi') is-invalid @enderror"
                                  name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-2 text-end">
                        <button type="submit" class="btn btn-success rounded-3 px-4">
                            <i class="bi bi-check-lg me-1"></i> Simpan Barang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Pilih Produk Kebersihan --}}
<div class="modal fade" id="modalPilihProduk" tabindex="-1" aria-labelledby="labelPilihProduk" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="labelPilihProduk">
                    <i class="bi bi-search text-success me-2"></i>Pilih Produk Kebersihan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-0">
                <input type="text" class="form-control rounded-3 mb-3" id="cariProdukModal"
                       placeholder="Cari nama barang...">
                <div class="table-responsive table-scroll-container">
                    <table class="table table-hover align-middle" style="font-size:0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="tabelProdukModal">
                            @foreach($daftarProduk as $produk)
                                <tr class="baris-produk" data-kode="{{ $produk->kode_barang }}"
                                    data-nama="{{ $produk->nama_barang }}" data-satuan="{{ $produk->satuan }}">
                                    <td class="text-muted">{{ $produk->kode_barang }}</td>
                                    <td class="fw-semibold">{{ $produk->nama_barang }}</td>
                                    <td>{{ $produk->satuan }}</td>
                                    <td>
                                        <button type="button" class="btn btn-success btn-sm rounded-2 pilih-produk-btn">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Filter tabel modal
document.getElementById('cariProdukModal').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.baris-produk').forEach(row => {
        row.style.display = row.dataset.nama.toLowerCase().includes(q) ? '' : 'none';
    });
});

// Pilih produk dari modal
document.querySelectorAll('.pilih-produk-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        document.getElementById('inputKodeBarang').value = row.dataset.kode;
        document.getElementById('inputSatuan').value = row.dataset.satuan;
        const info = document.getElementById('infoNamaProduk');
        info.textContent = '✓ Terhubung ke: ' + row.dataset.nama;
        info.style.display = 'block';
        bootstrap.Modal.getInstance(document.getElementById('modalPilihProduk')).hide();
    });
});
</script>
@endsection
