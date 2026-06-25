const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const NhaCungCap = sequelize.define('nha_cung_cap', {
  ma_nha_cung_cap: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ten_nha_cung_cap: {
    type: DataTypes.STRING(255),
    allowNull: false,
  },
  so_dien_thoai: {
    type: DataTypes.STRING(20),
    allowNull: true,
  },
  dia_chi: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  email: {
    type: DataTypes.STRING(150),
    allowNull: true,
  },
  ngay_tao: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});

module.exports = NhaCungCap;
