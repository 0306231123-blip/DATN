const LichSuHoatDong = require('../models/LichSuHoatDong');
const NguoiDung = require('../models/NguoiDung');

// Define association here if not globally defined
LichSuHoatDong.belongsTo(NguoiDung, { foreignKey: 'ma_nguoi_dung', as: 'nguoi_thuc_hien' });

// Map action types to icons and colors
const ACTION_META = {
  'TH\u00caM':                  { icon: '\u2795', color: '#16a34a' },
  'C\u1eacP NH\u1eacT':              { icon: '\u270f\ufe0f', color: '#2563eb' },
  'X\u00d3A':                  { icon: '\ud83d\uddd1\ufe0f', color: '#dc2626' },
  'KH\u00d3A':                 { icon: '\ud83d\udd12', color: '#d97706' },
  'M\u1ede KH\u00d3A':              { icon: '\ud83d\udd13', color: '#16a34a' },
  'C\u1eacP NH\u1eacT TR\u1ea0NG TH\u00c1I': { icon: '\ud83d\udce6', color: '#7c3aed' },
  'Th\u00eam':                 { icon: '\u2795', color: '#16a34a' },
  'X\u00f3a':                  { icon: '\ud83d\uddd1\ufe0f', color: '#dc2626' },
  'C\u1eadp nh\u1eadt':            { icon: '\u270f\ufe0f', color: '#2563eb' },
};

function getMeta(action) {
  return ACTION_META[action] || { icon: '\ud83d\udcd3', color: '#6b7280' };
}

class NotificationController {
  // GET /api/notifications/recent
  async getRecent(req, res) {
    try {
      const logs = await LichSuHoatDong.findAll({
        order: [['thoi_gian', 'DESC']],
        limit: 30,
        include: [
          {
            model: NguoiDung,
            as: 'nguoi_thuc_hien',
            attributes: ['ma_nguoi_dung', 'ho_ten', 'vai_tro'],
            required: false,
          },
        ],
      });

      const enriched = logs.map(log => {
        const meta = getMeta(log.loai_hanh_dong);
        const user = log.nguoi_thuc_hien ? log.nguoi_thuc_hien.ho_ten : 'H\u1ec7 th\u1ed1ng';
        return {
          ...log.dataValues,
          icon: meta.icon,
          color: meta.color,
          display_user: user,
          display_action: log.loai_hanh_dong,
          display_detail: log.chi_tiet || '',
        };
      });

      res.json({
        status: 'success',
        data: enriched,
        total: enriched.length,
      });
    } catch (error) {
      console.error('Error fetching notifications:', error);
      res.status(500).json({ status: 'error', message: 'Kh\u00f4ng th\u1ec3 t\u1ea3i th\u00f4ng b\u00e1o.' });
    }
  }

  // POST /api/notifications/log
  async logEvent(req, res) {
    try {
      const { loai_hanh_dong, bang_tac_dong, chi_tiet } = req.body;
      await LichSuHoatDong.create({
        ma_nguoi_dung: req.user ? req.user.ma_nguoi_dung : null,
        loai_hanh_dong,
        bang_tac_dong,
        chi_tiet,
      });
      res.json({ status: 'success' });
    } catch (error) {
      res.status(500).json({ status: 'error', message: error.message });
    }
  }
}

module.exports = new NotificationController();
