@extends('layouts.auth')

@section('title', 'Đăng nhập tài khoản')

@section('content')
<div class="flex flex-col items-center w-full max-w-2xl">
    
    <!-- Khối Form Đăng Nhập -->
    <div class="bg-[#fce4e4] w-full p-10 flex flex-col items-center rounded-3xl shadow-lg">
        
        <!-- Khối Logo -->
        <div class="mb-4 flex items-center justify-center">
            <img src="{{ asset('images/logo.jpg') }}" alt="Logo" class="w-24 h-24 rounded-full object-cover shadow-md border-2 border-white">
        </div>

        <!-- Khối Tiêu đề -->
        <h2 class="text-2xl text-gray-800 font-extrabold mb-8 uppercase tracking-wider">
            Đăng nhập
        </h2>

        <!-- Thông báo lỗi / thành công -->
        <div id="alert-box" class="w-full max-w-md mb-4 hidden">
            <div id="alert-message" class="p-3 rounded-xl text-sm text-center"></div>
        </div>

        <!-- Form nhập liệu -->
        <form id="login-form" class="w-full max-w-md">
            
            <!-- Email -->
            <div class="flex flex-col mb-5">
                <label for="email" class="text-sm text-gray-800 font-bold mb-2 ml-3 text-left">
                    Email
                </label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 rounded-full border-none focus:outline-none focus:ring-2 focus:ring-pink-400 shadow-inner" required>
                <span id="email-error" class="text-red-500 text-xs mt-1 ml-3 hidden"></span>
            </div>

            <!-- Mật khẩu -->
            <div class="flex flex-col mb-6">
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
                <span id="password-error" class="text-red-500 text-xs mt-1 ml-3 hidden"></span>
            </div>

            <!-- Nút Xác nhận -->
            <div class="flex justify-center mb-6">
                <button type="submit" id="login-btn" class="bg-white hover:bg-gray-100 text-gray-800 font-bold py-2 px-10 rounded-full shadow-md transition duration-200">
                    Xác nhận
                </button>
            </div>

            <!-- Khu vực Links (Đăng ký) -->
            <div class="text-right w-full mt-2 px-2">
                <a href="/register" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition duration-200">
                    Đăng ký
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const API_URL = 'http://localhost:3000/api';

    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    function showAlert(message, isError = true) {
        const box = document.getElementById('alert-box');
        const msg = document.getElementById('alert-message');
        box.classList.remove('hidden');
        msg.textContent = message;
        msg.className = isError
            ? 'p-3 rounded-xl text-sm text-center bg-red-100 border border-red-400 text-red-700'
            : 'p-3 rounded-xl text-sm text-center bg-green-100 border border-green-400 text-green-700';
    }

    document.getElementById('login-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const btn = document.getElementById('login-btn');

        // Reset errors
        document.getElementById('email-error').classList.add('hidden');
        document.getElementById('password-error').classList.add('hidden');
        document.getElementById('alert-box').classList.add('hidden');

        // Client-side validation
        if (!email) {
            document.getElementById('email-error').textContent = 'Vui lòng nhập email.';
            document.getElementById('email-error').classList.remove('hidden');
            return;
        }
        if (!password) {
            document.getElementById('password-error').textContent = 'Vui lòng nhập mật khẩu.';
            document.getElementById('password-error').classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Đang xử lý...';

        try {
            const response = await fetch(`${API_URL}/auth/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password }),
            });

            const data = await response.json();

            if (data.success) {
                // Lưu token và thông tin user vào localStorage
                localStorage.setItem('token', data.data.token);
                localStorage.setItem('user', JSON.stringify(data.data.user));
                document.cookie = 'token=' + encodeURIComponent(data.data.token) + '; path=/; max-age=604800; SameSite=Lax';

                showAlert('Đăng nhập thành công! Đang chuyển hướng...', false);

                // Điều hướng theo vai trò
                setTimeout(() => {
                    if (data.data.user.vai_tro === 'quan_tri_vien') {
                        window.location.href = '/admin/dashboard';
                    } else {
                        window.location.href = '/user/home';
                    }
                }, 1000);
            } else {
                showAlert(data.message);
            }
        } catch (error) {
            showAlert('Không thể kết nối đến server. Vui lòng kiểm tra lại.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Xác nhận';
        }
    });
</script>
@endsection