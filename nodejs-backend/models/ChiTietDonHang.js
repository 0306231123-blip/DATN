const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const ChiTietDonHang = sequelize.define('chi_tiet_don_hang', {
  ma_chi_tiet: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ma_don_hang: {
    type: DataTypes.INTEGER,
    allowNull: false,
  },
  ma_san_pham: {
    type: DataTypes.INTEGER,
    allowNull: false,
  },
  ten_san_pham: {
    type: DataTypes.STRING(255),
    allowNull: false,
  },
  don_gia: {
    type: DataTypes.DECIMAL(12, 2),
    allowNull: false,
  },
  so_luong: {
    type: DataTypes.INTEGER,
    allowNull: false,
    defaultValue: 1,
  },
  thanh_tien: {
    type: DataTypes.DECIMAL(12, 2),
    allowNull: false,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});

// 💡 Gợi ý thêm: Sau này nếu ông muốn Join bảng Chi tiết đơn hàng với bảng Sản phẩm hoặc Đơn hàng
// thì ông có thể require Model đó vào và nhét lệnh belongsTo ở đây nhé. Ví dụ:
// ChiTietDonHang.belongsTo(DonHang, { foreignKey: 'ma_don_hang' });

module.exports = ChiTietDonHang;