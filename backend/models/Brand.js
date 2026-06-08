const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/db');

/**
 * Model Brand: Merepresentasikan tabel 'Brands' di database.
 * Menyimpan data merek/brand dari produk sepatu.
 */
const Brand = sequelize.define('Brand', {
  id: {
    type: DataTypes.INTEGER,
    autoIncrement: true,
    primaryKey: true,
  },
  name: {
    type: DataTypes.STRING,
    allowNull: false,
    unique: true
  },
  description: {
    type: DataTypes.TEXT,
    allowNull: true,
  }
}, {
  timestamps: true,
});

module.exports = Brand;
