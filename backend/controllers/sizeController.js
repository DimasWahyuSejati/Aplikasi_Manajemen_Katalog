/**
 * Controller untuk mengelola data Ukuran Sepatu.
 * Umumnya digunakan hanya untuk membaca (GET) daftar ukuran yang tersedia.
 */
const Size = require('../models/Size');
const { asyncHandler } = require('../middleware/errorHandler');

/**
 * @desc    Get all available sizes, sorted ascending
 * @route   GET /api/sizes
 */
const getSizes = asyncHandler(async (req, res) => {
  const sizes = await Size.findAll({
    order: [['size_value', 'ASC']],
  });
  res.json(sizes);
});

module.exports = {
  getSizes,
};
