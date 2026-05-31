@extends('layouts.app')
@section('title', 'Katalog Produk')
@section('header_title', 'Katalog Sepatu')

@section('content')
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                
                <div class="d-flex align-items-center gap-3">
                    <h6 class="text-muted fw-bold mb-0 text-nowrap">Pilih Kategori:</h6>
                    
                    <div class="dropdown">
                        <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-filter me-1"></i> Semua Kategori
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3 mt-2">
                            <li><a class="dropdown-item active fw-bold" href="#">Semua Kategori</a></li>
                            <li><a class="dropdown-item" href="#">Sneakers</a></li>
                            <li><a class="dropdown-item" href="#">Boots</a></li>
                            <li><a class="dropdown-item" href="#">Running</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-light border-end-0 rounded-start-pill text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" class="form-control bg-light border-start-0 rounded-end-pill" placeholder="Cari model sepatu...">
                </div>
                
            </div>
        </div>
    </div>

    <div class="row g-4">
        
        <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100 product-card">
                <div class="bg-light rounded-top-4 d-flex align-items-center justify-content-center position-relative" style="height: 200px;">
                    <i class="fa-solid fa-shoe-prints fa-4x text-muted opacity-25"></i>
                    <span class="position-absolute top-0 end-0 m-3 badge bg-secondary bg-opacity-75 rounded-pill px-3 py-2 shadow-sm">
                        Sneakers
                    </span>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="fw-bold text-dark mb-1 text-truncate" title="Nike Air Max 97">Nike Air Max 97</h5>
                    <p class="text-primary fw-bold fs-5 mb-3 text-nowrap">Rp 2.500.000</p>
                    
                    <div class="mt-auto">
                        <hr class="text-muted opacity-25 my-3">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <span class="text-muted fw-bold fs-6 mb-0 text-nowrap">Stok: 15</span>
                            <a href="{{ url('/detail-produk/1') }}" class="btn btn-light text-primary btn-sm rounded-pill fw-bold px-4 text-nowrap">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100 product-card">
                <div class="bg-light rounded-top-4 d-flex align-items-center justify-content-center position-relative" style="height: 200px;">
                    <i class="fa-solid fa-boot fa-4x text-muted opacity-25"></i>
                    <span class="position-absolute top-0 end-0 m-3 badge bg-info bg-opacity-75 text-dark rounded-pill px-3 py-2 shadow-sm">
                        Boots
                    </span>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="fw-bold text-dark mb-1 text-truncate" title="Timberland Pro">Timberland Pro</h5>
                    <p class="text-primary fw-bold fs-5 mb-3 text-nowrap">Rp 3.200.000</p>
                    
                    <div class="mt-auto">
                        <hr class="text-muted opacity-25 my-3">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <span class="text-muted fw-bold fs-6 mb-0 text-nowrap">Stok: 8</span>
                            <a href="{{ url('/detail-produk/2') }}" class="btn btn-light text-primary btn-sm rounded-pill fw-bold px-4 text-nowrap">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100 product-card">
                <div class="bg-light rounded-top-4 d-flex align-items-center justify-content-center position-relative" style="height: 200px;">
                    <i class="fa-solid fa-person-running fa-4x text-muted opacity-25"></i>
                    <span class="position-absolute top-0 end-0 m-3 badge bg-warning bg-opacity-75 text-dark rounded-pill px-3 py-2 shadow-sm">
                        Running
                    </span>
                </div>
                <div class="card-body p-4 d-flex flex-column">
                    <h5 class="fw-bold text-dark mb-1 text-truncate" title="Adidas Ultraboost">Adidas Ultraboost</h5>
                    <p class="text-primary fw-bold fs-5 mb-3 text-nowrap">Rp 2.800.000</p>
                    
                    <div class="mt-auto">
                        <hr class="text-muted opacity-25 my-3">
                        <div class="d-flex justify-content-between align-items-center gap-2">
                            <span class="text-muted fw-bold fs-6 mb-0 text-nowrap">Stok: 20</span>
                            <a href="{{ url('/detail-produk/3') }}" class="btn btn-light text-primary btn-sm rounded-pill fw-bold px-4 text-nowrap">
                                Detail
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

