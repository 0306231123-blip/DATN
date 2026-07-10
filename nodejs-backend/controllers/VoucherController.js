const KhuyenMai = require('../models/KhuyenMai');
const DonHang = require('../models/DonHang');
const SanPham = require('../models/SanPham');
const DanhMuc = require('../models/DanhMuc');
const { Op } = require('sequelize');
const jwt = require('jsonwebtoken');

class VoucherController {
  // POST /api/voucher/check
  async checkVoucher(req, res) {
    try {
        // ==========================================
        // 1. LẤY ID NGƯỜI DÙNG TỪ TOKEN
        // ==========================================
        const authHeader = req.headers.authorization;
        if (!authHeader || !authHeader.startsWith('Bearer ')) {
            return res.status(401).json({ success: false, message: 'Bạn chưa đăng nhập.' });
        }
        
        const token = authHeader.split(' ')[1];
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        let maNguoiDung = decoded.ma_nguoi_dung !== undefined ? decoded.ma_nguoi_dung : decoded.id;

        const { ma_code, tong_tien_hang } = req.body;

        if (!ma_code) {
            return res.status(400).json({ success: false, message: 'Vui lòng nhập mã khuyến mãi.' });
        }

        // 2. Tìm voucher
        const voucher = await KhuyenMai.findOne({ 
            where: { 
                ma_code: ma_code,
                trang_thai: 'hoat_dong' 
            } 
        });

        if (!voucher) {
            return res.status(404).json({ success: false, message: 'Mã khuyến mãi không tồn tại hoặc đã bị khóa.' });
        }

        // ==========================================
        // 3. CHỐT CHẶN: KIỂM TRA 1 USER / 1 MÃ
        // ==========================================
        const daSuDung = await DonHang.findOne({
            where: {
                ma_nguoi_dung: maNguoiDung,
                ma_khuyen_mai: voucher.ma_khuyen_mai,
                trang_thai_don: { [Op.notIn]: ['da_huy', 'da_tra_hang'] } // Chỉ đơn đã hủy hoặc đã trả hàng/hoàn tiền thì mới cho dùng lại
            }
        });

        if (daSuDung) {
            return res.status(400).json({ success: false, message: 'Bạn đã sử dụng mã này cho một đơn hàng trước đó rồi!' });
        }
        // ==========================================

        // 4. Kiểm tra số lượng
        if (voucher.so_luong <= 0) {
            return res.status(400).json({ success: false, message: 'Mã khuyến mãi này đã hết lượt sử dụng.' });
        }

        // 5. Kiểm tra hạn sử dụng
        const now = new Date();
        if (now < voucher.ngay_bat_dau || now > voucher.ngay_ket_thuc) {
            return res.status(400).json({ success: false, message: 'Mã khuyến mãi chưa có hiệu lực hoặc đã hết hạn.' });
        }

        // 6. Kiểm tra điều kiện đơn tối thiểu
        if (tong_tien_hang < voucher.don_toi_thieu) {
            return res.status(400).json({ 
                success: false, 
                message: `Đơn hàng phải từ ${voucher.don_toi_thieu.toLocaleString()}đ để áp dụng mã này.` 
            });
        }

        // 7. Tính toán số tiền được giảm
        let so_tien_giam = 0;
        if (voucher.loai_giam === 'tien_mat') {
            so_tien_giam = voucher.gia_tri;
        } else if (voucher.loai_giam === 'phan_tram') {
            so_tien_giam = (tong_tien_hang * voucher.gia_tri) / 100;
            // Nếu có cấu hình giảm tối đa thì không được vượt mức này
            if (voucher.giam_toi_da && so_tien_giam > voucher.giam_toi_da) {
                so_tien_giam = voucher.giam_toi_da;
            }
        }

        // Không cho phép giảm âm tiền (VD: đơn 40k nhập mã 50k thì chỉ giảm 40k)
        if (so_tien_giam > tong_tien_hang) {
            so_tien_giam = tong_tien_hang;
        }

        res.json({
            success: true,
            message: 'Áp dụng mã thành công!',
            data: {
                ma_khuyen_mai: voucher.ma_khuyen_mai,
                ma_code: voucher.ma_code,
                so_tien_giam: so_tien_giam
            }
        });

    } catch (error) {
        if (error.name === 'JsonWebTokenError' || error.name === 'TokenExpiredError') {
            return res.status(401).json({ success: false, message: 'Token không hợp lệ hoặc đã hết hạn.' });
        }
        console.error(error);
        res.status(500).json({ success: false, message: 'Lỗi server khi kiểm tra voucher.' });
    }
  }

  // GET /api/voucher/active
  // Lấy danh sách voucher đang hoạt động, còn lượt và chưa hết hạn
  async getActiveVouchers(req, res) {
    try {
        const vouchers = await KhuyenMai.findAll({
            where: {
                trang_thai: 'hoat_dong',
                ngay_ket_thuc: { [Op.gt]: new Date() } // Chưa tới hạn kết thúc
            },
            order: [['gia_tri', 'DESC']] // Mã giảm giá trị cao xếp trên
        });

        let usedVoucherIds = [];
        const authHeader = req.headers.authorization;
        if (authHeader && authHeader.startsWith('Bearer ')) {
            try {
                const token = authHeader.split(' ')[1];
                const decoded = jwt.verify(token, process.env.JWT_SECRET);
                let maNguoiDung = decoded.ma_nguoi_dung !== undefined ? decoded.ma_nguoi_dung : decoded.id;
                
                if (maNguoiDung !== undefined) {
                    const usedOrders = await DonHang.findAll({
                        where: {
                            ma_nguoi_dung: maNguoiDung,
                            ma_khuyen_mai: { [Op.not]: null },
                            trang_thai_don: { [Op.notIn]: ['da_huy', 'da_tra_hang'] }
                        },
                        attributes: ['ma_khuyen_mai']
                    });
                    usedVoucherIds = usedOrders.map(o => o.ma_khuyen_mai);
                }
            } catch (err) {
                // Bỏ qua lỗi token (vì API này có thể gọi public)
            }
        }

        const data = vouchers.map(v => {
            const vData = v.toJSON();
            vData.da_su_dung = usedVoucherIds.includes(v.ma_khuyen_mai);
            return vData;
        });

        res.json({
            success: true,
            data: data
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ success: false, message: 'Lỗi lấy danh sách voucher' });
    }
  }

  // GET /api/voucher
  async getAll(req, res) {
    try {
        const { page = 1, limit = 10, search = '' } = req.query;
        const offset = (page - 1) * limit;
        const where = search ? { ma_code: { [Op.like]: `%${search}%` } } : {};

        const { count, rows } = await KhuyenMai.findAndCountAll({
            where,
            include: [
                { model: SanPham, as: 'san_pham', attributes: ['ten_san_pham'] },
                { model: DanhMuc, as: 'danh_muc', attributes: ['ten_danh_muc'] }
            ],
            limit: parseInt(limit),
            offset,
            order: [['ma_khuyen_mai', 'DESC']]
        });

        res.json({
            status: 'success',
            data: rows,
            pagination: {
                total: count,
                page: parseInt(page),
                limit: parseInt(limit),
                pages: Math.ceil(count / limit)
            }
        });
    } catch (error) {
        console.error(error);
        res.status(500).json({ status: 'error', message: 'Lỗi lấy danh sách voucher' });
    }
  }

  // GET /api/voucher/:id
  async getById(req, res) {
    try {
        const voucher = await KhuyenMai.findByPk(req.params.id);
        if (!voucher) return res.status(404).json({ status: 'error', message: 'Không tìm thấy voucher' });
        res.json({ status: 'success', data: voucher });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
  }

  // POST /api/voucher
  async create(req, res) {
    try {
        const checkExist = await KhuyenMai.findOne({ where: { ma_code: req.body.ma_code } });
        if (checkExist) {
            return res.status(400).json({ status: 'error', message: 'Mã code đã tồn tại' });
        }
        const voucher = await KhuyenMai.create(req.body);
        res.json({ status: 'success', data: voucher, message: 'Thêm voucher thành công' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
  }

  // PUT /api/voucher/:id
  async update(req, res) {
    try {
        if (req.body.ma_code) {
            const checkExist = await KhuyenMai.findOne({ 
                where: { 
                    ma_code: req.body.ma_code,
                    ma_khuyen_mai: { [Op.ne]: req.params.id }
                } 
            });
            if (checkExist) return res.status(400).json({ status: 'error', message: 'Mã code đã tồn tại' });
        }

        const updated = await KhuyenMai.update(req.body, { where: { ma_khuyen_mai: req.params.id } });
        if (!updated[0]) return res.status(404).json({ status: 'error', message: 'Không tìm thấy voucher' });
        res.json({ status: 'success', message: 'Cập nhật thành công' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
  }

  // DELETE /api/voucher/:id
  async delete(req, res) {
    try {
        const deleted = await KhuyenMai.destroy({ where: { ma_khuyen_mai: req.params.id } });
        if (!deleted) return res.status(404).json({ status: 'error', message: 'Không tìm thấy voucher' });
        res.json({ status: 'success', message: 'Xóa voucher thành công' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
  }
}

module.exports = new VoucherController();
