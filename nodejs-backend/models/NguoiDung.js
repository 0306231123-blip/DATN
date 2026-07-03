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
    unique: 'unique_email_index',
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
  ly_do_khoa: {
    type: DataTypes.STRING(255),
    allowNull: true,
  },
  so_lan_dang_nhap_sai: {
    type: DataTypes.INTEGER,
    defaultValue: 0,
  },
  thoi_gian_sai_cuoi: {
    type: DataTypes.DATE,
    allowNull: true,
  },
  thoi_gian_khoa_tam_thoi: {
    type: DataTypes.DATE,
    allowNull: true,
  },
  khoa_mua_hang_den: {
    type: DataTypes.DATE,
    allowNull: true,
  },
  ngay_tao: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
  ngay_cap_nhat: {
    type: DataTypes.DATE,
    allowNull: true,
  },
  ngan_hang: { type: DataTypes.STRING(100) },
so_tai_khoan: { type: DataTypes.STRING(50) },
chu_tai_khoan: { type: DataTypes.STRING(100) },
});

module.exports = NguoiDung;
