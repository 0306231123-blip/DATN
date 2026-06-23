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

<section class="bg-[#fcfdf2] py-4 px-8">
    <div id="ai-guest-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8">
        <div class="bg-white rounded-3xl p-10 text-center border border-pink-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-pink-300 to-purple-400"></div>
            <span class="text-5xl mb-4 block">🔮</span>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Bạn chưa biết sản phẩm nào hợp với mình?</h3>
            <p class="text-gray-600 mb-6 max-w-2xl mx-auto">Hãy để Trí tuệ Nhân tạo (AI) của chúng tôi phân tích và thiết kế chu trình Skincare dành riêng cho làn da của bạn.</p>
            <a href="/login" class="inline-block bg-gray-800 hover:bg-black text-white font-bold py-3 px-8 rounded-xl shadow-md transition transform hover:-translate-y-1">
                Đăng nhập để nhận AI Gợi ý
            </a>
        </div>
    </div>

    <div id="ai-recommendation-section" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 hidden">
        <div class="bg-gradient-to-r from-pink-50 to-white rounded-3xl p-8 border border-pink-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-pink-200 rounded-full opacity-40 blur-2xl"></div>

            <div class="flex items-center justify-between mb-8 relative z-10">
                <h2 class="text-2xl font-black text-gray-800 uppercase tracking-wider flex items-center">
                    <span class="text-3xl mr-3">✨</span>
                    ĐỀ XUẤT CHO BẠN
                </h2>
            </div>

            <div id="ai-products-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 relative z-10">
                <p class="text-gray-500 italic col-span-full animate-pulse font-medium">🤖 Đang kết nối với não bộ AI...</p>
            </div>
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
                                $tongLuot = \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $sp->ma_san_pham)->count();
                                $diemTB = $tongLuot > 0 ? \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $sp->ma_san_pham)->avg('diem_so') : 0;
                                $diemTron = round($diemTB);
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
    document.addEventListener('DOMContentLoaded', async function () {
        // --- 1. CHẠY SWIPER BANNER ---
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            breakpoints: {
                320: { slidesPerView: 1, spaceBetween: 10 },
                768: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 24 },
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

        // --- 2. CHẠY LOGIC AI GỢI Ý ---
        const token = localStorage.getItem('token');
        const aiSection = document.getElementById('ai-recommendation-section');
        const guestSection = document.getElementById('ai-guest-section');
        const productsContainer = document.getElementById('ai-products-container');

        if (token) {
            guestSection.classList.add('hidden');
            aiSection.classList.remove('hidden');

            try {
                const response = await fetch('http://localhost:3000/api/products/ai-suggest', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });

                const result = await response.json();

                if (result.success && result.data && result.data.length > 0) {
                    const skinMap = {
                        'da_dau': 'Da Dầu', 'da_kho': 'Da Khô',
                        'da_hon_hop': 'Da Hỗn Hợp', 'da_nhay_cam': 'Da Nhạy Cảm', 'da_thuong': 'Da Thường'
                    };
                    

                    let html = '';
                    result.data.forEach(sp => {
                        const price = new Intl.NumberFormat('vi-VN').format(sp.gia_khuyen_mai || sp.gia) + ' đ';
                        const img = (sp.danh_sach_anh && sp.danh_sach_anh.length > 0) ? sp.danh_sach_anh[0].duong_dan_anh : 'https://via.placeholder.com/300x300?text=No+Image';

                        // Định dạng thẻ sản phẩm giống hệt sản phẩm nổi bật bên dưới
                        html += `
                            <a href="/user/detail/${sp.ma_san_pham}" class="bg-white rounded-3xl shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 overflow-hidden flex flex-col border border-pink-100 relative group">
                                <div class="absolute top-2 left-2 bg-gradient-to-r from-pink-500 to-purple-500 text-white text-xs font-black px-3 py-1 rounded-full shadow-md z-20 flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"></path></svg>
                                    AI Recommend
                                </div>
                                <div class="aspect-square bg-gray-50 overflow-hidden relative flex items-center justify-center">
                                    <img src="${img}" alt="${sp.ten_san_pham}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-500">
                                </div>
                                <div class="p-4 flex flex-col items-center justify-between flex-1">
                                    <span class="text-gray-800 font-bold text-sm mb-2 text-center line-clamp-2 h-10 group-hover:text-pink-600 transition">
                                        ${sp.ten_san_pham}
                                    </span>
                                    <div class="flex flex-col items-center justify-end w-full mt-auto">
                                        <span class="text-pink-600 font-black text-lg">${price}</span>
                                    </div>
                                </div>
                            </a>
                        `;
                    });
                    
                    productsContainer.innerHTML = html;
                } else {
                    productsContainer.innerHTML = `
                        <div class="col-span-full bg-white p-6 rounded-3xl text-center border border-gray-100 shadow-sm">
                            <span class="text-4xl mb-3 block">📝</span>
                            <p class="text-gray-700 font-bold text-lg mb-2">Chưa đủ dữ liệu để AI phân tích!</p>
                            <p class="text-gray-500 mb-4">Bạn hãy vào Trang Cá Nhân cập nhật "Loại da" để AI hoạt động chính xác nhất nhé.</p>
                            <a href="/profileuser" class="inline-block bg-pink-100 text-pink-700 font-bold py-2 px-6 rounded-xl hover:bg-pink-200 transition">Cập nhật ngay</a>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Lỗi tải AI:', error);
                productsContainer.innerHTML = '<p class="text-red-500 col-span-full text-center font-bold bg-red-50 p-4 rounded-xl">Hệ thống AI đang bảo trì, vui lòng quay lại sau!</p>';
            }
        } else {
            guestSection.classList.remove('hidden');
            aiSection.classList.add('hidden');
        }
    });
</script>

@endsection