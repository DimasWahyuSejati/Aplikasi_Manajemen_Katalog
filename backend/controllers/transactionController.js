const { sequelize } = require('../config/db');
const { asyncHandler, AppError } = require('../middleware/errorHandler');
const StockTransaction = require('../models/StockTransaction');
const ProductVariant = require('../models/ProductVariant');
const Product = require('../models/Product');
const Size = require('../models/Size');
const { Op } = require('sequelize');

/**
 * @desc    Mencatat transaksi stok masuk/keluar dan update stok varian
 * @route   POST /api/transactions
 */
const addTransaction = asyncHandler(async (req, res) => {
  const { product_variant_id, type, quantity, reason } = req.body;

  if (!['IN', 'OUT'].includes(type)) {
    throw new AppError('Tipe transaksi harus IN atau OUT', 400);
  }

  if (!quantity || quantity <= 0) {
    throw new AppError('Quantity harus lebih besar dari 0', 400);
  }

  // Gunakan database transaction untuk mencegah race conditions
  const transaction = await sequelize.transaction();

  try {
    // 1. Cari varian produk (lock for update jika menggunakan dialek yg mendukung)
    const variant = await ProductVariant.findByPk(product_variant_id, { transaction });
    
    if (!variant) {
      throw new AppError('Varian produk tidak ditemukan', 404);
    }

    // 2. Hitung stok baru
    let newStock = variant.stock;
    if (type === 'IN') {
      newStock += parseInt(quantity);
    } else if (type === 'OUT') {
      newStock -= parseInt(quantity);
      if (newStock < 0) {
        throw new AppError(`Stok tidak mencukupi. Stok saat ini: ${variant.stock}`, 400);
      }
    }

    // 3. Update stok di varian
    await variant.update({ stock: newStock }, { transaction });

    // 4. Catat riwayat ke StockTransactions
    const stockLog = await StockTransaction.create({
      product_variant_id,
      type,
      quantity,
      reason
    }, { transaction });

    // Commit jika semua berhasil
    await transaction.commit();

    // Fetch related data untuk response
    const completeLog = await StockTransaction.findByPk(stockLog.id, {
      include: [{
        model: ProductVariant,
        include: [Product, Size]
      }]
    });

    res.status(201).json({
      message: 'Transaksi berhasil dicatat',
      transaction: completeLog,
      new_stock: newStock
    });
  } catch (error) {
    await transaction.rollback();
    throw error;
  }
});

/**
 * @desc    Mengambil riwayat transaksi dengan filter opsional
 * @route   GET /api/transactions
 */
const getTransactionHistory = asyncHandler(async (req, res) => {
  const { type, startDate, endDate } = req.query;

  let whereClause = {};

  if (type && ['IN', 'OUT'].includes(type.toUpperCase())) {
    whereClause.type = type.toUpperCase();
  }

  if (startDate && endDate) {
    whereClause.date = {
      [Op.between]: [new Date(startDate), new Date(endDate)]
    };
  } else if (startDate) {
    whereClause.date = { [Op.gte]: new Date(startDate) };
  } else if (endDate) {
    whereClause.date = { [Op.lte]: new Date(endDate) };
  }

  const transactions = await StockTransaction.findAll({
    where: whereClause,
    include: [{
      model: ProductVariant,
      include: [
        { model: Product, attributes: ['id', 'name', 'brand', 'category', 'imageUrl'] },
        { model: Size, attributes: ['size_value'] }
      ]
    }],
    order: [['date', 'DESC']],
    limit: 100 // Limit untuk keamanan (bisa diparameterisasi dengan paginasi nanti)
  });

  res.json(transactions);
});

/**
 * @desc    Mengambil daftar varian dengan stok menipis (Low Stock Alert)
 * @route   GET /api/transactions/low-stock
 */
const getLowStockVariants = asyncHandler(async (req, res) => {
  // Ambil nilai threshold dari query (default: 5)
  const threshold = req.query.threshold ? parseInt(req.query.threshold) : 5;

  const lowStockVariants = await ProductVariant.findAll({
    where: {
      stock: {
        [Op.lt]: threshold
      }
    },
    include: [
      { model: Product, attributes: ['id', 'name', 'brand', 'category', 'imageUrl', 'price'] },
      { model: Size, attributes: ['size_value'] }
    ],
    order: [['stock', 'ASC']]
  });

  res.json(lowStockVariants);
});

module.exports = {
  addTransaction,
  getTransactionHistory,
  getLowStockVariants
};
