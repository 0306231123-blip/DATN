const express = require('express');
const router = express.Router();
const TinNhan = require('../models/TinNhan');
const NguoiDung = require('../models/NguoiDung');
const { verifyToken } = require('../middleware/verifyToken');
const { Op } = require('sequelize');
const sequelize = require('../config/database');
const multer = require('multer');
const path = require('path');
const fs = require('fs');

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
router.post('/upload', verifyToken, (req, res) => {
  upload.single('image')(req, res, (err) => {
    if (err) return res.status(400).json({ success: false, message: err.message });
    if (!req.file) return res.status(400).json({ success: false, message: 'Vui lòng chọn ảnh' });
    const imageUrl = `/images/chat/${req.file.filename}`;
    res.json({ success: true, url: imageUrl });
  });
});


// 1. [Khách hàng] Lấy tin nhắn của chính mình
router.get('/', verifyToken, async (req, res) => {
    try {
        const ma_nguoi_dung = req.user.ma_nguoi_dung || req.user.id;
        
        if (!ma_nguoi_dung) {
            return res.status(401).json({ success: false, message: 'Token không hợp lệ, thiếu thông tin người dùng.' });
        }
        const messages = await TinNhan.findAll({
            where: { ma_nguoi_dung: ma_nguoi_dung },
            order: [['ngay_gui', 'ASC']]
        });
        res.json({ success: true, data: messages });
    } catch (error) {
        console.error('Lỗi lấy tin nhắn:', error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});

// 2. [Khách hàng] Gửi tin nhắn cho admin
router.post('/', verifyToken, async (req, res) => {
    try {
        const ma_nguoi_dung = req.user.ma_nguoi_dung || req.user.id;
        
        if (!ma_nguoi_dung) {
            return res.status(401).json({ success: false, message: 'Token không hợp lệ, thiếu thông tin người dùng.' });
        }
        const { noi_dung, hinh_anh } = req.body;
        
        if ((!noi_dung || !noi_dung.trim()) && !hinh_anh) {
            return res.status(400).json({ success: false, message: 'Nội dung không được để trống' });
        }

        // Kiểm tra xem đây có phải là tin nhắn đầu tiên của user không
        const messageCount = await TinNhan.count({ where: { ma_nguoi_dung } });
        const isFirstMessage = messageCount === 0;

        const newMessage = await TinNhan.create({
            ma_nguoi_dung: ma_nguoi_dung,
            noi_dung: noi_dung ? noi_dung.trim() : null,
            hinh_anh: hinh_anh || null,
            is_from_admin: false
        });

        // Nếu là tin nhắn đầu tiên, hệ thống tự động trả lời
        if (isFirstMessage) {
            setTimeout(async () => {
                try {
                    await TinNhan.create({
                        ma_nguoi_dung: ma_nguoi_dung,
                        noi_dung: "Chào bạn! Cảm ơn bạn đã liên hệ với Hệ thống Mỹ Phẩm. Các chuyên viên tư vấn của chúng tôi đã nhận được tin nhắn và sẽ phản hồi bạn trong ít phút nữa. Bạn có thể mô tả chi tiết vấn đề hoặc thắc mắc của mình tại đây nhé! 🥰",
                        is_from_admin: true
                    });
                } catch (e) {
                    console.error("Lỗi gửi tin nhắn tự động:", e);
                }
            }, 1000); // Đợi 1 giây rồi mới gửi cho giống người thật
        }

        res.json({ success: true, data: newMessage });
    } catch (error) {
        console.error('Lỗi gửi tin nhắn:', error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});

// 3. [Admin] Lấy danh sách khách hàng đã chat
router.get('/admin/users', verifyToken, async (req, res) => {
    try {
        if (req.user.vai_tro !== 'quan_tri_vien') {
            return res.status(403).json({ success: false, message: 'Không có quyền truy cập' });
        }

        // Lấy danh sách người dùng có tin nhắn, sắp xếp theo tin nhắn mới nhất
        const usersWithChats = await NguoiDung.findAll({
            include: [{
                model: TinNhan,
                as: 'tin_nhan',
                attributes: []
            }],
            attributes: [
                'ma_nguoi_dung',
                'ho_ten',
                'email',
                [sequelize.fn('MAX', sequelize.col('tin_nhan.ngay_gui')), 'latest_message_date']
            ],
            where: {
                '$tin_nhan.ma_tin_nhan$': { [Op.not]: null } // Inner join implicit check
            },
            group: ['nguoi_dung.ma_nguoi_dung'],
            order: [[sequelize.col('latest_message_date'), 'DESC']]
        });

        res.json({ success: true, data: usersWithChats });
    } catch (error) {
        console.error('Lỗi lấy danh sách user chat:', error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});

// 4. [Admin] Lấy tin nhắn của 1 khách hàng cụ thể
router.get('/admin/:userId', verifyToken, async (req, res) => {
    try {
        if (req.user.vai_tro !== 'quan_tri_vien') {
            return res.status(403).json({ success: false, message: 'Không có quyền truy cập' });
        }

        const { userId } = req.params;
        const messages = await TinNhan.findAll({
            where: { ma_nguoi_dung: userId },
            order: [['ngay_gui', 'ASC']]
        });
        res.json({ success: true, data: messages });
    } catch (error) {
        console.error('Lỗi lấy tin nhắn user:', error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});

// 5. [Admin] Gửi tin nhắn cho 1 khách hàng
router.post('/admin/:userId', verifyToken, async (req, res) => {
    try {
        if (req.user.vai_tro !== 'quan_tri_vien') {
            return res.status(403).json({ success: false, message: 'Không có quyền truy cập' });
        }

        const { userId } = req.params;
        const { noi_dung, hinh_anh } = req.body;

        if ((!noi_dung || !noi_dung.trim()) && !hinh_anh) {
            return res.status(400).json({ success: false, message: 'Nội dung không được để trống' });
        }

        const newMessage = await TinNhan.create({
            ma_nguoi_dung: userId,
            noi_dung: noi_dung ? noi_dung.trim() : null,
            hinh_anh: hinh_anh || null,
            is_from_admin: true
        });

        res.json({ success: true, data: newMessage });
    } catch (error) {
        console.error('Lỗi admin gửi tin nhắn:', error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});

module.exports = router;
