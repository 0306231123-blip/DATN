/**
 * Middleware for user validation
 */

/**
 * Validate query parameters for list endpoints
 */
const validateListQuery = (req, res, next) => {
  const { per_page, page } = req.query;

  // Validate per_page if provided
  if (per_page !== undefined) {
    const perPageNum = parseInt(per_page);
    if (isNaN(perPageNum) || perPageNum < 1 || perPageNum > 100) {
      return res.status(400).json({
        success: false,
        message: 'Số mục trên trang phải là từ 1 đến 100.',
      });
    }
  }

  // Validate page if provided
  if (page !== undefined) {
    const pageNum = parseInt(page);
    if (isNaN(pageNum) || pageNum < 1) {
      return res.status(400).json({
        success: false,
        message: 'Số trang phải là một số dương.',
      });
    }
  }

  next();
};

const validateUserCreate = (req, res, next) => {
  const { ho_ten, email, password, so_dien_thoai, dia_chi, vai_tro } = req.body;

  // Check required fields
  if (!ho_ten || !email || !password) {
    return res.status(400).json({
      success: false,
      message: 'Vui lòng nhập đầy đủ họ tên, email và mật khẩu.',
    });
  }

  // Validate email format
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    return res.status(400).json({
      success: false,
      message: 'Email không hợp lệ.',
    });
  }

  // Validate password length
  if (password.length < 6) {
    return res.status(400).json({
      success: false,
      message: 'Mật khẩu phải có ít nhất 6 ký tự.',
    });
  }

  // Validate role
  if (vai_tro && !['khach_hang', 'quan_tri_vien'].includes(vai_tro)) {
    return res.status(400).json({
      success: false,
      message: 'Vai trò không hợp lệ.',
    });
  }

  // Validate phone if provided
  if (so_dien_thoai && !/^\d{10,15}$/.test(so_dien_thoai.replace(/\s/g, ''))) {
    return res.status(400).json({
      success: false,
      message: 'Số điện thoại không hợp lệ (10-15 chữ số).',
    });
  }

  next();
};

const validateUserUpdate = (req, res, next) => {
  const { ho_ten, email, password, so_dien_thoai, dia_chi, vai_tro } = req.body;

  // Validate email format if provided
  if (email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return res.status(400).json({
        success: false,
        message: 'Email không hợp lệ.',
      });
    }
  }

  // Validate password if provided
  if (password && password.length < 6) {
    return res.status(400).json({
      success: false,
      message: 'Mật khẩu phải có ít nhất 6 ký tự.',
    });
  }

  // Validate role if provided
  if (vai_tro && !['khach_hang', 'quan_tri_vien'].includes(vai_tro)) {
    return res.status(400).json({
      success: false,
      message: 'Vai trò không hợp lệ.',
    });
  }

  // Validate phone if provided
  if (so_dien_thoai && !/^\d{10,15}$/.test(so_dien_thoai.replace(/\s/g, ''))) {
    return res.status(400).json({
      success: false,
      message: 'Số điện thoại không hợp lệ (10-15 chữ số).',
    });
  }

  next();
};

module.exports = {
  validateListQuery,
  validateUserCreate,
  validateUserUpdate,
};
