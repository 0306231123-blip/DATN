const express = require('express');
const router = express.Router();
const { verifyToken } = require('../middleware/verifyToken');
const multer = require('multer');
const path = require('path');
const fs = require('fs');
const ChatController = require('../controllers/ChatController');

// Cấu hình Multer cho upload ảnh chat
const UPLOAD_DIR = path.resolve(__dirname, '../../laravel-frontend/public/images/chat');
if (!fs.existsSync(UPLOAD_DIR)) {
  fs.mkdirSync(UPLOAD_DIR, { recursive: true });
}
const storage = multer.diskStorage({
  destination: (req, file, cb) => cb(null, UPLOAD_DIR),
  filename: (req, file, cb) => {
    const uniqueName = `chat_${Date.now()}_${Math.random().toString(36).substring(2, 8)}${path.extname(file.originalname).toLowerCase()}`;
    cb(null, uniqueName);
  },
});
const upload = multer({
  storage,
  limits: { fileSize: 5 * 1024 * 1024 }, // 5MB
  fileFilter: (req, file, cb) => {
    if (['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(file.mimetype)) cb(null, true);
    else cb(new Error('Chỉ chấp nhận file ảnh'), false);
  }
});

// 0. API Upload ảnh chat
router.post('/upload', verifyToken, upload.single('image'), (req, res, next) => {
    // Handle multer errors if any before passing to controller
    next();
}, ChatController.uploadImage);

// 1. [Khách hàng] Lấy tin nhắn của chính mình
router.get('/', verifyToken, ChatController.getMyMessages);

// 2. [Khách hàng] Gửi tin nhắn cho admin
router.post('/', verifyToken, ChatController.sendMessage);

// 3. [Admin] Lấy danh sách khách hàng đã chat
router.get('/admin/users', verifyToken, ChatController.getAdminUsers);

// 4. [Admin] Lấy tin nhắn của 1 khách hàng cụ thể
router.get('/admin/:userId', verifyToken, ChatController.getAdminMessages);

// 5. [Admin] Gửi tin nhắn cho 1 khách hàng
router.post('/admin/:userId', verifyToken, ChatController.sendAdminMessage);

module.exports = router;
