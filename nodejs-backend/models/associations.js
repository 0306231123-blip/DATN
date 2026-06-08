/**
 * Model Associations
 * Setup relationships between models
 */

const NguoiDung = require('./NguoiDung');
const DonHang = require('./DonHang');

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

module.exports = {
  NguoiDung,
  DonHang,
};
