/**
 * Model Associations
 * Setup relationships between models
 * 
 * IMPORTANT: Tất cả associations được khai báo TẬP TRUNG tại đây.
 * KHÔNG khai báo associations trong các file model riêng lẻ để tránh circular dependency.
 */

const NguoiDung = require('./NguoiDung');
const DonHang = require('./DonHang');
const SanPham = require('./SanPham');
const DanhMuc = require('./DanhMuc');
const ChiTietDonHang = require('./ChiTietDonHang');
const AnhSanPham = require('./AnhSanPham');
const GioHang = require('./GioHang');
const BienTheSanPham = require('./BienTheSanPham');
const NhaCungCap = require('./NhaCungCap');
const LichSuKho = require('./LichSuKho');
const YeuCauTraHang = require('./YeuCauTraHang');

// ========== User ↔ Order ==========
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

// ========== Order ↔ OrderDetail ==========
// Order has many OrderDetails
DonHang.hasMany(ChiTietDonHang, {
  foreignKey: 'ma_don_hang',
  as: 'chi_tiet',
});

// OrderDetail belongs to Order
ChiTietDonHang.belongsTo(DonHang, {
  foreignKey: 'ma_don_hang',
});

// ========== Order ↔ ReturnRequest ==========
DonHang.hasOne(YeuCauTraHang, {
  foreignKey: 'ma_don_hang',
  as: 'yeu_cau_tra_hang',
});

YeuCauTraHang.belongsTo(DonHang, {
  foreignKey: 'ma_don_hang',
  as: 'don_hang',
});

// ========== OrderDetail ↔ Product ==========
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

// ========== Product ↔ Category ==========
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

// ========== Product ↔ ProductImage ==========
// Product has many Images
SanPham.hasMany(AnhSanPham, {
  foreignKey: 'ma_san_pham',
  as: 'danh_sach_anh',
});

// Image belongs to Product
AnhSanPham.belongsTo(SanPham, {
  foreignKey: 'ma_san_pham',
  as: 'san_pham',
});

// ========== Cart ↔ Product ==========
// Cart item belongs to Product
GioHang.belongsTo(SanPham, {
  foreignKey: 'ma_san_pham',
  as: 'san_pham',
});

// Product has many Cart items
SanPham.hasMany(GioHang, {
  foreignKey: 'ma_san_pham',
  as: 'gio_hang',
});

// ========== Product ↔ Variant ==========
SanPham.hasMany(BienTheSanPham, {
  foreignKey: 'ma_san_pham',
  as: 'bien_the',
});
BienTheSanPham.belongsTo(SanPham, {
  foreignKey: 'ma_san_pham',
  as: 'san_pham',
});

// ========== Inventory ↔ Product/Variant/Supplier ==========
LichSuKho.belongsTo(SanPham, { foreignKey: 'ma_san_pham', as: 'san_pham' });
LichSuKho.belongsTo(BienTheSanPham, { foreignKey: 'ma_bien_the', as: 'bien_the' });
LichSuKho.belongsTo(NhaCungCap, { foreignKey: 'ma_nha_cung_cap', as: 'nha_cung_cap' });

SanPham.hasMany(LichSuKho, { foreignKey: 'ma_san_pham', as: 'lich_su_kho' });
BienTheSanPham.hasMany(LichSuKho, { foreignKey: 'ma_bien_the', as: 'lich_su_kho' });
NhaCungCap.hasMany(LichSuKho, { foreignKey: 'ma_nha_cung_cap', as: 'lich_su_kho' });

module.exports = {
  NguoiDung,
  DonHang,
  SanPham,
  DanhMuc,
  ChiTietDonHang,
  AnhSanPham,
  GioHang,
  BienTheSanPham,
  NhaCungCap,
  LichSuKho,
  YeuCauTraHang,
};

