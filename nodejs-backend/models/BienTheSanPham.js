const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const SanPham = require('./SanPham');

const BienTheSanPham = sequelize.define('bien_the_san_pham', {
  ma_bien_the: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ma_san_pham: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: SanPham,
      key: 'ma_san_pham',
    }
  },
  sku: {
    type: DataTypes.STRING(100),
    allowNull: false,
    unique: true,
  },
  ten_bien_the: {
    type: DataTypes.STRING(255),
    allowNull: true,
  },
  thuoc_tinh: {
    type: DataTypes.JSON, // Ví dụ: {"mau_sac": "Đỏ", "size": "50ml"}
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
  gia_nhap: {
    type: DataTypes.DECIMAL(12, 2),
    allowNull: true,
  },
  so_luong_ton: {
    type: DataTypes.INTEGER,
    allowNull: false,
    defaultValue: 0,
  },
  hinh_anh: {
    type: DataTypes.STRING(500),
    allowNull: true,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});

module.exports = BienTheSanPham;
