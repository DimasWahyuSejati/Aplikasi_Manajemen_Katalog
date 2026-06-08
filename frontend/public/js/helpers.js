/**
 * Helper functions yang dipakai bersama di seluruh halaman frontend.
 * File ini harus dimuat setelah api-config.js dan sebelum script halaman.
 */

// ─── Format Currency ──────────────────────────────────────────────

/**
 * Format angka ke mata uang IDR.
 * @param {number} amount - Nilai uang
 * @returns {string} Formatted string, contoh: "Rp 1.500.000,00"
 */
function formatCurrency(amount) {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
}

// ─── Variant Badge Rendering ──────────────────────────────────────

/**
 * Render HTML badges untuk variants produk (ukuran + stok).
 * Digunakan di dashboard, katalog, kategori, dan merek.
 *
 * @param {Array} variants - Array of variant objects dari API
 * @returns {string} HTML string berisi badges
 */
function renderVariantsBadges(variants) {
  if (!variants || variants.length === 0) {
    return '<span class="text-muted small">Tidak ada data ukuran</span>';
  }

  return variants
    .sort((a, b) => parseInt(a.Size.size_value) - parseInt(b.Size.size_value))
    .map((v) => {
      const badgeColor = v.stock > 0 ? 'bg-primary' : 'bg-secondary bg-opacity-50';
      return `<span class="badge ${badgeColor} me-2 mb-2 p-2">EU ${v.Size.size_value} <span class="badge bg-white text-dark ms-1 rounded-pill">${v.stock}</span></span>`;
    })
    .join('');
}

/**
 * Render HTML collapse row untuk menampilkan detail stok per ukuran.
 *
 * @param {number} productId - ID produk
 * @param {Array} variants - Array of variant objects
 * @param {string} prefix - Prefix untuk collapse ID (hindari duplikasi ID di halaman yang sama)
 * @returns {string} HTML string untuk collapse row
 */
function renderVariantsCollapse(productId, variants, prefix, colspan = 7) {
  const badgesHtml = renderVariantsBadges(variants);
  return `
    <td colspan="${colspan}" class="p-0 border-0">
      <div class="collapse" id="collapse-${prefix}-${productId}">
        <div class="p-3 bg-light d-flex align-items-center gap-3 border-bottom">
          <div class="fw-bold text-muted small"><i class="fa-solid fa-shoe-prints me-1"></i> Rincian Stok Ukuran:</div>
          <div class="d-flex flex-wrap flex-grow-1 align-items-center mt-2">
            ${badgesHtml}
          </div>
        </div>
      </div>
    </td>
  `;
}

// ─── SweetAlert Confirmation ──────────────────────────────────────

/**
 * Tampilkan dialog konfirmasi hapus menggunakan SweetAlert (atau fallback ke confirm).
 *
 * @param {Object} options - Opsi dialog
 * @param {string} options.title - Judul dialog
 * @param {string} options.text - Teks deskripsi
 * @param {Function} options.onConfirm - Callback saat user mengkonfirmasi
 */
function confirmDelete({ title, text, onConfirm }) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: title || 'Apakah Anda yakin?',
      text: text || 'Data yang dihapus tidak dapat dikembalikan!',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal',
    }).then((result) => {
      if (result.isConfirmed) onConfirm();
    });
  } else {
    if (confirm(text || 'Yakin hapus data ini?')) onConfirm();
  }
}

/**
 * Tampilkan notifikasi sukses menggunakan SweetAlert (atau fallback ke alert).
 *
 * @param {string} title - Judul
 * @param {string} text - Teks pesan
 * @param {Function} [onClose] - Callback saat dialog ditutup
 */
function showSuccess(title, text, onClose) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({ icon: 'success', title, text }).then(() => {
      if (onClose) onClose();
    });
  } else {
    alert(text);
    if (onClose) onClose();
  }
}

/**
 * Tampilkan notifikasi error menggunakan SweetAlert (atau fallback ke alert).
 *
 * @param {string} message - Pesan error
 */
function showError(message) {
  if (typeof Swal !== 'undefined') {
    Swal.fire({ icon: 'error', title: 'Oops...', text: message });
  } else {
    alert(message);
  }
}

// ─── Button Loading State ─────────────────────────────────────────

/**
 * Toggle loading state pada tombol submit.
 *
 * @param {HTMLElement} btn - Elemen button
 * @param {boolean} loading - True untuk set loading, false untuk reset
 * @param {string} [originalText] - Teks asli button (diperlukan saat loading=false)
 */
function setButtonLoading(btn, loading, originalText) {
  if (loading) {
    btn.dataset.originalText = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Memproses...';
    btn.disabled = true;
  } else {
    btn.innerHTML = originalText || btn.dataset.originalText || 'Submit';
    btn.disabled = false;
  }
}

// ─── Select Dropdown Population ───────────────────────────────────

/**
 * Isi dropdown select dengan data dari API.
 *
 * @param {HTMLSelectElement} selectEl - Elemen select
 * @param {Array} items - Array data items
 * @param {string} valueKey - Key untuk value option
 * @param {string} textKey - Key untuk teks option
 * @param {string} placeholder - Teks placeholder option pertama
 */
function populateSelect(selectEl, items, valueKey, textKey, placeholder) {
  selectEl.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
  items.forEach((item) => {
    const option = document.createElement('option');
    option.value = item[valueKey];
    option.textContent = item[textKey];
    selectEl.appendChild(option);
  });
}

// ─── Size Checkbox Grid ───────────────────────────────────────────

/**
 * Render grid checkbox ukuran dengan input stok.
 * Digunakan di form tambah-produk dan edit-produk.
 *
 * @param {HTMLElement} container - Container element
 * @param {Array} sizes - Array of size objects dari API
 * @param {Array} [existingVariants] - Variants yang sudah ada (untuk mode edit)
 */
function renderSizeCheckboxes(container, sizes, existingVariants) {
  container.innerHTML = '';

  sizes.forEach((size) => {
    const div = document.createElement('div');
    div.className = 'col-md-3 col-sm-4 col-6';
    div.innerHTML = `
      <div class="form-check mb-2">
        <input class="form-check-input size-checkbox" type="checkbox" value="${size.id}" id="size-${size.id}" data-size="${size.size_value}">
        <label class="form-check-label fw-bold" for="size-${size.id}">
          Ukuran ${size.size_value}
        </label>
      </div>
      <input type="number" class="form-control form-control-sm size-stock-input d-none" id="stock-${size.id}" placeholder="Stok" min="0">
    `;
    container.appendChild(div);

    // Toggle visibility input stok saat checkbox berubah
    const checkbox = document.getElementById(`size-${size.id}`);
    const stockInput = document.getElementById(`stock-${size.id}`);
    checkbox.addEventListener('change', function () {
      if (this.checked) {
        stockInput.classList.remove('d-none');
        stockInput.required = true;
      } else {
        stockInput.classList.add('d-none');
        stockInput.required = false;
        stockInput.value = '';
      }
    });

    // Pre-fill jika ada data existing (mode edit)
    if (existingVariants) {
      const variant = existingVariants.find((v) => v.size_id === size.id);
      if (variant) {
        checkbox.checked = true;
        stockInput.value = variant.stock;
        stockInput.classList.remove('d-none');
        stockInput.required = true;
      }
    }
  });
}

/**
 * Kumpulkan data variants dari size checkboxes yang sudah di-check.
 * @returns {Array} Array of { size_id, stock }
 */
function collectVariantsFromCheckboxes() {
  const variants = [];
  document.querySelectorAll('.size-checkbox:checked').forEach((checkbox) => {
    const sizeId = checkbox.value;
    const stock = document.getElementById(`stock-${sizeId}`).value;
    variants.push({
      size_id: parseInt(sizeId),
      stock: parseInt(stock),
    });
  });
  return variants;
}
