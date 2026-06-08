/**
 * Konfigurasi API terpusat.
 * Otomatis mendeteksi apakah sedang berjalan di server lokal (localhost) atau Vercel.
 */
const API_BASE_URL = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
  ? 'http://localhost:5000/api' 
  : 'https://katalog-backend.vercel.app/api';

/**
 * Mapping semua API endpoint.
 * Gunakan ini di seluruh frontend agar URL tidak hardcoded.
 *
 * @example
 * fetch(API_ENDPOINTS.catalog)            // GET semua produk
 * fetch(API_ENDPOINTS.catalogById(5))     // GET produk ID 5
 * fetch(API_ENDPOINTS.categories)         // GET semua kategori
 */
const API_ENDPOINTS = {
  // Auth
  login: `${API_BASE_URL}/auth/login`,
  register: `${API_BASE_URL}/auth/register`,
  userCount: `${API_BASE_URL}/auth/count`,

  // Catalog (Products)
  catalog: `${API_BASE_URL}/catalog`,
  catalogById: (id) => `${API_BASE_URL}/catalog/${id}`,

  // Categories
  categories: `${API_BASE_URL}/categories`,
  categoryById: (id) => `${API_BASE_URL}/categories/${id}`,

  // Brands
  brands: `${API_BASE_URL}/brands`,
  brandById: (id) => `${API_BASE_URL}/brands/${id}`,

  // Sizes
  sizes: `${API_BASE_URL}/sizes`,

  // Transactions & Reports
  transactions: `${API_BASE_URL}/transactions`,
  lowStock: (threshold = 5) => `${API_BASE_URL}/transactions/low-stock?threshold=${threshold}`,
};
