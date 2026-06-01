@extends('layouts.auth')

@section('title', 'Đăng ký tài khoản')

@section('content')
<div class="flex flex-col items-center w-full max-w-2xl">
    
    <!-- Khối Form Đăng Ký -->
    <div class="bg-[#fce4e4] w-full p-10 flex flex-col items-center rounded-3xl shadow-lg">
        
        <div class="mb-4 flex items-center justify-center">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="w-24 h-24 rounded-full object-cover shadow-md border-2 border-white">
        </div>

        <!-- Khối Tiêu đề (Đã xóa nền trắng, phóng to và in đậm chữ) -->
        <h2 class="text-2xl text-gray-800 font-extrabold mb-8 uppercase tracking-wider">
            Đăng ký
        </h2>

        <!-- Form nhập liệu -->
        <form action="#" method="POST" class="w-full max-w-md">
            @csrf
            
            <!-- Tài khoản -->
            <div class="flex flex-col mb-5">
                <label for="username" class="text-sm text-gray-800 font-bold mb-2 ml-3 text-left">
                    Tài khoản
                </label>
                <input type="text" id="username" name="username" class="w-full px-4 py-2 rounded-full border-none focus:outline-none focus:ring-2 focus:ring-pink-400 shadow-inner" required>
            </div>

            <!-- Mật khẩu -->
            <div class="flex flex-col mb-5">
                <label for="password" class="text-sm text-gray-800 font-bold mb-2 ml-3 text-left">
                    Mật khẩu
                </label>
                <div class="relative w-full">
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 pr-10 rounded-full border-none focus:outline-none focus:ring-2 focus:ring-pink-400 shadow-inner" required>
                    <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-pink-500 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Nhập lại mật khẩu -->
            <div class="flex flex-col mb-8">
                <label for="password_confirmation" class="text-sm text-gray-800 font-bold mb-2 ml-3 text-left">
                    Nhập lại mật khẩu
                </label>
                <div class="relative w-full">
                    <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2 pr-10 rounded-full border-none focus:outline-none focus:ring-2 focus:ring-pink-400 shadow-inner" required>
                    <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-3 flex items-center text-gray-500 hover:text-pink-500 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Nút Xác nhận -->
            <div class="flex justify-center mb-6">
                <button type="submit" class="bg-white hover:bg-gray-100 text-gray-800 font-bold py-2 px-10 rounded-full shadow-md transition duration-200">
                    Xác nhận
                </button>
            </div>

            <!-- Link chuyển hướng -->
            <div class="text-center w-full mt-2">
                <a href="/login" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition duration-200">
                    Đã có tài khoản, vui lòng đăng nhập
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    }
</script>
@endsection