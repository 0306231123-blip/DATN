const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const AnhSanPham = sequelize.define('ANH_SAN_PHAM', {
  ma_anh: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ma_san_pham: {
    type: DataTypes.INTEGER,
    allowNull: false,
  },
  duong_dan_anh: {
    type: DataTypes.STRING(500),
    allowNull: false,
  },
  la_anh_chinh: {
    type: DataTypes.TINYINT,
    defaultValue: 0,
  },
  thu_tu: {
    type: DataTypes.INTEGER,
    defaultValue: 0,
  },
  ngay_tao: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});

module.exports = AnhSanPham;
