const express = require('express');
const { getCategories, createCategory, deleteCategory } = require('../controllers/categoryController');

const router = express.Router();

router.route('/')
  .get(getCategories)
  .post(createCategory);

router.route('/:id')
  .delete(deleteCategory);

module.exports = router;
