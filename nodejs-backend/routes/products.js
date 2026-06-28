const express = require('express');
const ProductController = require('../controllers/ProductController');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');

const router = express.Router();

/**
 * Product Management Routes
 * Base URL: /api/products
 *
 * IMPORTANT: Specific routes (static segments) BEFORE generic /:id routes
 */

// Specific routes first
// GET /api/products/stats - Product statistics
router.get('/stats', verifyToken, requireAdmin, ProductController.getStats);

// GET /api/products/brands - List unique brands (for filter dropdown)
router.get('/brands', ProductController.getBrands);

// GET /api/products/ai-suggest - AI recommendations
router.get('/ai-suggest', ProductController.getAIRecommendation);

// GET /api/products/market-price/:id - Get suggested market import price
router.get('/market-price/:id', verifyToken, requireAdmin, ProductController.getMarketPrice);

// POST /api/products/bulk - Bulk create products from Excel
router.post('/bulk', verifyToken, requireAdmin, ProductController.bulkCreateProducts);

// Generic routes
// GET /api/products - List products with search, filter, pagination
router.get('/', ProductController.getAllProducts);

// GET /api/products/:id - Get product by ID
router.get('/:id', ProductController.getProductById);

// POST /api/products - Create new product
router.post('/', verifyToken, requireAdmin, ProductController.createProduct);

// PUT /api/products/:id - Update product
router.put('/:id', verifyToken, requireAdmin, ProductController.updateProduct);

// DELETE /api/products/:id - Delete product
router.delete('/:id', verifyToken, requireAdmin, ProductController.deleteProduct);

module.exports = router;
