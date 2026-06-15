const express = require('express');
const OrderController = require('../controllers/OrderController');

const router = express.Router();

/**
 * Order Management Routes
 * Base URL: /api/orders
 *
 * IMPORTANT: Specific routes (static segments) BEFORE generic /:id routes
 */

// Specific routes first
// GET /api/orders/stats - Order statistics
router.get('/stats', OrderController.getOrderStats);

// Generic routes
// GET /api/orders - List orders with search, filter, pagination
router.get('/', OrderController.getAllOrders);

// GET /api/orders/:id - Get order by ID with details
router.get('/:id', OrderController.getOrderById);

// PUT /api/orders/:id/status - Update order status
router.put('/:id/status', OrderController.updateOrderStatus);

module.exports = router;
