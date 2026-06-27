const express = require('express');
const sequelize = require('../config/database');
const { QueryTypes } = require('sequelize');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');

const router = express.Router();
router.use(verifyToken, requireAdmin);

router.get('/overview', async (req, res) => {
  try {
    let { startDate, endDate } = req.query;
    if (!startDate || !endDate) {
      const today = new Date();
      endDate = today.toISOString().split('T')[0];
      const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
      startDate = firstDay.toISOString().split('T')[0];
    }

    const start = new Date(startDate);
    const end = new Date(endDate);
    const diffDays = Math.ceil(Math.abs(end - start) / (1000 * 60 * 60 * 24)) + 1;
    const prevEnd = new Date(start);
    prevEnd.setDate(prevEnd.getDate() - 1);
    const prevStart = new Date(prevEnd);
    prevStart.setDate(prevStart.getDate() - diffDays + 1);
    const toDS = (d) => d.toISOString().split('T')[0];
    const prevStartDate = toDS(prevStart);
    const prevEndDate = toDS(prevEnd);

    // 1. Summary metrics - current period
    const [cm] = await sequelize.query(
      'SELECT COALESCE(SUM(CASE WHEN trang_thai_don=\'giao_thanh_cong\' THEN tong_thanh_toan ELSE 0 END),0) AS doanh_thu, COUNT(ma_don_hang) AS so_don FROM don_hang WHERE DATE(ngay_dat)>=:startDate AND DATE(ngay_dat)<=:endDate',
      { replacements: { startDate, endDate }, type: QueryTypes.SELECT }
    );

    // Summary metrics - previous period
    const [pm] = await sequelize.query(
      'SELECT COALESCE(SUM(CASE WHEN trang_thai_don=\'giao_thanh_cong\' THEN tong_thanh_toan ELSE 0 END),0) AS doanh_thu, COUNT(ma_don_hang) AS so_don FROM don_hang WHERE DATE(ngay_dat)>=:prevStartDate AND DATE(ngay_dat)<=:prevEndDate',
      { replacements: { prevStartDate, prevEndDate }, type: QueryTypes.SELECT }
    );

    // New customers - current
    const [cc] = await sequelize.query(
      'SELECT COUNT(*) AS khach_moi FROM nguoi_dung WHERE vai_tro=\'khach_hang\' AND DATE(ngay_tao)>=:startDate AND DATE(ngay_tao)<=:endDate',
      { replacements: { startDate, endDate }, type: QueryTypes.SELECT }
    );

    // New customers - previous
    const [pc] = await sequelize.query(
      'SELECT COUNT(*) AS khach_moi FROM nguoi_dung WHERE vai_tro=\'khach_hang\' AND DATE(ngay_tao)>=:prevStartDate AND DATE(ngay_tao)<=:prevEndDate',
      { replacements: { prevStartDate, prevEndDate }, type: QueryTypes.SELECT }
    );

    // Products sold - current
    const [cs] = await sequelize.query(
      'SELECT COALESCE(SUM(ctdh.so_luong),0) AS da_ban FROM chi_tiet_don_hang ctdh JOIN don_hang dh ON ctdh.ma_don_hang=dh.ma_don_hang WHERE dh.trang_thai_don=\'giao_thanh_cong\' AND DATE(dh.ngay_dat)>=:startDate AND DATE(dh.ngay_dat)<=:endDate',
      { replacements: { startDate, endDate }, type: QueryTypes.SELECT }
    );

    // Products sold - previous
    const [ps] = await sequelize.query(
      'SELECT COALESCE(SUM(ctdh.so_luong),0) AS da_ban FROM chi_tiet_don_hang ctdh JOIN don_hang dh ON ctdh.ma_don_hang=dh.ma_don_hang WHERE dh.trang_thai_don=\'giao_thanh_cong\' AND DATE(dh.ngay_dat)>=:prevStartDate AND DATE(dh.ngay_dat)<=:prevEndDate',
      { replacements: { prevStartDate, prevEndDate }, type: QueryTypes.SELECT }
    );

    // Total orders and Cancelled orders - current
    const [co] = await sequelize.query(
      'SELECT COUNT(*) AS tong_don, COALESCE(SUM(CASE WHEN trang_thai_don IN (\'da_huy\', \'tra_hang_hoan_tien\', \'da_tra_hang\') THEN 1 ELSE 0 END),0) AS don_huy FROM don_hang WHERE DATE(ngay_dat)>=:startDate AND DATE(ngay_dat)<=:endDate',
      { replacements: { startDate, endDate }, type: QueryTypes.SELECT }
    );

    // Total orders and Cancelled orders - previous
    const [po] = await sequelize.query(
      'SELECT COUNT(*) AS tong_don, COALESCE(SUM(CASE WHEN trang_thai_don IN (\'da_huy\', \'tra_hang_hoan_tien\', \'da_tra_hang\') THEN 1 ELSE 0 END),0) AS don_huy FROM don_hang WHERE DATE(ngay_dat)>=:prevStartDate AND DATE(ngay_dat)<=:prevEndDate',
      { replacements: { prevStartDate, prevEndDate }, type: QueryTypes.SELECT }
    );

    const pct = (cur, prev) => {
      const c = Number(cur), p = Number(prev);
      return p > 0 ? Math.round(((c - p) / p) * 100) : (c > 0 ? 100 : 0);
    };

    // 2. Trend chart - group by day
    const trendRows = await sequelize.query(
      'SELECT DATE(ngay_dat) AS ngay, COALESCE(SUM(CASE WHEN trang_thai_don=\'giao_thanh_cong\' THEN tong_thanh_toan ELSE 0 END),0) AS doanh_thu, COUNT(ma_don_hang) AS so_don FROM don_hang WHERE DATE(ngay_dat)>=:startDate AND DATE(ngay_dat)<=:endDate GROUP BY DATE(ngay_dat) ORDER BY ngay ASC',
      { replacements: { startDate, endDate }, type: QueryTypes.SELECT }
    );

    // Fill missing dates with 0
    const dateMap = {};
    for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
      dateMap[toDS(new Date(d))] = { doanh_thu: 0, so_don: 0 };
    }
    trendRows.forEach(row => {
      const key = (row.ngay instanceof Date) ? toDS(row.ngay) : String(row.ngay).split('T')[0];
      if (dateMap[key]) {
        dateMap[key] = { doanh_thu: Number(row.doanh_thu), so_don: Number(row.so_don) };
      }
    });
    const trendChart = Object.keys(dateMap).sort().map(k => ({
      ngay: k, doanh_thu: dateMap[k].doanh_thu, so_don: dateMap[k].so_don
    }));

    // 3. Category distribution
    const catRows = await sequelize.query(
      'SELECT dm.ten_danh_muc, COALESCE(SUM(ctdh.thanh_tien),0) AS tong_doanh_thu FROM danh_muc dm LEFT JOIN san_pham sp ON dm.ma_danh_muc=sp.ma_danh_muc LEFT JOIN chi_tiet_don_hang ctdh ON sp.ma_san_pham=ctdh.ma_san_pham LEFT JOIN don_hang dh ON ctdh.ma_don_hang=dh.ma_don_hang AND dh.trang_thai_don=\'giao_thanh_cong\' AND DATE(dh.ngay_dat)>=:startDate AND DATE(dh.ngay_dat)<=:endDate GROUP BY dm.ma_danh_muc, dm.ten_danh_muc HAVING tong_doanh_thu>0 ORDER BY tong_doanh_thu DESC',
      { replacements: { startDate, endDate }, type: QueryTypes.SELECT }
    );
    const totalCR = catRows.reduce((s, r) => s + Number(r.tong_doanh_thu), 0);
    const categoryData = catRows.map(r => ({
      ten_danh_muc: r.ten_danh_muc,
      tong_doanh_thu: Number(r.tong_doanh_thu),
      phan_tram: totalCR > 0 ? Math.round((Number(r.tong_doanh_thu) / totalCR) * 100) : 0
    }));

    // 4. Top 5 best-selling products
    const topProducts = await sequelize.query(
      'SELECT sp.ma_san_pham, sp.ten_san_pham, sp.anh_san_pham, COALESCE(SUM(ctdh.so_luong),0) AS da_ban, COALESCE(SUM(ctdh.thanh_tien),0) AS doanh_thu FROM san_pham sp JOIN chi_tiet_don_hang ctdh ON sp.ma_san_pham=ctdh.ma_san_pham JOIN don_hang dh ON ctdh.ma_don_hang=dh.ma_don_hang WHERE dh.trang_thai_don=\'giao_thanh_cong\' AND DATE(dh.ngay_dat)>=:startDate AND DATE(dh.ngay_dat)<=:endDate GROUP BY sp.ma_san_pham, sp.ten_san_pham, sp.anh_san_pham ORDER BY doanh_thu DESC LIMIT 5',
      { replacements: { startDate, endDate }, type: QueryTypes.SELECT }
    );

    // Response
    res.json({
      status: 'success',
      data: {
        summary: {
          doanh_thu: Number(cm.doanh_thu),
          doanh_thu_percent: pct(cm.doanh_thu, pm.doanh_thu),
          so_don: Number(cm.so_don),
          so_don_percent: pct(cm.so_don, pm.so_don),
          khach_moi: Number(cc.khach_moi),
          khach_moi_percent: pct(cc.khach_moi, pc.khach_moi),
          da_ban: Number(cs.da_ban),
          da_ban_percent: pct(cs.da_ban, ps.da_ban),
          aov: Number(cm.so_don) > 0 ? Math.round(Number(cm.doanh_thu) / Number(cm.so_don)) : 0,
          aov_percent: pct(
             Number(cm.so_don) > 0 ? Number(cm.doanh_thu)/Number(cm.so_don) : 0, 
             Number(pm.so_don) > 0 ? Number(pm.doanh_thu)/Number(pm.so_don) : 0
          ),
          ty_le_huy: Number(co.tong_don) > 0 ? parseFloat((Number(co.don_huy) / Number(co.tong_don) * 100).toFixed(1)) : 0,
          ty_le_huy_percent: pct(
             Number(co.tong_don) > 0 ? Number(co.don_huy)/Number(co.tong_don) : 0,
             Number(po.tong_don) > 0 ? Number(po.don_huy)/Number(po.tong_don) : 0
          ),
        },
        trend_chart: trendChart,
        doanh_thu_theo_danh_muc: categoryData,
        top_products: topProducts,
      },
    });
  } catch (error) {
    console.error('Statistics error:', error);
    res.status(500).json({ status: 'error', message: 'Error loading statistics.' });
  }
});

module.exports = router;
