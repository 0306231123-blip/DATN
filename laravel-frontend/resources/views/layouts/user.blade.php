<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trang chủ - Hệ thống Mỹ Phẩm')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    @yield('styles')
</head>
<body class="bg-[#fdf0e6] flex flex-col min-h-screen text-gray-800">

    <header class="bg-white/90 backdrop-blur-md py-4 px-4 md:px-8 sticky top-0 z-50 border-b-2 border-pink-500 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            
            <button id="mobile-menu-btn" class="md:hidden text-gray-700 hover:text-pink-500 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="/user/home" class="flex items-center">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo Trang Chủ" class="w-12 h-12 md:w-14 md:h-14 rounded-full object-cover shadow-sm border border-gray-200">
            </a>

            <nav class="hidden md:flex space-x-4 lg:space-x-6">
                <a href="/user/home" class="text-gray-700 font-bold hover:text-pink-500 transition">Trang chủ</a>
                <a href="/user/product" class="text-gray-700 font-bold hover:text-pink-500 transition">Sản phẩm</a>
                <a href="/user/sale" class="text-gray-700 font-bold hover:text-pink-500 transition">Khuyến mãi</a>
                <a href="/user/bestseller" class="text-gray-700 font-bold hover:text-pink-500 transition">Bán chạy</a>
            </nav>

            <div class="flex items-center space-x-4 md:space-x-6">
                
                <div class="relative hidden lg:block">
                    <input type="text" id="search-input" autocomplete="off" placeholder="Tìm kiếm tên sản phẩm, thương hiệu..." class="pl-4 pr-10 py-1.5 rounded-full border-none focus:outline-none focus:ring-2 focus:ring-pink-400 w-80 xl:w-[450px] transition-all shadow-inner text-gray-700">
                    <button class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </button>
                    <div id="search-dropdown" class="absolute left-0 top-full mt-2 w-full bg-white rounded-2xl shadow-2xl border border-gray-100 hidden overflow-hidden z-50 max-h-[400px] overflow-y-auto"></div>
                </div>

                <div class="flex items-center space-x-3 text-gray-700">
                    <button id="mobile-search-btn" class="lg:hidden hover:text-pink-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                    </button>

                    <a href="/user/cart" id="nav-cart" class="relative hover:text-pink-500 transition hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" /></svg>
                        <span id="cart-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full hidden">0</span>
                    </a>

                    <!-- Notifications -->
                    <div class="relative hidden items-center" id="nav-notification-container">
                        <button id="nav-notification" class="relative flex items-center justify-center hover:text-pink-500 transition focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                            <span id="notification-badge" class="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full hidden">0</span>
                        </button>
                        <div id="notification-dropdown" class="absolute right-[-10px] sm:right-0 top-full mt-5 w-[90vw] max-w-[340px] sm:w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 hidden flex-col overflow-hidden z-[60]">
                            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                                <h3 class="font-bold text-gray-800">Thông báo</h3>
                                <div>
                                    <button id="mark-all-read" class="text-xs text-pink-500 hover:text-pink-600 font-medium mr-3" title="Đánh dấu đã đọc">✔ Đã đọc</button>
                                    <button id="delete-read-notifs" class="text-xs text-red-500 hover:text-red-600 font-medium" title="Xóa thông báo đã đọc">🗑 Xóa</button>
                                </div>
                            </div>
                            <div id="notification-list" class="max-h-80 overflow-y-auto custom-scrollbar">
                                <div class="p-4 text-center text-sm text-gray-500">Đang tải...</div>
                            </div>
                        </div>
                    </div>
                    
                    <a href="/user/profileuser" id="nav-profile" class="hover:text-pink-500 transition hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    </a>
                    <a href="/login" id="nav-login" class="hover:text-pink-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                    </a>
                    <a href="javascript:void(0)" onclick="logout(event)" id="nav-logout" class="hover:text-red-500 transition hidden" title="Đăng xuất">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                    </a>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-300">
            <nav class="flex flex-col space-y-4 pt-4 px-2">
                <a href="/user/home" class="text-gray-700 font-bold hover:text-pink-500">Trang chủ</a>
                <a href="/user/product" class="text-gray-700 font-bold hover:text-pink-500">Sản phẩm</a>
                <a href="/user/sale" class="text-gray-700 font-bold hover:text-pink-500">Khuyến mãi</a>
                <a href="/user/bestseller" class="text-gray-700 font-bold hover:text-pink-500">Bán chạy</a>
            </nav>
        </div>

        <div id="mobile-search-bar" class="hidden lg:hidden mt-4 pb-2">
            <input type="text" id="mobile-search-input" placeholder="Tìm kiếm sản phẩm..." class="w-full pl-4 pr-10 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-400">
        </div>
    </header>

    <main class="flex-grow w-full max-w-[1440px] mx-auto px-4 md:px-8 lg:px-12 py-6">
        @yield('content')
    </main>

    <footer class="bg-white/90 backdrop-blur-md text-gray-700 py-10 mt-10 border-t-2 border-pink-500 shadow-inner">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Về chúng tôi</h3>
                <p class="text-sm leading-relaxed text-gray-600">
                    Hệ thống Mỹ Phẩm - Chuyên cung cấp các sản phẩm làm đẹp chính hãng, chất lượng cao. Giúp bạn luôn tự tin và rạng rỡ mỗi ngày.
                </p>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Chính sách</h3>
                <ul class="text-sm space-y-3 text-gray-600">
                    <li><a href="#" onclick="openPolicyModal('privacyPolicyModal', event)" class="font-bold hover:text-pink-500 transition cursor-pointer">Chính sách bảo mật</a></li>
                    <li><a href="#" onclick="openPolicyModal('shippingPolicyModal', event)" class="font-bold hover:text-pink-500 transition cursor-pointer">Chính sách giao hàng</a></li>
                    <li><a href="#" onclick="openPolicyModal('returnPolicyModal', event)" class="font-bold hover:text-pink-500 transition cursor-pointer">Chính sách đổi trả & hoàn tiền</a></li>
                    <li><a href="#" onclick="openPolicyModal('cancelPolicyModal', event)" class="font-bold hover:text-pink-500 transition cursor-pointer">Quy định hủy đơn hàng</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-800 mb-4">Liên hệ</h3>
                <ul class="text-sm space-y-2 text-gray-600">
                    <li class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                        support@mypham.com
                    </li>
                    <li class="flex items-center font-bold text-pink-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                        Hotline: 1900 6868
                    </li>
                    <li class="flex items-center mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-pink-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                        123 Đường Mỹ Phẩm, Quận 1, TP. HCM
                    </li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-8 border-t border-gray-100 text-center text-sm text-gray-500">
            &copy; 2026 Hệ thống Mỹ Phẩm. All rights reserved.
        </div>
    </footer>

    <!-- Modals -->
    <div id="returnPolicyModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-[90%] max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-pink-50">
                <h3 class="font-bold text-pink-600 text-lg flex items-center"><span class="mr-2">📜</span> QUY ĐỊNH ĐỔI TRẢ CỦA SHOP</h3>
                <button onclick="closePolicyModal('returnPolicyModal')" class="text-gray-400 hover:text-red-500 transition focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6">
                <ul class="list-disc pl-5 space-y-2 text-[14px] text-gray-700">
                    <li>Sản phẩm còn nguyên tem mác, bao bì nguyên vẹn, chưa qua sử dụng.</li>
                    <li>Sản phẩm không bị nứt vỡ, trầy xước do lỗi từ phía khách hàng.</li>
                    <li>Chỉ hỗ trợ đổi trả đối với sản phẩm bị lỗi do nhà sản xuất hoặc giao sai phân loại.</li>
                    <li>Yêu cầu đổi/trả phải được tạo trong vòng <strong>3 ngày</strong> kể từ khi nhận hàng.</li>
                    <li class="text-red-500 font-medium">Shop có quyền từ chối nếu không đáp ứng các điều kiện trên!</li>
                    <li style="color: #d81b60; font-weight: bold;">Lưu ý: Chúng tôi chỉ hoàn trả tiền bằng hình thức bank và không hoàn trả bằng tiền mặt</li>
                </ul>
                
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <h4 class="font-bold text-gray-800 text-sm mb-2">Hướng dẫn trả hàng:</h4>
                    <ol class="list-decimal pl-5 space-y-2 text-[13px] text-gray-600">
                        <li>Đăng nhập và truy cập vào trang <strong>Tài khoản cá nhân</strong> > Chọn mục <strong>Lịch sử mua hàng</strong>.</li>
                        <li>Tìm đơn hàng bạn muốn đổi/trả (ở trạng thái <em>Đã giao thành công</em>) và nhấn nút <strong>Trả hàng</strong>.</li>
                        <li>Nhập lý do trả hàng và gửi yêu cầu.</li>
                        <li>Chờ shop xác nhận yêu cầu (trạng thái đơn sẽ chuyển thành <em>Đang trả hàng</em>). Sau đó shop sẽ liên hệ hướng dẫn cách thức gửi lại sản phẩm.</li>
                    </ol>
                </div>

                <p class="mt-4 text-center text-[13px] text-pink-600 font-medium italic">Mọi thắc mắc xin vui lòng liên hệ bộ phận chăm sóc khách hàng của chúng tôi để được giải đáp!!</p>
                <div class="mt-6 text-center">
                    <button onclick="closePolicyModal('returnPolicyModal')" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-6 rounded-full transition shadow-md">Đã hiểu</button>
                </div>
            </div>
        </div>
    </div>

    <div id="cancelPolicyModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-[90%] max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-pink-50">
                <h3 class="font-bold text-pink-600 text-lg flex items-center"><span class="mr-2">⚠️</span> Quy định hủy đơn hàng</h3>
                <button onclick="closePolicyModal('cancelPolicyModal')" class="text-gray-400 hover:text-red-500 transition focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6">
                <p class="text-[14px] text-gray-700 leading-relaxed text-center">Chỉ cho phép hủy đơn trong vòng <strong class="text-red-500">30 phút</strong> sau khi tiến hành đặt đơn.<br>Sau 30 phút, sẽ không thể hủy đơn hàng nữa.</p>
                
                <div class="mt-5 pt-4 border-t border-gray-100 text-left">
                    <h4 class="font-bold text-gray-800 text-sm mb-2">Hướng dẫn hủy đơn hàng:</h4>
                    <ol class="list-decimal pl-5 space-y-2 text-[13px] text-gray-600">
                        <li>Đăng nhập và truy cập vào trang <strong>Tài khoản cá nhân</strong> > Chọn mục <strong>Lịch sử mua hàng</strong>.</li>
                        <li>Tại danh sách đơn hàng mới đặt (trạng thái <em>Chờ xác nhận</em>), nhấn vào nút <strong>Hủy đơn</strong>.</li>
                        <li>Hệ thống sẽ xác nhận lại yêu cầu hủy. Nếu trong vòng 30 phút, đơn sẽ được hủy tự động ngay lập tức.</li>
                        <li>Đơn hàng sẽ chuyển sang trạng thái <em>Đã hủy</em> và quá trình mua hàng cho đơn này sẽ kết thúc.</li>
                    </ol>
                </div>

                <p class="mt-4 text-center text-[13px] text-pink-600 font-medium italic">Mọi thắc mắc xin vui lòng liên hệ bộ phận chăm sóc khách hàng của chúng tôi để được giải đáp!!</p>
                <div class="mt-6 text-center">
                    <button onclick="closePolicyModal('cancelPolicyModal')" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-6 rounded-full transition shadow-md">Đã hiểu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Privacy Policy Modal -->
    <div id="privacyPolicyModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-[90%] max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-pink-50">
                <h3 class="font-bold text-pink-600 text-lg flex items-center"><span class="mr-2">🔒</span> Chính sách bảo mật</h3>
                <button onclick="closePolicyModal('privacyPolicyModal')" class="text-gray-400 hover:text-red-500 transition focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 text-[14px] text-gray-700 space-y-4">
                <div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1">1. Mục đích thu thập thông tin</h4>
                    <p>Thông tin cá nhân (Tên, Số điện thoại, Email, Địa chỉ) được sử dụng để xử lý đơn hàng, liên hệ giao hàng và gửi các thông báo về khuyến mãi (nếu có).</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1">2. Phạm vi sử dụng</h4>
                    <p>Thông tin chỉ lưu hành nội bộ và được cung cấp cho đối tác vận chuyển để thực hiện giao hàng.</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1">3. Cam kết bảo mật</h4>
                    <p>Chúng tôi cam kết không bán, chia sẻ hay trao đổi thông tin cá nhân của khách hàng cho bất kỳ bên thứ ba nào vì mục đích thương mại.</p>
                </div>
                <p class="mt-4 text-center text-[13px] text-pink-600 font-medium italic">Mọi thắc mắc xin vui lòng liên hệ bộ phận chăm sóc khách hàng của chúng tôi để được giải đáp!!</p>
                <div class="mt-6 text-center">
                    <button onclick="closePolicyModal('privacyPolicyModal')" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-6 rounded-full transition shadow-md">Đã hiểu</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipping Policy Modal -->
    <div id="shippingPolicyModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black bg-opacity-50 transition-opacity">
        <div class="bg-white rounded-xl shadow-2xl w-[90%] max-w-md overflow-hidden transform scale-95 transition-transform duration-300">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-pink-50">
                <h3 class="font-bold text-pink-600 text-lg flex items-center"><span class="mr-2">🚚</span> Chính sách giao hàng</h3>
                <button onclick="closePolicyModal('shippingPolicyModal')" class="text-gray-400 hover:text-red-500 transition focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <div class="p-6 text-[14px] text-gray-700 space-y-4">
                <div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1">1. Thời gian giao hàng</h4>
                    <ul class="list-disc pl-5">
                        <li><strong>Nội thành TP.HCM:</strong> Giao hàng trong 1-2 ngày làm việc.</li>
                        <li><strong>Ngoại thành và các tỉnh:</strong> Giao hàng từ 3-5 ngày làm việc.</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1">2. Chi phí giao hàng</h4>
                    <ul class="list-disc pl-5">
                        <li><strong>Nội thành TP.HCM:</strong> 20.000 VNĐ</li>
                        <li><strong>Ngoại thành và các tỉnh:</strong> 35.000 VNĐ</li>
                        <li><strong>MIỄN PHÍ GIAO HÀNG</strong> cho đơn hàng có giá trị từ <strong>1.999.000 VNĐ</strong> trở lên.</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 text-sm mb-1">3. Quy định kiểm hàng</h4>
                    <p>Khách hàng được quyền kiểm tra ngoại quan (tình trạng hộp, tem niêm phong) trước khi nhận. <strong>Không hỗ trợ bóc seal</strong> hộp sản phẩm dùng thử.</p>
                </div>
                <p class="mt-4 text-center text-[13px] text-pink-600 font-medium italic">Mọi thắc mắc xin vui lòng liên hệ bộ phận chăm sóc khách hàng của chúng tôi để được giải đáp!!</p>
                <div class="mt-6 text-center">
                    <button onclick="closePolicyModal('shippingPolicyModal')" class="bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 px-6 rounded-full transition shadow-md">Đã hiểu</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function openPolicyModal(id, event) {
            if (event) event.preventDefault();
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => {
                    modal.children[0].classList.remove('scale-95');
                    modal.children[0].classList.add('scale-100');
                }, 10);
            }
        }

        function closePolicyModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.children[0].classList.remove('scale-100');
                modal.children[0].classList.add('scale-95');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
            }
        }

        // HÀM ĐĂNG XUẤT
        function logout(event) {
            if (event) event.preventDefault();
            localStorage.removeItem('token');
            sessionStorage.clear();
            window.location.replace('/login');
        }

        // HÀM LOAD SỐ LƯỢNG GIỎ HÀNG
        async function loadCartBadge() {
            const token = localStorage.getItem('token');
            if (!token) return;

            try {
                const response = await fetch('http://localhost:3000/api/cart', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const result = await response.json();
                const badge = document.getElementById('cart-badge');
                if (badge && result.success) {
                    const count = result.data.length; 
                    if (count > 0) {
                        badge.innerText = count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                }
            } catch (error) {
                console.error('Lỗi load số lượng giỏ hàng:', error);
            }
        }

        // HÀM LOAD THÔNG BÁO TỪ ĐƠN HÀNG
        async function loadNotifications() {
            const token = localStorage.getItem('token');
            if (!token) return;
            
            let userId = 'default';
            try {
                const payload = JSON.parse(atob(token.split('.')[1]));
                userId = payload.ma_nguoi_dung || payload.id || 'default';
            } catch(e) {}
            
            const seenKey = 'last_seen_notif_' + userId;
            const delKey = 'deleted_notifs_' + userId;

            try {
                const response = await fetch('http://localhost:3000/api/order/my-orders', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                const result = await response.json();
                
                const notifContainer = document.getElementById('nav-notification-container');
                const badge = document.getElementById('notification-badge');
                const notifList = document.getElementById('notification-list');
                
                if (notifContainer) {
                    notifContainer.classList.remove('hidden');
                    notifContainer.classList.add('flex');
                }

                if (result.success && result.data && result.data.length > 0) {
                    let unreadCount = 0;
                    const lastSeenTime = localStorage.getItem(seenKey) || '0';
                    let latestTime = 0;
                    
                    let html = '';
                    
                    const deletedNotifs = JSON.parse(localStorage.getItem(delKey) || '[]');
                    
                    // Lọc ra các thông báo chưa bị xóa, rồi lấy 10 cái gần nhất
                    const filteredOrders = result.data.filter(order => {
                        const notifId = `${order.ma_don_hang}_${order.trang_thai_don}`;
                        return !deletedNotifs.includes(notifId);
                    });
                    
                    const recentOrders = filteredOrders.slice(0, 10);
                    
                    recentOrders.forEach(order => {
                        const notifId = `${order.ma_don_hang}_${order.trang_thai_don}`;
                        const timeVal = new Date(order.ngay_cap_nhat || order.ngay_dat).getTime();
                        if (timeVal > latestTime) latestTime = timeVal;
                        
                        const isRead = timeVal <= parseInt(lastSeenTime);
                        if (!isRead) unreadCount++;
                        
                        let msg = '';
                        let colorClass = 'text-gray-600';
                        
                        switch(order.trang_thai_don) {
                            case 'cho_xac_nhan': msg = 'Đặt hàng thành công, đang chờ xác nhận.'; colorClass = 'text-green-600'; break;
                            case 'da_xac_nhan': msg = 'Đơn hàng đã được xác nhận.'; colorClass = 'text-blue-600'; break;
                            case 'dang_giao': msg = 'Đơn hàng đang được giao đến bạn.'; colorClass = 'text-blue-600'; break;
                            case 'giao_thanh_cong': msg = 'Đơn hàng đã giao thành công.'; colorClass = 'text-green-600'; break;
                            case 'da_huy': msg = 'Đơn hàng đã bị hủy.'; colorClass = 'text-red-600'; break;
                            case 'hoan_thanh': msg = 'Đơn hàng đã hoàn thành. Cảm ơn bạn!'; colorClass = 'text-green-600'; break;
                            case 'dang_tra_hang':
                            case 'tra_hang_hoan_tien': msg = 'Yêu cầu trả hàng đang được xử lý.'; colorClass = 'text-orange-600'; break;
                            case 'tu_choi_tra_hang': msg = 'Yêu cầu trả hàng bị từ chối.'; colorClass = 'text-red-600'; break;
                            case 'da_tra_hang': msg = 'Đơn hàng đã được hoàn tiền/trả hàng.'; colorClass = 'text-gray-600'; break;
                            default: msg = 'Cập nhật trạng thái đơn hàng.'; break;
                        }
                        
                        let targetTab = 'orders';
                        if (['giao_thanh_cong', 'hoan_thanh', 'da_huy', 'da_tra_hang', 'tu_choi_tra_hang', 'khong_du_dieu_kien'].includes(order.trang_thai_don)) {
                            targetTab = 'history';
                        }

                        const dateObj = new Date(order.ngay_cap_nhat || order.ngay_dat);
                        const timeStr = dateObj.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) + ' - ' + dateObj.toLocaleDateString('vi-VN');

                        const isUnreadClass = !isRead ? 'bg-pink-50' : 'bg-white';
                        
                        html += `
                            <div class="relative block border-b border-gray-100 hover:bg-gray-50 transition ${isUnreadClass}" data-notif-id="${notifId}" data-is-read="${isRead}">
                                <a href="/user/profileuser?tab=${targetTab}#order-${order.ma_don_hang}" class="block p-4 pr-10" onclick="markNotificationRead(${timeVal})">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-bold text-gray-800 text-sm">Đơn hàng ${order.ma_don_hang_custom || '#' + order.ma_don_hang}</span>
                                        <span class="text-[11px] text-gray-400">${timeStr}</span>
                                    </div>
                                    <p class="text-sm font-medium ${colorClass}">${msg}</p>
                                </a>
                                <button onclick="deleteNotification('${notifId}', event)" class="absolute top-1/2 -translate-y-1/2 right-3 p-1.5 text-gray-300 hover:text-red-500 rounded-full hover:bg-red-50 transition" title="Xóa thông báo này">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        `;
                    });

                    notifList.innerHTML = html || '<div class="p-4 text-center text-sm text-gray-500">Chưa có thông báo nào.</div>';
                    
                    if (unreadCount > 0) {
                        badge.innerText = unreadCount > 9 ? '9+' : unreadCount;
                        badge.classList.remove('hidden');
                        
                        // Lưu lại latestTime để dùng khi mark as read
                        badge.setAttribute('data-latest', latestTime);
                    } else {
                        badge.classList.add('hidden');
                    }
                } else {
                    notifList.innerHTML = '<div class="p-4 text-center text-sm text-gray-500">Chưa có thông báo nào.</div>';
                }
            } catch (error) {
                console.error('Lỗi load thông báo:', error);
            }
        }

        window.deleteNotification = function(notifId, event) {
            event.preventDefault();
            event.stopPropagation();
            
            const token = localStorage.getItem('token');
            let userId = 'default';
            if (token) {
                try {
                    const payload = JSON.parse(atob(token.split('.')[1]));
                    userId = payload.ma_nguoi_dung || payload.id || 'default';
                } catch(e) {}
            }
            const delKey = 'deleted_notifs_' + userId;

            let deletedNotifs = JSON.parse(localStorage.getItem(delKey) || '[]');
            if (!deletedNotifs.includes(notifId)) {
                deletedNotifs.push(notifId);
                localStorage.setItem(delKey, JSON.stringify(deletedNotifs));
            }
            // Gọi lại loadNotifications để cập nhật UI
            loadNotifications();
        };

        window.markNotificationRead = function(timeVal) {
            const token = localStorage.getItem('token');
            let userId = 'default';
            if (token) {
                try {
                    const payload = JSON.parse(atob(token.split('.')[1]));
                    userId = payload.ma_nguoi_dung || payload.id || 'default';
                } catch(e) {}
            }
            const seenKey = 'last_seen_notif_' + userId;
            
            const currentLastSeen = parseInt(localStorage.getItem(seenKey) || '0');
            if (timeVal > currentLastSeen) {
                localStorage.setItem(seenKey, timeVal.toString());
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Phân quyền Navbar
            const token = localStorage.getItem('token');
            if (token) {
                document.getElementById('nav-cart')?.classList.remove('hidden');
                document.getElementById('nav-profile')?.classList.remove('hidden');
                document.getElementById('nav-logout')?.classList.remove('hidden');
                document.getElementById('nav-login')?.classList.add('hidden');
                loadCartBadge();
                loadNotifications();
            }

            // TOGGLE NOTIFICATION DROPDOWN
            const notifBtn = document.getElementById('nav-notification');
            const notifDropdown = document.getElementById('notification-dropdown');
            const markAllReadBtn = document.getElementById('mark-all-read');
            const deleteReadBtn = document.getElementById('delete-read-notifs');
            const notifBadge = document.getElementById('notification-badge');

            if (notifBtn && notifDropdown) {
                notifBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    notifDropdown.classList.toggle('hidden');
                    notifDropdown.classList.toggle('flex');
                });
                
                document.addEventListener('click', (e) => {
                    if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                        notifDropdown.classList.add('hidden');
                        notifDropdown.classList.remove('flex');
                    }
                });
            }

            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const latest = notifBadge.getAttribute('data-latest');
                    if (latest) {
                        const token = localStorage.getItem('token');
                        let userId = 'default';
                        if (token) {
                            try {
                                const payload = JSON.parse(atob(token.split('.')[1]));
                                userId = payload.ma_nguoi_dung || payload.id || 'default';
                            } catch(e) {}
                        }
                        const seenKey = 'last_seen_notif_' + userId;
                        
                        localStorage.setItem(seenKey, latest);
                        notifBadge.classList.add('hidden');
                        
                        // Xóa background unread
                        document.querySelectorAll('#notification-list .bg-pink-50').forEach(el => {
                            el.classList.remove('bg-pink-50');
                            el.classList.add('bg-white');
                            el.dataset.isRead = "true";
                        });
                    }
                });
            }

            if (deleteReadBtn) {
                deleteReadBtn.addEventListener('click', async (e) => {
                    e.stopPropagation();
                    const token = localStorage.getItem('token');
                    let userId = 'default';
                    if (token) {
                        try {
                            const payload = JSON.parse(atob(token.split('.')[1]));
                            userId = payload.ma_nguoi_dung || payload.id || 'default';
                        } catch(e) {}
                    }
                    const delKey = 'deleted_notifs_' + userId;
                    const seenKey = 'last_seen_notif_' + userId;
                    const lastSeenTime = parseInt(localStorage.getItem(seenKey) || '0');
                    
                    let deletedNotifs = JSON.parse(localStorage.getItem(delKey) || '[]');
                    let changed = false;

                    try {
                        const response = await fetch('http://localhost:3000/api/order/my-orders', {
                            headers: { 'Authorization': 'Bearer ' + token }
                        });
                        const result = await response.json();
                        
                        if (result.success && result.data && result.data.length > 0) {
                            result.data.forEach(order => {
                                const notifId = `${order.ma_don_hang}_${order.trang_thai_don}`;
                                const timeVal = new Date(order.ngay_cap_nhat || order.ngay_dat).getTime();
                                
                                if (timeVal <= lastSeenTime && !deletedNotifs.includes(notifId)) {
                                    deletedNotifs.push(notifId);
                                    changed = true;
                                }
                            });
                        }
                    } catch (error) {
                        console.error('Lỗi khi lấy thông báo để xóa:', error);
                    }
                    
                    if (changed) {
                        localStorage.setItem(delKey, JSON.stringify(deletedNotifs));
                        loadNotifications();
                    } else {
                        alert("Không có thông báo nào đã đọc để xóa!");
                    }
                });
            }

            // TOGGLE MENU MOBILE (Mở/Đóng 3 gạch)
            const mobileBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');
            if(mobileBtn && mobileMenu) {
                mobileBtn.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                });
            }

            // TOGGLE SEARCH MOBILE
            const searchBtnMobile = document.getElementById('mobile-search-btn');
            const searchBarMobile = document.getElementById('mobile-search-bar');
            if(searchBtnMobile && searchBarMobile) {
                searchBtnMobile.addEventListener('click', () => {
                    searchBarMobile.classList.toggle('hidden');
                });
            }

            // Logic Search PC (Giữ nguyên của ông)
            const searchInput = document.getElementById('search-input');
            const searchDropdown = document.getElementById('search-dropdown');
            let debounceTimer;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const keyword = this.value.trim();
                    if (keyword.length < 2) { searchDropdown.classList.add('hidden'); return; }
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(async () => {
                        try {
                            const response = await fetch(`/search-live?q=${encodeURIComponent(keyword)}`);
                            const result = await response.json();
                            const products = result.data;
                            if (products && products.length > 0) {
                                let html = products.map(item => `
                                    <a href="/user/detail/${item.ma_san_pham}" class="flex items-center p-3 hover:bg-gray-50 border-b border-gray-50">
                                        <img src="${item.anh}" class="w-12 h-12 object-cover rounded-md">
                                        <div class="ml-3"><p class="text-sm font-bold text-gray-800">${item.ten_san_pham}</p>
                                        <p class="text-sm text-pink-600 font-black">${new Intl.NumberFormat('vi-VN').format(item.gia_khuyen_mai || item.gia)} đ</p></div>
                                    </a>`).join('');
                                searchDropdown.innerHTML = html;
                                searchDropdown.classList.remove('hidden');
                            } else {
                                searchDropdown.innerHTML = '<div class="p-4 text-center text-sm text-gray-500">Không tìm thấy!</div>';
                                searchDropdown.classList.remove('hidden');
                            }
                        } catch (error) { console.error('Lỗi:', error); }
                    }, 300);
                });
            }

            // --- CHAT WIDGET LOGIC ---
            const chatToggleBtn = document.getElementById('chat-toggle-btn');
            const chatCloseBtn = document.getElementById('chat-close-btn');
            const chatBox = document.getElementById('chat-box');
            const chatMessages = document.getElementById('chat-messages');
            const chatInput = document.getElementById('chat-input');
            const chatSendBtn = document.getElementById('chat-send-btn');
            
            let chatInterval = null;
            let lastMessageCount = 0;

            function formatChatTime(dateString) {
                const date = new Date(dateString);
                return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }

            async function loadChatMessages() {
                const token = localStorage.getItem('token');
                if (!token) {
                    chatMessages.innerHTML = '<div class="text-center text-sm text-gray-500 my-4">Vui lòng <a href="/login" class="text-pink-600 underline">đăng nhập</a> để chat với CSKH.</div>';
                    return;
                }

                try {
                    const res = await fetch('http://localhost:3000/api/chat', {
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    const result = await res.json();
                    
                    if (result.success) {
                        const messages = result.data;
                        if (messages.length !== lastMessageCount) {
                            // Nếu đang đóng chat mà có tin nhắn mới thì hiện chấm đỏ
                            if (chatBox.classList.contains('hidden') && lastMessageCount > 0) {
                                document.getElementById('chat-unread-badge')?.classList.remove('hidden');
                            }
                            
                            lastMessageCount = messages.length;
                            chatMessages.innerHTML = '';
                            
                            if (messages.length === 0) {
                                chatMessages.innerHTML = '<div class="text-center text-xs text-gray-400 my-2">Hãy gửi lời chào đến nhân viên hỗ trợ!</div>';
                            }

                            messages.forEach(msg => {
                                const isAdmin = msg.is_from_admin;
                                const alignClass = isAdmin ? 'items-start' : 'items-end';
                                const bgClass = isAdmin ? 'bg-white border border-gray-100 text-gray-800' : 'bg-pink-500 text-white';
                                const radiusClass = isAdmin ? 'rounded-br-2xl rounded-tr-2xl rounded-bl-sm rounded-tl-2xl' : 'rounded-bl-2xl rounded-tl-2xl rounded-br-sm rounded-tr-2xl';
                                
                                const imgHtml = msg.hinh_anh ? `<img src="${msg.hinh_anh}" class="max-w-full rounded mt-1 mb-1 border border-gray-200" style="max-height: 150px; object-fit: contain;">` : '';
                                const textHtml = msg.noi_dung ? msg.noi_dung : '';
                                
                                const html = `
                                    <div class="flex flex-col ${alignClass} w-full">
                                        <div class="max-w-[80%] ${bgClass} px-3 py-2 ${radiusClass} text-sm shadow-sm flex flex-col">
                                            ${imgHtml}
                                            ${textHtml}
                                        </div>
                                        <span class="text-[10px] text-gray-400 mt-1">${formatChatTime(msg.ngay_gui)}</span>
                                    </div>
                                `;
                                chatMessages.insertAdjacentHTML('beforeend', html);
                            });
                            
                            // Cuộn xuống cuối cùng
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    }
                } catch (err) {
                    console.error('Lỗi tải tin nhắn:', err);
                }
            }

            let selectedChatImage = null;

            async function sendMessage() {
                const token = localStorage.getItem('token');
                if (!token) return;
                
                const text = chatInput.value.trim();
                if (!text && !selectedChatImage) return;
                
                chatInput.value = '';
                document.getElementById('chat-image-preview-container').classList.add('hidden');
                
                let hinh_anh = null;
                
                if (selectedChatImage) {
                    const formData = new FormData();
                    formData.append('image', selectedChatImage);
                    try {
                        const uploadRes = await fetch('http://localhost:3000/api/chat/upload', {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${token}` },
                            body: formData
                        });
                        const uploadData = await uploadRes.json();
                        if (uploadData.success) {
                            hinh_anh = 'http://localhost:8000' + uploadData.url; 
                        } else {
                            alert('Lỗi upload ảnh: ' + uploadData.message);
                            return;
                        }
                    } catch (e) {
                        console.error('Lỗi upload:', e);
                        return;
                    }
                }
                
                selectedChatImage = null;
                document.getElementById('chat-image-input').value = '';
                
                try {
                    const res = await fetch('http://localhost:3000/api/chat', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ noi_dung: text, hinh_anh })
                    });
                    
                    const result = await res.json();
                    if (result.success) {
                        loadChatMessages();
                    } else {
                        alert(result.message);
                    }
                } catch (error) {
                    console.error('Lỗi gửi tin nhắn:', error);
                }
            }

            if (chatToggleBtn) {
                chatToggleBtn.addEventListener('click', () => {
                    chatBox.classList.remove('hidden');
                    chatToggleBtn.classList.add('hidden');
                    document.getElementById('chat-unread-badge')?.classList.add('hidden');
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
            }

            if (chatCloseBtn) {
                chatCloseBtn.addEventListener('click', () => {
                    chatBox.classList.add('hidden');
                    chatToggleBtn.classList.remove('hidden');
                });
            }
            
            // Start background polling for chat
            if (localStorage.getItem('token')) {
                loadChatMessages(); // Initial load
                setInterval(loadChatMessages, 3000);
            }

            if (chatSendBtn) {
                chatSendBtn.addEventListener('click', sendMessage);
            }

            if (chatInput) {
                chatInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        sendMessage();
                    }
                });
            }

            const chatAttachBtn = document.getElementById('chat-attach-btn');
            const chatImageInput = document.getElementById('chat-image-input');
            const chatImagePreviewContainer = document.getElementById('chat-image-preview-container');
            const chatImagePreview = document.getElementById('chat-image-preview');
            const chatRemoveImageBtn = document.getElementById('chat-remove-image-btn');

            if (chatAttachBtn) chatAttachBtn.addEventListener('click', () => chatImageInput.click());
            
            if (chatImageInput) {
                chatImageInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        selectedChatImage = file;
                        chatImagePreview.src = URL.createObjectURL(file);
                        chatImagePreviewContainer.classList.remove('hidden');
                    }
                });
            }

            if (chatRemoveImageBtn) {
                chatRemoveImageBtn.addEventListener('click', () => {
                    selectedChatImage = null;
                    chatImageInput.value = '';
                    chatImagePreviewContainer.classList.add('hidden');
                });
            }

            // XỬ LÝ TÌM KIẾM (ENTER)
            const mainSearchInput = document.getElementById('search-input');
            if (mainSearchInput) {
                mainSearchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const keyword = mainSearchInput.value.trim();
                        if (keyword) {
                            window.location.href = `/user/product?search=${encodeURIComponent(keyword)}`;
                        }
                    }
                });
            }

        });
    </script>

    <!-- Chat Widget -->
    <div id="customer-chat-widget" class="fixed bottom-6 right-6 z-[100]">
        <!-- Chat Bubble Button -->
        <button id="chat-toggle-btn" class="relative w-14 h-14 bg-pink-500 rounded-full shadow-[0_4px_14px_rgba(236,72,153,0.4)] flex items-center justify-center text-white hover:bg-pink-600 transition-transform transform hover:scale-105 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
            <span id="chat-unread-badge" class="hidden absolute right-0 top-0 bg-red-500 w-3.5 h-3.5 rounded-full border-2 border-white"></span>
        </button>

        <!-- Chat Box -->
        <div id="chat-box" class="hidden absolute bottom-0 right-0 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden" style="height: 480px;">
            <!-- Header -->
            <div class="bg-gradient-to-r from-pink-500 to-pink-400 text-white p-4 flex justify-between items-center shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-inner">
                        <span class="text-xl">👩‍💻</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm tracking-wide">CSKH Natural</h4>
                        <p class="text-[11px] text-pink-100 flex items-center gap-1">
                            <span class="w-2 h-2 bg-green-400 rounded-full inline-block"></span> Đang hoạt động
                        </p>
                    </div>
                </div>
                <button id="chat-close-btn" class="text-white hover:text-gray-200 transition focus:outline-none p-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <!-- Messages Area -->
            <div id="chat-messages" class="flex-1 p-4 overflow-y-auto bg-gray-50 flex flex-col gap-3" style="scroll-behavior: smooth;">
                <div class="text-center text-xs text-gray-400 my-2">Bắt đầu cuộc trò chuyện</div>
            </div>

            <!-- Image Preview Area -->
            <div id="chat-image-preview-container" class="hidden px-3 py-2 bg-gray-50 border-t border-gray-100 flex items-start gap-2 relative">
                <img id="chat-image-preview" src="" class="h-16 rounded shadow-sm border border-gray-200">
                <button id="chat-remove-image-btn" class="absolute top-1 right-2 w-5 h-5 bg-gray-400 hover:bg-gray-600 rounded-full text-white flex items-center justify-center text-xs font-bold">&times;</button>
            </div>

            <!-- Input Area -->
            <div class="p-3 bg-white border-t border-gray-100 flex items-center gap-2">
                <input type="file" id="chat-image-input" class="hidden" accept="image/*">
                <button id="chat-attach-btn" class="text-gray-400 hover:text-pink-500 transition focus:outline-none p-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                </button>
                <input type="text" id="chat-input" class="flex-1 bg-gray-100 rounded-full px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-200 transition" placeholder="Nhập tin nhắn...">
                <button id="chat-send-btn" class="w-10 h-10 bg-pink-500 rounded-full text-white flex items-center justify-center hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-pink-300 transition shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform rotate-90 ml-1" viewBox="0 0 20 20" fill="currentColor"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" /></svg>
                </button>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>