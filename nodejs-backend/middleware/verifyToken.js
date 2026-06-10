const jwt = require('jsonwebtoken');

// LƯU Ý: Chỗ 'chuoi_khoa_bi_mat_jwt_cua_do_an' ông phải thay bằng đúng cái chuỗi bí mật mà ông dùng để tạo token ở chức năng Login nhé!
const JWT_SECRET = process.env.JWT_SECRET || 'chuoi_khoa_bi_mat_jwt_cua_do_an';

const verifyToken = (req, res, next) => {
    const authHeader = req.headers['authorization'];
    const token = authHeader && authHeader.split(' ')[1]; 

    if (!token) {
        return res.status(401).json({ success: false, message: 'Vui lòng đăng nhập!' });
    }

    try {
        const decoded = jwt.verify(token, JWT_SECRET);
        req.user = decoded; 
        next(); 
    } catch (error) {
        return res.status(403).json({ success: false, message: 'Token không hợp lệ hoặc đã hết hạn!' });
    }
};

module.exports = { verifyToken };