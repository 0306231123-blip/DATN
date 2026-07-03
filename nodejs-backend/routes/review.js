const express = require('express');
const router = express.Router();
const ReviewController = require('../controllers/ReviewController');

// Khai báo middleware xác thực token
const { verifyToken } = require('../middleware/verifyToken'); 

// Tạo API thêm đánh giá
router.post('/add', verifyToken, ReviewController.addReview);

// Kiểm tra quyền đánh giá (đã mua & đơn hàng hoàn thành)
router.get('/check-eligibility/:ma_san_pham', verifyToken, ReviewController.checkEligibility);

router.get('/:ma_san_pham', ReviewController.getReviews);

module.exports = router;