<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class NguoiDung extends Authenticatable
{
    use Notifiable;

    protected $table = 'NGUOI_DUNG';
    protected $primaryKey = 'ma_nguoi_dung';

    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = 'ngay_cap_nhat';

    protected $fillable = [
        'ho_ten', 'email', 'mat_khau', 'so_dien_thoai', 
        'dia_chi', 'loai_da', 'vai_tro', 'trang_thai'
    ];

    protected $hidden = [
        'mat_khau',
    ];
}