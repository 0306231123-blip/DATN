const GioHang = require('../models/GioHang');
const SanPham = require('../models/SanPham'); 
const AnhSanPham = require('../models/AnhSanPham');

class CartController {
    // POST /api/cart/add
    async addToCart(req, res) {
        try {
            console.log("--- BẮT ĐẦU THÊM GIỎ HÀNG ---");
            console.log("User:", req.user); 
            console.log("Body:", req.body); 

            const { ma_san_pham, so_luong } = req.body;
            const soLuongThem = parseInt(so_luong) || 1;

            if (!ma_san_pham) {
                return res.status(400).json({ success: false, message: 'Thiếu mã sản phẩm!' });
            }

            const sanPham = await SanPham.findByPk(ma_san_pham);
            if (!sanPham) {
                return res.status(404).json({ success: false, message: 'Sản phẩm không tồn tại!' });
            }

            let item = await GioHang.findOne({ 
                where: { ma_nguoi_dung: req.user.ma_nguoi_dung, ma_san_pham } 
            });

            let soLuongHienTaiTrongGio = item ? parseInt(item.so_luong) : 0;
            if (soLuongHienTaiTrongGio + soLuongThem > sanPham.so_luong_ton) {
                let maxThem = sanPham.so_luong_ton - soLuongHienTaiTrongGio;
                if (maxThem <= 0) {
                    return res.status(400).json({ success: false, message: `Bạn đã thêm tối đa số lượng sản phẩm này vào giỏ hàng rồi!` });
                }
                return res.status(400).json({ 
                    success: false, 
                    message: `Trong giỏ hàng của bạn đã có ${soLuongHienTaiTrongGio} sản phẩm. Bạn chỉ có thể thêm tối đa ${maxThem} sản phẩm nữa!` 
                });
            }

            if (item) {
                item.so_luong = soLuongHienTaiTrongGio + soLuongThem;
                await item.save();
            } else {
                await GioHang.create({
                    ma_nguoi_dung: req.user.ma_nguoi_dung,
                    ma_san_pham,
                    so_luong: soLuongThem
                });
            }
            res.json({ success: true, message: 'Đã thêm vào giỏ hàng!' });
        } catch (error) {
            console.error("LỖI CHI TIẾT:", error); 
            res.status(500).json({ success: false, message: 'Lỗi server: ' + error.message });
        }
    }

    // GET /api/cart
    async getCart(req, res) {
        try {
            const items = await GioHang.findAll({ 
                where: { ma_nguoi_dung: req.user.ma_nguoi_dung },
                include: [{ 
                    model: SanPham, 
                    as: 'san_pham',
                    include: [{ model: AnhSanPham, as: 'danh_sach_anh' }]
                }]
            });
            
            res.json({ success: true, data: items }); 
        } catch (error) {
            res.status(500).json({ success: false, message: error.message });
        }
    }

    // POST /api/cart/update
    async updateCart(req, res) {
        try {
            const { ma_san_pham, thay_doi, so_luong } = req.body; 

            const sanPham = await SanPham.findByPk(ma_san_pham);
            if (!sanPham) {
                return res.status(404).json({ success: false, message: 'Sản phẩm không tồn tại!' });
            }

            let item = await GioHang.findOne({ 
                where: { ma_nguoi_dung: req.user.ma_nguoi_dung, ma_san_pham } 
            });

            if (item) {
                let newSoLuong = item.so_luong;
                if (so_luong !== undefined) {
                    newSoLuong = parseInt(so_luong);
                } else if (thay_doi !== undefined) {
                    newSoLuong += parseInt(thay_doi);
                }

                if (newSoLuong > sanPham.so_luong_ton) {
                    return res.status(400).json({ success: false, message: `Vượt quá số lượng tồn! Sản phẩm chỉ còn ${sanPham.so_luong_ton} cái.` });
                }

                item.so_luong = newSoLuong;
                if (item.so_luong <= 0) await item.destroy(); 
                else await item.save();
                res.json({ success: true });
            } else {
                res.status(404).json({ success: false, message: 'Không tìm thấy sản phẩm trong giỏ hàng' });
            }
        } catch (error) {
            res.status(500).json({ success: false });
        }
    }

    // POST /api/cart/remove
    async removeFromCart(req, res) {
        try {
            const ma_nguoi_dung = req.user.ma_nguoi_dung;
            const { ma_san_pham } = req.body;

            if (!ma_san_pham) {
                return res.status(400).json({ success: false, message: 'Thiếu mã sản phẩm' });
            }

            await GioHang.destroy({
                where: {
                    ma_nguoi_dung: ma_nguoi_dung,
                    ma_san_pham: ma_san_pham
                }
            });

            res.json({ success: true, message: 'Đã xóa sản phẩm khỏi giỏ hàng' });
        } catch (error) {
            console.error("Lỗi xóa giỏ hàng:", error);
            res.status(500).json({ success: false, message: 'Lỗi server' });
        }
    }
}

module.exports = new CartController();
