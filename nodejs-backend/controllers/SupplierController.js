const NhaCungCap = require('../models/NhaCungCap');

exports.getAllSuppliers = async (req, res) => {
  try {
    const suppliers = await NhaCungCap.findAll({
      order: [['ngay_tao', 'DESC']]
    });
    res.json({ status: 'success', data: suppliers });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
};

exports.createSupplier = async (req, res) => {
  try {
    const { ten_nha_cung_cap, so_dien_thoai, dia_chi, email } = req.body;
    if (!ten_nha_cung_cap) {
      return res.status(400).json({ status: 'error', message: 'Tên nhà cung cấp là bắt buộc.' });
    }
    const supplier = await NhaCungCap.create({ ten_nha_cung_cap, so_dien_thoai, dia_chi, email });
    res.status(201).json({ status: 'success', data: supplier });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
};

exports.updateSupplier = async (req, res) => {
  try {
    const { id } = req.params;
    const { ten_nha_cung_cap, so_dien_thoai, dia_chi, email } = req.body;
    const supplier = await NhaCungCap.findByPk(id);
    if (!supplier) return res.status(404).json({ status: 'error', message: 'Nhà cung cấp không tồn tại.' });

    await supplier.update({ ten_nha_cung_cap, so_dien_thoai, dia_chi, email });
    res.json({ status: 'success', data: supplier });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
};

exports.deleteSupplier = async (req, res) => {
  try {
    const { id } = req.params;
    const supplier = await NhaCungCap.findByPk(id);
    if (!supplier) return res.status(404).json({ status: 'error', message: 'Nhà cung cấp không tồn tại.' });

    await supplier.destroy();
    res.json({ status: 'success', message: 'Xóa nhà cung cấp thành công.' });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
};
