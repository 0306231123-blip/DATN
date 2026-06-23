@extends('layouts.user') 
@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="flex flex-col items-center">
                
                <div class="w-full bg-gray-100 aspect-square rounded-3xl flex items-center justify-center shadow-inner mb-6 overflow-hidden">
                    @php
                        $anh = $sanPham->anhChinh()->first();
                    @endphp

                    @if($anh)
                        <img src="{{ $anh->duong_dan_anh }}" alt="{{ $sanPham->ten_san_pham }}" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                    @else
                        <span class="text-gray-400 font-bold">Chưa cập nhật ảnh</span>
                    @endif
                </div>

                <div class="flex space-x-4 w-full">
                    @if($sanPham->gia_khuyen_mai)
                        <div class="flex-1 bg-white p-4 rounded-xl text-center shadow-sm font-bold text-pink-600 text-xl">
                            {{ number_format($sanPham->gia_khuyen_mai, 0, ',', '.') }} đ
                        </div>
                        <div class="flex-1 bg-white p-4 rounded-xl text-center shadow-sm font-bold text-gray-400 line-through flex items-center justify-center">
                            {{ number_format($sanPham->gia, 0, ',', '.') }} đ
                        </div>
                    @else
                        <div class="w-full bg-white p-4 rounded-xl text-center shadow-sm font-bold text-pink-600 text-xl">
                            {{ number_format($sanPham->gia, 0, ',', '.') }} đ
                        </div>
                    @endif
                </div>

                <button 
                    onclick="addToCart({{ $sanPham->ma_san_pham }})" 
                    class="mt-6 w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-4 rounded-xl shadow-md transition uppercase tracking-wider">
                    Thêm vào giỏ hàng
                </button>
            </div>

            <div class="flex flex-col">
                
                <div class="bg-white p-6 rounded-2xl shadow-sm text-center mb-6 flex flex-col items-center border border-gray-100">
                    <h1 class="font-black text-3xl text-gray-800 mb-3">{{ $sanPham->ten_san_pham }}</h1>
                    
                    @php
                        // Gọi DB lấy tổng lượt đánh giá và tính điểm trung bình
                        $tongLuot = \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $sanPham->ma_san_pham)->count();
                        $diemTB = $tongLuot > 0 ? \Illuminate\Support\Facades\DB::table('danh_gia')->where('ma_san_pham', $sanPham->ma_san_pham)->avg('diem_so') : 0;
                        $diemTron = round($diemTB);
                    @endphp

                    <div class="flex items-center space-x-3 bg-gray-50 px-6 py-2 rounded-full border border-gray-100">
                        <div class="flex text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $diemTron)
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                @endif
                            @endfor
                        </div>
                        
                        <span class="text-sm font-bold text-gray-800">
                            {{ $diemTB > 0 ? number_format($diemTB, 1) . '/5.0' : 'Chưa có đánh giá' }}
                        </span>
                        
                        @if($tongLuot > 0)
                            <span class="text-sm text-gray-500 font-medium border-l border-gray-300 pl-3">{{ $tongLuot }} đánh giá</span>
                        @endif
                        
                        @if(isset($sanPham->tong_so_luong_ban) && $sanPham->tong_so_luong_ban > 0)
                            <span class="text-sm text-pink-500 font-bold border-l border-gray-300 pl-3">Đã bán: {{ $sanPham->tong_so_luong_ban }}</span>
                        @endif
                    </div>
                </div>
                
                <div class="flex gap-6">
                    <div class="flex-1 bg-white p-6 rounded-2xl shadow-sm min-h-[300px]">
                        <h3 class="font-bold text-gray-800 mb-2">Mô tả sản phẩm:</h3>
                        <p class="text-gray-600 whitespace-pre-line mb-4">{{ $sanPham->mo_ta }}</p>
                    </div>
                    
                    <div class="flex flex-col space-y-3 w-1/3">
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-600">
                            Hãng: {{ $sanPham->thuong_hieu }}
                        </span>
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-600">
                            Xuất xứ: {{ $sanPham->xuat_xu }}
                        </span>
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-600">
                            Dành cho: {{ $sanPham->loai_da_phu_hop }}
                        </span>
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-400 mt-4">
                            Còn lại: {{ $sanPham->so_luong_ton }} sp
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 bg-white rounded-3xl p-8 shadow-sm">
            <h3 class="text-2xl font-black text-gray-800 mb-6 border-b border-gray-100 pb-4">Đánh giá sản phẩm</h3>

            <div class="mb-10 bg-gray-50 p-6 rounded-2xl border border-gray-100">
                <h4 class="font-bold text-gray-700 mb-4">Gửi đánh giá của bạn</h4>
                <form id="form-danh-gia">
                    <div class="flex items-center mb-4 space-x-1" id="star-rating-container">
                        <span class="text-sm font-medium text-gray-500 mr-3">Chất lượng:</span>
                        @for($i = 1; $i <= 5; $i++)
                        <svg data-value="{{ $i }}" class="star-icon w-8 h-8 text-gray-300 cursor-pointer hover:text-yellow-400 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                        </svg>
                        @endfor
                        <input type="hidden" name="so_sao" id="so_sao_input" value="0">
                    </div>

                    <textarea id="noi_dung_danh_gia" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 mb-4 text-gray-700" placeholder="Hãy chia sẻ cảm nhận của bạn về sản phẩm này nhé..."></textarea>
                    
                    <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-6 rounded-xl transition shadow-sm">
                        Gửi đánh giá
                    </button>
                </form>
            </div>

            <div id="danh-sach-danh-gia" class="space-y-6">
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
                            starsHtml += `<svg class="w-4 h-4 text-yellow-400" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>`;
                        } else {
                            starsHtml += `<svg class="w-4 h-4 text-gray-300" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>`;
                        }
                    }

                    const dateObj = new Date(review.ngay_viet);
                    const dateStr = dateObj.toLocaleDateString('vi-VN');
                    
                    const tenNguoiDung = review.ho_ten || 'Khách hàng';
                    const avatarChar = tenNguoiDung.charAt(0).toUpperCase();

                    const html = `
                        <div class="border-b border-gray-100 pb-6">
                            <div class="flex items-center mb-2">
                                <div class="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center text-white font-bold mr-3">${avatarChar}</div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm">${tenNguoiDung}</p>
                                    <div class="flex space-x-1 mt-1">
                                        ${starsHtml}
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400 ml-auto">${dateStr}</span>
                            </div>
                            <p class="text-gray-600 text-sm mt-2">${review.noi_dung}</p>
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