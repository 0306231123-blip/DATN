@extends('layouts.admin')

@section('title', 'Thống kê')
@section('page-title', 'Thống kê')
@section('page-subtitle', 'Đang tải...')

@section('content')
<!-- Stats Summary Cards -->
<div class="stats-summary" id="stats-summary">
    <div class="summary-card" id="summary-revenue">
        <div class="summary-card-inner">
            <span class="summary-label">Doanh thu</span>
            <span class="summary-value" id="val-doanh-thu">--</span>
            <span class="summary-change" id="change-doanh-thu">
                <i data-lucide="minus" class="icon-xs"></i>
                <span>Đang tải...</span>
            </span>
        </div>
    </div>
    <div class="summary-card" id="summary-orders">
        <div class="summary-card-inner">
            <span class="summary-label">Đơn hàng</span>
            <span class="summary-value" id="val-don-hang">--</span>
            <span class="summary-change" id="change-don-hang">
                <i data-lucide="minus" class="icon-xs"></i>
                <span>Đang tải...</span>
            </span>
        </div>
    </div>
    <div class="summary-card" id="summary-customers">
        <div class="summary-card-inner">
            <span class="summary-label">Khách mới</span>
            <span class="summary-value" id="val-khach-moi">--</span>
            <span class="summary-change" id="change-khach-moi">
                <i data-lucide="minus" class="icon-xs"></i>
                <span>Đang tải...</span>
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
            <p style="color: #9ca3af; text-align: center; padding: 20px;">Đang tải...</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const API_BASE_URL = 'http://localhost:3000/api';
let weeklyChart = null;

async function loadStatistics() {
    try {
        const response = await fetch(`${API_BASE_URL}/statistics/overview`);
        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;
            renderSummary(data.summary);
            renderWeeklyChart(data.doanh_thu_theo_tuan);
            renderCategoryBars(data.doanh_thu_theo_danh_muc);

            const now = new Date();
            document.getElementById('page-subtitle').textContent =
                `Thống kê tháng ${now.getMonth() + 1}/${now.getFullYear()} · Cập nhật lúc ${now.getHours()}:${String(now.getMinutes()).padStart(2, '0')}`;
        } else {
            showError();
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
        showError();
    }
}

function showError() {
    document.getElementById('page-subtitle').textContent = 'Không thể kết nối đến server';
}

// ========== Render Summary Cards ==========
function renderSummary(summary) {
    // Doanh thu
    const doanhThu = summary.doanh_thu;
    let doanhThuFormatted;
    if (doanhThu >= 1000000000) {
        doanhThuFormatted = (doanhThu / 1000000000).toFixed(1) + ' tỷ đ';
    } else if (doanhThu >= 1000000) {
        doanhThuFormatted = (doanhThu / 1000000).toFixed(1) + 'tr đ';
    } else if (doanhThu >= 1000) {
        doanhThuFormatted = Math.round(doanhThu / 1000) + 'k đ';
    } else {
        doanhThuFormatted = new Intl.NumberFormat('vi-VN').format(doanhThu) + 'đ';
    }
    document.getElementById('val-doanh-thu').textContent = doanhThuFormatted;
    renderChange('change-doanh-thu', summary.doanh_thu_percent, 'so tháng trước');

    // Đơn hàng
    document.getElementById('val-don-hang').textContent = summary.so_don;
    renderChange('change-don-hang', summary.so_don_percent, 'so tháng trước');

    // Khách mới
    document.getElementById('val-khach-moi').textContent = summary.khach_moi;
    renderChange('change-khach-moi', summary.khach_moi_percent, 'so tháng trước');
}

function renderChange(elementId, percent, suffix) {
    const el = document.getElementById(elementId);
    const isUp = percent >= 0;
    const icon = isUp ? 'trending-up' : 'trending-down';
    const cls = isUp ? 'summary-change--up' : 'summary-change--down';

    el.className = `summary-change ${cls}`;
    el.innerHTML = `
        <i data-lucide="${icon}" class="icon-xs"></i>
        <span>${Math.abs(percent)}% ${suffix}</span>
    `;
    lucide.createIcons();
}

// ========== Render Weekly Chart ==========
function renderWeeklyChart(weeklyData) {
    const ctx = document.getElementById('weeklyRevenueChart').getContext('2d');

    const labels = weeklyData.map(w => w.tuan);
    const values = weeklyData.map(w => w.doanh_thu / 1000000);

    // Find max value week
    const maxVal = Math.max(...values);

    const bgColors = values.map(v =>
        v === maxVal && maxVal > 0 ? 'rgba(124, 92, 252, 0.75)' : 'rgba(124, 92, 252, 0.25)'
    );

    if (weeklyChart) weeklyChart.destroy();

    weeklyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                data: values,
                backgroundColor: bgColors,
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
                        label: function(ctx) { return ' ' + ctx.parsed.y.toFixed(1) + ' triệu'; }
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
                        callback: function(v) { return v; }
                    },
                    border: { display: false },
                    beginAtZero: true,
                    suggestedMax: Math.ceil(maxVal * 1.3) || 10
                }
            }
        }
    });
}

// ========== Render Category Bars ==========
function renderCategoryBars(categories) {
    const container = document.getElementById('stats-category-list');
    const colors = ['var(--color-primary, #7c5cfc)', 'var(--color-secondary, #f472b6)', 'var(--color-green, #34d399)', 'var(--color-yellow, #fbbf24)', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316'];

    if (!categories || categories.length === 0) {
        container.innerHTML = '<p style="color: #9ca3af; text-align: center; padding: 20px;">Chưa có dữ liệu doanh thu</p>';
        return;
    }

    container.innerHTML = categories.map((cat, index) => {
        const color = colors[index % colors.length];
        return `
            <div class="stats-category-item">
                <div class="stats-cat-info">
                    <span class="stats-cat-dot" style="background: ${color};"></span>
                    <span class="stats-cat-name">${escapeHtml(cat.ten_danh_muc)}</span>
                </div>
                <div class="stats-cat-bar-wrapper">
                    <div class="stats-cat-bar" style="width: 0%; background: ${color};" data-width="${cat.phan_tram}%"></div>
                </div>
                <span class="stats-cat-value">${cat.phan_tram}%</span>
            </div>
        `;
    }).join('');

    // Animate bars
    setTimeout(() => {
        const bars = container.querySelectorAll('.stats-cat-bar');
        bars.forEach((bar, index) => {
            const targetWidth = bar.getAttribute('data-width');
            setTimeout(() => {
                bar.style.width = targetWidth;
            }, 100 + index * 120);
        });
    }, 50);
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ========== Init ==========
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadStatistics();
});
</script>
@endsection
