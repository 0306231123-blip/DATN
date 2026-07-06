// Unified logger - delegates to logActivity utility
const { logActivity: _log } = require('./logActivity');

/**
 * @param {number|null} ma_nguoi_dung
 * @param {string} loai_hanh_dong
 * @param {string} bang_tac_dong
 * @param {string|object} chi_tiet
 */
const logActivity = async (ma_nguoi_dung, loai_hanh_dong, bang_tac_dong, chi_tiet = '') => {
  const detail = typeof chi_tiet === 'object' ? JSON.stringify(chi_tiet) : chi_tiet;
  await _log(loai_hanh_dong, bang_tac_dong, detail, ma_nguoi_dung || null);
};

module.exports = { logActivity };
