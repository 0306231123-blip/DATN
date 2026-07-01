const DanhGia = require('../models/DanhGia');
const sequelize = require('../config/database');
exports.addReview = async (req, res) => {
    try {
        // Lấy mã người dùng từ token (nhớ đảm bảo API này có dùng middleware verifyToken)
        const ma_nguoi_dung = req.user.ma_nguoi_dung; 
        const { ma_san_pham, diem_so, noi_dung } = req.body;

        // KIỂM TRA ĐIỀU KIỆN: Người dùng phải mua sản phẩm và đơn hàng phải 'hoan_thanh'
        const checkQuery = `
            SELECT 1 FROM don_hang dh
            JOIN chi_tiet_don_hang ct ON dh.ma_don_hang = ct.ma_don_hang
            WHERE dh.ma_nguoi_dung = :ma_nguoi_dung 
              AND ct.ma_san_pham = :ma_san_pham 
              AND dh.trang_thai_don = 'hoan_thanh'
            LIMIT 1
        `;
        const eligible = await sequelize.query(checkQuery, {
            replacements: { ma_nguoi_dung, ma_san_pham },
            type: sequelize.QueryTypes.SELECT
        });

        if (!eligible || eligible.length === 0) {
            return res.status(403).json({ success: false, message: 'Bạn chưa mua sản phẩm này hoặc đơn hàng chưa hoàn thành, nên không thể đánh giá!' });
        }

        // Thêm vào Database
        await DanhGia.create({
            ma_nguoi_dung,
            ma_san_pham,
            diem_so,
            noi_dung,
            trang_thai: 'hien_thi'
        });

        res.json({ success: true, message: 'Cảm ơn bạn đã đánh giá sản phẩm!' });
    } catch (error) {
        console.error('Lỗi khi lưu đánh giá:', error);
        res.status(500).json({ success: false, message: 'Lỗi server Node.js' });
    }
};
exports.getReviews = async (req, res) => {
    try {
        const { ma_san_pham } = req.params;

        // Dùng lệnh SQL thuần để nối bảng danh_gia với bảng nguoi_dung (để lấy tên người viết)
        const query = `
            SELECT d.*, n.ho_ten 
            FROM danh_gia d 
            JOIN nguoi_dung n ON d.ma_nguoi_dung = n.ma_nguoi_dung 
            WHERE d.ma_san_pham = :ma_san_pham
            ORDER BY d.ngay_viet DESC
        `;

        const reviews = await sequelize.query(query, {
            replacements: { ma_san_pham: ma_san_pham },
            type: sequelize.QueryTypes.SELECT
        });

        res.json({ success: true, data: reviews });
    } catch (error) {
        console.error('Lỗi lấy danh giá:', error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
};

exports.checkEligibility = async (req, res) => {
    try {
        const ma_nguoi_dung = req.user.ma_nguoi_dung;
        const { ma_san_pham } = req.params;

        const checkQuery = `
            SELECT 1 FROM don_hang dh
            JOIN chi_tiet_don_hang ct ON dh.ma_don_hang = ct.ma_don_hang
            WHERE dh.ma_nguoi_dung = :ma_nguoi_dung 
              AND ct.ma_san_pham = :ma_san_pham 
              AND dh.trang_thai_don = 'hoan_thanh'
            LIMIT 1
        `;
        const eligible = await sequelize.query(checkQuery, {
            replacements: { ma_nguoi_dung, ma_san_pham },
            type: sequelize.QueryTypes.SELECT
        });

        if (eligible && eligible.length > 0) {
            res.json({ success: true, eligible: true });
        } else {
            res.json({ success: true, eligible: false });
        }
    } catch (error) {
        console.error('Lỗi kiểm tra quyền đánh giá:', error);
        res.status(500).json({ success: false, message: 'Lỗi server' });
    }
};