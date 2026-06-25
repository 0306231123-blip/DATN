const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const SanPham = require('./SanPham');
const BienTheSanPham = require('./BienTheSanPham');
const NhaCungCap = require('./NhaCungCap');

const LichSuKho = sequelize.define('lich_su_kho', {
  ma_lich_su: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ma_san_pham: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: SanPham,
      key: 'ma_san_pham'
    }
  },
  ma_bien_the: {
    type: DataTypes.INTEGER,
    allowNull: true,
    references: {
      model: BienTheSanPham,
      key: 'ma_bien_the'
    }
  },
  loai_thao_tac: {
    type: DataTypes.ENUM('nhap_kho', 'xuat_kho', 'ban_hang', 'huy_don', 'kiem_kho'),
    allowNull: false,
  },
  so_luong_thay_doi: {
    type: DataTypes.INTEGER,
    allowNull: false, // Số âm cho xuất, dương cho nhập
  },
  ton_kho_cuoi: {
    type: DataTypes.INTEGER,
    allowNull: false,
  },
  gia_nhap: {
    type: DataTypes.DECIMAL(12, 2),
    allowNull: true, // Giá nhập vốn (thường dùng khi nhap_kho)
  },
  ma_nha_cung_cap: {
    type: DataTypes.INTEGER,
    allowNull: true,
    references: {
      model: NhaCungCap,
      key: 'ma_nha_cung_cap'
    }
  },
  ma_don_hang: {
    type: DataTypes.INTEGER,
    allowNull: true,
  },
  ghi_chu: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  nguoi_thuc_hien: {
    type: DataTypes.INTEGER,
    allowNull: true, // ID User/Admin thực hiện
  },
  ngay_tao: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});

module.exports = LichSuKho;
