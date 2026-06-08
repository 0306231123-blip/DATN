<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Gọi API Node.js backend để lấy dữ liệu dashboard
            $response = Http::timeout(10)->get('http://localhost:3000/api/dashboard/stats');

            if ($response->successful()) {
                $data = $response->json('data');
            } else {
                $data = $this->getEmptyData();
            }
        } catch (\Exception $e) {
            // Nếu backend không hoạt động, trả về dữ liệu rỗng
            $data = $this->getEmptyData();
        }

        return view('admin.dashboard', compact('data'));
    }

    /**
     * Trả về cấu trúc dữ liệu rỗng khi không kết nối được backend
     */
    private function getEmptyData()
    {
        return [
            'stats' => [
                'tong_doanh_thu' => 0,
                'tong_don_hang' => 0,
                'khach_hang_moi' => 0,
                'san_pham_dang_ban' => 0,
                'danh_gia_trung_binh' => 0,
            ],
            'doanh_thu_theo_thang' => [],
            'doanh_thu_theo_danh_muc' => [],
            'san_pham_ban_chay' => [],
            'don_hang_gan_day' => [],
            'thong_ke_trang_thai_don' => [
                'cho_xac_nhan' => 0,
                'da_xac_nhan' => 0,
                'dang_giao' => 0,
                'giao_thanh_cong' => 0,
                'da_huy' => 0,
            ],
        ];
    }
}
