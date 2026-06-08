@extends('layouts.app')
@section('title', 'Tambah Sepatu Baru')
@section('header_title', 'Tambah Katalog Produk')

@section('content')
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold">Form Tambah Sepatu Baru</h5>
            <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-light fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body p-4">
            <form id="form-tambah-produk">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Model</label>
                        <input type="text" id="input-nama" class="form-control" placeholder="Contoh: Vans Old Skool" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Merek</label>
                        <select id="input-merek" class="form-select" required>
                            <option selected disabled value="">Pilih Merek</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kategori</label>
                        <select id="input-kategori" class="form-select" required>
                            <option selected disabled value="">Pilih Kategori</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Harga (Rp)</label>
                        <input type="number" id="input-harga" class="form-control" placeholder="Contoh: 1500000" required>
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
                        <input type="url" id="input-gambar" class="form-control" placeholder="Contoh: https://example.com/gambar-sepatu.jpg">
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <a href="{{ url('/katalog') }}" class="btn btn-secondary me-2 fw-bold">Batal</a>
                    <button type="submit" id="btn-submit" class="btn btn-primary fw-bold">
                        <i class="fa-solid fa-save me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch dan populate dropdowns menggunakan helper
    fetchWithAuth(API_ENDPOINTS.categories)
        .then(res => res.json())
        .then(categories => populateSelect(document.getElementById('input-kategori'), categories, 'name', 'name', 'Pilih Kategori'))
        .catch(console.error);

    fetchWithAuth(API_ENDPOINTS.brands)
        .then(res => res.json())
        .then(brands => populateSelect(document.getElementById('input-merek'), brands, 'name', 'name', 'Pilih Merek'))
        .catch(console.error);

    // Fetch dan render size checkboxes menggunakan helper
    fetchWithAuth(API_ENDPOINTS.sizes)
        .then(res => res.json())
        .then(sizes => renderSizeCheckboxes(document.getElementById('sizes-container'), sizes))
        .catch(console.error);
});

// ─── Form Submit ──────────────────────────────────────────────────
document.getElementById('form-tambah-produk').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btnSubmit = document.getElementById('btn-submit');
    setButtonLoading(btnSubmit, true);

    const variants = collectVariantsFromCheckboxes();
    if (variants.length === 0) {
        alert('Pilih setidaknya satu ukuran dan masukkan stok.');
        setButtonLoading(btnSubmit, false, '<i class="fa-solid fa-save me-1"></i> Simpan Data');
        return;
    }

    const data = {
        name: document.getElementById('input-nama').value,
        brand: document.getElementById('input-merek').value,
        category: document.getElementById('input-kategori').value,
        color: 'Hitam',
        price: document.getElementById('input-harga').value,
        imageUrl: document.getElementById('input-gambar').value,
        description: 'Produk ditambahkan dari dashboard frontend',
        variants: variants
    };

    fetchWithAuth(API_ENDPOINTS.catalog, { method: 'POST', body: JSON.stringify(data) })
        .then(response => {
            if (!response.ok) throw new Error('Gagal menyimpan data ke backend.');
            return response.json();
        })
        .then(() => {
            showSuccess('Berhasil!', 'Data sepatu berhasil disimpan.', () => {
                window.location.href = "{{ url('/katalog') }}";
            });
        })
        .catch(error => {
            showError('Terjadi kesalahan: ' + error.message);
            setButtonLoading(btnSubmit, false, '<i class="fa-solid fa-save me-1"></i> Simpan Data');
        });
});
</script>
@endsection