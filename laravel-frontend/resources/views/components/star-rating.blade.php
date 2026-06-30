{{-- Star Rating Component - Dùng chung cho product card và detail --}}
{{-- Truyền vào biến $maSanPham --}}

@php
    $tongLuot = \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $maSanPham)->count();
    $diemTB = $tongLuot > 0 ? \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $maSanPham)->avg('diem_so') : 0;
    $diemTron = round($diemTB);
@endphp

<div class="star-rating">
    <div class="star-rating__stars">
        @for($i = 1; $i <= 5; $i++)
            @if($i <= $diemTron)
                <svg class="star-rating__icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            @else
                <svg class="star-rating__icon star-rating__icon--empty" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            @endif
        @endfor
    </div>
    <span class="star-rating__count">({{ $tongLuot > 0 ? $tongLuot : 'Chưa có' }})</span>
</div>
