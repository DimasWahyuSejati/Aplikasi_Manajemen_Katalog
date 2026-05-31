const Size = require('../models/Size');

const getSizes = async (req, res) => {
  try {
    const sizes = await Size.findAll({
      order: [['size_value', 'ASC']]
    });
    res.json(sizes);
  } catch (error) {
    res.status(500).json({ message: error.message });
  }
};

module.exports = {
  getSizes
};
