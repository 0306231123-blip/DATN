const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const NguoiDung = sequelize.define('nguoi_dung', {
  ma_nguoi_dung: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ho_ten: {
    type: DataTypes.STRING(100),
    allowNull: false,
  },
  email: {
    type: DataTypes.STRING(150),
    allowNull: false,
    unique: true,
  },
  mat_khau: {
    type: DataTypes.STRING(255),
    allowNull: false,
  },
  so_dien_thoai: {
    type: DataTypes.STRING(15),
    allowNull: true,
  },
  dia_chi: {
    type: DataTypes.STRING(300),
    allowNull: true,
  },
  loai_da: {
    type: DataTypes.ENUM('da_dau', 'da_kho', 'da_hon_hop', 'da_nhay_cam', 'da_thuong'),
    defaultValue: 'da_thuong',
  },
  vai_tro: {
    type: DataTypes.ENUM('khach_hang', 'quan_tri_vien'),
    defaultValue: 'khach_hang',
  },
  trang_thai: {
    type: DataTypes.ENUM('hoat_dong', 'bi_khoa'),
    defaultValue: 'hoat_dong',
  },
  ngay_tao: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
  ngay_cap_nhat: {
    type: DataTypes.DATE,
    allowNull: true,
  },
});

module.exports = NguoiDung;
