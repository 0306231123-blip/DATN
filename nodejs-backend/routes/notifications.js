const express = require('express');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');
const NotificationController = require('../controllers/NotificationController');

const router = express.Router();

// GET recent - no auth required (admin panel uses Laravel session, not JWT)
router.get('/recent', NotificationController.getRecent);

// POST log - requires admin auth
router.post('/log', verifyToken, requireAdmin, NotificationController.logEvent);

module.exports = router;
