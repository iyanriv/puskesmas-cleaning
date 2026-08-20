@extends('tata-letak.aplikasi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Kelola Barang</h3>
        <a href="{{ route('admin.barang.create') }}" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> Tambah Barang
        </a>
    </div>

    @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
            {{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('gagal'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
            {{ session('gagal') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.barang.index') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-9">
                    <input type="text" name="cari" class="form-control rounded-pill" placeholder="Cari Nama Barang..." value="{{ request('cari') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-success rounded-pill w-100">Cari</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Foto</th>
                            <th>Nama Barang</th>
                            <th>Stok</th>
                            <th>Satuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barang as $key => $b)
                            <tr>
                                <td>{{ $barang->firstItem() + $key }}</td>
                                <td>
                                    @if($b->foto_barang)
                                        <img src="{{ asset('storage/' . $b->foto_barang) }}" alt="{{ $b->nama_barang }}" width="50" class="img-thumbnail rounded">
                                    @else
                                        <span class="text-muted">Tidak ada foto</span>
                                    @endif
                                </td>
                                <td>{{ $b->nama_barang }}</td>
                                <td>{{ $b->stok_saat_ini }}</td>
                                <td>{{ $b->satuan }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.barang.edit', $b->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Edit</a>
                                        <form action="{{ route('admin.barang.destroy', $b->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Data barang tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $barang->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
