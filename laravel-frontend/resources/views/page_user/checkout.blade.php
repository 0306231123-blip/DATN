@extends('layouts.user')
@section('title', 'Checkout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-checkout.css') }}">
@endsection

@section('content')
<section class="user-section" style="position: relative;">
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
                    <label class="checkout-voucher__label">Mã khuyến mãi (Voucher)</label>
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
            <a href="/user/profileuser" class="btn-dark" style="width: 100%; text-align: center; text-transform: uppercase; letter-spacing: 0.05em;">
                Xem lịch sử đơn hàng
            </a>
        </div>
    </div>
</section>

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
                    result.data.forEach(item => {
                        const gia = item.san_pham.gia_khuyen_mai || item.san_pham.gia;
                        baseTotal += gia * item.so_luong;
                    });
                    
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
        // 4. KIỂM TRA BẮT BUỘC & THANH TOÁN
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
                    // GỬI KÈM CẢ ID VOUCHER VÀ SỐ TIỀN GIẢM LÊN SERVER
                    body: JSON.stringify({ 
                        dia_chi: fullAddress,
                        ma_khuyen_mai: appliedVoucherId,
                        so_tien_giam: currentDiscount
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
            btnText.textContent = 'Tôi đã chuyển khoản';
            loadingIcon.classList.add('hidden');
        }
    });
</script>
@endsection