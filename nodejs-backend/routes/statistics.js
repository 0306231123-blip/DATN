const express = require('express');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');
const StatisticsController = require('../controllers/StatisticsController');

const router = express.Router();
router.use(verifyToken, requireAdmin);

router.get('/overview', StatisticsController.getOverview);

module.exports = router;
