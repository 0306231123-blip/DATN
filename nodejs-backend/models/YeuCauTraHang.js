const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const DonHang = require('./DonHang');

const YeuCauTraHang = sequelize.define('yeu_cau_tra_hang', {
  ma_yeu_cau: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ma_don_hang: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: DonHang,
      key: 'ma_don_hang',
    }
  },
  ly_do: {
    type: DataTypes.STRING(255),
    allowNull: false,
  },
  hinh_anh_bang_chung: {
    type: DataTypes.JSON, // Có thể chứa mảng các URL hình ảnh/video
    allowNull: true,
  },
  ghi_chu_khach_hang: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  trang_thai: {
    type: DataTypes.ENUM('cho_duyet', 'da_duyet', 'tu_choi'),
    defaultValue: 'cho_duyet',
  },
  // Thêm vào bên dưới các trường đã có
ngan_hang: {
    type: DataTypes.STRING(100),
    allowNull: true
},
so_tai_khoan: {
    type: DataTypes.STRING(50),
    allowNull: true
},
chu_tai_khoan: {
    type: DataTypes.STRING(100),
    allowNull: true
},
  ngay_yeu_cau: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
  ngay_xu_ly: {
    type: DataTypes.DATE,
    allowNull: true,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});

module.exports = YeuCauTraHang;
