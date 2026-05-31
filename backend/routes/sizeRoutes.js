const express = require('express');
const router = express.Router();
const { getSizes } = require('../controllers/sizeController');
const { protect } = require('../middleware/authMiddleware');

router.get('/', protect, getSizes);

module.exports = router;
