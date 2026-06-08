const express = require('express');
const router = express.Router();
const { addTransaction, getTransactionHistory, getLowStockVariants } = require('../controllers/transactionController');

// PENTING: Route khusus (seperti '/low-stock') harus diletakkan sebelum route dinamis berparameter (jika ada)
// GET '/api/transactions/low-stock' - Mengambil varian produk yang stoknya di bawah batas minimal
router.get('/low-stock', getLowStockVariants);

// Mengatur route untuk endpoint utama transaksi '/api/transactions'
router.route('/')
  .get(getTransactionHistory) // GET: Mengambil riwayat log transaksi masuk/keluar
  .post(addTransaction);      // POST: Mencatat transaksi stok baru secara manual

module.exports = router;
