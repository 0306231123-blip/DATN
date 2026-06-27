@extends('layouts.user')
@section('title', 'Checkout')
@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen relative">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12">
        
        <div class="flex flex-col space-y-6">
            <div class="bg-white p-6 rounded-xl shadow-sm w-full border border-gray-100">
                <div class="text-center font-bold text-gray-700 w-full mb-4 uppercase tracking-wider">Tổng thanh toán</div>
                
                <div class="space-y-3 mb-6 text-gray-600 font-medium">
                    <div class="flex justify-between items-center">
                        <span>Tạm tính:</span>
                        <span id="sub-total-price">Đang tính...</span>
                    </div>
                    <div id="discount-row" class="flex justify-between items-center text-green-600 hidden">
                        <span>Mã giảm giá (<span id="applied-voucher-name"></span>):</span>
                        <span id="discount-amount">-0 VNĐ</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                        <span class="font-black text-gray-800 text-lg">Cần thanh toán:</span>
                        <span id="final-price" class="font-black text-pink-600 text-3xl">Đang tính...</span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <label class="block text-gray-800 font-black mb-3">Mã khuyến mãi (Voucher)</label>
                    <div class="flex space-x-2">
                        <input type="text" id="voucher-code" class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 font-bold text-gray-700 uppercase focus:outline-none focus:border-pink-500 transition" placeholder="Nhập mã giảm giá...">
                        <button id="btn-apply-voucher" class="bg-gray-800 hover:bg-black text-white font-bold px-6 py-3 rounded-lg transition whitespace-nowrap">
                            Áp dụng
                        </button>
                    </div>
                    <p id="voucher-msg" class="text-sm mt-3 font-medium hidden"></p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm w-full border-2 border-transparent focus-within:border-pink-200 transition-colors">
                <label class="block text-gray-800 font-black mb-4 text-lg">
                    Địa chỉ giao hàng <span class="text-red-500">*</span>
                    <span class="block text-sm font-normal text-gray-500 mt-1">Bắt buộc nhập đầy đủ thông tin bên dưới</span>
                </label>
                
                <div class="space-y-4">
                    <div>
                        <select id="province" required class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 font-medium text-gray-700 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition cursor-pointer">
                            <option value="" disabled selected>1. Chọn Tỉnh / Thành phố (Chuẩn mới)</option>
                        </select>
                    </div>
                    <div>
                        <select id="ward" required class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 font-medium text-gray-700 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition cursor-pointer">
                            <option value="" disabled selected>2. Chọn Phường / Xã</option>
                        </select>
                    </div>
                    <div>
                        <input type="text" id="street" required placeholder="3. Nhập số nhà, tên đường..." class="w-full border-2 border-gray-200 rounded-lg px-4 py-3 font-medium text-gray-700 focus:outline-none focus:border-pink-500 focus:ring-1 focus:ring-pink-500 transition">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center space-y-6">
            <div class="bg-white p-4 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full">Phương thức thanh toán (Payment Method)</div>
            
            <div class="flex space-x-8 w-full justify-center">
                <label class="flex flex-col items-center space-y-2 cursor-pointer">
                    <span class="bg-white px-6 py-2 rounded-lg shadow-sm font-bold text-gray-600 border-2 border-pink-100 hover:border-pink-300 transition">Banking</span>
                    <input type="radio" name="payment" value="banking" class="w-5 h-5 accent-pink-500" checked>
                </label>
                <label class="flex flex-col items-center space-y-2 cursor-pointer">
                    <span class="bg-white px-6 py-2 rounded-lg shadow-sm font-bold text-gray-600 border-2 border-gray-100 hover:border-pink-300 transition">Momo</span>
                    <input type="radio" name="payment" value="momo" class="w-5 h-5 accent-pink-500">
                </label>
            </div>

            <div class="bg-white w-full rounded-2xl flex flex-col items-center justify-center shadow-sm mt-4 p-8 border border-gray-100">
                <span class="text-gray-500 font-bold text-xl mb-4">Quét mã QR để thanh toán</span>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=ThanhToanDonHangGlowUp" alt="QR Code" class="w-48 h-48 border-4 border-gray-100 rounded-xl mb-6 shadow-sm">
                
                <button id="btn-confirm-pay" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-4 px-8 rounded-xl shadow-md transition uppercase tracking-wider w-full flex justify-center items-center text-lg">
                    <span id="btn-text">Tôi đã chuyển khoản</span>
                    <svg id="loading-icon" class="animate-spin ml-2 h-6 w-6 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>

    </div>

    <div id="success-popup" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 hidden flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="bg-white p-8 rounded-3xl shadow-2xl flex flex-col items-center max-w-sm w-full transform scale-95 transition-transform duration-300" id="popup-content">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-800 mb-2">Thành công!</h3>
            <p class="text-gray-500 text-center mb-8 font-medium">Cảm ơn bạn. Đơn hàng của bạn đã được thanh toán và đang được xử lý.</p>
            <a href="/user/profileuser" class="bg-gray-800 hover:bg-black text-white font-bold py-3 px-8 rounded-xl shadow-md transition w-full text-center uppercase tracking-wider">
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
            voucherMsg.classList.remove('hidden', 'text-red-500', 'text-green-600');
            if (type === 'error') voucherMsg.classList.add('text-red-500');
            else voucherMsg.classList.add('text-green-600');
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

            [provinceSelect, wardSelect, streetInput].forEach(el => el.classList.remove('border-red-500', 'ring-1', 'ring-red-500'));

            if (!provinceName) { alert('🛑 Vui lòng chọn Tỉnh / Thành phố!'); provinceSelect.focus(); return; }
            if (!wardName) { alert('🛑 Vui lòng chọn Phường / Xã!'); wardSelect.focus(); return; }
            if (!street || street.length < 5) { alert('🛑 Vui lòng nhập chi tiết Số nhà, tên đường!'); streetInput.focus(); return; }

            const fullAddress = `${street}, ${wardName}, ${provinceName}`;

            btnPay.disabled = true;
            btnPay.classList.replace('bg-pink-500', 'bg-gray-400');
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
                        successPopup.classList.remove('opacity-0');
                        successPopup.classList.add('opacity-100');
                        popupContent.classList.remove('scale-95');
                        popupContent.classList.add('scale-100');
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
            btnPay.classList.replace('bg-gray-400', 'bg-pink-500');
            btnText.textContent = 'Tôi đã chuyển khoản';
            loadingIcon.classList.add('hidden');
        }
    });
</script>
@endsection