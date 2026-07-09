const sequelize = require('../config/database');
const SanPham = require('../models/SanPham');
const BienTheSanPham = require('../models/BienTheSanPham');
const LichSuKho = require('../models/LichSuKho');
const DanhMuc = require('../models/DanhMuc');
const NhaCungCap = require('../models/NhaCungCap');
const NguoiDung = require('../models/NguoiDung');

exports.getInventoryLogs = async (req, res) => {
  try {
    const { ma_san_pham, ma_bien_the } = req.query;
    let where = {};
    if (ma_san_pham) where.ma_san_pham = ma_san_pham;
    if (ma_bien_the) where.ma_bien_the = ma_bien_the;

    const logs = await LichSuKho.findAll({
      where,
      order: [['ngay_tao', 'DESC']],
      include: [
        { model: NhaCungCap, as: 'nha_cung_cap' },
        { model: SanPham, as: 'san_pham', attributes: ['ten_san_pham', 'sku'] },
        { model: BienTheSanPham, as: 'bien_the', attributes: ['ten_bien_the', 'sku'] }
      ]
    });
    res.json({ status: 'success', data: logs });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
};

exports.importInventory = async (req, res) => {
  const t = await sequelize.transaction();
  try {
    const { items, ghi_chu, ma_nha_cung_cap, nguoi_thuc_hien } = req.body;
    // items = [{ ma_san_pham, ma_bien_the, so_luong, gia_nhap }]
    
    if (!items || !Array.isArray(items) || items.length === 0) {
      await t.rollback();
      return res.status(400).json({ status: 'error', message: 'Danh sách nhập kho trống.' });
    }

    for (let item of items) {
      const { ma_san_pham, ma_bien_the, so_luong, gia_nhap } = item;
      const amount = parseInt(so_luong);
      if (isNaN(amount) || amount <= 0) continue;

      let ton_kho_cuoi = 0;

      // Check if variant or product
      if (ma_bien_the) {
        const variant = await BienTheSanPham.findByPk(ma_bien_the, { transaction: t });
        if (variant) {
          variant.so_luong_ton += amount;
          if (gia_nhap) variant.gia_nhap = parseFloat(gia_nhap);
          ton_kho_cuoi = variant.so_luong_ton;
          await variant.save({ transaction: t });
          
          // Must update product total stock
          const product = await SanPham.findByPk(ma_san_pham, { transaction: t });
          if (product) {
            product.so_luong_ton += amount;
            if (gia_nhap) product.gia_nhap = parseFloat(gia_nhap);
            await product.save({ transaction: t });
          }
        }
      } else {
        const product = await SanPham.findByPk(ma_san_pham, { transaction: t });
        if (product) {
          product.so_luong_ton += amount;
          if (gia_nhap) product.gia_nhap = parseFloat(gia_nhap);
          ton_kho_cuoi = product.so_luong_ton;
          await product.save({ transaction: t });
        }
      }

      // Create log
      await LichSuKho.create({
        ma_san_pham,
        ma_bien_the: ma_bien_the || null,
        loai_thao_tac: 'nhap_kho',
        so_luong_thay_doi: amount,
        ton_kho_cuoi,
        gia_nhap: gia_nhap ? parseFloat(gia_nhap) : null,
        ma_nha_cung_cap: ma_nha_cung_cap || null,
        ghi_chu,
        nguoi_thuc_hien
      }, { transaction: t });
    }

    await t.commit();
    res.json({ status: 'success', message: 'Nhập kho thành công.' });
  } catch (error) {
    if (t) await t.rollback();
    res.status(500).json({ status: 'error', message: error.message });
  }
};

exports.exportInventory = async (req, res) => {
  const t = await sequelize.transaction();
  try {
    const { items, ghi_chu, nguoi_thuc_hien } = req.body;
    // items = [{ ma_san_pham, ma_bien_the, so_luong }]
    
    if (!items || !Array.isArray(items) || items.length === 0) {
      await t.rollback();
      return res.status(400).json({ status: 'error', message: 'Danh sách xuất kho trống.' });
    }

    for (let item of items) {
      const { ma_san_pham, ma_bien_the, so_luong } = item;
      const amount = parseInt(so_luong);
      if (isNaN(amount) || amount <= 0) continue;

      let ton_kho_cuoi = 0;

      if (ma_bien_the) {
        const variant = await BienTheSanPham.findByPk(ma_bien_the, { transaction: t });
        if (variant) {
          variant.so_luong_ton = Math.max(0, variant.so_luong_ton - amount);
          ton_kho_cuoi = variant.so_luong_ton;
          await variant.save({ transaction: t });
          
          const product = await SanPham.findByPk(ma_san_pham, { transaction: t });
          if (product) {
            product.so_luong_ton = Math.max(0, product.so_luong_ton - amount);
            await product.save({ transaction: t });
          }
        }
      } else {
        const product = await SanPham.findByPk(ma_san_pham, { transaction: t });
        if (product) {
          product.so_luong_ton = Math.max(0, product.so_luong_ton - amount);
          ton_kho_cuoi = product.so_luong_ton;
          await product.save({ transaction: t });
        }
      }

      await LichSuKho.create({
        ma_san_pham,
        ma_bien_the: ma_bien_the || null,
        loai_thao_tac: 'xuat_kho',
        so_luong_thay_doi: -amount,
        ton_kho_cuoi,
        ghi_chu,
        nguoi_thuc_hien
      }, { transaction: t });
    }

    await t.commit();
    res.json({ status: 'success', message: 'Xuất kho thành công.' });
  } catch (error) {
    if (t) await t.rollback();
    res.status(500).json({ status: 'error', message: error.message });
  }
};
