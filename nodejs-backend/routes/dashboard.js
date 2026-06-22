const express = require('express');
const sequelize = require('../config/database');
const { QueryTypes } = require('sequelize');

const router = express.Router();

/**
 * GET /api/dashboard/stats
 * Lấy toàn bộ dữ liệu thống kê cho trang Dashboard
 */
router.get('/stats', async (req, res) => {
  try {
    // ========== 1. Thống kê tổng quan ==========

    // Tổng doanh thu (chỉ đơn giao thành công)
    const [revenueResult] = await sequelize.query(
      `SELECT COALESCE(SUM(tong_thanh_toan), 0) AS tong_doanh_thu 
       FROM don_hang 
       WHERE trang_thai_don = 'giao_thanh_cong'`,
      { type: QueryTypes.SELECT }
    );

    // Tổng số đơn hàng
    const [ordersResult] = await sequelize.query(
      `SELECT COUNT(*) AS tong_don_hang FROM don_hang`,
      { type: QueryTypes.SELECT }
    );

    // Khách hàng mới (đăng ký trong tháng hiện tại)
    const [newCustomersResult] = await sequelize.query(
      `SELECT COUNT(*) AS khach_hang_moi 
       FROM nguoi_dung 
       WHERE vai_tro = 'khach_hang' 
         AND MONTH(ngay_tao) = MONTH(CURRENT_DATE()) 
         AND YEAR(ngay_tao) = YEAR(CURRENT_DATE())`,
      { type: QueryTypes.SELECT }
    );

    // Số sản phẩm đang bán
    const [productsResult] = await sequelize.query(
      `SELECT COUNT(*) AS san_pham_dang_ban 
       FROM san_pham 
       WHERE trang_thai = 'dang_ban'`,
      { type: QueryTypes.SELECT }
    );

    // Điểm đánh giá trung bình
    const [ratingResult] = await sequelize.query(
      `SELECT COALESCE(ROUND(AVG(diem_so), 1), 0) AS danh_gia_trung_binh 
       FROM danh_gia 
       WHERE trang_thai = 'hien_thi'`,
      { type: QueryTypes.SELECT }
    );

    // ========== 2. Doanh thu & đơn hàng theo tháng (6 tháng gần nhất) ==========
    const doanhThuTheoThang = await sequelize.query(
      `SELECT 
         MONTH(ngay_dat) AS thang,
         YEAR(ngay_dat) AS nam,
         COALESCE(SUM(CASE WHEN trang_thai_don = 'giao_thanh_cong' THEN tong_thanh_toan ELSE 0 END), 0) AS doanh_thu,
         COUNT(*) AS so_don
       FROM don_hang
       WHERE ngay_dat >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
       GROUP BY YEAR(ngay_dat), MONTH(ngay_dat)
       ORDER BY YEAR(ngay_dat) ASC, MONTH(ngay_dat) ASC`,
      { type: QueryTypes.SELECT }
    );

    // Tạo mảng đầy đủ 6 tháng (kể cả tháng không có dữ liệu)
    const now = new Date();
    const monthlyData = [];
    for (let i = 5; i >= 0; i--) {
      const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
      const m = d.getMonth() + 1;
      const y = d.getFullYear();
      const found = doanhThuTheoThang.find(r => r.thang === m && r.nam === y);
      monthlyData.push({
        thang: `T${m}`,
        nam: y,
        doanh_thu: found ? Number(found.doanh_thu) : 0,
        so_don: found ? Number(found.so_don) : 0,
      });
    }

    // ========== 3. Doanh thu theo danh mục ==========
    const doanhThuTheoDanhMuc = await sequelize.query(
      `SELECT 
         dm.ten_danh_muc,
         COALESCE(SUM(ctdh.thanh_tien), 0) AS tong_doanh_thu
       FROM danh_muc dm
       LEFT JOIN san_pham sp ON dm.ma_danh_muc = sp.ma_danh_muc
       LEFT JOIN chi_tiet_don_hang ctdh ON sp.ma_san_pham = ctdh.ma_san_pham
       LEFT JOIN don_hang dh ON ctdh.ma_don_hang = dh.ma_don_hang 
         AND dh.trang_thai_don = 'giao_thanh_cong'
       GROUP BY dm.ma_danh_muc, dm.ten_danh_muc
       HAVING tong_doanh_thu > 0
       ORDER BY tong_doanh_thu DESC`,
      { type: QueryTypes.SELECT }
    );

    // ========== 4. Sản phẩm bán chạy (top 5) ==========
    const sanPhamBanChay = await sequelize.query(
      `SELECT 
         sp.ten_san_pham, 
         sp.thuong_hieu,
         COALESCE(SUM(ctdh.so_luong), 0) AS tong_so_luong_ban,
         COALESCE(SUM(ctdh.thanh_tien), 0) AS tong_doanh_thu
       FROM san_pham sp
       JOIN chi_tiet_don_hang ctdh ON sp.ma_san_pham = ctdh.ma_san_pham
       JOIN don_hang dh ON ctdh.ma_don_hang = dh.ma_don_hang
       WHERE dh.trang_thai_don = 'giao_thanh_cong'
       GROUP BY sp.ma_san_pham, sp.ten_san_pham, sp.thuong_hieu
       ORDER BY tong_so_luong_ban DESC
       LIMIT 5`,
      { type: QueryTypes.SELECT }
    );

    // ========== 5. Đơn hàng gần đây (5 đơn mới nhất) ==========
    const donHangGanDay = await sequelize.query(
      `SELECT 
         dh.ma_don_hang,
         dh.ho_ten_nguoi_nhan,
         dh.tong_thanh_toan,
         dh.trang_thai_don,
         dh.ngay_dat
       FROM don_hang dh
       ORDER BY dh.ngay_dat DESC
       LIMIT 5`,
      { type: QueryTypes.SELECT }
    );

    // ========== 6. Thống kê trạng thái đơn hàng ==========
    const trangThaiDon = await sequelize.query(
      `SELECT 
         trang_thai_don, 
         COUNT(*) AS so_luong
       FROM don_hang
       GROUP BY trang_thai_don`,
      { type: QueryTypes.SELECT }
    );

    const thongKeTrangThai = {
      cho_xac_nhan: 0,
      da_xac_nhan: 0,
      dang_giao: 0,
      giao_thanh_cong: 0,
      da_huy: 0,
    };
    trangThaiDon.forEach(row => {
      thongKeTrangThai[row.trang_thai_don] = Number(row.so_luong);
    });

    // ========== Trả kết quả ==========
    res.json({
      success: true,
      data: {
        stats: {
          tong_doanh_thu: Number(revenueResult.tong_doanh_thu),
          tong_don_hang: Number(ordersResult.tong_don_hang),
          khach_hang_moi: Number(newCustomersResult.khach_hang_moi),
          san_pham_dang_ban: Number(productsResult.san_pham_dang_ban),
          danh_gia_trung_binh: Number(ratingResult.danh_gia_trung_binh),
        },
        doanh_thu_theo_thang: monthlyData,
        doanh_thu_theo_danh_muc: doanhThuTheoDanhMuc.map(r => ({
          ten_danh_muc: r.ten_danh_muc,
          tong_doanh_thu: Number(r.tong_doanh_thu),
        })),
        san_pham_ban_chay: sanPhamBanChay.map(r => ({
          ten_san_pham: r.ten_san_pham,
          thuong_hieu: r.thuong_hieu,
          tong_so_luong_ban: Number(r.tong_so_luong_ban),
          tong_doanh_thu: Number(r.tong_doanh_thu),
        })),
        don_hang_gan_day: donHangGanDay.map(r => ({
          ma_don_hang: r.ma_don_hang,
          ho_ten_nguoi_nhan: r.ho_ten_nguoi_nhan,
          tong_thanh_toan: Number(r.tong_thanh_toan),
          trang_thai_don: r.trang_thai_don,
          ngay_dat: r.ngay_dat,
        })),
        thong_ke_trang_thai_don: thongKeTrangThai,
      },
    });
  } catch (error) {
    console.error('Dashboard stats error:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi khi lấy dữ liệu thống kê.',
      error: error.message,
    });
  }
});

module.exports = router;
