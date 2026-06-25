const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken');

// IMPORT ĐÚNG ĐƯỜNG DẪN (Kiểm tra lại tên file trong thư mục models của ông)
const sequelize = require('../config/database');
const GioHang = require('../models/GioHang');
const SanPham = require('../models/SanPham');
const DonHang = require('../models/DonHang');
const ChiTietDonHang = require('../models/ChiTietDonHang');

// --- 1. TẠO ĐƠN HÀNG (TRỪ KHO) ---
router.post('/create', async (req, res) => {
    const t = await sequelize.transaction();
    try {
        const token = req.headers.authorization.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        const { dia_chi, so_dien_thoai } = req.body; // Lấy thêm số điện thoại từ frontend nếu có
        const maNguoiDung = decoded.ma_nguoi_dung;

        const items = await GioHang.findAll({
            where: { ma_nguoi_dung: maNguoiDung },
            include: [{ model: SanPham, as: 'san_pham' }]
        });

        if (!items || items.length === 0) {
            await t.rollback();
            return res.status(400).json({ success: false, message: 'Giỏ hàng trống' });
        }

        // 1. Tính toán
        let tong_tien = 0;
        for (let item of items) {
            if (item.san_pham.so_luong_ton < item.so_luong) {
                throw new Error(`Sản phẩm ${item.san_pham.ten_san_pham} không đủ hàng!`);
            }
            tong_tien += (item.san_pham.gia_khuyen_mai || item.san_pham.gia) * item.so_luong;
        }

        // 2. Tạo đơn hàng (ĐẦY ĐỦ CÁC TRƯỜNG)
        const donHangMoi = await DonHang.create({
            ma_nguoi_dung: maNguoiDung,
            ho_ten_nguoi_nhan: 'Khách hàng', // Nên lấy từ req.body nếu có
            so_dien_thoai_nhan: so_dien_thoai || '0123456789', // Mặc định nếu không có
            dia_chi_giao: dia_chi,
            tong_tien_hang: tong_tien,
            tong_thanh_toan: tong_tien,
            trang_thai_don: 'cho_xac_nhan',
            phuong_thuc_thanh_toan: 'chuyen_khoan',
            trang_thai_thanh_toan: 'da_thanh_toan'
        }, { transaction: t });

        // 3. Trừ kho và tạo chi tiết
        for (let item of items) {
            await SanPham.decrement('so_luong_ton', {
                by: item.so_luong,
                where: { ma_san_pham: item.ma_san_pham },
                transaction: t
            });

            await ChiTietDonHang.create({
                ma_don_hang: donHangMoi.ma_don_hang,
                ma_san_pham: item.ma_san_pham,
                ten_san_pham: item.san_pham.ten_san_pham, // BẮT BUỘC CÓ DÒNG NÀY
                so_luong: item.so_luong,
                don_gia: (item.san_pham.gia_khuyen_mai || item.san_pham.gia),
                thanh_tien: (item.san_pham.gia_khuyen_mai || item.san_pham.gia) * item.so_luong
            }, { transaction: t });
        }

        await GioHang.destroy({ where: { ma_nguoi_dung: maNguoiDung }, transaction: t });
        await t.commit();
        res.json({ success: true, message: 'Đặt hàng thành công!' });
    } catch (error) {
        await t.rollback();
        console.error("LỖI:", error);
        res.status(500).json({ success: false, message: error.message });
    }
});

// --- 2. HỦY ĐƠN HÀNG (CỘNG LẠI KHO) ---
router.post('/update-status', async (req, res) => {
    const t = await sequelize.transaction();
    try {
        const { ma_don_hang, trang_thai } = req.body;
        
        const donHang = await DonHang.findByPk(ma_don_hang, {
            include: [{ model: ChiTietDonHang, as: 'chi_tiet' }],
            transaction: t
        });

        if (!donHang) {
            await t.rollback();
            return res.status(404).json({ success: false, message: 'Không tìm thấy đơn' });
        }

        // Logic hủy: Chỉ được hủy khi đang chờ xác nhận
        if (trang_thai === 'da_huy') {
            if (donHang.trang_thai_don !== 'cho_xac_nhan') {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Đơn đã xác nhận, không hủy được!' });
            }

            // CỘNG LẠI KHO
            for (let item of donHang.chi_tiet) {
                await SanPham.increment('so_luong_ton', {
                    by: item.so_luong,
                    where: { ma_san_pham: item.ma_san_pham },
                    transaction: t
                });
            }
        }

        donHang.trang_thai_don = trang_thai;
        await donHang.save({ transaction: t });

        await t.commit();
        res.json({ success: true, message: 'Cập nhật thành công' });
    } catch (error) {
        await t.rollback();
        res.status(500).json({ success: false, message: error.message });
    }
});
router.get('/my-orders', async (req, res) => {
    try {
        const token = req.headers.authorization.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        const maNguoiDung = decoded.ma_nguoi_dung;

        const orders = await DonHang.findAll({
            where: { ma_nguoi_dung: maNguoiDung },
            order: [['ngay_dat', 'DESC']], // Sắp xếp đơn mới nhất lên đầu
            include: [{ 
                model: ChiTietDonHang, 
                as: 'chi_tiet',
                include: [{ model: SanPham, as: 'san_pham' }] // Thêm cái này để lấy tên sp, ảnh sp
            }]
        });

        res.json({ success: true, data: orders });
    } catch (error) {
        console.error("Lỗi lấy lịch sử đơn hàng:", error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});

module.exports = router;