@extends('layouts.app')
@section('title', 'Manajemen Kategori')
@section('header_title', 'Data Kategori Sepatu')

@section('content')
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0 rounded-top-4">
            <h5 class="m-0 fw-bold text-dark">Daftar Kategori Produk</h5>
            <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
                <i class="fa-solid fa-plus me-1"></i> Tambah Kategori
            </button>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Nama Kategori</th>
                        <th class="text-center">Total Produk</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0" id="kategori-list">
                    <tr><td colspan="4" class="text-center py-4">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-tambah-kategori">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Kategori</label>
                            <input type="text" id="kategori-name" class="form-control" placeholder="Contoh: Sandal" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi (Opsional)</label>
                            <textarea id="kategori-desc" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btn-submit-kategori" class="btn btn-primary fw-bold rounded-pill px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailKategori" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold">Detail Kategori: <span id="detail-kategori-title"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="ps-4">Gambar</th>
                                    <th>ID</th>
                                    <th>Nama Model</th>
                                    <th>Merek</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th class="pe-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0" id="detail-kategori-list">
                                <tr><td colspan="7" class="text-center text-muted py-4">Memuat produk...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4 pe-4 pt-3">
                    <button type="button" class="btn btn-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
let globalCategories = [];

document.addEventListener('DOMContentLoaded', function() {
    fetchCategories();
});

function fetchCategories() {
    fetchWithAuth(API_ENDPOINTS.categories)
        .then(response => response.json())
        .then(data => {
            globalCategories = data;
            renderCategoryTable(data);
        })
        .catch(() => {
            document.getElementById('kategori-list').innerHTML =
                `<tr><td colspan="4" class="text-center py-4 text-danger">Gagal memuat data kategori.</td></tr>`;
        });
}

function renderCategoryTable(data) {
    const tbody = document.getElementById('kategori-list');
    tbody.innerHTML = '';

    if (data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Belum ada kategori.</td></tr>';
        return;
    }

    data.forEach((cat, index) => {
        const deleteBtnHtml = cat.count === 0
            ? `<button class="btn btn-light text-danger btn-sm fw-bold rounded-pill px-3" onclick="hapusKategori(${cat.id})"><i class="fa-solid fa-trash"></i> Hapus</button>`
            : '';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="ps-4 text-muted fw-bold">${index + 1}</td>
            <td class="fw-bold text-dark">${cat.name}</td>
            <td class="text-center">
                <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill border border-info border-opacity-25">${cat.count} Produk</span>
            </td>
            <td class="text-center pe-4 text-nowrap">
                <button class="btn btn-light text-primary btn-sm fw-bold rounded-pill px-3 me-1" onclick="tampilkanDetailKategori('${cat.name}')">
                    <i class="fa-solid fa-eye"></i> Detail
                </button>
                ${deleteBtnHtml}
            </td>
        `;
        tbody.appendChild(tr);
    });
}

/**
 * Render detail produk dalam modal untuk kategori tertentu.
 * Menggunakan helper renderVariantsBadges & renderVariantsCollapse.
 */
window.tampilkanDetailKategori = function(catName) {
    const category = globalCategories.find(c => c.name === catName);
    if (!category) return;
    
    document.getElementById('detail-kategori-title').innerText = category.name;
    const tbody = document.getElementById('detail-kategori-list');
    tbody.innerHTML = '';
    
    if (!category.products || category.products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-4">Tidak ada produk dalam kategori ini.</td></tr>';
    } else {
        category.products.forEach(p => {
            let imgHtml = `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fa-solid fa-shoe-prints text-muted opacity-50"></i></div>`;
            if (p.imageUrl) {
                imgHtml = `<img src="${p.imageUrl}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">`;
            }

            // Main Row
            const tr = document.createElement('tr');
            tr.style.cursor = 'pointer';
            tr.onclick = function(e) {
                if (e.target.closest('a') || e.target.closest('button')) return;
                const collapseEl = document.getElementById(`collapse-kategori-product-${p.id}`);
                bootstrap.Collapse.getOrCreateInstance(collapseEl).toggle();
            };
            tr.innerHTML = `
                <td class="ps-4">${imgHtml}</td>
                <td class="text-muted">#${p.id}</td>
                <td class="fw-bold text-dark">${p.name} <i class="fa-solid fa-chevron-down ms-2 text-muted" style="font-size: 0.8rem;"></i></td>
                <td><span class="badge bg-dark text-white px-3 py-2 rounded-pill">${p.brand || '-'}</span></td>
                <td class="fw-bold">${formatCurrency(p.price)}</td>
                <td><span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">${p.stock} total</span></td>
                <td class="pe-4 text-center"><a href="/detail-produk/${p.id}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Lihat</a></td>
            `;
            tbody.appendChild(tr);

            // Collapse Row
            const trCollapse = document.createElement('tr');
            trCollapse.innerHTML = renderVariantsCollapse(p.id, p.variants, 'kategori-product');
            tbody.appendChild(trCollapse);
        });
    }
    
    new bootstrap.Modal(document.getElementById('modalDetailKategori')).show();
}

// ─── Form Tambah Kategori ─────────────────────────────────────────
document.getElementById('form-tambah-kategori').addEventListener('submit', function(e) {
    e.preventDefault();
    const btnSubmit = document.getElementById('btn-submit-kategori');
    setButtonLoading(btnSubmit, true);

    const data = {
        name: document.getElementById('kategori-name').value,
        description: document.getElementById('kategori-desc').value
    };

    fetchWithAuth(API_ENDPOINTS.categories, { method: 'POST', body: JSON.stringify(data) })
        .then(response => {
            if (!response.ok) throw new Error('Kategori sudah ada atau input tidak valid');
            return response.json();
        })
        .then(() => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahKategori'));
            if (modal) modal.hide();
            document.getElementById('form-tambah-kategori').reset();
            fetchCategories();
            showSuccess('Berhasil!', 'Kategori ditambahkan.');
        })
        .catch(error => alert(error.message))
        .finally(() => setButtonLoading(btnSubmit, false, 'Simpan'));
});

// ─── Hapus Kategori ───────────────────────────────────────────────
window.hapusKategori = function(id) {
    confirmDelete({
        title: 'Hapus Kategori?',
        text: 'Kategori akan dihapus dari daftar.',
        onConfirm: () => {
            fetchWithAuth(API_ENDPOINTS.categoryById(id), { method: 'DELETE' })
                .then(res => {
                    if (!res.ok) throw new Error('Gagal menghapus');
                    fetchCategories();
                    showSuccess('Terhapus!', 'Kategori telah dihapus.');
                })
                .catch(err => alert(err.message));
        }
    });
}
</script>
@endsection
