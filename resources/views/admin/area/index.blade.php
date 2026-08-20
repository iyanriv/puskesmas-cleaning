@extends('tata-letak.aplikasi')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">Kelola Area</h3>
        <a href="{{ route('admin.area.create') }}" class="btn btn-success rounded-pill px-4">
            <i class="bi bi-plus-circle me-1"></i> Tambah Area
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
            <form action="{{ route('admin.area.index') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-9">
                    <input type="text" name="cari" class="form-control rounded-pill" placeholder="Cari Nama Ruangan atau Lantai..." value="{{ request('cari') }}">
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
                            <th>Nama Ruangan</th>
                            <th>Lantai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($area as $key => $a)
                            <tr>
                                <td>{{ $area->firstItem() + $key }}</td>
                                <td>{{ $a->nama_ruangan }}</td>
                                <td>{{ $a->lantai }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.area.edit', $a->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">Edit</a>
                                        <form action="{{ route('admin.area.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus area ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">Data area tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-end mt-3">
                {{ $area->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
