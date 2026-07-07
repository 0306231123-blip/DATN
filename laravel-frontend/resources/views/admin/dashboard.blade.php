@extends('layouts.admin')

@section('title', 'Dashboard tổng quan')
@section('page-title', 'Dashboard tổng quan')

@section('content')
@php
    // ===== Trích xuất dữ liệu từ Controller =====
    $stats             = $data['stats'] ?? [];
    $doanhThuTheoThang = $data['doanh_thu_theo_thang'] ?? [];
    $sanPhamBanChay    = $data['san_pham_ban_chay'] ?? [];
    $donHangGanDay     = $data['don_hang_gan_day'] ?? [];
    $thongKeTrangThai  = $data['thong_ke_trang_thai_don'] ?? [];

    // ===== Helper: Format tiền VND rút gọn =====
    $formatTien = function($soTien) {
        if ($soTien >= 1000000000) return number_format($soTien / 1000000000, 1) . ' tỷ';
        if ($soTien >= 1000000)    return number_format($soTien / 1000000, 1) . ' tr';
        if ($soTien >= 1000)       return number_format($soTien / 1000, 0) . 'k';
        return number_format($soTien, 0);
    };

    // ===== Helper: Map trạng thái đơn hàng → tiếng Việt & CSS class =====
    $trangThaiMap = [
        'giao_thanh_cong' => ['text' => 'Hoàn thành',   'class' => 'order-badge--completed'],
        'hoan_thanh'      => ['text' => 'Hoàn thành',   'class' => 'order-badge--completed'],
        'dang_giao'       => ['text' => 'Đang giao',    'class' => 'order-badge--shipping'],
        'cho_xac_nhan'    => ['text' => 'Chờ xác nhận', 'class' => 'order-badge--pending'],
        'da_xac_nhan'     => ['text' => 'Đã xác nhận',  'class' => 'order-badge--confirmed'],
        'da_huy'          => ['text' => 'Đã hủy',       'class' => 'order-badge--cancelled'],
        'khong_du_dieu_kien'=> ['text' => 'Không đủ ĐK', 'class' => 'order-badge--cancelled'],
    ];
    $defaultTrangThai = ['text' => 'Không xác định', 'class' => 'order-badge--pending'];
@endphp

<div class="dashboard-container">

    {{-- ===== 1. VIỆC CẦN LÀM ===== --}}
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

    {{-- ===== 2. PHÂN TÍCH BÁN HÀNG ===== --}}
    <div class="dashboard-card">
        <h2 class="section-title">
            <i data-lucide="trending-up" class="icon-sm"></i> Phân Tích Bán Hàng
        </h2>
        <div class="analytics-layout">
            <div class="analytics-stats">
                <div class="stat-box">
                    <span class="stat-box-title">Doanh thu</span>
                    <span class="stat-box-value">{{ $formatTien($stats['tong_doanh_thu'] ?? 0) }}</span>
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

    {{-- ===== 3. INSIGHTS: TOP SẢN PHẨM + ĐƠN GẦN ĐÂY ===== --}}
    <div class="insights-grid">

        {{-- Top Sản Phẩm Bán Chạy --}}
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
                    <div class="empty-state">Chưa có dữ liệu</div>
                @endforelse
            </div>
        </div>

        {{-- Đơn Hàng Gần Đây --}}
        <div class="dashboard-card insight-card">
            <h2 class="section-title">
                <i data-lucide="clock" class="icon-sm"></i> Đơn Hàng Gần Đây
            </h2>
            <div class="table-responsive">
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
                                $trangThai = $trangThaiMap[$dh['trang_thai_don']] ?? $defaultTrangThai;
                            @endphp
                            <tr>
                                <td class="ro-id">{{ $dh['ma_don_hang_custom'] ? '#' . $dh['ma_don_hang_custom'] : '#DH' . str_pad($dh['ma_don_hang'], 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $dh['ho_ten_nguoi_nhan'] }}</td>
                                <td>{{ $formatTien($dh['tong_thanh_toan']) }}</td>
                                <td><span class="order-badge order-badge--sm {{ $trangThai['class'] }}">{{ $trangThai['text'] }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">Chưa có đơn hàng nào</td>
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
    lucide.createIcons();
    initRevenueChart();
});

/**
 * Khởi tạo biểu đồ Doanh thu & Đơn hàng theo tháng
 */
function initRevenueChart() {
    const canvas = document.getElementById('revenueOrdersChart');
    if (!canvas) return;

    // Bảng màu
    const COLORS = {
        primary:       '#7c5cfc',
        primaryAlpha:  'rgba(124, 92, 252, 0.75)',
        secondary:     '#f472b6',
        secondaryFill: 'rgba(244, 114, 182, 0.1)',
        tooltip:       '#1e1b4b',
        textMuted:     '#6b7280',
        gridLine:      '#f3f4f6',
    };

    // Dữ liệu từ backend
    const monthlyData   = @json($doanhThuTheoThang);
    const labels        = monthlyData.map(item => item.thang);
    const revenueValues = monthlyData.map(item => item.doanh_thu / 1_000_000);
    const orderValues   = monthlyData.map(item => item.so_don);

    const maxRevenue = Math.max(...revenueValues, 10);
    const maxOrders  = Math.max(...orderValues, 10);

    new Chart(canvas.getContext('2d'), {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Doanh thu',
                    data: revenueValues,
                    backgroundColor: COLORS.primaryAlpha,
                    borderColor: COLORS.primary,
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
                    borderColor: COLORS.secondary,
                    backgroundColor: COLORS.secondaryFill,
                    borderWidth: 2.5,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: COLORS.secondary,
                    pointBorderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1',
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: COLORS.tooltip,
                    titleFont: { family: 'Inter', size: 12 },
                    bodyFont:  { family: 'Inter', size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label(ctx) {
                            return ctx.dataset.label === 'Doanh thu'
                                ? ` Doanh thu: ${ctx.parsed.y.toFixed(1)}tr`
                                : ` Đơn hàng: ${ctx.parsed.y}`;
                        },
                    },
                },
            },
            scales: {
                x: {
                    grid:   { display: false },
                    ticks:  { font: { family: 'Inter', size: 12, weight: '500' }, color: COLORS.textMuted },
                    border: { display: false },
                },
                y: {
                    position: 'left',
                    grid:   { color: COLORS.gridLine, drawBorder: false },
                    ticks:  {
                        font: { family: 'Inter', size: 11 },
                        color: COLORS.textMuted,
                        callback: v => v + 'tr',
                        stepSize: Math.ceil(maxRevenue / 5),
                    },
                    border: { display: false },
                    beginAtZero: true,
                    suggestedMax: Math.ceil(maxRevenue * 1.2),
                },
                y1: {
                    position: 'right',
                    grid:   { display: false },
                    ticks:  {
                        font: { family: 'Inter', size: 11 },
                        color: COLORS.textMuted,
                        stepSize: Math.ceil(maxOrders / 5),
                    },
                    border: { display: false },
                    beginAtZero: true,
                    suggestedMax: Math.ceil(maxOrders * 1.2),
                },
            },
        },
    });
}
</script>
@endsection
