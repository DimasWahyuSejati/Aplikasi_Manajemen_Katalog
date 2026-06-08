const express = require('express');
const { getCategories, createCategory, deleteCategory } = require('../controllers/categoryController');

const router = express.Router();

// Mengatur route untuk endpoint utama kategori '/api/categories'
router.route('/')
  .get(getCategories)       // GET: Mengambil daftar semua kategori
  .post(createCategory);    // POST: Menambahkan kategori baru ke dalam database

// Mengatur route untuk endpoint dengan parameter ID '/api/categories/:id'
router.route('/:id')
  .delete(deleteCategory);  // DELETE: Menghapus kategori berdasarkan ID

module.exports = router;
