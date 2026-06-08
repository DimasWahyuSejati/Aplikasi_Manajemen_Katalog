@extends('layouts.app')
@section('title', 'Katalog Produk')
@section('header_title', 'Katalog Sepatu')

@section('content')
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <!-- Kategori Filter -->
                    <div class="dropdown">
                        <button class="btn btn-primary rounded-pill px-3 fw-bold shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="btn-filter-kategori">
                            <i class="fa-solid fa-filter me-1"></i> Semua Kategori
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3 mt-2" id="kategori-filter-list">
                            <li><a class="dropdown-item active fw-bold" href="#" data-category="all">Semua Kategori</a></li>
                        </ul>
                    </div>

                    <!-- Brand Filter -->
                    <div class="dropdown">
                        <button class="btn btn-dark rounded-pill px-3 fw-bold shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="btn-filter-merek">
                            <i class="fa-solid fa-copyright me-1"></i> Semua Merek
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3 mt-2" id="merek-filter-list">
                            <li><a class="dropdown-item active fw-bold" href="#" data-brand="all">Semua Merek</a></li>
                        </ul>
                    </div>

                    <!-- Size Filter -->
                    <div class="dropdown">
                        <button class="btn btn-info text-white rounded-pill px-3 fw-bold shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="btn-filter-ukuran">
                            <i class="fa-solid fa-ruler-combined me-1"></i> Semua Ukuran
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3 mt-2" id="ukuran-filter-list">
                            <li><a class="dropdown-item active fw-bold" href="#" data-size="all">Semua Ukuran</a></li>
                        </ul>
                    </div>

                    <!-- Sorting -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary rounded-pill px-3 fw-bold dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="btn-sort">
                            <i class="fa-solid fa-arrow-down-wide-short me-1"></i> Urutkan
                        </button>
                        <ul class="dropdown-menu shadow border-0 rounded-3 mt-2" id="sort-list">
                            <li><a class="dropdown-item active fw-bold" href="#" data-sort="default">Default</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-sort="price-high">Harga Tertinggi</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="price-low">Harga Terendah</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-sort="stock-high">Stok Terbanyak</a></li>
                            <li><a class="dropdown-item" href="#" data-sort="stock-low">Stok Tersedikit</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-light border-end-0 rounded-start-pill text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" id="search-input" class="form-control bg-light border-start-0 rounded-end-pill" placeholder="Cari model sepatu...">
                </div>
                
            </div>
        </div>
    </div>

    <div class="row g-4" id="product-container">
        <!-- Products will be injected here via JavaScript -->
        <div class="col-12 text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
let allProducts = [];
let currentCategory = 'all';
let currentBrand = 'all';
let currentSize = 'all';
let currentSort = 'default';
let searchQuery = '';

document.addEventListener('DOMContentLoaded', function() {
    fetchProducts();
    fetchCategoriesForFilter();
    fetchBrandsForFilter();
    fetchSizesForFilter();
    setupSortHandlers();
    setupSearchHandler();
});

function fetchSizesForFilter() {
    fetchWithAuth('http://localhost:5000/api/sizes')
        .then(res => res.json())
        .then(sizes => {
            const filterList = document.getElementById('ukuran-filter-list');
            filterList.innerHTML = '<li><a class="dropdown-item active fw-bold" href="#" data-size="all">Semua Ukuran</a></li>';
            
            // Sort sizes
            sizes.sort((a, b) => parseInt(a.size_value) - parseInt(b.size_value));
            
            sizes.forEach(size => {
                const li = document.createElement('li');
                li.innerHTML = `<a class="dropdown-item" href="#" data-size="${size.size_value}">EU ${size.size_value}</a>`;
                filterList.appendChild(li);
            });

            filterList.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    filterList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active', 'fw-bold'));
                    this.classList.add('active', 'fw-bold');
                    currentSize = this.dataset.size;
                    document.getElementById('btn-filter-ukuran').innerHTML = `<i class="fa-solid fa-ruler-combined me-1"></i> ${this.textContent}`;
                    renderProducts();
                });
            });
        })
        .catch(console.error);
}

function fetchCategoriesForFilter() {
    fetchWithAuth('http://localhost:5000/api/categories')
        .then(res => res.json())
        .then(categories => {
            const filterList = document.getElementById('kategori-filter-list');
            filterList.innerHTML = '<li><a class="dropdown-item active fw-bold" href="#" data-category="all">Semua Kategori</a></li>';
            categories.forEach(cat => {
                const li = document.createElement('li');
                li.innerHTML = `<a class="dropdown-item" href="#" data-category="${cat.name}">${cat.name}</a>`;
                filterList.appendChild(li);
            });

            filterList.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    filterList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active', 'fw-bold'));
                    this.classList.add('active', 'fw-bold');
                    currentCategory = this.dataset.category;
                    document.getElementById('btn-filter-kategori').innerHTML = `<i class="fa-solid fa-filter me-1"></i> ${this.textContent}`;
                    renderProducts();
                });
            });
        })
        .catch(console.error);
}

function fetchBrandsForFilter() {
    fetchWithAuth('http://localhost:5000/api/brands')
        .then(res => res.json())
        .then(brands => {
            const filterList = document.getElementById('merek-filter-list');
            filterList.innerHTML = '<li><a class="dropdown-item active fw-bold" href="#" data-brand="all">Semua Merek</a></li>';
            brands.forEach(brand => {
                const li = document.createElement('li');
                li.innerHTML = `<a class="dropdown-item" href="#" data-brand="${brand.name}">${brand.name}</a>`;
                filterList.appendChild(li);
            });

            filterList.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    filterList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active', 'fw-bold'));
                    this.classList.add('active', 'fw-bold');
                    currentBrand = this.dataset.brand;
                    document.getElementById('btn-filter-merek').innerHTML = `<i class="fa-solid fa-copyright me-1"></i> ${this.textContent}`;
                    renderProducts();
                });
            });
        })
        .catch(console.error);
}

function setupSortHandlers() {
    const sortList = document.getElementById('sort-list');
    sortList.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            sortList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active', 'fw-bold'));
            this.classList.add('active', 'fw-bold');
            currentSort = this.dataset.sort;
            document.getElementById('btn-sort').innerHTML = `<i class="fa-solid fa-arrow-down-wide-short me-1"></i> ${this.textContent}`;
            renderProducts();
        });
    });
}

function setupSearchHandler() {
    const searchInput = document.getElementById('search-input');
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            searchQuery = this.value.trim().toLowerCase();
            renderProducts();
        }, 300);
    });
}

function renderProducts() {
    const container = document.getElementById('product-container');
    container.innerHTML = '';

    let filtered = allProducts;

    // Filter by category
    if (currentCategory !== 'all') {
        filtered = filtered.filter(p => p.category === currentCategory);
    }

    // Filter by brand
    if (currentBrand !== 'all') {
        filtered = filtered.filter(p => p.brand === currentBrand);
    }

    // Filter by size
    if (currentSize !== 'all') {
        filtered = filtered.filter(p => p.variants && p.variants.some(v => v.Size.size_value == currentSize));
    }

    // Filter by search query
    if (searchQuery) {
        filtered = filtered.filter(p => 
            (p.name && p.name.toLowerCase().includes(searchQuery)) ||
            (p.brand && p.brand.toLowerCase().includes(searchQuery)) ||
            (p.category && p.category.toLowerCase().includes(searchQuery))
        );
    }

    // Sort
    if (currentSort === 'price-high') {
        filtered.sort((a, b) => b.price - a.price);
    } else if (currentSort === 'price-low') {
        filtered.sort((a, b) => a.price - b.price);
    } else if (currentSort === 'stock-high') {
        filtered.sort((a, b) => b.stock - a.stock);
    } else if (currentSort === 'stock-low') {
        filtered.sort((a, b) => a.stock - b.stock);
    }

    if(filtered.length === 0) {
        container.innerHTML = '<div class="col-12 text-center text-muted py-5"><h4>Tidak ada sepatu ditemukan.</h4></div>';
        return;
    }

    filtered.forEach(product => {
        let iconClass = 'fa-shoe-prints';
        let badgeClass = 'bg-secondary';
        let categoryName = product.category || 'Lainnya';
        
        if (product.category && product.category.toLowerCase() === 'sneakers') {
            iconClass = 'fa-shoe-prints';
            badgeClass = 'bg-secondary';
        } else if (product.category === 'Boots') {
            iconClass = 'fa-boot';
            badgeClass = 'bg-info text-dark';
        } else if (product.category === 'Running') {
            iconClass = 'fa-person-running';
            badgeClass = 'bg-warning text-dark';
        }

        const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(product.price);

        let imageContent = `<i class="fa-solid ${iconClass} fa-4x text-muted opacity-25"></i>`;
        if (product.imageUrl) {
            imageContent = `<img src="${product.imageUrl}" alt="${product.name}" class="w-100 h-100 rounded-top-4" style="object-fit: cover;">`;
        }

        const brandHtml = product.brand ? `<span class="badge bg-dark text-white rounded-pill px-3 py-1 me-1">${product.brand}</span>` : '';

        // Generate size labels
        let sizeLabels = '';
        if (product.variants && product.variants.length > 0) {
            const sortedVariants = [...product.variants].sort((a, b) => parseInt(a.Size.size_value) - parseInt(b.Size.size_value));
            sizeLabels = sortedVariants.map(v => {
                const badgeClass = v.stock > 0 ? 'border border-primary text-primary' : 'border border-secondary text-secondary opacity-50';
                return `<span class="badge ${badgeClass} bg-white me-1 mb-1 size-selector" style="cursor:pointer;" onclick="updateCardStock(${product.id}, ${v.stock}, this)">EU ${v.Size.size_value}</span>`;
            }).join('');
        } else {
            sizeLabels = '<span class="text-muted small">Ukuran tidak tersedia</span>';
        }

        const cardHtml = `
            <div class="col-md-4 col-lg-3">
                <div class="card shadow-sm border-0 rounded-4 h-100 product-card">
                    <div class="bg-light rounded-top-4 d-flex align-items-center justify-content-center position-relative" style="height: 200px;">
                        ${imageContent}
                        <span class="position-absolute top-0 end-0 m-3 badge ${badgeClass} bg-opacity-75 rounded-pill px-3 py-2 shadow-sm">
                            ${categoryName}
                        </span>
                    </div>
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="mb-1">${brandHtml}</div>
                        <h5 class="fw-bold text-dark mb-2 text-truncate" title="${product.name}">${product.name}</h5>
                        <div class="mb-2">
                            ${sizeLabels}
                        </div>
                        <p class="text-primary fw-bold fs-5 mb-3 text-nowrap">${priceFormatted}</p>
                        
                        <div class="mt-auto">
                            <hr class="text-muted opacity-25 my-3">
                            <div class="d-flex justify-content-between align-items-center gap-2">
                                <span class="text-muted fw-bold fs-6 mb-0 text-nowrap" id="card-stock-${product.id}">Stok: ${product.stock} total</span>
                                <a href="/detail-produk/${product.id}" class="btn btn-light text-primary btn-sm rounded-pill fw-bold px-4 text-nowrap">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += cardHtml;
    });
}

function updateCardStock(productId, stock, element) {
    // Reset all size badges in this card
    const card = element.closest('.product-card');
    card.querySelectorAll('.size-selector').forEach(el => {
        el.classList.remove('bg-primary', 'text-white');
        el.classList.add('bg-white');
        // keep border color
    });

    // Make the clicked one active
    element.classList.remove('bg-white', 'text-primary', 'text-secondary');
    element.classList.add('bg-primary', 'text-white');

    // Update the text
    const stockDisplay = document.getElementById(`card-stock-${productId}`);
    if (stockDisplay) {
        stockDisplay.innerHTML = `Stok: ${stock}`;
    }
}

function fetchProducts() {
    const apiUrl = 'http://localhost:5000/api/catalog';
    
    fetchWithAuth(apiUrl)
        .then(response => response.json())
        .then(data => {
            allProducts = data;
            renderProducts();
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById('product-container').innerHTML = `
                <div class="col-12 text-center text-danger py-5">
                    <h4>Gagal memuat data dari backend (Express.js).</h4>
                    <p>Pastikan server Express berjalan di port 5000.</p>
                </div>
            `;
        });
}
</script>
@endsection

