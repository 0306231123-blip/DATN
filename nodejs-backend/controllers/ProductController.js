// ==========================================
// THƯ VIỆN THÊM VÀO CHO AI (Thêm mới)
// ==========================================
const axios = require('axios');
const jwt = require('jsonwebtoken');
const AnhSanPham = require('../models/AnhSanPham');
const NguoiDung = require('../models/NguoiDung');
// ==========================================

const SanPham = require('../models/SanPham');
const BienTheSanPham = require('../models/BienTheSanPham');
const DanhMuc = require('../models/DanhMuc');
const { Op } = require('sequelize');
const sequelize = require('../config/database');
const { QueryTypes } = require('sequelize');
const { logActivity } = require('../utils/logger');

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
      low_stock_threshold = 20,
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
    if (trang_thai && trang_thai !== 'all') {
      if (trang_thai === 'sap_het_hang') {
        where.so_luong_ton = { [Op.gt]: 0, [Op.lt]: parseInt(low_stock_threshold) || 20 };
      } else if (['dang_ban', 'ngung_ban', 'het_hang'].includes(trang_thai)) {
        where.trang_thai = trang_thai;
      }
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
      }, {
        model: BienTheSanPham,
        as: 'bien_the',
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
    if (isNaN(productId) || productId < 0) {
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
      }, {
        model: BienTheSanPham,
        as: 'bien_the',
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
  const t = await sequelize.transaction();
  try {
    const {
      ten_san_pham, mo_ta, thanh_phan, huong_dan_su_dung,
      gia, gia_khuyen_mai, so_luong_ton, thuong_hieu,
      xuat_xu, ma_danh_muc, loai_da_phu_hop, anh_san_pham, trang_thai,
      sku, variants
    } = req.body;

    if (!ten_san_pham || !ten_san_pham.trim()) {
      await t.rollback();
      return res.status(400).json({ status: 'error', message: 'Tên sản phẩm không được để trống.' });
    }

    if (ma_danh_muc) {
      const category = await DanhMuc.findByPk(parseInt(ma_danh_muc));
      if (!category) {
        await t.rollback();
        return res.status(400).json({ status: 'error', message: 'Danh mục không tồn tại.' });
      }
    }

    const existing = await SanPham.findOne({ where: { ten_san_pham: ten_san_pham.trim() } });
    if (existing) {
      await t.rollback();
      return res.status(409).json({ status: 'error', message: 'Tên sản phẩm này đã tồn tại.' });
    }

    let finalGia = parseFloat(gia) || 0;
    let finalGiaMax = finalGia;
    let finalSoLuongTon = parseInt(so_luong_ton) || 0;
    let finalCoBienThe = false;

    if (variants && Array.isArray(variants) && variants.length > 0) {
      finalCoBienThe = true;
      const validGia = variants.map(v => parseFloat(v.gia)).filter(g => !isNaN(g) && g > 0);
      if (validGia.length > 0) {
        finalGia = Math.min(...validGia);
        finalGiaMax = Math.max(...validGia);
      }
      finalSoLuongTon = variants.reduce((sum, v) => sum + (parseInt(v.so_luong_ton) || 0), 0);
    }

    if (!finalCoBienThe && finalGia <= 0) {
      await t.rollback();
      return res.status(400).json({ status: 'error', message: 'Giá sản phẩm phải lớn hơn 0.' });
    }

    const product = await SanPham.create({
      ten_san_pham: ten_san_pham.trim(),
      sku: sku || null,
      co_bien_the: finalCoBienThe,
      mo_ta: mo_ta || null,
      thanh_phan: thanh_phan || null,
      huong_dan_su_dung: huong_dan_su_dung || null,
      gia: finalGia,
      gia_max: finalGiaMax,
      gia_khuyen_mai: gia_khuyen_mai ? parseFloat(gia_khuyen_mai) : null,
      so_luong_ton: finalSoLuongTon,
      thuong_hieu: thuong_hieu || null,
      xuat_xu: xuat_xu || null,
      ma_danh_muc: ma_danh_muc ? parseInt(ma_danh_muc) : null,
      loai_da_phu_hop: loai_da_phu_hop || null,
      anh_san_pham: anh_san_pham || null,
      trang_thai: trang_thai || 'dang_ban',
      ngay_tao: new Date(),
    }, { transaction: t });

    if (finalCoBienThe) {
      const variantRecords = variants.map(v => ({
        ma_san_pham: product.ma_san_pham,
        sku: v.sku,
        ten_bien_the: v.ten_bien_the,
        thuoc_tinh: v.thuoc_tinh,
        gia: parseFloat(v.gia),
        gia_khuyen_mai: v.gia_khuyen_mai ? parseFloat(v.gia_khuyen_mai) : null,
        so_luong_ton: parseInt(v.so_luong_ton) || 0,
        hinh_anh: v.hinh_anh || null
      }));
      await BienTheSanPham.bulkCreate(variantRecords, { transaction: t });
    }

    await t.commit();

    const createdProduct = await SanPham.findByPk(product.ma_san_pham, {
      include: [
        { model: DanhMuc, as: 'danh_muc', attributes: ['ma_danh_muc', 'ten_danh_muc'] },
        { model: BienTheSanPham, as: 'bien_the' }
      ]
    });

    // LƯU LOG
    if (req.user) {
      await logActivity(req.user.ma_nguoi_dung, 'Thêm', 'san_pham', `Thêm sản phẩm mới: ${product.ten_san_pham}`);
    }

    res.status(201).json({
      status: 'success',
      message: 'Tạo sản phẩm thành công.',
      data: createdProduct,
    });
  } catch (error) {
    if (t) await t.rollback();
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
  const t = await sequelize.transaction();
  try {
    const { id } = req.params;

    const productId = parseInt(id);
    if (isNaN(productId) || productId < 0) {
      await t.rollback();
      return res.status(400).json({ status: 'error', message: 'ID sản phẩm không hợp lệ.' });
    }

    const product = await SanPham.findByPk(productId);
    if (!product) {
      await t.rollback();
      return res.status(404).json({ status: 'error', message: 'Sản phẩm không tồn tại.' });
    }

    const {
      ten_san_pham, mo_ta, thanh_phan, huong_dan_su_dung,
      gia, gia_khuyen_mai, so_luong_ton, thuong_hieu,
      xuat_xu, ma_danh_muc, loai_da_phu_hop, anh_san_pham, trang_thai,
      sku, variants
    } = req.body;

    if (ten_san_pham && ten_san_pham.trim() !== product.ten_san_pham) {
      const existing = await SanPham.findOne({
        where: { ten_san_pham: ten_san_pham.trim(), ma_san_pham: { [Op.ne]: productId } }
      });
      if (existing) {
        await t.rollback();
        return res.status(409).json({ status: 'error', message: 'Tên sản phẩm này đã tồn tại.' });
      }
    }

    let finalGia = gia !== undefined ? parseFloat(gia) : parseFloat(product.gia);
    let finalGiaMax = product.gia_max !== undefined ? parseFloat(product.gia_max) : finalGia;
    let finalSoLuongTon = so_luong_ton !== undefined ? parseInt(so_luong_ton) : parseInt(product.so_luong_ton);
    let finalCoBienThe = product.co_bien_the;

    if (variants && Array.isArray(variants)) {
      if (variants.length > 0) {
        finalCoBienThe = true;
        const validGia = variants.map(v => parseFloat(v.gia)).filter(g => !isNaN(g) && g > 0);
        if (validGia.length > 0) {
          finalGia = Math.min(...validGia);
          finalGiaMax = Math.max(...validGia);
        }
        finalSoLuongTon = variants.reduce((sum, v) => sum + (parseInt(v.so_luong_ton) || 0), 0);
      } else {
        finalCoBienThe = false;
      }
    }

    const updateData = {};
    if (ten_san_pham !== undefined) updateData.ten_san_pham = ten_san_pham.trim();
    if (sku !== undefined) updateData.sku = sku || null;
    updateData.co_bien_the = finalCoBienThe;
    if (mo_ta !== undefined) updateData.mo_ta = mo_ta;
    if (thanh_phan !== undefined) updateData.thanh_phan = thanh_phan;
    if (huong_dan_su_dung !== undefined) updateData.huong_dan_su_dung = huong_dan_su_dung;
    updateData.gia = finalGia;
    updateData.gia_max = finalGiaMax;
    if (gia_khuyen_mai !== undefined) updateData.gia_khuyen_mai = gia_khuyen_mai ? parseFloat(gia_khuyen_mai) : null;
    updateData.so_luong_ton = finalSoLuongTon;
    if (thuong_hieu !== undefined) updateData.thuong_hieu = thuong_hieu;
    if (xuat_xu !== undefined) updateData.xuat_xu = xuat_xu;
    if (ma_danh_muc !== undefined) updateData.ma_danh_muc = ma_danh_muc ? parseInt(ma_danh_muc) : null;
    if (loai_da_phu_hop !== undefined) updateData.loai_da_phu_hop = loai_da_phu_hop;
    if (anh_san_pham !== undefined) updateData.anh_san_pham = anh_san_pham || null;
    if (trang_thai !== undefined && ['dang_ban', 'ngung_ban', 'het_hang'].includes(trang_thai)) {
      updateData.trang_thai = trang_thai;
    }

    updateData.ngay_cap_nhat = new Date();

    await product.update(updateData, { transaction: t });

    if (variants && Array.isArray(variants)) {
      const existingVariants = await BienTheSanPham.findAll({ where: { ma_san_pham: productId } });
      const incomingIds = variants.filter(v => v.ma_bien_the).map(v => parseInt(v.ma_bien_the));
      
      for (let ev of existingVariants) {
        if (!incomingIds.includes(ev.ma_bien_the)) {
          try {
            await ev.destroy({ transaction: t });
          } catch (err) {
            // Ignoring constraint error during deletion, leaving it alone
          }
        }
      }

      for (let v of variants) {
        if (v.ma_bien_the) {
          const ev = existingVariants.find(e => e.ma_bien_the === parseInt(v.ma_bien_the));
          if (ev) {
            await ev.update({
              sku: v.sku,
              ten_bien_the: v.ten_bien_the,
              thuoc_tinh: v.thuoc_tinh,
              gia: parseFloat(v.gia),
              gia_khuyen_mai: v.gia_khuyen_mai ? parseFloat(v.gia_khuyen_mai) : null,
              so_luong_ton: parseInt(v.so_luong_ton) || 0,
              hinh_anh: v.hinh_anh || null
            }, { transaction: t });
          }
        } else {
          await BienTheSanPham.create({
            ma_san_pham: productId,
            sku: v.sku,
            ten_bien_the: v.ten_bien_the,
            thuoc_tinh: v.thuoc_tinh,
            gia: parseFloat(v.gia),
            gia_khuyen_mai: v.gia_khuyen_mai ? parseFloat(v.gia_khuyen_mai) : null,
            so_luong_ton: parseInt(v.so_luong_ton) || 0,
            hinh_anh: v.hinh_anh || null
          }, { transaction: t });
        }
      }
    }

    await t.commit();

    await product.reload({
      include: [
        { model: DanhMuc, as: 'danh_muc', attributes: ['ma_danh_muc', 'ten_danh_muc'] },
        { model: BienTheSanPham, as: 'bien_the' }
      ]
    });

    res.status(200).json({ status: 'success', message: 'Cập nhật sản phẩm thành công.', data: product });
    logActivity(req.user ? req.user.ma_nguoi_dung : null, 'CẬP NHẬT', 'san_pham', `Cập nhật sản phẩm #${productId}: ${product.ten_san_pham}`);
  } catch (error) {
    if (t) await t.rollback();
    console.error('Update product error:', error);
    res.status(500).json({ status: 'error', message: 'Lỗi khi cập nhật sản phẩm.', error: error.message });
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
    if (isNaN(productId) || productId < 0) {
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

    // LƯU LOG XÓA SẢN PHẨM
    if (req.user) {
      await logActivity(req.user.ma_nguoi_dung, 'Xóa', 'san_pham', `Xóa sản phẩm: ${product.ten_san_pham}`);
    }

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
    const lowStockThreshold = parseInt(req.query.low_stock_threshold) || 20;
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
        so_luong_ton: { [Op.gt]: 0, [Op.lt]: lowStockThreshold },
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
        sap_het: sapHetHang,
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

/**
 * POST /api/products/bulk
 * Nhập hàng loạt sản phẩm từ Excel (JSON array)
 */
exports.bulkCreateProducts = async (req, res) => {
  const t = await sequelize.transaction();
  try {
    const { products } = req.body;
    
    if (!products || !Array.isArray(products) || products.length === 0) {
      await t.rollback();
      return res.status(400).json({ status: 'error', message: 'Dữ liệu không hợp lệ hoặc trống.' });
    }

    let successCount = 0;
    
    // Tìm ID lớn nhất hiện tại để tránh lỗi AUTO_INCREMENT bị lệch (PRIMARY must be unique)
    const maxProduct = await SanPham.findOne({ order: [['ma_san_pham', 'DESC']], transaction: t });
    let nextId = maxProduct ? maxProduct.ma_san_pham + 1 : 1;
    
    for (const item of products) {
      // Hàm helper tìm key bất chấp chữ hoa/chữ thường
      const findVal = (keywords) => {
        const key = Object.keys(item).find(k => keywords.some(kw => k.toLowerCase().includes(kw)));
        return key ? item[key] : null;
      };

      let tenSp = findVal(['tên', 'ten_san_pham', 'name']);
      if (tenSp === null || tenSp === undefined || String(tenSp).trim() === '') {
         continue; // Bỏ qua nếu không có tên
      }
      tenSp = String(tenSp).trim();
      
      const giaStr = findVal(['giá', 'gia', 'price']);
      let gia = 0;
      if (giaStr !== null && giaStr !== undefined) {
         const cleanGia = String(giaStr).replace(/[^\d]/g, '');
         gia = parseFloat(cleanGia) || 0;
      }
      
      const soLuongStr = findVal(['tồn', 'ton', 'số lượng', 'so luong', 'sl', 'stock']);
      const soLuong = soLuongStr ? parseInt(String(soLuongStr).replace(/[^\d]/g, '')) || 0 : 0;
      
      const thuongHieu = findVal(['thương', 'thuong', 'brand']);
      const danhMucName = findVal(['danh', 'danh mục', 'category']);
      const sku = findVal(['sku', 'mã', 'ma_sp']);
      
      let maDanhMuc = null;
      if (danhMucName) {
        let category = await DanhMuc.findOne({ where: { ten_danh_muc: String(danhMucName).trim() } });
        if (category) {
          maDanhMuc = category.ma_danh_muc;
        }
      }

      // Kiểm tra trùng lặp
      const existing = await SanPham.findOne({ where: { ten_san_pham: tenSp }, transaction: t });
      if (existing) continue; // Bỏ qua nếu đã tồn tại

      await SanPham.create({
        ma_san_pham: nextId,
        ten_san_pham: tenSp,
        co_bien_the: false,
        gia: gia > 0 ? gia : 0,
        gia_max: gia > 0 ? gia : 0,
        so_luong_ton: soLuong > 0 ? soLuong : 0,
        thuong_hieu: thuongHieu ? String(thuongHieu).trim() : null,
        ma_danh_muc: maDanhMuc,
        sku: sku ? String(sku).trim() : null,
        trang_thai: 'dang_ban',
        ngay_tao: new Date(),
      }, { transaction: t });
      
      nextId++;
      successCount++;
    }

    await t.commit();
    
    // Log
    if (req.user) {
      await logActivity(req.user.ma_nguoi_dung, 'Thêm', 'san_pham', `Nhập từ Excel ${successCount} sản phẩm`);
    }

    res.status(200).json({
      status: 'success',
      message: `Đã nhập thành công ${successCount} sản phẩm.`,
      data: { count: successCount }
    });
    
  } catch (error) {
    if (t) await t.rollback();
    console.error('Bulk create product error:', error);
    
    let errDetails = error.message;
    if (error.errors && Array.isArray(error.errors)) {
      errDetails += " - " + error.errors.map(e => `${e.path}: ${e.message}`).join(", ");
    }
    
    try {
      require('fs').writeFileSync('c:/xampp/htdocs/cosmetic-shop/debug_error.txt', errDetails + '\n' + error.stack);
    } catch(e) {}
    
    res.status(500).json({
      status: 'error',
      message: errDetails,
      error: error.message,
    });
  }
};

/**
 * GET /api/products/market-price/:id
 * Đề xuất giá nhập dựa vào giá bán (giả lập)
 */
exports.getMarketPrice = async (req, res) => {
  try {
    const { id } = req.params;
    const { type } = req.query; // 'product' or 'variant'
    
    let giaBan = 0;
    
    if (type === 'variant') {
      const variant = await BienTheSanPham.findByPk(id);
      if (!variant) return res.status(404).json({ success: false, message: 'Không tìm thấy phân loại' });
      giaBan = variant.gia;
    } else {
      const product = await SanPham.findByPk(id);
      if (!product) return res.status(404).json({ success: false, message: 'Không tìm thấy sản phẩm' });
      giaBan = product.gia;
    }
    
    if (!giaBan || giaBan <= 0) {
       return res.status(200).json({ success: true, data: { suggested_price: 0 } });
    }
    
    // Tính giá nhập giả lập: khoảng 60% - 70% giá bán
    const randomFactor = Math.random() * (0.7 - 0.6) + 0.6; // random giữa 0.6 và 0.7
    let giaNhap = Math.round(giaBan * randomFactor);
    
    // Làm tròn đến hàng nghìn
    giaNhap = Math.round(giaNhap / 1000) * 1000;
    
    res.status(200).json({
      success: true,
      data: {
        suggested_price: giaNhap
      }
    });
    
  } catch (error) {
    console.error('Get market price error:', error);
    res.status(500).json({ success: false, message: 'Lỗi khi tính giá nhập đề xuất.' });
  }
};


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

      // Gọi sang cổng 5000 của Python (Tạm tắt do đang đập đi xây lại)
      /*
      const pythonResponse = await axios.post('http://localhost:5000/api/recommend', {
          loai_da: loaiDaUser,
          ma_nguoi_dung: decoded.ma_nguoi_dung
      });
      const aiData = pythonResponse.data.data;
      */
      
      const aiData = { low_stock: [], next_step: [], skin_type: [] };

      // Gom tất cả ID lại để query 1 lần cho nhẹ DB
      const allProductIds = new Set([
          ...(aiData.low_stock || []),
          ...(aiData.next_step || []),
          ...(aiData.skin_type || [])
      ]);

      if (allProductIds.size === 0) {
          return res.json({ success: true, data: { low_stock: [], next_step: [], skin_type: [] } });
      }

      const products = await SanPham.findAll({
          where: { ma_san_pham: Array.from(allProductIds) },
          include: [{ 
              model: AnhSanPham, 
              as: 'danh_sach_anh' 
          }]
      });

      // Hàm helper để map lại mảng sản phẩm theo đúng thứ tự ID mà Python trả về
      const mapProducts = (ids) => ids.map(id => products.find(p => p.ma_san_pham === id)).filter(p => p);

      res.json({
          success: true,
          loai_da_text: loaiDaUser,
          data: {
              low_stock: mapProducts(aiData.low_stock || []),
              next_step: mapProducts(aiData.next_step || []),
              skin_type: mapProducts(aiData.skin_type || [])
          }
      });

  } catch (error) {
      console.error('Lỗi khi Node.js gọi Python AI:', error);
      res.status(500).json({ success: false, message: 'Hệ thống AI đang bảo trì' });
  }
};


// ==========================================