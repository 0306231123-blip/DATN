@extends('layouts.user')
@section('title', 'Trang chủ')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
@endsection

@section('content')

{{-- Banner Swiper --}}
<section class="home-banner mb-10 w-full">
    <div class="swiper mySwiper rounded-3xl overflow-hidden shadow-md group">
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
                   class="bg-white rounded-2xl shadow-sm border border-pink-50 p-4 flex flex-col items-center justify-center hover:shadow-lg hover:-translate-y-1 transition-all group">
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
        <div class="ai-guest-card bg-gradient-to-r from-pink-50 to-purple-50 p-8 rounded-3xl border border-pink-100 shadow-sm text-center">
            <span class="text-5xl block mb-4">🔮</span>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">Bạn chưa biết sản phẩm nào hợp với mình?</h3>
            <p class="text-gray-600 mb-6 max-w-2xl mx-auto">Hãy để Trí tuệ Nhân tạo (AI) của chúng tôi phân tích và thiết kế chu trình Skincare dành riêng cho làn da của bạn.</p>
            <a href="/login" class="inline-block bg-gray-900 text-white font-bold py-3 px-8 rounded-full hover:bg-pink-500 transition shadow-lg">
                Đăng nhập để nhận AI Gợi ý
            </a>
        </div>
    </div>

    {{-- Logged in (đã đăng nhập) --}}
    <div id="ai-recommendation-section" class="w-full hidden space-y-12">
        <div class="flex items-center">
            <span class="text-4xl mr-3">🔮</span>
            <h2 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-pink-500 uppercase tracking-widest">GÓC ĐỀ XUẤT TỪ AI</h2>
        </div>

        <!-- Row 1: Low Stock -->
        <div id="ai-low-stock-wrapper" class="hidden">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
                <div>
                    <div class="inline-flex items-center space-x-2 bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                        <span class="animate-pulse">🔴</span>
                        <span>Số lượng có hạn</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 uppercase">Mua Lại Kẻo Hết</h3>
                    <p class="text-gray-600 mt-1">Sản phẩm bạn từng mua đang sắp cháy hàng, chớp lấy cơ hội ngay!</p>
                </div>
            </div>
            <div id="ai-low-stock-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6"></div>
        </div>

        <!-- Row 2: Next Step -->
        <div id="ai-next-step-wrapper" class="hidden">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
                <div>
                    <div class="inline-flex items-center space-x-2 bg-pink-100 text-pink-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                        <span>💡</span>
                        <span>Kết hợp hoàn hảo</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 uppercase">Bước Tiếp Theo Của Bạn</h3>
                    <p class="text-gray-600 mt-1">Hệ thống gợi ý dùng kèm với các món bạn đã sở hữu để đạt hiệu quả tốt nhất.</p>
                </div>
            </div>
            <div id="ai-next-step-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6"></div>
        </div>

        <!-- Row 3: Skin Type -->
        <div id="ai-skin-type-wrapper" class="hidden">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-6">
                <div>
                    <div class="inline-flex items-center space-x-2 bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                        <span>✨</span>
                        <span>Phân tích NLP</span>
                    </div>
                    <h3 class="text-2xl font-black text-gray-800 uppercase">Chọn Riêng Cho Bạn</h3>
                    <p class="text-gray-600 mt-1">Top sản phẩm sinh ra 100% dành cho làn da của bạn.</p>
                </div>
            </div>
            <div id="ai-skin-type-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6"></div>
        </div>

        <!-- Loading / Empty State -->
        <div id="ai-loading-state" class="w-full bg-gradient-to-br from-orange-50 to-pink-50 p-6 rounded-[2rem] border border-orange-100 shadow-sm text-center py-10">
            <p class="text-gray-500 italic user-pulse font-medium">🤖 Đang kết nối với não bộ AI...</p>
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

        // --- 2. CHẠY LOGIC AI GỢI Ý ---
        const token = localStorage.getItem('token');
        const aiSection = document.getElementById('ai-recommendation-section');
        const guestSection = document.getElementById('ai-guest-section');
        const loadingState = document.getElementById('ai-loading-state');

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
                loadingState.classList.add('hidden'); // Ẩn loading

                if (result.success && result.data) {
                    const renderProducts = (containerId, wrapperId, products, badgeHtml = '') => {
                        if (!products || products.length === 0) return;
                        
                        document.getElementById(wrapperId).classList.remove('hidden');
                        let html = '';
                        products.forEach(sp => {
                            const price = new Intl.NumberFormat('vi-VN').format(sp.gia_khuyen_mai || sp.gia) + ' đ';
                            const img = (sp.danh_sach_anh && sp.danh_sach_anh.length > 0) ? sp.danh_sach_anh[0].duong_dan_anh : 'https://via.placeholder.com/300x300?text=No+Image';

                            html += `
                                <a href="/user/detail/${sp.ma_san_pham}" class="bg-[#fefcf8] border border-gray-100 rounded-2xl p-4 hover:shadow-xl hover:border-pink-300 transition duration-300 flex flex-col h-full group relative overflow-hidden">
                                    ${badgeHtml}
                                    <div class="relative w-full aspect-square mb-4 overflow-hidden rounded-xl bg-white mt-2">
                                        <img src="${img}" alt="${sp.ten_san_pham}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    </div>
                                    <div class="flex-grow flex flex-col justify-between">
                                        <h3 class="font-bold text-gray-800 text-sm md:text-base line-clamp-2 mb-2 group-hover:text-pink-600 transition">
                                            ${sp.ten_san_pham}
                                        </h3>
                                        <div class="text-pink-600 font-black text-lg">
                                            ${price}
                                        </div>
                                    </div>
                                </a>
                            `;
                        });
                        document.getElementById(containerId).innerHTML = html;
                    };

                    const lowStockBadge = `
                        <div class="absolute top-0 right-0 z-20 bg-gradient-to-r from-red-500 to-orange-500 text-white text-[10px] sm:text-xs font-bold px-3 py-1 rounded-bl-xl shadow-md flex items-center gap-1 animate-pulse">
                            <span>🔥</span> Sắp cháy hàng
                        </div>`;
                    
                    const nextStepBadge = `
                        <div class="absolute top-0 right-0 z-20 bg-gradient-to-r from-pink-500 to-rose-500 text-white text-[10px] sm:text-xs font-bold px-3 py-1 rounded-bl-xl shadow-md flex items-center gap-1">
                            <span>💡</span> Dùng kèm
                        </div>`;
                    
                    const skinTypeBadge = `
                        <div class="absolute top-0 right-0 z-20 bg-gradient-to-r from-blue-400 to-indigo-500 text-white text-[10px] sm:text-xs font-bold px-3 py-1 rounded-bl-xl shadow-md flex items-center gap-1">
                            <span>✨</span> Phù hợp
                        </div>`;

                    renderProducts('ai-low-stock-container', 'ai-low-stock-wrapper', result.data.low_stock, lowStockBadge);
                    renderProducts('ai-next-step-container', 'ai-next-step-wrapper', result.data.next_step, nextStepBadge);
                    renderProducts('ai-skin-type-container', 'ai-skin-type-wrapper', result.data.skin_type, skinTypeBadge);

                    // Nếu cả 3 mảng đều rỗng
                    if ((!result.data.low_stock || result.data.low_stock.length === 0) &&
                        (!result.data.next_step || result.data.next_step.length === 0) &&
                        (!result.data.skin_type || result.data.skin_type.length === 0)) {
                        
                        loadingState.classList.remove('hidden');
                        loadingState.innerHTML = `
                            <div class="bg-pink-50 p-6 md:p-10 rounded-3xl text-center border border-pink-100 w-full">
                                <span class="text-5xl mb-4 block">📝</span>
                                <p class="text-gray-800 font-black text-xl mb-2">Chưa đủ dữ liệu để AI phân tích!</p>
                                <p class="text-gray-600 mb-6 max-w-lg mx-auto">Bạn hãy vào Trang Cá Nhân cập nhật "Loại da" để AI hoạt động chính xác nhất nhé.</p>
                                <a href="/user/profileuser" class="inline-block bg-pink-500 text-white font-bold py-3 px-8 rounded-full hover:bg-pink-600 transition shadow-md">Cập nhật ngay</a>
                            </div>
                        `;
                    }
                }
            } catch (error) {
                console.error('Lỗi tải AI:', error);
                loadingState.classList.remove('hidden');
                loadingState.innerHTML = '<p class="text-red-500 col-span-full text-center font-bold bg-red-50 p-4 rounded-xl border border-red-200">Hệ thống AI đang kết nối, vui lòng tải lại trang!</p>';
            }
        } else {
            guestSection.classList.remove('hidden');
            aiSection.classList.add('hidden');
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
                        : `<button onclick="copyVoucher('${v.ma_code}')" class="bg-white text-pink-600 border border-pink-100 hover:bg-pink-50 hover:border-pink-200 font-bold text-xs py-1.5 px-4 rounded-full transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">Copy Mã</button>`;

                    html += `
                        <div class="bg-[#fefcf8] rounded-2xl border border-pink-50 shadow-sm overflow-hidden flex transition ${cardClass}">
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
                voucherListEl.innerHTML = '<p class="text-gray-500 italic col-span-full bg-gray-50 p-6 rounded-2xl text-center border border-gray-100">Hiện tại chưa có mã giảm giá nào.</p>';
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