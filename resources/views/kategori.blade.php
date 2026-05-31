@extends('layouts.app')
@section('title', 'Manajemen Kategori')
@section('header_title', 'Data Kategori Sepatu')

@section('content')
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0 rounded-top-4">
            <h5 class="m-0 fw-bold text-dark">Daftar Kategori Tersedia</h5>
            <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
            </button>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4">ID Kategori</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi Singkat</th>
                        <th class="text-center">Total Produk</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <tr>
                        <td class="ps-4 text-muted fw-bold">#CAT-01</td>
                        <td class="fw-bold text-dark">Sneakers</td>
                        <td class="text-muted">Sepatu kasual yang nyaman untuk gaya harian.</td>
                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill border border-info border-opacity-25">45 Pasang</span>
                        </td>
                        <td class="text-center pe-4 text-nowrap">
                            <button class="btn btn-light text-primary btn-sm fw-bold rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEditKategori">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <button class="btn btn-light text-danger btn-sm fw-bold rounded-pill px-3" onclick="konfirmasiHapusKategori()">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-4 text-muted fw-bold">#CAT-02</td>
                        <td class="fw-bold text-dark">Boots</td>
                        <td class="text-muted">Sepatu bot kuat untuk aktivitas luar ruangan.</td>
                        <td class="text-center">
                            <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill border border-info border-opacity-25">20 Pasang</span>
                        </td>
                        <td class="text-center pe-4 text-nowrap">
                            <button class="btn btn-light text-primary btn-sm fw-bold rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEditKategori">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <button class="btn btn-light text-danger btn-sm fw-bold rounded-pill px-3" onclick="konfirmasiHapusKategori()">
                                <i class="fa-solid fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Nama Kategori</label>
                            <input type="text" class="form-control rounded-3" placeholder="Contoh: Running Shoes">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Deskripsi</label>
                            <textarea class="form-control rounded-3" rows="3" placeholder="Tuliskan deskripsi singkat mengenai kategori ini..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Simpan Kategori</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditKategori" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-warning rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark">Edit Kategori</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Nama Kategori</label>
                            <input type="text" class="form-control rounded-3" value="Sneakers">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Deskripsi</label>
                            <textarea class="form-control rounded-3" rows="3">Sepatu kasual yang nyaman untuk gaya harian.</textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning fw-bold text-dark rounded-pill px-4" data-bs-dismiss="modal">Update Kategori</button>
                </div>
            </div>
        </div>
    </div>
@endsection
