<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham; // Gọi Model Sản Phẩm vào đây
use Illuminate\Support\Facades\DB;

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
        $danhSachBanChay = SanPham::select('san_pham.*')
            ->selectSub(function ($query) {
                $query->from('chi_tiet_don_hang')
                    ->join('don_hang', 'chi_tiet_don_hang.ma_don_hang', '=', 'don_hang.ma_don_hang')
                    ->whereColumn('chi_tiet_don_hang.ma_san_pham', 'san_pham.ma_san_pham')
                    ->whereNotIn('don_hang.trang_thai_don', ['da_huy', 'da_tra_hang', 'tra_hang_hoan_tien'])
                    ->selectRaw('SUM(so_luong)');
            }, 'tong_so_luong_ban')
            ->where('san_pham.trang_thai', 'dang_ban')
            // DÒNG NÀY SẼ LỌC BỎ NHỮNG SẢN PHẨM CÓ TỔNG SỐ LƯỢNG BÁN = 0 (HOẶC NULL)
            ->having('tong_so_luong_ban', '>', 0) 
            ->orderByRaw('tong_so_luong_ban DESC')
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

    /**
     * Tìm kiếm sản phẩm realtime (AJAX)
     * Trả về JSON cho chức năng search-live
     */
    public function searchAjax(Request $request)
    {
        $keyword = $request->get('q', '');
        if (strlen($keyword) < 2) return response()->json(['data' => []]);

        // Tìm 5 sản phẩm có tên chứa từ khóa
        $products = \App\Models\SanPham::where('trang_thai', 'dang_ban')
                    ->where(function ($q) use ($keyword) {
                        $q->where('ten_san_pham', 'LIKE', '%' . $keyword . '%')
                          ->orWhere('thuong_hieu', 'LIKE', '%' . $keyword . '%');
                    })
                    ->limit(5)
                    ->get();

        $results = $products->map(function($sp) {
            // Lấy ảnh thủ công cho an toàn, không sợ lỗi Model
            $anh = \App\Models\AnhSanPham::where('ma_san_pham', $sp->ma_san_pham)
                                         ->where('la_anh_chinh', 1)
                                         ->first();
            return [
                'ma_san_pham' => $sp->ma_san_pham,
                'ten_san_pham' => $sp->ten_san_pham,
                'thuong_hieu' => $sp->thuong_hieu,
                'gia' => $sp->gia,
                'gia_khuyen_mai' => $sp->gia_khuyen_mai,
                // Sửa lại asset() để lấy đúng link ảnh gốc của web
                'anh' => $anh ? asset($anh->duong_dan_anh) : asset('images/logo.jpg') 
            ];
        });

        return response()->json(['data' => $results]);
    }
}
