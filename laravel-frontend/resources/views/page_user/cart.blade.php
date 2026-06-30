@extends('layouts.user')
@section('title', 'Giỏ hàng')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-cart.css') }}">
@endsection

@section('content')
<section class="user-section">
    <div class="user-container--sm">
        
        <div id="cart-items" class="cart-items-list">
        </div>

        <div class="cart-footer">
            <div class="cart-footer__total-wrap">
                <span class="cart-footer__total-label">Tổng tiền</span>
                <div id="total-price" class="cart-footer__total-value">0 đ</div>
            </div>
            <a href="/user/checkout" class="btn-pink">
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
                        <div class="cart-item">
                            <div class="cart-item__left">
                                <img src="${linkAnh}" 
                                     alt="${sanPham.ten_san_pham}" 
                                     class="cart-item__img">
                                     
                                <div class="cart-item__name">${sanPham.ten_san_pham}</div>
                            </div>
                            <div class="cart-item__info-box">${gia.toLocaleString()} đ</div>
                            <div class="cart-item__info-box">${item.so_luong}</div>
                            <div class="cart-item__actions">
                                <button onclick="updateQuantity(${item.ma_san_pham}, 1)" class="cart-item__btn">+</button>
                                <button onclick="updateQuantity(${item.ma_san_pham}, -1)" class="cart-item__btn">-</button>
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