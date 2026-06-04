<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnhSanPham extends Model
{
    protected $table = 'ANH_SAN_PHAM';
    protected $primaryKey = 'ma_anh';

    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = null;

    protected $fillable = [
        'ma_san_pham', 'duong_dan_anh', 'la_anh_chinh', 'thu_tu'
    ];
    public function anhChinh()
    {
        return $this->hasOne(AnhSanPham::class, 'ma_san_pham', 'ma_san_pham')
                    ->where('la_anh_chinh', 1);
    }
}