// Tạo file mới: models/AnhSanPham.js
const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const AnhSanPham = sequelize.define('anh_san_pham', {
    ma_anh: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    ma_san_pham: { type: DataTypes.INTEGER, allowNull: false },
    duong_dan_anh: { type: DataTypes.STRING, allowNull: false },
    la_anh_chinh: { type: DataTypes.INTEGER, defaultValue: 0 },
    thu_tu: { type: DataTypes.INTEGER, defaultValue: 1 },
    ngay_tao: { type: DataTypes.DATE, defaultValue: DataTypes.NOW }
}, { timestamps: false, freezeTableName: true });

module.exports = AnhSanPham;