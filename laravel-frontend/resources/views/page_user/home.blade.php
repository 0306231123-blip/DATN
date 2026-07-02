@extends('layouts.user')
@section('title', 'Trang chủ')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
@endsection

@section('content')

{{-- Banner Swiper --}}
<section class="home-banner mb-10 w-full">
    <div class="swiper mySwiper rounded-2xl overflow-hidden pb-8">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <img src="{{ asset('images/qc1.jpg') }}" alt="QC 1" class="w-full h-[200px] md:h-[240px] lg:h-[280px] object-cover rounded-2xl shadow-sm">
            </div>
            <div class="swiper-slide">
                <img src="{{ asset('images/qc2.jpg') }}" alt="QC 2" class="w-full h-[200px] md:h-[240px] lg:h-[280px] object-cover rounded-2xl shadow-sm">
            </div>
            <div class="swiper-slide">
                <img src="{{ asset('images/qc3.jpg') }}" alt="QC 3" class="w-full h-[200px] md:h-[240px] lg:h-[280px] object-cover rounded-2xl shadow-sm">
            </div>
            <div class="swiper-slide">
                <img src="{{ asset('images/qc4.jpg') }}" alt="QC 4" class="w-full h-[200px] md:h-[240px] lg:h-[280px] object-cover rounded-2xl shadow-sm">
            </div>
        </div>
        <div class="swiper-pagination"></div>
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
    <div id="ai-recommendation-section" class="w-full hidden">
        <div class="ai-recommend-card">
            <div class="flex items-center mb-6">
                <span class="text-3xl mr-3 text-pink-500">✨</span>
                <h2 class="text-2xl font-black text-gray-800 uppercase tracking-widest">ĐỀ XUẤT CHO BẠN</h2>
            </div>

            <div id="ai-products-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
                <p class="text-gray-500 italic col-span-full user-pulse font-medium text-center py-10">🤖 Đang kết nối với não bộ AI...</p>
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
                    let html = '';
                    result.data.forEach(sp => {
                        const price = new Intl.NumberFormat('vi-VN').format(sp.gia_khuyen_mai || sp.gia) + ' đ';
                        const img = (sp.danh_sach_anh && sp.danh_sach_anh.length > 0) ? sp.danh_sach_anh[0].duong_dan_anh : 'https://via.placeholder.com/300x300?text=No+Image';

                        html += `
                            <a href="/user/detail/${sp.ma_san_pham}" class="bg-[#fefcf8] border border-pink-50 rounded-2xl p-4 hover:shadow-xl hover:border-pink-300 transition duration-300 flex flex-col h-full group">
                                <div class="relative w-full aspect-square mb-4 overflow-hidden rounded-xl">
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
                    
                    productsContainer.innerHTML = html;
                } else {
                    productsContainer.innerHTML = `
                        <div class="col-span-full bg-pink-50 p-6 md:p-10 rounded-3xl text-center border border-pink-100">
                            <span class="text-5xl mb-4 block">📝</span>
                            <p class="text-gray-800 font-black text-xl mb-2">Chưa đủ dữ liệu để AI phân tích!</p>
                            <p class="text-gray-600 mb-6 max-w-lg mx-auto">Bạn hãy vào Trang Cá Nhân cập nhật "Loại da" để AI hoạt động chính xác nhất nhé.</p>
                            <a href="/user/profileuser" class="inline-block bg-pink-500 text-white font-bold py-3 px-8 rounded-full hover:bg-pink-600 transition shadow-md">Cập nhật ngay</a>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Lỗi tải AI:', error);
                productsContainer.innerHTML = '<p class="text-red-500 col-span-full text-center font-bold bg-red-50 p-4 rounded-xl border border-red-200">Hệ thống AI đang kết nối, vui lòng tải lại trang!</p>';
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
                    const tienGiam = parseInt(v.gia_tri).toLocaleString() + (v.loai_giam === 'tien_mat' ? 'đ' : '%');
                    const donToiThieu = parseInt(v.don_toi_thieu).toLocaleString() + 'đ';
                    
                    const outOfStock = v.so_luong <= 0;
                    const cardClass = outOfStock ? 'opacity-50 grayscale' : 'hover:shadow-md';
                    const btnHtml = outOfStock 
                        ? `<button disabled class="bg-gray-100 text-gray-500 font-bold text-xs py-1.5 px-4 rounded-full cursor-not-allowed">Hết lượt</button>`
                        : `<button onclick="copyVoucher('${v.ma_code}')" class="bg-pink-50 text-pink-600 hover:bg-pink-100 font-bold text-xs py-1.5 px-4 rounded-full transition">Copy Mã</button>`;

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
                                    <p class="text-xs text-pink-500 font-medium">Còn lại: ${Math.max(0, v.so_luong)} lượt</p>
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