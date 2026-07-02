const { DataTypes } = require('sequelize');
const sequelize = require('../config/database');

const TinNhan = sequelize.define('tin_nhan', {
  ma_tin_nhan: {
    type: DataTypes.INTEGER,
    primaryKey: true,
    autoIncrement: true,
  },
  ma_nguoi_dung: {
    type: DataTypes.INTEGER,
    allowNull: false,
    references: {
      model: 'nguoi_dung', 
      key: 'ma_nguoi_dung'
    }
  },
  is_from_admin: {
    type: DataTypes.BOOLEAN,
    defaultValue: false,
  },
  noi_dung: {
    type: DataTypes.TEXT,
    allowNull: true,
  },
  hinh_anh: {
    type: DataTypes.STRING(255),
    allowNull: true,
  },
  ngay_gui: {
    type: DataTypes.DATE,
    defaultValue: DataTypes.NOW,
  },
}, {
  tableName: 'tin_nhan',
  timestamps: false
});

module.exports = TinNhan;
