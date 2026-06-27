const express = require('express');
const router = express.Router();
const KhuyenMai = require('../models/KhuyenMai');
const { Op } = require('sequelize');
const jwt = require('jsonwebtoken');
const DonHang = require('../models/DonHang');

// API: /api/voucher/check
router.post('/check', async (req, res) => {
    try {
        // ==========================================
        // 1. LẤY ID NGƯỜI DÙNG TỪ TOKEN
        // ==========================================
        const authHeader = req.headers.authorization;
        if (!authHeader || !authHeader.startsWith('Bearer ')) {
            return res.status(401).json({ success: false, message: 'Bạn chưa đăng nhập.' });
        }
        
        const token = authHeader.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        const maNguoiDung = decoded.ma_nguoi_dung;

        const { ma_code, tong_tien_hang } = req.body;

        if (!ma_code) {
            return res.status(400).json({ success: false, message: 'Vui lòng nhập mã khuyến mãi.' });
        }

        // 2. Tìm voucher
        const voucher = await KhuyenMai.findOne({ 
            where: { 
                ma_code: ma_code,
                trang_thai: 'hoat_dong' 
            } 
        });

        if (!voucher) {
            return res.status(404).json({ success: false, message: 'Mã khuyến mãi không tồn tại hoặc đã bị khóa.' });
        }

        // ==========================================
        // 3. CHỐT CHẶN: KIỂM TRA 1 USER / 1 MÃ
        // ==========================================
        const daSuDung = await DonHang.findOne({
            where: {
                ma_nguoi_dung: maNguoiDung,
                ma_khuyen_mai: voucher.ma_khuyen_mai,
                trang_thai_don: { [Op.ne]: 'da_huy' } // Trừ những đơn khách đã hủy ra (hủy thì cho xài lại)
            }
        });

        if (daSuDung) {
            return res.status(400).json({ success: false, message: 'Bạn đã sử dụng mã này cho một đơn hàng trước đó rồi!' });
        }
        // ==========================================

        // 4. Kiểm tra số lượng
        if (voucher.so_luong <= 0) {
            return res.status(400).json({ success: false, message: 'Mã khuyến mãi này đã hết lượt sử dụng.' });
        }

        // 5. Kiểm tra hạn sử dụng
        const now = new Date();
        if (now < voucher.ngay_bat_dau || now > voucher.ngay_ket_thuc) {
            return res.status(400).json({ success: false, message: 'Mã khuyến mãi chưa có hiệu lực hoặc đã hết hạn.' });
        }

        // 6. Kiểm tra điều kiện đơn tối thiểu
        if (tong_tien_hang < voucher.don_toi_thieu) {
            return res.status(400).json({ 
                success: false, 
                message: `Đơn hàng phải từ ${voucher.don_toi_thieu.toLocaleString()}đ để áp dụng mã này.` 
            });
        }

        // 7. Tính toán số tiền được giảm
        let so_tien_giam = 0;
        if (voucher.loai_giam === 'tien_mat') {
            so_tien_giam = voucher.gia_tri;
        } else if (voucher.loai_giam === 'phan_tram') {
            so_tien_giam = (tong_tien_hang * voucher.gia_tri) / 100;
            // Nếu có cấu hình giảm tối đa thì không được vượt mức này
            if (voucher.giam_toi_da && so_tien_giam > voucher.giam_toi_da) {
                so_tien_giam = voucher.giam_toi_da;
            }
        }

        // Không cho phép giảm âm tiền (VD: đơn 40k nhập mã 50k thì chỉ giảm 40k)
        if (so_tien_giam > tong_tien_hang) {
            so_tien_giam = tong_tien_hang;
        }

        res.json({
            success: true,
            message: 'Áp dụng mã thành công!',
            data: {
                ma_khuyen_mai: voucher.ma_khuyen_mai,
                ma_code: voucher.ma_code,
                so_tien_giam: so_tien_giam
            }
        });

    } catch (error) {
        if (error.name === 'JsonWebTokenError' || error.name === 'TokenExpiredError') {
            return res.status(401).json({ success: false, message: 'Token không hợp lệ hoặc đã hết hạn.' });
        }
        console.error(error);
        res.status(500).json({ success: false, message: 'Lỗi server khi kiểm tra voucher.' });
    }
});
// API: /api/voucher/active
// Lấy danh sách voucher đang hoạt động, còn lượt và chưa hết hạn
router.get('/active', async (req, res) => {
    try {
        const vouchers = await KhuyenMai.findAll({
            where: {
                trang_thai: 'hoat_dong',
                so_luong: { [Op.gt]: 0 }, // Số lượng phải lớn hơn 0
                ngay_ket_thuc: { [Op.gt]: new Date() } // Chưa tới hạn kết thúc
            },
            order: [['gia_tri', 'DESC']] // Mã giảm giá trị cao xếp trên
        });

        res.json({
            success: true,
            data: vouchers
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ success: false, message: 'Lỗi lấy danh sách voucher' });
    }
});
module.exports = router;