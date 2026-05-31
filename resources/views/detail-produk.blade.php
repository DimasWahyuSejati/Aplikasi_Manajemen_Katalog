@extends('layouts.app')
@section('title', 'Detail Produk')
@section('header_title', 'Detail Katalog Sepatu')

@section('content')
    <div class="mb-4">
        <a href="{{ url('/katalog') }}" class="btn btn-white bg-white text-dark fw-bold shadow-sm rounded-pill px-4 py-2 text-decoration-none border-0">
            <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Katalog
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 bg-white" id="detail-container">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Memuat data produk...</p>
        </div>
    </div>
@endsection

@section('modals')
    <!-- Modals removed, update handled via different page or ajax later -->
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get ID from URL segment e.g. /detail-produk/1
    const pathArray = window.location.pathname.split('/');
    const productId = pathArray[pathArray.length - 1];
    
    fetchProductDetail(productId);
});

function fetchProductDetail(id) {
    fetchWithAuth('http://localhost:5000/api/catalog/' + id)
        .then(response => {
            if(!response.ok) throw new Error('Produk tidak ditemukan');
            return response.json();
        })
        .then(product => {
            const container = document.getElementById('detail-container');
            const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(product.price);
            
            let imageContent = `
                <i class="fa-solid fa-shoe-prints text-muted opacity-25 mb-4" style="font-size: 10rem;"></i>
                <span class="text-muted fw-bold bg-white px-3 py-1 rounded-pill shadow-sm">Preview Gambar</span>
            `;

            if (product.imageUrl) {
                imageContent = `<img src="${product.imageUrl}" alt="${product.name}" class="img-fluid rounded-4 shadow-sm" style="max-height: 400px; object-fit: contain; width: 100%;">`;
            }

            let variantsHtml = '<span class="text-muted">Ukuran tidak tersedia</span>';
            if (product.variants && product.variants.length > 0) {
                variantsHtml = product.variants
                    .sort((a, b) => parseInt(a.Size.size_value) - parseInt(b.Size.size_value))
                    .map(v => {
                        const bgClass = v.stock > 0 ? 'btn-outline-primary' : 'btn-outline-secondary opacity-50';
                        return `
                            <div class="btn ${bgClass} rounded-3 text-start p-2 shadow-sm" style="min-width: 90px; cursor: default;">
                                <div class="fw-bold mb-1">EU ${v.Size.size_value}</div>
                                <div class="small" style="font-size: 0.75rem;">Stok: ${v.stock}</div>
                            </div>
                        `;
                    }).join('');
            }

            container.innerHTML = `
                <div class="row g-0">
                    <div class="col-md-5 bg-light d-flex flex-column align-items-center justify-content-center p-4 position-relative" style="min-height: 450px;">
                        ${imageContent}
                    </div>
                    
                    <div class="col-md-7 p-5 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex gap-2 flex-wrap">
                                ${product.brand ? `<span class="badge bg-dark text-white px-4 py-2 rounded-pill fs-6">${product.brand}</span>` : ''}
                                <span class="badge bg-secondary bg-opacity-25 text-secondary px-4 py-2 rounded-pill fs-6">${product.category}</span>
                            </div>
                            <span class="text-muted fw-bold bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill text-nowrap">
                                <i class="fa-solid fa-box me-1"></i> Tersedia: ${product.stock} Pasang
                            </span>
                        </div>
                        
                        <h1 class="fw-bold text-dark mb-1" style="font-size: 2.5rem;">${product.name}</h1>
                        <h2 class="text-primary fw-bold mb-4 fs-1 text-nowrap">${priceFormatted}</h2>
                        
                        <h6 class="fw-bold text-dark mb-2 fs-5">Deskripsi Produk</h6>
                        <p class="text-muted mb-4" style="line-height: 1.8;">
                            ${product.description || 'Tidak ada deskripsi'}
                        </p>
                        
                        <h6 class="fw-bold text-dark mb-3 fs-5">Ukuran Tersedia & Stok</h6>
                        <div class="d-flex flex-wrap gap-2 mb-5">
                            ${variantsHtml}
                        </div>

                        <div class="mt-auto"></div>
                        <hr class="text-muted opacity-10 mb-4">
                        
                        <div class="d-flex gap-3">
                            <button class="btn btn-warning fw-bold rounded-pill px-4 py-2 shadow-sm text-dark flex-grow-1 text-nowrap" onclick="editProduk(${product.id})">
                                <i class="fa-solid fa-pen me-2"></i> Edit Data
                            </button>
                            <button class="btn btn-danger fw-bold rounded-pill px-4 py-2 shadow-sm flex-grow-1 text-nowrap" onclick="konfirmasiHapus(${product.id})">
                                <i class="fa-solid fa-trash me-2"></i> Hapus Sepatu
                            </button>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            document.getElementById('detail-container').innerHTML = `
                <div class="text-center py-5 text-danger">
                    <h4>${error.message}</h4>
                    <a href="/katalog" class="btn btn-primary mt-3">Kembali ke Katalog</a>
                </div>
            `;
        });
}

function editProduk(id) {
    window.location.href = "/edit-produk/" + id; 
}

window.konfirmasiHapus = function(id) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Sepatu?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                prosesHapus(id);
            }
        })
    } else {
        if(confirm("Yakin hapus?")) prosesHapus(id);
    }
}

function prosesHapus(id) {
    fetchWithAuth('http://localhost:5000/api/catalog/' + id, {
        method: 'DELETE'
    })
    .then(response => {
        if(response.ok) {
            window.location.href = "/katalog";
        }
    });
}
</script>
@endsection