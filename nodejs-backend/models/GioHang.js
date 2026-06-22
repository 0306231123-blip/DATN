const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const GioHang = sequelize.define('gio_hang', {
  ma_gio_hang: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true }, // Nhớ có dấu phẩy ở cuối
  ma_nguoi_dung: { type: DataTypes.INTEGER, allowNull: false },
  ma_san_pham: { type: DataTypes.INTEGER, allowNull: false },
  so_luong: { type: DataTypes.INTEGER, defaultValue: 1 },
  ngay_them: { type: DataTypes.DATE, defaultValue: DataTypes.NOW }
}, { timestamps: false });

module.exports = GioHang;