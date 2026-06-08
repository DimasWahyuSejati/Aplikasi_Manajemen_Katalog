/**
 * Controller utama untuk mengelola Katalog Produk Sepatu.
 * Menangani CRUD untuk tabel Produk beserta tabel relasinya (ProductVariants).
 */
const { asyncHandler, AppError } = require('../middleware/errorHandler');
const {
  findAllProductsFormatted,
  findProductByIdFormatted,
  getProductIncludeOptions,
} = require('../helpers/productHelper');
const Product = require('../models/Product');
const ProductVariant = require('../models/ProductVariant');
const Size = require('../models/Size');
const StockTransaction = require('../models/StockTransaction');
const { sequelize } = require('../config/db');

/**
 * @desc    Get all products dengan variants dan total stock
 * @route   GET /api/catalog
 */
const getProducts = asyncHandler(async (req, res) => {
  const products = await findAllProductsFormatted();
  res.json(products);
});

/**
 * @desc    Get single product by ID
 * @route   GET /api/catalog/:id
 */
const getProductById = asyncHandler(async (req, res) => {
  const product = await findProductByIdFormatted(req.params.id);

  if (!product) {
    throw new AppError('Product not found', 404);
  }

  res.json(product);
});

/**
 * @desc    Create a new product dengan variants
 * @route   POST /api/catalog
 */
const createProduct = asyncHandler(async (req, res) => {
  const transaction = await sequelize.transaction();

  const { name, brand, category, color, price, description, imageUrl, variants } = req.body;

  // Buat produk utama
  const product = await Product.create(
    { name, brand, category, color, price, description, imageUrl },
    { transaction }
  );

  // Buat variants jika ada dan log transaksi
  if (variants && Array.isArray(variants)) {
    const createdVariants = await ProductVariant.bulkCreate(
      variants.map((v) => ({
        product_id: product.id,
        size_id: v.size_id,
        stock: v.stock || 0,
      })), 
      { transaction, returning: true }
    );

    // Log stok awal
    const logs = createdVariants.filter(v => v.stock > 0).map(v => ({
      product_variant_id: v.id,
      type: 'IN',
      quantity: v.stock,
      reason: 'Stok Awal (Penyesuaian Manual)'
    }));
    if (logs.length > 0) {
      await StockTransaction.bulkCreate(logs, { transaction });
    }
  }

  await transaction.commit();

  // Ambil kembali product dengan relasi lengkap
  const createdProduct = await Product.findByPk(product.id, {
    include: getProductIncludeOptions(),
  });

  res.status(201).json(createdProduct);
});

/**
 * @desc    Update existing product dan variants
 * @route   PUT /api/catalog/:id
 */
const updateProduct = asyncHandler(async (req, res) => {
  const transaction = await sequelize.transaction();

  const product = await Product.findByPk(req.params.id);
  if (!product) {
    await transaction.rollback();
    throw new AppError('Product not found', 404);
  }

  const { name, brand, category, color, price, description, imageUrl, variants } = req.body;

  await product.update(
    { name, brand, category, color, price, description, imageUrl },
    { transaction }
  );

  // Update variants tanpa destroy agar riwayat StockTransaction tidak error/hilang
  if (variants && Array.isArray(variants)) {
    for (const v of variants) {
      // Cari varian yang sudah ada
      let variant = await ProductVariant.findOne({ 
        where: { product_id: product.id, size_id: v.size_id },
        transaction 
      });

      const newStock = v.stock || 0;

      if (variant) {
        // Jika ada perubahan stok, catat log transaksinya
        if (variant.stock !== newStock) {
          const diff = newStock - variant.stock;
          await StockTransaction.create({
            product_variant_id: variant.id,
            type: diff > 0 ? 'IN' : 'OUT',
            quantity: Math.abs(diff),
            reason: 'Update Produk (Penyesuaian Manual)'
          }, { transaction });
          
          await variant.update({ stock: newStock }, { transaction });
        }
      } else {
        // Jika ini varian ukuran baru untuk produk ini
        variant = await ProductVariant.create({
          product_id: product.id,
          size_id: v.size_id,
          stock: newStock
        }, { transaction });

        if (newStock > 0) {
          await StockTransaction.create({
            product_variant_id: variant.id,
            type: 'IN',
            quantity: newStock,
            reason: 'Stok Awal (Penyesuaian Manual)'
          }, { transaction });
        }
      }
    }
  }

  await transaction.commit();

  const updatedProduct = await Product.findByPk(product.id, {
    include: getProductIncludeOptions(),
  });

  res.json(updatedProduct);
});

/**
 * @desc    Delete a product
 * @route   DELETE /api/catalog/:id
 */
const deleteProduct = asyncHandler(async (req, res) => {
  const product = await Product.findByPk(req.params.id);

  if (!product) {
    throw new AppError('Product not found', 404);
  }

  await product.destroy();
  res.json({ message: 'Product removed' });
});

module.exports = {
  getProducts,
  getProductById,
  createProduct,
  updateProduct,
  deleteProduct,
};
