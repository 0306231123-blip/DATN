const express = require('express');
const sequelize = require('../config/database');
const { QueryTypes } = require('sequelize');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');

const router = express.Router();

router.use(verifyToken, requireAdmin);

/**
 * GET /api/statistics/overview
 * Lấy toàn bộ dữ liệu thống kê cho trang Statistics
 */
router.get('/overview', async (req, res) => {
  try {
    // ========== 1. Doanh thu tháng hiện tại ==========
    const [currentMonth] = await sequelize.query(
      `SELECT 
         COALESCE(SUM(CASE WHEN trang_thai_don = 'giao_thanh_cong' THEN tong_thanh_toan ELSE 0 END), 0) AS doanh_thu,
         COUNT(*) AS so_don
       FROM don_hang
       WHERE MONTH(ngay_dat) = MONTH(CURRENT_DATE()) 
         AND YEAR(ngay_dat) = YEAR(CURRENT_DATE())`,
      { type: QueryTypes.SELECT }
    );

    // ========== 2. Doanh thu tháng trước (để tính %) ==========
    const [prevMonth] = await sequelize.query(
      `SELECT 
         COALESCE(SUM(CASE WHEN trang_thai_don = 'giao_thanh_cong' THEN tong_thanh_toan ELSE 0 END), 0) AS doanh_thu,
         COUNT(*) AS so_don
       FROM don_hang
       WHERE MONTH(ngay_dat) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
         AND YEAR(ngay_dat) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))`,
      { type: QueryTypes.SELECT }
    );

    // ========== 3. Khách hàng mới tháng này vs tháng trước ==========
    const [customersCurrentMonth] = await sequelize.query(
      `SELECT COUNT(*) AS so_luong
       FROM nguoi_dung
       WHERE vai_tro = 'khach_hang'
         AND MONTH(ngay_tao) = MONTH(CURRENT_DATE())
         AND YEAR(ngay_tao) = YEAR(CURRENT_DATE())`,
      { type: QueryTypes.SELECT }
    );

    const [customersPrevMonth] = await sequelize.query(
      `SELECT COUNT(*) AS so_luong
       FROM nguoi_dung
       WHERE vai_tro = 'khach_hang'
         AND MONTH(ngay_tao) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))
         AND YEAR(ngay_tao) = YEAR(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH))`,
      { type: QueryTypes.SELECT }
    );

    // ========== 4. Doanh thu theo tuần trong tháng ==========
    const doanhThuTheoTuan = await sequelize.query(
      `SELECT 
         CEIL(DAY(ngay_dat) / 7) AS tuan,
         COALESCE(SUM(CASE WHEN trang_thai_don = 'giao_thanh_cong' THEN tong_thanh_toan ELSE 0 END), 0) AS doanh_thu,
         COUNT(*) AS so_don
       FROM don_hang
       WHERE MONTH(ngay_dat) = MONTH(CURRENT_DATE())
         AND YEAR(ngay_dat) = YEAR(CURRENT_DATE())
       GROUP BY CEIL(DAY(ngay_dat) / 7)
       ORDER BY tuan ASC`,
      { type: QueryTypes.SELECT }
    );

    // Tạo mảng 5 tuần
    const weeklyData = [];
    for (let i = 1; i <= 5; i++) {
      const found = doanhThuTheoTuan.find(r => Number(r.tuan) === i);
      weeklyData.push({
        tuan: `Tuần ${i}`,
        doanh_thu: found ? Number(found.doanh_thu) : 0,
        so_don: found ? Number(found.so_don) : 0,
      });
    }

    // ========== 5. Doanh thu theo danh mục ==========
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

    // Tính % cho mỗi danh mục
    const totalCategoryRevenue = doanhThuTheoDanhMuc.reduce((sum, r) => sum + Number(r.tong_doanh_thu), 0);
    const categoryData = doanhThuTheoDanhMuc.map(r => ({
      ten_danh_muc: r.ten_danh_muc,
      tong_doanh_thu: Number(r.tong_doanh_thu),
      phan_tram: totalCategoryRevenue > 0
        ? Math.round((Number(r.tong_doanh_thu) / totalCategoryRevenue) * 100)
        : 0,
    }));

    // ========== Tính % tăng/giảm ==========
    const doanhThuCurrent = Number(currentMonth.doanh_thu);
    const doanhThuPrev = Number(prevMonth.doanh_thu);
    const doanhThuPercent = doanhThuPrev > 0
      ? Math.round(((doanhThuCurrent - doanhThuPrev) / doanhThuPrev) * 100)
      : (doanhThuCurrent > 0 ? 100 : 0);

    const donCurrent = Number(currentMonth.so_don);
    const donPrev = Number(prevMonth.so_don);
    const donPercent = donPrev > 0
      ? Math.round(((donCurrent - donPrev) / donPrev) * 100)
      : (donCurrent > 0 ? 100 : 0);

    const khachCurrent = Number(customersCurrentMonth.so_luong);
    const khachPrev = Number(customersPrevMonth.so_luong);
    const khachPercent = khachPrev > 0
      ? Math.round(((khachCurrent - khachPrev) / khachPrev) * 100)
      : (khachCurrent > 0 ? 100 : 0);

    // ========== Trả kết quả ==========
    res.json({
      status: 'success',
      data: {
        summary: {
          doanh_thu: doanhThuCurrent,
          doanh_thu_percent: doanhThuPercent,
          so_don: donCurrent,
          so_don_percent: donPercent,
          khach_moi: khachCurrent,
          khach_moi_percent: khachPercent,
        },
        doanh_thu_theo_tuan: weeklyData,
        doanh_thu_theo_danh_muc: categoryData,
      },
    });
  } catch (error) {
    console.error('Statistics error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi lấy dữ liệu thống kê.',
      error: error.message,
    });
  }
});

module.exports = router;
