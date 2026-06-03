<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    protected $table = 'CHI_TIET_DON_HANG';
    protected $primaryKey = 'ma_chi_tiet';

    // Bảng này hoàn toàn không có cột thời gian nào
    public $timestamps = false;

    protected $fillable = [
        'ma_don_hang', 'ma_san_pham', 'ten_san_pham', 
        'don_gia', 'so_luong', 'thanh_tien'
    ];
}