const express = require('express');
const { verifyToken } = require('../middleware/verifyToken');
const OrderController = require('../controllers/OrderController');

const router = express.Router();

// --- 1. TẠO ĐƠN HÀNG ---
router.post('/create', verifyToken, OrderController.createOrder);

// --- 2. CẬP NHẬT TRẠNG THÁI (HỦY ĐƠN / TRẢ HÀNG / HOÀN THÀNH) ---
router.post('/update-status', verifyToken, OrderController.updateUserOrderStatus);

// --- 3. LẤY LỊCH SỬ ĐƠN HÀNG ---
router.get('/my-orders', verifyToken, OrderController.getMyOrders);

// --- 4. YÊU CẦU HOÀN TIỀN ---
router.post('/request-refund', verifyToken, OrderController.requestRefund);

// --- 5. HARD DELETE ĐƠN HÀNG KHI HỦY THANH TOÁN QR ---
router.post('/delete-unpaid', verifyToken, OrderController.deleteUnpaid);

module.exports = router;