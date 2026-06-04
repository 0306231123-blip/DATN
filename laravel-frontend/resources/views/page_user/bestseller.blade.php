@extends('layouts.user')
@section('title', 'Sản phẩm Bán chạy')
@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <h2 class="text-2xl font-black text-gray-800 text-center mb-10 uppercase tracking-wide">
            Sản phẩm bán chạy
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
            @foreach($danhSachBanChay as $sp)
                <a href="/user/detail/{{ $sp->ma_san_pham }}" class="bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col border border-gray-100 relative">

            <div class="aspect-square bg-gray-50 overflow-hidden relative flex items-center justify-center">
                @php
                    // Mẹo gọi trực tiếp ảnh từ Database cực kỳ an toàn
                    $anh = \App\Models\AnhSanPham::where('ma_san_pham', $sp->ma_san_pham)->where('la_anh_chinh', 1)->first();
                @endphp

                @if($anh)
                    <img src="{{ $anh->duong_dan_anh }}" alt="{{ $sp->ten_san_pham }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                @else
                    <span class="text-gray-400 font-bold text-sm">Chưa có ảnh</span>
                @endif
                
                @if(isset($sp->tong_so_luong_ban))
                    <div class="absolute top-3 right-3 bg-pink-500 text-white text-xs font-bold px-2 py-1 rounded-lg shadow-sm">
                        Đã bán: {{ $sp->tong_so_luong_ban }}
                    </div>
                @endif
            </div>

            <div class="p-4 flex flex-col items-center justify-between flex-1">
                <span class="text-gray-800 font-bold text-sm mb-2 text-center line-clamp-2 h-10">
                    {{ $sp->ten_san_pham }}
                </span>
                
                <div class="flex flex-col items-center justify-end w-full mt-auto">
                    @if($sp->gia_khuyen_mai)
                        <span class="text-pink-600 font-black text-lg">{{ number_format($sp->gia_khuyen_mai, 0, ',', '.') }} đ</span>
                        <span class="text-gray-400 font-bold text-xs line-through">{{ number_format($sp->gia, 0, ',', '.') }} đ</span>
                    @else
                        <span class="text-pink-600 font-black text-lg">{{ number_format($sp->gia, 0, ',', '.') }} đ</span>
                    @endif
                </div>
            </div>
            
        </a>
            @endforeach
        </div>

    </div>
</section>
@endsection