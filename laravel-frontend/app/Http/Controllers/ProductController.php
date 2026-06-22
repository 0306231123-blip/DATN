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
    public function sale()
    {
        // Ra lệnh: Lấy các sản phẩm đang bán VÀ cột gia_khuyen_mai không bị rỗng (khác NULL)
        $danhSachKhuyenMai = SanPham::whereNotNull('gia_khuyen_mai')
                                    ->where('trang_thai', 'dang_ban')
                                    ->get();
        
        return view('page_user.sale', compact('danhSachKhuyenMai'));
    }

    // Hàm hiển thị trang Bán Chạy
    public function bestseller()
    {
        // Query trực tiếp thay vì dùng SQL View
        $danhSachBanChay = SanPham::select('san_pham.*')
                                  ->join(
                                      \DB::raw('(SELECT ctdh.ma_san_pham, SUM(ctdh.so_luong) AS tong_so_luong_ban
                                                FROM chi_tiet_don_hang ctdh
                                                JOIN don_hang dh ON ctdh.ma_don_hang = dh.ma_don_hang
                                                WHERE dh.trang_thai_don = \'giao_thanh_cong\'
                                                GROUP BY ctdh.ma_san_pham) AS ban_chay'),
                                      'san_pham.ma_san_pham', '=', 'ban_chay.ma_san_pham'
                                  )
                                  ->where('san_pham.trang_thai', 'dang_ban')
                                  ->orderBy('ban_chay.tong_so_luong_ban', 'desc')
                                  ->get();

        return view('page_user.bestseller', compact('danhSachBanChay'));
    }
    public function home()
    {
        // Lấy 6 sản phẩm đang bán, sắp xếp theo điểm đánh giá từ cao xuống thấp
        $sanPhamNoiBat = SanPham::where('trang_thai', 'dang_ban')
                                ->orderBy('diem_danh_gia', 'desc') // Ưu tiên điểm cao
                                ->orderBy('so_luot_danh_gia', 'desc') // Ưu tiên nhiều người đánh giá
                                ->take(6) // Chỉ lấy 6 sản phẩm cho đẹp 2 hàng lưới
                                ->get();
                                
        return view('page_user.home', compact('sanPhamNoiBat'));
    }

    /**
     * Tìm kiếm sản phẩm realtime (AJAX)
     * Trả về JSON cho chức năng search-live
     */
    public function searchAjax(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json(['data' => []]);
        }

        $results = SanPham::where('trang_thai', 'dang_ban')
                          ->where(function ($q) use ($query) {
                              $q->where('ten_san_pham', 'LIKE', "%{$query}%")
                                ->orWhere('thuong_hieu', 'LIKE', "%{$query}%");
                          })
                          ->select('ma_san_pham', 'ten_san_pham', 'gia', 'gia_khuyen_mai', 'anh_san_pham', 'thuong_hieu')
                          ->take(10)
                          ->get();

        return response()->json(['data' => $results]);
    }
}
