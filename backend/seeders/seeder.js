const User = require('../models/User');
const Size = require('../models/Size');
const Category = require('../models/Category');
const Brand = require('../models/Brand');
const bcrypt = require('bcryptjs');

/** Daftar ukuran sepatu default */
const DEFAULT_SIZES = ['36', '37', '38', '39', '40', '41', '42', '43', '44', '45'];

/** Kredensial admin default */
const DEFAULT_ADMIN = { username: 'admin', password: 'password123' };

/** Daftar kategori default */
const DEFAULT_CATEGORIES = [
  { name: 'Boots', description: 'Kategori sepatu Boots' },
  { name: 'Running', description: 'Kategori sepatu Running' },
  { name: 'Sneakers', description: 'Kategori sepatu Sneakers' },
];

/**
 * Seed ukuran sepatu default ke database.
 */
const seedSizes = async () => {
  for (const sizeValue of DEFAULT_SIZES) {
    await Size.findOrCreate({ where: { size_value: sizeValue } });
  }
  console.log('[Seeder] Default sizes seeded');
};

/**
 * Seed admin user default ke database.
 */
const seedAdmin = async () => {
  const adminExists = await User.findOne({ where: { username: DEFAULT_ADMIN.username } });
  if (!adminExists) {
    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash(DEFAULT_ADMIN.password, salt);

    await User.create({ username: DEFAULT_ADMIN.username, password: hashedPassword });
    console.log('[Seeder] Default admin created with hashed password');
  } else {
    console.log('[Seeder] Admin already exists, skipped');
  }
};

/**
 * Seed kategori default ke database.
 */
const seedCategories = async () => {
  for (const cat of DEFAULT_CATEGORIES) {
    const exists = await Category.findOne({ where: { name: cat.name } });
    if (!exists) {
      await Category.create(cat);
      console.log(`[Seeder] Category '${cat.name}' created`);
    }
  }
  console.log('[Seeder] Default categories seeded');
};

/**
 * Menjalankan semua seeder.
 * Dipanggil setelah database synchronized di index.js.
 */
const seedAll = async () => {
  console.log('[Seeder] Starting database seeding...');
  await seedSizes();
  await seedAdmin();
  await seedCategories();
  console.log('[Seeder] All seeding complete');
};

module.exports = { seedAll };
