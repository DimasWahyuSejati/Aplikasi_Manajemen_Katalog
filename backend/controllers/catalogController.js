const Product = require('../models/Product');
const ProductVariant = require('../models/ProductVariant');
const Size = require('../models/Size');
const { sequelize } = require('../config/db');

// Get all products
const getProducts = async (req, res) => {
  try {
    const products = await Product.findAll({
      include: [
        {
          model: ProductVariant,
          as: 'variants',
          include: [{ model: Size }]
        }
      ]
    });
    
    // Calculate total stock for each product to make frontend logic easier
    const formattedProducts = products.map(p => {
      const productData = p.toJSON();
      productData.totalStock = productData.variants.reduce((sum, variant) => sum + variant.stock, 0);
      
      // Kept for backward compatibility if some old frontend code expects 'stock' 
      // but 'totalStock' is better.
      productData.stock = productData.totalStock; 
      return productData;
    });

    res.json(formattedProducts);
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// Get product by ID
const getProductById = async (req, res) => {
  try {
    const product = await Product.findByPk(req.params.id, {
      include: [
        {
          model: ProductVariant,
          as: 'variants',
          include: [{ model: Size }]
        }
      ]
    });
    if (product) {
      const productData = product.toJSON();
      productData.totalStock = productData.variants.reduce((sum, variant) => sum + variant.stock, 0);
      productData.stock = productData.totalStock;
      res.json(productData);
    } else {
      res.status(404).json({ message: 'Product not found' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

// Create a product
const createProduct = async (req, res) => {
  const transaction = await sequelize.transaction();
  try {
    const { name, brand, category, color, price, description, imageUrl, variants } = req.body;
    
    // Create the main product
    const product = await Product.create({
      name,
      brand,
      category,
      color,
      price,
      description,
      imageUrl
    }, { transaction });

    // Handle variants array [{size_id: 1, stock: 10}, ...]
    if (variants && Array.isArray(variants)) {
      const variantData = variants.map(v => ({
        product_id: product.id,
        size_id: v.size_id,
        stock: v.stock || 0
      }));
      await ProductVariant.bulkCreate(variantData, { transaction });
    }

    await transaction.commit();
    
    // Fetch the created product with its variants
    const createdProduct = await Product.findByPk(product.id, {
      include: [{ model: ProductVariant, as: 'variants', include: [Size] }]
    });

    res.status(201).json(createdProduct);
  } catch (error) {
    await transaction.rollback();
    res.status(400).json({ message: error.message });
  }
};

// Update a product
const updateProduct = async (req, res) => {
  const transaction = await sequelize.transaction();
  try {
    const product = await Product.findByPk(req.params.id);
    if (!product) {
      await transaction.rollback();
      return res.status(404).json({ message: 'Product not found' });
    }

    const { name, brand, category, color, price, description, imageUrl, variants } = req.body;
    
    await product.update({
      name, brand, category, color, price, description, imageUrl
    }, { transaction });

    // Handle variants if provided
    if (variants && Array.isArray(variants)) {
      // Simplest approach: delete existing variants for this product and insert new ones
      await ProductVariant.destroy({ where: { product_id: product.id }, transaction });
      
      const variantData = variants.map(v => ({
        product_id: product.id,
        size_id: v.size_id,
        stock: v.stock || 0
      }));
      await ProductVariant.bulkCreate(variantData, { transaction });
    }

    await transaction.commit();
    
    const updatedProduct = await Product.findByPk(product.id, {
      include: [{ model: ProductVariant, as: 'variants', include: [Size] }]
    });

    res.json(updatedProduct);
  } catch (error) {
    await transaction.rollback();
    res.status(400).json({ message: error.message });
  }
};

// Delete a product
const deleteProduct = async (req, res) => {
  try {
    const product = await Product.findByPk(req.params.id);
    if (product) {
      await product.destroy();
      res.json({ message: 'Product removed' });
    } else {
      res.status(404).json({ message: 'Product not found' });
    }
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

module.exports = {
  getProducts,
  getProductById,
  createProduct,
  updateProduct,
  deleteProduct
};
