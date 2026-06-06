const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const AnhSanPham = require('./AnhSanPham'); // Import model ảnh

const SanPham = sequelize.define('san_pham', {
  ma_san_pham: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
  ten_san_pham: { type: DataTypes.STRING, allowNull: false },
  gia: { type: DataTypes.DECIMAL(10, 0), allowNull: false },
  gia_khuyen_mai: { type: DataTypes.DECIMAL(10, 0) },
  // Thêm các cột khác nếu cần
}, { timestamps: false, freezeTableName: true });

// KHAI BÁO LIÊN KẾT: 1 Sản phẩm có nhiều Ảnh
SanPham.hasMany(AnhSanPham, { foreignKey: 'ma_san_pham', as: 'anh_san_pham' });

module.exports = SanPham;