const Brand = require('../models/Brand');
const { asyncHandler, AppError } = require('../middleware/errorHandler');
const { findAllProductsFormatted, formatProductWithStock } = require('../helpers/productHelper');

/**
 * @desc    Get all brands dengan jumlah produk terkait
 * @route   GET /api/brands
 */
const getBrands = asyncHandler(async (req, res) => {
  const brands = await Brand.findAll();
  const products = await findAllProductsFormatted();

  const result = brands.map((brand) => {
    const brandProducts = products.filter((p) => p.brand === brand.name);
    return {
      id: brand.id,
      name: brand.name,
      description: brand.description,
      count: brandProducts.length,
      products: brandProducts,
    };
  });

  res.json(result);
});

/**
 * @desc    Create a new brand
 * @route   POST /api/brands
 */
const createBrand = asyncHandler(async (req, res) => {
  const { name, description } = req.body;

  const brandExists = await Brand.findOne({ where: { name } });
  if (brandExists) {
    throw new AppError('Brand already exists', 400);
  }

  const brand = await Brand.create({ name, description });
  res.status(201).json(brand);
});

/**
 * @desc    Delete a brand
 * @route   DELETE /api/brands/:id
 */
const deleteBrand = asyncHandler(async (req, res) => {
  const brand = await Brand.findByPk(req.params.id);

  if (!brand) {
    throw new AppError('Brand not found', 404);
  }

  await brand.destroy();
  res.json({ message: 'Brand removed' });
});

module.exports = {
  getBrands,
  createBrand,
  deleteBrand,
};
