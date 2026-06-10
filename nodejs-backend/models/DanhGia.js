const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const DanhGia = sequelize.define('danh_gia', {
    ma_danh_gia: { 
        type: DataTypes.INTEGER, 
        primaryKey: true, 
        autoIncrement: true 
    },
    ma_nguoi_dung: { 
        type: DataTypes.INTEGER, 
        allowNull: false 
    },
    ma_san_pham: { 
        type: DataTypes.INTEGER, 
        allowNull: false 
    },
    ma_don_hang: { 
        type: DataTypes.INTEGER,
        allowNull: true // Cho phép null nếu chưa cần ép buộc phải mua hàng mới được đánh giá
    },
    diem_so: { 
        type: DataTypes.INTEGER, 
        allowNull: false 
    },
    noi_dung: { 
        type: DataTypes.TEXT 
    },
    trang_thai: { 
        type: DataTypes.STRING, 
        defaultValue: 'hien_thi' // Hoặc 'cho_duyet' nếu ông muốn kiểm duyệt
    },
    ngay_viet: { 
        type: DataTypes.DATE, 
        defaultValue: DataTypes.NOW 
    }
}, { 
    timestamps: false, 
    freezeTableName: true 
});

module.exports = DanhGia;