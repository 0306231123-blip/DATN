const express = require('express');
const router = express.Router();
const CartController = require('../controllers/CartController');
const { verifyToken } = require('../middleware/verifyToken');

// Áp dụng middleware xác thực cho TẤT CẢ routes trong cart
router.use(verifyToken);

// 1. API Thêm vào giỏ hàng
router.post('/add', CartController.addToCart);

// 2. API Lấy danh sách giỏ hàng
router.get('/', CartController.getCart);

// 3. API Cập nhật số lượng
router.post('/update', CartController.updateCart);

// 4. API Xóa sản phẩm khỏi giỏ hàng
router.post('/remove', CartController.removeFromCart);

module.exports = router;