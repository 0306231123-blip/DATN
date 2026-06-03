@extends('layouts.admin')

@section('title', 'Dashboard tổng quan')
@section('page-title', 'Dashboard tổng quan')

@section('content')
<!-- Stats Cards -->
<div class="stats-grid" id="stats-grid">
    <div class="stat-card" id="stat-revenue">
        <div class="stat-card-header">
            <div class="stat-icon stat-icon--revenue">
                <i data-lucide="dollar-sign"></i>
            </div>
            <span class="stat-label">Doanh thu</span>
        </div>
        <div class="stat-value">48.2tr</div>
        <div class="stat-change stat-change--up">
            <i data-lucide="trending-up" class="icon-xs"></i>
            <span>12% so tháng trước</span>
        </div>
    </div>

    <div class="stat-card" id="stat-orders">
        <div class="stat-card-header">
            <div class="stat-icon stat-icon--orders">
                <i data-lucide="shopping-bag"></i>
            </div>
            <span class="stat-label">Đơn hàng</span>
        </div>
        <div class="stat-value">213</div>
        <div class="stat-change stat-change--up">
            <i data-lucide="trending-up" class="icon-xs"></i>
            <span>8%</span>
        </div>
    </div>

    <div class="stat-card" id="stat-new-customers">
        <div class="stat-card-header">
            <div class="stat-icon stat-icon--customers">
                <i data-lucide="user-plus"></i>
            </div>
            <span class="stat-label">Khách mới</span>
        </div>
        <div class="stat-value">67</div>
        <div class="stat-change stat-change--down">
            <i data-lucide="trending-down" class="icon-xs"></i>
            <span>5%</span>
        </div>
    </div>

    <div class="stat-card" id="stat-returns">
        <div class="stat-card-header">
            <div class="stat-icon stat-icon--returns">
                <i data-lucide="rotate-ccw"></i>
            </div>
            <span class="stat-label">Hoàn hàng</span>
        </div>
        <div class="stat-value">9</div>
        <div class="stat-change stat-change--up">
            <i data-lucide="trending-up" class="icon-xs"></i>
            <span>2 so tháng trước</span>
        </div>
    </div>

    <div class="stat-card" id="stat-rating">
        <div class="stat-card-header">
            <div class="stat-icon stat-icon--rating">
                <i data-lucide="star"></i>
            </div>
            <span class="stat-label">Đánh giá TB</span>
        </div>
        <div class="stat-value">4.7</div>
        <div class="stat-change stat-change--up">
            <i data-lucide="trending-up" class="icon-xs"></i>
            <span>0.2 điểm</span>
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
        <div class="category-legend" id="category-legend">
            <div class="cat-legend-item">
                <span class="cat-dot" style="background: #7c5cfc;"></span>
                <span class="cat-name">Chăm sóc da</span>
                <span class="cat-line" style="border-color: #7c5cfc;"></span>
                <span class="cat-value">68%</span>
            </div>
            <div class="cat-legend-item">
                <span class="cat-dot" style="background: #f472b6;"></span>
                <span class="cat-name">Trang điểm</span>
                <span class="cat-line" style="border-color: #f472b6;"></span>
                <span class="cat-value">20%</span>
            </div>
            <div class="cat-legend-item">
                <span class="cat-dot" style="background: #34d399;"></span>
                <span class="cat-name">Dưỡng tóc</span>
                <span class="cat-line" style="border-color: #34d399;"></span>
                <span class="cat-value">9%</span>
            </div>
            <div class="cat-legend-item">
                <span class="cat-dot" style="background: #fbbf24;"></span>
                <span class="cat-name">Nước hoa</span>
                <span class="cat-line" style="border-color: #fbbf24;"></span>
                <span class="cat-value">3%</span>
            </div>
        </div>

        <div class="top-products-mini" id="top-products-mini">
            <h3 class="top-products-title">Top sản phẩm bán chạy</h3>
            <div class="top-product-row">
                <span class="tp-name">Kem dưỡng ẩm SPF50</span>
                <span class="tp-count">142</span>
            </div>
            <div class="top-product-row">
                <span class="tp-name">Son môi lì #12</span>
                <span class="tp-count">98</span>
            </div>
            <div class="top-product-row">
                <span class="tp-name">Serum vitamin C</span>
                <span class="tp-count">87</span>
            </div>
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
                    <tr>
                        <td class="order-id">#DH0091</td>
                        <td>Lan Anh</td>
                        <td>855k</td>
                        <td><span class="order-badge order-badge--completed">Hoàn thành</span></td>
                    </tr>
                    <tr>
                        <td class="order-id">#DH00...</td>
                        <td>Minh Châu</td>
                        <td>320k</td>
                        <td><span class="order-badge order-badge--shipping">Đang giao</span></td>
                    </tr>
                    <tr>
                        <td class="order-id">#DH00...</td>
                        <td>Thùy Dung</td>
                        <td>635k</td>
                        <td><span class="order-badge order-badge--pending">Chờ xác nhận</span></td>
                    </tr>
                    <tr>
                        <td class="order-id">#DH00...</td>
                        <td>Hải Yến</td>
                        <td>1.42tr</td>
                        <td><span class="order-badge order-badge--cancelled">Đã hủy</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top 5 Best Selling -->
    <div class="chart-card" id="best-selling-card">
        <h2 class="chart-title">5 sản phẩm bán chạy nhất</h2>
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

    // ========== Revenue & Orders Chart ==========
    const revenueCtx = document.getElementById('revenueOrdersChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: ['T1', 'T2', 'T3', 'T4', 'T5'],
            datasets: [
                {
                    label: 'Doanh thu',
                    data: [28, 35, 32, 42, 48],
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
                    data: [120, 160, 140, 190, 213],
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
                                return ' Doanh thu: ' + context.parsed.y + 'tr';
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
                        stepSize: 10
                    },
                    border: { display: false },
                    beginAtZero: true,
                    max: 50
                },
                y1: {
                    position: 'right',
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: colors.textSecondary,
                        stepSize: 50
                    },
                    border: { display: false },
                    beginAtZero: true,
                    max: 250
                }
            }
        }
    });

    // ========== Category Donut Chart ==========
    const catCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: ['Chăm sóc da', 'Trang điểm', 'Dưỡng tóc', 'Nước hoa'],
            datasets: [{
                data: [68, 20, 9, 3],
                backgroundColor: ['#7c5cfc', '#f472b6', '#34d399', '#fbbf24'],
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
                        label: function(ctx) { return ' ' + ctx.label + ': ' + ctx.parsed + '%'; }
                    }
                }
            }
        }
    });

    // ========== Best Selling Horizontal Bar ==========
    const bestCtx = document.getElementById('bestSellingChart').getContext('2d');
    new Chart(bestCtx, {
        type: 'bar',
        data: {
            labels: ['Kem dưỡng ẩm', 'Son môi lì', 'Serum vit C', 'Dầu gội', 'Toner'],
            datasets: [{
                data: [142, 98, 87, 65, 52],
                backgroundColor: [
                    'rgba(124, 92, 252, 0.8)',
                    'rgba(244, 114, 182, 0.8)',
                    'rgba(52, 211, 153, 0.8)',
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(167, 139, 250, 0.8)'
                ],
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
                        stepSize: 50
                    },
                    border: { display: false },
                    beginAtZero: true,
                    max: 150,
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
});
</script>
@endsection
