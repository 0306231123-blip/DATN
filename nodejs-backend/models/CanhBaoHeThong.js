const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const CanhBaoHeThong = sequelize.define('canh_bao_he_thong', {
  ma_canh_bao: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ma_nguoi_dung: {
    type: DataTypes.INTEGER,
    allowNull: true,
  },
  loai_canh_bao: {
    type: DataTypes.STRING(50),
    allowNull: true,
  },
  noi_dung: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  da_doc: {
    type: DataTypes.BOOLEAN,
    defaultValue: false,
  },
  ngay_tao: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
}, {
  timestamps: false,
  freezeTableName: true,
});

module.exports = CanhBaoHeThong;
