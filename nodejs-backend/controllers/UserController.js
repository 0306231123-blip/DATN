const NguoiDung = require('../models/NguoiDung');
const bcrypt = require('bcryptjs');
const { Op, QueryTypes } = require('sequelize');
const sequelize = require('../config/database');
const { getUsersStats, getUserStats } = require('../utils/userStats');

/**
 * GET /api/users
 * Lấy danh sách người dùng với search, filter, pagination
 */
exports.getAllUsers = async (req, res) => {
  try {
    const { search, vai_tro, per_page = 15, page = 1 } = req.query;
    
    // Validate pagination parameters
    const pageNum = Math.max(1, parseInt(page) || 1);
    const perPageNum = Math.max(1, Math.min(100, parseInt(per_page) || 15));
    const offset = (pageNum - 1) * perPageNum;

    let where = {};

    // Search by name or email
    if (search && typeof search === 'string' && search.trim()) {
      where[Op.or] = [
        { ho_ten: { [Op.like]: `%${search}%` } },
        { email: { [Op.like]: `%${search}%` } },
      ];
    }

    // Filter by role
    if (vai_tro && vai_tro !== 'all' && ['khach_hang', 'quan_tri_vien'].includes(vai_tro)) {
      where.vai_tro = vai_tro;
    }

    const { count, rows } = await NguoiDung.findAndCountAll({
      where,
      limit: perPageNum,
      offset: offset,
      order: [['ngay_tao', 'DESC']],
      attributes: { exclude: ['mat_khau'] },
    });

    // Get all stats in one query (avoid N+1)
    const userIds = rows.map(u => u.ma_nguoi_dung);
    const statsMap = await getUsersStats(userIds);

    // Merge stats with users
    const usersWithStats = rows.map(user => ({
      ...user.dataValues,
      tong_don: statsMap[user.ma_nguoi_dung]?.tong_don || 0,
      tong_chi: statsMap[user.ma_nguoi_dung]?.tong_chi || 0,
      so_don_huy: statsMap[user.ma_nguoi_dung]?.so_don_huy || 0,
    }));

    res.status(200).json({
      success: true,
      data: usersWithStats,
      pagination: {
        total: count,
        per_page: perPageNum,
        current_page: pageNum,
        last_page: Math.ceil(count / perPageNum),
      },
    });
  } catch (error) {
    console.error('Get users error:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi khi lấy danh sách người dùng.',
      error: error.message,
    });
  }
};

/**
 * GET /api/users/:id
 * Lấy chi tiết một người dùng
 */
exports.getUserById = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Validate ID
    const userId = parseInt(id);
    if (isNaN(userId) || userId <= 0) {
      return res.status(400).json({
        success: false,
        message: 'ID người dùng không hợp lệ.',
      });
    }

    const user = await NguoiDung.findByPk(userId, {
      attributes: { exclude: ['mat_khau'] },
    });

    if (!user) {
      return res.status(404).json({
        success: false,
        message: 'Người dùng không tồn tại.',
      });
    }

    // Get user stats
    const stats = await getUserStats(userId);

    const userWithStats = {
      ...user.dataValues,
      tong_don: stats.tong_don,
      tong_chi: stats.tong_chi,
      so_don_huy: stats.so_don_huy,
    };

    res.status(200).json({
      success: true,
      data: userWithStats,
    });
  } catch (error) {
    console.error('Get user error:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi khi lấy thông tin người dùng.',
      error: error.message,
    });
  }
};

/**
 * POST /api/users
 * Tạo người dùng mới
 */
exports.createUser = async (req, res) => {
  try {
    const { ho_ten, email, so_dien_thoai, dia_chi, vai_tro, password } = req.body;

    // Validate required fields (middleware handles main validation)
    if (!ho_ten || !email || !password) {
      return res.status(400).json({
        success: false,
        message: 'Vui lòng nhập đầy đủ họ tên, email và mật khẩu.',
      });
    }

    // Check if email already exists
    const existingUser = await NguoiDung.findOne({ where: { email } });
    if (existingUser) {
      return res.status(409).json({
        success: false,
        message: 'Email này đã tồn tại trong hệ thống.',
      });
    }

    // Check if phone number already exists
    if (so_dien_thoai) {
      const existingPhone = await NguoiDung.findOne({ where: { so_dien_thoai } });
      if (existingPhone) {
        return res.status(409).json({
          success: false,
          message: 'Số điện thoại này đã tồn tại trong hệ thống.',
        });
      }
    }

    // Hash password
    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash(password, salt);

    // Create user
    const user = await NguoiDung.create({
      ho_ten,
      email,
      mat_khau: hashedPassword,
      so_dien_thoai: so_dien_thoai || null,
      dia_chi: dia_chi || null,
      vai_tro: vai_tro || 'khach_hang',
      trang_thai: 'hoat_dong',
    });

    res.status(201).json({
      success: true,
      message: 'Người dùng được tạo thành công.',
      data: {
        ma_nguoi_dung: user.ma_nguoi_dung,
        ho_ten: user.ho_ten,
        email: user.email,
        vai_tro: user.vai_tro,
      },
    });
  } catch (error) {
    console.error('Create user error:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi khi tạo người dùng.',
      error: error.message,
    });
  }
};

/**
 * PUT /api/users/:id
 * Cập nhật người dùng
 */
exports.updateUser = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Validate ID
    const userId = parseInt(id);
    if (isNaN(userId) || userId <= 0) {
      return res.status(400).json({
        success: false,
        message: 'ID người dùng không hợp lệ.',
      });
    }

    const { ho_ten, email, so_dien_thoai, dia_chi, vai_tro, password, trang_thai } = req.body;

    const user = await NguoiDung.findByPk(userId);
    if (!user) {
      return res.status(404).json({
        success: false,
        message: 'Người dùng không tồn tại.',
      });
    }

    // Check if email is unique (excluding current user)
    if (email && email !== user.email) {
      const existingUser = await NguoiDung.findOne({ where: { email } });
      if (existingUser) {
        return res.status(409).json({
          success: false,
          message: 'Email này đã tồn tại trong hệ thống.',
        });
      }
    }

    // Check if phone number is unique (excluding current user)
    if (so_dien_thoai && so_dien_thoai !== user.so_dien_thoai) {
      const existingPhone = await NguoiDung.findOne({ where: { so_dien_thoai } });
      if (existingPhone) {
        return res.status(409).json({
          success: false,
          message: 'Số điện thoại này đã tồn tại trong hệ thống.',
        });
      }
    }

    // Prepare update data - only update provided fields
    const updateData = {};
    if (ho_ten !== undefined) updateData.ho_ten = ho_ten;
    if (email !== undefined) updateData.email = email;
    if (so_dien_thoai !== undefined) updateData.so_dien_thoai = so_dien_thoai;
    if (dia_chi !== undefined) updateData.dia_chi = dia_chi;
    if (vai_tro !== undefined && ['khach_hang', 'quan_tri_vien'].includes(vai_tro)) {
      updateData.vai_tro = vai_tro;
    }
    if (trang_thai !== undefined && ['hoat_dong', 'bi_khoa'].includes(trang_thai)) {
      updateData.trang_thai = trang_thai;
    }

    // Hash new password if provided
    if (password) {
      if (password.length < 6) {
        return res.status(400).json({
          success: false,
          message: 'Mật khẩu phải có ít nhất 6 ký tự.',
        });
      }
      const salt = await bcrypt.genSalt(10);
      updateData.mat_khau = await bcrypt.hash(password, salt);
    }

    updateData.ngay_cap_nhat = new Date();

    await user.update(updateData);

    res.status(200).json({
      success: true,
      message: 'Cập nhật người dùng thành công.',
      data: {
        ma_nguoi_dung: user.ma_nguoi_dung,
        ho_ten: user.ho_ten,
        email: user.email,
        vai_tro: user.vai_tro,
      },
    });
  } catch (error) {
    console.error('Update user error:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi khi cập nhật người dùng.',
      error: error.message,
    });
  }
};

/**
 * DELETE /api/users/:id
 * Xóa người dùng
 */
exports.deleteUser = async (req, res) => {
  try {
    const { id } = req.params;
    
    // Validate ID
    const userId = parseInt(id);
    if (isNaN(userId) || userId <= 0) {
      return res.status(400).json({
        success: false,
        message: 'ID người dùng không hợp lệ.',
      });
    }

    const user = await NguoiDung.findByPk(userId);
    if (!user) {
      return res.status(404).json({
        success: false,
        message: 'Người dùng không tồn tại.',
      });
    }

    await user.destroy();

    res.status(200).json({
      success: true,
      message: 'Xóa người dùng thành công.',
    });
  } catch (error) {
    console.error('Delete user error:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi khi xóa người dùng.',
      error: error.message,
    });
  }
};

/**
 * GET /api/users/export/csv
 * Xuất danh sách người dùng ra CSV
 */
exports.exportUsers = async (req, res) => {
  try {
    const { search, vai_tro } = req.query;

    let where = {};

    if (search && typeof search === 'string' && search.trim()) {
      where[Op.or] = [
        { ho_ten: { [Op.like]: `%${search}%` } },
        { email: { [Op.like]: `%${search}%` } },
      ];
    }

    if (vai_tro && vai_tro !== 'all' && ['khach_hang', 'quan_tri_vien'].includes(vai_tro)) {
      where.vai_tro = vai_tro;
    }

    const users = await NguoiDung.findAll({
      where,
      order: [['ngay_tao', 'DESC']],
      attributes: { exclude: ['mat_khau'] },
    });

    // Get all stats in one query
    const userIds = users.map(u => u.ma_nguoi_dung);
    const statsMap = await getUsersStats(userIds);

    // Create CSV content
    let csv = 'Mã,Tên,Email,Điện thoại,Địa chỉ,Vai trò,Tổng đơn,Tổng chi,Ngày tạo\n';

    users.forEach(user => {
      const stats = statsMap[user.ma_nguoi_dung] || { tong_don: 0, tong_chi: 0 };
      const row = [
        user.ma_nguoi_dung,
        `"${user.ho_ten}"`,
        `"${user.email}"`,
        `"${user.so_dien_thoai || ''}"`,
        `"${user.dia_chi || ''}"`,
        user.vai_tro,
        stats.tong_don,
        stats.tong_chi,
        user.ngay_tao,
      ];
      csv += row.join(',') + '\n';
    });

    res.setHeader('Content-Type', 'text/csv; charset=utf-8');
    res.setHeader('Content-Disposition', `attachment; filename="users_${Date.now()}.csv"`);
    res.send(csv);
  } catch (error) {
    console.error('Export users error:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi khi xuất danh sách người dùng.',
      error: error.message,
    });
  }
};

/**
 * GET /api/users/statistics/overview
 * Lấy thống kê người dùng
 */
exports.getStatistics = async (req, res) => {
  try {
    console.log('DEBUG: getStatistics called');
    const totalUsers = await NguoiDung.count();

    const customersCount = await NguoiDung.count({
      where: { vai_tro: 'khach_hang' },
    });

    const adminsCount = await NguoiDung.count({
      where: { vai_tro: 'quan_tri_vien' },
    });

    res.status(200).json({
      status: 'success',
      data: {
        total: totalUsers,
        customers: customersCount,
        admins: adminsCount,
      },
    });
  } catch (error) {
    console.error('Get statistics error:', error);
    res.status(500).json({
      status: 'error',
      message: 'Lỗi khi lấy thống kê.',
      error: error.message,
    });
  }
};
