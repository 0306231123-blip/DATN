const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');
const NguoiDung = require('./NguoiDung');

const LichSuHoatDong = sequelize.define('lich_su_hoat_dong', {
  ma_hoat_dong: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ma_nguoi_dung: {
    type: DataTypes.INTEGER,
    allowNull: true,
  },
  loai_hanh_dong: {
    type: DataTypes.STRING(50),
    allowNull: false,
  },
  bang_tac_dong: {
    type: DataTypes.STRING(50),
    allowNull: false,
  },
  chi_tiet: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  thoi_gian: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});

module.exports = LichSuHoatDong;
