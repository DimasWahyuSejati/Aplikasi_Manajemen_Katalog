@extends('layouts.app')
@section('title', 'Edit Data Sepatu')
@section('header_title', 'Edit Data Produk')

@section('content')
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-warning py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold text-dark">Form Edit Sepatu</h5>
            <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-dark fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ url('/dashboard') }}" method="GET">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Model</label>
                        <input type="text" class="form-control" value="Nike Air Max 97" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kategori</label>
                        <select class="form-select" required>
                            <option selected>Sneakers</option>
                            <option>Boots</option>
                            <option>Running</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Harga (Rp)</label>
                        <input type="number" class="form-control" value="2500000" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Stok Awal</label>
                        <input type="number" class="form-control" value="15" required>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-end">
                    <a href="{{ url('/dashboard') }}" class="btn btn-secondary me-2 fw-bold">Batal</a>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">
                        <i class="fa-solid fa-check me-1"></i> Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection