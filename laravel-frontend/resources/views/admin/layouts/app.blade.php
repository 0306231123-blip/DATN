<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BeautyAdmin - Dashboard')</title>
    <meta name="description" content="BeautyAdmin - Hệ thống quản lý cửa hàng mỹ phẩm">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        /* ===== CSS Reset & Variables ===== */
        *, *::before, *::after {
            margin: 0; padding: 0; box-sizing: border-box;
        }

        :root {
            /* Colors */
            --bg-body: #f4f1ec;
            --bg-sidebar: #ffffff;
            --bg-card: #ffffff;
            --bg-header: #ffffff;

            --text-primary: #1a1a2e;
            --text-secondary: #6b7280;
            --text-muted: #9ca3af;

            --accent-purple: #7c5cfc;
            --accent-purple-light: #ede9fe;
            --accent-green: #22c55e;
            --accent-green-light: #dcfce7;
            --accent-orange: #f59e0b;
            --accent-orange-light: #fef3c7;
            --accent-red: #ef4444;
            --accent-red-light: #fee2e2;
            --accent-blue: #3b82f6;
            --accent-blue-light: #dbeafe;
            --accent-pink: #ec4899;

            --border-color: #e5e7eb;
            --border-radius: 16px;
            --border-radius-sm: 10px;
            --border-radius-xs: 6px;

            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.06);
            --shadow-lg: 0 8px 24px rgba(0,0,0,0.08);

            --sidebar-width: 240px;
            --header-height: 68px;

            --transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-body);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            -webkit-font-smoothing: antialiased;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0; top: 0;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: var(--transition);
        }

        .sidebar-logo {
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .sidebar-logo .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink));
            border-radius: var(--border-radius-xs);
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 18px;
            box-shadow: 0 4px 12px rgba(124, 92, 252, 0.35);
        }

        .sidebar-logo h1 {
            font-size: 17px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }

        .nav-group {
            margin-bottom: 8px;
        }

        .nav-group-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--text-muted);
            padding: 8px 12px 6px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--border-radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
        }

        .nav-item:hover {
            background: var(--accent-purple-light);
            color: var(--accent-purple);
        }

        .nav-item.active {
            background: var(--accent-purple-light);
            color: var(--accent-purple);
            font-weight: 600;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 20px;
            background: var(--accent-purple);
            border-radius: 0 3px 3px 0;
        }

        .nav-item .material-icons-round {
            font-size: 20px;
        }

        /* ===== Main Content ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
        }

        /* ===== Header ===== */
        .header {
            height: var(--header-height);
            background: var(--bg-header);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 32px;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .header-left h2 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .header-left p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .month-selector {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: var(--bg-body);
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
            cursor: pointer;
            transition: var(--transition);
            font-family: inherit;
        }

        .month-selector:hover {
            border-color: var(--accent-purple);
        }

        .header-icon-btn {
            width: 40px; height: 40px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--text-secondary);
            transition: var(--transition);
            position: relative;
        }

        .header-icon-btn:hover {
            background: var(--accent-purple-light);
            color: var(--accent-purple);
            border-color: var(--accent-purple);
        }

        .header-icon-btn .badge {
            position: absolute;
            top: 6px; right: 6px;
            width: 8px; height: 8px;
            background: var(--accent-red);
            border-radius: 50%;
            border: 2px solid var(--bg-header);
        }

        .header-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-pink));
            display: flex; align-items: center; justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(124, 92, 252, 0.3);
        }

        .header-avatar:hover {
            transform: scale(1.05);
        }

        /* ===== Page Content ===== */
        .page-content {
            padding: 24px 32px 40px;
        }

        /* ===== Stat Cards ===== */
        .stat-cards {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            padding: 20px;
            border: 1px solid var(--border-color);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            opacity: 0;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .stat-card:nth-child(1)::before { background: linear-gradient(90deg, var(--accent-purple), var(--accent-pink)); }
        .stat-card:nth-child(2)::before { background: linear-gradient(90deg, var(--accent-blue), #06b6d4); }
        .stat-card:nth-child(3)::before { background: linear-gradient(90deg, var(--accent-green), #a3e635); }
        .stat-card:nth-child(4)::before { background: linear-gradient(90deg, var(--accent-orange), #fbbf24); }
        .stat-card:nth-child(5)::before { background: linear-gradient(90deg, var(--accent-pink), var(--accent-purple)); }

        .stat-card-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .stat-card-icon {
            width: 36px; height: 36px;
            border-radius: var(--border-radius-xs);
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }

        .stat-card:nth-child(1) .stat-card-icon { background: var(--accent-purple-light); color: var(--accent-purple); }
        .stat-card:nth-child(2) .stat-card-icon { background: var(--accent-blue-light); color: var(--accent-blue); }
        .stat-card:nth-child(3) .stat-card-icon { background: var(--accent-green-light); color: var(--accent-green); }
        .stat-card:nth-child(4) .stat-card-icon { background: var(--accent-orange-light); color: var(--accent-orange); }
        .stat-card:nth-child(5) .stat-card-icon { background: #fce7f3; color: var(--accent-pink); }

        .stat-card-title {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .stat-card-value {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -1px;
            color: var(--text-primary);
            margin-bottom: 4px;
        }

        .stat-card-change {
            font-size: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .stat-card-change.positive { color: var(--accent-green); }
        .stat-card-change.negative { color: var(--accent-red); }

        /* ===== Dashboard Grid ===== */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1.6fr 1fr;
            gap: 20px;
            margin-bottom: 24px;
        }

        .dashboard-grid-bottom {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* ===== Cards ===== */
        .card {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
            padding: 24px;
            transition: var(--transition);
        }

        .card:hover {
            box-shadow: var(--shadow-sm);
        }

        .card-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title .legend {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 12px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .legend-dot {
            display: inline-block;
            width: 10px; height: 10px;
            border-radius: 50%;
            margin-right: 4px;
        }

        /* ===== Chart Containers ===== */
        .chart-container {
            position: relative;
            width: 100%;
        }

        .chart-container canvas {
            width: 100% !important;
        }

        /* ===== Doughnut / Category Section ===== */
        .category-section {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .doughnut-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .doughnut-chart-container {
            width: 140px; height: 140px;
            flex-shrink: 0;
            position: relative;
        }

        .doughnut-chart-container canvas {
            width: 100% !important;
            height: 100% !important;
        }

        .category-legend {
            flex: 1;
        }

        .category-legend-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 13px;
        }

        .category-legend-item .label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
        }

        .category-legend-item .dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .category-legend-item .value {
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ===== Top Products ===== */
        .top-products-divider {
            border: none;
            border-top: 1px solid var(--border-color);
            margin: 0;
        }

        .top-products h4 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .top-product-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
        }

        .top-product-item .name {
            color: var(--text-secondary);
        }

        .top-product-item .count {
            font-weight: 700;
            color: var(--text-primary);
        }

        /* ===== Table ===== */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 0;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table td {
            padding: 12px 0;
            font-size: 13px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .data-table .order-id {
            font-weight: 700;
            color: var(--accent-purple);
        }

        .data-table .customer {
            font-weight: 500;
        }

        /* ===== Status Badges ===== */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-success {
            background: var(--accent-green-light);
            color: #16a34a;
        }

        .badge-warning {
            background: var(--accent-orange-light);
            color: #d97706;
        }

        .badge-info {
            background: var(--accent-purple-light);
            color: var(--accent-purple);
        }

        .badge-danger {
            background: var(--accent-red-light);
            color: var(--accent-red);
        }

        /* ===== Horizontal Bar Chart ===== */
        .h-bar-chart-container {
            position: relative;
            height: 260px;
        }

        /* ===== Scrollbar ===== */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        /* ===== Animations ===== */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-in {
            animation: fadeInUp 0.4s ease-out forwards;
            opacity: 0;
        }

        .animate-in:nth-child(1) { animation-delay: 0.05s; }
        .animate-in:nth-child(2) { animation-delay: 0.1s; }
        .animate-in:nth-child(3) { animation-delay: 0.15s; }
        .animate-in:nth-child(4) { animation-delay: 0.2s; }
        .animate-in:nth-child(5) { animation-delay: 0.25s; }

        /* ===== Responsive ===== */
        @media (max-width: 1400px) {
            .stat-cards {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 1100px) {
            .dashboard-grid,
            .dashboard-grid-bottom {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {
            .stat-cards {
                grid-template-columns: repeat(2, 1fr);
            }
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">
                <span class="material-icons-round">spa</span>
            </div>
            <h1>BeautyAdmin</h1>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-group">
                <div class="nav-group-title">Tổng quan</div>
                <a href="#" class="nav-item active" id="nav-dashboard">
                    <span class="material-icons-round">dashboard</span>
                    Dashboard
                </a>
            </div>
            <div class="nav-group">
                <div class="nav-group-title">Quản lý</div>
                <a href="#" class="nav-item" id="nav-products">
                    <span class="material-icons-round">inventory_2</span>
                    Sản phẩm
                </a>
                <a href="#" class="nav-item" id="nav-categories">
                    <span class="material-icons-round">category</span>
                    Danh mục
                </a>
                <a href="#" class="nav-item" id="nav-orders">
                    <span class="material-icons-round">shopping_bag</span>
                    Đơn hàng
                </a>
                <a href="#" class="nav-item" id="nav-users">
                    <span class="material-icons-round">people</span>
                    Người dùng
                </a>
            </div>
            <div class="nav-group">
                <div class="nav-group-title">Báo cáo</div>
                <a href="#" class="nav-item" id="nav-stats">
                    <span class="material-icons-round">bar_chart</span>
                    Thống kê
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="header">
            <div class="header-left">
                <h2>@yield('page-title', 'Dashboard tổng quan')</h2>
                <p>@yield('page-subtitle', 'Cập nhật lúc 08:30 — 23/05/2026')</p>
            </div>
            <div class="header-right">
                <button class="month-selector" id="monthSelector">
                    <span class="material-icons-round" style="font-size:16px;">calendar_today</span>
                    Tháng 5/2026
                </button>
                <button class="header-icon-btn" id="btnSearch" aria-label="Tìm kiếm">
                    <span class="material-icons-round" style="font-size:20px;">search</span>
                </button>
                <button class="header-icon-btn" id="btnNotifications" aria-label="Thông báo">
                    <span class="material-icons-round" style="font-size:20px;">notifications</span>
                    <span class="badge"></span>
                </button>
                <div class="header-avatar" id="userAvatar" title="Admin">AD</div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="page-content">
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @yield('scripts')
</body>
</html>
