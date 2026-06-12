/**
 * Model Associations
 * Setup relationships between models
 */

const NguoiDung = require('./NguoiDung');
const DonHang = require('./DonHang');
const SanPham = require('./SanPham');
const DanhMuc = require('./DanhMuc');

// User has many Orders
NguoiDung.hasMany(DonHang, {
  foreignKey: 'ma_nguoi_dung',
  as: 'orders',
});

// Order belongs to User
DonHang.belongsTo(NguoiDung, {
  foreignKey: 'ma_nguoi_dung',
  as: 'user',
});

// Product belongs to Category
SanPham.belongsTo(DanhMuc, {
  foreignKey: 'ma_danh_muc',
  as: 'danh_muc',
});

// Category has many Products
DanhMuc.hasMany(SanPham, {
  foreignKey: 'ma_danh_muc',
  as: 'san_pham',
});

module.exports = {
  NguoiDung,
  DonHang,
  SanPham,
  DanhMuc,
};

