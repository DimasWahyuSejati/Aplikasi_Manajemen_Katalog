@extends('layouts.app')
@section('title', 'Edit Data Sepatu')
@section('header_title', 'Edit Katalog Produk')

@section('content')
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 800px;">
        <div class="card-header bg-warning text-dark py-3 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-pen me-2"></i> Form Edit Sepatu</h5>
            <a href="{{ url('/detail-produk/'.$id) }}" class="btn btn-sm btn-light fw-bold">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body p-4">
            <form id="form-edit-produk">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nama Model</label>
                        <input type="text" id="input-nama" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Merek</label>
                        <select id="input-merek" class="form-select" required>
                            <option disabled value="">Pilih Merek</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Kategori</label>
                        <select id="input-kategori" class="form-select" required>
                            <option disabled value="">Pilih Kategori</option>
                        </select>
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
                        <input type="url" id="input-gambar" class="form-control" placeholder="Contoh: https://example.com/gambar-sepatu.jpg">
                    </div>
                </div>

                <hr>
                <div class="d-flex justify-content-end">
                    <a href="{{ url('/detail-produk/'.$id) }}" class="btn btn-secondary me-2 fw-bold">Batal</a>
                    <button type="submit" id="btn-submit" class="btn btn-warning fw-bold">
                        <i class="fa-solid fa-save me-1"></i> Update Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
const productId = {{ $id }};

document.addEventListener('DOMContentLoaded', function() {
    // Load categories and brands in parallel, then load product data
    Promise.all([
        fetchWithAuth('http://localhost:5000/api/categories').then(res => res.json()),
        fetchWithAuth('http://localhost:5000/api/brands').then(res => res.json()),
        fetchWithAuth('http://localhost:5000/api/sizes').then(res => res.json())
    ])
    .then(([categories, brands, sizes]) => {
        // Populate categories
        const kategoriSelect = document.getElementById('input-kategori');
        kategoriSelect.innerHTML = '<option disabled value="">Pilih Kategori</option>';
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.name;
            option.textContent = cat.name;
            kategoriSelect.appendChild(option);
        });

        // Populate brands
        const merekSelect = document.getElementById('input-merek');
        merekSelect.innerHTML = '<option disabled value="">Pilih Merek</option>';
        brands.forEach(brand => {
            const option = document.createElement('option');
            option.value = brand.name;
            option.textContent = brand.name;
            merekSelect.appendChild(option);
        });

        // Render Sizes Grid
        const container = document.getElementById('sizes-container');
        container.innerHTML = '';
        sizes.forEach(size => {
            const div = document.createElement('div');
            div.className = 'col-md-3 col-sm-4 col-6';
            div.innerHTML = `
                <div class="form-check mb-2">
                    <input class="form-check-input size-checkbox" type="checkbox" value="${size.id}" id="size-${size.id}" data-size="${size.size_value}">
                    <label class="form-check-label fw-bold" for="size-${size.id}">
                        Ukuran ${size.size_value}
                    </label>
                </div>
                <input type="number" class="form-control form-control-sm size-stock-input d-none" id="stock-${size.id}" placeholder="Stok" min="0">
            `;
            container.appendChild(div);

            const checkbox = document.getElementById(`size-${size.id}`);
            const stockInput = document.getElementById(`stock-${size.id}`);
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    stockInput.classList.remove('d-none');
                    stockInput.required = true;
                } else {
                    stockInput.classList.add('d-none');
                    stockInput.required = false;
                    stockInput.value = '';
                }
            });
        });

        return fetchWithAuth('http://localhost:5000/api/catalog/' + productId);
    })
    .then(response => {
        if(!response.ok) throw new Error('Data tidak ditemukan');
        return response.json();
    })
    .then(data => {
        document.getElementById('input-nama').value = data.name;

        // Set brand
        const merekSelect = document.getElementById('input-merek');
        let brandExists = false;
        for (let i = 0; i < merekSelect.options.length; i++) {
            if (merekSelect.options[i].value === data.brand) {
                brandExists = true;
                break;
            }
        }
        if (!brandExists && data.brand) {
            const newOption = new Option(data.brand, data.brand);
            merekSelect.add(newOption);
        }
        merekSelect.value = data.brand || '';

        // Set category
        const kategoriSelect = document.getElementById('input-kategori');
        let optionExists = false;
        for (let i = 0; i < kategoriSelect.options.length; i++) {
            if (kategoriSelect.options[i].value === data.category) {
                optionExists = true;
                break;
            }
        }
        if (!optionExists && data.category) {
            const newOption = new Option(data.category, data.category);
            kategoriSelect.add(newOption);
        }
        kategoriSelect.value = data.category;
        
        document.getElementById('input-harga').value = data.price;
        document.getElementById('input-gambar').value = data.imageUrl || '';

        // Pre-fill variants
        if (data.variants && Array.isArray(data.variants)) {
            data.variants.forEach(variant => {
                const checkbox = document.getElementById(`size-${variant.size_id}`);
                const stockInput = document.getElementById(`stock-${variant.size_id}`);
                if (checkbox && stockInput) {
                    checkbox.checked = true;
                    stockInput.value = variant.stock;
                    stockInput.classList.remove('d-none');
                    stockInput.required = true;
                }
            });
        }
    })
    .catch(error => {
        alert('Gagal memuat data produk: ' + error.message);
    });
});

document.getElementById('form-edit-produk').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btnSubmit = document.getElementById('btn-submit');
    const originalText = btnSubmit.innerHTML;
    btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Mengupdate...';
    btnSubmit.disabled = true;

    const variants = [];
    document.querySelectorAll('.size-checkbox:checked').forEach(checkbox => {
        const sizeId = checkbox.value;
        const stock = document.getElementById(`stock-${sizeId}`).value;
        variants.push({
            size_id: parseInt(sizeId),
            stock: parseInt(stock)
        });
    });

    if (variants.length === 0) {
        alert('Pilih setidaknya satu ukuran dan masukkan stok.');
        btnSubmit.innerHTML = originalText;
        btnSubmit.disabled = false;
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

    fetchWithAuth('http://localhost:5000/api/catalog/' + productId, {
        method: 'PUT',
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Gagal mengupdate data ke backend.');
        }
        return response.json();
    })
    .then(result => {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Data sepatu berhasil diupdate.',
                confirmButtonText: 'Ke Detail'
            }).then(() => {
                window.location.href = "{{ url('/detail-produk') }}/" + productId;
            });
        } else {
            alert('Berhasil mengupdate data!');
            window.location.href = "{{ url('/detail-produk') }}/" + productId;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan: ' + error.message,
            });
        } else {
            alert('Terjadi kesalahan: ' + error.message);
        }
        btnSubmit.innerHTML = originalText;
        btnSubmit.disabled = false;
    });
});
</script>
@endsection