const express = require('express');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');
const DashboardController = require('../controllers/DashboardController');

const router = express.Router();

router.use(verifyToken, requireAdmin);

/**
 * GET /api/dashboard/stats
 * Trả về toàn bộ dữ liệu thống kê cho trang Dashboard
 */
router.get('/stats', DashboardController.getStats);

module.exports = router;
