const express = require('express');
const { registerUser, loginUser, getUserCount } = require('../controllers/authController');
const router = express.Router();

router.post('/register', registerUser);
router.post('/login', loginUser);
router.get('/count', getUserCount);

module.exports = router;
