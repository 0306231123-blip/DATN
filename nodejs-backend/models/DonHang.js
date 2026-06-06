const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const DonHang = sequelize.define('don_hang', {
    ma_don_hang: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    ma_nguoi_dung: { type: DataTypes.INTEGER, allowNull: false },
    ho_ten_nguoi_nhan: { type: DataTypes.STRING }, 
    so_dien_thoai_nhan: { type: DataTypes.STRING }, 
    dia_chi_giao: { type: DataTypes.STRING, allowNull: false }, // Đã sửa theo DB
    tong_tien_hang: { type: DataTypes.DECIMAL(10, 0) }, 
    phi_van_chuyen: { type: DataTypes.DECIMAL(10, 0), defaultValue: 0 },
    tong_thanh_toan: { type: DataTypes.DECIMAL(10, 0), allowNull: false }, // Đã sửa theo DB
    ghi_chu: { type: DataTypes.STRING },
    phuong_thuc_thanh_toan: { type: DataTypes.STRING },
    trang_thai_thanh_toan: { type: DataTypes.STRING, defaultValue: 'chua_thanh_toan' },
    trang_thai_don: { type: DataTypes.STRING, defaultValue: 'cho_xac_nhan' }, // Đã sửa theo DB
    ngay_dat: { type: DataTypes.DATE, defaultValue: DataTypes.NOW },
    ngay_cap_nhat: { type: DataTypes.DATE }
}, { timestamps: false, freezeTableName: true });

module.exports = DonHang;