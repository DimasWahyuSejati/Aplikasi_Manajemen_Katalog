/**
 * Controller untuk mengelola proses autentikasi.
 * Menangani registrasi user baru, verifikasi login, dan perhitungan jumlah user.
 */
const User = require('../models/User');
const jwt = require('jsonwebtoken');
const bcrypt = require('bcryptjs');
const { asyncHandler, AppError } = require('../middleware/errorHandler');

/** Masa berlaku token JWT */
const TOKEN_EXPIRY = '30d';

/**
 * Generate JWT token untuk user.
 * @param {number} id - User ID
 * @returns {string} JWT token
 */
const generateToken = (id) => {
  return jwt.sign({ id }, process.env.JWT_SECRET, {
    expiresIn: TOKEN_EXPIRY,
  });
};

/**
 * @desc    Register new user
 * @route   POST /api/auth/register
 */
const registerUser = asyncHandler(async (req, res) => {
  const { username, password } = req.body;

  const userExists = await User.findOne({ where: { username } });
  if (userExists) {
    throw new AppError('User already exists', 400);
  }

  // Hash password menggunakan bcrypt
  const salt = await bcrypt.genSalt(10);
  const hashedPassword = await bcrypt.hash(password, salt);

  const user = await User.create({ username, password: hashedPassword });

  if (!user) {
    throw new AppError('Invalid user data', 400);
  }

  res.status(201).json({
    id: user.id,
    username: user.username,
    token: generateToken(user.id),
  });
});

/**
 * @desc    Login user & get token
 * @route   POST /api/auth/login
 */
const loginUser = asyncHandler(async (req, res) => {
  const { username, password } = req.body;

  const user = await User.findOne({ where: { username } });

  if (!user) {
    throw new AppError('Invalid username or password', 401);
  }

  // Gunakan bcrypt.compare
  const isMatch = await bcrypt.compare(password, user.password);
  if (!isMatch) {
    throw new AppError('Invalid username or password', 401);
  }

  res.json({
    id: user.id,
    username: user.username,
    token: generateToken(user.id),
  });
});

/**
 * @desc    Get total user count
 * @route   GET /api/auth/count
 */
const getUserCount = asyncHandler(async (req, res) => {
  const count = await User.count();
  res.json({ count });
});

module.exports = { registerUser, loginUser, getUserCount };
