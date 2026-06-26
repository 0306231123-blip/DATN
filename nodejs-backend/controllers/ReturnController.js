const YeuCauTraHang = require('../models/YeuCauTraHang');
const DonHang = require('../models/DonHang');
const sequelize = require('../config/database');

exports.createReturnRequest = async (req, res) => {
  const transaction = await sequelize.transaction();
  try {
    const { ma_don_hang, ly_do, hinh_anh_bang_chung, ghi_chu_khach_hang } = req.body;

    const donHang = await DonHang.findByPk(ma_don_hang, { transaction });
    if (!donHang) {
      await transaction.rollback();
      return res.status(404).json({ status: 'error', message: 'Đơn hàng không tồn tại.' });
    }

    // Only allow returns if order is 'giao_thanh_cong'
    if (donHang.trang_thai_don !== 'giao_thanh_cong') {
      await transaction.rollback();
      return res.status(400).json({ status: 'error', message: 'Chỉ có thể yêu cầu trả hàng cho đơn hàng đã giao thành công.' });
    }

    // Check if request already exists
    const existingReq = await YeuCauTraHang.findOne({ where: { ma_don_hang }, transaction });
    if (existingReq) {
      await transaction.rollback();
      return res.status(400).json({ status: 'error', message: 'Đơn hàng này đã có yêu cầu trả hàng.' });
    }

    const returnReq = await YeuCauTraHang.create({
      ma_don_hang,
      ly_do,
      hinh_anh_bang_chung,
      ghi_chu_khach_hang,
      trang_thai: 'cho_duyet',
    }, { transaction });

    await donHang.update({ trang_thai_don: 'dang_tra_hang', ngay_cap_nhat: new Date() }, { transaction });

    await transaction.commit();

    res.status(201).json({
      status: 'success',
      message: 'Đã gửi yêu cầu trả hàng thành công.',
      data: returnReq,
    });
  } catch (error) {
    await transaction.rollback();
    console.error('Create return request error:', error);
    res.status(500).json({ status: 'error', message: 'Lỗi khi tạo yêu cầu trả hàng.', error: error.message });
  }
};

exports.updateReturnRequestStatus = async (req, res) => {
  const transaction = await sequelize.transaction();
  try {
    const { id } = req.params;
    const { trang_thai } = req.body;

    if (!['da_duyet', 'tu_choi'].includes(trang_thai)) {
      await transaction.rollback();
      return res.status(400).json({ status: 'error', message: 'Trạng thái không hợp lệ.' });
    }

    const returnReq = await YeuCauTraHang.findByPk(id, { transaction });
    if (!returnReq) {
      await transaction.rollback();
      return res.status(404).json({ status: 'error', message: 'Yêu cầu trả hàng không tồn tại.' });
    }

    if (returnReq.trang_thai !== 'cho_duyet') {
      await transaction.rollback();
      return res.status(400).json({ status: 'error', message: 'Yêu cầu này đã được xử lý.' });
    }

    const donHang = await DonHang.findByPk(returnReq.ma_don_hang, { transaction });

    await returnReq.update({
      trang_thai,
      ngay_xu_ly: new Date()
    }, { transaction });

    if (trang_thai === 'da_duyet') {
      await donHang.update({ trang_thai_don: 'da_tra_hang', ngay_cap_nhat: new Date() }, { transaction });
    } else if (trang_thai === 'tu_choi') {
      await donHang.update({ trang_thai_don: 'giao_thanh_cong', ngay_cap_nhat: new Date() }, { transaction });
    }

    await transaction.commit();

    res.status(200).json({
      status: 'success',
      message: 'Cập nhật trạng thái yêu cầu trả hàng thành công.',
      data: returnReq,
    });
  } catch (error) {
    await transaction.rollback();
    console.error('Update return request status error:', error);
    res.status(500).json({ status: 'error', message: 'Lỗi khi cập nhật trạng thái yêu cầu trả hàng.', error: error.message });
  }
};
