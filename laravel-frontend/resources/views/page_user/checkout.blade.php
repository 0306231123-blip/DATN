@extends('layouts.user')
@section('title', 'Checkout')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-checkout.css') }}?v={{ time() }}">
@endsection

@section('content')
<section class="user-section" style="position: relative; padding-top: 0.5rem;">
    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-0 pb-8">
    <div class="mb-4 max-w-[64rem] mx-auto">
        <a href="/user/cart" class="inline-flex items-center text-gray-500 hover:text-pink-600 font-bold transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại Giỏ hàng
        </a>
    </div>

    
    <div class="flex flex-col gap-4 max-w-6xl mx-auto">
        
        <!-- Address Section -->
        <div class="bg-white p-6 shadow-[0_1px_1px_0_rgba(0,0,0,0.05)] border-t-[3px] border-t-pink-500">
            <h2 class="text-pink-600 text-lg flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Địa Chỉ Nhận Hàng
                </div>
                <button id="btn-open-address-list" class="text-sm font-medium text-blue-500 hover:text-pink-500 underline transition">Chọn thông tin giao hàng</button>
            </h2>
            <div class="flex flex-col md:flex-row gap-4 mb-4">
                <div class="w-full md:w-1/3">
                    <input type="text" id="ho_ten" placeholder="Họ và tên..." class="w-full border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm">
                </div>
                <div class="w-full md:w-1/3">
                    <input type="text" id="so_dien_thoai" placeholder="Số điện thoại..." class="w-full border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm">
                </div>
            </div>
            <div class="flex flex-col md:flex-row gap-4">
                <div class="w-full md:w-1/3">
                    <select id="province" required class="w-full border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm">
                        <option value="" disabled selected>Chọn Tỉnh / Thành phố</option>
                    </select>
                </div>
                <div class="w-full md:w-1/3">
                    <select id="ward" required class="w-full border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm">
                        <option value="" disabled selected>Chọn Phường / Xã</option>
                    </select>
                </div>
                <div class="w-full md:w-1/3">
                    <input type="text" id="street" required placeholder="Số nhà, đường..." class="w-full border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm">
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="bg-white shadow-[0_1px_1px_0_rgba(0,0,0,0.05)] mt-2">
            <div class="p-6 hidden md:flex items-center text-gray-500 text-sm">
                <div class="flex-1 text-gray-800 font-medium text-base">Sản Phẩm</div>
                <div class="w-32 text-center">Đơn Giá</div>
                <div class="w-24 text-center">Số Lượng</div>
                <div class="w-32 text-center">Thành Tiền</div>
            </div>
            <div id="checkout-products-list" class="flex flex-col">
                <p class="text-gray-500 text-sm italic py-4 px-6">Đang tải...</p>
            </div>
        </div>

        <!-- Voucher Section -->
        <div class="bg-white shadow-[0_1px_1px_0_rgba(0,0,0,0.05)] mt-2 p-6 flex flex-col md:flex-row justify-between md:items-center border-b border-gray-50">
            <div class="flex items-center gap-2 mb-4 md:mb-0">
                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                <span class="text-gray-800 font-medium">Voucher (Mã giảm giá)</span>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-2">
                <input type="text" id="voucher-code" class="border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm w-full sm:w-48" placeholder="Nhập mã...">
                <div class="flex gap-2 w-full sm:w-auto">
                    <button id="btn-apply-voucher" class="bg-gray-800 text-white px-4 py-2 text-sm rounded-sm hover:bg-gray-700 w-full sm:w-auto whitespace-nowrap">Áp dụng</button>
                    <button id="btn-show-vouchers" class="text-pink-500 font-medium px-4 py-2 hover:bg-pink-50 rounded-sm transition whitespace-nowrap">Chọn Voucher</button>
                </div>
                <p id="voucher-msg" class="hidden text-sm ml-2"></p>
            </div>
        </div>

        <!-- Payment Methods & Summary Block -->
        <div class="bg-white shadow-[0_1px_1px_0_rgba(0,0,0,0.05)] mt-2">
            <!-- Payment Methods -->
            <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center gap-4">
                <h3 class="w-48 text-gray-800 font-medium shrink-0">Phương thức thanh toán</h3>
                <div class="flex flex-wrap gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="payment" value="banking" class="peer hidden" checked>
                        <div class="border border-gray-200 px-4 py-2 text-sm text-gray-800 peer-checked:border-pink-500 peer-checked:text-pink-500 hover:border-pink-500 transition relative bg-white">
                            Banking
                            <div class="hidden peer-checked:block absolute bottom-0 right-0 w-4 h-4 overflow-hidden">
                                <div class="w-8 h-8 bg-pink-500 transform rotate-45 translate-x-4 translate-y-4"></div>
                                <svg class="absolute bottom-0 right-0 text-white w-2.5 h-2.5 mb-0.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            </div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="payment" value="momo" class="peer hidden">
                        <div class="border border-gray-200 px-4 py-2 text-sm text-gray-800 peer-checked:border-pink-500 peer-checked:text-pink-500 hover:border-pink-500 transition relative bg-white">
                            Momo
                            <div class="hidden peer-checked:block absolute bottom-0 right-0 w-4 h-4 overflow-hidden">
                                <div class="w-8 h-8 bg-pink-500 transform rotate-45 translate-x-4 translate-y-4"></div>
                                <svg class="absolute bottom-0 right-0 text-white w-2.5 h-2.5 mb-0.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            </div>
                        </div>
                    </label>
                    <label class="cursor-pointer" id="cod-wrapper">
                        <input type="radio" name="payment" value="cod" id="cod-radio" class="peer hidden">
                        <div id="cod-box" class="border border-gray-200 px-4 py-2 text-sm text-gray-800 peer-checked:border-pink-500 peer-checked:text-pink-500 hover:border-pink-500 transition relative bg-white flex items-center gap-1 group">
                            Thanh toán khi nhận hàng
                            <span id="cod-warning" class="hidden text-white bg-red-500 rounded-full w-4 h-4 flex items-center justify-center text-[10px] font-bold cursor-help" title="Đơn hàng có giá trị trên 5.000.000đ nên phải thanh toán bằng Banking hoặc Momo">!</span>
                            <div class="hidden peer-checked:block absolute bottom-0 right-0 w-4 h-4 overflow-hidden">
                                <div class="w-8 h-8 bg-pink-500 transform rotate-45 translate-x-4 translate-y-4"></div>
                                <svg class="absolute bottom-0 right-0 text-white w-2.5 h-2.5 mb-0.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Summary Table -->
            <div class="p-6 bg-orange-50/20 flex flex-col items-end">
                <div class="w-full sm:w-96">
                    <div class="flex justify-between items-center py-2 text-sm">
                        <span class="text-gray-500">Tổng tiền hàng</span>
                        <span id="sub-total-price" class="text-gray-800">Đang tính...</span>
                    </div>
                    <div class="flex justify-between items-center py-2 text-sm">
                        <span class="text-gray-500">Phí vận chuyển</span>
                        <span id="shipping-fee" class="text-gray-800">Chưa tính</span>
                    </div>
                    <div class="flex justify-between items-center py-2 text-sm" id="discount-row">
                        <span class="text-gray-500">Mã giảm giá <span id="applied-voucher-name" class="text-pink-500 font-medium"></span></span>
                        <span id="discount-amount" class="text-gray-800">-0 đ</span>
                    </div>
                    <div class="flex justify-between items-center py-4 mt-2">
                        <span class="text-gray-500">Tổng thanh toán:</span>
                        <span id="final-price" class="text-pink-500 text-3xl font-medium">Đang tính...</span>
                    </div>
                </div>
            </div>

            <div class="border-t border-dashed border-gray-200"></div>

            <!-- Place Order Row -->
            <div class="p-6 bg-orange-50/20 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-500">
                    Nhấn "Đặt hàng" đồng nghĩa với việc bạn đồng ý tuân theo <a href="#" class="text-blue-600 hover:underline">Điều khoản của Cosmetics</a>.
                </div>
                <button id="btn-confirm-pay" class="bg-pink-500 hover:bg-pink-600 text-white font-medium py-3 px-10 text-sm transition rounded-sm flex items-center justify-center min-w-[200px] shadow-sm">
                    <span id="btn-text">Đặt hàng</span>
                    <svg id="loading-icon" class="checkout-spinner hidden ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="width: 20px; height: 20px;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="checkout-qr hidden">
            <img src="" class="checkout-qr__img">
            <span class="checkout-qr__text"></span>
        </div>

    </div>


    {{-- Success Popup --}}
    <div id="success-popup" class="checkout-success-overlay hidden">
        <div class="checkout-success-popup relative w-full" style="max-width: 800px; padding: 2rem;" id="popup-content">
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
                    <a href="/user/profileuser?tab=orders" id="btn-view-history" class="bg-gray-800 hover:bg-gray-700 text-white font-medium py-3 rounded-sm mt-6 transition w-full text-center uppercase text-sm block">
                        Xem lịch sử đơn hàng
                    </a>
                </div>
            </div>

            <div id="bill-section" class="hidden w-full">
                <h3 class="text-xl font-bold text-center text-gray-800 mb-6 uppercase tracking-wide">Hóa Đơn Của Bạn</h3>
                <div class="text-left text-sm text-gray-700 space-y-3 mb-6 bg-white p-6 border border-gray-100 shadow-sm">
                    <p class="flex justify-between items-center"><strong class="text-gray-800 font-medium">Khách hàng:</strong> <span id="bill-name" class="font-medium"></span></p>
                    <p class="flex justify-between items-center"><strong class="text-gray-800 font-medium">SĐT:</strong> <span id="bill-phone" class="font-medium"></span></p>
                    <p class="flex justify-between items-start"><strong class="text-gray-800 font-medium shrink-0">Địa chỉ:</strong> <span id="bill-address" class="font-medium text-right ml-4 line-clamp-2 w-2/3"></span></p>
                    
                    <div class="mt-4 border-t border-gray-100 pt-4">
                        <strong class="block mb-3 text-gray-800 font-medium">Sản phẩm:</strong>
                        <div id="bill-products" class="max-h-60 overflow-y-auto custom-scrollbar pr-2 space-y-3"></div>
                    </div>
                    
                    <div class="mt-4 border-t border-gray-100 pt-4 flex justify-between items-center text-base text-gray-800">
                        <span class="font-medium">Tổng tiền:</span>
                        <span id="bill-total" class="text-pink-600 font-bold text-xl"></span>
                    </div>
                </div>
                <button id="btn-proceed-pay" class="w-full bg-pink-500 hover:bg-pink-600 text-white font-medium py-3 rounded-sm transition flex items-center justify-center uppercase text-sm">
                    Thanh toán
                </button>
            </div>
            
            <div id="qr-payment-section" class="hidden flex-col items-center">
                <h3 class="checkout-success-popup__title" id="qr-title">Quét mã để thanh toán!</h3>
                <p id="qr-instruction" class="text-gray-800 font-bold mb-3 text-center">Vui lòng quét mã QR để thanh toán</p>
                <img id="popup-qr-img" src="" class="w-64 h-auto rounded-sm border border-gray-200 mb-2">
                <p class="text-xs text-red-500 font-medium text-center px-4 mb-2" id="qr-countdown-text">Đơn hàng sẽ tự động hủy sau <span id="qr-countdown" class="font-bold text-lg">03:00</span> phút nếu không nhận được thanh toán.</p>
                <p class="checkout-success-popup__desc text-gray-600">Sau khi quét mã thanh toán thành công, hệ thống sẽ tự động tạo đơn hàng cho bạn.</p>
                <button id="btn-done-qr" class="hidden">Đã thanh toán (Test)</button>
            </div>
        </div>
    </div>

    {{-- Voucher List Modal --}}
    <div id="voucher-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 transition-opacity duration-300">
        <div class="bg-white rounded-sm w-full max-w-md p-6 shadow-xl transform transition-transform scale-100">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-800 uppercase tracking-wide">Chọn Voucher</h3>
                <button id="close-voucher-modal" class="text-gray-400 hover:text-pink-500 bg-gray-50 hover:bg-pink-50 p-2 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div id="voucher-list-container" class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                <p class="text-center text-gray-500 py-4 font-medium">Đang tải...</p>
            </div>
        </div>
    </div>

    {{-- Address List Modal --}}
    <div id="address-modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 transition-opacity duration-300">
        <div class="bg-white rounded-sm w-full max-w-lg p-6 shadow-xl transform transition-transform scale-100 flex flex-col max-h-[90vh]">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 uppercase tracking-wide">Thông Tin Giao Hàng</h3>
                <button id="close-address-modal" class="text-gray-400 hover:text-pink-500 bg-gray-50 hover:bg-pink-50 p-2 rounded-full transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div id="address-list-container" class="space-y-3 overflow-y-auto flex-1 custom-scrollbar pr-2 mb-4">
                <!-- Address list generated by JS -->
            </div>

            <div class="border-t border-gray-100 pt-4 mt-auto" id="address-add-btn-wrap">
                <button id="btn-add-new-address" class="w-full border-2 border-dashed border-pink-400 text-pink-600 hover:bg-pink-50 font-medium py-3 rounded-sm flex justify-center items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Thêm thông tin giao hàng mới
                </button>
            </div>

            <div id="new-address-form" class="hidden flex-col gap-3 border-t border-gray-100 pt-4 mt-auto">
                <h4 class="font-bold text-gray-700 text-sm">Thêm địa chỉ mới</h4>
                <div class="flex gap-2">
                    <input type="text" id="new_ho_ten" placeholder="Họ và tên..." class="w-1/2 border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm">
                    <input type="text" id="new_so_dien_thoai" placeholder="Số điện thoại..." class="w-1/2 border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm">
                </div>
                <div class="flex gap-2">
                    <select id="new_province" class="w-1/2 border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm">
                        <option value="" disabled selected>Chọn Tỉnh/Thành</option>
                    </select>
                    <select id="new_ward" class="w-1/2 border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm">
                        <option value="" disabled selected>Chọn Phường/Xã</option>
                    </select>
                </div>
                <input type="text" id="new_street" placeholder="Số nhà, đường..." class="border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-pink-500 rounded-sm">
                <div class="flex gap-2 mt-2">
                    <button id="btn-save-new-address" class="flex-1 bg-pink-500 text-white py-2.5 rounded-sm hover:bg-pink-600 font-medium text-sm transition">Lưu địa chỉ</button>
                    <button id="btn-cancel-new-address" class="flex-1 bg-gray-100 text-gray-700 py-2.5 rounded-sm hover:bg-gray-200 font-medium text-sm transition">Hủy</button>
                </div>
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
                    // Populate initial form if empty
                    if (!hoTenInput.value) hoTenInput.value = result.data.ho_ten || '';
                    if (!soDienThoaiInput.value) soDienThoaiInput.value = result.data.so_dien_thoai || '';
                    
                    if (result.data.dia_chi) {
                        const parts = result.data.dia_chi.split(', ');
                        if (parts.length >= 3) {
                            const pName = parts[parts.length - 1];
                            const wName = parts[parts.length - 2];
                            const sName = parts.slice(0, parts.length - 2).join(', ');
                            
                            // Add to address list as default
                            const defaultAddr = {
                                id: 'default',
                                name: result.data.ho_ten || 'Khách hàng',
                                phone: result.data.so_dien_thoai || '',
                                provinceId: '', 
                                provinceName: pName,
                                wardId: '',
                                wardName: wName,
                                street: sName,
                                isDefault: true
                            };

                            // Check if default address is already in savedAddresses to prevent duplication
                            const existingDefaultIndex = savedAddresses.findIndex(a => a.id === 'default');
                            if (existingDefaultIndex === -1) {
                                savedAddresses.unshift(defaultAddr); // Add to beginning
                                saveAddressesToStorage(); // Optional: you can save it or just keep it in memory
                            } else {
                                // Update it in case profile changed
                                savedAddresses[existingDefaultIndex] = defaultAddr;
                            }

                            // If this is the first load and no province selected yet, auto select
                            if (!provinceSelect.value) {
                            
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
                            } // Close if (!provinceSelect.value)
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

                    // Clone for new address modal
                    let cloneOpt = option.cloneNode(true);
                    document.getElementById('new_province').appendChild(cloneOpt);
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
            document.getElementById('shipping-fee').textContent = currentShippingFee > 0 ? '+ ' + currentShippingFee.toLocaleString() + ' đ' : 'Miễn phí';
            updateFinalPrice();

            wardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường / Xã</option>';
            if(provinceId) {
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
            }
        });

        // New Address Modal Ward fetch
        document.getElementById('new_province').addEventListener('change', function() {
            const provinceId = this.value;
            const nWardSelect = document.getElementById('new_ward');
            nWardSelect.innerHTML = '<option value="" disabled selected>Chọn Phường / Xã</option>';
            if(provinceId) {
                fetch(`https://provinces.open-api.vn/api/v2/p/${provinceId}?depth=2`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.wards) {
                            data.wards.forEach(ward => {
                                let option = document.createElement('option');
                                option.value = ward.code;
                                option.text = ward.name;
                                option.setAttribute('data-name', ward.name);
                                nWardSelect.appendChild(option);
                            });
                        }
                    });
            }
        });

        // ==========================================
        // 1.5 XỬ LÝ SỔ ĐỊA CHỈ (LOCALSTORAGE)
        // ==========================================
        const addressModal = document.getElementById('address-modal');
        const btnOpenAddressList = document.getElementById('btn-open-address-list');
        const closeAddressModal = document.getElementById('close-address-modal');
        const addressListContainer = document.getElementById('address-list-container');
        const btnAddNewAddress = document.getElementById('btn-add-new-address');
        const newAddressForm = document.getElementById('new-address-form');
        const btnSaveNewAddress = document.getElementById('btn-save-new-address');
        const btnCancelNewAddress = document.getElementById('btn-cancel-new-address');
        const addressAddBtnWrap = document.getElementById('address-add-btn-wrap');
        
        let savedAddresses = [];
        let userId = 'guest';

        function loadAddressesFromStorage() {
            if (token) {
                try {
                    const payload = JSON.parse(atob(token.split('.')[1]));
                    userId = payload.ma_nguoi_dung || payload.id || 'guest';
                } catch(e) {}
            }
            const data = localStorage.getItem('addresses_' + userId);
            if (data) {
                savedAddresses = JSON.parse(data);
            }
        }

        function saveAddressesToStorage() {
            localStorage.setItem('addresses_' + userId, JSON.stringify(savedAddresses));
        }

        function renderAddressList() {
            if (savedAddresses.length === 0) {
                addressListContainer.innerHTML = '<p class="text-center text-gray-500 py-4 font-medium italic">Chưa có thông tin giao hàng nào được lưu.</p>';
                return;
            }

            let html = '';
            savedAddresses.forEach((addr, index) => {
                const isDefaultBadge = addr.isDefault ? '<span class="bg-pink-100 text-pink-600 text-[10px] font-bold px-2 py-0.5 rounded-sm uppercase ml-2 border border-pink-200">Mặc định</span>' : '';
                const deleteBtnHtml = addr.isDefault ? '' : `
                    <button onclick="deleteAddress(event, ${index})" class="text-gray-400 hover:text-red-500 p-2 hidden group-hover:block" title="Xóa">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                `;

                html += `
                    <div class="border border-gray-200 rounded-md p-4 hover:border-pink-500 transition cursor-pointer flex justify-between items-center group" onclick="selectAddress(${index})">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-gray-800">${addr.name}</span>
                                <span class="text-gray-400">|</span>
                                <span class="text-gray-600">${addr.phone}</span>
                                ${isDefaultBadge}
                            </div>
                            <div class="text-sm text-gray-500">${addr.street}, ${addr.wardName}, ${addr.provinceName}</div>
                        </div>
                        ${deleteBtnHtml}
                    </div>
                `;
            });
            addressListContainer.innerHTML = html;
        }

        window.selectAddress = function(index) {
            const addr = savedAddresses[index];
            if (!addr) return;

            hoTenInput.value = addr.name;
            soDienThoaiInput.value = addr.phone;
            
            // Set province
            let foundProvince = Array.from(provinceSelect.options).find(opt => opt.getAttribute('data-name') === addr.provinceName);
            if (foundProvince) {
                provinceSelect.value = foundProvince.value;
                provinceSelect.dispatchEvent(new Event('change'));
                
                // Wait for ward to load
                let checkWardExist = setInterval(function() {
                    if (wardSelect.options.length > 1) {
                        clearInterval(checkWardExist);
                        let foundWard = Array.from(wardSelect.options).find(opt => opt.getAttribute('data-name') === addr.wardName);
                        if (foundWard) {
                            wardSelect.value = foundWard.value;
                            streetInput.value = addr.street;
                        }
                    }
                }, 50);
            }

            addressModal.classList.remove('flex');
            addressModal.classList.add('hidden');
        };

        window.deleteAddress = function(event, index) {
            event.stopPropagation();
            if(confirm('Bạn có chắc muốn xóa địa chỉ này?')) {
                savedAddresses.splice(index, 1);
                saveAddressesToStorage();
                renderAddressList();
            }
        };

        if(btnOpenAddressList) {
            btnOpenAddressList.addEventListener('click', () => {
                loadAddressesFromStorage();
                renderAddressList();
                newAddressForm.classList.add('hidden');
                newAddressForm.classList.remove('flex');
                addressAddBtnWrap.classList.remove('hidden');
                
                addressModal.classList.remove('hidden');
                addressModal.classList.add('flex');
            });
        }

        closeAddressModal.addEventListener('click', () => {
            addressModal.classList.remove('flex');
            addressModal.classList.add('hidden');
        });

        btnAddNewAddress.addEventListener('click', () => {
            addressAddBtnWrap.classList.add('hidden');
            newAddressForm.classList.remove('hidden');
            newAddressForm.classList.add('flex');
            // Clear inputs
            document.getElementById('new_ho_ten').value = '';
            document.getElementById('new_so_dien_thoai').value = '';
            document.getElementById('new_province').value = '';
            document.getElementById('new_ward').innerHTML = '<option value="" disabled selected>Chọn Phường/Xã</option>';
            document.getElementById('new_street').value = '';
        });

        btnCancelNewAddress.addEventListener('click', () => {
            newAddressForm.classList.add('hidden');
            newAddressForm.classList.remove('flex');
            addressAddBtnWrap.classList.remove('hidden');
        });

        btnSaveNewAddress.addEventListener('click', () => {
            const name = document.getElementById('new_ho_ten').value.trim();
            const phone = document.getElementById('new_so_dien_thoai').value.trim();
            const provOpt = document.getElementById('new_province').options[document.getElementById('new_province').selectedIndex];
            const wardOpt = document.getElementById('new_ward').options[document.getElementById('new_ward').selectedIndex];
            const street = document.getElementById('new_street').value.trim();

            if(!name || !phone || !provOpt.value || !wardOpt.value || !street) {
                alert('Vui lòng điền đầy đủ thông tin!');
                return;
            }
            if(!/^0\d{9}$/.test(phone)) {
                alert('Số điện thoại không hợp lệ!');
                return;
            }
            if(street.length < 5 || !/[a-zA-ZÀ-ỹ]/.test(street) || !/\s/.test(street.trim())) {
                alert('Vui lòng nhập đầy đủ Số nhà và Tên đường hợp lệ (ví dụ: 123 Lê Lợi)!');
                document.getElementById('new_street').focus();
                return;
            }

            const newAddr = {
                id: Date.now(),
                name: name,
                phone: phone,
                provinceId: provOpt.value,
                provinceName: provOpt.getAttribute('data-name'),
                wardId: wardOpt.value,
                wardName: wardOpt.getAttribute('data-name'),
                street: street
            };

            savedAddresses.push(newAddr);
            saveAddressesToStorage();
            
            // Auto select newly added address
            selectAddress(savedAddresses.length - 1);
        });

        // Initialize address load for auto-saving initial profile address logic
        loadAddressesFromStorage();

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
                    window.checkoutSelectedItems = [];
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
                            window.checkoutSelectedItems.push(item);

                            const mangAnh = item.san_pham.danh_sach_anh; 
                            const linkAnh = (mangAnh && mangAnh.length > 0) ? mangAnh[0].duong_dan_anh : '/images/logo.jpg';

                            productsHtml += `
                                <div class="flex flex-col md:flex-row items-center bg-white py-4 px-6 border-t border-dashed border-gray-200 hover:bg-gray-50 transition duration-200">
                                    <div class="flex-1 flex items-center mb-3 md:mb-0 w-full md:w-auto">
                                        <img src="${linkAnh}" alt="${item.san_pham.ten_san_pham}" class="w-12 h-12 object-cover border border-gray-100">
                                        <div class="ml-4 flex-1">
                                            <h4 class="text-gray-800 text-sm line-clamp-2">${item.san_pham.ten_san_pham}</h4>
                                        </div>
                                    </div>
                                    
                                    <div class="w-full md:w-32 text-center text-gray-600 text-sm mb-3 md:mb-0 hidden md:block">
                                        ${new Intl.NumberFormat('vi-VN').format(gia)} đ
                                    </div>
                                    
                                    <div class="w-full md:w-24 text-center text-gray-600 text-sm mb-3 md:mb-0 hidden md:block">
                                        ${item.so_luong}
                                    </div>
                                    
                                    <div class="w-full md:w-32 text-center text-gray-800 text-sm font-medium mb-3 md:mb-0 hidden md:block">
                                        ${new Intl.NumberFormat('vi-VN').format(gia * item.so_luong)} đ
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
                    subTotalEl.textContent = baseTotal.toLocaleString() + ' đ';
                    
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
            finalPriceEl.textContent = final.toLocaleString() + ' đ';

            // Logic giới hạn thanh toán khi nhận hàng cho đơn > 5 triệu
            const COD_LIMIT = 5000000;
            const codWrapper = document.getElementById('cod-wrapper');
            const codRadio = document.getElementById('cod-radio');
            const codBox = document.getElementById('cod-box');
            const codWarning = document.getElementById('cod-warning');

            if (codWrapper && codRadio && codBox && codWarning) {
                if (final >= COD_LIMIT) {
                    codWrapper.classList.remove('cursor-pointer');
                    codWrapper.classList.add('cursor-not-allowed');
                    codRadio.disabled = true;
                    codBox.classList.add('opacity-50', 'bg-gray-100');
                    codBox.classList.remove('hover:border-pink-500');
                    codWarning.classList.remove('hidden');

                    // Nếu đang chọn COD thì chuyển sang Banking
                    if (codRadio.checked) {
                        const bankingRadio = document.querySelector('input[name="payment"][value="banking"]');
                        if (bankingRadio) bankingRadio.checked = true;
                    }
                } else {
                    codWrapper.classList.add('cursor-pointer');
                    codWrapper.classList.remove('cursor-not-allowed');
                    codRadio.disabled = false;
                    codBox.classList.remove('opacity-50', 'bg-gray-100');
                    codBox.classList.add('hover:border-pink-500');
                    codWarning.classList.add('hidden');
                }
            }
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
                    discountAmountEl.textContent = '-' + currentDiscount.toLocaleString() + ' đ';
                    
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
                const res = await fetch('http://localhost:3000/api/voucher/active', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const result = await res.json();
                
                if (result.success && result.data.length > 0) {
                    let html = '';
                    result.data.forEach(v => {
                        let desc = v.loai_giam === 'tien_mat' ? 
                                   `Giảm ${v.gia_tri.toLocaleString()} đ` : 
                                   `Giảm ${v.gia_tri}% (Tối đa ${(v.giam_toi_da||0).toLocaleString()} đ)`;
                        
                        let outOfStock = v.so_luong <= 0;
                        let daSuDung = v.da_su_dung;
                        let percentUsed = Math.round(Math.max(5, 100 - Math.sqrt(v.so_luong) * 3));
                        if (outOfStock) percentUsed = 100;
                        let minOrder = `Đơn tối thiểu: ${v.don_toi_thieu.toLocaleString()} đ`;
                        let isEligible = baseTotal >= v.don_toi_thieu && !outOfStock && !daSuDung;
                        
                        let btnClass = isEligible ? 'bg-pink-500 hover:bg-pink-600 text-white shadow-sm' : 'bg-gray-100 text-gray-400 cursor-not-allowed';
                        let btnAction = isEligible ? `onclick="window.selectVoucher('${v.ma_code}')"` : 'disabled';
                        let borderClass = (outOfStock || daSuDung) ? 'border-gray-200 opacity-60 grayscale pointer-events-none' : (isEligible ? 'border-pink-200' : 'border-gray-200 opacity-80');
                        let btnText = daSuDung ? 'Đã dùng' : (outOfStock ? 'Hết lượt' : (isEligible ? 'Dùng ngay' : 'Chưa đủ đ/k'));
                        
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
            if (!street || street.length < 5 || !/[a-zA-ZÀ-ỹ]/.test(street) || !/\s/.test(street.trim())) { alert('🛑 Vui lòng nhập đầy đủ Số nhà và Tên đường hợp lệ (ví dụ: 123 Lê Lợi)!'); streetInput.focus(); return; }

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
                
                                let billProductsHtml = '';
                (window.checkoutSelectedItems || []).forEach(item => {
                    let hinhAnhObj = item.san_pham.hinh_anh && item.san_pham.hinh_anh.find(img => img.is_primary == 1) || (item.san_pham.hinh_anh && item.san_pham.hinh_anh[0]);
                    let linkAnh = hinhAnhObj ? (hinhAnhObj.hinh_anh_url.startsWith('http') ? hinhAnhObj.hinh_anh_url : 'http://localhost:8000/storage/' + hinhAnhObj.hinh_anh_url) : '/images/logo.jpg';
                    
                    // Thử lấy danh_sach_anh (vì API cart lúc trước trả về danh_sach_anh)
                    if (!hinhAnhObj && item.san_pham.danh_sach_anh && item.san_pham.danh_sach_anh.length > 0) {
                        linkAnh = item.san_pham.danh_sach_anh[0].duong_dan_anh;
                    }

                    let gia = item.san_pham.gia_khuyen_mai ? item.san_pham.gia_khuyen_mai : (item.san_pham.gia_ban || item.san_pham.gia);
                    billProductsHtml += `
                        <div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                            <div class="w-12 h-12 flex-shrink-0 bg-gray-50 rounded-sm overflow-hidden border border-gray-200">
                                <img src="${linkAnh}" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-grow min-w-0">
                                <div class="text-sm font-medium text-gray-800 line-clamp-1" title="${item.san_pham.ten_san_pham}">${item.san_pham.ten_san_pham}</div>
                                <div class="flex justify-between mt-1 text-sm">
                                    <span class="text-gray-500">SL: ${item.so_luong}</span>
                                    <span class="text-pink-600 font-bold">${new Intl.NumberFormat('vi-VN').format(gia * item.so_luong)} đ</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
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
                        html_content: `<html><body style="font-family:sans-serif;text-align:center;padding:50px;background:#fdf2f8;"><h1 style="color:#db2777;font-size:30px">✅ Thanh toán thành công!</h1><p style="font-size:24px;font-weight:bold;color:#db2777;margin:15px 0">${amount.toLocaleString('vi-VN')} đ</p><p style="font-size:18px">Đơn hàng <strong>${orderId}</strong> của bạn sẽ được xác nhận sớm!!</p><p style="color:#666">Vui lòng kiểm tra màn hình máy tính.</p></body></html>`
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