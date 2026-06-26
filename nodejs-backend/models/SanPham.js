const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const AnhSanPham = require('./AnhSanPham'); // Import model ảnh

const SanPham = sequelize.define('san_pham', {
  ma_san_pham: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ten_san_pham: {
    type: DataTypes.STRING(255),
    allowNull: false,
  },
  sku: {
    type: DataTypes.STRING(100),
    allowNull: true,
    unique: true,
  },
  co_bien_the: {
    type: DataTypes.BOOLEAN,
    defaultValue: false,
  },
  mo_ta: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  thanh_phan: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  huong_dan_su_dung: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  gia: {
    type: DataTypes.DECIMAL(12, 2),
    allowNull: false,
  },
  gia_khuyen_mai: {
    type: DataTypes.DECIMAL(12, 2),
    allowNull: true,
  },
  gia_max: {
    type: DataTypes.DECIMAL(12, 2),
    allowNull: true,
  },
  so_luong_ton: {
    type: DataTypes.INTEGER,
    allowNull: false,
    defaultValue: 0,
  },
  thuong_hieu: {
    type: DataTypes.STRING(100),
    allowNull: true,
  },
  xuat_xu: {
    type: DataTypes.STRING(100),
    allowNull: true,
  },
  ma_danh_muc: {
    type: DataTypes.INTEGER,
    allowNull: true,
  },
  loai_da_phu_hop: {
    type: DataTypes.STRING(255),
    allowNull: true,
  },
  
  anh_san_pham: {
    type: DataTypes.STRING(500),
    allowNull: true,
  },
  diem_danh_gia: {
    type: DataTypes.DECIMAL(3, 1),
    allowNull: true,
    defaultValue: 0,
  },
  so_luot_danh_gia: {
    type: DataTypes.INTEGER,
    allowNull: true,
    defaultValue: 0,
  },
  trang_thai: {
    type: DataTypes.ENUM('dang_ban', 'ngung_ban', 'het_hang'),
    defaultValue: 'dang_ban',
  },
  ngay_tao: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
  ngay_cap_nhat: {
    type: DataTypes.DATE,
    allowNull: true,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});



module.exports = SanPham;
