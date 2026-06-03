<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'DANH_GIA';
    protected $primaryKey = 'ma_danh_gia';

    const CREATED_AT = 'ngay_viet';
    const UPDATED_AT = null;

    protected $fillable = [
        'ma_nguoi_dung', 'ma_san_pham', 'ma_don_hang', 
        'diem_so', 'noi_dung', 'trang_thai'
    ];
}