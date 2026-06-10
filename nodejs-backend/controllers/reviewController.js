const DanhGia = require('../models/DanhGia');
const sequelize = require('../config/database');
exports.addReview = async (req, res) => {
    try {
        // Lấy mã người dùng từ token (nhớ đảm bảo API này có dùng middleware verifyToken)
        const ma_nguoi_dung = req.user.ma_nguoi_dung; 
        const { ma_san_pham, diem_so, noi_dung } = req.body;

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