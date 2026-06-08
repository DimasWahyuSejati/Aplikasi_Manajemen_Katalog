const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/db');

/**
 * Model ProductVariant: Merepresentasikan tabel 'ProductVariants' di database.
 * Tabel persimpangan antara Product dan Size yang juga menyimpan jumlah stok spesifik untuk kombinasi tersebut.
 */
const ProductVariant = sequelize.define('ProductVariant', {
  id: {
    type: DataTypes.INTEGER,
    autoIncrement: true,
    primaryKey: true,
  },
  product_id: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: 'Products',
      key: 'id'
    },
    onDelete: 'CASCADE'
  },
  size_id: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: 'Sizes',
      key: 'id'
    },
    onDelete: 'CASCADE'
  },
  stock: {
    type: DataTypes.INTEGER,
    allowNull: false,
    defaultValue: 0
  }
}, {
  timestamps: true,
});

module.exports = ProductVariant;
