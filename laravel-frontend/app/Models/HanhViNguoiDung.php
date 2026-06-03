<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HanhViNguoiDung extends Model
{
    protected $table = 'HANH_VI_NGUOI_DUNG';
    protected $primaryKey = 'ma_hanh_vi';

    const CREATED_AT = 'thoi_gian';
    const UPDATED_AT = null;

    protected $fillable = [
        'ma_nguoi_dung', 'ma_san_pham', 'loai_hanh_dong'
    ];
}