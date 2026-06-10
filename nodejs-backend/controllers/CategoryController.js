const DanhMuc = require('../models/DanhMuc');
const { Op } = require('sequelize');

class CategoryController {
  async getAll(req, res) {
    try {
      const { page = 1, limit = 10, search = '' } = req.query;
      const offset = (page - 1) * limit;

      const where = {};
      if (search) {
        where.ten_danh_muc = { [Op.like]: `%${search}%` };
      }

      const { count, rows } = await DanhMuc.findAndCountAll({
        where,
        limit: parseInt(limit),
        offset,
        order: [['thu_tu_hien_thi', 'ASC'], ['ngay_tao', 'DESC']],
      });

      res.json({
        status: 'success',
        data: rows,
        pagination: {
          total: count,
          page: parseInt(page),
          limit: parseInt(limit),
          pages: Math.ceil(count / limit),
        },
      });
    } catch (error) {
      res.status(500).json({ status: 'error', message: error.message });
    }
  }

  async getById(req, res) {
    try {
      const { id } = req.params;
      const category = await DanhMuc.findByPk(id);

      if (!category) {
        return res.status(404).json({ status: 'error', message: 'Danh mục không tồn tại' });
      }

      res.json({ status: 'success', data: category });
    } catch (error) {
      res.status(500).json({ status: 'error', message: error.message });
    }
  }

  async create(req, res) {
    try {
      const { ten_danh_muc, mo_ta, ma_danh_muc_cha, thu_tu_hien_thi } = req.body;

      if (!ten_danh_muc) {
        return res.status(400).json({ status: 'error', message: 'Tên danh mục không được để trống' });
      }

      const existing = await DanhMuc.findOne({ where: { ten_danh_muc } });
      if (existing) {
        return res.status(400).json({ status: 'error', message: 'Danh mục này đã tồn tại' });
      }

      const category = await DanhMuc.create({
        ten_danh_muc,
        mo_ta: mo_ta || null,
        ma_danh_muc_cha: ma_danh_muc_cha || null,
        thu_tu_hien_thi: thu_tu_hien_thi || null,
      });

      res.status(201).json({ status: 'success', data: category, message: 'Tạo danh mục thành công' });
    } catch (error) {
      res.status(500).json({ status: 'error', message: error.message });
    }
  }

  async update(req, res) {
    try {
      const { id } = req.params;
      const { ten_danh_muc, mo_ta, ma_danh_muc_cha, thu_tu_hien_thi } = req.body;

      const category = await DanhMuc.findByPk(id);
      if (!category) {
        return res.status(404).json({ status: 'error', message: 'Danh mục không tồn tại' });
      }

      if (ten_danh_muc) {
        const existing = await DanhMuc.findOne({
          where: { ten_danh_muc, ma_danh_muc: { [Op.ne]: id } },
        });
        if (existing) {
          return res.status(400).json({ status: 'error', message: 'Tên danh mục này đã tồn tại' });
        }
      }

      await category.update({
        ten_danh_muc: ten_danh_muc || category.ten_danh_muc,
        mo_ta: mo_ta !== undefined ? mo_ta : category.mo_ta,
        ma_danh_muc_cha: ma_danh_muc_cha !== undefined ? ma_danh_muc_cha : category.ma_danh_muc_cha,
        thu_tu_hien_thi: thu_tu_hien_thi !== undefined ? thu_tu_hien_thi : category.thu_tu_hien_thi,
      });

      res.json({ status: 'success', data: category, message: 'Cập nhật danh mục thành công' });
    } catch (error) {
      res.status(500).json({ status: 'error', message: error.message });
    }
  }

  async delete(req, res) {
    try {
      const { id } = req.params;

      const category = await DanhMuc.findByPk(id);
      if (!category) {
        return res.status(404).json({ status: 'error', message: 'Danh mục không tồn tại' });
      }

      await category.destroy();
      res.json({ status: 'success', message: 'Xóa danh mục thành công' });
    } catch (error) {
      res.status(500).json({ status: 'error', message: error.message });
    }
  }

  async getStats(req, res) {
    try {
      const total = await DanhMuc.count();

      res.json({
        status: 'success',
        data: {
          total,
        },
      });
    } catch (error) {
      res.status(500).json({ status: 'error', message: error.message });
    }
  }
}

module.exports = new CategoryController();
