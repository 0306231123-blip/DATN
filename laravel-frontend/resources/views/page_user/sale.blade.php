@extends('layouts.user')

@section('title', 'Sản phẩm Khuyến mãi')

@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <h2 class="text-2xl font-black text-gray-800 text-center mb-10 uppercase tracking-wide">
            Sản phẩm khuyến mãi
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
            @foreach($danhSachKhuyenMai as $sp)
                <a href="/user/detail/{{ $sp->ma_san_pham }}" class="bg-gray-200 aspect-square rounded-3xl flex flex-col items-center justify-center shadow-md p-4 text-center">
                    <span class="text-gray-800 font-bold text-lg z-10 mb-2">{{ $sp->ten_san_pham }}</span>
                        
                    <span class="text-pink-600 font-black text-xl z-10">
                            {{ number_format($sp->gia_khuyen_mai, 0, ',', '.') }} đ
                    </span>
                        
                    <span class="text-gray-400 font-bold text-sm z-10 line-through mt-1">
                            {{ number_format($sp->gia, 0, ',', '.') }} đ
                    </span>
                </a>
            @endforeach
        </div>

    </div>
</section>
@endsection