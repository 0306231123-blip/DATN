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
                <a href="/user/detail/{{ $sp->ma_san_pham }}" class="bg-gray-200 aspect-square rounded-3xl flex flex-col items-center justify-center shadow-md p-4 text-center relative">
                    <div class="absolute top-4 right-4 bg-pink-500 text-white text-xs font-bold px-2 py-1 rounded-lg shadow-sm">
                        Đã bán: {{ $sp->tong_so_luong_ban }}
                    </div>

                <span class="text-gray-800 font-bold text-lg z-10 mb-2">{{ $sp->ten_san_pham }}</span>
                <span class="text-pink-600 font-black text-xl z-10">
                    {{ number_format($sp->gia, 0, ',', '.') }} đ
                </span>
                </a>
            @endforeach
        </div>

    </div>
</section>
@endsection