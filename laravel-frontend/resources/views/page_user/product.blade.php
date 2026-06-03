@extends('layouts.user')

@section('title', 'Tất cả Sản phẩm')

@section('content')

<!-- KHU VỰC DANH SÁCH SẢN PHẨM -->
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        
        <!-- Tiêu đề trang -->
        <h2 class="text-2xl font-black text-gray-800 text-center mb-10 uppercase tracking-wide">
            Tất cả sản phẩm
        </h2>
        
        <!-- Khung chứa 15 Sản phẩm (Grid 3 cột) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
            
            @foreach($danhSachSanPham as $sp)
                <a href="/user/detail/{{ $sp->ma_san_pham }}" class="bg-gray-200 aspect-square rounded-3xl flex flex-col items-center justify-center shadow-md hover:-translate-y-2 hover:shadow-xl transition-all duration-300 cursor-pointer overflow-hidden relative p-4 text-center">
                    
                    <span class="text-gray-800 font-bold text-lg z-10 mb-2 line-clamp-2">
                        {{ $sp->ten_san_pham }}
                    </span>
                    
                    <span class="text-pink-600 font-black text-xl z-10">
                        {{ number_format($sp->gia, 0, ',', '.') }} đ
                    </span>
                    
                </a>
            @endforeach

        </div>

        </div>

        

    </div>
</section>

@endsection