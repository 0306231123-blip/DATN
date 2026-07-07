@extends('layouts.user')
@section('title', 'Checkout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-checkout.css') }}">
@endsection

@section('content')
<section class="user-section" style="position: relative;">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-4 max-w-[64rem] mx-auto">
        <a href="/user/cart" class="inline-flex items-center text-gray-500 hover:text-pink-600 font-bold transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại Giỏ hàng
        </a>
    </div>

    <div class="checkout-grid">
        
        {{-- Left Column --}}
        <div class="checkout-column">
            {{-- Personal Info Form --}}
            <div class="checkout-address">
                <label class="checkout-address__label">
                    Thông tin người nhận <span class="checkout-address__label-required">*</span>
                </label>
                
                <div class="checkout-address__fields mb-6">
                    <div>
                        <input type="text" id="ho_ten" placeholder="Họ và tên người nhận..." class="checkout-address__input">
                    </div>
                    <div>
                        <input type="text" id="so_dien_thoai" placeholder="Số điện thoại người nhận..." class="checkout-address__input">
                    </div>
                </div>

                <hr class="my-6 border-pink-100">

                <label class="checkout-address__label">
                    Địa chỉ giao hàng <span class="checkout-address__label-required">*</span>
                    <span class="checkout-address__label-hint">Bắt buộc nhập đầy đủ thông tin bên dưới</span>
                </label>
                
                <div class="checkout-address__fields">
                    <div>
                        <select id="province" required class="checkout-address__select">
                            <option value="" disabled selected>1. Chọn Tỉnh / Thành phố (Chuẩn mới)</option>
                        </select>
                    </div>
                    <div>
                        <select id="ward" required class="checkout-address__select cursor-pointer">
                            <option value="" disabled selected>2. Chọn Phường / Xã</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" id="street" required placeholder="3. Nhập số nhà, tên đường..." class="checkout-address__input">
                    </div>
                </div>
            </div>

            {{-- Selected Products Form --}}
            <div class="checkout-address">
                <label class="checkout-address__label mb-4">
                    Sản phẩm đã chọn
                </label>
                <div id="checkout-products-list" class="flex flex-col">
                    <p class="text-gray-500 text-sm italic py-2">Đang tải...</p>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="checkout-column">
            {{-- Payment Summary --}}
            <div class="checkout-summary" style="width: 100%;">
                <div class="checkout-summary__title">Tổng thanh toán</div>
                
                <div class="checkout-summary__rows">
                    <div class="checkout-summary__row">
                        <span>Tạm tính:</span>
                        <span id="sub-total-price">Đang tính...</span>
                    </div>
                    <div id="discount-row" class="checkout-summary__row checkout-summary__row--discount hidden">
                        <span>Mã giảm giá (<span id="applied-voucher-name"></span>):</span>
                        <span id="discount-amount">-0 VNĐ</span>
                    </div>
                    <div class="checkout-summary__row">
                        <span>Phí giao hàng:</span>
                        <span id="shipping-fee">Chưa tính</span>
                    </div>
                    <div class="checkout-summary__row checkout-summary__row--total">
                        <span class="checkout-summary__total-label">Cần thanh toán:</span>
                        <span id="final-price" class="checkout-summary__total-value">Đang tính...</span>
                    </div>
                </div>

                <div class="checkout-voucher">
                    <div class="flex justify-between items-center mb-2">
                        <label class="checkout-voucher__label mb-0">Mã khuyến mãi (Voucher)</label>
                        <button id="btn-show-vouchers" class="text-pink-500 text-sm font-bold hover:text-pink-600 transition flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            Chọn Voucher
                        </button>
                    </div>
                    <div class="checkout-voucher__input-group">
                        <input type="text" id="voucher-code" class="checkout-voucher__input" placeholder="Nhập mã giảm giá...">
                        <button id="btn-apply-voucher" class="checkout-voucher__btn">
                            Áp dụng
                        </button>
                    </div>
                    <p id="voucher-msg" class="checkout-voucher__msg hidden"></p>
                </div>
            </div>

            <div class="checkout-payment-title" style="width: 100%;">Phương thức thanh toán (Payment Method)</div>
            
            <div class="checkout-payment-methods" style="width: 100%;">
                <label class="checkout-payment-option">
                    <span class="checkout-payment-option__label checkout-payment-option__label--active">Banking</span>
                    <input type="radio" name="payment" value="banking" class="checkout-payment-option__radio" checked>
                </label>
                <label class="checkout-payment-option">
                    <span class="checkout-payment-option__label">Momo</span>
                    <input type="radio" name="payment" value="momo" class="checkout-payment-option__radio">
                </label>
                <label class="checkout-payment-option">
                    <span class="checkout-payment-option__label">Thanh toán khi nhận hàng (COD)</span>
                    <input type="radio" name="payment" value="cod" class="checkout-payment-option__radio">
                </label>
            </div>

            <div class="checkout-qr hidden">
                <img src="" class="checkout-qr__img">
                <span class="checkout-qr__text"></span>
            </div>
            
            <button id="btn-confirm-pay" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-4 rounded-xl transition shadow-lg mt-6 text-lg flex justify-center items-center">
                <span id="btn-text">Xác nhận đặt hàng</span>
                <svg id="loading-icon" class="checkout-spinner hidden ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </div>

    </div>

    {{-- Success Popup --}}
    <div id="success-popup" class="checkout-success-overlay hidden">
        <div class="checkout-success-popup relative max-w-2xl w-full" id="popup-content">
            <button id="btn-cancel-qr" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors hidden z-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <div id="success-content" class="hidden">
                <div class="flex flex-col items-center text-center w-full">
                    <div class="checkout-success-popup__icon-wrap" id="success-icon-wrap">
                        <svg class="checkout-success-popup__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="checkout-success-popup__title w-full text-center" id="success-title" style="text-align: center;">Thành công!</h3>
                    <p class="checkout-success-popup__desc w-full text-center" id="success-desc" style="text-align: center;">Cảm ơn bạn. Đơn hàng của bạn đã được đặt thành công và đang được xử lý.</p>
                    <a href="/user/profileuser?tab=orders" id="btn-view-history" class="btn-dark mt-6" style="width: 100%; text-align: center; text-transform: uppercase; letter-spacing: 0.05em;">
                        Xem lịch sử đơn hàng
                    </a>
                </div>
            </div>

            <div id="bill-section" class="hidden">
                <h3 class="text-2xl font-black text-center text-gray-800 mb-6 uppercase tracking-wide">Hóa Đơn Của Bạn</h3>
                <div class="text-left text-base text-gray-700 space-y-3 mb-6 bg-gray-50 p-6 rounded-2xl border border-gray-200 shadow-sm">
                    <p class="flex justify-between"><strong class="text-gray-800">Khách hàng:</strong> <span id="bill-name" class="font-medium"></span></p>
                    <p class="flex justify-between"><strong class="text-gray-800">SĐT:</strong> <span id="bill-phone" class="font-medium"></span></p>
                    <p class="flex justify-between"><strong class="text-gray-800">Địa chỉ:</strong> <span id="bill-address" class="font-medium text-right w-2/3"></span></p>
                    <div class="mt-4 border-t border-gray-200 pt-4">
                        <strong class="block mb-3 text-pink-600 text-lg">Sản phẩm:</strong>
                        <div id="bill-products" class="max-h-60 overflow-y-auto custom-scrollbar pr-2 space-y-3"></div>
                    </div>
                    <div class="mt-4 border-t border-gray-300 pt-4 flex justify-between items-center font-bold text-xl text-gray-800">
                        <span>Tổng tiền:</span>
                        <span id="bill-total" class="text-pink-600 text-2xl"></span>
                    </div>
                </div>
                <button id="btn-proceed-pay" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-4 rounded-xl transition shadow-lg text-lg uppercase">
                    Thanh toán
                </button>
            </div>
            
            <div id="qr-payment-section" class="hidden flex-col items-center">
                <h3 class="checkout-success-popup__title" id="qr-title">Quét mã để thanh toán!</h3>
                <p id="qr-instruction" class="text-gray-800 font-bold mb-3 text-center">Vui lòng quét mã QR để thanh toán</p>
                <img id="popup-qr-img" src="" class="w-64 h-auto rounded-xl shadow-sm border border-gray-100 mb-2">
                <p class="text-xs text-red-500 font-medium text-center px-4 mb-2" id="qr-countdown-text">Đơn hàng sẽ tự động hủy sau <span id="qr-countdown" class="font-bold text-lg">03:00</span> phút nếu không nhận được thanh toán.</p>
                <p class="checkout-success-popup__desc text-gray-600">Sau khi quét mã thanh toán thành công, hệ thống sẽ tự động tạo đơn hàng cho bạn.</p>
                <button id="btn-done-qr" class="hidden">Đã thanh toán (Test)</button>
            </div>
        </div>
    </div>

    {{-- Voucher List Modal --}}
    <div id="voucher-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 transition-opacity duration-300">
        <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl transform transition-transform scale-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-gray-800 uppercase tracking-wide">🎁 Chọn Voucher</h3>
                <button id="close-voucher-modal" class="text-gray-400 hover:text-pink-500 bg-gray-50 hover:bg-pink-50 p-2 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="voucher-list-container" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                <p class="text-center text-gray-500 py-4 font-medium">Đang tải...</p>
            </div>
        </div>
    </div>
</section>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #fdf2f8; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #fbcfe8; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #f472b6; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', async function() {
        const btnPay = document.getElementById('btn-confirm-pay');
        const btnText = document.getElementById('btn-text');
        const loadingIcon = document.getElementById('loading-icon');
        const successPopup = document.getElementById('success-popup');
        const popupContent = document.getElementById('popup-content');
        const token = localStorage.getItem('token');
        
        const hoTenInput = document.getElementById('ho_ten');
        const soDienThoaiInput = document.getElementById('so_dien_thoai');

        if (token) {
            fetch('http://localhost:3000/api/auth/me', {
                headers: { 'Authorization': 'Bearer ' + token }
            })
            .then(res => res.json())
            .then(result => {
                if (result.success && result.data) {
                    hoTenInput.value = result.data.ho_ten || '';
                    soDienThoaiInput.value = result.data.so_dien_thoai || '';
                    
                    if (result.data.dia_chi) {
                        const parts = result.data.dia_chi.split(', ');
                        if (parts.length >= 3) {
                            const pName = parts[parts.length - 1];
                            const wName = parts[parts.length - 2];
                            const sName = parts.slice(0, parts.length - 2).join(', ');
                            
                            let checkExist = setInterval(function() {
                                if (provinceSelect.options.length > 1) {
                                    clearInterval(checkExist);
                                    let foundProvince = Array.from(provinceSelect.options).find(opt => opt.getAttribute('data-name') === pName);
                                    if (foundProvince) {
                                        provinceSelect.value = foundProvince.value;
                                        provinceSelect.dispatchEvent(new Event('change'));
                                        
                                        let checkWardExist = setInterval(function() {
                                            if (wardSelect.options.length > 1) {
                                                clearInterval(checkWardExist);
                                                let foundWard = Array.from(wardSelect.options).find(opt => opt.getAttribute('data-name') === wName);
                                                if (foundWard) {
                                                    wardSelect.value = foundWard.value;
                                                    streetInput.value = sName;
                                                }
                                            }
                                        }, 100);
                                    }
                                }
                            }, 100);
                        }
                    }
                }
            })
            .catch(err => console.error("Lỗi lấy thông tin cá nhân:", err));
        }

        // Khai báo biến lưu trạng thái Tiền & Voucher
        let baseTotal = 0;          // Tổng tiền hàng gốc
        let currentDiscount = 0;    // Số tiền được giảm
        let appliedVoucherId = null;// ID của voucher đã áp dụng
        let currentShippingFee = 0; // Phí giao hàng

        const subTotalEl = document.getElementById('sub-total-price');
        const finalPriceEl = document.getElementById('final-price');
        const discountRow = document.getElementById('discount-row');
        const discountAmountEl = document.getElementById('discount-amount');
        const appliedVoucherNameEl = document.getElementById('applied-voucher-name');
        
        const voucherInput = document.getElementById('voucher-code');
        const btnApplyVoucher = document.getElementById('btn-apply-voucher');
        const voucherMsg = document.getElementById('voucher-msg');

        // Các biến Form địa chỉ
        const provinceSelect = document.getElementById('province');
        const wardSelect = document.getElementById('ward');
        const streetInput = document.getElementById('street');

        // Lấy danh sách ID sản phẩm được chọn từ URL (chuyển từ giỏ hàng sang)
        const urlParams = new URLSearchParams(window.location.search);
        const itemsStr = urlParams.get('items');
        const selectedItems = itemsStr ? itemsStr.split(',').map(Number) : [];

        // 1. TẢI API TỈNH/THÀNH PHỐ
        fetch('https://provinces.open-api.vn/api/v2/p/')
            .then(res => res.json())
            .then(data => {
                data.forEach(province => {
                    let option = document.createElement('option');
                    option.value = province.code; 
                    option.text = province.name;
                    option.setAttribute('data-name', province.name);
                    provinceSelect.appendChild(option);
                });
            });

        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            
            // Tính phí giao hàng
            const provinceOption = provinceSelect.options[provinceSelect.selectedIndex];
            const provinceName = provinceOption ? provinceOption.getAttribute('data-name') : '';
            
            if (baseTotal >= 1999000) {
                currentShippingFee = 0;
            } else if (provinceName.includes('Hồ Chí Minh') || provinceName.includes('Ho Chi Minh')) {
                currentShippingFee = 20000;
            } else {
                currentShippingFee = 35000;
            }
            document.getElementById('shipping-fee').textContent = currentShippingFee > 0 ? '+ ' + currentShippingFee.toLocaleString() + ' VNĐ' : 'Miễn phí';
            updateFinalPrice();

            wardSelect.innerHTML = '<option value="" disabled selected>2. Chọn Phường / Xã</option>';
            fetch(`https://provinces.open-api.vn/api/v2/p/${provinceId}?depth=2`)
                .then(res => res.json())
                .then(data => {
                    if (data.wards) {
                        data.wards.forEach(ward => {
                            let option = document.createElement('option');
                            option.value = ward.code;
                            option.text = ward.name;
                            option.setAttribute('data-name', ward.name);
                            wardSelect.appendChild(option);
                        });
                    }
                });
        });

        // 2. TÍNH TIỀN TỪ GIỎ HÀNG
        async function loadCartTotal() {
            try {
                const response = await fetch('http://localhost:3000/api/cart', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const result = await response.json();

                if (result.success && result.data.length > 0) {
                    baseTotal = 0;
                    let hasValidItems = false;
                    let productsHtml = '';
                    result.data.forEach(item => {
                        // Chỉ tính những sản phẩm được check ở giỏ hàng (hoặc tính hết nếu URL không có items)
                        if (selectedItems.length === 0 || selectedItems.includes(item.ma_san_pham)) {
                            // Bỏ qua sản phẩm đã bị xóa khỏi hệ thống (san_pham = null)
                            if (!item.san_pham) {
                                console.warn('Sản phẩm mã', item.ma_san_pham, 'không còn tồn tại, bỏ qua.');
                                return;
                            }
                            const gia = item.san_pham.gia_khuyen_mai || item.san_pham.gia;
                            baseTotal += gia * item.so_luong;
                            hasValidItems = true;

                            const mangAnh = item.san_pham.danh_sach_anh; 
                            const linkAnh = (mangAnh && mangAnh.length > 0) ? mangAnh[0].duong_dan_anh : '/images/logo.jpg';

                            productsHtml += `
                                <div class="flex items-center gap-4 py-3 border-b border-gray-100 last:border-0">
                                    <div class="w-16 h-16 flex-shrink-0 bg-gray-50 rounded-lg overflow-hidden border border-gray-100">
                                        <img src="${linkAnh}" alt="${item.san_pham.ten_san_pham}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-grow min-w-0">
                                        <h4 class="font-bold text-gray-800 text-sm truncate" title="${item.san_pham.ten_san_pham}">${item.san_pham.ten_san_pham}</h4>
                                        <div class="flex items-center justify-between mt-1">
                                            <span class="text-sm font-medium text-pink-600">${new Intl.NumberFormat('vi-VN').format(gia)} VNĐ</span>
                                            <span class="text-sm text-gray-500 font-medium">x${item.so_luong}</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    });
                    
                    document.getElementById('checkout-products-list').innerHTML = productsHtml;
                    
                    if (!hasValidItems) {
                        alert('Không có sản phẩm nào được chọn hợp lệ!');
                        window.location.href = '/user/cart';
                        return;
                    }
                    
                    // Cập nhật giao diện
                    subTotalEl.textContent = baseTotal.toLocaleString() + ' VNĐ';
                    
                    // Cập nhật lại phí ship nếu đang chọn tỉnh
                    if (provinceSelect.value) {
                        provinceSelect.dispatchEvent(new Event('change'));
                    } else {
                        updateFinalPrice();
                    }
                } else {
                    alert('Giỏ hàng của bạn đang trống!');
                    window.location.href = '/user/cart';
                }
            } catch (err) {
                console.error('Lỗi tính tiền:', err);
                finalPriceEl.textContent = 'Lỗi tải dữ liệu';
            }
        }
        
        function updateFinalPrice() {
            let final = baseTotal + currentShippingFee - currentDiscount;
            if (final < 0) final = 0; // Không để âm tiền
            finalPriceEl.textContent = final.toLocaleString() + ' VNĐ';
        }

        loadCartTotal(); // Khởi chạy lúc mở trang

        // ==========================================
        // 3. XỬ LÝ NÚT "ÁP DỤNG VOUCHER"
        // ==========================================
        btnApplyVoucher.addEventListener('click', async function() {
            const code = voucherInput.value.trim().toUpperCase();
            
            if (!code) {
                showVoucherMsg('Vui lòng nhập mã giảm giá!', 'error');
                return;
            }

            btnApplyVoucher.disabled = true;
            btnApplyVoucher.textContent = '...';

            try {
                const response = await fetch('http://localhost:3000/api/voucher/check', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token // Gửi token để Backend check User
                    },
                    body: JSON.stringify({ 
                        ma_code: code, 
                        tong_tien_hang: baseTotal 
                    })
                });

                const result = await response.json();

                if (result.success) {
                    // Áp dụng thành công
                    showVoucherMsg(result.message, 'success');
                    
                    appliedVoucherId = result.data.ma_khuyen_mai;
                    currentDiscount = result.data.so_tien_giam;
                    
                    // Hiện dòng giảm giá
                    discountRow.classList.remove('hidden');
                    appliedVoucherNameEl.textContent = code;
                    discountAmountEl.textContent = '-' + currentDiscount.toLocaleString() + ' VNĐ';
                    
                    // Cập nhật lại tổng tiền
                    updateFinalPrice();

                    // Khóa ô nhập (Mỗi đơn chỉ 1 mã)
                    voucherInput.disabled = true;
                    btnApplyVoucher.style.display = 'none';
                } else {
                    // Báo lỗi (Chưa đạt đơn tối thiểu, hết hạn, hoặc đã dùng rồi...)
                    showVoucherMsg('🛑 ' + result.message, 'error');
                    resetVoucher();
                }
            } catch (error) {
                showVoucherMsg('Lỗi kết nối máy chủ!', 'error');
            } finally {
                btnApplyVoucher.disabled = false;
                btnApplyVoucher.textContent = 'Áp dụng';
            }
        });

        function showVoucherMsg(msg, type) {
            voucherMsg.textContent = msg;
            voucherMsg.classList.remove('hidden', 'checkout-voucher__msg--error', 'checkout-voucher__msg--success');
            if (type === 'error') voucherMsg.classList.add('checkout-voucher__msg--error');
            else voucherMsg.classList.add('checkout-voucher__msg--success');
        }

        function resetVoucher() {
            appliedVoucherId = null;
            currentDiscount = 0;
            discountRow.classList.add('hidden');
            updateFinalPrice();
        }

        // ==========================================
        // 3.5. XỬ LÝ MODAL CHỌN VOUCHER
        // ==========================================
        const btnShowVouchers = document.getElementById('btn-show-vouchers');
        const voucherModal = document.getElementById('voucher-modal');
        const closeVoucherModal = document.getElementById('close-voucher-modal');
        const voucherListContainer = document.getElementById('voucher-list-container');

        btnShowVouchers.addEventListener('click', async () => {
            voucherModal.classList.remove('hidden');
            voucherModal.classList.add('flex');
            
            try {
                const res = await fetch('http://localhost:3000/api/voucher/active');
                const result = await res.json();
                
                if (result.success && result.data.length > 0) {
                    let html = '';
                    result.data.forEach(v => {
                        let desc = v.loai_giam === 'tien_mat' ? 
                                   `Giảm ${v.gia_tri.toLocaleString()} VNĐ` : 
                                   `Giảm ${v.gia_tri}% (Tối đa ${(v.giam_toi_da||0).toLocaleString()} VNĐ)`;
                        
                        let outOfStock = v.so_luong <= 0;
                        let percentUsed = Math.round(Math.max(5, 100 - Math.sqrt(v.so_luong) * 3));
                        if (outOfStock) percentUsed = 100;
                        let minOrder = `Đơn tối thiểu: ${v.don_toi_thieu.toLocaleString()} VNĐ`;
                        let isEligible = baseTotal >= v.don_toi_thieu && !outOfStock;
                        
                        let btnClass = isEligible ? 'bg-pink-500 hover:bg-pink-600 text-white shadow-sm' : 'bg-gray-100 text-gray-400 cursor-not-allowed';
                        let btnAction = isEligible ? `onclick="window.selectVoucher('${v.ma_code}')"` : 'disabled';
                        let borderClass = outOfStock ? 'border-gray-200 opacity-60 grayscale pointer-events-none' : (isEligible ? 'border-pink-200' : 'border-gray-200 opacity-80');
                        let btnText = outOfStock ? 'Hết lượt' : (isEligible ? 'Dùng ngay' : 'Chưa đủ đ/k');
                        
                        html += `
                        <div class="border ${borderClass} rounded-2xl p-4 flex flex-col gap-3 bg-white hover:shadow-md transition relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 w-12 h-12 bg-pink-50 rounded-full group-hover:scale-150 transition duration-500 z-0"></div>
                            <div class="relative z-10 flex justify-between items-center">
                                <div>
                                    <div class="font-black text-pink-600 text-xl tracking-tight leading-none mb-1">${v.ma_code}</div>
                                    <div class="text-gray-800 font-bold text-sm">${desc}</div>
                                    <div class="text-gray-500 text-xs font-medium mt-1">${minOrder} | Đã dùng ${percentUsed}%</div>
                                </div>
                                <button ${btnAction} class="px-5 py-2 rounded-full font-bold text-sm transition ${btnClass}">
                                    ${btnText}
                                </button>
                            </div>
                        </div>
                        `;
                    });
                    voucherListContainer.innerHTML = html;
                } else {
                    voucherListContainer.innerHTML = '<p class="text-center text-gray-500 py-8 font-medium">Hiện không có mã giảm giá nào phù hợp.</p>';
                }
            } catch (err) {
                voucherListContainer.innerHTML = '<p class="text-center text-red-500 py-4">Lỗi tải danh sách voucher.</p>';
            }
        });

        closeVoucherModal.addEventListener('click', () => {
            voucherModal.classList.remove('flex');
            voucherModal.classList.add('hidden');
        });
        
        window.selectVoucher = function(code) {
            voucherInput.value = code;
            voucherModal.classList.remove('flex');
            voucherModal.classList.add('hidden');
            btnApplyVoucher.click();
        };

        // ==========================================
        // 4. CHUYỂN ĐỔI PHƯƠNG THỨC THANH TOÁN
        // ==========================================
        const paymentRadios = document.querySelectorAll('input[name="payment"]');
        
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.checkout-payment-option__label').forEach(lbl => lbl.classList.remove('checkout-payment-option__label--active'));
                this.previousElementSibling.classList.add('checkout-payment-option__label--active');
            });
        });

        // ==========================================
        // 5. KIỂM TRA BẮT BUỘC & THANH TOÁN
        // ==========================================
        let pendingAddress = '';
        let pendingPaymentMethod = '';

        async function callCreateOrderAPI(fullAddress, paymentMethod) {
            try {
                const response = await fetch('http://localhost:3000/api/order/create', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token 
                    },
                    body: JSON.stringify({ 
                        ho_ten: document.getElementById('ho_ten').value.trim(),
                        so_dien_thoai: document.getElementById('so_dien_thoai').value.trim(),
                        dia_chi: fullAddress,
                        ma_khuyen_mai: appliedVoucherId,
                        so_tien_giam: currentDiscount,
                        phi_van_chuyen: currentShippingFee,
                        selected_items: selectedItems,
                        phuong_thuc_thanh_toan: paymentMethod
                    })
                });
                return await response.json();
            } catch (error) {
                console.error(error);
                return null;
            }
        }

        async function processCheckout() {
            const provinceOption = provinceSelect.options[provinceSelect.selectedIndex];
            const wardOption = wardSelect.options[wardSelect.selectedIndex];
            
            const provinceName = (provinceOption && provinceOption.value !== "") ? provinceOption.getAttribute('data-name') : null;
            const wardName = (wardOption && wardOption.value !== "") ? wardOption.getAttribute('data-name') : null;
            const street = streetInput.value.trim();
            
            const hoTen = document.getElementById('ho_ten').value.trim();
            const sdt = document.getElementById('so_dien_thoai').value.trim();

            if (!hoTen) { alert('🛑 Vui lòng nhập họ và tên người nhận!'); document.getElementById('ho_ten').focus(); return; }
            if (!sdt || !/^0\d{9}$/.test(sdt)) { alert('🛑 Vui lòng nhập số điện thoại hợp lệ (10 số, bắt đầu bằng 0)!'); document.getElementById('so_dien_thoai').focus(); return; }

            if (!provinceName) { alert('🛑 Vui lòng chọn Tỉnh / Thành phố!'); provinceSelect.focus(); return; }
            if (!wardName) { alert('🛑 Vui lòng chọn Phường / Xã!'); wardSelect.focus(); return; }
            if (!street || street.length < 5) { alert('🛑 Vui lòng nhập chi tiết Số nhà, tên đường!'); streetInput.focus(); return; }

            pendingAddress = `${street}, ${wardName}, ${provinceName}`;
            pendingPaymentMethod = document.querySelector('input[name="payment"]:checked').value;

            btnPay.disabled = true;
            btnPay.classList.add('checkout-qr__btn--disabled', 'cursor-not-allowed', 'opacity-70');
            btnText.textContent = 'Đang xử lý...';
            loadingIcon.classList.remove('hidden');

            const qrSection = document.getElementById('qr-payment-section');
            const popupQrImg = document.getElementById('popup-qr-img');
            const successTitle = document.getElementById('success-title');
            const successDesc = document.getElementById('success-desc');
            const successIconWrap = document.getElementById('success-icon-wrap');

            const billSection = document.getElementById('bill-section');
            const successContent = document.getElementById('success-content');

            if (pendingPaymentMethod === 'cod') {
                // Tạo đơn hàng ngay với COD
                const result = await callCreateOrderAPI(pendingAddress, pendingPaymentMethod);
                if (result && result.success) {
                    successPopup.classList.remove('hidden');
                    qrSection.classList.add('hidden');
                    billSection.classList.add('hidden');
                    successContent.classList.remove('hidden');
                    
                    document.getElementById('btn-cancel-qr').classList.add('hidden');

                    setTimeout(() => {
                        successPopup.classList.add('checkout-success-overlay--visible');
                        popupContent.classList.add('checkout-success-popup--visible');
                    }, 10);
                } else {
                    alert(result ? 'Lỗi đặt hàng: ' + result.message : 'Không thể kết nối đến server Node.js!');
                    resetButton();
                }
            } else {
                // Hiển thị Bill trước, KHÔNG TẠO ĐƠN HÀNG NGAY
                document.getElementById('bill-name').textContent = hoTen;
                document.getElementById('bill-phone').textContent = sdt;
                document.getElementById('bill-address').textContent = pendingAddress;
                
                const checkoutImages = document.querySelectorAll('#checkout-products-list img');
                const checkoutProducts = document.querySelectorAll('#checkout-products-list .flex-grow h4');
                const checkoutPrices = document.querySelectorAll('#checkout-products-list .flex-grow .text-pink-600');
                const checkoutQtys = document.querySelectorAll('#checkout-products-list .flex-grow .text-gray-500');
                
                let billProductsHtml = '';
                for(let i=0; i<checkoutProducts.length; i++){
                    let imgSrc = checkoutImages[i] ? checkoutImages[i].src : '/images/logo.jpg';
                    billProductsHtml += `
                        <div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                            <div class="w-12 h-12 flex-shrink-0 bg-gray-50 rounded-md overflow-hidden border border-gray-200">
                                <img src="${imgSrc}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="text-sm font-medium text-gray-800 truncate" title="${checkoutProducts[i].textContent}">${checkoutProducts[i].textContent}</div>
                                <div class="flex justify-between mt-1 text-sm">
                                    <span class="text-gray-500">SL: ${checkoutQtys[i].textContent.replace('x', '')}</span>
                                    <span class="text-pink-600 font-bold">${checkoutPrices[i].textContent}</span>
                                </div>
                            </div>
                        </div>
                    `;
                }
                document.getElementById('bill-products').innerHTML = billProductsHtml;
                document.getElementById('bill-total').textContent = document.getElementById('final-price').textContent;

                successPopup.classList.remove('hidden');
                billSection.classList.remove('hidden');
                
                qrSection.classList.add('hidden');
                qrSection.classList.remove('flex');
                successContent.classList.add('hidden');
                
                let amountStr = document.getElementById('final-price').textContent.replace(/\D/g, '');
                let amount = parseInt(amountStr);
                
                let nextOrderIdRes = await fetch('http://localhost:3000/api/next-order-id').then(r=>r.json()).catch(()=>({nextId: Math.floor(Math.random() * 1000000), nextIdStr: 'MOCK#01'}));
                let orderId = nextOrderIdRes.nextIdStr || ('#' + nextOrderIdRes.nextId);
                
                // Giải pháp siêu việt vượt tường lửa (Windows Firewall) + vượt lỗi CORS
                // Dùng Backend Node.js của chính máy bạn để gọi lên webhook.site
                fetch('http://localhost:3000/api/create-mock-webhook', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        html_content: `<html><body style="font-family:sans-serif;text-align:center;padding:50px;background:#fdf2f8;"><h1 style="color:#db2777;font-size:30px">✅ Thanh toán thành công!</h1><p style="font-size:24px;font-weight:bold;color:#db2777;margin:15px 0">${amount.toLocaleString('vi-VN')} VNĐ</p><p style="font-size:18px">Đơn hàng <strong>${orderId}</strong> của bạn sẽ được xác nhận sớm!!</p><p style="color:#666">Vui lòng kiểm tra màn hình máy tính.</p></body></html>`
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) throw new Error('Lỗi tạo webhook');
                    
                    let webhookUrl = data.url;
                    
                    // Tạo mã QR trỏ về trang web webhook (công cộng, ai quét cũng được, mạng nào cũng được)
                    popupQrImg.src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(webhookUrl)}`;
                    
                    if (pendingPaymentMethod === 'momo') {
                        document.getElementById('qr-title').textContent = 'Quét mã Momo!';
                    } else {
                        document.getElementById('qr-title').textContent = 'Quét mã Ngân Hàng!';
                    }
                    
                    // Đặt hàm kiểm tra xem điện thoại đã quét QR chưa bằng cách gọi API Node.js (để Node.js hỏi lại webhook)
                    let checkPaymentInterval = setInterval(async () => {
                        try {
                            let res = await fetch(`http://localhost:3000/api/check-mock-webhook/${data.uuid}?t=${Date.now()}`);
                            let reqs = await res.json();
                            if (reqs.success && reqs.requests && reqs.requests.length > 0) {
                                clearInterval(checkPaymentInterval);
                                // Tự động nhấn nút hoàn thành
                                const btnDoneQr = document.getElementById('btn-done-qr');
                                if(btnDoneQr) btnDoneQr.click();
                            }
                        } catch(e) {}
                    }, 2000);
                    
                    popupQrImg.classList.remove('hidden');

                    const instruction = document.getElementById('qr-instruction');
                    instruction.classList.remove('text-green-500');
                    instruction.classList.add('text-gray-800');
                    instruction.textContent = 'Vui lòng quét mã QR bằng điện thoại để tự động thanh toán';

                    document.getElementById('btn-cancel-qr').classList.remove('hidden');

                    setTimeout(() => {
                        successPopup.classList.add('checkout-success-overlay--visible');
                        popupContent.classList.add('checkout-success-popup--visible');
                    }, 10);
                    
                    resetButton(); // Bỏ loading ở nút xác nhận ngoài form
                    
                    // Truyền ID vào hàm đếm ngược để có thể dọn dẹp interval
                    startQrCountdown(checkPaymentInterval);
                })
                .catch(err => {
                    alert('Lỗi tạo mã QR, vui lòng kiểm tra kết nối mạng hoặc Backend Node.js chưa chạy!');
                    resetButton();
                });
            }
        }

        let qrCountdownInterval = null;
        function startQrCountdown(pollingIntervalId) {
            if (qrCountdownInterval) clearInterval(qrCountdownInterval);
            let timeLeft = 180;
            const countdownEl = document.getElementById('qr-countdown');
            if (countdownEl) countdownEl.textContent = '03:00';
            
            qrCountdownInterval = setInterval(() => {
                timeLeft--;
                if (timeLeft < 0) {
                    clearInterval(qrCountdownInterval);
                    if (pollingIntervalId) clearInterval(pollingIntervalId);
                    alert('Hết thời gian thanh toán! Đơn hàng đã bị hủy.');
                    successPopup.classList.remove('checkout-success-overlay--visible');
                    popupContent.classList.remove('checkout-success-popup--visible');
                    setTimeout(() => {
                        successPopup.classList.add('hidden');
                    }, 300);
                    return;
                }
                
                let m = Math.floor(timeLeft / 60);
                let s = timeLeft % 60;
                if (countdownEl) countdownEl.textContent = `${m < 10 ? '0'+m : m}:${s < 10 ? '0'+s : s}`;
            }, 1000);
        }

        document.getElementById('btn-proceed-pay').addEventListener('click', function() {
            document.getElementById('bill-section').classList.add('hidden');
            document.getElementById('qr-payment-section').classList.remove('hidden');
            document.getElementById('qr-payment-section').classList.add('flex');
            startQrCountdown();
        });

        btnPay.addEventListener('click', function() {
            if (this.disabled) return;
            processCheckout();
        });

        function resetButton() {
            btnPay.disabled = false;
            btnPay.classList.remove('checkout-qr__btn--disabled', 'cursor-not-allowed', 'opacity-70');
            btnText.textContent = 'Xác nhận đặt hàng';
            loadingIcon.classList.add('hidden');
        }

        // "Tricks" bảo vệ đồ án: Nút giả lập thanh toán thành công
        const btnDoneQr = document.getElementById('btn-done-qr');
        const popupQrImg = document.getElementById('popup-qr-img');
        if (btnDoneQr) {
            btnDoneQr.addEventListener('click', async function() {
                const instruction = document.getElementById('qr-instruction');
                instruction.textContent = 'Đang xác nhận thanh toán và tạo đơn hàng...';
                
                // Gọi API tạo đơn hàng lúc này
                const result = await callCreateOrderAPI(pendingAddress, pendingPaymentMethod);
                
                if (result && result.success) {
                    document.getElementById('qr-title').textContent = 'Thanh toán thành công!';
                    document.getElementById('qr-title').classList.add('text-green-500');
                    
                    instruction.textContent = 'Đã thanh toán thành công. Hệ thống đang chuyển hướng...';
                    instruction.classList.add('text-green-500');
                    
                    // Ẩn ảnh QR đi cho thực tế
                    popupQrImg.classList.add('hidden');
                    btnDoneQr.classList.add('hidden');
                    
                    // Ẩn luôn nút X vì đã báo thành công
                    document.getElementById('btn-cancel-qr').classList.add('hidden');
                    
                    setTimeout(() => {
                        window.location.href = '/user/profileuser?tab=orders';
                    }, 1500);
                } else {
                    alert(result ? 'Lỗi tạo đơn: ' + result.message : 'Lỗi kết nối khi thanh toán!');
                    instruction.textContent = 'Vui lòng quét mã QR để thanh toán';
                }
            });
        }

        // Xử lý khi nhấn nút X để hủy thanh toán QR
        document.getElementById('btn-cancel-qr').addEventListener('click', function() {
            if (qrCountdownInterval) clearInterval(qrCountdownInterval);
            successPopup.classList.remove('checkout-success-overlay--visible');
            popupContent.classList.remove('checkout-success-popup--visible');
            setTimeout(() => {
                successPopup.classList.add('hidden');
            }, 300);
        });

    });
</script>
@endsection