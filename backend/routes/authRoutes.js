const express = require('express');
const { registerUser, loginUser, getUserCount } = require('../controllers/authController');
const router = express.Router();

// Route untuk mendaftarkan user baru
router.post('/register', registerUser);

// Route untuk login user dan mendapatkan token JWT
router.post('/login', loginUser);

// Route untuk mendapatkan total jumlah user untuk statistik dashboard
router.get('/count', getUserCount);

module.exports = router;
