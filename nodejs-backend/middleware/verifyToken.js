const jwt = require('jsonwebtoken');

/**
 * Middleware xác thực JWT token
 * Kiểm tra header Authorization: Bearer <token>
 * Nếu hợp lệ, gán thông tin user vào req.user
 */
const verifyToken = (req, res, next) => {
  const authHeader = req.headers.authorization || req.headers['authorization'];
  if (!authHeader || !authHeader.startsWith('Bearer ')) {
    return res.status(401).json({
      success: false,
      message: 'Chưa đăng nhập. Vui lòng cung cấp token.',
    });
  }

  try {
    const token = authHeader.split(' ')[1];
    // Thay đổi key secret nếu cần thiết
    const decoded = jwt.verify(token, process.env.JWT_SECRET || 'chuoi_khoa_bi_mat_jwt_cua_do_an');
    req.user = decoded;
    next();
  } catch (error) {
    if (error.name === 'TokenExpiredError') {
      return res.status(401).json({
        success: false,
        message: 'Token đã hết hạn. Vui lòng đăng nhập lại.',
      });
    }
    return res.status(401).json({
      success: false,
      message: 'Token không hợp lệ.',
    });
  }
};

module.exports = { verifyToken };
