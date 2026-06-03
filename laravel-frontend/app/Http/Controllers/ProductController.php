<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham; // Gọi Model Sản Phẩm vào đây

class ProductController extends Controller
{
    // Hàm hiển thị trang Tất cả sản phẩm
    public function index()
    {
        // Ra lệnh: Lấy tất cả sản phẩm đang có trạng thái 'dang_ban'
        $danhSachSanPham = SanPham::where('trang_thai', 'dang_ban')->get();
        
        // Truyền cục dữ liệu đó sang file giao diện (View)
        return view('page_user.product', compact('danhSachSanPham'));
    }
    public function detail($id)
    {
        // Ra lệnh: Tìm 1 sản phẩm có mã khớp với $id, nếu không thấy thì báo lỗi 404
        $sanPham = SanPham::findOrFail($id);
        
        // Truyền sản phẩm đó sang giao diện trang chi tiết
        return view('page_user.detail', compact('sanPham'));
    }
}
