const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const YeuCauTraHang = sequelize.define('YeuCauTraHang', {
    ma_yeu_cau: {
        type: DataTypes.INTEGER,
        primaryKey: true,
        autoIncrement: true
    },
    ma_don_hang: {
        type: DataTypes.INTEGER,
        allowNull: false
    },
    ly_do: {
        type: DataTypes.TEXT,
        allowNull: false
    },
    hinh_anh_bang_chung: {
        type: DataTypes.STRING,
        allowNull: true
    },
    ghi_chu_khach_hang: {
        type: DataTypes.TEXT,
        allowNull: true
    },
    trang_thai: {
        type: DataTypes.STRING,
        defaultValue: 'cho_xu_ly' // Các trạng thái: cho_xu_ly, da_duyet, tu_choi
    },
    ngay_yeu_cau: {
        type: DataTypes.DATE,
        defaultValue: DataTypes.NOW
    },
    ngay_xu_ly: {
        type: DataTypes.DATE,
        allowNull: true
    }
}, {
    tableName: 'yeu_cau_tra_hang',
    timestamps: false // Không dùng createdAt/updatedAt mặc định
});

module.exports = YeuCauTraHang;