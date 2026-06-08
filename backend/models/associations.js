const Product = require('./Product');
const ProductVariant = require('./ProductVariant');
const Size = require('./Size');
const StockTransaction = require('./StockTransaction');

/**
 * Mendefinisikan semua relasi antar model Sequelize.
 * File ini harus di-import sekali saat startup (index.js).
 *
 * Relasi:
 * - Product hasMany ProductVariant (1 produk → banyak varian ukuran)
 * - ProductVariant belongsTo Product
 * - Size hasMany ProductVariant (1 ukuran → bisa dipakai banyak produk)
 * - ProductVariant belongsTo Size
 */
const setupAssociations = () => {
  Product.hasMany(ProductVariant, { foreignKey: 'product_id', as: 'variants' });
  ProductVariant.belongsTo(Product, { foreignKey: 'product_id' });
  Size.hasMany(ProductVariant, { foreignKey: 'size_id' });
  ProductVariant.belongsTo(Size, { foreignKey: 'size_id' });

  // Transaksi Stok
  ProductVariant.hasMany(StockTransaction, { foreignKey: 'product_variant_id', as: 'transactions' });
  StockTransaction.belongsTo(ProductVariant, { foreignKey: 'product_variant_id' });
};

module.exports = setupAssociations;
