const jwt = require('jsonwebtoken');

const verifyToken = (req, res, next) => {
    const authHeader = req.headers['authorization'];
    const token = authHeader && authHeader.split(' ')[1]; // Lấy token từ chuỗi "Bearer <token>"

    if (!token) {
        return res.status(401).json({ success: false, message: 'Vui lòng đăng nhập để sử dụng chức năng này!' });
    }

    try {
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        req.user = decoded; // Gắn thông tin user vào req để dùng cho các bước sau
        next(); // Cho phép đi tiếp
    } catch (error) {
        return res.status(403).json({ success: false, message: 'Phiên đăng nhập hết hạn hoặc không hợp lệ.' });
    }
};

module.exports = verifyToken;