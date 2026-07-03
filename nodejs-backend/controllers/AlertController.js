const { Op } = require('sequelize');
const SanPham = require('../models/SanPham');
const DonHang = require('../models/DonHang');
const TinNhan = require('../models/TinNhan');
const CanhBaoHeThong = require('../models/CanhBaoHeThong');

class AlertController {
  // GET /api/alerts
  async getAlerts(req, res) {
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

      // Count unread system alerts
      const unreadSystemAlertsCount = await CanhBaoHeThong.count({
        where: {
          da_doc: false
        }
      });

      res.json({
        status: 'success',
        data: {
          low_stock_count: lowStockCount,
          return_requests_count: returnRequestsCount,
          unread_messages_count: unreadMessagesCount,
          unread_system_alerts_count: unreadSystemAlertsCount
        }
      });
    } catch (error) {
      console.error('Error fetching alerts:', error);
      res.status(500).json({ status: 'error', message: 'Không thể tải thông báo cảnh báo.' });
    }
  }

  // GET /api/alerts/system
  async getSystemAlerts(req, res) {
    try {
      const alerts = await CanhBaoHeThong.findAll({
        order: [['ngay_tao', 'DESC']],
        limit: 100
      });
      res.json({ status: 'success', data: alerts });
    } catch (error) {
      res.status(500).json({ status: 'error', message: error.message });
    }
  }

  // PUT /api/alerts/system/:id/read
  async markSystemAlertAsRead(req, res) {
    try {
      const alert = await CanhBaoHeThong.findByPk(req.params.id);
      if (alert) {
        alert.da_doc = true;
        await alert.save();
      }
      res.json({ status: 'success' });
    } catch (error) {
      res.status(500).json({ status: 'error', message: error.message });
    }
  }
}

module.exports = new AlertController();
