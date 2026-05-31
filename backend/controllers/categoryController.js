const Category = require('../models/Category');
const Product = require('../models/Product');
const ProductVariant = require('../models/ProductVariant');
const Size = require('../models/Size');

// Get all categories with their associated products count
const getCategories = async (req, res) => {
  try {
    const categories = await Category.findAll();
    const products = await Product.findAll({
      include: [
        {
          model: ProductVariant,
          as: 'variants',
          include: [{ model: Size }]
        }
      ]
    });

    const result = categories.map(cat => {
      const catProducts = products.filter(p => p.category === cat.name).map(p => {
        const productData = p.toJSON();
        productData.totalStock = productData.variants.reduce((sum, variant) => sum + variant.stock, 0);
        productData.stock = productData.totalStock; // For backward compatibility
        return productData;
      });
      return {
        id: cat.id,
        name: cat.name,
        description: cat.description,
        count: catProducts.length,
        products: catProducts
      };
    });
    
    res.json(result);
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// Create a category
const createCategory = async (req, res) => {
  try {
    const { name, description } = req.body;
    const categoryExists = await Category.findOne({ where: { name } });
    if (categoryExists) {
      return res.status(400).json({ message: 'Category already exists' });
    }
    const category = await Category.create({ name, description });
    res.status(201).json(category);
  } catch (error) {
    res.status(400).json({ message: error.message });
  }
};

// Delete a category
const deleteCategory = async (req, res) => {
  try {
    const category = await Category.findByPk(req.params.id);
    if (category) {
      await category.destroy();
      res.json({ message: 'Category removed' });
    } else {
      res.status(404).json({ message: 'Category not found' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

module.exports = {
  getCategories,
  createCategory,
  deleteCategory
};
