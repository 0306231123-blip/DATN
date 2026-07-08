@extends('layouts.user')
@section('title', 'Giỏ hàng')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-cart.css') }}">
@endsection

@section('content')
<section class="user-section">
    <div class="max-w-6xl mx-auto mt-4">
        <div class="mb-4">
            <a href="/user/product" class="inline-flex items-center text-gray-500 hover:text-pink-600 font-bold transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Tiếp tục mua sắm
            </a>
        </div>

                <div class="flex flex-col space-y-4">
            
            <!-- Table Header (Shopee style) -->
            <div class="bg-white p-4 shadow-sm border border-gray-100 hidden md:flex items-center text-gray-500 text-sm font-medium mt-4">
                <div class="w-12 flex justify-center">
                    <input type="checkbox" id="check-all-top" class="w-4 h-4 accent-pink-500 rounded-sm cursor-pointer" onchange="toggleCheckAll(this)">
                </div>
                <div class="flex-1 ml-4">Sản Phẩm</div>
                <div class="w-32 text-center">Đơn Giá</div>
                <div class="w-32 text-center">Số Lượng</div>
                <div class="w-32 text-center">Số Tiền</div>
                <div class="w-24 text-center">Thao Tác</div>
            </div>

            <!-- Container chứa sản phẩm -->
            <div id="cart-container" class="space-y-4 md:space-y-0 shadow-sm border border-gray-100 bg-white"></div>

            <!-- Checkout Bottom Bar (Sticky) -->
            <div class="bg-white p-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)] border-t border-gray-200 sticky bottom-0 z-40 flex flex-col md:flex-row items-center justify-between gap-4 mt-8 w-full">
                <!-- Select all -->
                <div class="flex items-center space-x-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="check-all" class="w-4 h-4 accent-pink-500 rounded-sm cursor-pointer mr-2" onchange="toggleCheckAll(this)">
                        <label for="check-all" class="text-gray-800 cursor-pointer text-sm">Chọn Tất Cả (<span id="selected-count">0</span>)</label>
                    </div>
                </div>

                <!-- Price and Checkout -->
                <div class="flex items-center space-x-4">
                    <div class="text-right flex items-center space-x-2 md:space-x-4">
                        <span class="text-gray-800 text-sm hidden md:inline">Tổng thanh toán (<span id="selected-count-2">0</span> Sản phẩm):</span>
                        <div class="flex flex-col">
                            <span id="total-price" class="text-pink-600 text-xl font-medium">0 đ</span>
                            <span id="subtotal-price" class="hidden">0 đ</span>
                        </div>
                    </div>
                    <button onclick="proceedToCheckout()" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-2 px-8 text-sm transition rounded-sm">
                        Mua Hàng
                    </button>
                </div>
            </div>
            
        </div>
    </div>
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
                        <div class="flex flex-col md:flex-row items-center bg-white p-4 border-b border-gray-100 hover:bg-gray-50 transition duration-200">
                            <div class="w-12 flex justify-center mb-3 md:mb-0">
                                <input type="checkbox" class="cart-item-check w-4 h-4 accent-pink-500 rounded-sm cursor-pointer" 
                                    data-id="${item.ma_san_pham}" 
                                    data-price="${gia}" 
                                    onchange="calculateTotal()">
                            </div>
                            
                            <div class="flex-1 flex items-center ml-0 md:ml-4 mb-3 md:mb-0 w-full md:w-auto">
                                <img src="${linkAnh}" alt="${sanPham.ten_san_pham}" class="w-20 h-20 object-cover border border-gray-100">
                                <div class="ml-3 flex-1">
                                    <h3 class="text-gray-800 text-sm hover:text-pink-600 transition cursor-pointer line-clamp-2">${sanPham.ten_san_pham}</h3>
                                    <p class="text-xs text-gray-500 mt-1">Còn lại: ${soLuongTon}</p>
                                    <p class="text-[10px] text-gray-400 mt-1">Đã thêm: ${new Date(item.ngay_them).toLocaleString('vi-VN')}</p>
                                </div>
                            </div>
                            
                            <div class="w-full md:w-32 text-center text-gray-600 text-sm mb-3 md:mb-0 hidden md:block">
                                ${formatPrice(gia)}
                            </div>
                            
                            <div class="flex items-center justify-center space-x-0 w-full md:w-32 mb-3 md:mb-0">
                                <button onclick="updateQty(${item.ma_san_pham}, -1, ${soLuongTon})" class="w-8 h-8 border border-gray-200 text-gray-500 hover:bg-gray-50 transition flex items-center justify-center rounded-none">-</button>
                                
                                <input type="number" id="qty-${item.ma_san_pham}" 
                                    value="${item.so_luong}" 
                                    min="1" max="${soLuongTon}" 
                                    onchange="handleManualInput(${item.ma_san_pham}, ${soLuongTon})"
                                    class="w-12 h-8 text-center border-t border-b border-transparent border-t-gray-200 border-b-gray-200 focus:outline-none focus:border-pink-300 text-gray-800 text-sm rounded-none">
                                    
                                <button onclick="updateQty(${item.ma_san_pham}, 1, ${soLuongTon})" class="w-8 h-8 border border-gray-200 text-gray-500 hover:bg-gray-50 transition flex items-center justify-center rounded-none">+</button>
                            </div>
                            
                            <div class="w-full md:w-32 text-center text-pink-600 text-sm font-medium mb-3 md:mb-0 hidden md:block">
                                ${formatPrice(gia * item.so_luong)}
                            </div>
                            
                            <div class="w-full md:w-24 text-center">
                                <button onclick="removeItem(${item.ma_san_pham})" class="text-gray-600 hover:text-pink-600 transition text-sm">
                                    Xóa
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                updateCartIconBadge(); // Cập nhật số đỏ trên Navbar
            } else {
                cartContainer.innerHTML = '<div class="bg-white p-10 shadow-sm text-center py-20"><span class="text-5xl mb-4 block">🛒</span><p class="text-gray-500 mb-4 font-medium">Giỏ hàng của bạn còn trống.</p><a href="/user/product" class="inline-block bg-pink-500 text-white font-medium py-2 px-8 rounded-sm hover:bg-pink-600 transition shadow-sm uppercase text-sm">Mua ngay</a></div>';
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

        document.getElementById('selected-count').innerText = count;
        document.getElementById('subtotal-price').innerText = formatPrice(total);
        document.getElementById('total-price').innerText = formatPrice(total);
        
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
        
        if (document.getElementById('check-all-top')) document.getElementById('check-all-top').checked = source.checked;
        if (document.getElementById('check-all')) document.getElementById('check-all').checked = source.checked;
        
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