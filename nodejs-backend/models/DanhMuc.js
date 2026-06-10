const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const DanhMuc = sequelize.define('danh_muc', {
  ma_danh_muc: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ten_danh_muc: {
    type: DataTypes.STRING(100),
    allowNull: false,
  },
  mo_ta: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  ma_danh_muc_cha: {
    type: DataTypes.INTEGER,
    allowNull: true,
  },
  thu_tu_hien_thi: {
    type: DataTypes.INTEGER,
    allowNull: true,
  },
  ngay_tao: {
    type: DataTypes.DATE,
    allowNull: true,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});

module.exports = DanhMuc;
