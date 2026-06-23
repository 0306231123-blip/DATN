// ==========================================
// THƯ VIỆN THÊM VÀO CHO AI (Thêm mới)
// ==========================================
const axios = require('axios');
const jwt = require('jsonwebtoken');
const AnhSanPham = require('../models/AnhSanPham');
const NguoiDung = require('../models/NguoiDung');
// ==========================================

const SanPham = require('../models/SanPham');
const DanhMuc = require('../models/DanhMuc');
const { Op } = require('sequelize');
const sequelize = require('../config/database');
const { QueryTypes } = require('sequelize');

/**
 * GET /api/products
 * Lấy danh sách sản phẩm với search, filter, pagination
 */
exports.getAllProducts = async (req, res) => {
  try {
    const {
      search,
      ma_danh_muc,
      trang_thai,
      thuong_hieu,
      gia_min,
      gia_max,
      sort_by = 'ngay_tao',
      sort_order = 'DESC',
      per_page = 15,
      page = 1,
    } = req.query;

    // Validate pagination
    const pageNum = Math.max(1, parseInt(page) || 1);
    const perPageNum = Math.max(1, Math.min(100, parseInt(per_page) || 15));
    const offset = (pageNum - 1) * perPageNum;

    let where = {};

    // Search by product name or brand
    if (search && typeof search === 'string' && search.trim()) {
      where[Op.or] = [
        { ten_san_pham: { [Op.like]: `%${search}%` } },
        { thuong_hieu: { [Op.like]: `%${search}%` } },
      ];
    }

    // Filter by category
    if (ma_danh_muc && ma_danh_muc !== 'all') {
      where.ma_danh_muc = parseInt(ma_danh_muc);
    }

    // Filter by status
    if (trang_thai && trang_thai !== 'all' && ['dang_ban', 'ngung_ban', 'het_hang'].includes(trang_thai)) {
      where.trang_thai = trang_thai;
    }

    // Filter by brand
    if (thuong_hieu && thuong_hieu !== 'all') {
      where.thuong_hieu = thuong_hieu;
    }

    // Filter by price range
    if (gia_min || gia_max) {
      where.gia = {};
      if (gia_min) where.gia[Op.gte] = parseFloat(gia_min);
      if (gia_max) where.gia[Op.lte] = parseFloat(gia_max);
    }

    // Validate sort
    const allowedSortFields = ['ngay_tao', 'ten_san_pham', 'gia', 'so_luong_ton', 'diem_danh_gia'];
    const sortField = allowedSortFields.includes(sort_by) ? sort_by : 'ngay_tao';
    const sortDir = sort_order.toUpperCase() === 'ASC' ? 'ASC' : 'DESC';

    const { count, rows } = await SanPham.findAndCountAll({
      where,
      limit: perPageNum,
      offset,
      order: [[sortField, sortDir]],
      include: [{
        model: DanhMuc,
        as: 'danh_muc',
        attributes: ['ma_danh_muc', 'ten_danh_muc'],
        required: false,
      }],
    });

    res.status(200).json({
      status: 'success',
      data: rows,
      pagination: {
        total: count,
        per_page: perPageNum,
        current_page: pageNum,
        last_page: Math.ceil(count / perPageNum),
      },
    });
  } catch (error) {
    console.error('Get products error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi lấy danh sách sản phẩm.',
      error: error.message,
    });
  }
};

/**
 * GET /api/products/:id
 * Lấy chi tiết một sản phẩm
 */
exports.getProductById = async (req, res) => {
  try {
    const { id } = req.params;

    const productId = parseInt(id);
    if (isNaN(productId) || productId <= 0) {
      return res.status(400).json({
        status: 'error',
        message: 'ID sản phẩm không hợp lệ.',
      });
    }

    const product = await SanPham.findByPk(productId, {
      include: [{
        model: DanhMuc,
        as: 'danh_muc',
        attributes: ['ma_danh_muc', 'ten_danh_muc'],
        required: false,
      }],
    });

    if (!product) {
      return res.status(404).json({
        status: 'error',
        message: 'Sản phẩm không tồn tại.',
      });
    }

    res.status(200).json({
      status: 'success',
      data: product,
    });
  } catch (error) {
    console.error('Get product error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi lấy thông tin sản phẩm.',
      error: error.message,
    });
  }
};

/**
 * POST /api/products
 * Tạo sản phẩm mới
 */
exports.createProduct = async (req, res) => {
  try {
    const {
      ten_san_pham, mo_ta, thanh_phan, huong_dan_su_dung,
      gia, gia_khuyen_mai, so_luong_ton, thuong_hieu,
      xuat_xu, ma_danh_muc, loai_da_phu_hop, anh_san_pham, trang_thai,
    } = req.body;

    // Validate required fields
    if (!ten_san_pham || !ten_san_pham.trim()) {
      return res.status(400).json({
        status: 'error',
        message: 'Tên sản phẩm không được để trống.',
      });
    }

    if (!gia || parseFloat(gia) <= 0) {
      return res.status(400).json({
        status: 'error',
        message: 'Giá sản phẩm phải lớn hơn 0.',
      });
    }

    // Validate sale price
    if (gia_khuyen_mai && parseFloat(gia_khuyen_mai) >= parseFloat(gia)) {
      return res.status(400).json({
        status: 'error',
        message: 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
      });
    }

    // Validate category exists (if provided)
    if (ma_danh_muc) {
      const category = await DanhMuc.findByPk(parseInt(ma_danh_muc));
      if (!category) {
        return res.status(400).json({
          status: 'error',
          message: 'Danh mục không tồn tại.',
        });
      }
    }

    // Check duplicate product name
    const existing = await SanPham.findOne({
      where: { ten_san_pham: ten_san_pham.trim() },
    });
    if (existing) {
      return res.status(409).json({
        status: 'error',
        message: 'Tên sản phẩm này đã tồn tại.',
      });
    }

    const product = await SanPham.create({
      ten_san_pham: ten_san_pham.trim(),
      mo_ta: mo_ta || null,
      thanh_phan: thanh_phan || null,
      huong_dan_su_dung: huong_dan_su_dung || null,
      gia: parseFloat(gia),
      gia_khuyen_mai: gia_khuyen_mai ? parseFloat(gia_khuyen_mai) : null,
      so_luong_ton: parseInt(so_luong_ton) || 0,
      thuong_hieu: thuong_hieu || null,
      xuat_xu: xuat_xu || null,
      ma_danh_muc: ma_danh_muc ? parseInt(ma_danh_muc) : null,
      loai_da_phu_hop: loai_da_phu_hop || null,
      anh_san_pham: anh_san_pham || null,
      trang_thai: trang_thai || 'dang_ban',
      ngay_tao: new Date(),
    });

    res.status(201).json({
      status: 'success',
      message: 'Tạo sản phẩm thành công.',
      data: product,
    });
  } catch (error) {
    console.error('Create product error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi tạo sản phẩm.',
      error: error.message,
    });
  }
};

/**
 * PUT /api/products/:id
 * Cập nhật sản phẩm
 */
exports.updateProduct = async (req, res) => {
  try {
    const { id } = req.params;

    const productId = parseInt(id);
    if (isNaN(productId) || productId <= 0) {
      return res.status(400).json({
        status: 'error',
        message: 'ID sản phẩm không hợp lệ.',
      });
    }

    const product = await SanPham.findByPk(productId);
    if (!product) {
      return res.status(404).json({
        status: 'error',
        message: 'Sản phẩm không tồn tại.',
      });
    }

    const {
      ten_san_pham, mo_ta, thanh_phan, huong_dan_su_dung,
      gia, gia_khuyen_mai, so_luong_ton, thuong_hieu,
      xuat_xu, ma_danh_muc, loai_da_phu_hop, anh_san_pham, trang_thai,
    } = req.body;

    // Check unique name (exclude current product)
    if (ten_san_pham && ten_san_pham.trim() !== product.ten_san_pham) {
      const existing = await SanPham.findOne({
        where: {
          ten_san_pham: ten_san_pham.trim(),
          ma_san_pham: { [Op.ne]: productId },
        },
      });
      if (existing) {
        return res.status(409).json({
          status: 'error',
          message: 'Tên sản phẩm này đã tồn tại.',
        });
      }
    }

    // Validate sale price vs original price
    const newGia = gia !== undefined ? parseFloat(gia) : parseFloat(product.gia);
    if (gia_khuyen_mai !== undefined && gia_khuyen_mai !== null && parseFloat(gia_khuyen_mai) >= newGia) {
      return res.status(400).json({
        status: 'error',
        message: 'Giá khuyến mãi phải nhỏ hơn giá gốc.',
      });
    }

    // Validate category if provided
    if (ma_danh_muc !== undefined && ma_danh_muc !== null) {
      const category = await DanhMuc.findByPk(parseInt(ma_danh_muc));
      if (!category) {
        return res.status(400).json({
          status: 'error',
          message: 'Danh mục không tồn tại.',
        });
      }
    }

    // Prepare update data - only update provided fields
    const updateData = {};
    if (ten_san_pham !== undefined) updateData.ten_san_pham = ten_san_pham.trim();
    if (mo_ta !== undefined) updateData.mo_ta = mo_ta;
    if (thanh_phan !== undefined) updateData.thanh_phan = thanh_phan;
    if (huong_dan_su_dung !== undefined) updateData.huong_dan_su_dung = huong_dan_su_dung;
    if (gia !== undefined) updateData.gia = parseFloat(gia);
    if (gia_khuyen_mai !== undefined) updateData.gia_khuyen_mai = gia_khuyen_mai ? parseFloat(gia_khuyen_mai) : null;
    if (so_luong_ton !== undefined) updateData.so_luong_ton = parseInt(so_luong_ton);
    if (thuong_hieu !== undefined) updateData.thuong_hieu = thuong_hieu;
    if (xuat_xu !== undefined) updateData.xuat_xu = xuat_xu;
    if (ma_danh_muc !== undefined) updateData.ma_danh_muc = ma_danh_muc ? parseInt(ma_danh_muc) : null;
    if (loai_da_phu_hop !== undefined) updateData.loai_da_phu_hop = loai_da_phu_hop;
    if (anh_san_pham !== undefined) updateData.anh_san_pham = anh_san_pham || null;
    if (trang_thai !== undefined && ['dang_ban', 'ngung_ban', 'het_hang'].includes(trang_thai)) {
      updateData.trang_thai = trang_thai;
    }

    updateData.ngay_cap_nhat = new Date();

    await product.update(updateData);

    // Reload with category association
    await product.reload({
      include: [{
        model: DanhMuc,
        as: 'danh_muc',
        attributes: ['ma_danh_muc', 'ten_danh_muc'],
        required: false,
      }],
    });

    res.status(200).json({
      status: 'success',
      message: 'Cập nhật sản phẩm thành công.',
      data: product,
    });
  } catch (error) {
    console.error('Update product error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi cập nhật sản phẩm.',
      error: error.message,
    });
  }
};

/**
 * DELETE /api/products/:id
 * Xóa sản phẩm
 */
exports.deleteProduct = async (req, res) => {
  try {
    const { id } = req.params;

    const productId = parseInt(id);
    if (isNaN(productId) || productId <= 0) {
      return res.status(400).json({
        status: 'error',
        message: 'ID sản phẩm không hợp lệ.',
      });
    }

    const product = await SanPham.findByPk(productId);
    if (!product) {
      return res.status(404).json({
        status: 'error',
        message: 'Sản phẩm không tồn tại.',
      });
    }

    // Check if product is referenced in order details
    const [orderCheck] = await sequelize.query(
      `SELECT COUNT(*) AS count FROM chi_tiet_don_hang WHERE ma_san_pham = :id`,
      { replacements: { id: productId }, type: QueryTypes.SELECT }
    );

    if (orderCheck && orderCheck.count > 0) {
      return res.status(400).json({
        status: 'error',
        message: 'Không thể xóa sản phẩm đã có trong đơn hàng. Hãy chuyển sang trạng thái "ngừng bán".',
      });
    }

    await product.destroy();

    res.status(200).json({
      status: 'success',
      message: 'Xóa sản phẩm thành công.',
    });
  } catch (error) {
    console.error('Delete product error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi xóa sản phẩm.',
      error: error.message,
    });
  }
};

/**
 * GET /api/products/stats
 * Thống kê sản phẩm
 */
exports.getStats = async (req, res) => {
  try {
    const total = await SanPham.count();

    const dangBan = await SanPham.count({
      where: { trang_thai: 'dang_ban' },
    });

    const ngungBan = await SanPham.count({
      where: { trang_thai: 'ngung_ban' },
    });

    const hetHang = await SanPham.count({
      where: { so_luong_ton: 0 },
    });

    const sapHetHang = await SanPham.count({
      where: {
        so_luong_ton: { [Op.gt]: 0, [Op.lte]: 30 },
      },
    });

    const khuyenMai = await SanPham.count({
      where: {
        gia_khuyen_mai: { [Op.not]: null },
        trang_thai: 'dang_ban',
      },
    });

    res.status(200).json({
      status: 'success',
      data: {
        total,
        dang_ban: dangBan,
        ngung_ban: ngungBan,
        het_hang: hetHang,
        sap_het_hang: sapHetHang,
        khuyen_mai: khuyenMai,
      },
    });
  } catch (error) {
    console.error('Get product stats error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi lấy thống kê sản phẩm.',
      error: error.message,
    });
  }
};

/**
 * GET /api/products/brands
 * Lấy danh sách thương hiệu (dùng cho filter dropdown)
 */
exports.getBrands = async (req, res) => {
  try {
    const brands = await SanPham.findAll({
      attributes: [[sequelize.fn('DISTINCT', sequelize.col('thuong_hieu')), 'thuong_hieu']],
      where: {
        thuong_hieu: { [Op.not]: null, [Op.ne]: '' },
      },
      order: [['thuong_hieu', 'ASC']],
      raw: true,
    });

    res.status(200).json({
      status: 'success',
      data: brands.map(b => b.thuong_hieu),
    });
  } catch (error) {
    console.error('Get brands error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi lấy danh sách thương hiệu.',
      error: error.message,
    });
  }
};

// ==========================================
// ĐOẠN CODE CỦA MÌNH THÊM VÀO NẰM Ở ĐÂY
// ==========================================
// API: GỌI PYTHON ĐỂ LẤY GỢI Ý AI CHO USER
exports.getAIRecommendation = async (req, res) => {
  try {
      const authHeader = req.headers.authorization;
      if (!authHeader) {
          return res.status(401).json({ success: false, message: 'Bạn chưa đăng nhập' });
      }

      const token = authHeader.split(' ')[1];
      const decoded = jwt.verify(token, process.env.JWT_SECRET);
      
      // =============== ĐOẠN CODE SỬA MỚI NẰM Ở ĐÂY ===============
      // Bỏ cách lấy cũ: const loaiDaUser = decoded.loai_da || 'da_thuong';
      
      // Cách mới: Dùng ID trong token để chui vào Database lấy loại da mới nhất
      const user = await NguoiDung.findByPk(decoded.ma_nguoi_dung);
      const loaiDaUser = (user && user.loai_da) ? user.loai_da : 'da_thuong'; 
      // ==========================================================

      // Gọi sang cổng 5000 của Python
      const pythonResponse = await axios.post('http://localhost:5000/api/recommend', {
          loai_da: loaiDaUser
      });

      // ... (Phần code bên dưới giữ nguyên y hệt như cũ) ...
      const productIds = pythonResponse.data.data;

      if (!productIds || productIds.length === 0) {
          return res.json({ success: true, data: [] });
      }

      const products = await SanPham.findAll({
          where: { ma_san_pham: productIds },
          include: [{ 
              model: AnhSanPham, 
              as: 'danh_sach_anh' 
          }]
      });

      res.json({
          success: true,
          loai_da_text: loaiDaUser, // Chữ gửi về giao diện sẽ cập nhật chuẩn 100%
          data: products
      });

  } catch (error) {
      console.error('Lỗi khi Node.js gọi Python AI:', error);
      res.status(500).json({ success: false, message: 'Hệ thống AI đang bảo trì' });
  }
};
// ==========================================