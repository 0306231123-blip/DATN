@extends('layouts.user')
@section('title', 'Trang cá nhân')
@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10">
        
        <div class="col-span-1 flex flex-col items-center space-y-6">
            <div class="w-32 h-32 bg-transparent border-4 border-gray-800 rounded-full flex flex-col items-center justify-end overflow-hidden mb-2">
                <div class="w-12 h-12 bg-gray-800 rounded-full mb-1"></div>
                <div class="w-24 h-12 bg-gray-800 rounded-t-full"></div>
            </div>
            
            <div class="bg-white px-8 py-3 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full">Tên khách hàng</div>
            <button class="bg-white hover:bg-gray-50 px-8 py-3 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full transition">Lịch sử mua hàng</button>
            <button class="bg-white hover:bg-gray-50 px-8 py-3 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full transition">Quản lý đơn hàng</button>
            <button class="bg-white hover:bg-gray-50 px-8 py-3 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full transition">Quản lý thông tin</button>
        
        </div>

        <div class="col-span-2 bg-gray-200 rounded-3xl shadow-inner min-h-[500px]">
            </div>

    </div>
</section>
@endsection