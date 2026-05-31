const express = require('express');
const { getBrands, createBrand, deleteBrand } = require('../controllers/brandController');

const router = express.Router();

router.route('/')
  .get(getBrands)
  .post(createBrand);

router.route('/:id')
  .delete(deleteBrand);

module.exports = router;
