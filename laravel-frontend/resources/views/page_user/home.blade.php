@extends('layouts.user')
@section('title', 'Trang chủ')
@section('content')

<section class="bg-[#fcfdf2] pt-12 pb-4 px-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 mb-8">
        
        <div class="swiper mySwiper group relative px-2 pb-12">
            <div class="swiper-wrapper">
                
                <div class="swiper-slide">
                    <img src="{{ asset('images/qc1.jpg') }}" alt="QC 1" class="w-full h-[220px] md:h-[320px] object-cover rounded-2xl shadow hover:shadow-md transition">
                </div>
                
                <div class="swiper-slide">
                    <img src="{{ asset('images/qc2.jpg') }}" alt="QC 2" class="w-full h-[220px] md:h-[320px] object-cover rounded-2xl shadow hover:shadow-md transition">
                </div>
                
                <div class="swiper-slide">
                    <img src="{{ asset('images/qc3.jpg') }}" alt="QC 3" class="w-full h-[220px] md:h-[320px] object-cover rounded-2xl shadow hover:shadow-md transition">
                </div>
                
                <div class="swiper-slide">
                    <img src="{{ asset('images/qc4.jpg') }}" alt="QC 4" class="w-full h-[220px] md:h-[320px] object-cover rounded-2xl shadow hover:shadow-md transition">
                </div>
                
            </div>
            <div class="swiper-pagination"></div>
        </div>  
        
    </div>
</section>

<section class="bg-[#fcfdf2] py-8 px-8">
    <div class="max-w-7xl mx-auto">
        
        <h2 class="text-center font-black text-2xl text-gray-800 mb-8 uppercase tracking-widest">Sản phẩm nổi bật</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
            
            @foreach($sanPhamNoiBat as $sp)
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
                    @php
                        // Đếm tổng số đánh giá và tính điểm trung bình
                        $tongLuot = \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $sp->ma_san_pham)->count();
                        $diemTB = $tongLuot > 0 ? \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $sp->ma_san_pham)->avg('diem_so') : 0;
                        $diemTron = round($diemTB); // Làm tròn để in màu sao
                    @endphp

                    <div class="flex items-center justify-center mt-1 mb-2">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $diemTron)
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                @else
                                    <svg class="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xs text-gray-400 ml-1">
                            @if($tongLuot > 0)
                                ({{ $tongLuot }})
                            @else
                                (Chưa có)
                            @endif
                        </span>
                    </div>
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

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 3500, // 3.5 giây tự chuyển ảnh
                disableOnInteraction: false,
            },
            // CẤU HÌNH HIỂN THỊ SỐ LƯỢNG ẢNH
            breakpoints: {
                // Điện thoại: 1 ảnh
                320: {
                    slidesPerView: 1,
                    spaceBetween: 10,
                },
                // Ipad: 2 ảnh
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                // Desktop: 3 ảnh
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 24,
                },
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });
    });
</script>

@endsection