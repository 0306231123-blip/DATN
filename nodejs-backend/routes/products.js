const express = require('express');
const ProductController = require('../controllers/ProductController');

const router = express.Router();

/**
 * Product Management Routes
 * Base URL: /api/products
 *
 * IMPORTANT: Specific routes (static segments) BEFORE generic /:id routes
 */

// Specific routes first
// GET /api/products/stats - Product statistics
router.get('/stats', ProductController.getStats);

// GET /api/products/brands - List unique brands (for filter dropdown)
router.get('/brands', ProductController.getBrands);


router.get('/ai-suggest', ProductController.getAIRecommendation);

// Generic routes
// GET /api/products - List products with search, filter, pagination
router.get('/', ProductController.getAllProducts);

// GET /api/products/:id - Get product by ID
router.get('/:id', ProductController.getProductById);

// POST /api/products - Create new product
router.post('/', ProductController.createProduct);

// PUT /api/products/:id - Update product
router.put('/:id', ProductController.updateProduct);

// DELETE /api/products/:id - Delete product
router.delete('/:id', ProductController.deleteProduct);

module.exports = router;
