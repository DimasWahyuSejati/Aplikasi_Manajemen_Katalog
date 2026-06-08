@extends('layouts.app')
@section('title', 'Detail Produk')
@section('header_title', 'Rincian Katalog')

@section('content')
    <div class="card shadow-sm border-0 mx-auto rounded-4 overflow-hidden" style="max-width: 900px;" id="detail-card">
        <div class="row g-0">
            <div class="col-md-5 bg-light d-flex align-items-center justify-content-center p-4 position-relative" style="min-height: 350px;">
                <div id="product-image-container" class="w-100 h-100 d-flex align-items-center justify-content-center">
                    <i class="fa-solid fa-shoe-prints fa-5x text-muted opacity-25"></i>
                </div>
                <div class="position-absolute top-0 start-0 m-3">
                    <a href="{{ url('/katalog') }}" class="btn btn-sm btn-dark bg-opacity-50 border-0 rounded-circle shadow-sm" style="width: 35px; height: 35px; d-flex; align-items: center; justify-content: center;">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </div>
                <span class="position-absolute top-0 end-0 m-3 badge bg-secondary bg-opacity-75 rounded-pill px-3 py-2 shadow-sm" id="badge-kategori">
                    Memuat...
                </span>
            </div>
            
            <div class="col-md-7">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-dark text-white rounded-pill px-3 py-1 mb-2" id="badge-merek">Memuat...</span>
                            <h3 class="fw-bold text-dark mb-1" id="product-name">Memuat Data...</h3>
                            <p class="text-muted small mb-3">ID Produk: <span id="product-id">#{{ $id }}</span></p>
                        </div>
                        <h4 class="text-primary fw-bold m-0" id="product-price">Rp -</h4>
                    </div>

                    <p class="text-muted mb-4" style="line-height: 1.6;" id="product-description">
                        Sedang memuat deskripsi produk...
                    </p>

                    <div class="mb-4">
                        <h6 class="fw-bold mb-3 d-flex justify-content-between">
                            <span>Pilihan Ukuran & Stok</span>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3" id="total-stock">Total: 0</span>
                        </h6>
                        <div class="d-flex flex-wrap gap-2" id="variants-container">
                            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                        </div>
                    </div>

                    <hr class="text-muted opacity-25 mb-4">

                    <div class="d-flex gap-2">
                        <a href="{{ url('/edit-produk/' . $id) }}" class="btn btn-primary fw-bold px-4 flex-grow-1 rounded-pill">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Data
                        </a>
                        <button class="btn btn-outline-danger fw-bold px-4 rounded-pill" onclick="hapusProdukIni()">
                            <i class="fa-solid fa-trash me-1"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
const productId = {{ $id }};

document.addEventListener('DOMContentLoaded', function() {
    fetchWithAuth(API_ENDPOINTS.catalogById(productId))
        .then(response => {
            if (!response.ok) throw new Error('Produk tidak ditemukan');
            return response.json();
        })
        .then(product => {
            renderProductDetail(product);
        })
        .catch(error => {
            document.getElementById('detail-card').innerHTML = 
                `<div class="p-5 text-center text-danger">
                    <i class="fa-solid fa-circle-exclamation fa-3x mb-3"></i>
                    <h5>${error.message}</h5>
                    <a href="/katalog" class="btn btn-primary rounded-pill mt-3 px-4">Kembali ke Katalog</a>
                </div>`;
        });
});

function renderProductDetail(product) {
    document.getElementById('product-name').innerText = product.name;
    document.getElementById('badge-kategori').innerText = product.category || 'Lainnya';
    document.getElementById('badge-merek').innerText = product.brand || 'Tidak ada merek';
    
    document.getElementById('product-price').innerText = formatCurrency(product.price);
    
    if (product.description) {
        document.getElementById('product-description').innerText = product.description;
    } else {
        document.getElementById('product-description').innerHTML = '<i>Tidak ada deskripsi tersedia untuk produk ini.</i>';
    }

    document.getElementById('total-stock').innerText = `Total Stok: ${product.stock}`;

    // Handle Image
    if (product.imageUrl) {
        document.getElementById('product-image-container').innerHTML = 
            `<img src="${product.imageUrl}" alt="${product.name}" class="img-fluid w-100 h-100" style="object-fit: cover;">`;
    }

    // Render variants using helper function (atau custom logic if needed)
    const variantsContainer = document.getElementById('variants-container');
    if (!product.variants || product.variants.length === 0) {
        variantsContainer.innerHTML = '<span class="text-muted italic">Tidak ada data ukuran tersedia.</span>';
    } else {
        // Kita butuh styling khusus untuk halaman detail, jadi kita render manual sedikit beda dari badge biasa
        const sortedVariants = [...product.variants].sort((a, b) => parseInt(a.Size.size_value) - parseInt(b.Size.size_value));
        const html = sortedVariants.map(v => {
            const hasStock = v.stock > 0;
            const borderClass = hasStock ? 'border-primary text-primary' : 'border-secondary text-muted bg-light opacity-50';
            const stockBadge = hasStock 
                ? `<span class="badge bg-primary rounded-pill ms-2">${v.stock}</span>`
                : `<span class="badge bg-secondary rounded-pill ms-2">Habis</span>`;

            return `
                <div class="border rounded-pill px-3 py-2 d-flex align-items-center ${borderClass}">
                    <span class="fw-bold">EU ${v.Size.size_value}</span>
                    ${stockBadge}
                </div>
            `;
        }).join('');
        variantsContainer.innerHTML = html;
    }
}

window.hapusProdukIni = function() {
    confirmDelete({
        title: 'Hapus Produk Ini?',
        text: 'Data sepatu akan dihapus permanen dari sistem!',
        onConfirm: () => {
            fetchWithAuth(API_ENDPOINTS.catalogById(productId), { method: 'DELETE' })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal menghapus produk');
                    return response.json();
                })
                .then(() => {
                    showSuccess('Terhapus!', 'Produk berhasil dihapus.', () => {
                        window.location.href = "{{ url('/katalog') }}";
                    });
                })
                .catch(error => alert(error.message));
        }
    });
}
</script>
@endsection