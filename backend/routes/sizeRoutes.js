const express = require('express');
const router = express.Router();
const { getSizes } = require('../controllers/sizeController');
const { protect } = require('../middleware/authMiddleware');

// GET '/api/sizes'
// Mengambil semua daftar ukuran sepatu yang tersedia di database
// Endpoint ini dilindungi oleh middleware 'protect' (membutuhkan token JWT)
router.get('/', protect, getSizes);

module.exports = router;
