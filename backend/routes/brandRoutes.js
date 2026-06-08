const express = require('express');
const { getBrands, createBrand, deleteBrand } = require('../controllers/brandController');

const router = express.Router();

// Mengatur route untuk endpoint utama '/api/brands'
router.route('/')
  .get(getBrands)       // GET: Mengambil daftar semua merek
  .post(createBrand);   // POST: Menambahkan merek baru ke dalam database

// Mengatur route untuk endpoint dengan parameter ID '/api/brands/:id'
router.route('/:id')
  .delete(deleteBrand); // DELETE: Menghapus merek berdasarkan ID

module.exports = router;
