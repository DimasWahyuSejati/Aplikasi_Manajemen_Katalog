/**
 * Custom error class dengan HTTP status code.
 * Gunakan ini untuk melempar error yang bisa ditangani oleh errorHandler middleware.
 */
class AppError extends Error {
  constructor(message, statusCode) {
    super(message);
    this.statusCode = statusCode;
    this.isOperational = true;
    Error.captureStackTrace(this, this.constructor);
  }
}

/**
 * Wrapper untuk async route handlers.
 * Menangkap error secara otomatis dan meneruskannya ke error handling middleware.
 * Menghilangkan kebutuhan try-catch berulang di setiap handler.
 *
 * @param {Function} fn - Async route handler function (req, res, next)
 * @returns {Function} Wrapped handler
 *
 * @example
 * // Sebelum (repetitif):
 * const getProducts = async (req, res) => {
 *   try { ... } catch (error) { res.status(500).json({ message: error.message }); }
 * };
 *
 * // Sesudah (clean):
 * const getProducts = asyncHandler(async (req, res) => {
 *   // ... logic tanpa try-catch
 * });
 */
const asyncHandler = (fn) => (req, res, next) => {
  Promise.resolve(fn(req, res, next)).catch(next);
};

/**
 * Centralized error handling middleware.
 * Harus dipasang sebagai middleware terakhir di Express app.
 */
const errorHandler = (err, req, res, _next) => {
  const statusCode = err.statusCode || 500;
  const message = err.message || 'Internal Server Error';

  console.error(`[Error] ${statusCode} - ${message}`);

  res.status(statusCode).json({
    message,
    ...(process.env.NODE_ENV === 'development' && { stack: err.stack }),
  });
};

module.exports = { AppError, asyncHandler, errorHandler };
