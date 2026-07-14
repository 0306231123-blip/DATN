@extends('layouts.user')
@section('title', 'Trang chủ')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
@endsection

@section('content')

{{-- Banner Swiper --}}
<section class="home-banner mb-10 w-full">
    <div class="swiper mySwiper rounded-sm overflow-hidden shadow-md group">
        <div class="swiper-wrapper">
            <div class="swiper-slide relative">
                <img src="{{ asset('images/qc1.jpg') }}" alt="QC 1" class="w-full h-[200px] md:h-[260px] lg:h-[320px] object-cover transition-transform duration-1000 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
            </div>
            <div class="swiper-slide relative">
                <img src="{{ asset('images/qc2.jpg') }}" alt="QC 2" class="w-full h-[200px] md:h-[260px] lg:h-[320px] object-cover transition-transform duration-1000 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
            </div>
            <div class="swiper-slide relative">
                <img src="{{ asset('images/qc3.jpg') }}" alt="QC 3" class="w-full h-[200px] md:h-[260px] lg:h-[320px] object-cover transition-transform duration-1000 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
            </div>
            <div class="swiper-slide relative">
                <img src="{{ asset('images/qc4.jpg') }}" alt="QC 4" class="w-full h-[200px] md:h-[260px] lg:h-[320px] object-cover transition-transform duration-1000 group-hover:scale-105">
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
            </div>
        </div>
        <!-- Pagination -->
        <div class="swiper-pagination !bottom-4"></div>
        <!-- Navigation Buttons -->
        <div class="swiper-button-next !text-white !opacity-0 group-hover:!opacity-100 transition-opacity drop-shadow-md hidden md:flex"></div>
        <div class="swiper-button-prev !text-white !opacity-0 group-hover:!opacity-100 transition-opacity drop-shadow-md hidden md:flex"></div>
    </div>  
</section>

{{-- Danh Muc Section --}}
<section class="home-category mb-12">
    <div class="w-full">
        <div class="flex items-center mb-6">
            <span class="text-3xl mr-3">📂</span>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-widest">Danh Mục Sản Phẩm</h2>
        </div>
        <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-4 text-center">
            @foreach($danhMucs as $dm)
                <a href="/user/product?danh_muc={{ $dm->ma_danh_muc }}" 
                   @if($dm->so_luong_sp == 0)
                   onclick="alert('Hiện tại danh mục {{ $dm->ten_danh_muc }} chưa có sản phẩm nào. Bạn vui lòng thêm sản phẩm vào danh mục này sau nhé!'); return false;"
                   @endif
                   class="bg-white rounded-sm shadow-sm border border-pink-50 p-4 flex flex-col items-center justify-center hover:shadow-md hover:-translate-y-[1px] transition-all group">
                    <div class="w-16 h-16 rounded-full bg-gradient-to-br from-pink-50 to-pink-100 flex items-center justify-center mb-3 shadow-inner relative">
                        <span class="text-3xl group-hover:scale-110 transition-transform">
                            @php
                                $name = mb_strtolower($dm->ten_danh_muc);
                                if(str_contains($name, 'son') || str_contains($name, 'môi')) echo '💄';
                                elseif(str_contains($name, 'da') || str_contains($name, 'skin') || str_contains($name, 'mặt') || str_contains($name, 'rửa')) echo '🧴';
                                elseif(str_contains($name, 'tẩy trang')) echo '💧';
                                elseif(str_contains($name, 'chống nắng')) echo '☀️';
                                elseif(str_contains($name, 'trang điểm') || str_contains($name, 'makeup') || str_contains($name, 'nền') || str_contains($name, 'phấn')) echo '🎨';
                                elseif(str_contains($name, 'mắt') || str_contains($name, 'mascara')) echo '👁️';
                                elseif(str_contains($name, 'tóc') || str_contains($name, 'dưỡng tóc')) echo '💇‍♀️';
                                elseif(str_contains($name, 'toàn thân') || str_contains($name, 'body')) echo '🛀';
                                else echo '🛍️';
                            @endphp
                        </span>
                    </div>
                    <span class="text-sm text-gray-700 font-bold group-hover:text-pink-600 transition-colors line-clamp-2 leading-snug">{{ $dm->ten_danh_muc }}</span>
                </a>
            @endforeach
        </div>
</section>

{{-- Voucher Section --}}
<section class="home-voucher mb-12">
    <div class="w-full">
        <div class="flex items-center mb-6">
            <span class="text-3xl mr-3">🎫</span>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-widest">Kho Voucher</h2>
        </div>
        
        <div id="voucher-list" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <p class="text-gray-500 font-medium user-pulse col-span-full">Đang tìm kiếm mã giảm giá dành cho bạn...</p>
        </div>
    </div>
</section>

{{-- AI Section --}}
<section class="home-ai mb-12">
    {{-- Guest (chưa đăng nhập) --}}
    <div id="ai-guest-section" class="w-full">
        <div class="ai-guest-card bg-gradient-to-r from-pink-50 to-purple-50 p-8 rounded-sm border border-transparent hover:border-pink-500 shadow-sm text-center">
            <span class="text-5xl block mb-4">🔮</span>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Bạn chưa biết sản phẩm nào hợp với mình?</h3>
            <p class="text-gray-600 mb-6 max-w-2xl mx-auto">Hãy để Trí tuệ Nhân tạo (AI) của chúng tôi phân tích và thiết kế chu trình Skincare dành riêng cho làn da của bạn.</p>
            <a href="/login" class="inline-block bg-gray-900 text-white font-bold py-3 px-8 rounded-full hover:bg-pink-500 transition shadow-md">
                Đăng nhập để nhận AI Gợi ý
            </a>
        </div>
    </div>

</section>

{{-- AI NEXT STEP RECOMMENDATION --}}
<section id="ai-next-step-section" class="mb-12 bg-gradient-to-br from-pink-50 to-orange-50 p-6 rounded-sm border border-transparent hover:border-pink-500 shadow-sm relative overflow-hidden hidden">
    <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">🤖</div>
    <div class="flex items-center mb-4 relative z-10">
        <span class="text-3xl mr-3 animate-pulse">💡</span>
        <h2 class="text-2xl font-black text-pink-600 uppercase tracking-widest">Gợi Ý Riêng Cho Bạn</h2>
    </div>
    
    <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Cột 1: Sản phẩm đi kèm -->
        <div id="ai-companion-block" class="hidden flex-col gap-3">
            <h3 class="font-bold text-lg text-pink-600 border-b border-pink-200 pb-2">Sản Phẩm Đi Kèm</h3>
            <div class="bg-white p-3 rounded-sm shadow-sm border border-pink-50 relative min-h-[60px]">
                <div class="absolute -left-2 -top-2 text-xl">✨</div>
                <div id="ai-companion-reason" class="text-gray-700 italic font-medium leading-relaxed text-sm">
                </div>
            </div>
            <div id="ai-companion-product" class="flex gap-3 overflow-x-auto pb-2 custom-scrollbar">
            </div>
        </div>

        <!-- Cột 2: Các bước còn thiếu -->
        <div id="ai-missing-block" class="hidden flex-col gap-3">
            <h3 class="font-bold text-lg text-orange-600 border-b border-orange-200 pb-2">Các Bước Còn Thiếu</h3>
            <div class="bg-white p-3 rounded-sm shadow-sm border border-orange-50 relative min-h-[60px]">
                <div class="absolute -left-2 -top-2 text-xl">✨</div>
                <div id="ai-next-step-reason" class="text-gray-700 italic font-medium leading-relaxed text-sm">
                </div>
            </div>
            <div id="ai-next-step-product" class="flex gap-3 overflow-x-auto pb-2 custom-scrollbar">
            </div>
        </div>
        
        <!-- Skeleton Loader (Chung khi đang load) -->
        <div id="ai-next-step-skeleton" class="col-span-1 md:col-span-2 flex gap-6 w-full">
            <div class="flex-1">
                <div class="bg-white p-4 rounded-sm shadow-sm border border-pink-50 relative min-h-[80px]">
                    <div class="animate-pulse flex flex-col gap-2">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                    </div>
                </div>
            </div>
            <div class="w-full md:w-1/2 flex-shrink-0 min-h-[150px]">
                <div class="bg-white p-2 flex flex-col h-full group relative border border-gray-100 rounded-sm animate-pulse">
                    <div class="relative w-full aspect-square mb-3 overflow-hidden rounded-sm bg-gray-200"></div>
                </div>
            </div>
        </div>
    </div>
</section>




{{-- AI SKIN TYPE RECOMMENDATION --}}
<section id="ai-skin-type-section" class="mb-12 bg-gradient-to-br from-green-50 to-teal-50 p-6 rounded-sm border border-transparent hover:border-teal-500 shadow-sm relative overflow-hidden hidden">
    <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">🌿</div>
    <div class="flex items-center mb-4 relative z-10">
        <span class="text-3xl mr-3 animate-pulse">✨</span>
        <h2 class="text-2xl font-black text-teal-600 uppercase tracking-widest">Chăm Sóc Dành Riêng Cho <span id="skin-type-label" class="text-green-600">...</span></h2>
    </div>
    <div class="relative z-10 flex flex-col md:flex-row gap-6 items-center">
        <div class="flex-1">
            <div class="bg-white p-4 rounded-sm shadow-sm border border-teal-50 relative min-h-[80px]">
                <div class="absolute -left-2 -top-2 text-2xl">💧</div>
                <div id="ai-skin-type-reason" class="text-gray-700 italic font-medium leading-relaxed">
                     <!-- Skeleton Loader -->
                     <div class="animate-pulse flex flex-col gap-2">
                        <div class="h-4 bg-gray-200 rounded w-full"></div>
                        <div class="h-4 bg-gray-200 rounded w-4/5"></div>
                        <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="ai-skin-type-product" class="w-full md:flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 custom-scrollbar overflow-x-auto min-h-[250px]">
            <!-- Skeleton Loader for Products (Show 3) -->
            <div class="bg-white p-2 flex flex-col h-full group relative border border-gray-100 rounded-sm animate-pulse min-w-[150px]">
                <div class="relative w-full aspect-square mb-3 overflow-hidden rounded-sm bg-gray-200"></div>
                <div class="h-4 bg-gray-200 rounded mb-2 w-full"></div>
                <div class="h-4 bg-gray-200 rounded mb-2 w-1/2"></div>
            </div>
            <div class="bg-white p-2 flex flex-col h-full group relative border border-gray-100 rounded-sm animate-pulse min-w-[150px]">
                <div class="relative w-full aspect-square mb-3 overflow-hidden rounded-sm bg-gray-200"></div>
                <div class="h-4 bg-gray-200 rounded mb-2 w-full"></div>
                <div class="h-4 bg-gray-200 rounded mb-2 w-1/2"></div>
            </div>
            <div class="bg-white p-2 flex flex-col h-full group relative border border-gray-100 rounded-sm animate-pulse min-w-[150px]">
                <div class="relative w-full aspect-square mb-3 overflow-hidden rounded-sm bg-gray-200"></div>
                <div class="h-4 bg-gray-200 rounded mb-2 w-full"></div>
                <div class="h-4 bg-gray-200 rounded mb-2 w-1/2"></div>
            </div>
        </div>
    </div>
</section>

{{-- Featured Products --}}
<section class="home-featured mb-12">
    <div class="w-full">
        <div class="flex items-center mb-6">
            <span class="text-3xl mr-3">🔥</span>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-widest">Sản phẩm nổi bật</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
            @foreach($sanPhamNoiBat as $sp)
                @include('components.product-card', ['sp' => $sp])
            @endforeach
        </div>
    </div>
</section>

{{-- Daily Discover / Gợi Ý Hôm Nay --}}
<section class="home-daily-discover mb-12 pt-4 pb-8 mt-8">
    <div class="w-full">
        <!-- Shopee-style Tab Header -->
        <div class="flex justify-center mb-6 bg-white pt-4 border-b border-pink-500 shadow-sm sticky top-20 z-40">
            <div class="relative text-center pb-3 px-8">
                <h2 class="text-xl font-bold text-pink-500 uppercase tracking-wider">Gợi Ý Hôm Nay</h2>
                <div class="absolute bottom-0 left-0 w-full h-1 bg-pink-500"></div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
            @foreach($sanPhamHomNay as $sp)
                @include('components.product-card', ['sp' => $sp])
            @endforeach
        </div>
        
        <!-- View More Button -->
        <div class="mt-8 flex justify-center">
            <a href="/user/product" class="bg-white text-gray-600 border border-gray-300 hover:bg-gray-50 hover:text-pink-500 font-medium py-2 px-12 rounded transition shadow-sm">
                Xem thêm
            </a>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', async function () {
        
        // --- 1. CHẠY SWIPER BANNER (HIỆN 3 ẢNH) ---
        var swiper = new Swiper(".mySwiper", {
            loop: true,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            // Đây là phần chia cột theo thiết bị
            breakpoints: {
                320: { slidesPerView: 1, spaceBetween: 15 }, // Điện thoại: 1 ảnh
                768: { slidesPerView: 2, spaceBetween: 20 }, // Tablet: 2 ảnh
                1024: { slidesPerView: 3, spaceBetween: 24 } // Máy tính: 3 ảnh
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
        });

        // --- 2. CHẠY LOGIC AI GỢI Ý BƯỚC TIẾP THEO ---
        const token = localStorage.getItem('token');
        const aiSection = document.getElementById('ai-next-step-section');
        const reasonEl = document.getElementById('ai-next-step-reason');
        const productEl = document.getElementById('ai-next-step-product');
        const guestSection = document.getElementById('ai-guest-section');

        if (token) {
            if (guestSection) guestSection.classList.add('hidden');
            try {
                // Lấy thông tin user từ token để lấy user_id
                const base64Url = token.split('.')[1];
                const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                }).join(''));
                const payload = JSON.parse(jsonPayload);
                const userId = payload.ma_nguoi_dung;

                if(userId) {
                    
                    // --- FETCH SKIN TYPE SUGGESTION ---
                    const skinSection = document.getElementById('ai-skin-type-section');
                    const skinReasonEl = document.getElementById('ai-skin-type-reason');
                    const skinProductEl = document.getElementById('ai-skin-type-product');
                    const skinTypeLabel = document.getElementById('skin-type-label');
                    
                    if (skinSection) skinSection.classList.remove('hidden');

                    fetch('http://localhost:5000/api/ai-skin-type-suggest', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ user_id: userId })
                    }).then(res => res.json()).then(result => {
                        if (result.success && result.products && result.products.length > 0) {
                            skinTypeLabel.innerText = result.loai_da_text;
                            skinReasonEl.innerHTML = result.reason;
                            
                            let productsHtml = '';
                            result.products.forEach(sp => {
                                const price = new Intl.NumberFormat('vi-VN').format(sp.gia_khuyen_mai || sp.gia) + ' đ';
                                let img = sp.hinh_anh_url ? sp.hinh_anh_url : 'https://via.placeholder.com/300x300?text=No+Image';
                                if (!img.startsWith('http')) {
                                    img = img.startsWith('/') ? img : '/' + img;
                                }
                                const tagName = sp.ten_danh_muc ? sp.ten_danh_muc : 'Gợi ý cho bạn';

                                productsHtml += `
                                    <a href="/user/detail/${sp.ma_san_pham}" class="bg-white p-2 hover:shadow-md hover:-translate-y-[1px] transition-all duration-200 flex flex-col h-full group relative border border-transparent hover:border-teal-500 flex-1 min-w-[150px]">
                                        <div class="absolute top-0 right-0 z-20 bg-gradient-to-r from-teal-500 to-green-400 text-white text-[10px] sm:text-xs font-bold px-3 py-1 rounded-bl-sm shadow-md">
                                            ${tagName}
                                        </div>
                                        <div class="relative w-full aspect-square mb-3 overflow-hidden rounded-sm bg-gray-50">
                                            <img src="${img}" alt="${sp.ten_san_pham}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        </div>
                                        <div class="flex-grow flex flex-col justify-between">
                                            <h3 class="font-bold text-gray-800 text-sm line-clamp-2 mb-1 group-hover:text-teal-600 transition">
                                                ${sp.ten_san_pham}
                                            </h3>
                                            <div class="text-teal-600 font-medium text-base">
                                                ${price}
                                            </div>
                                        </div>
                                    </a>
                                `;
                            });
                            
                            if (skinProductEl) skinProductEl.innerHTML = productsHtml;
                        } else {
                            if (skinSection) skinSection.classList.add('hidden');
                        }
                    }).catch(err => {
                        console.error('Lỗi tải AI Skin Type:', err);
                        if (skinSection) skinSection.classList.add('hidden');
                    });
                    
                    if (aiSection) aiSection.classList.remove('hidden');
                    const response = await fetch('http://localhost:5000/api/ai-next-step-suggest', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ user_id: userId })
                    });

                    const result = await response.json();
                    
                    const skeleton = document.getElementById('ai-next-step-skeleton');
                    if (skeleton) skeleton.classList.add('hidden');
                    
                    if (result.success && (result.missing.products.length > 0 || result.companion.products.length > 0)) {
                        
                        // Hàm tạo HTML thẻ sản phẩm thu nhỏ
                        const createProductCards = (products, colorClass, borderClass, isCompanion = false) => {
                            let html = '';
                            products.forEach(sp => {
                                const price = new Intl.NumberFormat('vi-VN').format(sp.gia_khuyen_mai || sp.gia) + ' đ';
                                let img = sp.duong_dan_anh ? sp.duong_dan_anh : 'https://via.placeholder.com/300x300?text=No+Image';
                                if (!img.startsWith('http')) {
                                    img = img.startsWith('/') ? img : '/' + img;
                                }
                                const tagName = sp.ten_danh_muc ? sp.ten_danh_muc : (isCompanion ? 'Đi kèm hoàn hảo' : 'Mảnh ghép hoàn hảo');
                                
                                html += `
                                    <a href="/user/detail/${sp.ma_san_pham}" class="bg-white p-2 hover:shadow-md hover:-translate-y-[1px] transition-all duration-200 flex flex-col h-full group relative border border-transparent ${borderClass} min-w-[140px] max-w-[160px] flex-shrink-0">
                                        <div class="absolute top-0 right-0 z-20 bg-gradient-to-r from-${colorClass}-500 to-${colorClass}-400 text-white text-[9px] font-bold px-2 py-0.5 rounded-bl-sm shadow-md">
                                            ${tagName}
                                        </div>
                                        <div class="relative w-full aspect-square mb-2 overflow-hidden rounded-sm bg-gray-50">
                                            <img src="${img}" alt="${sp.ten_san_pham}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        </div>
                                        <div class="flex-grow flex flex-col justify-between">
                                            <h3 class="font-bold text-gray-800 text-[11px] leading-tight line-clamp-2 mb-1 group-hover:text-${colorClass}-600 transition">
                                                ${sp.ten_san_pham}
                                            </h3>
                                            <div class="text-${colorClass}-600 font-medium text-xs mb-1">
                                                ${price}
                                            </div>
                                            ${isCompanion && sp.for_product ? `<div class="text-[9px] text-gray-500 italic border-t border-gray-100 pt-1 mt-1 leading-tight">Đi kèm cho:<br><span class="font-semibold text-gray-600">${sp.for_product}</span></div>` : ''}
                                        </div>
                                    </a>
                                `;
                            });
                            return html;
                        };
                        
                        // Xử lý cột Sản phẩm đi kèm
                        if (result.companion && result.companion.products && result.companion.products.length > 0) {
                            const companionBlock = document.getElementById('ai-companion-block');
                            const companionReason = document.getElementById('ai-companion-reason');
                            const companionProduct = document.getElementById('ai-companion-product');
                            
                            companionBlock.classList.remove('hidden');
                            companionBlock.classList.add('flex');
                            companionReason.innerHTML = result.companion.reason;
                            companionProduct.innerHTML = createProductCards(result.companion.products, 'pink', 'hover:border-pink-500', true);
                        }
                        
                        // Xử lý cột Các bước còn thiếu
                        if (result.missing && result.missing.products && result.missing.products.length > 0) {
                            const missingBlock = document.getElementById('ai-missing-block');
                            const missingReason = document.getElementById('ai-next-step-reason');
                            const missingProduct = document.getElementById('ai-next-step-product');
                            
                            missingBlock.classList.remove('hidden');
                            missingBlock.classList.add('flex');
                            missingReason.innerHTML = result.missing.reason;
                            missingProduct.innerHTML = createProductCards(result.missing.products, 'orange', 'hover:border-orange-500', false);
                        }

                    } else {
                        const missingBlock = document.getElementById('ai-missing-block');
                        missingBlock.classList.remove('hidden');
                        missingBlock.classList.add('flex');
                        document.getElementById('ai-next-step-reason').innerHTML = "Có vẻ bạn là khách hàng mới! Hãy trải nghiệm mua sắm tại shop hoặc chờ hệ thống AI thu thập thêm dữ liệu để có thể đưa ra những gợi ý chính xác nhất cho chu trình Skincare của bạn nhé!";
                        document.getElementById('ai-next-step-product').innerHTML = `
                            <div class="bg-white border-2 border-dashed border-pink-200 rounded-sm p-6 h-full flex flex-col items-center justify-center text-center opacity-70 w-full min-w-[200px]">
                                <span class="text-4xl mb-2 grayscale">🛒</span>
                                <p class="text-pink-600 font-bold text-sm">Chờ đón đơn hàng đầu tiên</p>
                            </div>
                        `;
                    }
                }
            } catch (error) {
                console.error('Lỗi tải AI Next Step:', error);
                if (aiSection) aiSection.classList.add('hidden');
            }
        }
    });

    // --- 3. VOUCHER ---
    document.addEventListener('DOMContentLoaded', async function() {
        const voucherListEl = document.getElementById('voucher-list');

        try {
            const response = await fetch('http://localhost:3000/api/voucher/active');
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                let html = '';
                result.data.forEach(v => {
                    const tienGiam = parseInt(v.gia_tri).toLocaleString() + (v.loai_giam === 'tien_mat' ? ' VNĐ' : '%');
                    const donToiThieu = parseInt(v.don_toi_thieu).toLocaleString() + ' VNĐ';
                    
                    const outOfStock = v.so_luong <= 0;
                    const cardClass = outOfStock ? 'opacity-50 grayscale' : 'hover:shadow-md';
                    const btnHtml = outOfStock 
                        ? `<button disabled class="bg-gray-100 text-gray-500 font-bold text-xs py-1.5 px-4 rounded-full cursor-not-allowed shadow-inner">Hết lượt</button>`
                        : `<button onclick="copyVoucher('${v.ma_code}')" class="bg-white text-pink-600 border border-transparent hover:border-pink-500 hover:bg-pink-50 hover:border-pink-200 font-bold text-xs py-1.5 px-4 rounded-full transition-all shadow-md hover:shadow-md transform hover:-translate-y-0.5">Copy Mã</button>`;

                    html += `
                        <div class="bg-[#fefcf8] rounded-sm border border-pink-50 shadow-sm overflow-hidden flex transition ${cardClass}">
                            <div class="bg-pink-500 w-24 flex flex-col justify-center items-center p-2 text-white text-center relative">
                                <div class="w-4 h-4 bg-white rounded-full absolute -top-2 -right-2"></div>
                                <div class="w-4 h-4 bg-white rounded-full absolute -bottom-2 -right-2"></div>
                                <span class="text-xs font-bold uppercase mb-1">Giảm</span>
                                <span class="text-xl font-black">${tienGiam}</span>
                            </div>
                            <div class="p-4 flex-grow flex flex-col justify-between bg-[#fefcf8] border-l-2 border-dashed border-gray-200">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-lg mb-1">${v.ma_code}</h4>
                                    <p class="text-xs text-gray-500 mb-1">Đơn tối thiểu ${donToiThieu}</p>
                                    
                                    ${(() => {
                                        // Tính toán % đã dùng cố định dựa trên số lượng còn lại (Tạo FOMO nhưng không bị random)
                                        // Sử dụng hàm toán học: khi so_luong càng nhỏ, % đã dùng càng cao
                                        let percent = 0;
                                        if (v.so_luong <= 0) {
                                            percent = 100;
                                        } else {
                                            // Ví dụ: so_luong = 1 -> 97%, so_luong = 10 -> 92%, so_luong = 50 -> 82%, so_luong = 100 -> 75%
                                            percent = 100 - Math.round(Math.sqrt(v.so_luong) * 2.5);
                                            // Đảm bảo % luôn nằm trong khoảng 5% - 99% nếu còn hàng
                                            if (percent > 99) percent = 99;
                                            if (percent < 5) percent = 5;
                                        }
                                        
                                        return `
                                        <div class="mt-2">
                                            <div class="flex justify-between text-[10px] mb-1">
                                                <span class="font-bold ${v.so_luong > 30 ? 'text-pink-500' : 'text-red-500 animate-pulse'}">${outOfStock ? 'Đã hết' : (v.so_luong <= 30 ? '🔥 Sắp hết!' : 'Đang hot')}</span>
                                                <span class="text-gray-500 font-medium">Đã dùng ${percent}%</span>
                                            </div>
                                            <div class="w-full bg-pink-100 rounded-full h-1.5">
                                                <div class="bg-gradient-to-r from-pink-400 to-red-500 h-1.5 rounded-full transition-all duration-1000" style="width: ${percent}%"></div>
                                            </div>
                                        </div>
                                        `;
                                    })()}
                                </div>
                                <div class="mt-3 text-right">
                                    ${btnHtml}
                                </div>
                            </div>
                        </div>
                    `;
                });
                voucherListEl.innerHTML = html;
            } else {
                voucherListEl.innerHTML = '<p class="text-gray-500 italic col-span-full bg-gray-50 p-6 rounded-sm text-center border border-gray-100">Hiện tại chưa có mã giảm giá nào.</p>';
            }
        } catch (error) {
            voucherListEl.innerHTML = '<p class="text-red-500 col-span-full">Lỗi tải mã giảm giá.</p>';
        }
    });

    // Hàm copy mã
    function copyVoucher(code) {
        navigator.clipboard.writeText(code).then(() => {
            alert('Đã copy mã: ' + code + '. Hãy dán ở bước Thanh toán nhé!');
        }).catch(err => {
            console.error('Lỗi copy:', err);
        });
    }
</script>

@endsection