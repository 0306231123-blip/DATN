@extends('layouts.user') 

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-detail.css') }}">
@endsection

@section('content')
<section class="user-section">
    <div class="user-container">
        <div class="detail-grid">
            {{-- Gallery Column --}}
            <div class="detail-gallery">
                <div class="detail-gallery__main">
                    @php
                        $anh = $sanPham->anhChinh()->first();
                    @endphp

                    @if($anh)
                        <img src="{{ $anh->duong_dan_anh }}" alt="{{ $sanPham->ten_san_pham }}" class="detail-gallery__img">
                    @else
                        <span class="detail-gallery__no-image">Chưa cập nhật ảnh</span>
                    @endif
                </div>
            </div>

            {{-- Info Column --}}
            <div class="detail-info">
                <div class="detail-info__header" style="padding: 0 0 1rem 0; margin-bottom: 1rem; text-align: left; border-bottom: none;">
                    <h1 class="detail-info__title" style="font-weight: 500; font-size: 1.25rem; color: #000; margin-bottom: 0.5rem; text-align: left;">{{ $sanPham->ten_san_pham }}</h1>
                    
                    @php
                        $tongLuot = \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $sanPham->ma_san_pham)->count();
                        $diemTB = $tongLuot > 0 ? \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $sanPham->ma_san_pham)->avg('diem_so') : 0;
                        $diemTron = round($diemTB);
                    @endphp

                    <div class="detail-info__rating-bar" style="display: flex; align-items: center; gap: 0.75rem; background: transparent; padding: 0; border: none; margin: 0;">
                        <span class="detail-info__rating-score" style="font-size: 1rem; font-weight: 500; color: #ee4d2d; border-bottom: 1px solid #ee4d2d;">
                            {{ $diemTB > 0 ? number_format($diemTB, 1) : '0' }}
                        </span>
                        
                        <div class="detail-info__rating-stars" style="display: flex; color: #ee4d2d;">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $diemTron)
                                    <svg viewBox="0 0 24 24" fill="currentColor" style="width: 1rem; height: 1rem;"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                @else
                                    <svg class="star-empty" viewBox="0 0 24 24" fill="currentColor" style="width: 1rem; height: 1rem; color: #d5d5d5;"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                @endif
                            @endfor
                        </div>
                        
                        @if($tongLuot > 0)
                            <span class="detail-info__rating-count" style="font-size: 0.875rem; color: #767676; border-left: 1px solid rgba(0,0,0,.14); padding-left: 1rem; margin-left: 0.25rem;">
                                <strong style="color: #000; font-weight: 500;">{{ $tongLuot }}</strong> Đánh Giá
                            </span>
                        @endif
                        
                        @if(isset($sanPham->tong_so_luong_ban) && $sanPham->tong_so_luong_ban > 0)
                            <span class="detail-info__sold" style="font-size: 0.875rem; color: #767676; border-left: 1px solid rgba(0,0,0,.14); padding-left: 1rem; margin-left: 0.25rem;">
                                <strong style="color: #000; font-weight: 500;">{{ $sanPham->tong_so_luong_ban }}</strong> Đã Bán
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="detail-price-row" style="background: #fafafa; padding: 1rem 1.25rem; border-radius: 4px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
                    @if($sanPham->gia_khuyen_mai)
                        <div class="detail-price-box--original" style="font-size: 1rem; color: #929292; text-decoration: line-through;">
                            ₫{{ number_format($sanPham->gia, 0, ',', '.') }}
                        </div>
                        <div class="detail-price-box--sale" style="font-size: 1.875rem; font-weight: 500; color: #ee4d2d;">
                            ₫{{ number_format($sanPham->gia_khuyen_mai, 0, ',', '.') }}
                        </div>
                    @else
                        <div class="detail-price-box--sale detail-price-box--full" style="font-size: 1.875rem; font-weight: 500; color: #ee4d2d;">
                            ₫{{ number_format($sanPham->gia, 0, ',', '.') }}
                        </div>
                    @endif
                </div>

                <div class="detail-action__benefits" style="margin-bottom: 2rem; font-size: 0.875rem;">
                    <div style="display: flex; margin-bottom: 1rem; align-items: flex-start;">
                        <span style="color: #767676; width: 110px;">Chính Sách</span>
                        <span style="color: #222;">🛡️ Đổi trả miễn phí 3 ngày</span>
                    </div>
                    <div style="display: flex; margin-bottom: 1rem; align-items: flex-start;">
                        <span style="color: #767676; width: 110px;">Vận Chuyển</span>
                        <span style="color: #222;">🚚 Freeship đơn từ 1.999k</span>
                    </div>
                    <div style="display: flex; margin-bottom: 1rem; align-items: flex-start;">
                        <span style="color: #767676; width: 110px;">Cam Kết</span>
                        <span style="color: #222;">✔️ Chính hãng 100%</span>
                    </div>
                    <div style="display: flex; margin-bottom: 1rem; align-items: center;">
                        <span style="color: #767676; width: 110px;">Số Lượng</span>
                        <div style="display: flex; border: 1px solid rgba(0,0,0,.09); border-radius: 2px; {{ $sanPham->so_luong_ton == 0 ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                            <button id="btn-qty-minus" style="width: 32px; height: 32px; background: #fff; border-right: 1px solid rgba(0,0,0,.09); color: #000; font-size: 1rem; display: flex; align-items: center; justify-content: center; cursor: pointer;">-</button>
                            <input type="text" id="qty-input" value="{{ $sanPham->so_luong_ton == 0 ? '0' : '1' }}" data-max="{{ $sanPham->so_luong_ton }}" style="width: 50px; height: 32px; text-align: center; border: none; font-size: 1rem; outline: none;" {{ $sanPham->so_luong_ton == 0 ? 'disabled' : '' }}>
                            <button id="btn-qty-plus" style="width: 32px; height: 32px; background: #fff; border-left: 1px solid rgba(0,0,0,.09); color: #000; font-size: 1rem; display: flex; align-items: center; justify-content: center; cursor: pointer;">+</button>
                        </div>
                        <span style="color: #767676; margin-left: 1rem;">
                            @if($sanPham->so_luong_ton == 0)
                                <strong class="text-red-500">Đã hết hàng</strong>
                            @else
                                {{ $sanPham->so_luong_ton }} sản phẩm có sẵn
                            @endif
                        </span>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    @if($sanPham->so_luong_ton > 0)
                        <button 
                            onclick="addToCart({{ $sanPham->ma_san_pham }}, parseInt(document.getElementById('qty-input').value))" 
                            style="background: rgba(255,87,34,0.1); border: 1px solid #ee4d2d; color: #ee4d2d; padding: 0 1.25rem; height: 48px; border-radius: 2px; font-size: 1rem; display: flex; align-items: center; cursor: pointer; transition: background 0.2s;">
                            <svg viewBox="0 0 24 24" fill="currentColor" style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem;"><path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2zm-9.83-3.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.86-7.01L19.42 4l-3.86 7H8.53L4.27 2H1v2h2l3.6 7.59-1.35 2.44C7.09 14.33 7 14.65 7 15c0 1.1.9 2 2 2h12v-2H9.42c-.14 0-.25-.11-.25-.25z"/></svg>
                            Thêm Vào Giỏ Hàng
                        </button>
                        <button 
                            onclick="buyNow({{ $sanPham->ma_san_pham }}, parseInt(document.getElementById('qty-input').value))" 
                            style="background: #ee4d2d; color: #fff; border: none; padding: 0 1.25rem; height: 48px; border-radius: 2px; font-size: 1rem; cursor: pointer; transition: background 0.2s; min-width: 140px;">
                            Mua Ngay
                        </button>
                    @else
                        <button 
                            disabled
                            style="background: #f5f5f5; border: 1px solid #e0e0e0; color: #9e9e9e; padding: 0 1.25rem; height: 48px; border-radius: 2px; font-size: 1rem; display: flex; align-items: center; cursor: not-allowed; min-width: 140px; font-weight: bold;">
                            Tạm Thời Hết Hàng
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Chi tiết & Mô tả (Mới) --}}
        <div class="detail-reviews" style="margin-top: 2rem;">
            <h3 class="detail-reviews__title">Chi tiết sản phẩm</h3>
            <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2.5rem; font-size: 1rem; color: var(--user-gray-800);">
                <div style="display: flex;"><span style="color: var(--user-gray-500); width: 140px; font-weight: 600;">Hãng:</span><span style="font-weight: 500;">{{ $sanPham->thuong_hieu }}</span></div>
                <div style="display: flex;"><span style="color: var(--user-gray-500); width: 140px; font-weight: 600;">Xuất xứ:</span><span style="font-weight: 500;">{{ $sanPham->xuat_xu }}</span></div>
                <div style="display: flex;"><span style="color: var(--user-gray-500); width: 140px; font-weight: 600;">Dành cho da:</span><span style="font-weight: 500;">{{ $sanPham->loai_da_phu_hop }}</span></div>
            </div>

            <h3 class="detail-reviews__title">Mô tả sản phẩm</h3>
            <div style="font-size: 1rem; color: var(--user-gray-700); line-height: 1.8; white-space: pre-line;">{{ $sanPham->mo_ta }}</div>
        </div>

        {{-- Review Section --}}
        <div class="detail-reviews">
            <h3 class="detail-reviews__title">Đánh giá sản phẩm</h3>

            <div class="detail-review-form hidden" id="review-form-container">
                <h4 class="detail-review-form__title">Gửi đánh giá của bạn</h4>
                <form id="form-danh-gia">
                    <div class="detail-review-form__stars" id="star-rating-container">
                        <span class="detail-review-form__stars-label">Chất lượng:</span>
                        @for($i = 1; $i <= 5; $i++)
                        <svg data-value="{{ $i }}" class="star-icon detail-review-form__star" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                        </svg>
                        @endfor
                        <input type="hidden" name="so_sao" id="so_sao_input" value="0">
                    </div>

                    <textarea id="noi_dung_danh_gia" rows="3" class="detail-review-form__textarea" placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm này nhé..."></textarea>
                    
                    <button type="submit" class="detail-review-form__submit">
                        Gửi đánh giá
                    </button>
                </form>
            </div>
            
            <div id="review-ineligible-message" class="bg-gray-50 border border-gray-100 rounded-2xl p-6 text-center text-gray-500 mb-8">
                Bạn chỉ có thể đánh giá sau khi đã mua và nhận được sản phẩm này.
            </div>

            <div id="danh-sach-danh-gia" class="detail-review-list">
            </div>
        </div>

    </div>
</section>

<script>
    const API_URL = 'http://localhost:3000/api';

    // 1. CHỨC NĂNG THÊM VÀO GIỎ HÀNG
    async function addToCart(maSanPham, soLuong = 1) {
        const token = localStorage.getItem('token');
        if (!token) {
            alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
            window.location.href = '/login'; 
            return;
        }

        try {
            const response = await fetch(`${API_URL}/cart/add`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ma_san_pham: maSanPham, so_luong: soLuong })
            });

            const result = await response.json();
            if (result.success) {
                alert('🎉 Đã thêm sản phẩm vào giỏ hàng thành công!');
            } else {
                alert('Lỗi: ' + result.message);
            }
        } catch (error) {
            console.error('Lỗi khi thêm vào giỏ hàng:', error);
            alert('Không thể kết nối đến server Node.js!');
        }
    }

    // 2. CHỨC NĂNG MUA NGAY (Chuyển sang trang thanh toán)
    async function buyNow(maSanPham, soLuong = 1) {
        const token = localStorage.getItem('token');
        if (!token) {
            alert('Vui lòng đăng nhập để mua hàng!');
            window.location.href = '/login'; 
            return;
        }

        try {
            const response = await fetch(`${API_URL}/cart/add`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ ma_san_pham: maSanPham, so_luong: soLuong })
            });

            const result = await response.json();
            if (result.success) {
                window.location.href = '/user/checkout';
            } else {
                alert('Lỗi: ' + result.message);
            }
        } catch (error) {
            console.error('Lỗi khi mua ngay:', error);
            alert('Không thể kết nối đến server Node.js!');
        }
    }

    document.addEventListener('DOMContentLoaded', async function() {
        const maSanPham = {{ $sanPham->ma_san_pham }};
        const token = localStorage.getItem('token');
        
        // KIỂM TRA QUYỀN ĐÁNH GIÁ (Chỉ hiện form nếu đã mua và đơn hàng hoàn thành)
        if (token) {
            try {
                const checkRes = await fetch(`${API_URL}/reviews/check-eligibility/${maSanPham}`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const checkData = await checkRes.json();
                
                if (checkData.success && checkData.eligible) {
                    document.getElementById('review-form-container').classList.remove('hidden');
                    document.getElementById('review-ineligible-message').classList.add('hidden');
                }
            } catch (error) {
                console.error('Lỗi kiểm tra quyền đánh giá:', error);
            }
        }
        
        const stars = document.querySelectorAll('.star-icon');
        const ratingInput = document.getElementById('so_sao_input');
        const formDanhGia = document.getElementById('form-danh-gia');

        function highlightStars(value) {
            stars.forEach(star => {
                if (star.getAttribute('data-value') <= value) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }

        stars.forEach(star => {
            star.addEventListener('mouseover', function() { highlightStars(this.getAttribute('data-value')); });
            star.addEventListener('mouseout', function() { highlightStars(ratingInput.value); });
            star.addEventListener('click', function() { 
                ratingInput.value = this.getAttribute('data-value'); 
                highlightStars(ratingInput.value);
            });
        });

        if(formDanhGia) {
            formDanhGia.addEventListener('submit', async function(e) {
                e.preventDefault(); 
                
                const token = localStorage.getItem('token');
                if (!token) {
                    alert('Vui lòng đăng nhập để gửi đánh giá!');
                    window.location.href = '/login';
                    return;
                }

                const diemSo = document.getElementById('so_sao_input').value;
                const noiDung = document.getElementById('noi_dung_danh_gia').value;
                const maSanPham = {{ $sanPham->ma_san_pham }};

                if (diemSo == 0) {
                    alert('Vui lòng chọn số sao đánh giá nhé!');
                    return;
                }

                try {
                    const response = await fetch(`${API_URL}/reviews/add`, {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            ma_san_pham: maSanPham,
                            diem_so: diemSo,
                            noi_dung: noiDung
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        alert('🎉 ' + result.message);
                        loadReviews(); // Tải lại danh sách ngay sau khi đánh giá thành công
                        
                        document.getElementById('noi_dung_danh_gia').value = ''; 
                        ratingInput.value = 0;
                        highlightStars(0);
                    } else {
                        alert(result.message || 'Lỗi server');
                    }
                } catch (error) {
                    console.error('Lỗi khi gửi đánh giá:', error);
                    alert('Bạn chưa mua sản phẩm này hoặc phiên đăng nhập đã hết hạn!');
                }
            });
        }
    });

    // 3. TỰ ĐỘNG TẢI VÀ HIỂN THỊ ĐÁNH GIÁ
    async function loadReviews() {
        const maSanPham = {{ $sanPham->ma_san_pham }};
        const container = document.getElementById('danh-sach-danh-gia');
        
        try {
            const response = await fetch(`${API_URL}/reviews/${maSanPham}`);
            const result = await response.json();

            if (result.success) {
                container.innerHTML = ''; 

                if (result.data.length === 0) {
                    container.innerHTML = '<p class="text-center text-gray-500 italic py-4">Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>';
                    return;
                }

                result.data.forEach(review => {
                    let starsHtml = '';
                    for (let i = 1; i <= 5; i++) {
                        if (i <= review.diem_so) {
                            starsHtml += `<svg class="star-rating__icon" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>`;
                        } else {
                            starsHtml += `<svg class="star-rating__icon star-rating__icon--empty" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>`;
                        }
                    }

                    const dateObj = new Date(review.ngay_viet);
                    const dateStr = dateObj.toLocaleDateString('vi-VN');
                    
                    const tenNguoiDung = review.ho_ten || 'Khách hàng';
                    const avatarChar = tenNguoiDung.charAt(0).toUpperCase();

                    const html = `
                        <div class="detail-review-item">
                            <div class="detail-review-item__header">
                                <div class="detail-review-item__avatar">${avatarChar}</div>
                                <div>
                                    <p class="detail-review-item__name">${tenNguoiDung}</p>
                                    <div class="detail-review-item__stars">
                                        ${starsHtml}
                                    </div>
                                </div>
                                <span class="detail-review-item__date">${dateStr}</span>
                            </div>
                            <p class="detail-review-item__content">${review.noi_dung}</p>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', html);
                });
            }
        } catch (error) {
            console.error('Lỗi khi tải đánh giá:', error);
        }
    }

    // Gọi tải dữ liệu khi mở trang
    loadReviews();

    // 4. XỬ LÝ SỐ LƯỢNG
    const btnMinus = document.getElementById('btn-qty-minus');
    const btnPlus = document.getElementById('btn-qty-plus');
    const qtyInput = document.getElementById('qty-input');

    if (btnMinus && btnPlus && qtyInput) {
        const maxStock = parseInt(qtyInput.getAttribute('data-max')) || 0;

        function updateQty(newVal) {
            let val = parseInt(newVal);
            if (isNaN(val) || val < 1) val = 1;
            if (val > maxStock) {
                val = maxStock;
            }
            qtyInput.value = val;
        }

        btnMinus.addEventListener('click', () => {
            updateQty(parseInt(qtyInput.value) - 1);
        });

        btnPlus.addEventListener('click', () => {
            updateQty(parseInt(qtyInput.value) + 1);
        });

        qtyInput.addEventListener('change', (e) => {
            updateQty(e.target.value);
        });
        
        // Ngăn nhập chữ cái
        qtyInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
    }
</script>
@endsection