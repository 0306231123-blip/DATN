<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    protected $table = 'DON_HANG';
    protected $primaryKey = 'ma_don_hang';

    const CREATED_AT = 'ngay_dat';
    const UPDATED_AT = 'ngay_cap_nhat';

    protected $fillable = [
        'ma_nguoi_dung', 'ho_ten_nguoi_nhan', 'so_dien_thoai_nhan', 
        'dia_chi_giao', 'tong_tien_hang', 'phi_van_chuyen', 
        'tong_thanh_toan', 'ghi_chu', 'phuong_thuc_thanh_toan', 
        'trang_thai_thanh_toan', 'trang_thai_don'
    ];
}