const Product = require('../models/Product');
const ProductVariant = require('../models/ProductVariant');
const Size = require('../models/Size');

/**
 * Opsi include standar untuk query Product beserta variants dan size.
 * Gunakan ini di semua query yang membutuhkan relasi Product → Variants → Size.
 * @returns {Array} Sequelize include options
 */
const getProductIncludeOptions = () => [
  {
    model: ProductVariant,
    as: 'variants',
    include: [{ model: Size }],
  },
];

/**
 * Format satu product object dengan menambahkan field totalStock dan stock.
 * @param {Object} product - Sequelize product instance atau plain object
 * @returns {Object} Product data dengan totalStock dan stock
 */
const formatProductWithStock = (product) => {
  const productData = product.toJSON ? product.toJSON() : { ...product };

  productData.totalStock = productData.variants
    ? productData.variants.reduce((sum, variant) => sum + variant.stock, 0)
    : 0;

  // Backward compatibility untuk frontend yang masih menggunakan 'stock'
  productData.stock = productData.totalStock;

  return productData;
};

/**
 * Query semua products dengan variants & size, lalu format dengan totalStock.
 * @returns {Promise<Array>} Array of formatted product objects
 */
const findAllProductsFormatted = async () => {
  const products = await Product.findAll({
    include: getProductIncludeOptions(),
  });

  return products.map(formatProductWithStock);
};

/**
 * Query satu product by ID dengan variants & size, lalu format dengan totalStock.
 * @param {number} id - Product ID
 * @returns {Promise<Object|null>} Formatted product object atau null jika tidak ditemukan
 */
const findProductByIdFormatted = async (id) => {
  const product = await Product.findByPk(id, {
    include: getProductIncludeOptions(),
  });

  if (!product) return null;

  return formatProductWithStock(product);
};

module.exports = {
  getProductIncludeOptions,
  formatProductWithStock,
  findAllProductsFormatted,
  findProductByIdFormatted,
};
