@extends('layouts.user')
@section('title', 'Checkout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-checkout.css') }}">
@endsection

@section('content')
<section class="user-section" style="position: relative;">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-4">
        <a href="/user/cart" class="inline-flex items-center text-gray-500 hover:text-pink-600 font-bold transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại Giỏ hàng
        </a>
    </div>
    
    <div class="checkout-grid">
        
        {{-- Left Column --}}
        <div class="checkout-column">
            {{-- Payment Summary --}}
            <div class="checkout-summary">
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
            
            {{-- Address Form --}}
            <div class="checkout-address">
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
                        <select id="ward" required class="checkout-address__select">
                            <option value="" disabled selected>2. Chọn Phường / Xã</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" id="street" required placeholder="3. Nhập số nhà, tên đường..." class="checkout-address__input">
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="checkout-column" style="align-items: center;">
            <div class="checkout-payment-title">Phương thức thanh toán (Payment Method)</div>
            
            <div class="checkout-payment-methods">
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

            <div class="checkout-qr">
                <span class="checkout-qr__text">Quét mã QR để thanh toán</span>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=ThanhToanDonHangGlowUp" alt="QR Code" class="checkout-qr__img">
                
                <button id="btn-confirm-pay" class="checkout-qr__btn">
                    <span id="btn-text">Tôi đã chuyển khoản</span>
                    <svg id="loading-icon" class="checkout-spinner hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>

    </div>

    {{-- Success Popup --}}
    <div id="success-popup" class="checkout-success-overlay hidden">
        <div class="checkout-success-popup" id="popup-content">
            <div class="checkout-success-popup__icon-wrap">
                <svg class="checkout-success-popup__icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="checkout-success-popup__title">Thành công!</h3>
            <p class="checkout-success-popup__desc">Cảm ơn bạn. Đơn hàng của bạn đã được thanh toán và đang được xử lý.</p>
            <a href="/user/profileuser?tab=orders" class="btn-dark" style="width: 100%; text-align: center; text-transform: uppercase; letter-spacing: 0.05em;">
                Xem lịch sử đơn hàng
            </a>
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

        // Khai báo biến lưu trạng thái Tiền & Voucher
        let baseTotal = 0;          // Tổng tiền hàng gốc
        let currentDiscount = 0;    // Số tiền được giảm
        let appliedVoucherId = null;// ID của voucher đã áp dụng

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
                    result.data.forEach(item => {
                        // Chỉ tính những sản phẩm được check ở giỏ hàng (hoặc tính hết nếu URL không có items)
                        if (selectedItems.length === 0 || selectedItems.includes(item.ma_san_pham)) {
                            const gia = item.san_pham.gia_khuyen_mai || item.san_pham.gia;
                            baseTotal += gia * item.so_luong;
                            hasValidItems = true;
                        }
                    });
                    
                    if (!hasValidItems) {
                        alert('Không có sản phẩm nào được chọn hợp lệ!');
                        window.location.href = '/user/cart';
                        return;
                    }
                    
                    // Cập nhật giao diện
                    subTotalEl.textContent = baseTotal.toLocaleString() + ' VNĐ';
                    updateFinalPrice();
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
            let final = baseTotal - currentDiscount;
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
                                   `Giảm ${v.gia_tri.toLocaleString()}đ` : 
                                   `Giảm ${v.gia_tri}% (Tối đa ${(v.giam_toi_da||0).toLocaleString()}đ)`;
                        
                        let minOrder = `Đơn tối thiểu: ${v.don_toi_thieu.toLocaleString()}đ`;
                        let isEligible = baseTotal >= v.don_toi_thieu;
                        
                        let btnClass = isEligible ? 'bg-pink-500 hover:bg-pink-600 text-white shadow-sm' : 'bg-gray-100 text-gray-400 cursor-not-allowed';
                        let btnAction = isEligible ? `onclick="window.selectVoucher('${v.ma_code}')"` : 'disabled';
                        let borderClass = isEligible ? 'border-pink-200' : 'border-gray-200 opacity-70';
                        
                        html += `
                        <div class="border ${borderClass} rounded-2xl p-4 flex flex-col gap-3 bg-white hover:shadow-md transition relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 w-12 h-12 bg-pink-50 rounded-full group-hover:scale-150 transition duration-500 z-0"></div>
                            <div class="relative z-10 flex justify-between items-center">
                                <div>
                                    <div class="font-black text-pink-600 text-xl tracking-tight leading-none mb-1">${v.ma_code}</div>
                                    <div class="text-gray-800 font-bold text-sm">${desc}</div>
                                    <div class="text-gray-500 text-xs font-medium mt-1">${minOrder}</div>
                                </div>
                                <button ${btnAction} class="px-5 py-2 rounded-full font-bold text-sm transition ${btnClass}">
                                    ${isEligible ? 'Dùng ngay' : 'Chưa đủ đ/k'}
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
        const qrImg = document.querySelector('.checkout-qr__img');
        const qrText = document.querySelector('.checkout-qr__text');
        
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.checkout-payment-option__label').forEach(lbl => lbl.classList.remove('checkout-payment-option__label--active'));
                this.previousElementSibling.classList.add('checkout-payment-option__label--active');
                
                if (this.value === 'cod') {
                    qrImg.classList.add('hidden');
                    qrText.classList.add('hidden');
                    btnText.textContent = 'Xác nhận đặt hàng';
                } else {
                    qrImg.classList.remove('hidden');
                    qrText.classList.remove('hidden');
                    btnText.textContent = 'Tôi đã chuyển khoản';
                }
            });
        });

        // ==========================================
        // 5. KIỂM TRA BẮT BUỘC & THANH TOÁN
        // ==========================================
        btnPay.addEventListener('click', async function() {
            const provinceOption = provinceSelect.options[provinceSelect.selectedIndex];
            const wardOption = wardSelect.options[wardSelect.selectedIndex];
            
            const provinceName = (provinceOption && provinceOption.value !== "") ? provinceOption.getAttribute('data-name') : null;
            const wardName = (wardOption && wardOption.value !== "") ? wardOption.getAttribute('data-name') : null;
            const street = streetInput.value.trim();

            if (!provinceName) { alert('🛑 Vui lòng chọn Tỉnh / Thành phố!'); provinceSelect.focus(); return; }
            if (!wardName) { alert('🛑 Vui lòng chọn Phường / Xã!'); wardSelect.focus(); return; }
            if (!street || street.length < 5) { alert('🛑 Vui lòng nhập chi tiết Số nhà, tên đường!'); streetInput.focus(); return; }

            const fullAddress = `${street}, ${wardName}, ${provinceName}`;

            btnPay.disabled = true;
            btnPay.classList.add('checkout-qr__btn--disabled');
            btnText.textContent = 'Đang xử lý...';
            loadingIcon.classList.remove('hidden');

            try {
                const response = await fetch('http://localhost:3000/api/order/create', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token 
                    },
                    // GỬI KÈM CẢ ID VOUCHER VÀ SỐ TIỀN GIẢM LÊN SERVER VÀ DANH SÁCH SẢN PHẨM ĐƯỢC CHỌN
                    body: JSON.stringify({ 
                        dia_chi: fullAddress,
                        ma_khuyen_mai: appliedVoucherId,
                        so_tien_giam: currentDiscount,
                        selected_items: selectedItems,
                        phuong_thuc_thanh_toan: document.querySelector('input[name="payment"]:checked').value
                    })
                });

                const result = await response.json();

                if (result.success) {
                    successPopup.classList.remove('hidden');
                    setTimeout(() => {
                        successPopup.classList.add('checkout-success-overlay--visible');
                        popupContent.classList.add('checkout-success-popup--visible');
                    }, 10);
                } else {
                    alert('Lỗi đặt hàng: ' + result.message);
                    resetButton();
                }
            } catch (error) {
                alert('Không thể kết nối đến server Node.js!');
                resetButton();
            }
        });

        function resetButton() {
            btnPay.disabled = false;
            btnPay.classList.remove('checkout-qr__btn--disabled');
            const selectedPayment = document.querySelector('input[name="payment"]:checked').value;
            btnText.textContent = selectedPayment === 'cod' ? 'Xác nhận đặt hàng' : 'Tôi đã chuyển khoản';
            loadingIcon.classList.add('hidden');
        }
    });
</script>
@endsection