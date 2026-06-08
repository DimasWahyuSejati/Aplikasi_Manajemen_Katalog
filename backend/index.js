const express = require('express');
const cors = require('cors');
require('dotenv').config();

// ─── Config & Database ────────────────────────────────────────────
const { connectDB, sequelize } = require('./config/db');
const setupAssociations = require('./models/associations');

// ─── Routes ───────────────────────────────────────────────────────
const authRoutes = require('./routes/authRoutes');
const catalogRoutes = require('./routes/catalogRoutes');
const categoryRoutes = require('./routes/categoryRoutes');
const brandRoutes = require('./routes/brandRoutes');
const sizeRoutes = require('./routes/sizeRoutes');
const transactionRoutes = require('./routes/transactionRoutes');

// ─── Middleware ───────────────────────────────────────────────────
const { errorHandler } = require('./middleware/errorHandler');

// ─── Seeders ──────────────────────────────────────────────────────
const { seedAll } = require('./seeders/seeder');

// ─── Setup Model Associations ─────────────────────────────────────
setupAssociations();

// ─── Express App ──────────────────────────────────────────────────
const app = express();

app.use(cors());
app.use(express.json());

// ─── API Routes ───────────────────────────────────────────────────
app.use('/api/auth', authRoutes);
app.use('/api/catalog', catalogRoutes);
app.use('/api/categories', categoryRoutes);
app.use('/api/brands', brandRoutes);
app.use('/api/sizes', sizeRoutes);
app.use('/api/transactions', transactionRoutes);

// Root route
app.get('/', (req, res) => {
  res.send('API is running...');
});

// ─── Centralized Error Handler (harus terakhir) ───────────────────
app.use(errorHandler);

// ─── Start Server ─────────────────────────────────────────────────
const PORT = process.env.PORT || 5000;

connectDB().then(async () => {
  await sequelize.sync({ alter: true });
  console.log('Database synchronized');

  await seedAll();

  app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
  });
}).catch((err) => {
  console.error('Failed to start server:', err);
});
