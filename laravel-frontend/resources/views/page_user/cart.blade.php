@extends('layouts.user')
@section('title', 'Giỏ hàng')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-cart.css') }}">
@endsection

@section('content')
<section class="user-section">
    <div class="max-w-5xl mx-auto mt-8">
        <div class="mb-4">
            <a href="/user/product" class="inline-flex items-center text-gray-500 hover:text-pink-600 font-bold transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Tiếp tục mua sắm
            </a>
        </div>
        <div class="bg-[#fefcf8] p-6 rounded-3xl shadow-sm mb-6 flex justify-between items-center sticky top-20 z-40 border border-pink-100">
            <div class="flex items-center space-x-4">
                <input type="checkbox" id="check-all" class="w-5 h-5 accent-pink-500 rounded-lg cursor-pointer" onchange="toggleCheckAll(this)">
                <label for="check-all" class="font-bold text-gray-800 cursor-pointer">Chọn tất cả</label>
            </div>
            <div class="flex items-center space-x-6">
                <p class="text-gray-600 font-bold">Tổng tiền (<span id="selected-count">0</span> SP): 
                    <span id="total-price" class="text-pink-600 text-xl ml-2">0 đ</span>
                </p>
                <button onclick="proceedToCheckout()" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 px-8 rounded-full transition shadow-md">
                    THANH TOÁN
                </button>
            </div>
        </div>

        <div id="cart-container" class="space-y-4">
            </div>
    </div>
</section>

<script>
    // Hàm Format Tiền (Bỏ .00, dùng dấu chấm)
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price) + ' đ'; 
    }

    // 1. GỌI API ĐỂ LOAD GIỎ HÀNG
    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('token');
        const cartContainer = document.getElementById('cart-container');

        if (!token) {
            cartContainer.innerHTML = '<div class="bg-[#fefcf8] p-10 rounded-3xl border border-pink-100 shadow-sm text-center"><span class="text-5xl mb-4 block">🔒</span><p class="text-xl font-bold text-gray-800 mb-4">Bạn cần đăng nhập để xem giỏ hàng.</p><a href="/login" class="inline-block bg-pink-500 text-white font-bold py-3 px-8 rounded-full hover:bg-pink-600 transition shadow-md">Đăng nhập ngay</a></div>';
            return;
        }

        try {
            const response = await fetch('http://localhost:3000/api/cart', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                cartContainer.innerHTML = ''; // Xóa chữ loading

                result.data.forEach(item => {
                    const sanPham = item.san_pham;
                    const gia = sanPham.gia_khuyen_mai || sanPham.gia;
                    const soLuongTon = sanPham.so_luong_ton || 100; // Thay bằng cột tồn kho thực tế nếu có
                    
                    const mangAnh = sanPham.danh_sach_anh; 
                    const linkAnh = (mangAnh && mangAnh.length > 0) ? mangAnh[0].duong_dan_anh : '/images/logo.jpg';

                    // HTML CHO MỖI SẢN PHẨM
                    cartContainer.innerHTML += `
                        <div class="flex items-center bg-[#fefcf8] p-4 rounded-2xl border border-pink-50 shadow-sm hover:shadow-md transition duration-300 mb-4">
                            <input type="checkbox" class="cart-item-check w-5 h-5 accent-pink-500 mr-4 rounded cursor-pointer" 
                                data-id="${item.ma_san_pham}" 
                                data-price="${gia}" 
                                onchange="calculateTotal()">
                            
                            <img src="${linkAnh}" alt="${sanPham.ten_san_pham}" class="w-20 h-20 object-cover rounded-xl border border-gray-100">
                            
                            <div class="flex-1 ml-4">
                                <h3 class="font-bold text-gray-800 text-lg hover:text-pink-600 transition cursor-pointer">${sanPham.ten_san_pham}</h3>
                                <p class="text-sm text-gray-500 mt-1">Còn lại: ${soLuongTon} SP</p>
                            </div>
                            
                            <div class="w-32 text-center font-black text-pink-600 text-lg">
                                ${formatPrice(gia)}
                            </div>
                            
                            <div class="flex items-center justify-center space-x-2 w-32 ml-4">
                                <button onclick="updateQty(${item.ma_san_pham}, -1, ${soLuongTon})" class="w-8 h-8 bg-pink-50 rounded-full text-pink-600 hover:bg-pink-100 font-bold transition flex items-center justify-center">-</button>
                                
                                <input type="number" id="qty-${item.ma_san_pham}" 
                                    value="${item.so_luong}" 
                                    min="1" max="${soLuongTon}" 
                                    onchange="handleManualInput(${item.ma_san_pham}, ${soLuongTon})"
                                    class="w-12 h-8 text-center bg-white rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-pink-300 font-bold text-gray-800">
                                    
                                <button onclick="updateQty(${item.ma_san_pham}, 1, ${soLuongTon})" class="w-8 h-8 bg-pink-50 rounded-full text-pink-600 hover:bg-pink-100 font-bold transition flex items-center justify-center">+</button>
                            </div>
                            
                            <button onclick="removeItem(${item.ma_san_pham})" class="ml-6 text-gray-400 hover:text-red-500 transition bg-gray-50 hover:bg-red-50 p-2 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    `;
                });
                
                updateCartIconBadge(); // Cập nhật số đỏ trên Navbar
            } else {
                cartContainer.innerHTML = '<div class="bg-[#fefcf8] p-10 rounded-3xl border border-pink-100 shadow-sm text-center"><span class="text-5xl mb-4 block">🛒</span><p class="text-xl font-bold text-gray-800 mb-4">Giỏ hàng của bạn đang trống.</p><a href="/user/product" class="inline-block bg-pink-500 text-white font-bold py-3 px-8 rounded-full hover:bg-pink-600 transition shadow-md">Tiếp tục mua sắm</a></div>';
            }
        } catch (err) {
            console.error(err);
        }
    });

    // 2. GỌI API CẬP NHẬT SỐ LƯỢNG KHI BẤM +/- HOẶC GÕ TAY
    async function updateQuantityAPI(maSanPham, soLuongMoi) {
        const token = localStorage.getItem('token');
        try {
            await fetch('http://localhost:3000/api/cart/update', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token 
                },
                body: JSON.stringify({ 
                    ma_san_pham: maSanPham, 
                    thay_doi: 0, 
                    so_luong: soLuongMoi 
                })
            });
        } catch (error) {
            console.error("Lỗi cập nhật giỏ hàng:", error);
        }
    }

    // 3. XỬ LÝ NÚT +/- 
    function updateQty(id, change, maxStock) {
        const inputObj = document.getElementById(`qty-${id}`);
        let val = parseInt(inputObj.value) + change;
        
        if (val < 1) val = 1;
        if (val > maxStock) {
            val = maxStock;
            alert(`Sản phẩm này chỉ còn ${maxStock} trong kho!`);
        }

        inputObj.value = val;
        calculateTotal();
        updateQuantityAPI(id, val); 
    }

    // 4. XỬ LÝ KHÁCH TỰ GÕ SỐ
    function handleManualInput(id, maxStock) {
        const inputObj = document.getElementById(`qty-${id}`);
        let val = parseInt(inputObj.value);

        if (isNaN(val) || val < 1) {
            val = 1;
            alert('Số lượng tối thiểu là 1');
        } else if (val > maxStock) {
            val = maxStock;
            alert(`Rất tiếc, sản phẩm này chỉ còn ${maxStock} trong kho!`);
        }
        
        inputObj.value = val;
        calculateTotal();
        updateQuantityAPI(id, val); 
    }

    // 5. TÍNH TỔNG TIỀN DỰA TRÊN CHECKBOX
    function calculateTotal() {
        let total = 0;
        let count = 0;
        const checkboxes = document.querySelectorAll('.cart-item-check');
        const checkAllBox = document.getElementById('check-all');
        
        let allChecked = true;
        let hasItem = false;

        checkboxes.forEach(cb => {
            hasItem = true;
            if (cb.checked) {
                const id = cb.dataset.id;
                const price = parseFloat(cb.dataset.price);
                const qty = parseInt(document.getElementById(`qty-${id}`).value);
                
                total += (price * qty);
                count++;
            } else {
                allChecked = false;
            }
        });

        document.getElementById('total-price').innerText = formatPrice(total);
        document.getElementById('selected-count').innerText = count;
        
        if (hasItem) {
            checkAllBox.checked = allChecked;
        }
    }

    // 6. CHỌN TẤT CẢ
    function toggleCheckAll(source) {
        const checkboxes = document.querySelectorAll('.cart-item-check');
        checkboxes.forEach(cb => {
            cb.checked = source.checked;
        });
        calculateTotal();
    }

    // 7. CẬP NHẬT SỐ ĐỎ TRÊN NAVBAR (DÀNH RIÊNG CHO TRANG GIỎ HÀNG)
    function updateCartIconBadge() {
        const inputs = document.querySelectorAll('.cart-item-check');
        const badge = document.getElementById('cart-badge');
        
        if(badge && inputs.length > 0) {
            badge.innerText = inputs.length; 
            badge.classList.remove('hidden');
        } else if(badge) {
            badge.classList.add('hidden');
        }
    }

    // 8. CHUYỂN TRANG THANH TOÁN
    function proceedToCheckout() {
        const checkedItems = document.querySelectorAll('.cart-item-check:checked');
        if (checkedItems.length === 0) {
            alert('Vui lòng chọn ít nhất 1 sản phẩm để thanh toán!');
            return;
        }
        
        let selectedIds = [];
        checkedItems.forEach(cb => selectedIds.push(cb.dataset.id));
        console.log("Thanh toán các SP:", selectedIds);
        window.location.href = '/user/checkout?items=' + selectedIds.join(',');
    }

    // 9. HÀM XÓA SẢN PHẨM KHỎI GIỎ HÀNG
    async function removeItem(maSanPham) {
        if (!confirm('Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?')) {
            return; 
        }

        const token = localStorage.getItem('token');
        try {
            const response = await fetch('http://localhost:3000/api/cart/remove', {
                method: 'POST', 
                headers: { 
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token 
                },
                body: JSON.stringify({ ma_san_pham: maSanPham })
            });

            if (response.ok) {
                location.reload(); 
            } else {
                const data = await response.json();
                alert(data.message || 'Lỗi khi xóa sản phẩm!');
            }
        } catch (error) {
            console.error("Lỗi:", error);
        }
    }
</script>
@endsection