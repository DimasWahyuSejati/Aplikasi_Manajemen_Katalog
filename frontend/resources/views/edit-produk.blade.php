@extends('layouts.app')
@section('title', 'Edit Sepatu')
@section('header_title', 'Edit Katalog Produk')

@section('content')
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold">Form Edit Sepatu</h5>
            <a href="{{ url('/katalog') }}" class="btn btn-sm btn-light fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body p-4" id="form-container" style="display: none;">
            <form id="form-edit-produk">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Model</label>
                        <input type="text" id="input-nama" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Merek</label>
                        <select id="input-merek" class="form-select" required></select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kategori</label>
                        <select id="input-kategori" class="form-select" required></select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Harga (Rp)</label>
                        <input type="number" id="input-harga" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Ukuran & Stok</label>
                        <div class="card bg-light border-0 rounded-4">
                            <div class="card-body p-3">
                                <div class="row g-3" id="sizes-container">
                                    <div class="col-12 text-center text-muted">Memuat ukuran...</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Link Gambar Sepatu (Opsional)</label>
                        <input type="url" id="input-gambar" class="form-control">
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <a href="{{ url('/katalog') }}" class="btn btn-secondary me-2 fw-bold">Batal</a>
                    <button type="submit" id="btn-submit" class="btn btn-primary fw-bold">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body p-5 text-center" id="loading-container">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Memuat data produk...</p>
        </div>
    </div>
@endsection

@section('scripts')
<script>
const productId = {{ $id }};
let existingProduct = null;
let allSizes = [];

document.addEventListener('DOMContentLoaded', function() {
    // Fetch data yang dibutuhkan secara paralel
    Promise.all([
        fetchWithAuth(API_ENDPOINTS.categories).then(res => res.json()),
        fetchWithAuth(API_ENDPOINTS.brands).then(res => res.json()),
        fetchWithAuth(API_ENDPOINTS.sizes).then(res => res.json()),
        fetchWithAuth(API_ENDPOINTS.catalogById(productId)).then(res => {
            if (!res.ok) throw new Error('Produk tidak ditemukan');
            return res.json();
        })
    ])
    .then(([categories, brands, sizes, product]) => {
        existingProduct = product;
        allSizes = sizes;

        // Populate dropdowns using helper
        populateSelect(document.getElementById('input-kategori'), categories, 'name', 'name', 'Pilih Kategori');
        populateSelect(document.getElementById('input-merek'), brands, 'name', 'name', 'Pilih Merek');
        
        // Render checkboxes with pre-filled variants using helper
        renderSizeCheckboxes(document.getElementById('sizes-container'), sizes, product.variants);

        // Pre-fill form fields
        document.getElementById('input-nama').value = product.name;
        document.getElementById('input-merek').value = product.brand || '';
        document.getElementById('input-kategori').value = product.category || '';
        document.getElementById('input-harga').value = product.price;
        document.getElementById('input-gambar').value = product.imageUrl || '';

        document.getElementById('loading-container').style.display = 'none';
        document.getElementById('form-container').style.display = 'block';
    })
    .catch(error => {
        document.getElementById('loading-container').innerHTML = 
            `<h5 class="text-danger">Gagal memuat data: ${error.message}</h5>
             <a href="/katalog" class="btn btn-primary mt-3">Kembali ke Katalog</a>`;
    });
});

// ─── Form Submit ──────────────────────────────────────────────────
document.getElementById('form-edit-produk').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btnSubmit = document.getElementById('btn-submit');
    setButtonLoading(btnSubmit, true);

    const variants = collectVariantsFromCheckboxes();
    if (variants.length === 0) {
        alert('Pilih setidaknya satu ukuran dan masukkan stok.');
        setButtonLoading(btnSubmit, false, '<i class="fa-solid fa-save me-1"></i> Simpan Perubahan');
        return;
    }

    const data = {
        name: document.getElementById('input-nama').value,
        brand: document.getElementById('input-merek').value,
        category: document.getElementById('input-kategori').value,
        price: document.getElementById('input-harga').value,
        imageUrl: document.getElementById('input-gambar').value,
        variants: variants
    };

    fetchWithAuth(API_ENDPOINTS.catalogById(productId), { method: 'PUT', body: JSON.stringify(data) })
        .then(response => {
            if (!response.ok) throw new Error('Gagal mengupdate data.');
            return response.json();
        })
        .then(() => {
            showSuccess('Tersimpan!', 'Data sepatu berhasil diperbarui.', () => {
                window.location.href = `/detail-produk/${productId}`;
            });
        })
        .catch(error => {
            showError('Terjadi kesalahan: ' + error.message);
            setButtonLoading(btnSubmit, false, '<i class="fa-solid fa-save me-1"></i> Simpan Perubahan');
        });
});
</script>
@endsection