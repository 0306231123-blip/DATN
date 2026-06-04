<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    // 1. Chỉ định chính xác tên bảng trong Database
    protected $table = 'SAN_PHAM';

    // 2. Chỉ định khóa chính (vì mặc định Laravel tìm cột 'id')
    protected $primaryKey = 'ma_san_pham';

    // 3. Vì bạn dùng 'ngay_tao' và 'ngay_cap_nhat' thay vì 'created_at', 'updated_at'
    const CREATED_AT = 'ngay_tao';
    const UPDATED_AT = 'ngay_cap_nhat';

    // 4. Cho phép Laravel thêm dữ liệu vào các cột này
    protected $fillable = [
        'ten_san_pham', 'mo_ta', 'thanh_phan', 'huong_dan_su_dung',
        'gia', 'so_luong_ton', 'thuong_hieu', 'xuat_xu', 
        'ma_danh_muc', 'loai_da_phu_hop', 'diem_danh_gia', 
        'so_luot_danh_gia', 'trang_thai'
    ];
    public function anhChinh()
    {
        return $this->hasOne(AnhSanPham::class, 'ma_san_pham', 'ma_san_pham')
                    ->where('la_anh_chinh', 1);
    }
}