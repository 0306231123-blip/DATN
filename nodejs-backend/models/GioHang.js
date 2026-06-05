const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const SanPham = require('./SanPham');

const GioHang = sequelize.define('gio_hang', {
  ma_gio_hang: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true }, // Nhớ có dấu phẩy ở cuối
  ma_nguoi_dung: { type: DataTypes.INTEGER, allowNull: false }, // Dấu phẩy
  ma_san_pham: { type: DataTypes.INTEGER, allowNull: false }, // Dấu phẩy
  so_luong: { type: DataTypes.INTEGER, defaultValue: 1 }, // Dấu phẩy
  ngay_them: { type: DataTypes.DATE, defaultValue: DataTypes.NOW }
}, { timestamps: false });

GioHang.belongsTo(SanPham, { foreignKey: 'ma_san_pham', as: 'san_pham' });

module.exports = GioHang;