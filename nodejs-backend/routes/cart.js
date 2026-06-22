const express = require('express');
const router = express.Router();
const GioHang = require('../models/GioHang');
const SanPham = require('../models/SanPham'); 
const AnhSanPham = require('../models/AnhSanPham'); // Import model ảnh lên đầu file
const jwt = require('jsonwebtoken');

// 1. API Thêm vào giỏ hàng
router.post('/add', async (req, res) => {
    try {
        const token = req.headers.authorization.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        const { ma_san_pham, so_luong } = req.body;

        let item = await GioHang.findOne({ 
            where: { ma_nguoi_dung: decoded.ma_nguoi_dung, ma_san_pham } 
        });

        if (item) {
            item.so_luong += parseInt(so_luong);
            await item.save();
        } else {
            await GioHang.create({
                ma_nguoi_dung: decoded.ma_nguoi_dung,
                ma_san_pham,
                so_luong
            });
        }
        res.json({ success: true, message: 'Đã thêm vào giỏ hàng!' });
    } catch (error) {
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});

// 2. API Lấy danh sách giỏ hàng
router.get('/', async (req, res) => {
    try {
        const token = req.headers.authorization.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        
        // ĐÃ SỬA: Đưa câu lệnh truy vấn có Include ảnh vào ĐÚNG VỊ TRÍ này
        const items = await GioHang.findAll({ 
            where: { ma_nguoi_dung: decoded.ma_nguoi_dung },
            include: [{ 
                model: SanPham, 
                as: 'san_pham',
                include: [{ model: AnhSanPham, as: 'danh_sach_anh' }] // Kết nối bảng ảnh
            }]
        });
        
        res.json({ success: true, data: items }); 
    } catch (error) {
        res.status(500).json({ success: false, message: error.message });
    }
});

// 3. API Cập nhật số lượng
router.post('/update', async (req, res) => {
    try {
        const token = req.headers.authorization.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        const { ma_san_pham, thay_doi } = req.body; 

        let item = await GioHang.findOne({ 
            where: { ma_nguoi_dung: decoded.ma_nguoi_dung, ma_san_pham } 
        });

        if (item) {
            item.so_luong += thay_doi;
            if (item.so_luong <= 0) await item.destroy(); 
            else await item.save();
            res.json({ success: true });
        }
    } catch (error) {
        res.status(500).json({ success: false });
    }
});

// Dòng này LUÔN LUÔN nằm cuối cùng
module.exports = router;