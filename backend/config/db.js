const { Sequelize } = require('sequelize');
require('dotenv').config();

/**
 * Inisialisasi koneksi database menggunakan Sequelize.
 * Mengambil konfigurasi dari environment variables (.env).
 */
const sequelize = new Sequelize(
  process.env.DB_NAME,
  process.env.DB_USER,
  process.env.DB_PASSWORD,
  {
    host: process.env.DB_HOST,
    dialect: process.env.DB_DIALECT || 'mysql',
    logging: false, // set true to see sql logs
  }
);

const connectDB = async () => {
  try {
    await sequelize.authenticate();
    console.log('MySQL connected successfully.');
  } catch (error) {
    console.error('Unable to connect to the database:', error);
    process.exit(1);
  }
};

module.exports = { sequelize, connectDB };
