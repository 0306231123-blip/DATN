const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const NguoiDung = require('./NguoiDung');

const DonHang = sequelize.define('don_hang', {
  ma_don_hang: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ma_nguoi_dung: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: NguoiDung,
      key: 'ma_nguoi_dung',
    },
  },
  so_don_hang: {
    type: DataTypes.STRING(50),
    allowNull: false,
    unique: true,
  },
  tong_thanh_toan: {
    type: DataTypes.DECIMAL(10, 2),
    allowNull: false,
  },
  trang_thai_don: {
    type: DataTypes.ENUM('cho_xu_ly', 'dang_van_chuyen', 'giao_thanh_cong', 'huy'),
    defaultValue: 'cho_xu_ly',
  },
  ghi_chu: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  ngay_dat: {
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

// Relationship
DonHang.belongsTo(NguoiDung, { foreignKey: 'ma_nguoi_dung' });

module.exports = DonHang;
