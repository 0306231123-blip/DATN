@extends('layouts.user')

@section('title', 'Trang chủ')

@section('content')

<section class="bg-gray-300 py-12 px-8">
    <div class="max-w-7xl mx-auto">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-400 h-48 rounded-2xl flex items-center justify-center shadow-sm hover:shadow-md transition cursor-pointer">
                <span class="text-gray-700 font-bold text-lg">Quảng cáo 1</span>
            </div>
            <div class="bg-gray-400 h-48 rounded-2xl flex items-center justify-center shadow-sm hover:shadow-md transition cursor-pointer">
                <span class="text-gray-700 font-bold text-lg">Quảng cáo 2</span>
            </div>
            <div class="bg-gray-400 h-48 rounded-2xl flex items-center justify-center shadow-sm hover:shadow-md transition cursor-pointer">
                <span class="text-gray-700 font-bold text-lg">Quảng cáo 3</span>
            </div>
        </div>
    </div>
</section>

<section class="bg-[#fcfdf2] py-16 px-8">
    <div class="max-w-7xl mx-auto">
        
        <h2 class="text-center font-black text-2xl text-gray-800 mb-8 uppercase tracking-widest">Sản phẩm nổi bật</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
            
            @foreach($sanPhamNoiBat as $sp)
                <a href="/user/detail/{{ $sp->ma_san_pham }}" class="bg-gray-200 aspect-square rounded-3xl flex flex-col items-center justify-center shadow-md hover:-translate-y-2 hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden relative p-4 text-center">
                    
                    <div class="absolute top-4 right-4 bg-white text-yellow-500 text-xs font-black px-2 py-1 rounded-lg shadow-sm flex items-center">
                        ★ {{ $sp->diem_danh_gia }}
                    </div>

                    <span class="text-gray-800 font-bold text-lg z-10 mb-2 line-clamp-2">
                        {{ $sp->ten_san_pham }}
                    </span>
                    
                    @if($sp->gia_khuyen_mai)
                        <span class="text-pink-600 font-black text-xl z-10">
                            {{ number_format($sp->gia_khuyen_mai, 0, ',', '.') }} đ
                        </span>
                        <span class="text-gray-400 font-bold text-sm z-10 line-through mt-1">
                            {{ number_format($sp->gia, 0, ',', '.') }} đ
                        </span>
                    @else
                        <span class="text-pink-600 font-black text-xl z-10">
                            {{ number_format($sp->gia, 0, ',', '.') }} đ
                        </span>
                    @endif
                </a>
            @endforeach

        </div>

    </div>
</section>

@endsection