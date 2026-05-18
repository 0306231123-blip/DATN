@extends('layouts.auth')

@section('title', 'Đăng nhập')

@section('content')
    <h2 class="text-lg font-semibold text-gray-700 mb-4 text-center">ĐĂNG NHẬP HỆ THỐNG</h2>
    
    <form action="#" method="POST" class="space-y-4">
        
        <div>
            <label class="block text-gray-700 text-sm font-medium mb-1" for="email">Email:</label>
            <input class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" 
                   id="email" type="email" placeholder="Nhập email của bạn">
        </div>
        
        <div>
            <label class="block text-gray-700 text-sm font-medium mb-1" for="password">Mật khẩu:</label>
            <input class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500" 
                   id="password" type="password" placeholder="Nhập mật khẩu">
        </div>

        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded mt-2" type="button">
            Đăng nhập
        </button>

    </form>
    
    <div class="mt-4 text-center">
        <p class="text-sm text-gray-600">
            Chưa có tài khoản? 
            <a href="/register" class="text-blue-600 hover:underline">Đăng ký ngay</a>
        </p>
    </div>
@endsection