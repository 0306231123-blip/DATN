const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken');
const { verifyToken } = require('../middleware/verifyToken'); // Nhớ check lại đúng đường dẫn thư mục middleware của ông nhé

// IMPORT ĐÚNG ĐƯỜNG DẪN 
const sequelize = require('../config/database');
const GioHang = require('../models/GioHang');
const SanPham = require('../models/SanPham');
const DonHang = require('../models/DonHang');
const ChiTietDonHang = require('../models/ChiTietDonHang');
const YeuCauTraHang = require('../models/YeuCauTraHang');
const NguoiDung = require('../models/NguoiDung');
const KhuyenMai = require('../models/KhuyenMai');

// --- 1. TẠO ĐƠN HÀNG (TRỪ KHO) ---
// --- 1. TẠO ĐƠN HÀNG (TRỪ KHO VÀ TRỪ VOUCHER) ---
router.post('/create', async (req, res) => {
    const t = await sequelize.transaction();
    try {
        const token = req.headers.authorization.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        
        // 1. Lấy dữ liệu từ Frontend gửi lên
        const { dia_chi, so_dien_thoai, ma_khuyen_mai, so_tien_giam, phi_van_chuyen, phuong_thuc_thanh_toan } = req.body; 
        
        if (!dia_chi || dia_chi.trim() === '' || dia_chi.trim().startsWith(',')) {
            await t.rollback();
            return res.status(400).json({ success: false, message: 'Bắt buộc phải nhập đầy đủ số nhà và tên đường!' });
        }

        const maNguoiDung = decoded.ma_nguoi_dung;
        const user = await NguoiDung.findByPk(maNguoiDung);
        const { Op } = require('sequelize');

        let whereCondition = { ma_nguoi_dung: maNguoiDung };
        if (req.body.selected_items && Array.isArray(req.body.selected_items) && req.body.selected_items.length > 0) {
            whereCondition.ma_san_pham = { [Op.in]: req.body.selected_items };
        }

        const items = await GioHang.findAll({
            where: whereCondition,
            include: [{ model: SanPham, as: 'san_pham' }]
        });

        if (!items || items.length === 0) {
            await t.rollback();
            return res.status(400).json({ success: false, message: 'Giỏ hàng trống hoặc không có sản phẩm được chọn' });
        }

        // 2. Tính TỔNG TIỀN HÀNG từ các món trong giỏ
        let tong_tien = 0;
        for (let item of items) {
            if (item.san_pham.so_luong_ton < item.so_luong) throw new Error(`Sản phẩm ${item.san_pham.ten_san_pham} không đủ hàng!`);
            tong_tien += (item.san_pham.gia_khuyen_mai || item.san_pham.gia) * item.so_luong;
        }

        // 3. Xử lý tính toán VOUCHER VÀ SHIP
        const tienGiam = so_tien_giam || 0;
        const tienShip = phi_van_chuyen || 0;
        let tongThanhToan = tong_tien + tienShip - tienGiam;
        if (tongThanhToan < 0) tongThanhToan = 0; // Chống lỗi âm tiền

        // 4. Lệnh tạo đơn hàng (Đã sửa lại biến user cho chuẩn)
        const donHangMoi = await DonHang.create({
            ma_nguoi_dung: maNguoiDung, 
            ho_ten_nguoi_nhan: req.body.ho_ten || (user ? user.ho_ten : 'Khách hàng'), 
            so_dien_thoai_nhan: so_dien_thoai || (user ? user.so_dien_thoai : '0123456789'), 
            dia_chi_giao: dia_chi, 
            tong_tien_hang: tong_tien,          // Giá gốc
            phi_van_chuyen: tienShip,
            tong_thanh_toan: tongThanhToan,     // Giá đã tính ship & voucher
            ma_khuyen_mai: ma_khuyen_mai || null,
            so_tien_giam: tienGiam,
            trang_thai_don: 'cho_xac_nhan', 
            phuong_thuc_thanh_toan: phuong_thuc_thanh_toan || 'chuyen_khoan', 
            trang_thai_thanh_toan: phuong_thuc_thanh_toan === 'cod' ? 'chua_thanh_toan' : 'da_thanh_toan'
        }, { transaction: t });

        // 5. TRỪ LƯỢT SỬ DỤNG VOUCHER (Nếu có áp dụng mã)
        if (ma_khuyen_mai) {
            const daSuDung = await DonHang.findOne({
                where: {
                    ma_nguoi_dung: maNguoiDung,
                    ma_khuyen_mai: ma_khuyen_mai,
                    trang_thai_don: { [Op.notIn]: ['da_huy', 'da_tra_hang'] }
                },
                transaction: t
            });
            
            if (daSuDung) {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Bạn đã sử dụng mã khuyến mãi này rồi, và đơn hàng vẫn đang tồn tại!' });
            }

            const voucher = await KhuyenMai.findByPk(ma_khuyen_mai, { transaction: t });
            if (voucher && voucher.so_luong > 0) {
                await voucher.decrement('so_luong', { by: 1, transaction: t });
            }
        }

        // 6. Lưu chi tiết đơn hàng và trừ kho sản phẩm
        for (let item of items) {
            await SanPham.decrement('so_luong_ton', { by: item.so_luong, where: { ma_san_pham: item.ma_san_pham }, transaction: t });
            await ChiTietDonHang.create({
                ma_don_hang: donHangMoi.ma_don_hang, 
                ma_san_pham: item.ma_san_pham, 
                ten_san_pham: item.san_pham.ten_san_pham, 
                so_luong: item.so_luong, 
                don_gia: (item.san_pham.gia_khuyen_mai || item.san_pham.gia),
                thanh_tien: (item.san_pham.gia_khuyen_mai || item.san_pham.gia) * item.so_luong
            }, { transaction: t });
        }

        // 7. Xóa các sản phẩm đã đặt khỏi giỏ hàng và lưu Transaction
        let destroyWhere = { ma_nguoi_dung: maNguoiDung };
        if (req.body.selected_items && Array.isArray(req.body.selected_items) && req.body.selected_items.length > 0) {
            destroyWhere.ma_san_pham = { [Op.in]: req.body.selected_items };
        }
        await GioHang.destroy({ where: destroyWhere, transaction: t });
        await t.commit();
        
        res.json({ success: true, message: 'Đặt hàng thành công!', ma_don_hang: donHangMoi.ma_don_hang });
    } catch (error) {
        await t.rollback();
        console.error("Lỗi tạo đơn:", error);
        res.status(500).json({ success: false, message: error.message });
    }
});

// --- 2. CẬP NHẬT TRẠNG THÁI (HỦY ĐƠN / TRẢ HÀNG / HOÀN THÀNH) ---
router.post('/update-status', async (req, res) => {
    const t = await sequelize.transaction();
    try {
        // Lấy thêm 3 trường ngân hàng từ req.body
        let { 
            ma_don_hang, 
            trang_thai, 
            ly_do_tra_hang, 
            ly_do_huy_don,
            ngan_hang_hoan_tien,
            stk_hoan_tien,
            chu_tk_hoan_tien
        } = req.body;
        
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
            if (donHang.trang_thai_don !== 'cho_xac_nhan' && donHang.trang_thai_don !== 'cho_xu_ly' && donHang.trang_thai_don !== 'da_xac_nhan') {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Đơn đang giao hoặc đã hoàn thành, không hủy được!' });
            }
            
            // QUY TẮC: Chỉ cho phép hủy trong vòng 30 phút kể từ khi đặt hàng
            const orderDate = new Date(donHang.ngay_dat);
            const diffMinutes = Math.floor((new Date() - orderDate) / (1000 * 60));
            if (diffMinutes > 30) {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Đã quá 30 phút kể từ lúc đặt hàng, bạn không thể tự hủy đơn! Vui lòng liên hệ Hotline để được hỗ trợ.' });
            }
            
            const soDonHuyTruocDo = await DonHang.count({
                where: {
                    ma_nguoi_dung: donHang.ma_nguoi_dung, 
                    trang_thai_don: 'da_huy'
                },
                transaction: t
            });
            
            if (soDonHuyTruocDo >= 4) {
                await NguoiDung.update({ 
                    trang_thai: 'bi_khoa',
                    ly_do_khoa: 'Tài khoản bị khóa do hủy quá nhiều đơn hàng (5 đơn)'
                }, { 
                    where: { ma_nguoi_dung: donHang.ma_nguoi_dung }, 
                    transaction: t 
                });
            }
            
            donHang.ly_do_huy_don = ly_do_huy_don;
            
            for (let item of donHang.chi_tiet) {
                await SanPham.increment('so_luong_ton', {
                    by: item.so_luong, where: { ma_san_pham: item.ma_san_pham }, transaction: t
                });
            }
            // HOÀN LẠI VOUCHER
            if (donHang.ma_khuyen_mai) {
                await KhuyenMai.increment('so_luong', { by: 1, where: { ma_khuyen_mai: donHang.ma_khuyen_mai }, transaction: t });
            }
        } 
        
        // ==========================================
        // CASE 2: YÊU CẦU TRẢ HÀNG (GHI VÀO BẢNG YÊU CẦU + LƯU ĐỐI CHIẾU VÀO ĐƠN HÀNG)
        // ==========================================
        else if (trang_thai === 'tra_hang_hoan_tien' || trang_thai === 'dang_tra_hang') {
            if (donHang.trang_thai_don !== 'giao_thanh_cong') {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Chỉ đơn hàng đã giao thành công mới được yêu cầu trả hàng!' });
            }
            
            // 1. Lưu vào bảng yeu_cau_tra_hang
            await YeuCauTraHang.create({
                ma_don_hang: ma_don_hang,
                ly_do: ly_do_tra_hang || 'Không có lý do', 
                trang_thai: 'cho_duyet', 
                ngay_yeu_cau: new Date()
            }, { transaction: t });

            // 2. LƯU THÔNG TIN ĐỐI CHIẾU TRỰC TIẾP VÀO BẢNG DON_HANG
            donHang.ly_do_tra_hang = ly_do_tra_hang;
            donHang.ngan_hang_hoan_tien = ngan_hang_hoan_tien;
            donHang.stk_hoan_tien = stk_hoan_tien;
            donHang.chu_tk_hoan_tien = chu_tk_hoan_tien;

            trang_thai = 'dang_tra_hang';
        }

        // CASE 3: KHÁCH BẤM "ĐÃ NHẬN ĐƯỢC HÀNG" (HOÀN THÀNH)
        else if (trang_thai === 'hoan_thanh') {
            if (donHang.trang_thai_don !== 'giao_thanh_cong') {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Chỉ đơn hàng đã giao mới có thể xác nhận hoàn thành!' });
            }
        }
        
        // CASE 4: ADMIN DUYỆT ĐÃ TRẢ HÀNG -> CỘNG LẠI KHO
        else if (trang_thai === 'da_tra_hang') {
            for (let item of donHang.chi_tiet) {
                await SanPham.increment('so_luong_ton', {
                    by: item.so_luong, 
                    where: { ma_san_pham: item.ma_san_pham }, 
                    transaction: t
                });
            }
            // HOÀN LẠI VOUCHER
            if (donHang.ma_khuyen_mai) {
                await KhuyenMai.increment('so_luong', { by: 1, where: { ma_khuyen_mai: donHang.ma_khuyen_mai }, transaction: t });
            }
        }

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
router.post('/request-refund', verifyToken, async (req, res) => {
    try {
        // Lấy dữ liệu từ Frontend gửi lên
        const { ma_don_hang, ly_do, ngan_hang, so_tai_khoan, chu_tai_khoan } = req.body;

        // Bắt lỗi nếu khách cố tình không nhập
        if (!ngan_hang || !so_tai_khoan || !chu_tai_khoan) {
            return res.status(400).json({ success: false, message: 'Vui lòng nhập đầy đủ thông tin nhận tiền hoàn!' });
        }

        // Lưu vào Database
        await YeuCauTraHang.create({
            ma_don_hang: ma_don_hang,
            ma_nguoi_dung: req.user.ma_nguoi_dung,
            ly_do: ly_do,
            ngan_hang: ngan_hang,
            so_tai_khoan: so_tai_khoan,
            chu_tai_khoan: chu_tai_khoan,
            trang_thai: 'cho_xac_nhan' // Trạng thái mặc định
        });

        res.json({ success: true, message: 'Đã gửi yêu cầu hoàn tiền thành công!' });
    } catch (error) {
        console.error('Lỗi hoàn tiền:', error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});

// --- HARD DELETE ĐƠN HÀNG KHI HỦY THANH TOÁN QR ---
router.post('/delete-unpaid', verifyToken, async (req, res) => {
    const t = await sequelize.transaction();
    try {
        const { ma_don_hang } = req.body;
        const donHang = await DonHang.findByPk(ma_don_hang, {
            include: [{ model: ChiTietDonHang, as: 'chi_tiet' }],
            transaction: t
        });

        if (!donHang) {
            await t.rollback();
            return res.status(404).json({ success: false, message: 'Không tìm thấy đơn hàng' });
        }

        // Hoàn lại kho
        if (donHang.chi_tiet && donHang.chi_tiet.length > 0) {
            for (let item of donHang.chi_tiet) {
                await SanPham.increment('so_luong_ton', {
                    by: item.so_luong,
                    where: { ma_san_pham: item.ma_san_pham },
                    transaction: t
                });
            }
            // Xóa chi tiết đơn hàng
            await ChiTietDonHang.destroy({ where: { ma_don_hang }, transaction: t });
        }

        // Hoàn lại voucher (nếu có)
        if (donHang.ma_khuyen_mai) {
            await KhuyenMai.increment('so_luong', { by: 1, where: { ma_khuyen_mai: donHang.ma_khuyen_mai }, transaction: t });
        }

        // Xóa đơn hàng
        await donHang.destroy({ transaction: t });

        await t.commit();
        res.json({ success: true, message: 'Đã hủy và xóa đơn hàng thành công' });
    } catch (error) {
        await t.rollback();
        console.error('Lỗi xóa đơn:', error);
        res.status(500).json({ success: false, message: 'Lỗi server khi xóa đơn' });
    }
});

module.exports = router;