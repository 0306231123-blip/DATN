<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhMuc extends Model
{
    protected $table = 'DANH_MUC';
    protected $primaryKey = 'ma_danh_muc';

    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = null; // Bảng này không có cột cập nhật

    protected $fillable = [
        'ten_danh_muc', 'mo_ta', 'ma_danh_muc_cha', 'thu_tu_hien_thi'
    ];
}