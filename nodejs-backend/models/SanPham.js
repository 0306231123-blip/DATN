const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const SanPham = sequelize.define('san_pham', {
  ma_san_pham: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
  ten_san_pham: { type: DataTypes.STRING, allowNull: false },
  gia: { type: DataTypes.DECIMAL(10, 0), allowNull: false },
  gia_khuyen_mai: { type: DataTypes.DECIMAL(10, 0) },
  // Thêm các cột khác nếu cần
}, { timestamps: false });

module.exports = SanPham;