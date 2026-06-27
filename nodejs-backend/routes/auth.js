const express = require('express');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const NguoiDung = require('../models/NguoiDung');

const router = express.Router();

/**
 * POST /api/auth/register
 * Đăng ký tài khoản mới
 */
router.post('/register', async (req, res) => {
  try {
    const { ho_ten, email, password } = req.body;

    // Validate
    if (!ho_ten || !email || !password) {
      return res.status(400).json({
        success: false,
        message: 'Vui lòng nhập đầy đủ họ tên, email và mật khẩu.',
      });
    }

    if (password.length < 6) {
      return res.status(400).json({
        success: false,
        message: 'Mật khẩu phải có ít nhất 6 ký tự.',
      });
    }

    // Kiểm tra email đã tồn tại
    const existingEmail = await NguoiDung.findOne({ where: { email } });
    if (existingEmail) {
      return res.status(409).json({
        success: false,
        message: 'Email này đã được đăng ký.',
      });
    }

    // Kiểm tra số điện thoại (nếu có nhập)
    if (req.body.so_dien_thoai) {
      const existingPhone = await NguoiDung.findOne({ where: { so_dien_thoai: req.body.so_dien_thoai } });
      if (existingPhone) {
        return res.status(409).json({
          success: false,
          message: 'Số điện thoại này đã được đăng ký.',
        });
      }
    }

    // Hash mật khẩu
    const salt = await bcrypt.genSalt(10);
    const hashedPassword = await bcrypt.hash(password, salt);

    // Tạo tài khoản
    const nguoiDung = await NguoiDung.create({
      ho_ten,
      email,
      mat_khau: hashedPassword,
      so_dien_thoai: req.body.so_dien_thoai || null,
      vai_tro: 'khach_hang',
      trang_thai: 'hoat_dong',
    });

    // Tạo JWT token
    const token = jwt.sign(
      {
        ma_nguoi_dung: nguoiDung.ma_nguoi_dung,
        email: nguoiDung.email,
        vai_tro: nguoiDung.vai_tro,
      },
      process.env.JWT_SECRET,
      { expiresIn: '7d' }
    );

    res.status(201).json({
      success: true,
      message: 'Đăng ký thành công!',
      data: {
        token,
        user: {
          ma_nguoi_dung: nguoiDung.ma_nguoi_dung,
          ho_ten: nguoiDung.ho_ten,
          email: nguoiDung.email,
          vai_tro: nguoiDung.vai_tro,
        },
      },
    });
  } catch (error) {
    console.error('Register error:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi hệ thống. Vui lòng thử lại sau.',
    });
  }
});

/**
 * POST /api/auth/login
 * Đăng nhập
 */
router.post('/login', async (req, res) => {
  try {
    const { email, password } = req.body;

    // Validate
    if (!email || !password) {
      return res.status(400).json({
        success: false,
        message: 'Vui lòng nhập email và mật khẩu.',
      });
    }

    // Tìm người dùng
    const nguoiDung = await NguoiDung.findOne({ where: { email } });
    if (!nguoiDung) {
      return res.status(401).json({
        success: false,
        message: 'Email không tồn tại trong hệ thống.',
      });
    }

    // Kiểm tra tài khoản bị khóa
    if (nguoiDung.trang_thai === 'bi_khoa') {
      const reason = nguoiDung.ly_do_khoa ? ` (${nguoiDung.ly_do_khoa})` : '';
      return res.status(403).json({
        success: false,
        message: `Tài khoản của bạn đã bị khóa${reason}.`,
      });
    }

    // Kiểm tra mật khẩu
    const isMatch = await bcrypt.compare(password, nguoiDung.mat_khau);
    if (!isMatch) {
      return res.status(401).json({
        success: false,
        message: 'Mật khẩu không chính xác.',
      });
    }

    // Tạo JWT token
    const token = jwt.sign(
      {
        ma_nguoi_dung: nguoiDung.ma_nguoi_dung,
        email: nguoiDung.email,
        vai_tro: nguoiDung.vai_tro,
      },
      process.env.JWT_SECRET,
      { expiresIn: '7d' }
    );

    res.json({
      success: true,
      message: 'Đăng nhập thành công!',
      data: {
        token,
        user: {
          ma_nguoi_dung: nguoiDung.ma_nguoi_dung,
          ho_ten: nguoiDung.ho_ten,
          email: nguoiDung.email,
          vai_tro: nguoiDung.vai_tro,
        },
      },
    });
  } catch (error) {
    console.error('Login error:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi hệ thống. Vui lòng thử lại sau.',
    });
  }
});

/**
 * GET /api/auth/me
 * Lấy thông tin user hiện tại (cần token)
 */
router.get('/me', async (req, res) => {
  try {
    const authHeader = req.headers.authorization;
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return res.status(401).json({
        success: false,
        message: 'Chưa đăng nhập.',
      });
    }

    const token = authHeader.split(' ')[1];
    const decoded = jwt.verify(token, process.env.JWT_SECRET);

    const nguoiDung = await NguoiDung.findByPk(decoded.ma_nguoi_dung, {
      attributes: { exclude: ['mat_khau'] },
    });

    if (!nguoiDung) {
      return res.status(404).json({
        success: false,
        message: 'Người dùng không tồn tại.',
      });
    }

    res.json({
      success: true,
      data: nguoiDung,
    });
  } catch (error) {
    if (error.name === 'JsonWebTokenError' || error.name === 'TokenExpiredError') {
      return res.status(401).json({
        success: false,
        message: 'Token không hợp lệ hoặc đã hết hạn.',
      });
    }
    console.error('Get me error:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi hệ thống.',
    });
  }
});

router.put('/update-profile', async (req, res) => {
  try {
    // 1. Kiểm tra xem người dùng đã đăng nhập chưa (Kiểm tra Token)
    const authHeader = req.headers.authorization;
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
      return res.status(401).json({
        success: false,
        message: 'Chưa đăng nhập hoặc phiên làm việc đã hết hạn.',
      });
    }

    // Giải mã token để lấy ID của người dùng
    const token = authHeader.split(' ')[1];
    const decoded = jwt.verify(token, process.env.JWT_SECRET);

    // 2. Lấy các dữ liệu mà Frontend gửi sang (ĐÃ THÊM 3 TRƯỜNG NGÂN HÀNG)
    const { 
        ho_ten, 
        so_dien_thoai, 
        dia_chi, 
        loai_da, 
        mat_khau_moi,
        ngan_hang,
        so_tai_khoan,
        chu_tai_khoan
    } = req.body;

    // 3. Tìm người dùng trong Database
    const nguoiDung = await NguoiDung.findByPk(decoded.ma_nguoi_dung);
    if (!nguoiDung) {
      return res.status(404).json({
        success: false,
        message: 'Người dùng không tồn tại.',
      });
    }

    // 4. Ghi đè thông tin mới vào
    if (ho_ten) nguoiDung.ho_ten = ho_ten;
    if (so_dien_thoai !== undefined) nguoiDung.so_dien_thoai = so_dien_thoai;
    if (dia_chi !== undefined) nguoiDung.dia_chi = dia_chi;
    if (loai_da !== undefined) nguoiDung.loai_da = loai_da;
    
    // --- LƯU THÔNG TIN NGÂN HÀNG ---
    if (ngan_hang !== undefined) nguoiDung.ngan_hang = ngan_hang;
    if (so_tai_khoan !== undefined) nguoiDung.so_tai_khoan = so_tai_khoan;
    if (chu_tai_khoan !== undefined) nguoiDung.chu_tai_khoan = chu_tai_khoan;

    // 5. Nếu người dùng có nhập mật khẩu mới -> Mã hóa nó rồi mới lưu
    if (mat_khau_moi) {
      const salt = await bcrypt.genSalt(10);
      nguoiDung.mat_khau = await bcrypt.hash(mat_khau_moi, salt);
    }

    // 6. Ra lệnh cho SQL lưu toàn bộ thay đổi
    await nguoiDung.save();

    res.json({
      success: true,
      message: 'Cập nhật thông tin thành công!',
    });

  } catch (error) {
    if (error.name === 'JsonWebTokenError' || error.name === 'TokenExpiredError') {
      return res.status(401).json({
        success: false,
        message: 'Token không hợp lệ hoặc đã hết hạn.',
      });
    }
    console.error('Lỗi cập nhật profile:', error);
    res.status(500).json({
      success: false,
      message: 'Lỗi hệ thống. Vui lòng thử lại sau.',
    });
  }
});
module.exports = router;