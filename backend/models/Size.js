const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/db');

/**
 * Model Size: Merepresentasikan tabel 'Sizes' di database.
 * Menyimpan nilai ukuran sepatu (contoh: 38, 39, 40).
 */
const Size = sequelize.define('Size', {
  id: {
    type: DataTypes.INTEGER,
    autoIncrement: true,
    primaryKey: true,
  },
  size_value: {
    type: DataTypes.STRING,
    allowNull: false,
    unique: true
  }
}, {
  timestamps: false,
});

module.exports = Size;
