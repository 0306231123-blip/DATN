<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * URL gốc của Node.js API backend
     */
    private const API_BASE_URL = 'http://localhost:3000/api';

    /**
     * Hiển thị trang Dashboard tổng quan
     */
    public function index(Request $request)
    {
        try {
            $token = $request->cookie('admin_token') ?? $request->cookie('token');
            $client = Http::timeout(10);

            if ($token) {
                $client = $client->withToken($token);
            }

            $response = $client->get(self::API_BASE_URL . '/dashboard/stats');

            if ($response->successful()) {
                $data = $response->json('data');
            } else {
                Log::error('Dashboard API Error (Response): ' . $response->body());
                $data = $this->getEmptyData();
            }
        } catch (\Exception $e) {
            Log::error('Dashboard API Error (Exception): ' . $e->getMessage());
            $data = $this->getEmptyData();
        }

        return view('admin.dashboard', compact('data'));
    }

    /**
     * Cấu trúc dữ liệu rỗng (fallback khi không kết nối được backend)
     */
    private function getEmptyData(): array
    {
        return [
            'stats' => [
                'tong_doanh_thu'     => 0,
                'tong_don_hang'      => 0,
                'khach_hang_moi'     => 0,
                'san_pham_dang_ban'  => 0,
                'danh_gia_trung_binh' => 0,
            ],
            'doanh_thu_theo_thang'     => [],
            'doanh_thu_theo_danh_muc'  => [],
            'san_pham_ban_chay'        => [],
            'don_hang_gan_day'         => [],
            'thong_ke_trang_thai_don'  => [
                'cho_xac_nhan'    => 0,
                'da_xac_nhan'     => 0,
                'dang_giao'       => 0,
                'giao_thanh_cong' => 0,
                'da_huy'          => 0,
            ],
        ];
    }
}
