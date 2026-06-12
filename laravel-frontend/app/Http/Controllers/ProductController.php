<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham; // Gọi Model Sản Phẩm vào đây

class ProductController extends Controller
{
    // 1. Hàm hiển thị trang TẤT CẢ SẢN PHẨM
    public function index()
    {
        // Đã sửa: Dùng paginate(12) để tự động chia 12 sản phẩm/trang
        $danhSachSanPham = SanPham::where('trang_thai', 'dang_ban')
                                  ->orderBy('ngay_tao', 'desc')
                                  ->paginate(12);
        
        return view('page_user.product', compact('danhSachSanPham'));
    }

    // 2. Hàm hiển thị trang CHI TIẾT SẢN PHẨM
    public function detail($id)
    {
        // Tìm 1 sản phẩm có mã khớp với $id, nếu không thấy thì báo lỗi 404
        $sanPham = SanPham::findOrFail($id);
        
        return view('page_user.detail', compact('sanPham'));
    }

    // 3. Hàm hiển thị trang KHUYẾN MÃI
    public function sale()
    {
        // Đã nâng cấp: Dùng paginate(12) thay vì get() để lỡ có nhiều hàng sale thì web vẫn mượt
        $danhSachKhuyenMai = SanPham::whereNotNull('gia_khuyen_mai')
                                    ->where('trang_thai', 'dang_ban')
                                    ->paginate(12);
        
        return view('page_user.sale', compact('danhSachKhuyenMai'));
    }

    // 4. Hàm hiển thị trang BÁN CHẠY
    public function bestseller()
    {
        // Đã nâng cấp: Dùng paginate(12) thay vì get()
        $danhSachBanChay = SanPham::join('v_san_pham_ban_chay', 'SAN_PHAM.ma_san_pham', '=', 'v_san_pham_ban_chay.ma_san_pham')
                                  ->where('SAN_PHAM.trang_thai', 'dang_ban')
                                  ->orderBy('v_san_pham_ban_chay.tong_so_luong_ban', 'desc')
                                  ->paginate(12);

        return view('page_user.bestseller', compact('danhSachBanChay'));
    }

    // 5. Hàm hiển thị TRANG CHỦ
    public function home()
    {
        // Lấy 6 sản phẩm đang bán, sắp xếp theo điểm đánh giá từ cao xuống thấp
        $sanPhamNoiBat = SanPham::where('trang_thai', 'dang_ban')
                                ->orderBy('diem_danh_gia', 'desc') // Ưu tiên điểm cao
                                ->orderBy('so_luot_danh_gia', 'desc') // Ưu tiên nhiều người đánh giá
                                ->take(6) // Lấy đúng 6 sản phẩm thôi để xếp 2 hàng ngang cho đẹp
                                ->get();
                                
        return view('page_user.home', compact('sanPhamNoiBat'));
    }
}