const express = require('express');
const router = express.Router();
const jwt = require('jsonwebtoken');
const GioHang = require('../models/GioHang');
const SanPham = require('../models/SanPham');
const DonHang = require('../models/DonHang'); // Hãy đảm bảo bạn đã tạo model này
const ChiTietDonHang = require('../models/ChiTietDonHang'); // Hãy đảm bảo bạn đã tạo model này

router.post('/create', async (req, res) => {
    try {
        const token = req.headers.authorization.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        const { dia_chi } = req.body;
        const maNguoiDung = decoded.ma_nguoi_dung;

        // 1. Lấy toàn bộ sản phẩm trong giỏ hàng
        const items = await GioHang.findAll({
            where: { ma_nguoi_dung: maNguoiDung },
            include: [{ model: SanPham, as: 'san_pham' }]
        });

        if (items.length === 0) {
            return res.status(400).json({ success: false, message: 'Giỏ hàng trống' });
        }

        // 2. Tính tổng tiền
        let tong_tien = 0;
        items.forEach(item => {
            const gia = item.san_pham.gia_khuyen_mai || item.san_pham.gia;
            tong_tien += gia * item.so_luong;
        });

       const donHangMoi = await DonHang.create({
            ma_nguoi_dung: maNguoiDung,
            ho_ten_nguoi_nhan: 'Khách hàng', // Dữ liệu tạm (vì form chưa có)
            so_dien_thoai_nhan: '0123456789', // Dữ liệu tạm 
            dia_chi_giao: dia_chi,
            tong_tien_hang: tong_tien,
            phi_van_chuyen: 0,
            tong_thanh_toan: tong_tien,
            phuong_thuc_thanh_toan: 'chuyen_khoan', 
            trang_thai_thanh_toan: 'da_thanh_toan', // Giả sử quét QR là đã thanh toán
            trang_thai_don: 'giao_thanh_cong' 
        });

        // 4. Lưu từng sản phẩm vào bảng chi_tiet_don_hang
        for (let item of items) {
            const gia = item.san_pham.gia_khuyen_mai || item.san_pham.gia;
            const thanhTien = gia * item.so_luong; // Tính thành tiền theo DB
            
            await ChiTietDonHang.create({
                ma_don_hang: donHangMoi.ma_don_hang, 
                ma_san_pham: item.ma_san_pham,
                ten_san_pham: item.san_pham.ten_san_pham, // Thêm tên SP
                don_gia: gia, // Dùng don_gia thay vì gia
                so_luong: item.so_luong,
                thanh_tien: thanhTien // Thêm cột thanh_tien
            });
        }

        // 5. Xóa sạch giỏ hàng của user sau khi đặt hàng thành công
        await GioHang.destroy({ where: { ma_nguoi_dung: maNguoiDung } });

        res.json({ success: true, message: 'Tạo đơn hàng thành công' });

    } catch (error) {
        console.error("Lỗi tạo đơn hàng:", error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});
DonHang.hasMany(ChiTietDonHang, { foreignKey: 'ma_don_hang', as: 'chi_tiet' });
router.get('/my-orders', async (req, res) => {
    try {
        const token = req.headers.authorization.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);

        // Tìm tất cả đơn hàng của user này, sắp xếp ngày đặt mới nhất lên đầu
        const orders = await DonHang.findAll({
            where: { ma_nguoi_dung: decoded.ma_nguoi_dung },
            order: [['ngay_dat', 'DESC']],
            include: [{ model: ChiTietDonHang, as: 'chi_tiet' }]
        });

        res.json({ success: true, data: orders });
    } catch (error) {
        console.error("Lỗi lấy danh sách đơn hàng:", error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
});
module.exports = router;