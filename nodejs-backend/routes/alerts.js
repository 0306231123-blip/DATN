const express = require('express');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');
const AlertController = require('../controllers/AlertController');

const router = express.Router();

router.use(verifyToken, requireAdmin);

// Lấy số lượng thông báo (tồn kho, tin nhắn, trả hàng, cảnh báo hệ thống)
router.get('/', AlertController.getAlerts);

// Lấy danh sách cảnh báo hệ thống
router.get('/system', AlertController.getSystemAlerts);

// Đánh dấu đã đọc cảnh báo hệ thống
router.put('/system/:id/read', AlertController.markSystemAlertAsRead);

module.exports = router;
