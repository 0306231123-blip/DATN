@extends('layouts.user')
@section('title', 'Checkout')
@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen relative">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12">
        
        <div class="flex flex-col space-y-6">
            <div class="bg-white p-4 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full">Tổng thanh toán (Total Amount)</div>
            <div id="total-price" class="bg-white h-14 rounded-xl shadow-sm w-full flex items-center justify-center font-black text-pink-600 text-2xl">
                Đang tính toán...
            </div>
            
            <div class="bg-white p-6 rounded-xl shadow-sm w-full mt-4 border-2 border-transparent focus-within:border-pink-200 transition-colors">
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
        
        const totalPriceEl = document.getElementById('total-price');
        const token = localStorage.getItem('token');

        // Các biến của form địa chỉ (Bỏ Quận/Huyện)
        const provinceSelect = document.getElementById('province');
        const wardSelect = document.getElementById('ward');
        const streetInput = document.getElementById('street');

        // ==========================================
        // 1. TẢI DỮ LIỆU TỈNH THÀNH (API MỚI - 34 TỈNH)
        // ==========================================
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
            })
            .catch(err => console.error('Lỗi tải API Tỉnh:', err));

        // Khi chọn Tỉnh -> Tải Phường Xã (API trả thẳng về Phường/Xã bằng depth=2)
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
                })
                .catch(err => console.error('Lỗi tải API Phường/Xã:', err));
        });

        // ==========================================
        // 2. TÍNH TIỀN TỪ GIỎ HÀNG
        // ==========================================
        try {
            const response = await fetch('http://localhost:3000/api/cart', {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                let total = 0;
                result.data.forEach(item => {
                    const gia = item.san_pham.gia_khuyen_mai || item.san_pham.gia;
                    total += gia * item.so_luong;
                });
                totalPriceEl.textContent = total.toLocaleString() + ' VNĐ';
            } else {
                totalPriceEl.textContent = '0 VNĐ';
                alert('Giỏ hàng của bạn đang trống!');
                window.location.href = '/user/cart';
            }
        } catch (err) {
            console.error('Lỗi tính tiền:', err);
            totalPriceEl.textContent = 'Lỗi tải dữ liệu';
        }

        // ==========================================
        // 3. KIỂM TRA BẮT BUỘC & THANH TOÁN
        // ==========================================
        btnPay.addEventListener('click', async function() {
            // Lấy Tên Tỉnh/Phường
            const provinceOption = provinceSelect.options[provinceSelect.selectedIndex];
            const wardOption = wardSelect.options[wardSelect.selectedIndex];
            
            const provinceName = (provinceOption && provinceOption.value !== "") ? provinceOption.getAttribute('data-name') : null;
            const wardName = (wardOption && wardOption.value !== "") ? wardOption.getAttribute('data-name') : null;
            const street = streetInput.value.trim();

            // Reset viền đỏ trước khi kiểm tra lại
            [provinceSelect, wardSelect, streetInput].forEach(el => {
                el.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
            });

            // KIỂM TRA ĐIỀU KIỆN BẮT BUỘC
            if (!provinceName) {
                alert('🛑 BẮT BUỘC: Vui lòng chọn Tỉnh / Thành phố!');
                provinceSelect.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                provinceSelect.focus();
                return;
            }
            if (!wardName) {
                alert('🛑 BẮT BUỘC: Vui lòng chọn Phường / Xã!');
                wardSelect.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                wardSelect.focus();
                return;
            }
            
            // CHỐT CHẶN KIỂM TRA SỐ NHÀ SIÊU CẤP
            if (!street) {
                alert('🛑 BẮT BUỘC: Vui lòng nhập Số nhà, tên đường!');
                streetInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                streetInput.focus();
                return;
            }

            if (street.length < 5) {
                alert('🛑 ĐỊA CHỈ QUÁ NGẮN: Vui lòng nhập rõ ràng và chi tiết hơn (ít nhất 5 ký tự)!');
                streetInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                streetInput.focus();
                return;
            }

            const isSpam = /(.)\1{2,}/.test(street);
            if (isSpam) {
                alert('🛑 ĐỊA CHỈ KHÔNG HỢP LỆ: Vui lòng nhập địa chỉ có ý nghĩa, không nhập ký tự linh tinh!');
                streetInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                streetInput.focus();
                return;
            }

            const hasLetters = /[a-zA-ZÀ-ỹ]/.test(street);
            if (!hasLetters) {
                alert('🛑 ĐỊA CHỈ THIẾU: Vui lòng nhập thêm tên đường, tổ, hoặc thôn/xóm (không được chỉ nhập mỗi số)!');
                streetInput.classList.add('border-red-500', 'ring-1', 'ring-red-500');
                streetInput.focus();
                return;
            }

            // Gom địa chỉ chuẩn 2 cấp + chi tiết
            const fullAddress = `${street}, ${wardName}, ${provinceName}`;

            // Khóa nút bấm để tránh spam click
            btnPay.disabled = true;
            btnPay.classList.replace('bg-pink-500', 'bg-gray-400');
            btnPay.classList.replace('hover:bg-pink-600', 'cursor-not-allowed');
            btnText.textContent = 'Đang xử lý...';
            loadingIcon.classList.remove('hidden');

            try {
                // Gọi API tạo đơn hàng
                const response = await fetch('http://localhost:3000/api/order/create', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token 
                    },
                    body: JSON.stringify({ dia_chi: fullAddress })
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
                console.error("Lỗi server:", error);
                alert('Không thể kết nối đến server Node.js!');
                resetButton();
            }
        });

        // Khôi phục nút nếu lỗi
        function resetButton() {
            btnPay.disabled = false;
            btnPay.classList.replace('bg-gray-400', 'bg-pink-500');
            btnPay.classList.replace('cursor-not-allowed', 'hover:bg-pink-600');
            btnText.textContent = 'Tôi đã chuyển khoản';
            loadingIcon.classList.add('hidden');
        }
    });
</script>
@endsection