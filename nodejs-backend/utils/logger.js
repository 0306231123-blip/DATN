const LichSuHoatDong = require('../models/LichSuHoatDong');

const logActivity = async (ma_nguoi_dung, loai_hanh_dong, bang_tac_dong, chi_tiet = '') => {
  try {
    await LichSuHoatDong.create({
      ma_nguoi_dung: ma_nguoi_dung || null,
      loai_hanh_dong,
      bang_tac_dong,
      chi_tiet: typeof chi_tiet === 'object' ? JSON.stringify(chi_tiet) : chi_tiet,
    });
  } catch (error) {
    console.error('Error logging activity:', error);
  }
};

module.exports = { logActivity };
