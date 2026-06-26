const { QueryTypes } = require('sequelize');
const sequelize = require('../config/database');

/**
 * Get user statistics (total orders and total spent)
 * Using optimized query instead of N+1
 */
async function getUsersStats(userIds) {
  if (!userIds || userIds.length === 0) return {};

  const placeholders = userIds.map(() => '?').join(',');
  
  const query = `
    SELECT 
      ma_nguoi_dung,
      COUNT(*) as tong_don,
      COALESCE(SUM(tong_thanh_toan), 0) as tong_chi,
      SUM(CASE WHEN trang_thai_don = 'da_huy' THEN 1 ELSE 0 END) as so_don_huy
    FROM don_hang
    WHERE ma_nguoi_dung IN (${placeholders})
    GROUP BY ma_nguoi_dung
  `;

  const stats = await sequelize.query(query, {
    replacements: userIds,
    type: QueryTypes.SELECT,
  });

  // Convert array to object for easy lookup
  const statsMap = {};
  stats.forEach(stat => {
    statsMap[stat.ma_nguoi_dung] = {
      tong_don: parseInt(stat.tong_don) || 0,
      tong_chi: parseFloat(stat.tong_chi) || 0,
      so_don_huy: parseInt(stat.so_don_huy) || 0,
    };
  });

  return statsMap;
}

/**
 * Get stats for a single user
 */
async function getUserStats(userId) {
  const statsMap = await getUsersStats([userId]);
  return statsMap[userId] || { tong_don: 0, tong_chi: 0, so_don_huy: 0 };
}

module.exports = {
  getUsersStats,
  getUserStats,
};
