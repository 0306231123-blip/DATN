/**
 * Model Associations
 * Setup relationships between models
 */

const NguoiDung = require('./NguoiDung');
const DonHang = require('./DonHang');
const SanPham = require('./SanPham');
const DanhMuc = require('./DanhMuc');
const ChiTietDonHang = require('./ChiTietDonHang');

// User has many Orders
NguoiDung.hasMany(DonHang, {
  foreignKey: 'ma_nguoi_dung',
  as: 'orders',
});

// Order belongs to User
DonHang.belongsTo(NguoiDung, {
  foreignKey: 'ma_nguoi_dung',
  as: 'nguoi_dung',
});

// Order has many OrderDetails
DonHang.hasMany(ChiTietDonHang, {
  foreignKey: 'ma_don_hang',
  as: 'chi_tiet',
});

// OrderDetail belongs to Order
ChiTietDonHang.belongsTo(DonHang, {
  foreignKey: 'ma_don_hang',
});

// OrderDetail belongs to Product
ChiTietDonHang.belongsTo(SanPham, {
  foreignKey: 'ma_san_pham',
  as: 'san_pham',
});

// Product has many OrderDetails
SanPham.hasMany(ChiTietDonHang, {
  foreignKey: 'ma_san_pham',
  as: 'chi_tiet_don_hang',
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
  ChiTietDonHang,
};
