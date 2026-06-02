@extends('layouts.app')
@section('title', 'Giỏ hàng')
@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen">
    <div class="max-w-6xl mx-auto">
        
        <div class="bg-gray-200 p-4 rounded-2xl flex items-center justify-between mb-6 shadow-sm">
            <div class="flex items-center space-x-4 w-1/2">
                <div class="w-20 h-20 bg-white flex items-center justify-center text-xs text-gray-500 rounded-lg">Hình</div>
                <div class="bg-white px-4 py-2 rounded-lg text-gray-700 font-bold flex-1">Tên sản phẩm</div>
            </div>
            <div class="bg-white px-4 py-2 rounded-lg text-gray-700 font-bold">Giá tiền</div>
            <div class="bg-white px-4 py-2 rounded-lg text-gray-700 font-bold">Số lượng</div>
            <div class="flex items-center space-x-2">
                <button class="bg-white w-8 h-8 rounded-lg font-bold hover:bg-gray-100">+</button>
                <button class="bg-white w-8 h-8 rounded-lg font-bold hover:bg-gray-100">-</button>
                <button class="bg-white w-8 h-8 rounded-lg font-bold text-red-500 hover:bg-red-50">✕</button>
            </div>
        </div>

        <div class="flex justify-between items-end mt-12">
            <div class="flex items-center space-x-4">
                <span class="bg-white px-6 py-3 rounded-xl font-bold text-gray-700 shadow-sm">Tổng tiền</span>
                <div class="bg-white w-48 h-12 rounded-xl shadow-sm"></div>
            </div>
            <a href="/checkout" class="bg-pink-500 hover:bg-pink-600 text-white px-8 py-3 rounded-xl font-bold shadow-md transition">
                Thanh toán
            </a>
        </div>
    </div>
</section>
@endsection