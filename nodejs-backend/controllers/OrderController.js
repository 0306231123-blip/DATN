const DonHang = require('../models/DonHang');
const NguoiDung = require('../models/NguoiDung');
const ChiTietDonHang = require('../models/ChiTietDonHang');
const SanPham = require('../models/SanPham');
const { Op } = require('sequelize');
const sequelize = require('../config/database');
const { QueryTypes } = require('sequelize');

/**
 * GET /api/orders
 * Lấy danh sách đơn hàng với search, filter theo trạng thái, pagination
 */
exports.getAllOrders = async (req, res) => {
  try {
    const {
      search,
      trang_thai,
      per_page = 15,
      page = 1,
      sort_by = 'ngay_dat',
      sort_order = 'DESC',
    } = req.query;

    const pageNum = Math.max(1, parseInt(page) || 1);
    const perPageNum = Math.max(1, Math.min(100, parseInt(per_page) || 15));
    const offset = (pageNum - 1) * perPageNum;

    let where = {};

    // Search by order ID or recipient name
    if (search && typeof search === 'string' && search.trim()) {
      where[Op.or] = [
        sequelize.where(sequelize.cast(sequelize.col('don_hang.ma_don_hang'), 'CHAR'), { [Op.like]: `%${search}%` }),
        { ho_ten_nguoi_nhan: { [Op.like]: `%${search}%` } },
      ];
    }

    // Filter by status
    const validStatuses = ['cho_xac_nhan', 'da_xac_nhan', 'dang_giao', 'giao_thanh_cong', 'da_huy'];
    if (trang_thai && trang_thai !== 'all' && validStatuses.includes(trang_thai)) {
      where.trang_thai_don = trang_thai;
    }

    // Validate sort
    const allowedSortFields = ['ngay_dat', 'tong_thanh_toan', 'ma_don_hang'];
    const sortField = allowedSortFields.includes(sort_by) ? sort_by : 'ngay_dat';
    const sortDir = sort_order.toUpperCase() === 'ASC' ? 'ASC' : 'DESC';

    const { count, rows } = await DonHang.findAndCountAll({
      where,
      limit: perPageNum,
      offset,
      order: [[sortField, sortDir]],
      include: [
        {
          model: NguoiDung,
          as: 'nguoi_dung',
          attributes: ['ma_nguoi_dung', 'ho_ten', 'email', 'so_dien_thoai'],
          required: false,
        },
        {
          model: ChiTietDonHang,
          as: 'chi_tiet',
          attributes: ['ma_chi_tiet', 'ten_san_pham', 'don_gia', 'so_luong', 'thanh_tien'],
          required: false,
        },
      ],
    });

    // Add so_san_pham (product count) to each order
    const ordersData = rows.map(order => {
      const orderJSON = order.toJSON();
      orderJSON.so_san_pham = orderJSON.chi_tiet ? orderJSON.chi_tiet.length : 0;
      return orderJSON;
    });

    res.status(200).json({
      status: 'success',
      data: ordersData,
      pagination: {
        total: count,
        per_page: perPageNum,
        current_page: pageNum,
        last_page: Math.ceil(count / perPageNum),
      },
    });
  } catch (error) {
    console.error('Get orders error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi lấy danh sách đơn hàng.',
      error: error.message,
    });
  }
};

/**
 * GET /api/orders/:id
 * Lấy chi tiết một đơn hàng
 */
exports.getOrderById = async (req, res) => {
  try {
    const { id } = req.params;

    const orderId = parseInt(id);
    if (isNaN(orderId) || orderId <= 0) {
      return res.status(400).json({
        status: 'error',
        message: 'ID đơn hàng không hợp lệ.',
      });
    }

    const order = await DonHang.findByPk(orderId, {
      include: [
        {
          model: NguoiDung,
          as: 'nguoi_dung',
          attributes: ['ma_nguoi_dung', 'ho_ten', 'email', 'so_dien_thoai', 'dia_chi'],
          required: false,
        },
        {
          model: ChiTietDonHang,
          as: 'chi_tiet',
          include: [{
            model: SanPham,
            as: 'san_pham',
            attributes: ['ma_san_pham', 'ten_san_pham', 'gia', 'gia_khuyen_mai', 'thuong_hieu'],
            required: false,
          }],
        },
      ],
    });

    if (!order) {
      return res.status(404).json({
        status: 'error',
        message: 'Đơn hàng không tồn tại.',
      });
    }

    res.status(200).json({
      status: 'success',
      data: order,
    });
  } catch (error) {
    console.error('Get order error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi lấy thông tin đơn hàng.',
      error: error.message,
    });
  }
};

/**
 * PUT /api/orders/:id/status
 * Cập nhật trạng thái đơn hàng
 */
exports.updateOrderStatus = async (req, res) => {
  try {
    const { id } = req.params;
    const { trang_thai_don } = req.body;

    const orderId = parseInt(id);
    if (isNaN(orderId) || orderId <= 0) {
      return res.status(400).json({
        status: 'error',
        message: 'ID đơn hàng không hợp lệ.',
      });
    }

    const validStatuses = ['cho_xac_nhan', 'da_xac_nhan', 'dang_giao', 'giao_thanh_cong', 'da_huy'];
    if (!trang_thai_don || !validStatuses.includes(trang_thai_don)) {
      return res.status(400).json({
        status: 'error',
        message: 'Trạng thái không hợp lệ.',
      });
    }

    const order = await DonHang.findByPk(orderId);
    if (!order) {
      return res.status(404).json({
        status: 'error',
        message: 'Đơn hàng không tồn tại.',
      });
    }

    // Validate state transitions
    const currentStatus = order.trang_thai_don;
    const allowedTransitions = {
      'cho_xac_nhan': ['da_xac_nhan', 'da_huy'],
      'da_xac_nhan': ['dang_giao', 'da_huy'],
      'dang_giao': ['giao_thanh_cong', 'da_huy'],
      'giao_thanh_cong': [],
      'da_huy': [],
    };

    if (!allowedTransitions[currentStatus]?.includes(trang_thai_don)) {
      return res.status(400).json({
        status: 'error',
        message: `Không thể chuyển từ "${currentStatus}" sang "${trang_thai_don}".`,
      });
    }

    await order.update({
      trang_thai_don,
      ngay_cap_nhat: new Date(),
    });

    res.status(200).json({
      status: 'success',
      message: 'Cập nhật trạng thái đơn hàng thành công.',
      data: order,
    });
  } catch (error) {
    console.error('Update order status error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi cập nhật trạng thái đơn hàng.',
      error: error.message,
    });
  }
};

/**
 * GET /api/orders/stats
 * Thống kê đơn hàng
 */
exports.getOrderStats = async (req, res) => {
  try {
    const total = await DonHang.count();

    const choXacNhan = await DonHang.count({ where: { trang_thai_don: 'cho_xac_nhan' } });
    const daXacNhan = await DonHang.count({ where: { trang_thai_don: 'da_xac_nhan' } });
    const dangGiao = await DonHang.count({ where: { trang_thai_don: 'dang_giao' } });
    const giaoThanhCong = await DonHang.count({ where: { trang_thai_don: 'giao_thanh_cong' } });
    const daHuy = await DonHang.count({ where: { trang_thai_don: 'da_huy' } });

    // Total revenue (completed orders only)
    const [revenueResult] = await sequelize.query(
      `SELECT COALESCE(SUM(tong_thanh_toan), 0) AS tong_doanh_thu 
       FROM don_hang 
       WHERE trang_thai_don = 'giao_thanh_cong'`,
      { type: QueryTypes.SELECT }
    );

    // Current month stats
    const [monthResult] = await sequelize.query(
      `SELECT 
         COUNT(*) AS don_thang_nay,
         COALESCE(SUM(CASE WHEN trang_thai_don = 'giao_thanh_cong' THEN tong_thanh_toan ELSE 0 END), 0) AS doanh_thu_thang
       FROM don_hang 
       WHERE MONTH(ngay_dat) = MONTH(CURRENT_DATE()) 
         AND YEAR(ngay_dat) = YEAR(CURRENT_DATE())`,
      { type: QueryTypes.SELECT }
    );

    res.status(200).json({
      status: 'success',
      data: {
        total,
        cho_xac_nhan: choXacNhan,
        da_xac_nhan: daXacNhan,
        dang_giao: dangGiao,
        giao_thanh_cong: giaoThanhCong,
        da_huy: daHuy,
        tong_doanh_thu: Number(revenueResult.tong_doanh_thu),
        don_thang_nay: Number(monthResult.don_thang_nay),
        doanh_thu_thang: Number(monthResult.doanh_thu_thang),
      },
    });
  } catch (error) {
    console.error('Get order stats error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi lấy thống kê đơn hàng.',
      error: error.message,
    });
  }
};
