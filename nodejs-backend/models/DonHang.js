const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const NguoiDung = require('./NguoiDung'); // Nhúng model NguoiDung để làm khóa ngoại

const DonHang = sequelize.define('don_hang', {
  ma_don_hang: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ma_nguoi_dung: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: { // Giữ lại ràng buộc khóa ngoại từ đoạn 1
      model: NguoiDung,
      key: 'ma_nguoi_dung',
    }
  },
  ho_ten_nguoi_nhan: {
    type: DataTypes.STRING(100),
    allowNull: true,
  },
  so_dien_thoai_nhan: {
    type: DataTypes.STRING(15),
    allowNull: true,
  },
  dia_chi_giao: {
    type: DataTypes.STRING(300),
    allowNull: true,
  },
  tong_tien_hang: {
    type: DataTypes.DECIMAL(15, 0),
    allowNull: true,
  },
  phi_van_chuyen: {
    type: DataTypes.DECIMAL(15, 0),
    allowNull: true,
  },
  tong_thanh_toan: {
    type: DataTypes.DECIMAL(15, 0),
    allowNull: false,
  },
  ghi_chu: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  phuong_thuc_thanh_toan: {
    type: DataTypes.ENUM('tien_mat', 'chuyen_khoan', 'vi_dien_tu', 'the_tin_dung'),
    defaultValue: 'tien_mat',
  },
  trang_thai_thanh_toan: {
    type: DataTypes.ENUM('chua_thanh_toan', 'da_thanh_toan'),
    defaultValue: 'chua_thanh_toan',
  },
  trang_thai_don: {
    type: DataTypes.ENUM('cho_xac_nhan', 'da_xac_nhan', 'dang_giao', 'giao_thanh_cong', 'da_huy', 'dang_tra_hang', 'da_tra_hang', 'tra_hang_hoan_tien', 'hoan_thanh'),
    defaultValue: 'cho_xac_nhan',
  },
  // THÊM CỘT NÀY VÀO DƯỚI CÙNG
  ly_do_tra_hang: {
    type: DataTypes.TEXT,
    allowNull: true
  },
  ma_khuyen_mai: {
    type: DataTypes.INTEGER,
    allowNull: true
  },
  so_tien_giam: {
    type: DataTypes.INTEGER,
    defaultValue: 0
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

module.exports = DonHang;