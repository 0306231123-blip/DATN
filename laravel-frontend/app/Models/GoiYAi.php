<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoiYAi extends Model
{
    protected $table = 'GOI_Y_AI';
    protected $primaryKey = 'ma_goi_y';

    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = null;

    protected $fillable = [
        'ma_nguoi_dung', 'ma_san_pham', 'diem_goi_y', 
        'ly_do', 'thuat_toan'
    ];
}