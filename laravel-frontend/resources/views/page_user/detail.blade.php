@extends('layouts.user')
@section('title', 'Chi tiết sản phẩm')
@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
            <div class="flex flex-col items-center">
                <div class="w-full bg-gray-200 aspect-square rounded-3xl flex items-center justify-center shadow-inner mb-6">
                    <span class="text-gray-500 font-bold">Hình ảnh sản phẩm</span>
                </div>
                <div class="flex space-x-4 w-full">
                    <div class="flex-1 bg-white p-4 rounded-xl text-center shadow-sm font-bold text-gray-700">Giá tiền</div>
                    <div class="flex-1 bg-white p-4 rounded-xl text-center shadow-sm font-bold text-gray-400 line-through">Giá KM (nếu có)</div>
                </div>
                <button class="mt-6 w-full bg-pink-500 hover:bg-pink-600 text-white font-bold py-4 rounded-xl shadow-md transition">
                    Thêm vào giỏ hàng
                </button>
            </div>

            <div class="flex flex-col">
                <div class="bg-white p-4 rounded-xl shadow-sm text-center font-bold text-xl text-gray-800 mb-6">
                    Tên sản phẩm
                </div>
                <div class="flex gap-6">
                    <div class="flex-1 bg-white p-6 rounded-2xl shadow-sm min-h-[300px] flex items-center justify-center">
                        <span class="text-gray-500 font-medium">Mô tả sản phẩm</span>
                    </div>
                    <div class="flex flex-col space-y-3 w-1/3">
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-600">Tag sản phẩm</span>
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-600">Tag sản phẩm</span>
                        <span class="bg-white px-3 py-2 rounded-lg text-center shadow-sm text-sm font-bold text-gray-600">Tag sản phẩm</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection