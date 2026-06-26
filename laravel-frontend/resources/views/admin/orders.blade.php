@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')
@section('page-title', 'Quản lý đơn hàng')
@section('page-subtitle', 'Đang tải...')

@section('content')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-orders.css') }}">
@endsection
<!-- Action Bar -->
<div class="page-action-bar" id="orders-action-bar">
    <div class="action-bar-left">
        <!-- Status Tabs -->
        <div class="status-tabs" id="order-status-tabs">
            <button class="status-tab active" data-status="all" id="tab-all">
                <span>Tất cả</span>
                <span class="tab-count" id="count-all">0</span>
            </button>
            <button class="status-tab" data-status="cho_xac_nhan" id="tab-pending">
                <span>Chờ xác nhận</span>
                <span class="tab-count" id="count-cho_xac_nhan">0</span>
            </button>
            <button class="status-tab" data-status="da_xac_nhan" id="tab-confirmed">
                <span>Đã xác nhận</span>
                <span class="tab-count" id="count-da_xac_nhan">0</span>
            </button>
            <button class="status-tab" data-status="dang_giao" id="tab-shipping">
                <span>Đang giao</span>
                <span class="tab-count" id="count-dang_giao">0</span>
            </button>
            <button class="status-tab" data-status="giao_thanh_cong" id="tab-completed">
                <span>Hoàn thành</span>
                <span class="tab-count" id="count-giao_thanh_cong">0</span>
            </button>
            <button class="status-tab" data-status="da_huy" id="tab-cancelled">
                <span>Đã hủy</span>
                <span class="tab-count" id="count-da_huy">0</span>
            </button>
            <button class="status-tab" data-status="dang_tra_hang" id="tab-returning">
                <span>Yêu cầu trả</span>
                <span class="tab-count" id="count-dang_tra_hang">0</span>
            </button>
            <button class="status-tab" data-status="da_tra_hang" id="tab-returned">
                <span>Đã trả hàng</span>
                <span class="tab-count" id="count-da_tra_hang">0</span>
            </button>
        </div>
    </div>
    <div class="action-bar-right">
        <div class="search-box" id="search-orders">
            <i data-lucide="search" class="icon-xs search-icon"></i>
            <input type="text" placeholder="Mã đơn, tên khách..." class="search-input search-input--sm" id="order-search-input">
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="data-card" id="orders-table-card">
    <div class="table-wrapper">
        <table class="admin-table" id="orders-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="orders-tbody">
                <tr class="loading-row">
                    <td colspan="7" style="text-align: center; padding: 20px;">
                        <span>Đang tải dữ liệu...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper" id="orders-pagination"></div>
</div>

<!-- Modal Chi Tiết Đơn Hàng -->
<div class="modal" id="modal-order-detail" style="display: none;">
    <div class="modal-content modal-content--lg">
        <div class="modal-header">
            <h2 id="modal-order-title">Chi tiết đơn hàng</h2>
            <button class="modal-close" id="btn-close-detail">&times;</button>
        </div>
        <div class="order-detail-body" id="order-detail-body">
            <p>Đang tải...</p>
        </div>
    </div>
</div>

<!-- Modal Overlay -->
<div class="modal-overlay" id="modal-overlay" style="display: none;"></div>

@endsection

@section('scripts')
<script>
const API_BASE_URL = 'http://localhost:3000/api';
let allOrders = [];
let currentStatus = 'all';
let currentPage = 1;
let totalPages = 1;
let searchTimeout = null;

// ========== Load Orders ==========
async function loadOrders(search = '', status = 'all', page = 1) {
    try {
        let url = `${API_BASE_URL}/orders?per_page=15&page=${page}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status !== 'all') url += `&trang_thai=${status}`;

        const response = await fetch(url);
        const result = await response.json();

        if (result.status === 'success') {
            allOrders = result.data;
            currentPage = result.pagination.current_page;
            totalPages = result.pagination.last_page;
            renderTable(allOrders);
            renderPagination(result.pagination);
        } else {
            showError('Lỗi khi tải đơn hàng');
        }
    } catch (error) {
        console.error('Error loading orders:', error);
        showError('Không thể kết nối đến server');
    }
}

// ========== Load Stats ==========
async function loadStats() {
    try {
        const response = await fetch(`${API_BASE_URL}/orders/stats`);
        const result = await response.json();

        if (result.status === 'success') {
            const s = result.data;
            document.getElementById('count-all').textContent = s.total;
            document.getElementById('count-cho_xac_nhan').textContent = s.cho_xac_nhan;
            document.getElementById('count-da_xac_nhan').textContent = s.da_xac_nhan;
            document.getElementById('count-dang_giao').textContent = s.dang_giao;
            document.getElementById('count-giao_thanh_cong').textContent = s.giao_thanh_cong;
            document.getElementById('count-da_huy').textContent = s.da_huy;
            document.getElementById('count-dang_tra_hang').textContent = s.dang_tra_hang || 0;
            document.getElementById('count-da_tra_hang').textContent = s.da_tra_hang || 0;

            const summaryText = `${s.total} đơn · tháng ${new Date().getMonth() + 1}/${new Date().getFullYear()}`;
            const pageSubtitle = document.getElementById('page-subtitle');
            if (pageSubtitle) {
                pageSubtitle.textContent = summaryText;
            }
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// ========== Render Table ==========
function renderTable(orders) {
    const tbody = document.getElementById('orders-tbody');

    if (orders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 30px; color: #9ca3af;">Không có đơn hàng nào</td></tr>';
        return;
    }

    tbody.innerHTML = orders.map(order => {
        const statusInfo = getStatusInfo(order.trang_thai_don);
        const isDefaultName = !order.ho_ten_nguoi_nhan || order.ho_ten_nguoi_nhan === 'Khách hàng';
        const customerName = isDefaultName && order.nguoi_dung ? order.nguoi_dung.ho_ten : (order.ho_ten_nguoi_nhan || 'N/A');
        const totalFormatted = formatCurrency(order.tong_thanh_toan);
        const dateFormatted = formatDate(order.ngay_dat);
        const soSanPham = order.so_san_pham || 0;
        const actionButtons = getActionButtons(order);

        return `
            <tr>
                <td><span class="order-id-link" onclick="viewOrderDetail(${order.ma_don_hang})">#DH${String(order.ma_don_hang).padStart(4, '0')}</span></td>
                <td><span class="text-bold">${escapeHtml(customerName)}</span></td>
                <td><span class="text-secondary">${soSanPham} sản phẩm</span></td>
                <td><span class="text-bold">${totalFormatted}</span></td>
                <td>
                    <select class="status-select" onchange="updateStatus(${order.ma_don_hang}, this, '${order.trang_thai_don}')" style="padding: 6px 10px; border-radius: 6px; border: 1px solid #e5e7eb; background: #f9fafb; font-size: 13px; cursor: pointer; outline: none; font-weight: 500; color: #374151;">
                        <option value="cho_xac_nhan" ${order.trang_thai_don === 'cho_xac_nhan' ? 'selected' : ''}>Chờ xác nhận</option>
                        <option value="da_xac_nhan" ${order.trang_thai_don === 'da_xac_nhan' ? 'selected' : ''}>Đã xác nhận</option>
                        <option value="dang_giao" ${order.trang_thai_don === 'dang_giao' ? 'selected' : ''}>Đang giao</option>
                        <option value="giao_thanh_cong" ${order.trang_thai_don === 'giao_thanh_cong' ? 'selected' : ''}>Giao thành công</option>
                        <option value="hoan_thanh" ${order.trang_thai_don === 'hoan_thanh' ? 'selected' : ''}>Hoàn thành</option>
                        <option value="da_huy" ${order.trang_thai_don === 'da_huy' ? 'selected' : ''}>Đã hủy</option>
                        <option value="dang_tra_hang" ${order.trang_thai_don === 'dang_tra_hang' ? 'selected' : ''}>Yêu cầu trả</option>
                        <option value="da_tra_hang" ${order.trang_thai_don === 'da_tra_hang' ? 'selected' : ''}>Đã trả hàng</option>
                    </select>
                </td>
                <td><span class="text-secondary">${dateFormatted}</span></td>
                <td>
                    <div class="action-btns">
                        <button class="icon-action-btn" title="Xem chi tiết" onclick="viewOrderDetail(${order.ma_don_hang})">
                            <i data-lucide="eye" class="icon-xs"></i>
                        </button>
                        ${actionButtons}
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    lucide.createIcons();
}

// ========== Get Action Buttons ==========
function getActionButtons(order) {
    const status = order.trang_thai_don;
    let buttons = '';

    if (status === 'dang_tra_hang' && order.yeu_cau_tra_hang) {
        buttons += `<button class="icon-action-btn icon-action-btn--success" title="Phê duyệt trả hàng" onclick="handleReturn(${order.yeu_cau_tra_hang.ma_yeu_cau}, 'da_duyet')"><i data-lucide="check" class="icon-xs"></i></button>`;
        buttons += `<button class="icon-action-btn icon-action-btn--danger" title="Từ chối trả hàng" onclick="handleReturn(${order.yeu_cau_tra_hang.ma_yeu_cau}, 'tu_choi')"><i data-lucide="x" class="icon-xs"></i></button>`;
    }

    return buttons;
}

// ========== Update Order Status ==========
async function updateStatus(orderId, selectElement, oldStatus) {
    const newStatus = selectElement.value;
    
    // Tìm label của newStatus để hiển thị confirm
    const statusOptions = {
        'cho_xac_nhan': 'Chờ xác nhận',
        'da_xac_nhan': 'Đã xác nhận',
        'dang_giao': 'Đang giao',
        'giao_thanh_cong': 'Giao thành công',
        'hoan_thanh': 'Hoàn thành',
        'da_huy': 'Đã hủy'
    };
    const label = statusOptions[newStatus] || newStatus;

    const confirmResult = await window.showCustomDialog({
        title: 'Cập nhật trạng thái',
        message: `Bạn chắc chắn muốn cập nhật trạng thái đơn hàng thành: ${label}?`,
        isPrompt: false
    });
    if (!confirmResult) {
        selectElement.value = oldStatus; // Revert nếu cancel
        return;
    }

    try {
        const response = await fetch(`${API_BASE_URL}/orders/${orderId}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ trang_thai_don: newStatus }),
        });
        const result = await response.json();

        if (result.status === 'success') {
            showAlert(result.message || 'Cập nhật thành công', 'success');
            loadOrders(document.getElementById('order-search-input').value, currentStatus, currentPage);
            loadStats();
        } else {
            showAlert(result.message || 'Lỗi khi cập nhật', 'error');
            selectElement.value = oldStatus; // Revert
        }
    } catch (error) {
        console.error('Error updating status:', error);
        showAlert('Lỗi khi kết nối đến server', 'error');
        selectElement.value = oldStatus; // Revert
    }
}

// ========== Handle Return Request ==========
async function handleReturn(returnId, status) {
    const label = status === 'da_duyet' ? 'PHÊ DUYỆT' : 'TỪ CHỐI';
    const confirmResult = await window.showCustomDialog({
        title: 'Xử lý yêu cầu trả hàng',
        message: `Bạn chắc chắn muốn ${label} yêu cầu trả hàng này?`,
        isPrompt: false
    });
    if (!confirmResult) return;

    try {
        const response = await fetch(`${API_BASE_URL}/returns/${returnId}/status`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ trang_thai: status }),
        });
        const result = await response.json();

        if (result.status === 'success') {
            showAlert(result.message || 'Xử lý thành công', 'success');
            loadOrders(document.getElementById('order-search-input').value, currentStatus, currentPage);
            loadStats();
        } else {
            showAlert(result.message || 'Lỗi khi xử lý', 'error');
        }
    } catch (error) {
        console.error('Error handling return:', error);
        showAlert('Lỗi khi kết nối đến server', 'error');
    }
}

// ========== View Order Detail ==========
async function viewOrderDetail(orderId) {
    try {
        document.getElementById('modal-order-detail').style.display = 'block';
        document.getElementById('modal-overlay').style.display = 'block';
        document.getElementById('order-detail-body').innerHTML = '<p style="text-align: center; padding: 20px;">Đang tải...</p>';

        const response = await fetch(`${API_BASE_URL}/orders/${orderId}`);
        const result = await response.json();

        if (result.status === 'success') {
            const order = result.data;
            const statusInfo = getStatusInfo(order.trang_thai_don);
            const isDefaultName = !order.ho_ten_nguoi_nhan || order.ho_ten_nguoi_nhan === 'Khách hàng';
            const customerName = isDefaultName && order.nguoi_dung ? order.nguoi_dung.ho_ten : (order.ho_ten_nguoi_nhan || 'N/A');
            const customerEmail = order.nguoi_dung ? order.nguoi_dung.email : '';
            const customerPhone = order.so_dien_thoai_nhan || (order.nguoi_dung ? order.nguoi_dung.so_dien_thoai : '');

            document.getElementById('modal-order-title').textContent =
                `Đơn hàng #DH${String(order.ma_don_hang).padStart(4, '0')}`;

            let detailsHTML = `
                <div class="order-info-grid">
                    <div class="order-info-section">
                        <h4>Thông tin khách hàng</h4>
                        <p><strong>Tên:</strong> ${escapeHtml(customerName)}</p>
                        ${customerEmail ? `<p><strong>Email:</strong> ${escapeHtml(customerEmail)}</p>` : ''}
                        ${customerPhone ? `<p><strong>SĐT:</strong> ${escapeHtml(customerPhone)}</p>` : ''}
                        ${order.dia_chi_giao ? `<p><strong>Địa chỉ:</strong> ${escapeHtml(order.dia_chi_giao)}</p>` : ''}
                    </div>
                    <div class="order-info-section">
                        <h4>Thông tin đơn hàng</h4>
                        <p><strong>Trạng thái:</strong> <span class="status-badge ${statusInfo.class}">${statusInfo.label}</span></p>
                        <p><strong>Ngày đặt:</strong> ${formatDate(order.ngay_dat)}</p>
                        <p><strong>Tổng tiền:</strong> <span class="text-bold" style="color: #7c5cfc;">${formatCurrency(order.tong_thanh_toan)}</span></p>
                        ${order.ghi_chu ? `<p><strong>Ghi chú:</strong> ${escapeHtml(order.ghi_chu)}</p>` : ''}
                    </div>
                </div>
            `;

            if (order.yeu_cau_tra_hang) {
                const req = order.yeu_cau_tra_hang;
                let imagesList = [];
                try {
                    imagesList = Array.isArray(req.hinh_anh_bang_chung) ? req.hinh_anh_bang_chung : JSON.parse(req.hinh_anh_bang_chung || '[]');
                } catch (e) {
                    console.error('Lỗi parse hình ảnh:', e);
                }

                const imagesHtml = imagesList.length > 0 ? 
                    imagesList.map(url => `<a href="${url}" target="_blank"><img src="${url}" style="width:60px; height:60px; object-fit:cover; margin-right:5px; border-radius:4px; border:1px solid #ddd;"></a>`).join('')
                    : 'Không có hình ảnh';

                detailsHTML += `
                    <h4 style="margin-top: 20px; margin-bottom: 10px; color: #e11d48;"><i data-lucide="alert-triangle" class="icon-xs"></i> Thông tin Hoàn trả</h4>
                    <div class="order-info-section" style="background-color: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 15px;">
                        <p><strong>Lý do:</strong> ${escapeHtml(req.ly_do)}</p>
                        ${req.ghi_chu_khach_hang ? `<p><strong>Ghi chú của khách:</strong> ${escapeHtml(req.ghi_chu_khach_hang)}</p>` : ''}
                        <p><strong>Bằng chứng:</strong></p>
                        <div style="margin-top: 5px;">${imagesHtml}</div>
                    </div>
                `;
            }

            if (order.chi_tiet && order.chi_tiet.length > 0) {
                detailsHTML += `
                    <h4 style="margin-top: 20px; margin-bottom: 10px;">Sản phẩm (${order.chi_tiet.length})</h4>
                    <table class="admin-table detail-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>SL</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${order.chi_tiet.map(item => `
                                <tr>
                                    <td>${escapeHtml(item.ten_san_pham)}</td>
                                    <td>${formatCurrency(item.don_gia)}</td>
                                    <td>${item.so_luong}</td>
                                    <td class="text-bold">${formatCurrency(item.thanh_tien)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right; font-weight: 600;">Tổng cộng:</td>
                                <td class="text-bold" style="color: #7c5cfc;">${formatCurrency(order.tong_thanh_toan)}</td>
                            </tr>
                        </tfoot>
                    </table>
                `;
            }

            document.getElementById('order-detail-body').innerHTML = detailsHTML;
        } else {
            document.getElementById('order-detail-body').innerHTML = '<p style="color: red;">Lỗi khi tải chi tiết đơn hàng</p>';
        }
    } catch (error) {
        console.error('Error loading order detail:', error);
        document.getElementById('order-detail-body').innerHTML = '<p style="color: red;">Không thể kết nối đến server</p>';
    }
}

// ========== Pagination ==========
function renderPagination(pagination) {
    const wrapper = document.getElementById('orders-pagination');
    if (pagination.last_page <= 1) {
        wrapper.innerHTML = '';
        return;
    }

    let html = '<div class="pagination">';
    html += `<button class="page-btn" ${pagination.current_page <= 1 ? 'disabled' : ''} onclick="goToPage(${pagination.current_page - 1})">‹</button>`;

    for (let i = 1; i <= pagination.last_page; i++) {
        if (i === pagination.current_page) {
            html += `<button class="page-btn page-btn--active">${i}</button>`;
        } else if (i <= 3 || i > pagination.last_page - 2 || Math.abs(i - pagination.current_page) <= 1) {
            html += `<button class="page-btn" onclick="goToPage(${i})">${i}</button>`;
        } else if (i === 4 || i === pagination.last_page - 2) {
            html += '<span class="page-dots">...</span>';
        }
    }

    html += `<button class="page-btn" ${pagination.current_page >= pagination.last_page ? 'disabled' : ''} onclick="goToPage(${pagination.current_page + 1})">›</button>`;
    html += '</div>';

    wrapper.innerHTML = html;
}

function goToPage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    loadOrders(document.getElementById('order-search-input').value, currentStatus, page);
}

// ========== Helpers ==========
function getStatusInfo(status) {
    const map = {
        'cho_xac_nhan': { label: 'Chờ xác nhận', class: 'status-badge--warning' },
        'da_xac_nhan': { label: 'Đã xác nhận', class: 'status-badge--info' },
        'dang_giao': { label: 'Đang giao', class: 'status-badge--info' },
        'giao_thanh_cong': { label: 'Giao thành công', class: 'status-badge--success' },
        'hoan_thanh': { label: 'Hoàn thành', class: 'status-badge--success' },
        'da_huy': { label: 'Đã hủy', class: 'status-badge--danger' },
        'dang_tra_hang': { label: 'Yêu cầu trả', class: 'status-badge--warning' },
        'da_tra_hang': { label: 'Đã trả hàng', class: 'status-badge--secondary' },
    };
    return map[status] || { label: status, class: '' };
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + 'đ';
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(msg) {
    const tbody = document.getElementById('orders-tbody');
    tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 30px; color: #ef4444;">${msg}</td></tr>`;
}

function closeModal() {
    document.getElementById('modal-order-detail').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
}

// ========== Event Listeners ==========

// Status tabs
document.querySelectorAll('.status-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        currentStatus = this.getAttribute('data-status');
        currentPage = 1;
        loadOrders(document.getElementById('order-search-input').value, currentStatus, 1);
    });
});

// Search with debounce
document.getElementById('order-search-input').addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage = 1;
        loadOrders(e.target.value, currentStatus, 1);
    }, 300);
});

// Modal close
document.getElementById('btn-close-detail').addEventListener('click', closeModal);
document.getElementById('modal-overlay').addEventListener('click', closeModal);

// Initial load
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadOrders();
    loadStats();
});
</script>

@endsection
