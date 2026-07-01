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

    <header class="bg-[#ffe1e8] py-4 px-4 md:px-8 sticky top-0 z-50 border-b border-pink-100 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            
            <button id="mobile-menu-btn" class="md:hidden text-gray-700 hover:text-pink-500 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <a href="/user/home" class="flex items-center">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo Trang Chủ" class="w-12 h-12 md:w-14 md:h-14 rounded-full object-cover shadow-sm border border-gray-200">
            </a>

            <nav class="hidden md:flex space-x-8">
                <a href="/user/product" class="text-gray-700 font-bold hover:text-pink-500 transition">Sản phẩm</a>
                <a href="/user/sale" class="text-gray-700 font-bold hover:text-pink-500 transition">Khuyến mãi</a>
                <a href="/user/bestseller" class="text-gray-700 font-bold hover:text-pink-500 transition">Bán chạy</a>
            </nav>

            <div class="flex items-center space-x-4 md:space-x-6">
                
                <div class="relative hidden lg:block">
                    <input type="text" id="search-input" autocomplete="off" placeholder="Tìm kiếm..." class="pl-4 pr-10 py-1.5 rounded-full border-none focus:outline-none focus:ring-2 focus:ring-pink-400 w-64 shadow-inner text-gray-700">
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
                                <button id="mark-all-read" class="text-xs text-pink-500 hover:text-pink-600 font-medium">Đánh dấu đã đọc</button>
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

    <script>
        // HÀM ĐĂNG XUẤT
        function logout(event) {
            if (event) event.preventDefault();
            localStorage.clear();
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
                    const lastSeenTime = localStorage.getItem('last_seen_notification_time') || '0';
                    let latestTime = 0;
                    
                    let html = '';
                    
                    const deletedNotifs = JSON.parse(localStorage.getItem('deleted_notifications') || '[]');
                    
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
                        if (timeVal > parseInt(lastSeenTime)) unreadCount++;
                        
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
                        if (['giao_thanh_cong', 'hoan_thanh', 'da_huy', 'da_tra_hang', 'tu_choi_tra_hang'].includes(order.trang_thai_don)) {
                            targetTab = 'history';
                        }

                        const dateObj = new Date(order.ngay_cap_nhat || order.ngay_dat);
                        const timeStr = dateObj.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) + ' - ' + dateObj.toLocaleDateString('vi-VN');

                        const isUnread = timeVal > parseInt(lastSeenTime) ? 'bg-pink-50' : 'bg-white';
                        
                        html += `
                            <div class="relative block border-b border-gray-100 hover:bg-gray-50 transition ${isUnread}">
                                <a href="/user/profileuser?tab=${targetTab}#order-${order.ma_don_hang}" class="block p-4 pr-10">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="font-bold text-gray-800 text-sm">Đơn hàng #${order.ma_don_hang}</span>
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

                    notifList.innerHTML = html;
                    
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
            let deletedNotifs = JSON.parse(localStorage.getItem('deleted_notifications') || '[]');
            if (!deletedNotifs.includes(notifId)) {
                deletedNotifs.push(notifId);
                localStorage.setItem('deleted_notifications', JSON.stringify(deletedNotifs));
            }
            // Gọi lại loadNotifications để cập nhật UI
            loadNotifications();
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
                        localStorage.setItem('last_seen_notification_time', latest);
                        notifBadge.classList.add('hidden');
                        
                        // Xóa background unread
                        document.querySelectorAll('#notification-list .bg-pink-50').forEach(el => {
                            el.classList.remove('bg-pink-50');
                            el.classList.add('bg-white');
                        });
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
        });
    </script>
</body>
</html>