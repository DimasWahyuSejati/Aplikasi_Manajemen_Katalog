const { connectDB, sequelize } = require('./config/db');
const Category = require('./models/Category');

const seedCategories = async () => {
  try {
    await connectDB();
    await sequelize.sync(); // ensure tables exist

    const categoriesToSeed = [
      { name: 'Boots', description: 'Kategori sepatu Boots' },
      { name: 'Running', description: 'Kategori sepatu Running' },
      { name: 'Sneakers', description: 'Kategori sepatu Sneakers' }
    ];

    for (const cat of categoriesToSeed) {
      const exists = await Category.findOne({ where: { name: cat.name } });
      if (!exists) {
        await Category.create(cat);
        console.log(`Seeded category: ${cat.name}`);
      } else {
        console.log(`Category ${cat.name} already exists.`);
      }
    }
    process.exit(0);
  } catch (error) {
    console.error('Failed to seed categories:', error);
    process.exit(1);
  }
};

seedCategories();
