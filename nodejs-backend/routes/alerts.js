const express = require('express');
const { Op } = require('sequelize');
const SanPham = require('../models/SanPham');
const YeuCauTraHang = require('../models/YeuCauTraHang');
const DonHang = require('../models/DonHang');
const TinNhan = require('../models/TinNhan');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');

const router = express.Router();

router.use(verifyToken, requireAdmin);

router.get('/', async (req, res) => {
  try {
    const lowStockThreshold = parseInt(req.query.lowStockThreshold) || 20;

    // Count low stock products
    const lowStockCount = await SanPham.count({
      where: {
        so_luong_ton: { [Op.lte]: lowStockThreshold }
      }
    });

    // Count pending return requests based on DonHang status
    const returnRequestsCount = await DonHang.count({
      where: {
        trang_thai_don: 'dang_tra_hang'
      }
    });

    // Count unread messages from customers
    const unreadMessagesCount = await TinNhan.count({
      where: {
        is_from_admin: false,
        da_doc: false
      }
    });

    res.json({
      status: 'success',
      data: {
        low_stock_count: lowStockCount,
        return_requests_count: returnRequestsCount,
        unread_messages_count: unreadMessagesCount
      }
    });
  } catch (error) {
    console.error('Error fetching alerts:', error);
    res.status(500).json({ status: 'error', message: 'Không thể tải thông báo cảnh báo.' });
  }
});

module.exports = router;
