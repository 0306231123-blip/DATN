const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const KhuyenMai = sequelize.define('KhuyenMai', {
    ma_khuyen_mai: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
    },
    ma_code: {
        type: DataTypes.STRING(50),
        allowNull: false,
        unique: true
    },
    loai_giam: {
        type: DataTypes.ENUM('phan_tram', 'tien_mat'),
        defaultValue: 'tien_mat'
    },
    gia_tri: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    don_toi_thieu: {
        type: DataTypes.INTEGER,
        defaultValue: 0
    },
    giam_toi_da: {
        type: DataTypes.INTEGER,
        allowNull: true
    },
    so_luong: {
        type: DataTypes.INTEGER,
        defaultValue: 0
    },
    ngay_bat_dau: {
        type: DataTypes.DATE,
        defaultValue: DataTypes.NOW
    },
    ngay_ket_thuc: {
        type: DataTypes.DATE,
        allowNull: false
    },
    trang_thai: {
        type: DataTypes.ENUM('hoat_dong', 'tam_dung'),
        defaultValue: 'hoat_dong'
    }
}, {
    tableName: 'khuyen_mai',
    timestamps: false
});

module.exports = KhuyenMai;