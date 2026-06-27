const express = require('express');
const LichSuHoatDong = require('../models/LichSuHoatDong');
const NguoiDung = require('../models/NguoiDung');
const { verifyToken, requireAdmin } = require('../middleware/verifyToken');

const router = express.Router();

// Define association here if not globally defined
LichSuHoatDong.belongsTo(NguoiDung, { foreignKey: 'ma_nguoi_dung', as: 'nguoi_thuc_hien' });

router.use(verifyToken, requireAdmin);

router.get('/recent', async (req, res) => {
  try {
    const logs = await LichSuHoatDong.findAll({
      order: [['thoi_gian', 'DESC']],
      limit: 20,
      include: [
        {
          model: NguoiDung,
          as: 'nguoi_thuc_hien',
          attributes: ['ma_nguoi_dung', 'ho_ten', 'vai_tro'],
        },
      ],
    });

    res.json({
      status: 'success',
      data: logs,
    });
  } catch (error) {
    console.error('Error fetching notifications:', error);
    res.status(500).json({ status: 'error', message: 'Không thể tải thông báo.' });
  }
});

// A helper endpoint to log a custom event (can be used by other parts of the app)
router.post('/log', async (req, res) => {
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
});

module.exports = router;
