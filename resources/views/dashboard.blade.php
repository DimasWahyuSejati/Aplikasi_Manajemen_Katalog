@extends('layouts.app')
@section('title', 'Dashboard Katalog Sepatu')
@section('header_title', 'Dashboard Admin')

@section('content')
    <div class="row mb-4 g-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0 p-3 bg-white rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-1">TOTAL SEPATU</h6>
                        <h3 class="fw-bold text-dark mb-0">124 Pasang</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-box-open fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0 p-3 bg-white rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-1">KATEGORI</h6>
                        <h3 class="fw-bold text-dark mb-0">8 Tipe</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-tags fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0 p-3 bg-white rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-1">USER AKTIF</h6>
                        <h3 class="fw-bold text-dark mb-0">3 Admin</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-users fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0 rounded-top-4">
            <h5 class="m-0 fw-bold text-dark">Data Sepatu Terbaru</h5>
            <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fa-solid fa-plus me-1"></i> Tambah Sepatu
            </button>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nama Model</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <tr>
                        <td class="ps-4 text-muted">#001</td>
                        <td class="fw-bold text-dark">Nike Air Max 97</td>
                        <td><span class="badge bg-secondary bg-opacity-25 text-secondary px-3 py-2 rounded-pill">Sneakers</span></td>
                        <td class="fw-bold">Rp 2.500.000</td>
                        <td>15</td>
                        <td class="text-center pe-4 text-nowrap">
                            <button class="btn btn-light text-primary btn-sm fw-bold rounded-pill px-3 me-1" data-bs-toggle="modal" data-bs-target="#modalEdit">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <button class="btn btn-light text-danger btn-sm fw-bold rounded-pill px-3" onclick="konfirmasiHapus()">
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
    <div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold">Tambah Sepatu Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Nama Model</label>
                            <input type="text" class="form-control rounded-3" placeholder="Contoh: Vans Old Skool">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Kategori</label>
                            <select class="form-select rounded-3">
                                <option selected disabled>Pilih Kategori</option>
                                <option>Sneakers</option>
                                <option>Boots</option>
                                <option>Running</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Harga (Rp)</label>
                            <input type="number" class="form-control rounded-3" placeholder="Contoh: 1500000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Stok</label>
                            <input type="number" class="form-control rounded-3" placeholder="Contoh: 10">
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary fw-bold rounded-pill px-4" data-bs-dismiss="modal">Simpan Data</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-warning rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark">Edit Data Sepatu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Nama Model</label>
                            <input type="text" class="form-control rounded-3" value="Nike Air Max 97">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Kategori</label>
                            <select class="form-select rounded-3">
                                <option selected>Sneakers</option>
                                <option>Boots</option>
                                <option>Running</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Harga (Rp)</label>
                            <input type="number" class="form-control rounded-3" value="2500000">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Stok</label>
                            <input type="number" class="form-control rounded-3" value="15">
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4">
                    <button type="button" class="btn btn-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-warning fw-bold text-dark rounded-pill px-4" data-bs-dismiss="modal">Update Data</button>
                </div>
            </div>
        </div>
    </div>
@endsection

