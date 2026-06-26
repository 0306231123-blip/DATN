<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BeautyAdmin - Trang quản trị cửa hàng mỹ phẩm">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-components.css') }}">
    @yield('styles')

    <!-- Kiểm tra quyền truy cập Admin -->
    <script>
        const token = localStorage.getItem('token');
        const userStr = localStorage.getItem('user');

        if (!token || !userStr) {
            window.location.href = '/login';
        } else {
            try {
                const user = JSON.parse(userStr);
                if (user.vai_tro !== 'quan_tri_vien') {
                    window.location.href = '/user/home';
                }
            } catch (e) {
                window.location.href = '/login';
            }
        }

        if (token) {
            document.cookie = 'token=' + encodeURIComponent(token) + '; path=/; max-age=604800; SameSite=Lax';
        }

        window.ADMIN_API_BASE_URL = 'http://localhost:3000/api';
        const adminFetch = window.fetch.bind(window);
        window.fetch = (resource, options = {}) => {
            const requestUrl = typeof resource === 'string' ? resource : resource?.url;
            if (requestUrl && requestUrl.startsWith(window.ADMIN_API_BASE_URL)) {
                const authToken = localStorage.getItem('token');
                const headers = new Headers(options.headers || (resource instanceof Request ? resource.headers : undefined));
                if (authToken && !headers.has('Authorization')) {
                    headers.set('Authorization', `Bearer ${authToken}`);
                }
                options = { ...options, headers };
            }
            return adminFetch(resource, options);
        };
    </script>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="{{ asset('images/logo.jpg') }}" alt="BeautyAdmin" class="logo-image">
                    <span class="logo-text">BeautyAdmin</span>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-group">
                    <span class="nav-group-label">Tổng quan</span>
                    <a href="{{ url('/admin/dashboard') }}" class="nav-item {{ request()->is('admin/dashboard') ? 'active' : '' }}" id="nav-dashboard">
                        <i data-lucide="layout-dashboard" class="nav-icon"></i>
                        <span>Dashboard</span>
                    </a>
                </div>

                <div class="nav-group">
                    <span class="nav-group-label">Quản lý</span>
                    <a href="{{ url('/admin/products') }}" class="nav-item {{ request()->is('admin/products') ? 'active' : '' }}" id="nav-products">
                        <i data-lucide="package" class="nav-icon"></i>
                        <span>Sản phẩm</span>
                    </a>
                    <a href="{{ url('/admin/categories') }}" class="nav-item {{ request()->is('admin/categories') ? 'active' : '' }}" id="nav-categories">
                        <i data-lucide="layers" class="nav-icon"></i>
                        <span>Danh mục</span>
                    </a>
                    <a href="{{ url('/admin/orders') }}" class="nav-item {{ request()->is('admin/orders') ? 'active' : '' }}" id="nav-orders">
                        <i data-lucide="shopping-cart" class="nav-icon"></i>
                        <span>Đơn hàng</span>
                    </a>
                    <a href="{{ url('/admin/users') }}" class="nav-item {{ request()->is('admin/users') ? 'active' : '' }}" id="nav-users">
                        <i data-lucide="users" class="nav-icon"></i>
                        <span>Người dùng</span>
                    </a>
                </div>

                <div class="nav-group">
                    <span class="nav-group-label">Báo cáo</span>
                    <a href="{{ url('/admin/statistics') }}" class="nav-item {{ request()->is('admin/statistics') ? 'active' : '' }}" id="nav-statistics">
                        <i data-lucide="bar-chart-3" class="nav-icon"></i>
                        <span>Thống kê</span>
                    </a>
                </div>

                <div class="nav-group" style="margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                    <a href="#" class="nav-item text-danger" id="btn-logout" style="color: #ef4444;">
                        <i data-lucide="log-out" class="nav-icon"></i>
                        <span>Đăng xuất</span>
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header" id="top-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menu-toggle" aria-label="Toggle menu">
                        <i data-lucide="menu" class="icon-sm"></i>
                    </button>
                    <div class="header-title-section">
                        <h1 class="header-title">@yield('page-title', 'Dashboard tổng quan')</h1>
                        <p class="header-subtitle" id="page-subtitle">@yield('page-subtitle', 'Cập nhật lúc ' . now()->format('H:i') . ' — ' . now()->format('d/m/Y'))</p>
                    </div>
                </div>
                <div class="header-right">
                    <div class="header-month-selector" id="month-selector">
                        <i data-lucide="calendar" class="icon-xs"></i>
                        <span>Tháng {{ now()->format('n/Y') }}</span>
                        <i data-lucide="chevron-down" class="icon-xs"></i>
                    </div>
                    <button class="header-icon-btn" id="btn-notifications" aria-label="Notifications">
                        <i data-lucide="bell" class="icon-sm"></i>
                        <span class="notification-dot"></span>
                    </button>
                    <div class="header-avatar" id="user-avatar">
                        <span>AD</span>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Custom Message Box Global -->
    <div class="modal" id="modal-confirm-global" style="display: none; max-width: 400px; z-index: 9999;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="confirm-title-global" style="font-size: 1.25rem;">Xác nhận</h2>
            </div>
            <div style="padding: 20px 0;">
                <p id="confirm-message-global" style="margin-bottom: 15px; font-size: 15px; color: var(--text-color);"></p>
                <div id="prompt-container-global" style="display: none;">
                    <input type="text" id="prompt-input-global" class="form-control" style="width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-confirm-cancel-global">Hủy</button>
                <button type="button" class="btn btn-primary" id="btn-confirm-ok-global">Đồng ý</button>
            </div>
        </div>
    </div>
    <div class="modal-overlay" id="modal-overlay-global" style="display: none; z-index: 9998;"></div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Sidebar toggle
        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');

        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }

        // Custom Dialog Function Global
        window.showCustomDialog = function(options) {
            return new Promise((resolve) => {
                const { title, message, isPrompt, defaultValue, isAlert } = options;
                
                document.getElementById('confirm-title-global').textContent = title;
                document.getElementById('confirm-message-global').textContent = message;
                
                const promptContainer = document.getElementById('prompt-container-global');
                const promptInput = document.getElementById('prompt-input-global');
                
                if (isPrompt) {
                    promptContainer.style.display = 'block';
                    promptInput.value = defaultValue || '';
                    setTimeout(() => promptInput.focus(), 50);
                } else {
                    promptContainer.style.display = 'none';
                }
                
                const btnCancel = document.getElementById('btn-confirm-cancel-global');
                const btnOk = document.getElementById('btn-confirm-ok-global');

                if (isAlert) {
                    btnCancel.style.display = 'none';
                    btnOk.textContent = 'OK';
                } else {
                    btnCancel.style.display = 'inline-block';
                    btnOk.textContent = 'Đồng ý';
                }
                
                document.getElementById('modal-confirm-global').style.display = 'block';
                document.getElementById('modal-overlay-global').style.display = 'block';
                
                const cleanup = () => {
                    document.getElementById('modal-confirm-global').style.display = 'none';
                    document.getElementById('modal-overlay-global').style.display = 'none';
                    
                    // Remove listeners to prevent duplicates
                    btnCancel.replaceWith(btnCancel.cloneNode(true));
                    btnOk.replaceWith(btnOk.cloneNode(true));
                };
                
                document.getElementById('btn-confirm-cancel-global').addEventListener('click', () => { 
                    cleanup(); 
                    resolve(null); 
                });
                
                document.getElementById('btn-confirm-ok-global').addEventListener('click', () => {
                    const val = isPrompt ? document.getElementById('prompt-input-global').value : true;
                    cleanup();
                    resolve(val);
                });
            });
        };

        // Show alert Global
        window.showAlert = function(message, type = 'info') {
            const title = type === 'error' ? 'Lỗi' : (type === 'success' ? 'Thành công' : 'Thông báo');
            return window.showCustomDialog({
                title: title,
                message: message,
                isPrompt: false,
                isAlert: true
            });
        };

        // Logout
        const btnLogout = document.getElementById('btn-logout');
        if (btnLogout) {
            btnLogout.addEventListener('click', async (e) => {
                e.preventDefault();
                const confirmLogout = await showCustomDialog({
                    title: 'Đăng xuất',
                    message: 'Bạn có chắc chắn muốn đăng xuất?',
                    isPrompt: false
                });
                if (confirmLogout) {
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    document.cookie = 'token=; path=/; max-age=0; SameSite=Lax';
                    window.location.href = '/login';
                }
            });
        }

    </script>

    @yield('scripts')
</body>
</html>
