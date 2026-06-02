@extends('layouts.app')
@section('title', 'Checkout')
@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen relative">
    <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12">
        
        <div class="flex flex-col space-y-6">
            <div class="bg-white p-4 rounded-xl shadow-sm text-center font-bold text-gray-700 w-2/3">Total Amount</div>
            <div class="bg-white h-12 rounded-xl shadow-sm w-2/3 flex items-center px-4 font-bold text-pink-500 text-xl">
                500.000 VNĐ
            </div>
            
            <div class="bg-white p-4 rounded-xl shadow-sm text-center font-bold text-gray-700 w-2/3 mt-8">Shipping Address</div>
            <textarea class="bg-white w-full h-32 rounded-xl shadow-sm p-4 border-none focus:ring-2 focus:ring-pink-300 outline-none" placeholder="Enter your address..."></textarea>
        </div>

        <div class="flex flex-col items-center space-y-6">
            <div class="bg-white p-4 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full">Payment Method</div>
            
            <div class="flex space-x-8 w-full justify-center">
                <label class="flex flex-col items-center space-y-2 cursor-pointer">
                    <span class="bg-white px-6 py-2 rounded-lg shadow-sm font-bold text-gray-600">Banking</span>
                    <input type="radio" name="payment" value="banking" class="w-5 h-5 accent-pink-500" checked>
                </label>
                <label class="flex flex-col items-center space-y-2 cursor-pointer">
                    <span class="bg-white px-6 py-2 rounded-lg shadow-sm font-bold text-gray-600">Momo</span>
                    <input type="radio" name="payment" value="momo" class="w-5 h-5 accent-pink-500">
                </label>
            </div>

            <div class="bg-white w-full rounded-2xl flex flex-col items-center justify-center shadow-sm mt-4 p-8">
                <span class="text-gray-500 font-bold text-xl mb-4">Quét mã QR để thanh toán</span>
                
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=ThanhToanDonHangGlowUp" alt="QR Code" class="w-48 h-48 border-4 border-gray-100 rounded-xl mb-6 shadow-sm">
                
                <button id="btn-confirm-pay" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-3 px-8 rounded-xl shadow-md transition uppercase tracking-wider w-full flex justify-center items-center">
                    <span id="btn-text">Tôi đã chuyển khoản</span>
                    <svg id="loading-icon" class="animate-spin ml-2 h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
            <a href="/home" class="bg-gray-800 hover:bg-black text-white font-bold py-3 px-8 rounded-xl shadow-md transition w-full text-center uppercase tracking-wider">
                Về trang chủ
            </a>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnPay = document.getElementById('btn-confirm-pay');
        const btnText = document.getElementById('btn-text');
        const loadingIcon = document.getElementById('loading-icon');
        const successPopup = document.getElementById('success-popup');
        const popupContent = document.getElementById('popup-content');

        btnPay.addEventListener('click', function() {
            // 1. Vô hiệu hóa nút và đổi chữ thành "Đang kiểm tra"
            btnPay.disabled = true;
            btnPay.classList.replace('bg-pink-500', 'bg-gray-400');
            btnPay.classList.replace('hover:bg-pink-600', 'cursor-not-allowed');
            btnText.textContent = 'Đang kiểm tra...';
            loadingIcon.classList.remove('hidden');

            // 2. Chờ giả lập 2.5 giây (2500ms) rồi bật Popup
            setTimeout(() => {
                // Hiển thị nền mờ
                successPopup.classList.remove('hidden');
                
                // Trì hoãn 10ms để hiệu ứng mờ (fade) và phóng to (scale) hoạt động mượt mà
                setTimeout(() => {
                    successPopup.classList.remove('opacity-0');
                    successPopup.classList.add('opacity-100');
                    popupContent.classList.remove('scale-95');
                    popupContent.classList.add('scale-100');
                }, 10);
            }, 2500);
        });
    });
</script>
@endsection