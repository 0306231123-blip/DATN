const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken');

// IMPORT ĐÚNG ĐƯỜNG DẪN 
const sequelize = require('../config/database');
const GioHang = require('../models/GioHang');
const SanPham = require('../models/SanPham');
const DonHang = require('../models/DonHang');
const ChiTietDonHang = require('../models/ChiTietDonHang');
const YeuCauTraHang = require('../models/YeuCauTraHang');
const NguoiDung = require('../models/NguoiDung');

// --- 1. TẠO ĐƠN HÀNG (TRỪ KHO) ---
router.post('/create', async (req, res) => {
    // ... [ĐOẠN CODE NÀY GIỮ NGUYÊN KHÔNG ĐỔI] ...
    const t = await sequelize.transaction();
    try {
        const token = req.headers.authorization.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        const { dia_chi, so_dien_thoai } = req.body; 
        
        if (!dia_chi || dia_chi.trim() === '' || dia_chi.trim().startsWith(',')) {
            await t.rollback();
            return res.status(400).json({ success: false, message: 'Bắt buộc phải nhập đầy đủ số nhà và tên đường!' });
        }

        const maNguoiDung = decoded.ma_nguoi_dung;
        const user = await NguoiDung.findByPk(maNguoiDung);

        const items = await GioHang.findAll({
            where: { ma_nguoi_dung: maNguoiDung },
            include: [{ model: SanPham, as: 'san_pham' }]
        });

        if (!items || items.length === 0) {
            await t.rollback();
            return res.status(400).json({ success: false, message: 'Giỏ hàng trống' });
        }

        let tong_tien = 0;
        for (let item of items) {
            if (item.san_pham.so_luong_ton < item.so_luong) throw new Error(`Sản phẩm ${item.san_pham.ten_san_pham} không đủ hàng!`);
            tong_tien += (item.san_pham.gia_khuyen_mai || item.san_pham.gia) * item.so_luong;
        }

        const donHangMoi = await DonHang.create({
            ma_nguoi_dung: maNguoiDung, 
            ho_ten_nguoi_nhan: req.body.ho_ten || (user ? user.ho_ten : 'Khách hàng'), 
            so_dien_thoai_nhan: so_dien_thoai || (user ? user.so_dien_thoai : '0123456789'), 
            dia_chi_giao: dia_chi, 
            tong_tien_hang: tong_tien, 
            tong_thanh_toan: tong_tien,
            trang_thai_don: 'cho_xac_nhan', 
            phuong_thuc_thanh_toan: 'chuyen_khoan', 
            trang_thai_thanh_toan: 'da_thanh_toan'
        }, { transaction: t });

        for (let item of items) {
            await SanPham.decrement('so_luong_ton', { by: item.so_luong, where: { ma_san_pham: item.ma_san_pham }, transaction: t });
            await ChiTietDonHang.create({
                ma_don_hang: donHangMoi.ma_don_hang, ma_san_pham: item.ma_san_pham, ten_san_pham: item.san_pham.ten_san_pham, 
                so_luong: item.so_luong, don_gia: (item.san_pham.gia_khuyen_mai || item.san_pham.gia),
                thanh_tien: (item.san_pham.gia_khuyen_mai || item.san_pham.gia) * item.so_luong
            }, { transaction: t });
        }

        await GioHang.destroy({ where: { ma_nguoi_dung: maNguoiDung }, transaction: t });
        await t.commit();
        res.json({ success: true, message: 'Đặt hàng thành công!' });
    } catch (error) {
        await t.rollback();
        res.status(500).json({ success: false, message: error.message });
    }
});

// --- 2. CẬP NHẬT TRẠNG THÁI (HỦY ĐƠN / TRẢ HÀNG / HOÀN THÀNH) ---
router.post('/update-status', async (req, res) => {
    const t = await sequelize.transaction();
    try {
        const { ma_don_hang, trang_thai, ly_do_tra_hang, ly_do_huy_don } = req.body;
        
        const donHang = await DonHang.findByPk(ma_don_hang, {
            include: [{ model: ChiTietDonHang, as: 'chi_tiet' }],
            transaction: t
        });

        if (!donHang) {
            await t.rollback();
            return res.status(404).json({ success: false, message: 'Không tìm thấy đơn' });
        }

        // CASE 1: HỦY ĐƠN
        if (trang_thai === 'da_huy') {
            if (donHang.trang_thai_don !== 'cho_xac_nhan' && donHang.trang_thai_don !== 'cho_xu_ly') {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Đơn đã xác nhận hoặc đang giao, không hủy được!' });
            }
            
            // LƯU LOGIC ĐẾM SỐ ĐƠN HỦY & TỰ ĐỘNG KHÓA
            const soDonHuyTruocDo = await DonHang.count({
                where: {
                    ma_nguoi_dung: maNguoiDung,
                    trang_thai_don: 'da_huy'
                },
                transaction: t
            });
            
            // Nếu đây là lần hủy thứ 5 (đã có 4 đơn hủy trước đó + đơn này là 5)
            if (soDonHuyTruocDo >= 4) {
                await NguoiDung.update({ 
                    trang_thai: 'bi_khoa',
                    ly_do_khoa: 'Tài khoản bị khóa do hủy quá nhiều đơn hàng (5 đơn)'
                }, { 
                    where: { ma_nguoi_dung: maNguoiDung }, 
                    transaction: t 
                });
            }
            
            // Lưu lý do hủy trực tiếp vào bảng don_hang
            donHang.ly_do_huy_don = ly_do_huy_don;
            
            for (let item of donHang.chi_tiet) {
                await SanPham.increment('so_luong_ton', {
                    by: item.so_luong, where: { ma_san_pham: item.ma_san_pham }, transaction: t
                });
            }
        } 
        
        // ==========================================
        // CASE 2: YÊU CẦU TRẢ HÀNG (GHI VÀO BẢNG MỚI)
        // ==========================================
        else if (trang_thai === 'tra_hang_hoan_tien' || trang_thai === 'dang_tra_hang') {
            if (donHang.trang_thai_don !== 'giao_thanh_cong') {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Chỉ đơn hàng đã giao thành công mới được yêu cầu trả hàng!' });
            }
            
            // LƯU LOGIC VÀO BẢNG `yeu_cau_tra_hang`
            await YeuCauTraHang.create({
                ma_don_hang: ma_don_hang,
                ly_do: ly_do_tra_hang || 'Không có lý do', // Đảm bảo không bị null
                trang_thai: 'cho_duyet', // Map với ENUM của Admin
                ngay_yeu_cau: new Date()
            }, { transaction: t });

            // Ép kiểu trạng thái về chuẩn của hệ thống Admin
            trang_thai = 'dang_tra_hang';
        }

        // CASE 3: KHÁCH BẤM "ĐÃ NHẬN ĐƯỢC HÀNG" (HOÀN THÀNH)
        else if (trang_thai === 'hoan_thanh') {
            if (donHang.trang_thai_don !== 'giao_thanh_cong') {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Chỉ đơn hàng đã giao mới có thể xác nhận hoàn thành!' });
            }
        }

        // Cập nhật trạng thái mới cho bảng đơn hàng
        donHang.trang_thai_don = trang_thai;
        await donHang.save({ transaction: t });

        await t.commit();
        res.json({ success: true, message: 'Cập nhật trạng thái thành công' });
    } catch (error) {
        await t.rollback();
        console.log(error);
        res.status(500).json({ success: false, message: error.message });
    }
});

// --- 3. LẤY LỊCH SỬ ĐƠN HÀNG (AUTO-COMPLETE 7 NGÀY) ---
router.get('/my-orders', async (req, res) => {
    try {
        const token = req.headers.authorization.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);

        const orders = await DonHang.findAll({
            where: { ma_nguoi_dung: decoded.ma_nguoi_dung },
            order: [['ngay_dat', 'DESC']], 
            include: [{ 
                model: ChiTietDonHang, as: 'chi_tiet', include: [{ model: SanPham, as: 'san_pham' }] 
            }]
        });

        const now = new Date();
        for (let order of orders) {
            if (order.trang_thai_don === 'giao_thanh_cong') {
                const ngayGiao = new Date(order.ngay_cap_nhat || order.ngay_dat);
                const diffTime = Math.abs(now - ngayGiao);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                
                if (diffDays >= 7) {
                    order.trang_thai_don = 'hoan_thanh';
                    await order.save();
                }
            }
        }

        res.json({ success: true, data: orders });
    } catch (error) {
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});

module.exports = router;