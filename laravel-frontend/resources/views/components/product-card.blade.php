{{-- Product Card Component - Dùng chung cho home, product, sale, bestseller --}}
{{-- Truyền vào biến $sp (model SanPham) --}}

<a href="/user/detail/{{ $sp->ma_san_pham }}" class="product-card">
    <div class="product-card__image-wrap">
        @php
            $anh = \App\Models\AnhSanPham::where('ma_san_pham', $sp->ma_san_pham)->where('la_anh_chinh', 1)->first();
        @endphp

        @if($anh)
            <img src="{{ asset($anh->duong_dan_anh) }}" alt="{{ $sp->ten_san_pham }}" class="product-card__img">
        @else
            <img src="{{ asset('images/logo.jpg') }}" alt="No image" class="product-card__img">
        @endif

        @if(isset($sp->tong_so_luong_ban) && $sp->tong_so_luong_ban > 0)
            <div class="product-card__badge-sold">
                Đã bán: {{ $sp->tong_so_luong_ban }}
            </div>
        @endif
    </div>

    <div class="product-card__body">
        <div class="product-card__name">
            {{ $sp->ten_san_pham }}
        </div>

        @include('components.star-rating', ['maSanPham' => $sp->ma_san_pham])

        <div class="price-wrap">
            @if($sp->gia_khuyen_mai)
                <span class="price-current">{{ number_format($sp->gia_khuyen_mai, 0, ',', '.') }} đ</span>
                <span class="price-original">{{ number_format($sp->gia, 0, ',', '.') }} đ</span>
            @else
                <span class="price-current">{{ number_format($sp->gia, 0, ',', '.') }} đ</span>
            @endif
        </div>
    </div>
</a>
