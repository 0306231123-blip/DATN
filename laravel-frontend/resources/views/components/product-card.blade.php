{{-- Product Card Component - Dùng chung cho home, product, sale, bestseller --}}
{{-- Truyền vào biến $sp (model SanPham) --}}

<a href="/user/detail/{{ $sp->ma_san_pham }}" class="product-card group relative block bg-white border border-transparent hover:border-pink-500 hover:shadow-md hover:-translate-y-[1px] transition-all duration-200 {{ $sp->so_luong_ton == 0 ? 'opacity-60 grayscale hover:grayscale-0' : '' }}">
    <div class="product-card__image-wrap overflow-hidden relative">
        @if($sp->so_luong_ton == 0)
            <div class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none">
                <span class="bg-black/70 text-white font-bold py-1.5 px-4 rounded-full border border-white/20 shadow-lg tracking-wider text-sm uppercase backdrop-blur-sm">Hết Hàng</span>
            </div>
        @endif
        @php
            $anh = \App\Models\AnhSanPham::where('ma_san_pham', $sp->ma_san_pham)->where('la_anh_chinh', 1)->first();
        @endphp

        @if($anh)
            <img src="{{ asset($anh->duong_dan_anh) }}" alt="{{ $sp->ten_san_pham }}" class="product-card__img w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @else
            <img src="{{ asset('images/logo.jpg') }}" alt="No image" class="product-card__img w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
        @endif

        {{-- Badges: Nằm đè lên ảnh --}}
        <div class="absolute top-2 left-2 flex flex-col gap-1 z-10">
            @if($sp->gia_khuyen_mai && $sp->gia > 0)
                <span class="bg-red-500 text-white text-[10px] md:text-xs font-bold px-1.5 py-0.5 shadow-sm">
                    GIẢM {{ round((($sp->gia - $sp->gia_khuyen_mai) / $sp->gia) * 100) }}%
                </span>
            @endif
            @if(isset($sp->tong_so_luong_ban) && $sp->tong_so_luong_ban > 50)
                <span class="bg-yellow-400 text-gray-900 text-[10px] md:text-xs font-bold px-1.5 py-0.5 shadow-sm">
                    HOT 🔥
                </span>
            @elseif(isset($sp->ngay_tao) && \Carbon\Carbon::parse($sp->ngay_tao)->diffInDays(now()) <= 2)
                <span class="bg-green-500 text-white text-[10px] md:text-xs font-bold px-1.5 py-0.5 shadow-sm">
                    MỚI ✨
                </span>
            @endif
        </div>

        @if(isset($sp->tong_so_luong_ban) && $sp->tong_so_luong_ban > 0)
            <div class="absolute top-2 right-2 bg-gray-900/80 text-white text-[10px] font-medium px-1.5 py-0.5">
                Đã bán {{ $sp->tong_so_luong_ban }}
            </div>
        @endif


    </div>

    <div class="product-card__body p-3">
        <div class="product-card__name text-gray-800 text-xs md:text-sm line-clamp-2 mb-2 group-hover:text-pink-600 transition">
            {{ $sp->ten_san_pham }}
        </div>

        @include('components.star-rating', ['maSanPham' => $sp->ma_san_pham])

        <div class="price-wrap mt-2 flex flex-col justify-end flex-grow">
            @if($sp->gia_khuyen_mai)
                <span class="price-current text-pink-600 font-medium text-base">{{ number_format($sp->gia_khuyen_mai, 0, ',', '.') }} đ</span>
                <span class="price-original text-gray-400 text-xs line-through mt-0.5">{{ number_format($sp->gia, 0, ',', '.') }} đ</span>
            @else
                <span class="price-current text-pink-600 font-medium text-base">{{ number_format($sp->gia, 0, ',', '.') }} đ</span>
            @endif
        </div>
    </div>
</a>
