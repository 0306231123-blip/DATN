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

                <div class="detail-price-row">
                    @if($sanPham->gia_khuyen_mai)
                        <div class="detail-price-box detail-price-box--sale">
                            {{ number_format($sanPham->gia_khuyen_mai, 0, ',', '.') }} đ
                        </div>
                        <div class="detail-price-box detail-price-box--original">
                            {{ number_format($sanPham->gia, 0, ',', '.') }} đ
                        </div>
                    @else
                        <div class="detail-price-box detail-price-box--sale detail-price-box--full">
                            {{ number_format($sanPham->gia, 0, ',', '.') }} đ
                        </div>
                    @endif
                </div>

                <button 
                    onclick="addToCart({{ $sanPham->ma_san_pham }})" 
                    class="detail-add-cart">
                    Thêm vào giỏ hàng
                </button>
            </div>

            {{-- Info Column --}}
            <div class="detail-info">
                <div class="detail-info__header">
                    <h1 class="detail-info__title">{{ $sanPham->ten_san_pham }}</h1>
                    
                    @php
                        $tongLuot = \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $sanPham->ma_san_pham)->count();
                        $diemTB = $tongLuot > 0 ? \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $sanPham->ma_san_pham)->avg('diem_so') : 0;
                        $diemTron = round($diemTB);
                    @endphp

                    <div class="detail-info__rating-bar">
                        <div class="detail-info__rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $diemTron)
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                @else
                                    <svg class="star-empty" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                @endif
                            @endfor
                        </div>
                        
                        <span class="detail-info__rating-score">
                            {{ $diemTB > 0 ? number_format($diemTB, 1) . '/5.0' : 'Chưa có đánh giá' }}
                        </span>
                        
                        @if($tongLuot > 0)
                            <span class="detail-info__rating-count">{{ $tongLuot }} đánh giá</span>
                        @endif
                        
                        @if(isset($sanPham->tong_so_luong_ban) && $sanPham->tong_so_luong_ban > 0)
                            <span class="detail-info__sold">Đã bán: {{ $sanPham->tong_so_luong_ban }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="detail-content-row">
                    <div class="detail-description">
                        <h3 class="detail-description__label">Mô tả sản phẩm:</h3>
                        <p class="detail-description__text">{{ $sanPham->mo_ta }}</p>
                    </div>
                    
                    <div class="detail-specs">
                        <span class="detail-specs__item">
                            Hãng: {{ $sanPham->thuong_hieu }}
                        </span>
                        <span class="detail-specs__item">
                            Xuất xứ: {{ $sanPham->xuat_xu }}
                        </span>
                        <span class="detail-specs__item">
                            Dành cho: {{ $sanPham->loai_da_phu_hop }}
                        </span>
                        <span class="detail-specs__item detail-specs__item--muted">
                            Còn lại: {{ $sanPham->so_luong_ton }} sp
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Review Section --}}
        <div class="detail-reviews">
            <h3 class="detail-reviews__title">Đánh giá sản phẩm</h3>

            <div class="detail-review-form">
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

    // 2. HIỆU ỨNG CHỌN SAO & GỬI ĐÁNH GIÁ
    document.addEventListener('DOMContentLoaded', function() {
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
                        alert('Lỗi: ' + result.message);
                    }
                } catch (error) {
                    console.error('Lỗi khi gửi đánh giá:', error);
                    alert('Không thể kết nối đến server Node.js!');
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
</script>
@endsection