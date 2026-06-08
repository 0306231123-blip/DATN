<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trang chủ - Hệ thống Mỹ Phẩm')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white flex flex-col min-h-screen">

    <header class="bg-gray-200 py-4 px-8 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            
            <a href="/user/home" class="flex items-center">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo Trang Chủ" class="w-14 h-14 rounded-full object-cover shadow-sm border border-gray-200">
            </a>

            <nav class="hidden md:flex space-x-8">
                <a href="/user/product" class="text-gray-700 font-bold hover:text-pink-500 transition">Sản phẩm</a>
                <a href="/user/sale" class="text-gray-700 font-bold hover:text-pink-500 transition">Khuyến mãi</a>
                <a href="/user/bestseller" class="text-gray-700 font-bold hover:text-pink-500 transition">Bán chạy</a>
            </nav>

            <div class="flex items-center space-x-6">
                <div class="relative hidden sm:block">
                    <input type="text" placeholder="Tìm kiếm..." class="pl-4 pr-10 py-1.5 rounded-full border-none focus:outline-none focus:ring-2 focus:ring-pink-400 w-64 shadow-inner">
                    <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-pink-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center space-x-4 text-gray-700">
                    <a href="/user/cart" class="hover:text-pink-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                        </svg>
                    </a>
                    <a href="/user/profileuser" class="hover:text-pink-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </a>
                    <a href="/login" class="hover:text-pink-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        @yield('content')
    </main>

    <footer class="bg-gray-200 py-10 mt-10">
        <div class="max-w-7xl mx-auto px-8 grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left">
            <div>
                <h4 class="font-bold text-gray-800 mb-4 uppercase">Về chúng tôi</h4>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Mô tả ngắn gọn về website mỹ phẩm. Hệ thống AI gợi ý chăm sóc da cá nhân hóa...
                </p>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 mb-4 uppercase">Địa chỉ</h4>
                <p class="text-sm text-gray-600 leading-relaxed">
                    Toà nhà ABC, Đường XYZ<br>
                    Quận 1, TP. Hồ Chí Minh
                </p>
            </div>
            <div>
                <h4 class="font-bold text-gray-800 mb-4 uppercase">Hotline & Hỗ trợ</h4>
                <p class="text-sm text-gray-600 leading-relaxed">
                    1900 xxxx (24/7)<br>
                    support@mypham.vn
                </p>
            </div>
        </div>
    </footer>

</body>
</html>