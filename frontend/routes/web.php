<?php

use Illuminate\Support\Facades\Route;

// Halaman Login (Default)
Route::get('/', function () {
    return view('login');
});

// Halaman Register
Route::get('/register', function () {
    return view('register');
});

// Dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
});

// Katalog (Daftar Produk)
Route::get('/katalog', function () {
    return view('katalog');
});

// Tambah Produk
Route::get('/tambah-produk', function () {
    return view('tambah-produk');
});

// Edit Produk
Route::get('/edit-produk/{id}', function ($id) {
    return view('edit-produk', ['id' => $id]);
});

// Detail Produk
Route::get('/detail-produk/{id}', function ($id) {
    // Data produk sekarang dimuat melalui API di client-side
    // Laravel hanya me-render view dan meneruskan ID
    return view('detail-produk', ['id' => $id]);
});

// Manajemen Kategori
Route::get('/kategori', function () {
    return view('kategori');
});

// Manajemen Merek
Route::get('/merek', function () {
    return view('merek');
});

// Laporan Riwayat Transaksi
Route::get('/laporan', function () {
    return view('laporan');
});

// Transaksi Stok Baru
Route::get('/transaksi-baru', function () {
    return view('transaksi-baru');
});