const express = require('express');
const router = express.Router();
const reviewController = require('../controllers/reviewController');

// Khai báo middleware xác thực token (Tôi thấy ông có thư mục middleware kìa)
// Lưu ý: Đổi tên './auth' thành tên file middleware thực tế của ông nếu ông đặt khác nhé
const { verifyToken } = require('../middleware/verifyToken'); 

// Tạo API thêm đánh giá
router.post('/add', verifyToken, reviewController.addReview);
router.get('/:ma_san_pham', reviewController.getReviews);
module.exports = router;