const { DataTypes } = require('sequelize');
const { sequelize } = require('../config/db');

/**
 * Model User: Merepresentasikan tabel 'Users' di database.
 * Menyimpan data kredensial login (username dan password yang di-hash).
 */
const User = sequelize.define('User', {
  id: {
    type: DataTypes.INTEGER,
    autoIncrement: true,
    primaryKey: true,
  },
  username: {
    type: DataTypes.STRING,
    allowNull: false,
    unique: true,
  },
  password: {
    type: DataTypes.STRING,
    allowNull: false,
  },
}, {
  timestamps: true,
});

module.exports = User;
