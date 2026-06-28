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

<div class="dashboard-container">
    <!-- 1. Việc Cần Làm -->
    <div class="dashboard-card">
        <h2 class="section-title">
            <i data-lucide="clipboard-list" class="icon-sm"></i> Việc Cần Làm
        </h2>
        <div class="todo-grid">
            <a href="/admin/orders?status=cho_xac_nhan" class="todo-item">
                <span class="todo-number">{{ $thongKeTrangThai['cho_xac_nhan'] ?? 0 }}</span>
                <span class="todo-label">Chờ xác nhận</span>
            </a>
            <a href="/admin/orders?status=da_xac_nhan" class="todo-item">
                <span class="todo-number">{{ $thongKeTrangThai['da_xac_nhan'] ?? 0 }}</span>
                <span class="todo-label">Đã xác nhận</span>
            </a>
            <a href="/admin/orders?status=dang_giao" class="todo-item">
                <span class="todo-number">{{ $thongKeTrangThai['dang_giao'] ?? 0 }}</span>
                <span class="todo-label">Đang giao</span>
            </a>
            <a href="/admin/orders?status=da_huy" class="todo-item">
                <span class="todo-number">{{ $thongKeTrangThai['da_huy'] ?? 0 }}</span>
                <span class="todo-label">Đơn hủy</span>
            </a>
        </div>
    </div>

    <!-- 2. Phân Tích Bán Hàng -->
    <div class="dashboard-card">
        <h2 class="section-title">
            <i data-lucide="trending-up" class="icon-sm"></i> Phân Tích Bán Hàng
        </h2>
        <div class="analytics-layout">
            <div class="analytics-stats">
                <div class="stat-box">
                    <span class="stat-box-title">Doanh thu</span>
                    <span class="stat-box-value">{{ $doanhThuFormatted }}</span>
                </div>
                <div class="stat-box">
                    <span class="stat-box-title">Đơn hàng</span>
                    <span class="stat-box-value">{{ number_format($stats['tong_don_hang'] ?? 0) }}</span>
                </div>
                <div class="stat-box">
                    <span class="stat-box-title">Khách hàng mới</span>
                    <span class="stat-box-value">{{ number_format($stats['khach_hang_moi'] ?? 0) }}</span>
                </div>
                <div class="stat-box">
                    <span class="stat-box-title">Sản phẩm đang bán</span>
                    <span class="stat-box-value">{{ number_format($stats['san_pham_dang_ban'] ?? 0) }}</span>
                </div>
            </div>
            <div class="analytics-chart-area">
                <canvas id="revenueOrdersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 3. Insights -->
    <div class="insights-grid">
        <!-- Top Products -->
        <div class="dashboard-card insight-card">
            <h2 class="section-title">
                <i data-lucide="award" class="icon-sm"></i> Top Sản Phẩm Bán Chạy
            </h2>
            <div class="top-products-list">
                @forelse (array_slice($sanPhamBanChay, 0, 5) as $index => $sp)
                    <div class="top-product-item">
                        <div class="tp-rank {{ $index < 3 ? 'top-' . ($index + 1) : '' }}">{{ $index + 1 }}</div>
                        <div class="tp-info">
                            <span class="tp-name" title="{{ $sp['ten_san_pham'] }}">{{ $sp['ten_san_pham'] }}</span>
                            <span class="tp-sales">Đã bán: <span class="tp-value">{{ number_format($sp['tong_so_luong_ban']) }}</span></span>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; color: var(--text-muted); padding: 20px;">Chưa có dữ liệu</div>
                @endforelse
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="dashboard-card insight-card">
            <h2 class="section-title">
                <i data-lucide="clock" class="icon-sm"></i> Đơn Hàng Gần Đây
            </h2>
            <div style="overflow-x: auto;">
                <table class="recent-orders-minimal">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Tổng</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (array_slice($donHangGanDay, 0, 5) as $dh)
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
                                    'giao_thanh_cong', 'hoan_thanh' => 'order-badge--completed',
                                    'dang_giao' => 'order-badge--shipping',
                                    'cho_xac_nhan' => 'order-badge--pending',
                                    'da_xac_nhan' => 'order-badge--confirmed',
                                    'da_huy' => 'order-badge--cancelled',
                                    default => 'order-badge--pending',
                                };
                                $badgeText = match($dh['trang_thai_don']) {
                                    'giao_thanh_cong', 'hoan_thanh' => 'Hoàn thành',
                                    'dang_giao' => 'Đang giao',
                                    'cho_xac_nhan' => 'Chờ xác nhận',
                                    'da_xac_nhan' => 'Đã xác nhận',
                                    'da_huy' => 'Đã hủy',
                                    default => $dh['trang_thai_don'],
                                };
                            @endphp
                            <tr>
                                <td class="ro-id">#DH{{ str_pad($dh['ma_don_hang'], 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $dh['ho_ten_nguoi_nhan'] }}</td>
                                <td>{{ $tongFormatted }}</td>
                                <td><span class="order-badge {{ $badgeClass }}" style="font-size: 0.75rem; padding: 2px 6px;">{{ $badgeText }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">Chưa có đơn hàng nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
        textPrimary: '#1e1b4b',
        textSecondary: '#6b7280',
        gridLine: '#f3f4f6'
    };

    // ========== Data from backend ==========
    const monthlyData = @json($doanhThuTheoThang);

    // ========== Revenue & Orders Chart ==========
    const revenueLabels = monthlyData.map(item => item.thang);
    const revenueValues = monthlyData.map(item => item.doanh_thu / 1000000); // Convert to triệu
    const orderValues = monthlyData.map(item => item.so_don);

    const maxRevenue = Math.max(...revenueValues, 10);
    const maxOrders = Math.max(...orderValues, 10);

    const revenueCtx = document.getElementById('revenueOrdersChart');
    if (revenueCtx) {
        new Chart(revenueCtx.getContext('2d'), {
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
    }
});
</script>
@endsection
