const express = require('express');
const AuthController = require('../controllers/AuthController');
const rateLimit = require('express-rate-limit');

const router = express.Router();

// Rate limiter cho chức năng đăng nhập (chặn IP spam)
// Giới hạn 10 requests / 5 phút trên mỗi IP
const loginLimiter = rateLimit({
  windowMs: 5 * 60 * 1000, 
  max: 10,
  message: {
    success: false,
    message: 'Bạn đã đăng nhập quá nhiều lần. Vui lòng thử lại sau 5 phút.'
  }
});

/**
 * Auth Routes
 * Base URL: /api/auth
 */

// POST /api/auth/register - Đăng ký tài khoản mới
router.post('/register', AuthController.register);

// POST /api/auth/login - Đăng nhập (có bảo vệ Rate Limit)
router.post('/login', loginLimiter, AuthController.login);

// GET /api/auth/me - Lấy thông tin user hiện tại (cần token)
router.get('/me', AuthController.getMe);

// PUT /api/auth/update-profile - Cập nhật hồ sơ
router.put('/update-profile', AuthController.updateProfile);

module.exports = router;