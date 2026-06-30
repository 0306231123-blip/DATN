@extends('layouts.user')
@section('title', 'Trang chủ')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-home.css') }}">
@endsection

@section('content')

{{-- Banner Swiper --}}
<section class="home-banner">
    <div class="home-banner__container">
        <div class="swiper mySwiper home-banner__swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="{{ asset('images/qc1.jpg') }}" alt="QC 1" class="home-banner__img">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/qc2.jpg') }}" alt="QC 2" class="home-banner__img">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/qc3.jpg') }}" alt="QC 3" class="home-banner__img">
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/qc4.jpg') }}" alt="QC 4" class="home-banner__img">
                </div>
            </div>
            <div class="swiper-pagination"></div>
        </div>  
    </div>
</section>

{{-- Voucher Section --}}
<section class="home-voucher">
    <div class="user-container">
        <div class="user-section-title--left">
            <span class="user-section-title__icon">🎫</span>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-widest">Kho Voucher</h2>
        </div>
        
        <div id="voucher-list" class="voucher-grid">
            <p class="text-gray-500 font-medium user-pulse">Đang tìm kiếm mã giảm giá dành cho bạn...</p>
        </div>
    </div>
</section>

{{-- AI Section --}}
<section class="home-ai">
    {{-- Guest (chưa đăng nhập) --}}
    <div id="ai-guest-section" class="user-container" style="margin-bottom: 2rem;">
        <div class="ai-guest-card">
            <div class="ai-guest-card__bar"></div>
            <span class="ai-guest-card__icon">🔮</span>
            <h3 class="ai-guest-card__title">Bạn chưa biết sản phẩm nào hợp với mình?</h3>
            <p class="ai-guest-card__desc">Hãy để Trí tuệ Nhân tạo (AI) của chúng tôi phân tích và thiết kế chu trình Skincare dành riêng cho làn da của bạn.</p>
            <a href="/login" class="btn-dark">
                Đăng nhập để nhận AI Gợi ý
            </a>
        </div>
    </div>

    {{-- Logged in (đã đăng nhập) --}}
    <div id="ai-recommendation-section" class="user-container hidden" style="margin-bottom: 2rem;">
        <div class="ai-recommend-card">
            <div class="ai-recommend-card__glow"></div>

            <div class="ai-recommend-card__header">
                <h2 class="ai-recommend-card__title">
                    <span class="ai-recommend-card__title-icon">✨</span>
                    ĐỀ XUẤT CHO BẠN
                </h2>
            </div>

            <div id="ai-products-container" class="product-grid--4col" style="position: relative; z-index: 10;">
                <p class="text-gray-500 italic col-span-full user-pulse font-medium">🤖 Đang kết nối với não bộ AI...</p>
            </div>
        </div>
    </div>
</section>

{{-- Featured Products --}}
<section class="home-featured">
    <div class="user-container">
        <h2 class="home-featured__title">Sản phẩm nổi bật</h2>
        <div class="product-grid">
            @foreach($sanPhamNoiBat as $sp)
                @include('components.product-card', ['sp' => $sp])
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
                    let html = '';
                    result.data.forEach(sp => {
                        const price = new Intl.NumberFormat('vi-VN').format(sp.gia_khuyen_mai || sp.gia) + ' đ';
                        const img = (sp.danh_sach_anh && sp.danh_sach_anh.length > 0) ? sp.danh_sach_anh[0].duong_dan_anh : 'https://via.placeholder.com/300x300?text=No+Image';

                        html += `
                            <a href="/user/detail/${sp.ma_san_pham}" class="product-card product-card--pink-border">
                                <div class="product-card__image-wrap">
                                    <img src="${img}" alt="${sp.ten_san_pham}" class="product-card__img">
                                </div>
                                <div class="product-card__body">
                                    <span class="product-card__name">
                                        ${sp.ten_san_pham}
                                    </span>
                                    <div class="price-wrap">
                                        <span class="price-current">${price}</span>
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
                    
                    html += `
                        <div class="voucher-card">
                            <div class="voucher-card__left">
                                <span class="voucher-card__label">Giảm</span>
                                <span class="voucher-card__value">${tienGiam}</span>
                            </div>
                            <div class="voucher-card__right">
                                <div>
                                    <h4 class="voucher-card__code">${v.ma_code}</h4>
                                    <p class="voucher-card__min">Đơn tối thiểu ${donToiThieu}</p>
                                    <p class="voucher-card__remaining">Còn lại: ${v.so_luong} lượt</p>
                                </div>
                                <div class="voucher-card__copy-wrap">
                                    <button onclick="copyVoucher('${v.ma_code}')" class="voucher-card__copy-btn">
                                        Copy Mã
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });
                voucherListEl.innerHTML = html;
            } else {
                voucherListEl.innerHTML = '<p class="text-gray-500 italic">Hiện tại chưa có mã giảm giá nào.</p>';
            }
        } catch (error) {
            voucherListEl.innerHTML = '<p class="text-red-500">Lỗi tải mã giảm giá.</p>';
        }
    });

    // Hàm copy mã vào Clipboard
    function copyVoucher(code) {
        navigator.clipboard.writeText(code).then(() => {
            alert('Đã copy mã: ' + code + '. Hãy dán ở bước Thanh toán nhé!');
        }).catch(err => {
            console.error('Lỗi copy:', err);
        });
    }
</script>

@endsection