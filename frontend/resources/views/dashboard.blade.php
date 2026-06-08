@extends('layouts.app')
@section('title', 'Dashboard Katalog Sepatu')
@section('header_title', 'Dashboard Admin')

@section('content')
    <div class="row mb-4 g-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0 p-3 bg-white rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-1">TOTAL SEPATU</h6>
                        <h3 class="fw-bold text-dark mb-0" id="stat-total-sepatu">- Pasang</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-box-open fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0 p-3 bg-white rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-1">KATEGORI</h6>
                        <h3 class="fw-bold text-dark mb-0" id="stat-total-kategori">- Tipe</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-tags fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm h-100 border-0 p-3 bg-white rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted fw-bold mb-1">USER AKTIF</h6>
                        <h3 class="fw-bold text-dark mb-0" id="stat-total-admin">- Admin</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-users fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Peringatan Stok Menipis -->
    <div class="card shadow-sm border-0 rounded-4 mb-4 border-start border-warning border-5 d-none" id="low-stock-card">
        <div class="card-body p-4">
            <h5 class="fw-bold text-warning mb-3"><i class="fa-solid fa-triangle-exclamation me-2"></i> Peringatan Stok Menipis</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Produk</th>
                            <th>Merek</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Ukuran</th>
                            <th>Sisa Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="low-stock-list">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0 rounded-top-4">
            <h5 class="m-0 fw-bold text-dark">Data Sepatu Terbaru</h5>
            <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" id="btn-tambah-sepatu">
                <i class="fa-solid fa-plus me-1"></i> Tambah Sepatu
            </button>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Gambar</th>
                        <th>Nama Model</th>
                        <th>Merek</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0" id="dashboard-product-list">
                    <tr><td colspan="7" class="text-center py-4">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchDashboardData();

    // Redirect tombol "Tambah Sepatu" ke halaman tambah
    document.getElementById('btn-tambah-sepatu').addEventListener('click', function() {
        window.location.href = "{{ url('/tambah-produk') }}";
    });
});

function fetchDashboardData() {
    // Fetch user count
    fetchWithAuth(API_ENDPOINTS.userCount)
        .then(res => res.json())
        .then(data => {
            document.getElementById('stat-total-admin').innerText = data.count + ' Admin';
        })
        .catch(console.error);

    // Fetch categories count
    fetchWithAuth(API_ENDPOINTS.categories)
        .then(res => res.json())
        .then(data => {
            document.getElementById('stat-total-kategori').innerText = data.length + ' Tipe';
        })
        .catch(console.error);

    // Fetch products & render table
    fetchWithAuth(API_ENDPOINTS.catalog)
        .then(response => response.json())
        .then(data => {
            document.getElementById('stat-total-sepatu').innerText = data.length + ' Pasang';
            renderDashboardTable(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('dashboard-product-list').innerHTML =
                `<tr><td colspan="7" class="text-center py-4 text-danger">Gagal memuat data.</td></tr>`;
        });

    // Fetch low stock alerts
    fetchWithAuth(API_ENDPOINTS.lowStock(5))
        .then(res => res.json())
        .then(data => renderLowStockAlert(data))
        .catch(console.error);
}

function renderLowStockAlert(variants) {
    if (!variants || variants.length === 0) return;

    const card = document.getElementById('low-stock-card');
    const tbody = document.getElementById('low-stock-list');
    tbody.innerHTML = '';

    variants.forEach(v => {
        const imageUrl = v.Product.imageUrl || 'https://via.placeholder.com/50?text=No+Image';
        const imgHtml = `<img src="${imageUrl}" alt="${v.Product.name}" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">`;
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="text-muted small">#${v.Product.id}</td>
            <td>${imgHtml}</td>
            <td class="fw-bold text-dark">${v.Product.name}</td>
            <td><span class="badge bg-dark text-white rounded-pill">${v.Product.brand || '-'}</span></td>
            <td><span class="badge bg-secondary bg-opacity-25 text-secondary px-3 py-2 rounded-pill">${v.Product.category || '-'}</span></td>
            <td class="fw-bold">${formatCurrency(v.Product.price)}</td>
            <td><span class="badge border border-primary text-primary bg-white rounded-pill">EU ${v.Size.size_value}</span></td>
            <td><span class="badge bg-danger text-white rounded-pill">${v.stock} total</span></td>
            <td>
                <a href="/detail-produk/${v.Product.id}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Detail</a>
            </td>
        `;
        tbody.appendChild(tr);
    });

    card.classList.remove('d-none');
}

function renderDashboardTable(data) {
    const tbody = document.getElementById('dashboard-product-list');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data sepatu.</td></tr>';
        return;
    }

    data.forEach(product => {
        const variantsHtml = renderVariantsBadges(product.variants);
        
        const imageUrl = product.imageUrl || 'https://via.placeholder.com/50?text=No+Image';
        const imgHtml = `<img src="${imageUrl}" alt="${product.name}" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">`;

        // Main Row
        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.onclick = function(e) {
            if (e.target.closest('a') || e.target.closest('button')) return;
            const collapseEl = document.getElementById(`collapse-product-${product.id}`);
            const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl);
            bsCollapse.toggle();
        };
        tr.innerHTML = `
            <td class="ps-4 text-muted">#${product.id}</td>
            <td>${imgHtml}</td>
            <td class="fw-bold text-dark">
                ${product.name}
                <i class="fa-solid fa-chevron-down ms-2 text-muted" style="font-size: 0.8rem;"></i>
            </td>
            <td><span class="badge bg-dark text-white px-3 py-2 rounded-pill">${product.brand || '-'}</span></td>
            <td><span class="badge bg-secondary bg-opacity-25 text-secondary px-3 py-2 rounded-pill">${product.category}</span></td>
            <td class="fw-bold">${formatCurrency(product.price)}</td>
            <td><span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">${product.stock} total</span></td>
            <td class="text-center pe-4 text-nowrap">
                <a href="/detail-produk/${product.id}" class="btn btn-light text-primary btn-sm fw-bold rounded-pill px-3 me-1">
                    <i class="fa-solid fa-eye"></i> Detail
                </a>
                <button class="btn btn-light text-danger btn-sm fw-bold rounded-pill px-3" onclick="hapusProduk(${product.id})">
                    <i class="fa-solid fa-trash"></i> Hapus
                </button>
            </td>
        `;
        tbody.appendChild(tr);

        // Collapse Row
        const trCollapse = document.createElement('tr');
        trCollapse.innerHTML = renderVariantsCollapse(product.id, product.variants, 'product', 8);
        tbody.appendChild(trCollapse);
    });
}

window.hapusProduk = function(id) {
    confirmDelete({
        title: 'Apakah Anda yakin?',
        text: 'Data yang dihapus tidak dapat dikembalikan!',
        onConfirm: () => {
            fetchWithAuth(API_ENDPOINTS.catalogById(id), { method: 'DELETE' })
                .then(response => {
                    if (!response.ok) throw new Error('Gagal menghapus data');
                    return response.json();
                })
                .then(() => {
                    showSuccess('Terhapus!', 'Data sepatu telah dihapus.');
                    fetchDashboardData();
                })
                .catch(error => alert(error.message));
        }
    });
}
</script>
@endsection
