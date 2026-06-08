const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/db');

/**
 * Model Category: Merepresentasikan tabel 'Categories' di database.
 * Menyimpan jenis/kategori sepatu (contoh: Sneakers, Boots).
 */
const Category = sequelize.define('Category', {
  id: {
    type: DataTypes.INTEGER,
    autoIncrement: true,
    primaryKey: true,
  },
  name: {
    type: DataTypes.STRING,
    allowNull: false,
    unique: true,
  },
  description: {
    type: DataTypes.TEXT,
    allowNull: true,
  }
}, {
  timestamps: true,
});

module.exports = Category;
