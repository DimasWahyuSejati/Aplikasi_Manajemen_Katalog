@extends('layouts.app')
@section('title', 'Detail Produk')
@section('header_title', 'Detail Katalog Sepatu')

@section('content')
    <div class="mb-4">
        <a href="{{ url('/katalog') }}" class="btn btn-white bg-white text-dark fw-bold shadow-sm rounded-pill px-4 py-2 text-decoration-none border-0">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Katalog
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 bg-white">
        <div class="row g-0">
            
            <div class="col-md-5 bg-light d-flex flex-column align-items-center justify-content-center p-5 position-relative" style="min-height: 450px;">
                <button class="btn btn-white bg-white shadow-sm rounded-circle position-absolute top-0 end-0 m-4 text-danger d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="fa-solid fa-heart"></i>
                </button>
                
                <i class="fa-solid fa-shoe-prints text-muted opacity-25 mb-4" style="font-size: 10rem;"></i>
                <span class="text-muted fw-bold bg-white px-3 py-1 rounded-pill shadow-sm">Preview Gambar</span>
            </div>
            
            <div class="col-md-7 p-5 d-flex flex-column">
                
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-secondary bg-opacity-25 text-secondary px-4 py-2 rounded-pill fs-6">{{ $produk->kategori ?? 'Sneakers' }}</span>
                    <span class="text-muted fw-bold bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill text-nowrap">
                        <i class="fa-solid fa-box me-1"></i> Tersedia: {{ $produk->stok ?? 15 }} Pasang
                    </span>
                </div>
                
                <h1 class="fw-bold text-dark mb-1" style="font-size: 2.5rem;">{{ $produk->nama ?? 'Nike Air Max 97' }}</h1>
                <h2 class="text-primary fw-bold mb-4 fs-1 text-nowrap">Rp {{ isset($produk->harga) ? number_format($produk->harga, 0, ',', '.') : '2.500.000' }}</h2>
                
                <h6 class="fw-bold text-dark mb-2 fs-5">Deskripsi Produk</h6>
                <p class="text-muted mb-4" style="line-height: 1.8;">
                    {{ $produk->deskripsi ?? 'Sepatu kasual bergaya retro modern dengan bantalan udara maksimal untuk kenyamanan sepanjang hari. Cocok untuk digunakan sehari-hari maupun untuk aktivitas ringan. Material upper terbuat dari bahan kulit sintetis dan rajutan yang breathable sehingga kaki tetap sejuk.' }}
                </p>
                
                <h6 class="fw-bold text-dark mb-3 fs-5">Pilihan Ukuran (EU)</h6>
                <div class="d-flex flex-wrap gap-2 mb-5">
                    <button class="btn btn-outline-secondary rounded-3 fw-bold px-4 py-2">39</button>
                    <button class="btn btn-outline-secondary rounded-3 fw-bold px-4 py-2">40</button>
                    <button class="btn btn-primary rounded-3 fw-bold px-4 py-2 shadow-sm">41</button> 
                    <button class="btn btn-outline-secondary rounded-3 fw-bold px-4 py-2">42</button>
                    <button class="btn btn-light text-muted border rounded-3 fw-bold px-4 py-2" disabled>43 (Habis)</button>
                </div>

                <div class="mt-auto"></div>
                <hr class="text-muted opacity-10 mb-4">
                
                <div class="d-flex gap-3">
                    <button class="btn btn-warning fw-bold rounded-pill px-4 py-2 shadow-sm text-dark flex-grow-1 text-nowrap" data-bs-toggle="modal" data-bs-target="#modalEdit">
                        <i class="fa-solid fa-pen me-2"></i> Edit Data
                    </button>
                    <button class="btn btn-danger fw-bold rounded-pill px-4 py-2 shadow-sm flex-grow-1 text-nowrap" onclick="konfirmasiHapus()">
                        <i class="fa-solid fa-trash me-2"></i> Hapus Sepatu
                    </button>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('modals')
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
                            <input type="text" class="form-control rounded-3" value="{{ $produk->nama ?? 'Nike Air Max 97' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Kategori</label>
                            <select class="form-select rounded-3">
                                <option selected>{{ $produk->kategori ?? 'Sneakers' }}</option>
                                <option>Boots</option>
                                <option>Running</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Harga (Rp)</label>
                            <input type="number" class="form-control rounded-3" value="{{ $produk->harga ?? '2500000' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted">Stok</label>
                            <input type="number" class="form-control rounded-3" value="{{ $produk->stok ?? '15' }}">
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