@extends('tata-letak.aplikasi')

@section('content')
<div class="container-fluid px-3 px-lg-4 py-4">

    {{-- ================================================================
         HEADER & STATS
         ================================================================ --}}
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-list-check text-success me-2"></i>Kelola Master Jenis Pekerjaan
            </h4>
            <p class="text-muted mb-0" style="font-size:0.84rem;">
                PUSKESMAS CEMPAKA PUTIH &mdash;
                <span class="fw-semibold text-dark">MASTER PILIHAN TUGAS OPERAN SHIFT & CEKLIS CS</span>
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-success btn-sm rounded-3 px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPekerjaan">
                <i class="bi bi-plus-circle me-1"></i> Tambah Jenis Pekerjaan
            </button>
        </div>
    </div>

    {{-- ================================================================
         FLASH ALERT
         ================================================================ --}}
    @if(session('sukses'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('gagal'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('gagal') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-1 ps-3" style="font-size:0.85rem;">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================================================================
         KPI CARDS
         ================================================================ --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:44px; height:44px; background:#e6f4f5; color:#267A7E; font-size:1.3rem;">
                        <i class="bi bi-check2-all"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark mb-0">{{ $totalAktif }}</div>
                        <div class="text-muted small" style="font-size:0.75rem;">Pekerjaan Aktif</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:44px; height:44px; background:#f3f4f6; color:#6b7280; font-size:1.3rem;">
                        <i class="bi bi-pause-circle"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark mb-0">{{ $totalNonAktif }}</div>
                        <div class="text-muted small" style="font-size:0.75rem;">Non-Aktif / Arsip</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                         style="width:44px; height:44px; background:#e0f2fe; color:#0369a1; font-size:1.3rem;">
                        <i class="bi bi-card-checklist"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-dark mb-0">{{ $jenisPekerjaan->total() }}</div>
                        <div class="text-muted small" style="font-size:0.75rem;">Total Jenis Pekerjaan</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================
         PANEL FILTER & PENCARIAN
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.jenis-pekerjaan.index') }}" method="GET">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-6">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-search text-success me-1"></i>Cari Jenis Pekerjaan
                        </label>
                        <input type="text" name="cari" class="form-control form-control-sm rounded-3"
                               placeholder="Contoh: Pembersihan Kaca, Plafon, Lantai..." value="{{ request('cari') }}">
                    </div>

                    <div class="col-12 col-md-3">
                        <label class="form-label fw-semibold mb-1" style="font-size:0.78rem; text-transform:uppercase; letter-spacing:.4px;">
                            <i class="bi bi-toggle-on text-success me-1"></i>Status
                        </label>
                        <select name="status" class="form-select form-select-sm rounded-3">
                            <option value="">— Semua Status —</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Non-Aktif</option>
                        </select>
                    </div>

                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-sm rounded-3 px-3">
                            <i class="bi bi-funnel me-1"></i> Filter
                        </button>
                    </div>

                    @if(request('cari') || request('status'))
                    <div class="col-auto">
                        <a href="{{ route('admin.jenis-pekerjaan.index') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                            <i class="bi bi-x-circle me-1"></i> Reset
                        </a>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- ================================================================
         TABEL UTAMA
         ================================================================ --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between rounded-top-4">
            <h6 class="fw-bold mb-0 text-success">
                <i class="bi bi-table me-2"></i>DAFTAR MASTER JENIS PEKERJAAN
            </h6>
            <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size:0.75rem;">
                Menampilkan {{ $jenisPekerjaan->count() }} dari {{ $jenisPekerjaan->total() }} data
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive table-scroll-container">
                <table class="table table-hover align-middle mb-0" style="font-size:0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3 text-center" style="width:50px;">No</th>
                            <th style="width:70px;" class="text-center">Urutan</th>
                            <th>Nama Jenis Pekerjaan</th>
                            <th>Deskripsi / Catatan SOP</th>
                            <th class="text-center" style="width:130px;">Status</th>
                            <th class="text-center" style="width:130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenisPekerjaan as $key => $jp)
                            <tr>
                                <td class="ps-3 text-center text-muted">{{ $jenisPekerjaan->firstItem() + $key }}</td>
                                <td class="text-center fw-semibold text-secondary">
                                    <span class="badge bg-light text-dark border">{{ $jp->urutan }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">{{ $jp->nama_pekerjaan }}</span>
                                </td>
                                <td>
                                    <span class="text-muted" style="font-size:0.8rem;">
                                        {{ $jp->deskripsi ?: '—' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.jenis-pekerjaan.toggle-status', $jp->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm py-0 px-2 rounded-pill border-0 {{ $jp->is_aktif ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}"
                                                style="font-size:0.75rem; cursor:pointer;" title="Klik untuk ubah status">
                                            @if($jp->is_aktif)
                                                <i class="bi bi-check-circle-fill me-1"></i> Aktif
                                            @else
                                                <i class="bi bi-slash-circle me-1"></i> Non-Aktif
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-2 btn-edit-pekerjaan"
                                                data-id="{{ $jp->id }}"
                                                data-nama="{{ $jp->nama_pekerjaan }}"
                                                data-deskripsi="{{ $jp->deskripsi }}"
                                                data-urutan="{{ $jp->urutan }}"
                                                data-aktif="{{ $jp->is_aktif ? '1' : '0' }}"
                                                data-action="{{ route('admin.jenis-pekerjaan.update', $jp->id) }}"
                                                title="Edit Pekerjaan">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-2 btn-hapus-pekerjaan"
                                                data-nama="{{ $jp->nama_pekerjaan }}"
                                                data-action="{{ route('admin.jenis-pekerjaan.destroy', $jp->id) }}"
                                                title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                    Tidak ada data jenis pekerjaan yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($jenisPekerjaan->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-center">
                    {{ $jenisPekerjaan->links() }}
                </div>
            </div>
        @endif
    </div>

</div>

{{-- ================================================================
     MODAL TAMBAH JENIS PEKERJAAN
     ================================================================ --}}
<div class="modal fade" id="modalTambahPekerjaan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form action="{{ route('admin.jenis-pekerjaan.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom py-3 px-4">
                    <h6 class="modal-title fw-bold text-success">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Jenis Pekerjaan Baru
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="nama_pekerjaan" class="form-label fw-semibold small text-secondary">
                            Nama Jenis Pekerjaan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control rounded-3" id="nama_pekerjaan" name="nama_pekerjaan"
                               placeholder="Contoh: Pembersihan Exhaust Fan" required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-semibold small text-secondary">
                            Deskripsi / Catatan Khusus
                        </label>
                        <textarea class="form-control rounded-3" id="deskripsi" name="deskripsi" rows="2"
                                  placeholder="Keterangan SOP atau standar pengerjaan (opsional)..." maxlength="500"></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="urutan" class="form-label fw-semibold small text-secondary">Urutan Tampil</label>
                            <input type="number" class="form-control rounded-3" id="urutan" name="urutan" min="1"
                                   placeholder="Otomatis di akhir">
                        </div>
                        <div class="col-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_aktif" name="is_aktif" value="1" checked>
                                <label class="form-check-label small fw-semibold text-dark" for="is_aktif">Aktifkan Langsung</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================================
     MODAL EDIT JENIS PEKERJAAN
     ================================================================ --}}
<div class="modal fade" id="modalEditPekerjaan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="formEditPekerjaan" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom py-3 px-4">
                    <h6 class="modal-title fw-bold text-success">
                        <i class="bi bi-pencil-square me-2"></i>Ubah Jenis Pekerjaan
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label for="edit_nama_pekerjaan" class="form-label fw-semibold small text-secondary">
                            Nama Jenis Pekerjaan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control rounded-3" id="edit_nama_pekerjaan" name="nama_pekerjaan"
                               required maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label fw-semibold small text-secondary">
                            Deskripsi / Catatan Khusus
                        </label>
                        <textarea class="form-control rounded-3" id="edit_deskripsi" name="deskripsi" rows="2"
                                  placeholder="Keterangan SOP atau standar pengerjaan (opsional)..." maxlength="500"></textarea>
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label for="edit_urutan" class="form-label fw-semibold small text-secondary">Urutan Tampil</label>
                            <input type="number" class="form-control rounded-3" id="edit_urutan" name="urutan" min="1">
                        </div>
                        <div class="col-6 d-flex align-items-center pt-3">
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="edit_is_aktif" name="is_aktif" value="1">
                                <label class="form-check-label small fw-semibold text-dark" for="edit_is_aktif">Status Aktif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-3 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-3 px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ================================================================
     MODAL KONFIRMASI HAPUS
     ================================================================ --}}
<div class="modal fade" id="modalHapusPekerjaan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <form id="formHapusPekerjaan" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body text-center p-4">
                    <div class="mb-3 text-danger" style="font-size:2.5rem;">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1">Hapus Jenis Pekerjaan?</h6>
                    <p class="text-muted small mb-0" id="teksKonfirmasiHapus">
                        Apakah Anda yakin ingin menghapus data ini?
                    </p>
                </div>
                <div class="modal-footer border-0 p-3 pt-0 d-flex" style="gap:0.5rem;">
                    <button type="button" class="btn btn-light rounded-3 flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-3 flex-fill fw-semibold">Ya, Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Modal Edit Handler
    const modalEditEl = document.getElementById('modalEditPekerjaan');
    const modalEdit   = new bootstrap.Modal(modalEditEl);
    const formEdit    = document.getElementById('formEditPekerjaan');

    document.querySelectorAll('.btn-edit-pekerjaan').forEach(function (btn) {
        btn.addEventListener('click', function () {
            formEdit.action = this.dataset.action;
            document.getElementById('edit_nama_pekerjaan').value = this.dataset.nama || '';
            document.getElementById('edit_deskripsi').value = this.dataset.deskripsi || '';
            document.getElementById('edit_urutan').value = this.dataset.urutan || '1';
            document.getElementById('edit_is_aktif').checked = (this.dataset.aktif === '1');
            modalEdit.show();
        });
    });

    // Modal Hapus Handler
    const modalHapusEl = document.getElementById('modalHapusPekerjaan');
    const modalHapus   = new bootstrap.Modal(modalHapusEl);
    const formHapus    = document.getElementById('formHapusPekerjaan');
    const teksHapus    = document.getElementById('teksKonfirmasiHapus');

    document.querySelectorAll('.btn-hapus-pekerjaan').forEach(function (btn) {
        btn.addEventListener('click', function () {
            formHapus.action = this.dataset.action;
            teksHapus.innerHTML = `Yakin menghapus jenis pekerjaan <strong>"${this.dataset.nama}"</strong>?`;
            modalHapus.show();
        });
    });
});
</script>
@endsection
