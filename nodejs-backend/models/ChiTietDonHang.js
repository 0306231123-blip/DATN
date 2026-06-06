const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const ChiTietDonHang = sequelize.define('chi_tiet_don_hang', {
    ma_chi_tiet: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    ma_don_hang: { type: DataTypes.INTEGER, allowNull: false },
    ma_san_pham: { type: DataTypes.INTEGER, allowNull: false },
    ten_san_pham: { type: DataTypes.STRING }, // Thêm theo DB
    don_gia: { type: DataTypes.DECIMAL(10, 0), allowNull: false }, // Đã sửa theo DB
    so_luong: { type: DataTypes.INTEGER, allowNull: false },
    thanh_tien: { type: DataTypes.DECIMAL(10, 0), allowNull: false } // Thêm theo DB
}, { timestamps: false, freezeTableName: true });

module.exports = ChiTietDonHang;