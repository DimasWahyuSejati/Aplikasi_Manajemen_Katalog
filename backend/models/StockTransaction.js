const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/db');

/**
 * Model StockTransaction: Merepresentasikan tabel 'stock_transactions'.
 * Digunakan untuk mencatat setiap perubahan (mutasi) stok, baik barang masuk (IN) maupun barang keluar (OUT).
 */
const StockTransaction = sequelize.define('StockTransaction', {
  id: {
    type: DataTypes.INTEGER,
    autoIncrement: true,
    primaryKey: true,
  },
  product_variant_id: {
    type: DataTypes.INTEGER,
    allowNull: false,
  },
  type: {
    type: DataTypes.ENUM('IN', 'OUT'),
    allowNull: false,
    comment: 'IN untuk barang masuk/retur, OUT untuk penjualan/rusak',
  },
  quantity: {
    type: DataTypes.INTEGER,
    allowNull: false,
    validate: {
      min: 1, // Harus selalu positif, tipe IN/OUT yang menentukan arahnya
    },
  },
  reason: {
    type: DataTypes.STRING,
    allowNull: false,
    defaultValue: 'Penyesuaian Manual',
  },
  date: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
}, {
  tableName: 'stock_transactions',
  timestamps: false,
});

module.exports = StockTransaction;
