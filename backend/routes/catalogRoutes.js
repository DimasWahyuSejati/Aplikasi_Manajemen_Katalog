const express = require('express');
const {
  getProducts,
  getProductById,
  createProduct,
  updateProduct,
  deleteProduct
} = require('../controllers/catalogController');
const { protect } = require('../middleware/authMiddleware');

const router = express.Router();

// Mengatur route untuk endpoint utama katalog '/api/catalog'
router.route('/')
  .get(getProducts)               // GET: Mengambil semua daftar produk 
  .post(protect, createProduct);  // POST: Membuat produk baru 

// Mengatur route untuk endpoint katalog dengan parameter ID '/api/catalog/:id'
router.route('/:id')
  .get(getProductById)            // GET: Mengambil detail produk berdasarkan ID
  .put(protect, updateProduct)    // PUT: Mengubah data produk yang sudah ada 
  .delete(protect, deleteProduct);// DELETE: Menghapus produk 

module.exports = router;
