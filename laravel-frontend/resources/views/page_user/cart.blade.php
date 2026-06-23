@extends('layouts.user')
@section('title', 'Giỏ hàng')
@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen">
    <div class="max-w-6xl mx-auto">
        
        <div id="cart-items" class="space-y-4">
        </div>

        <div class="flex justify-between items-end mt-12">
            <div class="flex items-center space-x-4">
                <span class="bg-white px-6 py-3 rounded-xl font-bold text-gray-700 shadow-sm">Tổng tiền</span>
                <div id="total-price" class="bg-white px-6 py-3 rounded-xl shadow-sm font-bold text-pink-600">0 đ</div>
            </div>
            <a href="/user/checkout" class="bg-pink-500 hover:bg-pink-600 text-white px-8 py-3 rounded-xl font-bold shadow-md transition">
                Thanh toán
            </a>
        </div>
    </div>
</section>
<script>
    // Hàm gọi API tăng/giảm số lượng
    async function updateQuantity(maSanPham, thayDoi) {
        console.log("Đang gửi lệnh update cho SP:", maSanPham); // Bật F12 lên xem log này
        const token = localStorage.getItem('token');
        
        try {
            const response = await fetch('http://localhost:3000/api/cart/update', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token 
                },
                body: JSON.stringify({ 
                    ma_san_pham: maSanPham, 
                    thay_doi: thayDoi 
                })
            });
            
            if (response.ok) {
                location.reload();
            } else {
                alert("Có lỗi xảy ra khi cập nhật!");
            }
        } catch (error) {
            console.error("Lỗi:", error);
        }
    }

    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('token');
        const cartContainer = document.getElementById('cart-items');
        const totalPriceEl = document.getElementById('total-price');

        try {
            const response = await fetch('http://localhost:3000/api/cart', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                let total = 0;
                cartContainer.innerHTML = ''; 

                result.data.forEach(item => {
                    const sanPham = item.san_pham;
                    const gia = sanPham.gia_khuyen_mai || sanPham.gia;
                    
                    // Lấy mảng ảnh từ include bằng tên liên kết mới
                    const mangAnh = sanPham.danh_sach_anh; 
                    
                    // Kiểm tra nếu có ảnh thì lấy ảnh đầu tiên, nếu không thì dùng ảnh mặc định
                    const linkAnh = (mangAnh && mangAnh.length > 0) ? mangAnh[0].duong_dan_anh : '/images/logo.jpg';

                    total += gia * item.so_luong;

                    cartContainer.innerHTML += `
                        <div class="bg-gray-200 p-4 rounded-2xl flex items-center justify-between shadow-sm">
                            <div class="flex items-center space-x-4 w-1/2">
                                <img src="${linkAnh}" 
                                     alt="${sanPham.ten_san_pham}" 
                                     style="width: 60px; height: 60px; object-fit: cover;" 
                                     class="rounded-lg flex-shrink-0">
                                     
                                <div class="bg-white px-4 py-2 rounded-lg text-gray-700 font-bold flex-1 truncate">${sanPham.ten_san_pham}</div>
                            </div>
                            <div class="bg-white px-4 py-2 rounded-lg text-gray-700 font-bold">${gia.toLocaleString()} đ</div>
                            <div class="bg-white px-4 py-2 rounded-lg text-gray-700 font-bold">${item.so_luong}</div>
                            <div class="flex items-center space-x-2">
                                <button onclick="updateQuantity(${item.ma_san_pham}, 1)" class="bg-white w-8 h-8 rounded-lg font-bold hover:bg-gray-100">+</button>
                                <button onclick="updateQuantity(${item.ma_san_pham}, -1)" class="bg-white w-8 h-8 rounded-lg font-bold hover:bg-gray-100">-</button>
                            </div>
                        </div>
                    `;
                });
                totalPriceEl.textContent = total.toLocaleString() + ' đ';
            }
        } catch (err) {
            console.error(err);
        }
    });
</script>
@endsection