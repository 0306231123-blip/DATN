<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SanPham; // Gọi Model Sản Phẩm vào đây
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // 1. Hàm hiển thị trang TẤT CẢ SẢN PHẨM
    public function index(Request $request)
    {
        $query = SanPham::where('trang_thai', 'dang_ban');

        // Lọc theo danh mục
        if ($request->has('danh_muc') && $request->get('danh_muc') != '') {
            $dmArray = explode(',', $request->get('danh_muc'));
            
            // Hỗ trợ danh mục 2 cấp: Lấy cả ID của các danh mục con nếu chọn danh mục cha
            $allCategoryIds = DB::table('danh_muc')
                                ->whereIn('ma_danh_muc', $dmArray)
                                ->orWhereIn('ma_danh_muc_cha', $dmArray)
                                ->pluck('ma_danh_muc')
                                ->toArray();

            $query->whereIn('ma_danh_muc', $allCategoryIds);
        }

        // Lọc theo thương hiệu
        if ($request->has('thuong_hieu') && $request->get('thuong_hieu') != '') {
            $thArray = explode(',', $request->get('thuong_hieu'));
            $query->whereIn('thuong_hieu', $thArray);
        }

        // Lọc theo khoảng giá
        if ($request->has('min_price') && $request->get('min_price') != '') {
            $min = (int) $request->get('min_price');
            $query->whereRaw('COALESCE(gia_khuyen_mai, gia) >= ?', [$min]);
        }
        if ($request->has('max_price') && $request->get('max_price') != '') {
            $max = (int) $request->get('max_price');
            $query->whereRaw('COALESCE(gia_khuyen_mai, gia) <= ?', [$max]);
        }

        // Sắp xếp
        if ($request->has('sort') && $request->get('sort') != '') {
            $sort = $request->get('sort');
            if ($sort == 'price_asc') {
                $query->orderByRaw('COALESCE(gia_khuyen_mai, gia) ASC');
            } elseif ($sort == 'price_desc') {
                $query->orderByRaw('COALESCE(gia_khuyen_mai, gia) DESC');
            } else {
                $query->orderBy('ngay_tao', 'desc');
            }
        } else {
            $query->orderBy('ngay_tao', 'desc');
        }

        $danhSachSanPham = $query->paginate(12);

        // Nối thêm parameter vào pagination link để giữ nguyên query lọc khi chuyển trang
        $danhSachSanPham->appends($request->all());
        
        // Lấy danh sách danh mục để hiển thị checkbox filter
        $danhMucs = DB::table('danh_muc')->orderBy('thu_tu_hien_thi', 'asc')->get();

        // Lấy danh sách thương hiệu để hiển thị
        $thuongHieus = SanPham::where('trang_thai', 'dang_ban')->whereNotNull('thuong_hieu')->where('thuong_hieu', '!=', '')->distinct()->orderBy('thuong_hieu', 'asc')->pluck('thuong_hieu');
        
        return view('page_user.product', compact('danhSachSanPham', 'danhMucs', 'thuongHieus'));
    }

    // 2. Hàm hiển thị trang CHI TIẾT SẢN PHẨM
    public function detail($id)
    {
        // Tìm 1 sản phẩm có mã khớp với $id, nếu không thấy thì báo lỗi 404
        $sanPham = SanPham::findOrFail($id);
        
        return view('page_user.detail', compact('sanPham'));
    }

    // 3. Hàm hiển thị trang KHUYẾN MÃI
    public function sale(Request $request)
    {
        $query = SanPham::whereNotNull('gia_khuyen_mai')
                        ->where('trang_thai', 'dang_ban');
                        
        // Lọc theo danh mục
        if ($request->has('danh_muc') && $request->get('danh_muc') != '') {
            $dmArray = explode(',', $request->get('danh_muc'));
            $allCategoryIds = DB::table('danh_muc')
                                ->whereIn('ma_danh_muc', $dmArray)
                                ->orWhereIn('ma_danh_muc_cha', $dmArray)
                                ->pluck('ma_danh_muc')
                                ->toArray();
            $query->whereIn('ma_danh_muc', $allCategoryIds);
        }

        // Lọc theo thương hiệu
        if ($request->has('thuong_hieu') && $request->get('thuong_hieu') != '') {
            $thArray = explode(',', $request->get('thuong_hieu'));
            $query->whereIn('thuong_hieu', $thArray);
        }

        // Lọc theo khoảng giá
        if ($request->has('min_price') && $request->get('min_price') != '') {
            $min = (int) $request->get('min_price');
            $query->whereRaw('COALESCE(gia_khuyen_mai, gia) >= ?', [$min]);
        }
        if ($request->has('max_price') && $request->get('max_price') != '') {
            $max = (int) $request->get('max_price');
            $query->whereRaw('COALESCE(gia_khuyen_mai, gia) <= ?', [$max]);
        }

        // Sắp xếp
        if ($request->has('sort') && $request->get('sort') != '') {
            $sort = $request->get('sort');
            if ($sort == 'price_asc') {
                $query->orderByRaw('COALESCE(gia_khuyen_mai, gia) ASC');
            } elseif ($sort == 'price_desc') {
                $query->orderByRaw('COALESCE(gia_khuyen_mai, gia) DESC');
            } else {
                $query->orderBy('ngay_tao', 'desc');
            }
        } else {
            $query->orderBy('ngay_tao', 'desc');
        }

        $danhSachKhuyenMai = $query->paginate(12);
        $danhSachKhuyenMai->appends($request->all());
        
        $danhMucs = DB::table('danh_muc')->orderBy('thu_tu_hien_thi', 'asc')->get();
        $thuongHieus = SanPham::where('trang_thai', 'dang_ban')->whereNotNull('thuong_hieu')->where('thuong_hieu', '!=', '')->distinct()->orderBy('thuong_hieu', 'asc')->pluck('thuong_hieu');
        
        return view('page_user.sale', compact('danhSachKhuyenMai', 'danhMucs', 'thuongHieus'));
    }

    // 4. Hàm hiển thị trang BÁN CHẠY
    public function bestseller(Request $request)
    {
        $query = SanPham::select('san_pham.*')
            ->selectSub(function ($subquery) {
                $subquery->from('chi_tiet_don_hang')
                    ->join('don_hang', 'chi_tiet_don_hang.ma_don_hang', '=', 'don_hang.ma_don_hang')
                    ->whereColumn('chi_tiet_don_hang.ma_san_pham', 'san_pham.ma_san_pham')
                    ->whereNotIn('don_hang.trang_thai_don', ['da_huy', 'da_tra_hang', 'tra_hang_hoan_tien'])
                    ->selectRaw('SUM(so_luong)');
            }, 'tong_so_luong_ban')
            ->where('san_pham.trang_thai', 'dang_ban')
            ->having('tong_so_luong_ban', '>', 0);
            
        // Lọc theo danh mục
        if ($request->has('danh_muc') && $request->get('danh_muc') != '') {
            $dmArray = explode(',', $request->get('danh_muc'));
            $allCategoryIds = DB::table('danh_muc')
                                ->whereIn('ma_danh_muc', $dmArray)
                                ->orWhereIn('ma_danh_muc_cha', $dmArray)
                                ->pluck('ma_danh_muc')
                                ->toArray();
            $query->whereIn('san_pham.ma_danh_muc', $allCategoryIds);
        }

        // Lọc theo thương hiệu
        if ($request->has('thuong_hieu') && $request->get('thuong_hieu') != '') {
            $thArray = explode(',', $request->get('thuong_hieu'));
            $query->whereIn('san_pham.thuong_hieu', $thArray);
        }

        // Lọc theo khoảng giá
        if ($request->has('min_price') && $request->get('min_price') != '') {
            $min = (int) $request->get('min_price');
            $query->whereRaw('COALESCE(san_pham.gia_khuyen_mai, san_pham.gia) >= ?', [$min]);
        }
        if ($request->has('max_price') && $request->get('max_price') != '') {
            $max = (int) $request->get('max_price');
            $query->whereRaw('COALESCE(san_pham.gia_khuyen_mai, san_pham.gia) <= ?', [$max]);
        }

        // Sắp xếp
        if ($request->has('sort') && $request->get('sort') != '') {
            $sort = $request->get('sort');
            if ($sort == 'price_asc') {
                $query->orderByRaw('COALESCE(san_pham.gia_khuyen_mai, san_pham.gia) ASC');
            } elseif ($sort == 'price_desc') {
                $query->orderByRaw('COALESCE(san_pham.gia_khuyen_mai, san_pham.gia) DESC');
            } else {
                $query->orderByRaw('tong_so_luong_ban DESC');
            }
        } else {
            $query->orderByRaw('tong_so_luong_ban DESC');
        }
            
        $danhSachBanChay = $query->paginate(12);
        $danhSachBanChay->appends($request->all());

        $danhMucs = DB::table('danh_muc')->orderBy('thu_tu_hien_thi', 'asc')->get();
        $thuongHieus = SanPham::where('trang_thai', 'dang_ban')->whereNotNull('thuong_hieu')->where('thuong_hieu', '!=', '')->distinct()->orderBy('thuong_hieu', 'asc')->pluck('thuong_hieu');
        
        return view('page_user.bestseller', compact('danhSachBanChay', 'danhMucs', 'thuongHieus'));
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
                                
        // Lấy danh sách danh mục để hiển thị ra trang chủ
        $danhMucs = DB::table('danh_muc')->orderBy('thu_tu_hien_thi', 'asc')->get();
        foreach($danhMucs as $dm) {
            $childIds = DB::table('danh_muc')->where('ma_danh_muc_cha', $dm->ma_danh_muc)->pluck('ma_danh_muc')->toArray();
            $allIds = array_merge([$dm->ma_danh_muc], $childIds);
            $dm->so_luong_sp = DB::table('san_pham')
                ->whereIn('ma_danh_muc', $allIds)
                ->where('trang_thai', 'dang_ban')
                ->count();
        }
                                
        return view('page_user.home', compact('sanPhamNoiBat', 'danhMucs'));
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
