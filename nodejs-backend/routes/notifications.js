const express = require('express');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');
const NotificationController = require('../controllers/NotificationController');

const router = express.Router();

router.use(verifyToken, requireAdmin);

router.get('/recent', NotificationController.getRecent);

// A helper endpoint to log a custom event (can be used by other parts of the app)
router.post('/log', NotificationController.logEvent);

module.exports = router;
