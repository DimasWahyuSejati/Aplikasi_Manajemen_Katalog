const express = require('express');
const cors = require('cors');
require('dotenv').config();

const { connectDB, sequelize } = require('./config/db');
const authRoutes = require('./routes/authRoutes');
const catalogRoutes = require('./routes/catalogRoutes');
const categoryRoutes = require('./routes/categoryRoutes');
const brandRoutes = require('./routes/brandRoutes');
const sizeRoutes = require('./routes/sizeRoutes');
const Product = require('./models/Product');
const Size = require('./models/Size');
const ProductVariant = require('./models/ProductVariant');

// Setup Associations
Product.hasMany(ProductVariant, { foreignKey: 'product_id', as: 'variants' });
ProductVariant.belongsTo(Product, { foreignKey: 'product_id' });
Size.hasMany(ProductVariant, { foreignKey: 'size_id' });
ProductVariant.belongsTo(Size, { foreignKey: 'size_id' });

const app = express();

// Middleware
app.use(cors());
app.use(express.json());

// Routes
app.use('/api/auth', authRoutes);
app.use('/api/catalog', catalogRoutes);
app.use('/api/categories', categoryRoutes);
app.use('/api/brands', brandRoutes);
app.use('/api/sizes', sizeRoutes);

// Root route
app.get('/', (req, res) => {
  res.send('API is running...');
});

const PORT = process.env.PORT || 5000;

// Connect to DB, sync models, and start server
connectDB().then(() => {
  // Sync all models - using force: true to reset tables due to schema changes
  sequelize.sync({ force: true }).then(async () => {
    console.log('Database synchronized (tables dropped and recreated)');
    
    // Seed default sizes
    const defaultSizes = ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];
    for (const size of defaultSizes) {
      await Size.findOrCreate({ where: { size_value: size } });
    }
    console.log('Default sizes seeded');

    // Also run seedAdmin logic here since we force reset DB
    // Removed because seedAdmin is a standalone script, not a function
    
    app.listen(PORT, () => {
      console.log(`Server is running on port ${PORT}`);
      // Run the standalone script after server starts
      require('./seedAdmin');
    });
  }).catch((err) => {
    console.error('Failed to sync database:', err);
  });
});
