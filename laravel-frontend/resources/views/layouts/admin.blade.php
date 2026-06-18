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
    </script>

    @yield('scripts')
</body>
</html>
