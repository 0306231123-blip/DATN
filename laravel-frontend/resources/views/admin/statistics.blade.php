@extends('layouts.admin')

@section('title', 'Thống kê')
@section('page-title', 'Thống kê bán hàng')
@section('page-subtitle', 'Đang tải...')

@section('content')
<!-- Date Range Filter Bar -->
<div class="stats-filter-bar" id="stats-filter-bar">
    <select class="filter-select" id="filter-range-select">
        <option value="today">Hôm nay</option>
        <option value="yesterday">Hôm qua</option>
        <option value="7days">7 ngày qua</option>
        <option value="30days" selected>30 ngày qua</option>
        <option value="custom">Tùy chỉnh...</option>
    </select>
    <div class="filter-separator"></div>
    <div id="current-range-display" class="current-range-display">
        <span id="range-text">30 ngày qua</span>
    </div>
    <div style="flex-grow: 1;"></div>
    <button class="btn btn-outline" id="btn-export-excel" style="display:flex; align-items:center; gap:8px; border:1px solid var(--border-color); background:var(--bg-card); color:var(--text-primary); padding:8px 16px; border-radius:var(--radius-sm); cursor:pointer; font-weight:500;">
        <i data-lucide="download" class="icon-sm"></i>
        Xuất báo cáo
    </button>
</div>

<!-- Custom Date Modal (MessageBox) -->
<dialog id="custom-date-modal" class="stats-modal">
    <div class="stats-modal-content">
        <h3 class="stats-modal-title">Tùy chỉnh thời gian</h3>
        <div class="stats-modal-body">
            <div class="stats-date-inputs">
                <div class="date-input-group">
                    <label>Từ ngày</label>
                    <input type="date" id="filter-start-date" title="Từ ngày">
                </div>
                <span class="date-separator"><i data-lucide="arrow-right" class="icon-xs"></i></span>
                <div class="date-input-group">
                    <label>Đến ngày</label>
                    <input type="date" id="filter-end-date" title="Đến ngày">
                </div>
            </div>
        </div>
        <div class="stats-modal-footer">
            <button class="btn-cancel" id="btn-cancel-date">Hủy</button>
            <button class="btn-apply" id="btn-apply-date">Áp dụng</button>
        </div>
    </div>
</dialog>

<!-- Top Container: Summary (Left) & Chart (Right) -->
<div class="stats-top-container">
    <!-- Key Metrics Summary Cards -->
    <div class="stats-summary" id="stats-summary">
        <div class="summary-card" id="summary-revenue">
            <div class="summary-card-inner">
                <div class="summary-card-icon icon-revenue">
                    <i data-lucide="wallet" class="icon-sm"></i>
                </div>
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
                <div class="summary-card-icon icon-orders">
                    <i data-lucide="shopping-bag" class="icon-sm"></i>
                </div>
                <span class="summary-label">Đơn hàng</span>
                <span class="summary-value" id="val-don-hang">--</span>
                <span class="summary-change" id="change-don-hang">
                    <i data-lucide="minus" class="icon-xs"></i>
                    <span>Đang tải...</span>
                </span>
            </div>
        </div>
        <div class="summary-card" id="summary-sold">
            <div class="summary-card-inner">
                <div class="summary-card-icon icon-sold">
                    <i data-lucide="package-check" class="icon-sm"></i>
                </div>
                <span class="summary-label">Sản phẩm đã bán</span>
                <span class="summary-value" id="val-da-ban">--</span>
                <span class="summary-change" id="change-da-ban">
                    <i data-lucide="minus" class="icon-xs"></i>
                    <span>Đang tải...</span>
                </span>
            </div>
        </div>
        <div class="summary-card" id="summary-customers">
            <div class="summary-card-inner">
                <div class="summary-card-icon icon-customers">
                    <i data-lucide="user-plus" class="icon-sm"></i>
                </div>
                <span class="summary-label">Khách hàng mới</span>
                <span class="summary-value" id="val-khach-moi">--</span>
                <span class="summary-change" id="change-khach-moi">
                    <i data-lucide="minus" class="icon-xs"></i>
                    <span>Đang tải...</span>
                </span>
            </div>
        </div>
        <div class="summary-card" id="summary-aov">
            <div class="summary-card-inner">
                <div class="summary-card-icon" style="color: #fbbf24; background: rgba(251, 191, 36, 0.1);">
                    <i data-lucide="receipt" class="icon-sm"></i>
                </div>
                <span class="summary-label">Giá trị TB đơn</span>
                <span class="summary-value" id="val-aov">--</span>
                <span class="summary-change" id="change-aov">
                    <i data-lucide="minus" class="icon-xs"></i>
                    <span>Đang tải...</span>
                </span>
            </div>
        </div>
        <div class="summary-card" id="summary-cancel">
            <div class="summary-card-inner">
                <div class="summary-card-icon" style="color: #ef4444; background: rgba(239, 68, 68, 0.1);">
                    <i data-lucide="x-circle" class="icon-sm"></i>
                </div>
                <span class="summary-label">Tỷ lệ Hủy/Hoàn</span>
                <span class="summary-value" id="val-huy">--</span>
                <span class="summary-change" id="change-huy">
                    <i data-lucide="minus" class="icon-xs"></i>
                    <span>Đang tải...</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Trend Line Chart -->
    <div class="stats-charts-row" id="stats-charts-row">
        <div class="data-card data-card--chart" id="trend-chart-card">
            <div class="chart-header-row">
                <div>
                    <h3 class="card-section-title" style="margin-bottom: 2px;">Biểu đồ xu hướng</h3>
                    <p class="chart-description">So sánh doanh thu (trục trái, đơn vị: đồng) và số đơn hàng (trục phải) theo từng ngày trong kỳ</p>
                </div>
                <div class="chart-insight-badges" id="chart-insight-badges">
                    <!-- filled by JS -->
                </div>
            </div>
            <div class="chart-legend-toggles" id="chart-legend-toggles">
                <div class="chart-legend-item active" data-dataset="revenue" id="toggle-revenue">
                    <span class="chart-legend-dot" style="background: #7c5cfc;"></span>
                    <span>Doanh thu</span>
                    <span class="legend-axis-hint">(trái)</span>
                </div>
                <div class="chart-legend-item active" data-dataset="orders" id="toggle-orders">
                    <span class="chart-legend-dot" style="background: #f472b6;"></span>
                    <span>Đơn hàng</span>
                    <span class="legend-axis-hint">(phải)</span>
                </div>
                <span class="chart-hint-text"><i data-lucide="info" class="icon-xs"></i> Click vào tên để ẩn/hiện đường</span>
            </div>
            <div class="chart-body chart-body--stats">
                <canvas id="trendChart"></canvas>
            </div>
            <div class="chart-footer-note">
                <i data-lucide="mouse-pointer-2" class="icon-xs"></i>
                Di chuột lên biểu đồ để xem chi tiết từng ngày
            </div>
        </div>
    </div>
</div>



<!-- Bottom Row: Top Products + Category Distribution -->
<div class="stats-bottom-row" id="stats-bottom-row">
    <!-- Top Products -->
    <div class="data-card data-card--chart top-products-section" id="top-products-card">
        <h3 class="card-section-title">Top sản phẩm bán chạy</h3>
        <p class="chart-description" style="margin-top: 2px; margin-bottom: 12px;">Sản phẩm được bán nhiều nhất trong kỳ, xếp hạng theo số lượng đã bán</p>
        <table class="top-products-table" id="top-products-table">
            <thead>
                <tr>
                    <th title="Thứ hạng">#</th>
                    <th>Sản phẩm</th>
                    <th title="Tổng số lượng bán được">Đã bán <span class="th-unit">(cái)</span></th>
                    <th title="Tổng doanh thu từ sản phẩm này">Doanh thu <span class="th-unit">(đ)</span></th>
                </tr>
            </thead>
            <tbody id="top-products-tbody">
                <tr><td colspan="4" style="text-align: center; color: #9ca3af; padding: 30px;">Đang tải...</td></tr>
            </tbody>
        </table>
    </div>

    <!-- Category Distribution (Doughnut) -->
    <div class="data-card data-card--chart category-chart-section" id="category-chart-card">
        <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;">
            <div>
                <h3 class="card-section-title" style="margin-bottom: 2px;">Cơ cấu danh mục</h3>
                <p class="chart-description">Tỷ lệ đóng góp doanh thu của từng danh mục sản phẩm</p>
            </div>
            <div class="category-total-badge" id="category-total-badge"></div>
        </div>
        <div class="category-chart-wrapper" id="category-chart-wrapper">
            <div class="category-doughnut-container" style="position: relative;">
                <canvas id="categoryDoughnutChart"></canvas>
                <!-- Center label -->
                <div class="doughnut-center-label" id="doughnut-center-label">
                    <span class="doughnut-center-value" id="doughnut-center-value">--</span>
                    <span class="doughnut-center-sub">Tổng DT</span>
                </div>
            </div>
            <div class="category-legend" id="category-legend">
                <p style="color: #9ca3af; text-align: center; padding: 20px;">Đang tải...</p>
            </div>
        </div>
    </div>
</div>

<style>
/* Chart header row */
.chart-header-row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.chart-description {
    font-size: 0.78rem;
    color: var(--text-muted);
    margin: 0 0 4px 0;
    line-height: 1.5;
}

.chart-insight-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.insight-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: var(--bg-body);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-sm);
    padding: 6px 12px;
    min-width: 80px;
}

.insight-badge-label {
    font-size: 0.68rem;
    color: var(--text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    white-space: nowrap;
}

.insight-badge-value {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-top: 1px;
}

.legend-axis-hint {
    font-size: 0.7rem;
    color: var(--text-muted);
    margin-left: 2px;
}

.chart-hint-text {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 0.75rem;
    color: var(--text-muted);
    font-style: italic;
}

.chart-footer-note {
    margin-top: 8px;
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.73rem;
    color: var(--text-muted);
    font-style: italic;
}

/* Doughnut center label */
.doughnut-center-label {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    pointer-events: none;
}

.doughnut-center-value {
    display: block;
    font-size: 0.8rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1.2;
}

.doughnut-center-sub {
    display: block;
    font-size: 0.62rem;
    color: var(--text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.category-total-badge {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--color-primary);
    background: var(--color-primary-light);
    border-radius: var(--radius-full);
    padding: 3px 10px;
    white-space: nowrap;
    flex-shrink: 0;
    align-self: flex-start;
    margin-top: 2px;
}

/* Table header unit hint */
.th-unit {
    font-weight: 400;
    opacity: 0.65;
    font-size: 0.7em;
}

/* Category legend - updated for revenue display */
.category-legend-revenue {
    font-size: 0.73rem;
    color: var(--text-muted);
    font-weight: 500;
    display: block;
    margin-top: 1px;
}

/* Tooltip explain for summary cards */
.summary-label-row {
    display: flex;
    align-items: center;
    gap: 4px;
}

.summary-info-icon {
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: var(--border-color);
    color: var(--text-muted);
    font-size: 0.6rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    cursor: help;
    flex-shrink: 0;
    line-height: 1;
    font-style: normal;
}

/* Modal Styles */
.stats-modal {
    border: none;
    border-radius: var(--radius-lg);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    padding: 0;
    width: 400px;
    max-width: 90vw;
    background: var(--bg-card);
    color: var(--text-primary);
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    margin: 0;
}
.stats-modal::backdrop {
    background: rgba(15, 23, 42, 0.5);
    backdrop-filter: blur(4px);
}
.stats-modal-content {
    display: flex;
    flex-direction: column;
}
.stats-modal-title {
    padding: 20px;
    margin: 0;
    border-bottom: 1px solid var(--border-light);
    font-size: 1.1rem;
    font-weight: 600;
}
.stats-modal-body {
    padding: 24px 20px;
}
.stats-date-inputs {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
}
.date-input-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
}
.date-input-group label {
    font-size: 0.85rem;
    color: var(--text-secondary);
    font-weight: 500;
}
.stats-date-inputs input[type="date"] {
    width: 100%;
    padding: 10px 12px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border-color);
    font-family: 'Inter', sans-serif;
    font-size: 0.9rem;
    color: var(--text-primary);
    background: var(--bg-body);
    outline: none;
    transition: border-color 0.2s;
}
.stats-date-inputs input[type="date"]:focus {
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px var(--color-primary-light);
}
.stats-modal .date-separator {
    padding-bottom: 10px;
    color: var(--text-muted);
}
.stats-modal-footer {
    padding: 16px 20px;
    border-top: 1px solid var(--border-light);
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: var(--bg-body);
    border-radius: 0 0 var(--radius-lg) var(--radius-lg);
}
.stats-modal-footer button {
    padding: 8px 16px;
    border-radius: var(--radius-sm);
    font-weight: 500;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}
.btn-cancel {
    background: transparent;
    color: var(--text-secondary);
}
.btn-cancel:hover {
    background: var(--border-light);
    color: var(--text-primary);
}
.btn-apply {
    background: var(--color-primary);
    color: #fff;
    box-shadow: 0 2px 6px rgba(124, 92, 252, 0.3);
}
.btn-apply:hover {
    background: var(--color-primary-dark);
}
.current-range-display {
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-weight: 500;
}
</style>

@endsection

@section('scripts')
<script>
const API_BASE_URL = window.ADMIN_API_BASE_URL || 'http://localhost:3000/api';
let trendChartInstance = null;
let categoryChartInstance = null;
let currentStartDate = '';
let currentEndDate = '';
let currentLabel = '30 ngày qua';
let lastLoadedData = null;

// ========== Date Helpers ==========
function toDateStr(d) {
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    return `${y}-${m}-${day}`;
}

function getDateRange(rangeKey) {
    const today = new Date();
    let start, end;
    switch (rangeKey) {
        case 'today':
            start = end = new Date(today);
            break;
        case 'yesterday':
            start = end = new Date(today);
            start.setDate(start.getDate() - 1);
            end = new Date(start);
            break;
        case '7days':
            end = new Date(today);
            start = new Date(today);
            start.setDate(start.getDate() - 6);
            break;
        case '30days':
        default:
            end = new Date(today);
            start = new Date(today);
            start.setDate(start.getDate() - 29);
            break;
    }
    return { startDate: toDateStr(start), endDate: toDateStr(end) };
}

// ========== Filter Bar Events ==========
function initFilterBar() {
    const rangeSelect = document.getElementById('filter-range-select');
    const startInput = document.getElementById('filter-start-date');
    const endInput = document.getElementById('filter-end-date');
    const modal = document.getElementById('custom-date-modal');
    const btnCancel = document.getElementById('btn-cancel-date');
    const btnApply = document.getElementById('btn-apply-date');
    const rangeText = document.getElementById('range-text');

    // Set default range (30 days)
    const defaultRange = getDateRange('30days');
    startInput.value = defaultRange.startDate;
    endInput.value = defaultRange.endDate;
    currentStartDate = defaultRange.startDate;
    currentEndDate = defaultRange.endDate;

    let previousSelectValue = '30days';

    rangeSelect.addEventListener('change', (e) => {
        const val = e.target.value;
        if (val === 'custom') {
            modal.showModal();
        } else {
            const range = getDateRange(val);
            startInput.value = range.startDate;
            endInput.value = range.endDate;
            currentStartDate = range.startDate;
            currentEndDate = range.endDate;
            previousSelectValue = val;
            currentLabel = e.target.options[e.target.selectedIndex].text;
            rangeText.textContent = currentLabel;
            loadStatistics();
        }
    });

    btnCancel.addEventListener('click', () => {
        modal.close();
        rangeSelect.value = previousSelectValue;
    });

    btnApply.addEventListener('click', () => {
        if (startInput.value && endInput.value) {
            currentStartDate = startInput.value;
            currentEndDate = endInput.value;
            previousSelectValue = 'custom';
            currentLabel = `Từ ${startInput.value} đến ${endInput.value}`;
            rangeText.textContent = currentLabel;
            modal.close();
            loadStatistics();
        } else {
            alert('Vui lòng chọn đầy đủ ngày bắt đầu và ngày kết thúc');
        }
    });
}

// ========== Load Statistics ==========
async function loadStatistics() {
    try {
        const url = `${API_BASE_URL}/statistics/overview?startDate=${currentStartDate}&endDate=${currentEndDate}`;
        const response = await fetch(url);
        const result = await response.json();

        if (result.status === 'success') {
            const data = result.data;
            lastLoadedData = data;
            renderSummary(data.summary);
            renderTrendChart(data.trend_chart);
            renderTopProducts(data.top_products);
            renderCategoryDoughnut(data.doanh_thu_theo_danh_muc);
            updateSubtitle();
        } else {
            showError();
        }
    } catch (error) {
        console.error('Error loading statistics:', error);
        showError();
    }
}

function updateSubtitle() {
    const now = new Date();
    const subtitle = document.getElementById('page-subtitle');
    if (subtitle) {
        subtitle.textContent =
            `${currentLabel} | Cập nhật lúc ${now.getHours()}:${String(now.getMinutes()).padStart(2, '0')}`;
    }
}

function showError() {
    const subtitle = document.getElementById('page-subtitle');
    if (subtitle) subtitle.textContent = 'Không thể kết nối đến server';
}

// ========== Export to CSV ==========
document.getElementById('btn-export-excel').addEventListener('click', () => {
    if (!lastLoadedData) {
        alert('Dữ liệu chưa sẵn sàng');
        return;
    }
    const sum = lastLoadedData.summary;
    let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
    csvContent += `BÁO CÁO THỐNG KÊ BÁN HÀNG\n`;
    csvContent += `Thời gian,${currentLabel}\n\n`;
    
    csvContent += `CHỈ SỐ TỔNG QUAN\n`;
    csvContent += `Chỉ số,Giá trị\n`;
    csvContent += `Doanh thu,${sum.doanh_thu}\n`;
    csvContent += `Đơn hàng,${sum.so_don}\n`;
    csvContent += `Sản phẩm đã bán,${sum.da_ban}\n`;
    csvContent += `Khách hàng mới,${sum.khach_moi}\n`;
    csvContent += `Giá trị TB đơn,${sum.aov}\n`;
    csvContent += `Tỷ lệ Hủy/Hoàn (%),${sum.ty_le_huy}\n\n`;

    csvContent += `TOP SẢN PHẨM BÁN CHẠY\n`;
    csvContent += `Tên sản phẩm,Đã bán,Doanh thu\n`;
    lastLoadedData.top_products.forEach(p => {
        csvContent += `"${p.ten_san_pham.replace(/"/g, '""')}",${p.da_ban},${p.doanh_thu}\n`;
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `baocao_thongke_${currentStartDate}_${currentEndDate}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
});

// ========== Render Summary Cards ==========
function renderSummary(summary) {
    // Revenue
    document.getElementById('val-doanh-thu').textContent = formatCurrency(summary.doanh_thu);
    renderChange('change-doanh-thu', summary.doanh_thu_percent);

    // Orders
    document.getElementById('val-don-hang').textContent = new Intl.NumberFormat('vi-VN').format(summary.so_don);
    renderChange('change-don-hang', summary.so_don_percent);

    // Products sold
    document.getElementById('val-da-ban').textContent = new Intl.NumberFormat('vi-VN').format(summary.da_ban);
    renderChange('change-da-ban', summary.da_ban_percent);

    // New customers
    document.getElementById('val-khach-moi').textContent = new Intl.NumberFormat('vi-VN').format(summary.khach_moi);
    renderChange('change-khach-moi', summary.khach_moi_percent);

    // AOV
    document.getElementById('val-aov').textContent = formatCurrency(summary.aov);
    renderChange('change-aov', summary.aov_percent);

    // Cancel Rate
    document.getElementById('val-huy').textContent = summary.ty_le_huy + '%';
    renderChange('change-huy', summary.ty_le_huy_percent);
}

function formatCurrency(amount) {
    if (amount >= 1000000000) return (amount / 1000000000).toFixed(1) + ' tỷ';
    if (amount >= 1000000) return (amount / 1000000).toFixed(1) + 'tr';
    if (amount >= 1000) return Math.round(amount / 1000) + 'k';
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

function formatCurrencyFull(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

function renderChange(elementId, percent) {
    const el = document.getElementById(elementId);
    if (!el) return;
    const isUp = percent >= 0;
    const icon = isUp ? 'trending-up' : 'trending-down';
    const cls = isUp ? 'summary-change--up' : 'summary-change--down';
    el.className = `summary-change ${cls}`;
    el.innerHTML = `
        <i data-lucide="${icon}" class="icon-xs"></i>
        <span>${Math.abs(percent)}% so với trước</span>
    `;
    lucide.createIcons();
}

// ========== Render Trend Chart ==========
function renderTrendChart(trendData) {
    const ctx = document.getElementById('trendChart').getContext('2d');
    const labels = trendData.map(d => {
        const date = new Date(d.ngay + 'T00:00:00');
        return `${date.getDate()}/${date.getMonth() + 1}`;
    });
    const revenueData = trendData.map(d => d.doanh_thu);
    const ordersData = trendData.map(d => d.so_don);

    // ---- Insight badges ----
    const maxRevenue = Math.max(...revenueData);
    const maxRevDate = labels[revenueData.indexOf(maxRevenue)];
    const totalRevenue = revenueData.reduce((a, b) => a + b, 0);
    const avgOrders = ordersData.length ? (ordersData.reduce((a, b) => a + b, 0) / ordersData.length).toFixed(1) : 0;
    const badgesEl = document.getElementById('chart-insight-badges');
    if (badgesEl) {
        badgesEl.innerHTML = `
            <div class="insight-badge">
                <span class="insight-badge-label">Doanh thu cao nhất</span>
                <span class="insight-badge-value" style="color:#7c5cfc;">${formatCurrency(maxRevenue)}</span>
                <span class="insight-badge-label" style="margin-top:1px;">ngày ${maxRevDate}</span>
            </div>
            <div class="insight-badge">
                <span class="insight-badge-label">Tổng kỳ</span>
                <span class="insight-badge-value" style="color:#7c5cfc;">${formatCurrency(totalRevenue)}</span>
                <span class="insight-badge-label" style="margin-top:1px;">doanh thu</span>
            </div>
            <div class="insight-badge">
                <span class="insight-badge-label">TB đơn/ngày</span>
                <span class="insight-badge-value" style="color:#f472b6;">${avgOrders}</span>
                <span class="insight-badge-label" style="margin-top:1px;">đơn hàng</span>
            </div>
        `;
    }

    if (trendChartInstance) trendChartInstance.destroy();

    trendChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Doanh thu (đ)',
                    data: revenueData,
                    borderColor: '#7c5cfc',
                    backgroundColor: 'rgba(124, 92, 252, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#7c5cfc',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    yAxisID: 'y',
                },
                {
                    label: 'Số đơn hàng',
                    data: ordersData,
                    borderColor: '#f472b6',
                    backgroundColor: 'rgba(244, 114, 182, 0.06)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#f472b6',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                    yAxisID: 'y1',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    titleColor: '#e2e8f0',
                    titleFont: { family: 'Inter', size: 12, weight: '600' },
                    bodyFont: { family: 'Inter', size: 12 },
                    footerFont: { family: 'Inter', size: 10 },
                    padding: 14,
                    cornerRadius: 10,
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    callbacks: {
                        title: function(items) {
                            return 'Ngày ' + items[0].label;
                        },
                        label: function(ctx) {
                            if (ctx.datasetIndex === 0) {
                                return '  💰 Doanh thu: ' + formatCurrencyFull(ctx.parsed.y);
                            }
                            return '  🛒 Đơn hàng: ' + ctx.parsed.y + ' đơn';
                        },
                        footer: function(items) {
                            const rev = items[0]?.parsed.y || 0;
                            const ord = items[1]?.parsed.y || 0;
                            if (ord > 0) {
                                return '  Trung bình/đơn: ' + formatCurrencyFull(Math.round(rev / ord));
                            }
                            return '';
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 11, weight: '500' },
                        color: '#9ca3af',
                        maxTicksLimit: 12,
                    },
                    border: { display: false },
                    title: {
                        display: false,
                    },
                },
                y: {
                    position: 'left',
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: '#7c5cfc',
                        callback: v => formatCurrency(v),
                    },
                    border: { display: false },
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Doanh thu (đ)',
                        color: '#7c5cfc',
                        font: { family: 'Inter', size: 10, weight: '600' },
                        padding: { bottom: 4 },
                    },
                },
                y1: {
                    position: 'right',
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Inter', size: 11 },
                        color: '#f472b6',
                        stepSize: 1,
                        callback: v => v + ' đơn',
                    },
                    border: { display: false },
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Số đơn hàng',
                        color: '#f472b6',
                        font: { family: 'Inter', size: 10, weight: '600' },
                        padding: { bottom: 4 },
                    },
                }
            }
        }
    });

    // Legend toggle
    initChartLegendToggles();
    lucide.createIcons();
}

function initChartLegendToggles() {
    const toggles = document.querySelectorAll('.chart-legend-item');
    toggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            if (!trendChartInstance) return;
            const dataset = toggle.dataset.dataset;
            const idx = dataset === 'revenue' ? 0 : 1;
            const meta = trendChartInstance.getDatasetMeta(idx);
            meta.hidden = !meta.hidden;
            toggle.classList.toggle('active', !meta.hidden);
            trendChartInstance.update();
        });
    });
}

// ========== Render Top Products ==========
function renderTopProducts(products) {
    const tbody = document.getElementById('top-products-tbody');
    if (!products || products.length === 0) {
        tbody.innerHTML = `
            <tr><td colspan="4">
                <div class="stats-empty-state">
                    <i data-lucide="package-x" style="width:32px;height:32px;"></i>
                    <p>Chưa có dữ liệu sản phẩm</p>
                </div>
            </td></tr>`;
        lucide.createIcons();
        return;
    }

    tbody.innerHTML = products.map((p, idx) => {
        const rankClass = idx < 3 ? ` rank-${idx + 1}` : '';
        const imgSrc = p.anh_san_pham
            ? (p.anh_san_pham.startsWith('http') ? p.anh_san_pham : `http://localhost:3000${p.anh_san_pham}`)
            : '/images/placeholder.png';
        return `
            <tr>
                <td><span class="product-rank${rankClass}">${idx + 1}</span></td>
                <td>
                    <div class="top-product-info">
                        <img class="top-product-img" src="${imgSrc}" alt="${escapeHtml(p.ten_san_pham)}" onerror="this.src='/images/placeholder.png'">
                        <span class="top-product-name" title="${escapeHtml(p.ten_san_pham)}">${escapeHtml(p.ten_san_pham)}</span>
                    </div>
                </td>
                <td>${new Intl.NumberFormat('vi-VN').format(Number(p.da_ban))}</td>
                <td style="font-weight: 600;">${formatCurrency(Number(p.doanh_thu))}</td>
            </tr>
        `;
    }).join('');
}

// ========== Render Category Doughnut ==========
function renderCategoryDoughnut(categories) {
    const ctx = document.getElementById('categoryDoughnutChart').getContext('2d');
    const legendEl = document.getElementById('category-legend');
    const chartColors = ['#7c5cfc', '#f472b6', '#34d399', '#fbbf24', '#ef4444', '#8b5cf6', '#06b6d4', '#f97316'];

    if (!categories || categories.length === 0) {
        legendEl.innerHTML = '<p style="color: #9ca3af; text-align: center;">Chưa có dữ liệu</p>';
        const centerEl = document.getElementById('doughnut-center-value');
        if (centerEl) centerEl.textContent = '0đ';
        if (categoryChartInstance) { categoryChartInstance.destroy(); categoryChartInstance = null; }
        return;
    }

    const labels = categories.map(c => c.ten_danh_muc);
    const data = categories.map(c => c.tong_doanh_thu);
    const colors = categories.map((_, i) => chartColors[i % chartColors.length]);
    const totalDT = data.reduce((a, b) => a + b, 0);

    // Update center label
    const centerEl = document.getElementById('doughnut-center-value');
    if (centerEl) centerEl.textContent = formatCurrency(totalDT);

    // Update total badge
    const badgeEl = document.getElementById('category-total-badge');
    if (badgeEl) badgeEl.textContent = categories.length + ' danh mục';

    if (categoryChartInstance) categoryChartInstance.destroy();

    categoryChartInstance = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels,
            datasets: [{
                data,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '62%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e1b4b',
                    titleColor: '#e2e8f0',
                    titleFont: { family: 'Inter', size: 12, weight: '600' },
                    bodyFont: { family: 'Inter', size: 12 },
                    footerFont: { family: 'Inter', size: 10, style: 'italic' },
                    footerColor: '#94a3b8',
                    padding: 14,
                    cornerRadius: 10,
                    borderColor: 'rgba(255,255,255,0.1)',
                    borderWidth: 1,
                    callbacks: {
                        title: function(items) {
                            return items[0].label;
                        },
                        label: function(ctx) {
                            const pct = totalDT > 0 ? ((ctx.raw / totalDT) * 100).toFixed(1) : 0;
                            return `  Doanh thu: ${formatCurrencyFull(ctx.raw)}`;
                        },
                        footer: function(items) {
                            const pct = totalDT > 0 ? ((items[0].raw / totalDT) * 100).toFixed(1) : 0;
                            return `  Tỷ lệ: ${pct}% tổng doanh thu`;
                        }
                    }
                }
            }
        }
    });

    // Render legend - show both % and revenue
    legendEl.innerHTML = categories.map((cat, i) => `
        <div class="category-legend-item" style="cursor: default;" title="${escapeHtml(cat.ten_danh_muc)}: ${formatCurrencyFull(Number(cat.tong_doanh_thu))}">
            <span class="category-legend-dot" style="background: ${colors[i]};"></span>
            <span class="category-legend-label">${escapeHtml(cat.ten_danh_muc)}</span>
            <div style="display:flex;flex-direction:column;align-items:flex-end;">
                <span class="category-legend-value" style="color:${colors[i]};">${cat.phan_tram}%</span>
                <span class="category-legend-revenue">${formatCurrency(Number(cat.tong_doanh_thu))}</span>
            </div>
        </div>
    `).join('');
}

// ========== Utilities ==========
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ========== Init ==========
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    initFilterBar();
    loadStatistics();
});
</script>
@endsection
