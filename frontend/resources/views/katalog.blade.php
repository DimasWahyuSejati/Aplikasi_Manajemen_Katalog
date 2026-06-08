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

/**
 * Setup generic dropdown filter handler.
 * Mengurangi duplikasi kode setup filter di setiap dropdown.
 */
function setupDropdownFilter(listId, buttonId, dataAttr, iconHtml, onSelect) {
    const filterList = document.getElementById(listId);
    filterList.querySelectorAll('.dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            filterList.querySelectorAll('.dropdown-item').forEach(i => i.classList.remove('active', 'fw-bold'));
            this.classList.add('active', 'fw-bold');
            onSelect(this.dataset[dataAttr]);
            document.getElementById(buttonId).innerHTML = `${iconHtml} ${this.textContent}`;
            renderProducts();
        });
    });
}

function fetchSizesForFilter() {
    fetchWithAuth(API_ENDPOINTS.sizes)
        .then(res => res.json())
        .then(sizes => {
            const filterList = document.getElementById('ukuran-filter-list');
            filterList.innerHTML = '<li><a class="dropdown-item active fw-bold" href="#" data-size="all">Semua Ukuran</a></li>';
            
            sizes.sort((a, b) => parseInt(a.size_value) - parseInt(b.size_value));
            sizes.forEach(size => {
                const li = document.createElement('li');
                li.innerHTML = `<a class="dropdown-item" href="#" data-size="${size.size_value}">EU ${size.size_value}</a>`;
                filterList.appendChild(li);
            });

            setupDropdownFilter('ukuran-filter-list', 'btn-filter-ukuran', 'size',
                '<i class="fa-solid fa-ruler-combined me-1"></i>',
                (val) => { currentSize = val; }
            );
        })
        .catch(console.error);
}

function fetchCategoriesForFilter() {
    fetchWithAuth(API_ENDPOINTS.categories)
        .then(res => res.json())
        .then(categories => {
            const filterList = document.getElementById('kategori-filter-list');
            filterList.innerHTML = '<li><a class="dropdown-item active fw-bold" href="#" data-category="all">Semua Kategori</a></li>';
            categories.forEach(cat => {
                const li = document.createElement('li');
                li.innerHTML = `<a class="dropdown-item" href="#" data-category="${cat.name}">${cat.name}</a>`;
                filterList.appendChild(li);
            });

            setupDropdownFilter('kategori-filter-list', 'btn-filter-kategori', 'category',
                '<i class="fa-solid fa-filter me-1"></i>',
                (val) => { currentCategory = val; }
            );
        })
        .catch(console.error);
}

function fetchBrandsForFilter() {
    fetchWithAuth(API_ENDPOINTS.brands)
        .then(res => res.json())
        .then(brands => {
            const filterList = document.getElementById('merek-filter-list');
            filterList.innerHTML = '<li><a class="dropdown-item active fw-bold" href="#" data-brand="all">Semua Merek</a></li>';
            brands.forEach(brand => {
                const li = document.createElement('li');
                li.innerHTML = `<a class="dropdown-item" href="#" data-brand="${brand.name}">${brand.name}</a>`;
                filterList.appendChild(li);
            });

            setupDropdownFilter('merek-filter-list', 'btn-filter-merek', 'brand',
                '<i class="fa-solid fa-copyright me-1"></i>',
                (val) => { currentBrand = val; }
            );
        })
        .catch(console.error);
}

function setupSortHandlers() {
    setupDropdownFilter('sort-list', 'btn-sort', 'sort',
        '<i class="fa-solid fa-arrow-down-wide-short me-1"></i>',
        (val) => { currentSort = val; }
    );
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

    let filtered = [...allProducts];

    // Apply filters
    if (currentCategory !== 'all') filtered = filtered.filter(p => p.category === currentCategory);
    if (currentBrand !== 'all') filtered = filtered.filter(p => p.brand === currentBrand);
    if (currentSize !== 'all') filtered = filtered.filter(p => p.variants && p.variants.some(v => v.Size.size_value == currentSize));
    if (searchQuery) {
        filtered = filtered.filter(p => 
            (p.name && p.name.toLowerCase().includes(searchQuery)) ||
            (p.brand && p.brand.toLowerCase().includes(searchQuery)) ||
            (p.category && p.category.toLowerCase().includes(searchQuery))
        );
    }

    // Apply sorting
    const sortMap = {
        'price-high': (a, b) => b.price - a.price,
        'price-low': (a, b) => a.price - b.price,
        'stock-high': (a, b) => b.stock - a.stock,
        'stock-low': (a, b) => a.stock - b.stock,
    };
    if (sortMap[currentSort]) filtered.sort(sortMap[currentSort]);

    if (filtered.length === 0) {
        container.innerHTML = '<div class="col-12 text-center text-muted py-5"><h4>Tidak ada sepatu ditemukan.</h4></div>';
        return;
    }

    filtered.forEach(product => {
        let iconClass = 'fa-shoe-prints';
        let badgeClass = 'bg-secondary';
        let categoryName = product.category || 'Lainnya';
        
        if (product.category === 'Boots') {
            iconClass = 'fa-boot'; badgeClass = 'bg-info text-dark';
        } else if (product.category === 'Running') {
            iconClass = 'fa-person-running'; badgeClass = 'bg-warning text-dark';
        }

        let imageContent = `<i class="fa-solid ${iconClass} fa-4x text-muted opacity-25"></i>`;
        if (product.imageUrl) {
            imageContent = `<img src="${product.imageUrl}" alt="${product.name}" class="w-100 h-100 rounded-top-4" style="object-fit: cover;">`;
        }

        const brandHtml = product.brand ? `<span class="badge bg-dark text-white rounded-pill px-3 py-1 me-1">${product.brand}</span>` : '';

        // Generate size labels
        let sizeLabels = '<span class="text-muted small">Ukuran tidak tersedia</span>';
        if (product.variants && product.variants.length > 0) {
            const sortedVariants = [...product.variants].sort((a, b) => parseInt(a.Size.size_value) - parseInt(b.Size.size_value));
            sizeLabels = sortedVariants.map(v => {
                const hasStock = v.stock > 0;
                const cls = hasStock ? 'border border-primary text-primary' : 'border border-secondary text-secondary opacity-50';
                return `<span class="badge ${cls} bg-white me-1 mb-1 size-selector" style="cursor:pointer;" onclick="updateCardStock(${product.id}, ${v.stock}, this)" data-has-stock="${hasStock}">EU ${v.Size.size_value}</span>`;
            }).join('');
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
                        <div class="mb-2">${sizeLabels}</div>
                        <p class="text-primary fw-bold fs-5 mb-3 text-nowrap">${formatCurrency(product.price)}</p>
                        
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
    const card = element.closest('.product-card');
    card.querySelectorAll('.size-selector').forEach(el => {
        el.classList.remove('bg-primary', 'text-white');
        el.classList.add('bg-white');
        
        // Restore text color based on original stock state
        if (el.dataset.hasStock === 'true') {
            el.classList.add('text-primary');
        } else {
            el.classList.add('text-secondary');
        }
    });

    element.classList.remove('bg-white', 'text-primary', 'text-secondary');
    element.classList.add('bg-primary', 'text-white');

    const stockDisplay = document.getElementById(`card-stock-${productId}`);
    if (stockDisplay) stockDisplay.innerHTML = `Stok: ${stock}`;
}

function fetchProducts() {
    fetchWithAuth(API_ENDPOINTS.catalog)
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
