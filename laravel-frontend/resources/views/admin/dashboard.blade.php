@extends('layouts.admin')

@section('title', 'Dashboard tổng quan')
@section('page-title', 'Dashboard tổng quan')

@section('content')
@php
    $stats = $data['stats'] ?? [];
    $doanhThuTheoThang = $data['doanh_thu_theo_thang'] ?? [];
    $doanhThuTheoDanhMuc = $data['doanh_thu_theo_danh_muc'] ?? [];
    $sanPhamBanChay = $data['san_pham_ban_chay'] ?? [];
    $donHangGanDay = $data['don_hang_gan_day'] ?? [];
    $thongKeTrangThai = $data['thong_ke_trang_thai_don'] ?? [];

    // Format doanh thu
    $tongDoanhThu = $stats['tong_doanh_thu'] ?? 0;
    if ($tongDoanhThu >= 1000000000) {
        $doanhThuFormatted = number_format($tongDoanhThu / 1000000000, 1) . ' tỷ';
    } elseif ($tongDoanhThu >= 1000000) {
        $doanhThuFormatted = number_format($tongDoanhThu / 1000000, 1) . ' tr';
    } elseif ($tongDoanhThu >= 1000) {
        $doanhThuFormatted = number_format($tongDoanhThu / 1000, 0) . 'k';
    } else {
        $doanhThuFormatted = number_format($tongDoanhThu, 0);
    }
@endphp

<!-- Stats Cards -->
<div class="stats-grid" id="stats-grid">
    <div class="stat-card" id="stat-revenue">
        <div class="stat-card-header">
            <div class="stat-icon stat-icon--revenue">
                <i data-lucide="dollar-sign"></i>
            </div>
            <span class="stat-label">Doanh thu</span>
        </div>
        <div class="stat-value">{{ $doanhThuFormatted }}</div>
        <div class="stat-change stat-change--up">
            <i data-lucide="bar-chart-3" class="icon-xs"></i>
            <span>Đơn thành công</span>
        </div>
    </div>

    <div class="stat-card" id="stat-orders">
        <div class="stat-card-header">
            <div class="stat-icon stat-icon--orders">
                <i data-lucide="shopping-bag"></i>
            </div>
            <span class="stat-label">Đơn hàng</span>
        </div>
        <div class="stat-value">{{ number_format($stats['tong_don_hang'] ?? 0) }}</div>
        <div class="stat-change stat-change--up">
            <i data-lucide="package" class="icon-xs"></i>
            <span>{{ ($thongKeTrangThai['cho_xac_nhan'] ?? 0) }} chờ xác nhận</span>
        </div>
    </div>

    <div class="stat-card" id="stat-new-customers">
        <div class="stat-card-header">
            <div class="stat-icon stat-icon--customers">
                <i data-lucide="user-plus"></i>
            </div>
            <span class="stat-label">Khách mới</span>
        </div>
        <div class="stat-value">{{ number_format($stats['khach_hang_moi'] ?? 0) }}</div>
        <div class="stat-change stat-change--up">
            <i data-lucide="calendar" class="icon-xs"></i>
            <span>Tháng {{ now()->format('n') }}</span>
        </div>
    </div>

    <div class="stat-card" id="stat-products">
        <div class="stat-card-header">
            <div class="stat-icon stat-icon--returns">
                <i data-lucide="package"></i>
            </div>
            <span class="stat-label">Sản phẩm</span>
        </div>
        <div class="stat-value">{{ number_format($stats['san_pham_dang_ban'] ?? 0) }}</div>
        <div class="stat-change stat-change--up">
            <i data-lucide="check-circle" class="icon-xs"></i>
            <span>Đang kinh doanh</span>
        </div>
    </div>

    <div class="stat-card" id="stat-rating">
        <div class="stat-card-header">
            <div class="stat-icon stat-icon--rating">
                <i data-lucide="star"></i>
            </div>
            <span class="stat-label">Đánh giá TB</span>
        </div>
        <div class="stat-value">{{ $stats['danh_gia_trung_binh'] ?? 0 }}</div>
        <div class="stat-change stat-change--up">
            <i data-lucide="message-square" class="icon-xs"></i>
            <span>Từ khách hàng</span>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="charts-row" id="charts-row">
    <!-- Revenue & Orders Chart -->
    <div class="chart-card chart-card--wide" id="revenue-chart-card">
        <div class="chart-card-header">
            <h2 class="chart-title">Doanh thu & đơn hàng theo tháng</h2>
            <div class="chart-legend">
                <span class="legend-item">
                    <span class="legend-dot legend-dot--revenue"></span> Doanh thu
                </span>
                <span class="legend-item">
                    <span class="legend-dot legend-dot--orders"></span> Đơn hàng
                </span>
            </div>
        </div>
        <div class="chart-body">
            <canvas id="revenueOrdersChart"></canvas>
        </div>
    </div>

    <!-- Category Distribution -->
    <div class="chart-card chart-card--narrow" id="category-chart-card">
        <h2 class="chart-title">Doanh thu theo danh mục</h2>
        <div class="chart-body chart-body--donut">
            <canvas id="categoryChart"></canvas>
        </div>
        @php
            $categoryColors = ['#7c5cfc', '#f472b6', '#34d399', '#fbbf24', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#84cc16', '#ec4899', '#14b8a6'];
            $totalDoanhThuDanhMuc = collect($doanhThuTheoDanhMuc)->sum('tong_doanh_thu');
        @endphp
        <div class="category-legend" id="category-legend">
            @forelse ($doanhThuTheoDanhMuc as $index => $dm)
                @php
                    $color = $categoryColors[$index % count($categoryColors)];
                    $percent = $totalDoanhThuDanhMuc > 0 ? round(($dm['tong_doanh_thu'] / $totalDoanhThuDanhMuc) * 100) : 0;
                @endphp
                <div class="cat-legend-item">
                    <span class="cat-dot" style="background: {{ $color }};"></span>
                    <span class="cat-name">{{ $dm['ten_danh_muc'] }}</span>
                    <span class="cat-line" style="border-color: {{ $color }};"></span>
                    <span class="cat-value">{{ $percent }}%</span>
                </div>
            @empty
                <div class="cat-legend-item">
                    <span class="cat-name" style="color: #9ca3af;">Chưa có dữ liệu</span>
                </div>
            @endforelse
        </div>

        <div class="top-products-mini" id="top-products-mini">
            <h3 class="top-products-title">Top sản phẩm bán chạy</h3>
            @forelse (array_slice($sanPhamBanChay, 0, 3) as $sp)
                <div class="top-product-row">
                    <span class="tp-name">{{ $sp['ten_san_pham'] }}</span>
                    <span class="tp-count">{{ number_format($sp['tong_so_luong_ban']) }}</span>
                </div>
            @empty
                <div class="top-product-row">
                    <span class="tp-name" style="color: #9ca3af;">Chưa có dữ liệu</span>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="bottom-row" id="bottom-row">
    <!-- Recent Orders -->
    <div class="chart-card" id="recent-orders-card">
        <h2 class="chart-title">Đơn hàng gần đây</h2>
        <div class="orders-table-wrapper">
            <table class="orders-table" id="orders-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donHangGanDay as $dh)
                        @php
                            $tongTT = $dh['tong_thanh_toan'];
                            if ($tongTT >= 1000000) {
                                $tongFormatted = number_format($tongTT / 1000000, 2) . 'tr';
                            } elseif ($tongTT >= 1000) {
                                $tongFormatted = number_format($tongTT / 1000, 0) . 'k';
                            } else {
                                $tongFormatted = number_format($tongTT, 0);
                            }

                            $badgeClass = match($dh['trang_thai_don']) {
                                'giao_thanh_cong' => 'order-badge--completed',
                                'dang_giao' => 'order-badge--shipping',
                                'cho_xac_nhan' => 'order-badge--pending',
                                'da_xac_nhan' => 'order-badge--confirmed',
                                'da_huy' => 'order-badge--cancelled',
                                default => 'order-badge--pending',
                            };
                            $badgeText = match($dh['trang_thai_don']) {
                                'giao_thanh_cong' => 'Hoàn thành',
                                'dang_giao' => 'Đang giao',
                                'cho_xac_nhan' => 'Chờ xác nhận',
                                'da_xac_nhan' => 'Đã xác nhận',
                                'da_huy' => 'Đã hủy',
                                default => $dh['trang_thai_don'],
                            };
                        @endphp
                        <tr>
                            <td class="order-id">#DH{{ str_pad($dh['ma_don_hang'], 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $dh['ho_ten_nguoi_nhan'] }}</td>
                            <td>{{ $tongFormatted }}</td>
                            <td><span class="order-badge {{ $badgeClass }}">{{ $badgeText }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: #9ca3af; padding: 2rem;">Chưa có đơn hàng nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top 5 Best Selling -->
    <div class="chart-card" id="best-selling-card">
        <h2 class="chart-title">Top sản phẩm bán chạy nhất</h2>
        <div class="chart-body">
            <canvas id="bestSellingChart"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Re-init icons after dynamic content
    lucide.createIcons();

    // Color palette
    const colors = {
        primary: '#7c5cfc',
        primaryLight: 'rgba(124, 92, 252, 0.15)',
        secondary: '#f472b6',
        green: '#34d399',
        yellow: '#fbbf24',
        red: '#ef4444',
        textPrimary: '#1e1b4b',
        textSecondary: '#6b7280',
        border: '#e5e7eb',
        gridLine: '#f3f4f6'
    };

    // ========== Data from backend ==========
    const monthlyData = @json($doanhThuTheoThang);
    const categoryData = @json($doanhThuTheoDanhMuc);
    const bestSellingData = @json(array_slice($sanPhamBanChay, 0, 5));
    const categoryColors = ['#7c5cfc', '#f472b6', '#34d399', '#fbbf24', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316', '#84cc16', '#ec4899', '#14b8a6'];

    // ========== Revenue & Orders Chart ==========
    const revenueLabels = monthlyData.map(item => item.thang);
    const revenueValues = monthlyData.map(item => item.doanh_thu / 1000000); // Convert to triệu
    const orderValues = monthlyData.map(item => item.so_don);

    const maxRevenue = Math.max(...revenueValues, 10);
    const maxOrders = Math.max(...orderValues, 10);

    const revenueCtx = document.getElementById('revenueOrdersChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: revenueLabels,
            datasets: [
                {
                    label: 'Doanh thu',
                    data: revenueValues,
                    backgroundColor: 'rgba(124, 92, 252, 0.75)',
                    borderColor: '#7c5cfc',
                    borderWidth: 1,
                    borderRadius: 6,
                    borderSkipped: false,
                    yAxisID: 'y',
                    barPercentage: 0.6,
                    categoryPercentage: 0.7,
                },
                {
                    label: 'Đơn hàng',
                    data: orderValues,
                    type: 'line',
                    borderColor: colors.secondary,
                    backgroundColor: 'rgba(244, 114, 182, 0.1)',
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: colors.secondary,
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    titleFont: { family: 'Inter', size: 12 },
                    bodyFont: { family: 'Inter', size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            if (context.dataset.label === 'Doanh thu') {
                                return ' Doanh thu: ' + context.parsed.y.toFixed(1) + 'tr';
                            }
                            return ' Đơn hàng: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 12, weight: '500' },
                        color: colors.textSecondary
                    },
                    border: { display: false }
                },
                y: {
                    position: 'left',
                    grid: {
                        color: colors.gridLine,
                        drawBorder: false
                    },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: colors.textSecondary,
                        callback: function(v) { return v + 'tr'; },
                        stepSize: Math.ceil(maxRevenue / 5)
                    },
                    border: { display: false },
                    beginAtZero: true,
                    suggestedMax: Math.ceil(maxRevenue * 1.2)
                },
                y1: {
                    position: 'right',
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: colors.textSecondary,
                        stepSize: Math.ceil(maxOrders / 5)
                    },
                    border: { display: false },
                    beginAtZero: true,
                    suggestedMax: Math.ceil(maxOrders * 1.2)
                }
            }
        }
    });

    // ========== Category Donut Chart ==========
    const catLabels = categoryData.map(item => item.ten_danh_muc);
    const catValues = categoryData.map(item => item.tong_doanh_thu);
    const catColors = categoryData.map((_, i) => categoryColors[i % categoryColors.length]);

    const catCtx = document.getElementById('categoryChart').getContext('2d');
    if (catLabels.length > 0) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catValues,
                    backgroundColor: catColors,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e1b4b',
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? Math.round((ctx.parsed / total) * 100) : 0;
                                const valueTr = (ctx.parsed / 1000000).toFixed(1);
                                return ' ' + ctx.label + ': ' + valueTr + 'tr (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // ========== Best Selling Horizontal Bar ==========
    const bestLabels = bestSellingData.map(item => {
        // Rút ngắn tên nếu quá dài
        const name = item.ten_san_pham;
        return name.length > 20 ? name.substring(0, 20) + '...' : name;
    });
    const bestValues = bestSellingData.map(item => item.tong_so_luong_ban);
    const bestColors = [
        'rgba(124, 92, 252, 0.8)',
        'rgba(244, 114, 182, 0.8)',
        'rgba(52, 211, 153, 0.8)',
        'rgba(251, 191, 36, 0.8)',
        'rgba(167, 139, 250, 0.8)'
    ];

    const maxBest = Math.max(...bestValues, 10);

    const bestCtx = document.getElementById('bestSellingChart').getContext('2d');
    if (bestLabels.length > 0) {
        new Chart(bestCtx, {
            type: 'bar',
            data: {
                labels: bestLabels,
                datasets: [{
                    data: bestValues,
                    backgroundColor: bestColors.slice(0, bestLabels.length),
                    borderRadius: 4,
                    borderSkipped: false,
                    barPercentage: 0.65,
                    categoryPercentage: 0.8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1e1b4b',
                        titleFont: { family: 'Inter', size: 12 },
                        bodyFont: { family: 'Inter', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            title: function(ctx) {
                                // Hiện tên đầy đủ trong tooltip
                                const index = ctx[0].dataIndex;
                                return bestSellingData[index].ten_san_pham;
                            },
                            label: function(ctx) { return ' Đã bán: ' + ctx.parsed.x; }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: colors.gridLine,
                            drawBorder: false
                        },
                        ticks: {
                            font: { family: 'Inter', size: 11 },
                            color: colors.textSecondary,
                            stepSize: Math.ceil(maxBest / 5)
                        },
                        border: { display: false },
                        beginAtZero: true,
                        suggestedMax: Math.ceil(maxBest * 1.2),
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { family: 'Inter', size: 12, weight: '500' },
                            color: colors.textPrimary
                        },
                        border: { display: false }
                    }
                }
            }
        });
    } else {
        // Hiển thị thông báo không có dữ liệu
        bestCtx.font = '14px Inter';
        bestCtx.fillStyle = '#9ca3af';
        bestCtx.textAlign = 'center';
        bestCtx.fillText('Chưa có dữ liệu bán hàng', bestCtx.canvas.width / 2, bestCtx.canvas.height / 2);
    }
});
</script>
@endsection
