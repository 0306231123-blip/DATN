const express = require('express');
const UserController = require('../controllers/UserController');
const { validateListQuery, validateUserCreate, validateUserUpdate } = require('../middleware/userValidation');

const router = express.Router();

/**
 * User Management Routes
 * Base URL: /api/users
 * 
 * IMPORTANT: Route order matters in Express!
 * - Specific routes (with static segments) come before generic routes with parameters
 * - e.g., /statistics/overview and /export/csv must be before /:id
 */

// Specific routes first (static segments)
// GET /api/users/stats - User statistics (alias)
router.get('/stats', UserController.getStatistics);

// GET /api/users/statistics/overview - User statistics
router.get('/statistics/overview', UserController.getStatistics);

// GET /api/users/export/csv - Export users to CSV
router.get('/export/csv', UserController.exportUsers);

// Generic routes (with parameters)
// GET /api/users - List users with search, filter, pagination
router.get('/', validateListQuery, UserController.getAllUsers);

// GET /api/users/:id - Get user by ID
router.get('/:id', UserController.getUserById);

// POST /api/users - Create new user
router.post('/', validateUserCreate, UserController.createUser);

// PUT /api/users/:id - Update user
router.put('/:id', validateUserUpdate, UserController.updateUser);

// DELETE /api/users/:id - Delete user
router.delete('/:id', UserController.deleteUser);

module.exports = router;
