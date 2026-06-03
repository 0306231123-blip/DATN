@extends('layouts.admin')

@section('title', 'Thống kê')
@section('page-title', 'Thống kê tháng 5/2026')
@section('page-subtitle', 'Cập nhật lúc 08:30 hôm nay')

@section('content')
<!-- Stats Summary Cards -->
<div class="stats-summary" id="stats-summary">
    <div class="summary-card" id="summary-revenue">
        <div class="summary-card-inner">
            <span class="summary-label">Doanh thu</span>
            <span class="summary-value">48.2tr đ</span>
            <span class="summary-change summary-change--up">
                <i data-lucide="trending-up" class="icon-xs"></i>
                12% so tháng trước
            </span>
        </div>
    </div>
    <div class="summary-card" id="summary-orders">
        <div class="summary-card-inner">
            <span class="summary-label">Đơn hàng</span>
            <span class="summary-value">213</span>
            <span class="summary-change summary-change--up">
                <i data-lucide="trending-up" class="icon-xs"></i>
                8%
            </span>
        </div>
    </div>
    <div class="summary-card" id="summary-customers">
        <div class="summary-card-inner">
            <span class="summary-label">Khách mới</span>
            <span class="summary-value">67</span>
            <span class="summary-change summary-change--down">
                <i data-lucide="trending-down" class="icon-xs"></i>
                5%
            </span>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="stats-charts-row" id="stats-charts-row">
    <!-- Weekly Revenue Chart -->
    <div class="data-card data-card--chart" id="weekly-revenue-card">
        <h3 class="card-section-title">Doanh thu theo tuần (triệu đ)</h3>
        <div class="chart-body chart-body--stats">
            <canvas id="weeklyRevenueChart"></canvas>
        </div>
    </div>

    <!-- Category Revenue Distribution -->
    <div class="data-card data-card--chart" id="category-revenue-card">
        <h3 class="card-section-title">Doanh thu theo danh mục</h3>
        <div class="stats-category-list" id="stats-category-list">
            <div class="stats-category-item">
                <div class="stats-cat-info">
                    <span class="stats-cat-dot" style="background: var(--color-primary);"></span>
                    <span class="stats-cat-name">Chăm sóc da</span>
                </div>
                <div class="stats-cat-bar-wrapper">
                    <div class="stats-cat-bar" style="width: 68%; background: var(--color-primary);"></div>
                </div>
                <span class="stats-cat-value">68%</span>
            </div>
            <div class="stats-category-item">
                <div class="stats-cat-info">
                    <span class="stats-cat-dot" style="background: var(--color-secondary);"></span>
                    <span class="stats-cat-name">Trang điểm</span>
                </div>
                <div class="stats-cat-bar-wrapper">
                    <div class="stats-cat-bar" style="width: 20%; background: var(--color-secondary);"></div>
                </div>
                <span class="stats-cat-value">20%</span>
            </div>
            <div class="stats-category-item">
                <div class="stats-cat-info">
                    <span class="stats-cat-dot" style="background: var(--color-green);"></span>
                    <span class="stats-cat-name">Dưỡng tóc</span>
                </div>
                <div class="stats-cat-bar-wrapper">
                    <div class="stats-cat-bar" style="width: 9%; background: var(--color-green);"></div>
                </div>
                <span class="stats-cat-value">9%</span>
            </div>
            <div class="stats-category-item">
                <div class="stats-cat-info">
                    <span class="stats-cat-dot" style="background: var(--color-yellow);"></span>
                    <span class="stats-cat-name">Nước hoa</span>
                </div>
                <div class="stats-cat-bar-wrapper">
                    <div class="stats-cat-bar" style="width: 3%; background: var(--color-yellow);"></div>
                </div>
                <span class="stats-cat-value">3%</span>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    // ========== Weekly Revenue Bar Chart ==========
    const weeklyCtx = document.getElementById('weeklyRevenueChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'bar',
        data: {
            labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4', 'Tuần 5'],
            datasets: [{
                data: [8, 10, 9, 14, 7],
                backgroundColor: [
                    'rgba(124, 92, 252, 0.25)',
                    'rgba(124, 92, 252, 0.25)',
                    'rgba(124, 92, 252, 0.25)',
                    'rgba(124, 92, 252, 0.75)',
                    'rgba(124, 92, 252, 0.25)'
                ],
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.55,
                categoryPercentage: 0.7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    titleFont: { family: 'Inter', size: 12 },
                    bodyFont: { family: 'Inter', size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(ctx) { return ' ' + ctx.parsed.y + ' triệu'; }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 12, weight: '500' },
                        color: '#6b7280'
                    },
                    border: { display: false }
                },
                y: {
                    grid: {
                        color: '#f3f4f6',
                        drawBorder: false
                    },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: '#9ca3af',
                        stepSize: 5,
                        callback: function(v) { return v; }
                    },
                    border: { display: false },
                    beginAtZero: true,
                    max: 20
                }
            }
        }
    });

    // Animate stats category bars
    const statsBars = document.querySelectorAll('.stats-cat-bar');
    statsBars.forEach((bar, index) => {
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = targetWidth;
        }, 200 + index * 150);
    });
});
</script>
@endsection
