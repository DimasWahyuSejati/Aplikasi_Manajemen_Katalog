@extends('layouts.app')
@section('title', 'Transaksi Stok Baru')
@section('header_title', 'Input Transaksi Inventori')

@section('content')
    <div class="card shadow-sm border-0 mx-auto" style="max-width: 700px;">
        <div class="card-header bg-primary text-white py-3">
            <h5 class="m-0 fw-bold"><i class="fa-solid fa-cart-flatbed me-2"></i> Form Transaksi Stok</h5>
        </div>
        <div class="card-body p-4">
            <form id="form-transaksi">
                <div class="mb-3">
                    <label class="form-label fw-bold">Pilih Produk</label>
                    <select id="input-produk" class="form-select" required>
                        <option value="" selected disabled>Memuat produk...</option>
                    </select>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Varian Ukuran</label>
                        <select id="input-varian" class="form-select" required disabled>
                            <option value="" selected disabled>Pilih produk terlebih dahulu</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Stok Saat Ini</label>
                        <input type="text" id="current-stock" class="form-control bg-light" readonly placeholder="-">
                    </div>
                </div>

                <hr class="text-muted opacity-25 my-4">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tipe Transaksi</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="transType" id="typeIn" value="IN" required>
                                <label class="form-check-label text-success fw-bold" for="typeIn">
                                    Masuk (IN)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="transType" id="typeOut" value="OUT">
                                <label class="form-check-label text-danger fw-bold" for="typeOut">
                                    Keluar (OUT)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jumlah (Quantity)</label>
                        <input type="number" id="input-qty" class="form-control" min="1" placeholder="Masukkan jumlah" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Alasan Transaksi</label>
                    <select id="input-reason" class="form-select" required>
                        <option value="" selected disabled>Pilih tipe transaksi terlebih dahulu</option>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" id="btn-submit" class="btn btn-primary fw-bold py-2 shadow-sm">
                        <i class="fa-solid fa-save me-1"></i> Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
let globalProducts = [];

document.addEventListener('DOMContentLoaded', function() {
    // Load products
    fetchWithAuth(API_ENDPOINTS.catalog)
        .then(res => res.json())
        .then(products => {
            globalProducts = products;
            const selectProduk = document.getElementById('input-produk');
            selectProduk.innerHTML = '<option value="" selected disabled>-- Pilih Produk --</option>';
            products.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = `${p.name} (${p.brand || 'No Brand'})`;
                selectProduk.appendChild(opt);
            });
        })
        .catch(console.error);

    // Event listener saat produk dipilih -> load variannya
    document.getElementById('input-produk').addEventListener('change', function() {
        const prodId = this.value;
        const product = globalProducts.find(p => p.id == prodId);
        const selectVarian = document.getElementById('input-varian');
        
        selectVarian.innerHTML = '<option value="" selected disabled>-- Pilih Ukuran --</option>';
        document.getElementById('current-stock').value = '-';
        
        if (product && product.variants && product.variants.length > 0) {
            selectVarian.disabled = false;
            // Sort variants by size
            const sortedVariants = [...product.variants].sort((a, b) => parseInt(a.Size.size_value) - parseInt(b.Size.size_value));
            
            sortedVariants.forEach(v => {
                const opt = document.createElement('option');
                opt.value = v.id;
                opt.dataset.stock = v.stock;
                opt.textContent = `EU ${v.Size.size_value} (Sisa: ${v.stock})`;
                selectVarian.appendChild(opt);
            });
        } else {
            selectVarian.innerHTML = '<option value="" disabled>Tidak ada varian ukuran</option>';
            selectVarian.disabled = true;
        }
    });

    // Event listener saat varian dipilih -> tampilkan stok saat ini
    document.getElementById('input-varian').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.stock) {
            document.getElementById('current-stock').value = selectedOption.dataset.stock + ' pasang';
        }
    });

    // Validasi logic radio button dan alasan
    const reasons = {
        'IN': ['Restock dari Supplier', 'Retur dari Pelanggan'],
        'OUT': ['Penjualan Offline', 'Penjualan Online', 'Barang Rusak/Cacat']
    };

    document.getElementsByName('transType').forEach(radio => {
        radio.addEventListener('change', function() {
            const reasonSelect = document.getElementById('input-reason');
            reasonSelect.innerHTML = '<option value="" selected disabled>Pilih alasan</option>';
            
            const selectedType = this.value;
            reasons[selectedType].forEach(reason => {
                const opt = document.createElement('option');
                opt.value = reason;
                opt.textContent = reason;
                reasonSelect.appendChild(opt);
            });
        });
    });
});

// Form Submit
document.getElementById('form-transaksi').addEventListener('submit', function(e) {
    e.preventDefault();
    const btnSubmit = document.getElementById('btn-submit');
    setButtonLoading(btnSubmit, true);

    const variantId = document.getElementById('input-varian').value;
    const type = document.querySelector('input[name="transType"]:checked').value;
    const qty = document.getElementById('input-qty').value;
    const reason = document.getElementById('input-reason').value;

    const payload = {
        product_variant_id: parseInt(variantId),
        type: type,
        quantity: parseInt(qty),
        reason: reason
    };

    fetchWithAuth(API_ENDPOINTS.transactions, {
        method: 'POST',
        body: JSON.stringify(payload)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw new Error(err.message || 'Gagal menyimpan transaksi') });
        }
        return response.json();
    })
    .then(data => {
        showSuccess('Berhasil!', 'Transaksi stok berhasil dicatat.', () => {
            // Reload page or reset form
            location.reload();
        });
    })
    .catch(error => {
        showError(error.message);
        setButtonLoading(btnSubmit, false, '<i class="fa-solid fa-save me-1"></i> Simpan Transaksi');
    });
});
</script>
@endsection
