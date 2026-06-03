<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    protected $table = 'GIO_HANG';
    protected $primaryKey = 'ma_gio_hang';

    const CREATED_AT = 'ngay_them';
    const UPDATED_AT = null;

    protected $fillable = [
        'ma_nguoi_dung', 'ma_san_pham', 'so_luong'
    ];
}