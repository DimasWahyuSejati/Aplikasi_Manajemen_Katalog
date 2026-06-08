@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('header_title', 'Laporan Penjualan')

@section('content')

<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-body p-4">

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

            <div class="d-flex align-items-center gap-3">
                <h6 class="text-muted fw-bold mb-0 text-nowrap">Filter Laporan:</h6>

                <div class="dropdown">
                    <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-calendar me-1"></i> Bulan Ini
                    </button>

                    <ul class="dropdown-menu shadow border-0 rounded-3 mt-2">
                        <li><a class="dropdown-item active fw-bold" href="#">Bulan Ini</a></li>
                        <li><a class="dropdown-item" href="#">Minggu Ini</a></li>
                        <li><a class="dropdown-item" href="#">Tahun Ini</a></li>
                    </ul>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fa-solid fa-file-excel me-1"></i> Export Excel
                </button>
            </div>

        </div>
    </div>
</div>

{{-- SUMMARY --}}
<div class="row g-4 mb-4">

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <h6 class="text-muted">Total Penjualan</h6>
            <h3 class="fw-bold text-primary">Rp 12.500.000</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <h6 class="text-muted">Total Transaksi</h6>
            <h3 class="fw-bold text-success">120</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-3">
            <h6 class="text-muted">Produk Terjual</h6>
            <h3 class="fw-bold text-warning">350</h3>
        </div>
    </div>

</div>

{{-- TABLE --}}
<div class="card shadow-sm border-0 rounded-4">
    <div class="card-body p-4">

        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Produk</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>1</td>
                        <td>2026-05-30</td>
                        <td>Nike Air Max 97</td>
                        <td>2</td>
                        <td>Rp 2.500.000</td>
                        <td>Rp 5.000.000</td>
                        <td><span class="badge bg-success">Lunas</span></td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>2026-05-29</td>
                        <td>Adidas Ultraboost</td>
                        <td>1</td>
                        <td>Rp 2.800.000</td>
                        <td>Rp 2.800.000</td>
                        <td><span class="badge bg-success">Lunas</span></td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <td>2026-05-28</td>
                        <td>Timberland Pro</td>
                        <td>1</td>
                        <td>Rp 3.200.000</td>
                        <td>Rp 3.200.000</td>
                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                    </tr>

                </tbody>
            </table>
        </div>

    </div>
</div>

@endsection