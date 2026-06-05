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
                <div class="bg-white p-4 rounded-xl shadow-sm text-center font-bold text-2xl text-gray-800 mb-6">
                    {{ $sanPham->ten_san_pham }}
                </div>
                
                <div class="flex gap-6">
                    <div class="flex-1 bg-white p-6 rounded-2xl shadow-sm min-h-[300px]">
                        <h3 class="font-bold text-gray-800 mb-2">Mô tả sản phẩm:</h3>
                        <p class="text-gray-600 whitespace-pre-line mb-4">{{ $sanPham->mo_ta }}</p>
                        
                        <h3 class="font-bold text-gray-800 mb-2 mt-4">Thành phần chính:</h3>
                        <p class="text-gray-600 whitespace-pre-line">{{ $sanPham->thanh_phan }}</p>
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
    </div>
</section>
<script>
    const API_URL = 'http://localhost:3000/api';

    // Hàm gọi khi khách hàng bấm nút "Thêm vào giỏ hàng"
    async function addToCart(maSanPham, soLuong = 1) {
        // 1. Kiểm tra xem khách đã đăng nhập chưa
        console.log("Nút đã được bấm! Đang thêm SP:", maSanPham);
        const token = localStorage.getItem('token');
        if (!token) {
            alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
            window.location.href = '/login'; // Đá về trang đăng nhập
            return;
        }

        try {
            // 2. Gửi yêu cầu sang Node.js
            const response = await fetch(`${API_URL}/cart/add`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    ma_san_pham: maSanPham,
                    so_luong: soLuong
                })
            });

            const result = await response.json();

            // 3. Xử lý kết quả trả về
            if (result.success) {
                alert('🎉 Đã thêm sản phẩm vào giỏ hàng thành công!');
                // (Tùy chọn) Cập nhật con số trên icon giỏ hàng trên thanh menu
            } else {
                alert('Lỗi: ' + result.message);
            }
        } catch (error) {
            console.error('Lỗi khi thêm vào giỏ hàng:', error);
            alert('Không thể kết nối đến server Node.js!');
        }
    }
</script>
@endsection