@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- HEADER --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-pencil-square text-success me-2"></i>Ubah Barang
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">{{ $barang->nama_barang }}</span>
            </p>
        </div>
        <div>
            <a href="{{ route('admin.barang.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-3">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- FORM CARD --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 rounded-top-4">
            <h6 class="fw-bold mb-0 text-success">
                <i class="bi bi-pencil-square me-2"></i>EDIT DATA MASTER BARANG
            </h6>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">

                    {{-- Nama Barang --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Nama Barang <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm rounded-3 @error('nama_barang') is-invalid @enderror"
                               name="nama_barang" id="inputNamaBarang" value="{{ old('nama_barang', $barang->nama_barang) }}"
                               required maxlength="150">
                        @error('nama_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Kode Barang --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Kode Barang
                            <span class="text-muted fw-normal font-monospace" style="font-size:0.75rem;">(disamakan dengan laporan stok)</span>
                        </label>
                        <div class="input-group input-group-sm">
                            <input type="text" class="form-control rounded-start-3 @error('kode_barang') is-invalid @enderror"
                                   name="kode_barang" id="inputKodeBarang"
                                   value="{{ old('kode_barang', $barang->kode_barang) }}"
                                   maxlength="30" placeholder="Pilih dari daftar atau isi manual">
                            <button type="button" class="btn btn-outline-secondary rounded-end-3"
                                    data-bs-toggle="modal" data-bs-target="#modalPilihProduk">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                        @error('kode_barang') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                        @if($barang->produkKebersihan)
                            <div class="text-success fw-semibold mt-1" style="font-size:0.8rem;" id="infoNamaProduk">
                                ✓ Terhubung ke: {{ $barang->produkKebersihan->nama_barang }}
                            </div>
                        @else
                            <div id="infoNamaProduk" class="text-success fw-semibold mt-1" style="font-size:0.8rem; display:none;"></div>
                        @endif
                    </div>

                    {{-- Satuan --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Satuan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control form-control-sm rounded-3 @error('satuan') is-invalid @enderror"
                               name="satuan" id="inputSatuan" value="{{ old('satuan', $barang->satuan) }}"
                               required maxlength="30">
                        @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Kategori --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Kategori
                        </label>
                        <select name="kategori" class="form-select form-select-sm rounded-3">
                            <option value="">— Pilih Kategori —</option>
                            @foreach(\App\Models\BarangInventori::$daftarKategori as $k)
                                <option value="{{ $k }}" {{ old('kategori', $barang->kategori) === $k ? 'selected' : '' }}>{{ $k }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Stok --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Stok Saat Ini <span class="text-danger">*</span>
                        </label>
                        <input type="number" class="form-control form-control-sm rounded-3 @error('stok_saat_ini') is-invalid @enderror"
                               name="stok_saat_ini" value="{{ old('stok_saat_ini', $barang->stok_saat_ini) }}"
                               min="0" required>
                        @error('stok_saat_ini') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text" style="font-size:0.75rem;">
                            <i class="bi bi-exclamation-triangle text-warning"></i>
                            Gunakan ini hanya untuk <strong>koreksi</strong>. Untuk restok gunakan
                            <a href="{{ route('stok-masuk.index') }}" class="text-primary fw-semibold">Stok Masuk / Restok</a>.
                        </div>
                    </div>

                    {{-- Stok Minimum --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Stok Minimum Alert
                        </label>
                        <input type="number" class="form-control form-control-sm rounded-3 @error('stok_minimum') is-invalid @enderror"
                               name="stok_minimum" value="{{ old('stok_minimum', $barang->stok_minimum) }}" min="0">
                        @error('stok_minimum') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Foto --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Foto Barang (kosongkan jika tidak ingin mengubah)
                        </label>
                        <input type="file" class="form-control form-control-sm rounded-3 @error('foto_barang') is-invalid @enderror"
                               name="foto_barang" accept="image/*">
                        @error('foto_barang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        @if($barang->foto_barang)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $barang->foto_barang) }}"
                                     alt="Foto Barang" width="60" height="60" class="img-thumbnail rounded-3 object-fit-cover shadow-sm">
                            </div>
                        @endif
                    </div>

                    {{-- Deskripsi --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.82rem; text-transform:uppercase; letter-spacing:.4px;">
                            Deskripsi
                        </label>
                        <textarea class="form-control form-control-sm rounded-3 @error('deskripsi') is-invalid @enderror"
                                  name="deskripsi" rows="3">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                        @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12 mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.barang.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">Batal</a>
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-4">
                            <i class="bi bi-check2-circle me-1"></i> Perbarui Barang
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
            <div class="modal-header border-bottom py-3 px-4">
                <h6 class="modal-title fw-bold text-success mb-0" id="labelPilihProduk">
                    <i class="bi bi-search me-2"></i>PILIH PRODUK KEBERSIHAN
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="text" class="form-control form-control-sm rounded-3 mb-3" id="cariProdukModal"
                       placeholder="Cari nama barang...">
                <div class="table-responsive table-scroll-container">
                    <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                        <thead class="table-light">
                            <tr>
                                <th style="width:100px;">Kode</th>
                                <th>Nama Barang</th>
                                <th style="width:100px;">Satuan</th>
                                <th class="text-center" style="width:80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tabelProdukModal">
                            @foreach($daftarProduk ?? [] as $produk)
                                <tr class="baris-produk" data-kode="{{ $produk->kode_barang }}"
                                    data-nama="{{ $produk->nama_barang }}" data-satuan="{{ $produk->satuan }}">
                                    <td class="text-muted font-monospace">{{ $produk->kode_barang }}</td>
                                    <td class="fw-semibold text-dark">{{ $produk->nama_barang }}</td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1">
                                            {{ $produk->satuan }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-success btn-sm rounded-2 px-2 pilih-produk-btn" style="font-size:0.75rem;">
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
document.getElementById('cariProdukModal').addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.baris-produk').forEach(row => {
        row.style.display = row.dataset.nama.toLowerCase().includes(q) ? '' : 'none';
    });
});

document.querySelectorAll('.pilih-produk-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const inputNama = document.getElementById('inputNamaBarang');
        if (inputNama && (!inputNama.value || inputNama.value.trim() === '')) {
            inputNama.value = row.dataset.nama;
        }
        document.getElementById('inputKodeBarang').value = row.dataset.kode;
        document.getElementById('inputSatuan').value = row.dataset.satuan;
        const info = document.getElementById('infoNamaProduk');
        if (info) {
            info.textContent = '✓ Terhubung ke: ' + row.dataset.nama;
            info.style.display = 'block';
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('modalPilihProduk')).hide();
    });
});
</script>
@endsection
