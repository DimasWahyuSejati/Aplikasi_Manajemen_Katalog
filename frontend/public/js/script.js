

// Fungsi Hapus Data Sepatu (Untuk Dashboard & Detail Produk)
function konfirmasiHapus() {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data sepatu ini akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#e2e8f0',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: '<span style="color: #475569">Batal</span>',
        customClass: { popup: 'rounded-4' }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Terhapus!',
                text: 'Data sepatu berhasil dihapus dari katalog.',
                icon: 'success',
                customClass: { popup: 'rounded-4' }
            }).then(() => {
                if(window.location.href.indexOf("detail-produk") > -1) {
                    window.location.href = "/katalog";
                }
            });
        }
    });
}

// Fungsi Hapus Kategori (Untuk Halaman Kategori)
function konfirmasiHapusKategori() {
    Swal.fire({
        title: 'Hapus Kategori Ini?',
        text: "Kategori yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#e2e8f0',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: '<span style="color: #475569">Batal</span>',
        customClass: { popup: 'rounded-4' }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Terhapus!',
                text: 'Kategori berhasil dihapus dari sistem.',
                icon: 'success',
                customClass: { popup: 'rounded-4' }
            });
        }
    });
}