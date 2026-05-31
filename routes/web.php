<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/tambah-produk', function () {
    return view('tambah-produk');
});

Route::get('/edit-produk/{id}', function ($id) {
    return view('edit-produk', ['id' => $id]);
});

Route::get('/kategori', function () {
    return view('kategori');
});

Route::get('/merek', function () {
    return view('merek');
});

Route::get('/katalog', function () {
    return view('katalog');
});

Route::get('/detail-produk/{id}', function ($id) {
    // SIMULASI DATA DARI DATABASE (Nantinya ini diganti dengan pemanggilan Model/Database sungguhan)
    $produk = (object) [
        'id' => $id,
        'nama' => 'Nike Air Max 97',
        'kategori' => 'Sneakers',
        'harga' => 2500000,
        'stok' => 15,
        'deskripsi' => 'Sepatu kasual bergaya retro modern dengan bantalan udara maksimal untuk kenyamanan sepanjang hari. Cocok untuk digunakan sehari-hari maupun untuk aktivitas ringan.'
    ];

    // Mengirim variabel $produk ke file detail-produk.blade.php
    return view('detail-produk', compact('produk'));
});