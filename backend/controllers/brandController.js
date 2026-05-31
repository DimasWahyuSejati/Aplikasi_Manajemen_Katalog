const Brand = require('../models/Brand');
const Product = require('../models/Product');
const ProductVariant = require('../models/ProductVariant');
const Size = require('../models/Size');

// Get all brands with their associated products count
const getBrands = async (req, res) => {
  try {
    const brands = await Brand.findAll();
    const products = await Product.findAll({
      include: [
        {
          model: ProductVariant,
          as: 'variants',
          include: [{ model: Size }]
        }
      ]
    });

    const result = brands.map(brand => {
      const brandProducts = products.filter(p => p.brand === brand.name).map(p => {
        const productData = p.toJSON();
        productData.totalStock = productData.variants.reduce((sum, variant) => sum + variant.stock, 0);
        productData.stock = productData.totalStock; // For backward compatibility
        return productData;
      });
      return {
        id: brand.id,
        name: brand.name,
        description: brand.description,
        count: brandProducts.length,
        products: brandProducts
      };
    });
    
    res.json(result);
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// Create a brand
const createBrand = async (req, res) => {
  try {
    const { name, description } = req.body;
    const brandExists = await Brand.findOne({ where: { name } });
    if (brandExists) {
      return res.status(400).json({ message: 'Brand already exists' });
    }
    const brand = await Brand.create({ name, description });
    res.status(201).json(brand);
  } catch (error) {
    res.status(400).json({ message: error.message });
  }
};

// Delete a brand
const deleteBrand = async (req, res) => {
  try {
    const brand = await Brand.findByPk(req.params.id);
    if (brand) {
      await brand.destroy();
      res.json({ message: 'Brand removed' });
    } else {
      res.status(404).json({ message: 'Brand not found' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

module.exports = {
  getBrands,
  createBrand,
  deleteBrand
};
