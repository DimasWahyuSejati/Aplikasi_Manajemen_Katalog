@extends('layouts.app')
@section('title', 'Manajemen Merek')
@section('header_title', 'Data Merek Sepatu')

@section('content')
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0 rounded-top-4">
            <h5 class="m-0 fw-bold text-dark">Daftar Merek Produk</h5>
            <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahMerek">
                <i class="fa-solid fa-plus me-1"></i> Tambah Merek
            </button>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4">No.</th>
                        <th>Nama Merek</th>
                        <th class="text-center">Total Produk</th>
                        <th class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0" id="merek-list">
                    <tr><td colspan="4" class="text-center py-4">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('modals')
    <div class="modal fade" id="modalTambahMerek" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold">Tambah Merek Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-tambah-merek">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Merek</label>
                            <input type="text" id="merek-name" class="form-control" placeholder="Contoh: Nike" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Deskripsi (Opsional)</label>
                            <textarea id="merek-desc" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4">
                        <button type="button" class="btn btn-light fw-bold rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="btn-submit-merek" class="btn btn-primary fw-bold rounded-pill px-4">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetailMerek" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4 border-0">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold">Detail Merek: <span id="detail-merek-title"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-muted">
                                <tr>
                                    <th class="ps-4">Gambar</th>
                                    <th>Nama Model</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th class="pe-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0" id="detail-merek-list">
                                <tr><td colspan="6" class="text-center text-muted py-4">Memuat produk...</td></tr>
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
let globalBrands = [];

document.addEventListener('DOMContentLoaded', function() {
    fetchBrands();
});

function fetchBrands() {
    fetchWithAuth('http://localhost:5000/api/brands')
        .then(response => response.json())
        .then(data => {
            globalBrands = data;
            const tbody = document.getElementById('merek-list');
            tbody.innerHTML = '';
            
            if(data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Belum ada merek yang ditambahkan.</td></tr>';
                return;
            }

            data.forEach((brand, index) => {
                const tr = document.createElement('tr');
                
                let deleteBtnHtml = '';
                if (brand.count === 0) {
                    deleteBtnHtml = `
                        <button class="btn btn-light text-danger btn-sm fw-bold rounded-pill px-3" onclick="konfirmasiHapusMerek(${brand.id})">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </button>
                    `;
                }

                tr.innerHTML = `
                    <td class="ps-4 text-muted fw-bold">${index + 1}</td>
                    <td class="fw-bold text-dark">${brand.name}</td>
                    <td class="text-center">
                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill border border-info border-opacity-25">${brand.count} Produk</span>
                    </td>
                    <td class="text-center pe-4 text-nowrap">
                        <button class="btn btn-light text-primary btn-sm fw-bold rounded-pill px-3 me-1" onclick="tampilkanDetailMerek('${brand.name}')">
                            <i class="fa-solid fa-eye"></i> Detail
                        </button>
                        ${deleteBtnHtml}
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            document.getElementById('merek-list').innerHTML = `<tr><td colspan="4" class="text-center py-4 text-danger">Gagal memuat data merek.</td></tr>`;
        });
}

window.tampilkanDetailMerek = function(brandName) {
    const brand = globalBrands.find(b => b.name === brandName);
    if (!brand) return;
    
    document.getElementById('detail-merek-title').innerText = brand.name;
    const tbody = document.getElementById('detail-merek-list');
    tbody.innerHTML = '';
    
    if (brand.products && brand.products.length > 0) {
        brand.products.forEach(p => {
            const tr = document.createElement('tr');
            
            let imgHtml = `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;"><i class="fa-solid fa-shoe-prints text-muted opacity-50"></i></div>`;
            if (p.imageUrl) {
                imgHtml = `<img src="${p.imageUrl}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">`;
            }

            const priceFormatted = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(p.price);

            let variantsHtml = '<span class="text-muted small">Tidak ada data ukuran</span>';
            if (p.variants && p.variants.length > 0) {
                variantsHtml = p.variants
                    .sort((a, b) => parseInt(a.Size.size_value) - parseInt(b.Size.size_value))
                    .map(v => {
                        const badgeColor = v.stock > 0 ? 'bg-primary' : 'bg-secondary bg-opacity-50';
                        return `<span class="badge ${badgeColor} me-2 mb-2 p-2">EU ${v.Size.size_value} <span class="badge bg-white text-dark ms-1 rounded-pill">${v.stock}</span></span>`;
                    }).join('');
            }

            // Main Row
            tr.style.cursor = 'pointer';
            tr.onclick = function(e) {
                if (e.target.closest('a') || e.target.closest('button')) return;
                const collapseEl = document.getElementById(`collapse-merek-product-${p.id}`);
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl);
                bsCollapse.toggle();
            };
            tr.innerHTML = `
                <td class="ps-4">${imgHtml}</td>
                <td class="fw-bold text-dark">
                    ${p.name}
                    <i class="fa-solid fa-chevron-down ms-2 text-muted" style="font-size: 0.8rem;"></i>
                </td>
                <td><span class="badge bg-secondary bg-opacity-25 text-secondary px-3 py-2 rounded-pill">${p.category}</span></td>
                <td class="fw-bold">${priceFormatted}</td>
                <td><span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded-pill">${p.stock} total</span></td>
                <td class="pe-4 text-center">
                    <a href="/detail-produk/${p.id}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Lihat</a>
                </td>
            `;
            tbody.appendChild(tr);

            // Collapse Row
            const trCollapse = document.createElement('tr');
            trCollapse.innerHTML = `
                <td colspan="6" class="p-0 border-0">
                    <div class="collapse" id="collapse-merek-product-${p.id}">
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
    } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Tidak ada produk dalam merek ini.</td></tr>';
    }
    
    const modalEl = document.getElementById('modalDetailMerek');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

document.getElementById('form-tambah-merek').addEventListener('submit', function(e) {
    e.preventDefault();
    const btnSubmit = document.getElementById('btn-submit-merek');
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = 'Menyimpan...';

    const data = {
        name: document.getElementById('merek-name').value,
        description: document.getElementById('merek-desc').value
    };

    fetchWithAuth('http://localhost:5000/api/brands', {
        method: 'POST',
        body: JSON.stringify(data)
    })
    .then(response => {
        if(!response.ok) throw new Error('Merek sudah ada atau input tidak valid');
        return response.json();
    })
    .then(result => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalTambahMerek')) || new bootstrap.Modal(document.getElementById('modalTambahMerek'));
        modal.hide();
        document.getElementById('form-tambah-merek').reset();
        fetchBrands();
        if (typeof Swal !== 'undefined') {
            Swal.fire('Berhasil!', 'Merek ditambahkan.', 'success');
        }
    })
    .catch(error => {
        alert(error.message);
    })
    .finally(() => {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = 'Simpan';
    });
});

window.konfirmasiHapusMerek = function(id) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Merek?',
            text: "Merek akan dihapus dari daftar.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                hapusMerek(id);
            }
        });
    } else {
        if(confirm('Hapus merek?')) hapusMerek(id);
    }
}

function hapusMerek(id) {
    fetchWithAuth('http://localhost:5000/api/brands/' + id, {
        method: 'DELETE'
    })
    .then(res => {
        if(!res.ok) throw new Error('Gagal menghapus');
        fetchBrands();
        if (typeof Swal !== 'undefined') Swal.fire('Terhapus!', 'Merek telah dihapus.', 'success');
    })
    .catch(err => alert(err.message));
}
</script>
@endsection
