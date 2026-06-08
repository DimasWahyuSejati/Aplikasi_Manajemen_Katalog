@extends('layouts.app')
@section('title', 'Laporan Riwayat Transaksi')
@section('header_title', 'Laporan Pergerakan Stok')

@section('content')
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white py-4 d-flex justify-content-between align-items-center border-0 rounded-top-4">
            <h5 class="m-0 fw-bold text-dark">Riwayat Transaksi Stok</h5>
            <div class="d-flex gap-2">
                <select id="filter-type" class="form-select form-select-sm rounded-pill px-3 shadow-sm" style="width: auto;">
                    <option value="">Semua Tipe</option>
                    <option value="IN">Masuk (IN)</option>
                    <option value="OUT">Keluar (OUT)</option>
                </select>
                <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm" onclick="fetchTransactions()">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-muted">
                    <tr>
                        <th class="ps-4">Tanggal & Waktu</th>
                        <th>Gambar</th>
                        <th>Produk</th>
                        <th>Ukuran</th>
                        <th>Tipe</th>
                        <th>Jumlah</th>
                        <th>Alasan</th>
                        <th class="pe-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="border-top-0" id="transaction-list">
                    <tr><td colspan="6" class="text-center py-4">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchTransactions();
});

function fetchTransactions() {
    const typeFilter = document.getElementById('filter-type').value;
    let url = API_ENDPOINTS.transactions;
    if (typeFilter) {
        url += `?type=${typeFilter}`;
    }

    const tbody = document.getElementById('transaction-list');
    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></td></tr>';

    fetchWithAuth(url)
        .then(response => response.json())
        .then(data => renderTransactionsTable(data))
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-danger">Gagal memuat riwayat transaksi.</td></tr>`;
        });
}

function renderTransactionsTable(data) {
    const tbody = document.getElementById('transaction-list');
    tbody.innerHTML = '';

    if (!data || data.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat transaksi.</td></tr>';
        return;
    }

    data.forEach(t => {
        const date = new Date(t.date).toLocaleString('id-ID', {
            day: '2-digit', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });

        const isOut = t.type === 'OUT';
        const typeBadge = isOut 
            ? `<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2"><i class="fa-solid fa-arrow-down me-1"></i> OUT</span>`
            : `<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2"><i class="fa-solid fa-arrow-up me-1"></i> IN</span>`;
        
        const productName = t.ProductVariant && t.ProductVariant.Product ? t.ProductVariant.Product.name : 'Produk Dihapus';
        const brand = t.ProductVariant && t.ProductVariant.Product && t.ProductVariant.Product.brand ? ` <span class="badge bg-dark rounded-pill">${t.ProductVariant.Product.brand}</span>` : '';
        const size = t.ProductVariant && t.ProductVariant.Size ? t.ProductVariant.Size.size_value : '-';
        
        const imageUrl = t.ProductVariant && t.ProductVariant.Product && t.ProductVariant.Product.imageUrl 
            ? t.ProductVariant.Product.imageUrl 
            : 'https://via.placeholder.com/50?text=No+Image';
        const imgHtml = `<img src="${imageUrl}" alt="${productName}" class="rounded shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">`;

        const qtyPrefix = isOut ? '-' : '+';
        const qtyClass = isOut ? 'text-danger' : 'text-success';
        
        const actionHtml = t.ProductVariant && t.ProductVariant.Product 
            ? `<a href="/detail-produk/${t.ProductVariant.Product.id}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Detail</a>`
            : '-';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="ps-4 text-muted small">${date}</td>
            <td>${imgHtml}</td>
            <td class="fw-bold text-dark">${productName}${brand}</td>
            <td>EU ${size}</td>
            <td>${typeBadge}</td>
            <td class="fw-bold ${qtyClass}">${qtyPrefix}${t.quantity}</td>
            <td class="text-muted">${t.reason}</td>
            <td class="pe-4 text-center">${actionHtml}</td>
        `;
        tbody.appendChild(tr);
    });
}
</script>
@endsection
