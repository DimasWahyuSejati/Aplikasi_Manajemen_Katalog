/**
 * Controller untuk mengelola data Kategori.
 * Menangani pembuatan, pengambilan, dan penghapusan data kategori produk.
 */
const Category = require('../models/Category');
const { asyncHandler, AppError } = require('../middleware/errorHandler');
const { findAllProductsFormatted } = require('../helpers/productHelper');

/**
 * @desc    Get all categories dengan jumlah produk terkait
 * @route   GET /api/categories
 */
const getCategories = asyncHandler(async (req, res) => {
  const categories = await Category.findAll();
  const products = await findAllProductsFormatted();

  const result = categories.map((cat) => {
    const catProducts = products.filter((p) => p.category === cat.name);
    return {
      id: cat.id,
      name: cat.name,
      description: cat.description,
      count: catProducts.length,
      products: catProducts,
    };
  });

  res.json(result);
});

/**
 * @desc    Create a new category
 * @route   POST /api/categories
 */
const createCategory = asyncHandler(async (req, res) => {
  const { name, description } = req.body;

  const categoryExists = await Category.findOne({ where: { name } });
  if (categoryExists) {
    throw new AppError('Category already exists', 400);
  }

  const category = await Category.create({ name, description });
  res.status(201).json(category);
});

/**
 * @desc    Delete a category
 * @route   DELETE /api/categories/:id
 */
const deleteCategory = asyncHandler(async (req, res) => {
  const category = await Category.findByPk(req.params.id);

  if (!category) {
    throw new AppError('Category not found', 404);
  }

  await category.destroy();
  res.json({ message: 'Category removed' });
});

module.exports = {
  getCategories,
  createCategory,
  deleteCategory,
};
