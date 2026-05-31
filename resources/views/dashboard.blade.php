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
                        <h3 class="fw-bold text-dark mb-0">124 Pasang</h3>
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
                        <h3 class="fw-bold text-dark mb-0">8 Tipe</h3>
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
                        <h3 class="fw-bold text-dark mb-0">3 Admin</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fa-solid fa-users fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0 rounded-top-4">
            <h5 class="m-0 fw-bold text-dark">Data Sepatu Terbaru</h5>
            <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fa-solid fa-plus me-1"></i> Tambah Sepatu
            </button>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4">ID</th>
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
@section('modals')
    <!-- Modals are kept here, but we will redirect to /tambah-produk for adding -->
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchDashboardProducts();
});

function fetchDashboardProducts() {
    // Fetch user count
    fetchWithAuth('http://localhost:5000/api/auth/count')
        .then(res => res.json())
        .then(countData => {
            const totalElements = document.querySelectorAll('.fw-bold.text-dark.mb-0');
            if(totalElements.length >= 3) totalElements[2].innerText = countData.count + ' Admin';
        })
        .catch(console.error);

    fetchWithAuth('http://localhost:5000/api/categories')
        .then(res => res.json())
        .then(categoriesData => {
            const totalElements = document.querySelectorAll('.fw-bold.text-dark.mb-0');
            if(totalElements.length >= 2) totalElements[1].innerText = categoriesData.length + ' Tipe';
        })
        .catch(console.error);

    fetchWithAuth('http://localhost:5000/api/catalog')
        .then(response => response.json())
        .then(data => {
            // Update Dashboard Counters
            const totalElements = document.querySelectorAll('.fw-bold.text-dark.mb-0');
            if(totalElements.length >= 1) totalElements[0].innerText = data.length + ' Pasang';
            
            // Render Table
            const tbody = document.getElementById('dashboard-product-list');
            tbody.innerHTML = '';
            
            if(data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data sepatu.</td></tr>';
                return;
            }

            data.forEach(product => {
                const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(product.price);
                
                // Build variants badge HTML
                let variantsHtml = '<span class="text-muted small">Tidak ada data ukuran</span>';
                if (product.variants && product.variants.length > 0) {
                    variantsHtml = product.variants
                        .sort((a, b) => parseInt(a.Size.size_value) - parseInt(b.Size.size_value))
                        .map(v => {
                            const badgeColor = v.stock > 0 ? 'bg-primary' : 'bg-secondary bg-opacity-50';
                            return `<span class="badge ${badgeColor} me-2 mb-2 p-2">EU ${v.Size.size_value} <span class="badge bg-white text-dark ms-1 rounded-pill">${v.stock}</span></span>`;
                        }).join('');
                }

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
                    <td class="fw-bold text-dark">
                        ${product.name}
                        <i class="fa-solid fa-chevron-down ms-2 text-muted" style="font-size: 0.8rem;"></i>
                    </td>
                    <td><span class="badge bg-dark text-white px-3 py-2 rounded-pill">${product.brand || '-'}</span></td>
                    <td><span class="badge bg-secondary bg-opacity-25 text-secondary px-3 py-2 rounded-pill">${product.category}</span></td>
                    <td class="fw-bold">${priceFormatted}</td>
                    <td><span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">${product.stock} total</span></td>
                    <td class="text-center pe-4 text-nowrap">
                        <a href="/detail-produk/${product.id}" class="btn btn-light text-primary btn-sm fw-bold rounded-pill px-3 me-1">
                            <i class="fa-solid fa-eye"></i> Detail
                        </a>
                        <button class="btn btn-light text-danger btn-sm fw-bold rounded-pill px-3" onclick="konfirmasiHapus(${product.id})">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    </td>
                `;
                tbody.appendChild(tr);

                // Collapse Row
                const trCollapse = document.createElement('tr');
                trCollapse.innerHTML = `
                    <td colspan="7" class="p-0 border-0">
                        <div class="collapse" id="collapse-product-${product.id}">
                            <div class="p-3 bg-light d-flex align-items-center gap-3 border-bottom">
                                <div class="fw-bold text-muted small"><i class="fa-solid fa-shoe-prints me-1"></i> Rincian Stok Ukuran:</div>
                                <div class="d-flex flex-wrap flex-grow-1 align-items-center mt-2">
                                    ${variantsHtml}
                                </div>
                            </div>
                        </div>
                    </td>
                `;
                tbody.appendChild(trCollapse);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('dashboard-product-list').innerHTML = `<tr><td colspan="7" class="text-center py-4 text-danger">Gagal memuat data.</td></tr>`;
        });
}

// Ganti aksi tombol "Tambah Sepatu" di dashboard untuk arahkan ke halaman tambah-produk
document.querySelector('[data-bs-target="#modalTambah"]').removeAttribute('data-bs-toggle');
document.querySelector('[data-bs-target="#modalTambah"]').removeAttribute('data-bs-target');
document.querySelector('.btn-primary.btn-sm.rounded-pill').addEventListener('click', function() {
    window.location.href = "{{ url('/tambah-produk') }}";
});

window.konfirmasiHapus = function(id) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                prosesHapus(id);
            }
        })
    } else {
        if(confirm("Apakah Anda yakin ingin menghapus data ini?")) {
            prosesHapus(id);
        }
    }
}

function prosesHapus(id) {
    fetchWithAuth('http://localhost:5000/api/catalog/' + id, {
        method: 'DELETE'
    })
    .then(response => {
        if(!response.ok) throw new Error('Gagal menghapus data');
        return response.json();
    })
    .then(data => {
        if (typeof Swal !== 'undefined') {
            Swal.fire('Terhapus!', 'Data sepatu telah dihapus.', 'success');
        } else {
            alert('Data terhapus!');
        }
        fetchDashboardProducts(); // Refresh tabel
    })
    .catch(error => {
        alert(error.message);
    });
}
</script>
@endsection

