const sequelize = require('../config/database');
const { QueryTypes } = require('sequelize');

/**
 * Ghi log hoạt động vào bảng lich_su_hoat_dong
 * @param {string} action - VD: 'THÊM', 'CẬP NHẬT', 'XÓA', 'DUYỆT', 'HỦY'
 * @param {string} table  - Tên bảng bị tác động (VD: 'san_pham', 'don_hang')
 * @param {string} detail - Mô tả chi tiết (VD: 'Sản phẩm #5 - Kem dưỡng da')
 * @param {number|null} userId - ma_nguoi_dung của người thực hiện
 */
async function logActivity(action, table, detail, userId = null) {
  try {
    await sequelize.query(
      `INSERT INTO lich_su_hoat_dong (ma_nguoi_dung, loai_hanh_dong, bang_tac_dong, chi_tiet, thoi_gian)
       VALUES (:userId, :action, :table, :detail, NOW())`,
      {
        replacements: { userId, action, table, detail },
        type: QueryTypes.INSERT,
      }
    );
  } catch (err) {
    // Không để lỗi log làm hỏng luồng chính
    console.warn('[logActivity] Could not write log:', err.message);
  }
}

module.exports = { logActivity };
