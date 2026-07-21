const DonHang = require('../models/DonHang');
const NguoiDung = require('../models/NguoiDung');
const ChiTietDonHang = require('../models/ChiTietDonHang');
const SanPham = require('../models/SanPham');
const YeuCauTraHang = require('../models/YeuCauTraHang');
const CanhBaoHeThong = require('../models/CanhBaoHeThong');
const GioHang = require('../models/GioHang');
const KhuyenMai = require('../models/KhuyenMai');
const jwt = require('jsonwebtoken');
const { Op } = require('sequelize');
const sequelize = require('../config/database');
const { QueryTypes } = require('sequelize');
const { logActivity } = require('../utils/logActivity');

const STATUS_LABEL = {
  cho_xac_nhan: 'Chờ xác nhận',
  da_xac_nhan: 'Đã xác nhận',
  dang_giao: 'Đang giao',
  giao_thanh_cong: 'Giao thành công',
  da_huy: 'Đã hủy',
  dang_tra_hang: 'Đang trả hàng',
  da_tra_hang: 'Đã trả hàng',
  tra_hang_hoan_tien: 'Trả hàng hoàn tiền',
  tu_choi_tra_hang: 'Từ chối trả hàng',
  hoan_thanh: 'Hoàn thành',
};

class OrderController {
  // ==========================================
  // ADMIN API
  // ==========================================

  /**
   * GET /api/orders
   * Lấy danh sách đơn hàng với search, filter theo trạng thái, pagination
   */
  async getAllOrders(req, res) {
    try {
      const {
        search,
        trang_thai,
        per_page = 15,
        page = 1,
        sort_by = 'ngay_dat',
        sort_order = 'DESC',
        start_date,
        end_date,
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
      const validStatuses = ['cho_xac_nhan', 'da_xac_nhan', 'dang_giao', 'giao_thanh_cong', 'da_huy', 'dang_tra_hang', 'da_tra_hang', 'tra_hang_hoan_tien', 'hoan_thanh', 'tu_choi_tra_hang', 'khong_du_dieu_kien'];
      if (trang_thai && trang_thai !== 'all' && validStatuses.includes(trang_thai)) {
        where.trang_thai_don = trang_thai;
      }

      // Filter by date
      if (start_date || end_date) {
        where.ngay_dat = {};
        if (start_date) where.ngay_dat[Op.gte] = new Date(start_date + 'T00:00:00.000Z');
        if (end_date) where.ngay_dat[Op.lte] = new Date(end_date + 'T23:59:59.999Z');
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
          {
            model: YeuCauTraHang,
            as: 'yeu_cau_tra_hang',
            required: false,
          }
        ],
      });

      // Auto-complete orders that have been delivered for more than 3 days
      const now = new Date();
      for (let order of rows) {
          if (order.trang_thai_don === 'giao_thanh_cong') {
              const ngayGiao = new Date(order.ngay_cap_nhat || order.ngay_dat);
              const diffTime = Math.abs(now - ngayGiao);
              const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
              
              if (diffDays > 3) {
                  order.trang_thai_don = 'hoan_thanh';
                  await order.save();
              }
          }
      }

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
  }

  /**
   * GET /api/orders/:id
   * Lấy chi tiết một đơn hàng
   */
  async getOrderById(req, res) {
    try {
      const { id } = req.params;

      const orderId = id;
      if (!orderId) {
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
            attributes: ['ma_nguoi_dung', 'ho_ten', 'email', 'so_dien_thoai', 'dia_chi','ngan_hang', 'so_tai_khoan', 'chu_tai_khoan'],
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
          {
            model: YeuCauTraHang,
            as: 'yeu_cau_tra_hang',
            required: false,
          }
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
  }

  /**
   * PUT /api/orders/:id/status
   * Cập nhật trạng thái đơn hàng (Có tích hợp cộng lại kho)
   */
  async updateOrderStatus(req, res) {
    try {
      const { id } = req.params;
      const { trang_thai_don, ly_do_tu_choi_tra } = req.body;

      const orderId = id;
      if (!orderId) {
        return res.status(400).json({
          status: 'error',
          message: 'ID đơn hàng không hợp lệ.',
        });
      }

      const validStatuses = ['cho_xac_nhan', 'da_xac_nhan', 'dang_giao', 'giao_thanh_cong', 'da_huy', 'dang_tra_hang', 'da_tra_hang', 'tra_hang_hoan_tien', 'hoan_thanh', 'tu_choi_tra_hang', 'khong_du_dieu_kien'];
      if (!trang_thai_don || !validStatuses.includes(trang_thai_don)) {
        return res.status(400).json({
          status: 'error',
          message: 'Trạng thái không hợp lệ.',
        });
      }

      const order = await DonHang.findByPk(orderId, {
          include: [{
              model: ChiTietDonHang,
              as: 'chi_tiet'
          }]
      });

      if (!order) {
        return res.status(404).json({
          status: 'error',
          message: 'Đơn hàng không tồn tại.',
        });
      }

      const currentStatus = order.trang_thai_don;

      const newIsCancelOrReturn = (trang_thai_don === 'da_huy' || trang_thai_don === 'da_tra_hang' || trang_thai_don === 'tra_hang_hoan_tien');
      const oldIsCancelOrReturn = (currentStatus === 'da_huy' || currentStatus === 'da_tra_hang' || currentStatus === 'tra_hang_hoan_tien');

      // 1. Hoàn lại Voucher nếu mới chuyển sang trạng thái hủy/trả hàng (lần đầu)
      if (newIsCancelOrReturn && !oldIsCancelOrReturn) {
          if (order.ma_khuyen_mai) {
              const KhuyenMai = require('../models/KhuyenMai');
              await KhuyenMai.increment('so_luong', { by: 1, where: { ma_khuyen_mai: order.ma_khuyen_mai } });
          }
      }

      // 2. Trả lại kho sản phẩm CHỈ khi là HỦY ĐƠN hoặc TRẢ HÀNG HOÀN TIỀN
      const newNeedsRestock = (trang_thai_don === 'da_huy' || trang_thai_don === 'tra_hang_hoan_tien');
      const oldAlreadyRestocked = (currentStatus === 'da_huy' || currentStatus === 'tra_hang_hoan_tien');

      if (newNeedsRestock && !oldAlreadyRestocked) {
          for (let item of order.chi_tiet) {
              await SanPham.increment('so_luong_ton', {
                  by: item.so_luong,
                  where: { ma_san_pham: item.ma_san_pham }
              });
          }
      }

      const updateData = {
          trang_thai_don,
          ngay_cap_nhat: new Date()
      };

      if (trang_thai_don === 'tu_choi_tra_hang') {
          updateData.ly_do_tu_choi_tra = ly_do_tu_choi_tra;
      }

      await order.update(updateData);

      if (trang_thai_don === 'dang_tra_hang') {
        const existingReq = await YeuCauTraHang.findOne({ where: { ma_don_hang: orderId } });
        if (!existingReq) {
          await YeuCauTraHang.create({
            ma_don_hang: orderId,
            ly_do: 'Admin đổi trạng thái bằng tay',
            trang_thai: 'cho_duyet',
            ngay_yeu_cau: new Date()
          });
        }
      }

      res.status(200).json({
        status: 'success',
        message: 'Cập nhật trạng thái đơn hàng thành công.',
        data: order,
      });
      logActivity(
        'CẬP NHẬT TRẠNG THÁI',
        'don_hang',
        `Đơn #${orderId}: ${STATUS_LABEL[currentStatus] || currentStatus} → ${STATUS_LABEL[trang_thai_don] || trang_thai_don}`
      );
    } catch (error) {
      console.error('Update order status error:', error);
      res.status(500).json({
        status: 'error',
        message: 'Lỗi khi cập nhật trạng thái đơn hàng.',
        error: error.message,
      });
    }
  }

  /**
   * GET /api/orders/stats
   * Thống kê đơn hàng
   */
  async getOrderStats(req, res) {
    try {
      const total = await DonHang.count();

      const choXacNhan = await DonHang.count({ where: { trang_thai_don: 'cho_xac_nhan' } });
      const daXacNhan = await DonHang.count({ where: { trang_thai_don: 'da_xac_nhan' } });
      const dangGiao = await DonHang.count({ where: { trang_thai_don: 'dang_giao' } });
      const giaoThanhCong = await DonHang.count({ where: { trang_thai_don: 'giao_thanh_cong' } });
      const hoanThanh = await DonHang.count({ where: { trang_thai_don: 'hoan_thanh' } });
      const daHuy = await DonHang.count({ where: { trang_thai_don: 'da_huy' } });
      const dangTraHang = await DonHang.count({ where: { trang_thai_don: 'dang_tra_hang' } });
      const daTraHang = await DonHang.count({ where: { trang_thai_don: 'da_tra_hang' } });
      const tuChoiTraHang = await DonHang.count({ where: { trang_thai_don: 'tu_choi_tra_hang' } });
      const khongDuDieuKien = await DonHang.count({ where: { trang_thai_don: 'khong_du_dieu_kien' } });


      const [revenueResult] = await sequelize.query(
        `SELECT COALESCE(SUM(tong_thanh_toan), 0) AS tong_doanh_thu 
         FROM don_hang 
         WHERE trang_thai_don = 'giao_thanh_cong' OR trang_thai_don = 'hoan_thanh'`,
        { type: QueryTypes.SELECT }
      );

      const [monthResult] = await sequelize.query(
        `SELECT 
           COUNT(*) AS don_thang_nay,
           COALESCE(SUM(CASE WHEN trang_thai_don = 'giao_thanh_cong' OR trang_thai_don = 'hoan_thanh' THEN tong_thanh_toan ELSE 0 END), 0) AS doanh_thu_thang
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
          hoan_thanh: hoanThanh,
          da_huy: daHuy,
          dang_tra_hang: dangTraHang,
          da_tra_hang: daTraHang,
          tu_choi_tra_hang: tuChoiTraHang,
          khong_du_dieu_kien: khongDuDieuKien,
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
  }

  // ==========================================
  // CLIENT API
  // ==========================================

  // --- 1. TẠO ĐƠN HÀNG (TRỪ KHO VÀ TRỪ VOUCHER) ---
  async createOrder(req, res) {
    const t = await sequelize.transaction();
    try {
        let maNguoiDung = req.user.ma_nguoi_dung !== undefined ? req.user.ma_nguoi_dung : req.user.id;
        
        // 1. Lấy dữ liệu từ Frontend gửi lên
        const { dia_chi, so_dien_thoai, ma_khuyen_mai, so_tien_giam, phi_van_chuyen, phuong_thuc_thanh_toan } = req.body; 
        
        if (!dia_chi || dia_chi.trim() === '' || dia_chi.trim().startsWith(',')) {
            await t.rollback();
            return res.status(400).json({ success: false, message: 'Bắt buộc phải nhập đầy đủ số nhà và tên đường!' });
        }

        const user = await NguoiDung.findByPk(maNguoiDung);
        
        if (!user) {
            await t.rollback();
            return res.status(404).json({ success: false, message: 'Người dùng không tồn tại' });
        }

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
            if (!item.san_pham) throw new Error(`Một sản phẩm trong giỏ hàng (Mã SP: ${item.ma_san_pham}) không còn tồn tại trên hệ thống!`);
            if (item.san_pham.so_luong_ton < item.so_luong) throw new Error(`Sản phẩm ${item.san_pham.ten_san_pham} không đủ hàng!`);
            tong_tien += (item.san_pham.gia_khuyen_mai || item.san_pham.gia) * item.so_luong;
        }

        // 3. Xử lý tính toán VOUCHER VÀ SHIP
        const tienGiam = so_tien_giam || 0;
        const tienShip = phi_van_chuyen || 0;
        let tongThanhToan = tong_tien + tienShip - tienGiam;
        if (tongThanhToan < 0) tongThanhToan = 0; // Chống lỗi âm tiền

        let mappedPaymentMethod = 'chuyen_khoan';
        if (phuong_thuc_thanh_toan === 'cod') mappedPaymentMethod = 'tien_mat';
        else if (phuong_thuc_thanh_toan === 'momo') mappedPaymentMethod = 'vi_dien_tu';
        else if (phuong_thuc_thanh_toan === 'banking') mappedPaymentMethod = 'chuyen_khoan';

        // TẠO MÃ ĐƠN HÀNG CUSTOM (VD: 0707#01)
        const todayStart = new Date();
        todayStart.setHours(0, 0, 0, 0);
        const todayEnd = new Date();
        todayEnd.setHours(23, 59, 59, 999);
        
        const countOrdersToday = await DonHang.count({
            where: {
                ngay_dat: {
                    [Op.between]: [todayStart, todayEnd]
                }
            },
            transaction: t
        });
        
        const day = String(todayStart.getDate()).padStart(2, '0');
        const month = String(todayStart.getMonth() + 1).padStart(2, '0');
        const year = String(todayStart.getFullYear()).slice(-2);
        const sequence = String(countOrdersToday + 1).padStart(2, '0');
        const customOrderCode = `${day}${month}${year}#${sequence}`;

        // 4. KIỂM TRA VÀ TRỪ LƯỢT VOUCHER TRƯỚC KHI TẠO ĐƠN
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

        // 5. Lệnh tạo đơn hàng
        const donHangMoi = await DonHang.create({
            ma_don_hang: customOrderCode,
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
            phuong_thuc_thanh_toan: mappedPaymentMethod, 
            trang_thai_thanh_toan: phuong_thuc_thanh_toan === 'cod' ? 'chua_thanh_toan' : 'da_thanh_toan'
        }, { transaction: t });

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

        // 7. Xóa các sản phẩm đã đặt khỏi giỏ hàng
        let destroyWhere = { ma_nguoi_dung: maNguoiDung };
        if (req.body.selected_items && Array.isArray(req.body.selected_items) && req.body.selected_items.length > 0) {
            destroyWhere.ma_san_pham = { [Op.in]: req.body.selected_items };
        }
        await GioHang.destroy({ where: destroyWhere, transaction: t });

        // --- SPAM CHECKS (NO BLOCK, JUST ALERT) ---
        // 1. Check for High Value or Large Quantity
        let tongSoLuong = 0;
        items.forEach(item => tongSoLuong += item.so_luong);
        
        if (tongThanhToan > 10000000 || tongSoLuong > 50) {
            await CanhBaoHeThong.create({
                ma_nguoi_dung: maNguoiDung,
                loai_canh_bao: 'MUA_SO_LUONG_LON',
                noi_dung: `Người dùng ${maNguoiDung} (${user.ho_ten}) đặt đơn hàng ${donHangMoi.ma_don_hang} có giá trị ${tongThanhToan.toLocaleString()}đ hoặc số lượng ${tongSoLuong} sản phẩm.`
            }, { transaction: t });
        }

        // 2. Check for High Frequency (e.g. > 5 orders in 1 hour)
        const now = new Date();
        const oneHourAgo = new Date(now.getTime() - 60 * 60 * 1000);
        const recentOrdersCount = await DonHang.count({
            where: {
                ma_nguoi_dung: maNguoiDung,
                ngay_dat: { [Op.gte]: oneHourAgo }
            },
            transaction: t
        });

        if (recentOrdersCount >= 5) {
            await CanhBaoHeThong.create({
                ma_nguoi_dung: maNguoiDung,
                loai_canh_bao: 'SPAM_DON_HANG',
                noi_dung: `Người dùng ${maNguoiDung} (${user.ho_ten}) đã đặt ${recentOrdersCount + 1} đơn hàng trong 1 giờ qua.`
            }, { transaction: t });
        }
        // -----------------------------------------

        await t.commit();
        
        res.json({ success: true, message: 'Đặt hàng thành công!', ma_don_hang: donHangMoi.ma_don_hang });
    } catch (error) {
        await t.rollback();
        console.error("Lỗi tạo đơn:", error);
        res.status(500).json({ success: false, message: error.message });
    }
  }

  // --- 2. CẬP NHẬT TRẠNG THÁI (HỦY ĐƠN / TRẢ HÀNG / HOÀN THÀNH) ---
  async updateUserOrderStatus(req, res) {
    const t = await sequelize.transaction();
    try {
        let maNguoiDung = req.user.ma_nguoi_dung !== undefined ? req.user.ma_nguoi_dung : req.user.id;

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
        
        const donHang = await DonHang.findOne({
            where: { ma_don_hang, ma_nguoi_dung: maNguoiDung },
            include: [{ model: ChiTietDonHang, as: 'chi_tiet' }],
            transaction: t
        });

        if (!donHang) {
            await t.rollback();
            return res.status(404).json({ success: false, message: 'Không tìm thấy đơn hàng' });
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
            
            // SPAM LOGIC: Alert admin if user cancelled many orders
            const soDonHuyTruocDo = await DonHang.count({
                where: {
                    ma_nguoi_dung: donHang.ma_nguoi_dung, 
                    trang_thai_don: 'da_huy'
                },
                transaction: t
            });
            
            if (soDonHuyTruocDo >= 10) {
                const user = await NguoiDung.findByPk(donHang.ma_nguoi_dung, { transaction: t });
                await CanhBaoHeThong.create({
                    ma_nguoi_dung: donHang.ma_nguoi_dung,
                    loai_canh_bao: 'HUY_DON_NHIEU',
                    noi_dung: `Người dùng ${donHang.ma_nguoi_dung} (${user ? user.ho_ten : 'Unknown'}) đã hủy quá 10 đơn hàng.`
                }, { transaction: t });
                // We REMOVED the block logic according to user's instruction
            }
            
            donHang.ly_do_huy_don = ly_do_huy_don;
            
            if (ngan_hang_hoan_tien && stk_hoan_tien && chu_tk_hoan_tien) {
                donHang.ngan_hang_hoan_tien = ngan_hang_hoan_tien;
                donHang.stk_hoan_tien = stk_hoan_tien;
                donHang.chu_tk_hoan_tien = chu_tk_hoan_tien;
            }
            
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

            if (!ngan_hang_hoan_tien || !stk_hoan_tien || !chu_tk_hoan_tien || !ly_do_tra_hang) {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Vui lòng cung cấp đầy đủ thông tin ngân hàng và lý do trả hàng!' });
            }

            
            // KIỂM TRA ĐIỀU KIỆN 3 NGÀY
            const ngayGiao = new Date(donHang.ngay_cap_nhat || donHang.ngay_dat);
            const diffTime = Math.abs(new Date() - ngayGiao);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
            if (diffDays > 3) {
                await t.rollback();
                return res.status(400).json({ success: false, message: 'Đã quá 3 ngày kể từ khi nhận hàng, bạn không thể yêu cầu hoàn trả nữa!' });
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
        
        donHang.trang_thai_don = trang_thai;
        await donHang.save({ transaction: t });

        await t.commit();
        res.json({ success: true, message: 'Cập nhật trạng thái thành công' });
    } catch (error) {
        await t.rollback();
        console.log(error);
        res.status(500).json({ success: false, message: error.message });
    }
  }

  // --- 3. LẤY LỊCH SỬ ĐƠN HÀNG (AUTO-COMPLETE 7 NGÀY) ---
  async getMyOrders(req, res) {
    try {
        let maNguoiDung = req.user.ma_nguoi_dung !== undefined ? req.user.ma_nguoi_dung : req.user.id;

        const orders = await DonHang.findAll({
            where: { ma_nguoi_dung: maNguoiDung },
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
                
                if (diffDays > 3) {
                    order.trang_thai_don = 'hoan_thanh';
                    await order.save();
                }
            }
        }

        res.json({ success: true, data: orders });
    } catch (error) {
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
  }

  // Yêu cầu hoàn tiền (dành cho API rời nếu frontend gọi)
  async requestRefund(req, res) {
    try {
        const { ma_don_hang, ly_do, ngan_hang, so_tai_khoan, chu_tai_khoan } = req.body;

        if (!ngan_hang || !so_tai_khoan || !chu_tai_khoan) {
            return res.status(400).json({ success: false, message: 'Vui lòng nhập đầy đủ thông tin nhận tiền hoàn!' });
        }

        await YeuCauTraHang.create({
            ma_don_hang: ma_don_hang,
            ma_nguoi_dung: req.user.ma_nguoi_dung !== undefined ? req.user.ma_nguoi_dung : req.user.id,
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
  }

  // --- HARD DELETE ĐƠN HÀNG KHI HỦY THANH TOÁN QR ---
  async deleteUnpaid(req, res) {
    const t = await sequelize.transaction();
    try {
        let maNguoiDung = req.user.ma_nguoi_dung !== undefined ? req.user.ma_nguoi_dung : req.user.id;
        const { ma_don_hang } = req.body;
        
        const donHang = await DonHang.findOne({
            where: { ma_don_hang, ma_nguoi_dung: maNguoiDung },
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
  }
}

module.exports = new OrderController();
