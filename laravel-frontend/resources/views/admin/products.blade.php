@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Quản lý sản phẩm')
@section('page-subtitle', 'Đang tải...')

@section('content')
<!-- Action Bar -->
<div class="page-action-bar" id="products-action-bar">
    <div class="action-bar-left">
        <button class="btn btn-primary" id="btn-add-product">
            <i data-lucide="plus" class="icon-xs"></i>
            <span>Thêm sản phẩm</span>
        </button>
        <div class="search-box" id="search-products">
            <i data-lucide="search" class="icon-xs search-icon"></i>
            <input type="text" placeholder="Tìm kiếm sản phẩm..." class="search-input" id="product-search-input">
        </div>
    </div>
    <div class="action-bar-right">
        <!-- Status Filter Tabs -->
        <div class="role-tabs" id="product-status-tabs">
            <button class="role-tab active" data-status="all">Tất cả</button>
            <button class="role-tab" data-status="dang_ban">Đang bán</button>
            <button class="role-tab" data-status="ngung_ban">Ngừng bán</button>
            <button class="role-tab" data-status="het_hang">Hết hàng</button>
        </div>
    </div>
</div>

<!-- Products Table -->
<div class="data-card" id="products-table-card">
    <div class="table-wrapper">
        <table class="admin-table" id="products-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Kho</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="products-tbody">
                <tr class="loading-row">
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        <span>Đang tải dữ liệu...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-wrapper" id="pagination-wrapper" style="display: none;">
        <div class="pagination-info" id="pagination-info"></div>
        <div class="pagination-btns" id="pagination-btns"></div>
    </div>
</div>

<!-- Stats Cards -->
<div class="data-card" style="margin-top: 20px;">
    <h3 class="card-section-title">Thống kê sản phẩm</h3>
    <div class="stats-grid">
        <div class="stat-box">
            <span class="stat-label">Tổng sản phẩm</span>
            <span class="stat-value" id="stat-total">0</span>
        </div>
        <div class="stat-box">
            <span class="stat-label">Đang bán</span>
            <span class="stat-value" id="stat-dang-ban">0</span>
        </div>
        <div class="stat-box stat-box--warning">
            <span class="stat-label">Sắp hết hàng</span>
            <span class="stat-value" id="stat-sap-het">0</span>
        </div>
        <div class="stat-box stat-box--danger">
            <span class="stat-label">Hết hàng</span>
            <span class="stat-value" id="stat-het-hang">0</span>
        </div>
        <div class="stat-box stat-box--success">
            <span class="stat-label">Khuyến mãi</span>
            <span class="stat-value" id="stat-khuyen-mai">0</span>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Product -->
<div class="modal" id="modal-product" style="display: none;">
    <div class="modal-content modal-content--wide">
        <div class="modal-header">
            <h2 id="modal-title">Thêm sản phẩm</h2>
            <button class="modal-close" id="btn-close-modal">&times;</button>
        </div>
        <form id="form-product" class="form">
            <input type="hidden" id="product-id" value="">

            <div class="form-row">
                <div class="form-group form-group--flex">
                    <label for="ten_san_pham">Tên sản phẩm *</label>
                    <input type="text" id="ten_san_pham" name="ten_san_pham" required class="form-control">
                    <span class="error-message" id="error-ten_san_pham"></span>
                </div>
                <div class="form-group">
                    <label for="ma_danh_muc">Danh mục</label>
                    <select id="ma_danh_muc" name="ma_danh_muc" class="form-control">
                        <option value="">-- Chọn danh mục --</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="gia">Giá (VNĐ) *</label>
                    <input type="number" id="gia" name="gia" required class="form-control" min="0" step="1000">
                    <span class="error-message" id="error-gia"></span>
                </div>
                <div class="form-group">
                    <label for="gia_khuyen_mai">Giá khuyến mãi</label>
                    <input type="number" id="gia_khuyen_mai" name="gia_khuyen_mai" class="form-control" min="0" step="1000">
                    <span class="error-message" id="error-gia_khuyen_mai"></span>
                </div>
                <div class="form-group">
                    <label for="so_luong_ton">Số lượng tồn *</label>
                    <input type="number" id="so_luong_ton" name="so_luong_ton" class="form-control" min="0" value="0">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="thuong_hieu">Thương hiệu</label>
                    <input type="text" id="thuong_hieu" name="thuong_hieu" class="form-control">
                </div>
                <div class="form-group">
                    <label for="xuat_xu">Xuất xứ</label>
                    <input type="text" id="xuat_xu" name="xuat_xu" class="form-control">
                </div>
                <div class="form-group">
                    <label for="loai_da_phu_hop">Loại da phù hợp</label>
                    <input type="text" id="loai_da_phu_hop" name="loai_da_phu_hop" class="form-control" placeholder="VD: Da dầu, Da khô">
                </div>
            </div>

            <div class="form-group">
                <label for="mo_ta">Mô tả</label>
                <textarea id="mo_ta" name="mo_ta" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-row">
                <div class="form-group form-group--flex">
                    <label for="thanh_phan">Thành phần</label>
                    <textarea id="thanh_phan" name="thanh_phan" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group form-group--flex">
                    <label for="huong_dan_su_dung">Hướng dẫn sử dụng</label>
                    <textarea id="huong_dan_su_dung" name="huong_dan_su_dung" class="form-control" rows="2"></textarea>
                </div>
            </div>

            <div class="form-group">
                <label for="trang_thai_form">Trạng thái</label>
                <select id="trang_thai_form" name="trang_thai" class="form-control">
                    <option value="dang_ban">Đang bán</option>
                    <option value="ngung_ban">Ngừng bán</option>
                    <option value="het_hang">Hết hàng</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-cancel-modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Overlay -->
<div class="modal-overlay" id="modal-overlay" style="display: none;"></div>

@endsection

@section('scripts')
<script>
const API_BASE_URL = 'http://localhost:3000/api';
let allProducts = [];
let allCategories = [];
let currentFilter = 'all';
let currentEditId = null;
let currentPage = 1;
let totalPages = 1;
let searchTimeout = null;

// ========== LOAD DATA ==========

async function loadProducts(search = '', status = 'all', page = 1) {
    try {
        let url = `${API_BASE_URL}/products?per_page=15&page=${page}`;
        if (search) {
            url += `&search=${encodeURIComponent(search)}`;
        }
        if (status && status !== 'all') {
            url += `&trang_thai=${status}`;
        }

        const response = await fetch(url);
        const result = await response.json();

        if (result.status === 'success') {
            allProducts = result.data;
            currentPage = result.pagination.current_page;
            totalPages = result.pagination.last_page;
            renderTable();
            renderPagination(result.pagination);
            loadStats();
        }
    } catch (error) {
        console.error('Error loading products:', error);
        showAlert('Không thể kết nối đến server. Hãy đảm bảo backend đang chạy.', 'error');
    }
}

async function loadCategories() {
    try {
        const response = await fetch(`${API_BASE_URL}/categories?limit=100`);
        const result = await response.json();

        if (result.status === 'success') {
            allCategories = result.data;
            updateCategorySelect();
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE_URL}/products/stats`);
        const result = await response.json();

        if (result.status === 'success') {
            const s = result.data;
            document.getElementById('stat-total').textContent = s.total;
            document.getElementById('stat-dang-ban').textContent = s.dang_ban;
            document.getElementById('stat-sap-het').textContent = s.sap_het_hang;
            document.getElementById('stat-het-hang').textContent = s.het_hang;
            document.getElementById('stat-khuyen-mai').textContent = s.khuyen_mai;
            const subtitleEl = document.querySelector('.header-subtitle');
            if (subtitleEl) subtitleEl.textContent = `${s.total} sản phẩm · ${s.dang_ban} đang bán`;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// ========== RENDER ==========

function renderTable() {
    const tbody = document.getElementById('products-tbody');

    if (allProducts.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 20px;">Không có sản phẩm nào</td></tr>';
        return;
    }

    tbody.innerHTML = allProducts.map(product => {
        const categoryName = product.danh_muc ? product.danh_muc.ten_danh_muc : '--';
        const statusLabel = getStatusLabel(product.trang_thai);
        const statusClass = getStatusClass(product.trang_thai, product.so_luong_ton);
        const price = formatPrice(product.gia);
        const salePrice = product.gia_khuyen_mai ? formatPrice(product.gia_khuyen_mai) : '';
        const stockClass = product.so_luong_ton === 0 ? 'text-danger' : (product.so_luong_ton <= 30 ? 'text-warning' : '');

        return `
            <tr>
                <td>
                    <div class="product-name-cell">
                        <div class="product-thumb" style="background: linear-gradient(135deg, ${getProductColor(product.ma_san_pham)});"></div>
                        <div class="product-info">
                            <span class="product-name">${escapeHtml(product.ten_san_pham)}</span>
                            ${product.thuong_hieu ? `<span class="product-brand">${escapeHtml(product.thuong_hieu)}</span>` : ''}
                        </div>
                    </div>
                </td>
                <td><span class="text-secondary">${escapeHtml(categoryName)}</span></td>
                <td>
                    <div class="price-cell">
                        ${salePrice ? `<span class="price-original">${price}</span><span class="price-sale">${salePrice}</span>` : `<span class="text-bold">${price}</span>`}
                    </div>
                </td>
                <td><span class="text-bold ${stockClass}">${product.so_luong_ton}</span></td>
                <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                <td>
                    <div class="action-btns">
                        <button class="icon-action-btn" title="Sửa" onclick="editProduct(${product.ma_san_pham})">
                            <i data-lucide="pencil" class="icon-xs"></i>
                        </button>
                        <button class="icon-action-btn icon-action-btn--danger" title="Xóa" onclick="deleteProduct(${product.ma_san_pham})">
                            <i data-lucide="trash-2" class="icon-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    lucide.createIcons();
}

function renderPagination(pagination) {
    const wrapper = document.getElementById('pagination-wrapper');
    const info = document.getElementById('pagination-info');
    const btns = document.getElementById('pagination-btns');

    if (pagination.last_page <= 1) {
        wrapper.style.display = 'none';
        return;
    }

    wrapper.style.display = 'flex';
    info.textContent = `Trang ${pagination.current_page} / ${pagination.last_page} (${pagination.total} sản phẩm)`;

    let html = '';
    // Previous button
    html += `<button class="page-btn" ${pagination.current_page <= 1 ? 'disabled' : ''} onclick="goToPage(${pagination.current_page - 1})">‹</button>`;

    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        if (i === 1 || i === pagination.last_page || Math.abs(i - pagination.current_page) <= 2) {
            html += `<button class="page-btn ${i === pagination.current_page ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
        } else if (Math.abs(i - pagination.current_page) === 3) {
            html += `<span class="page-ellipsis">...</span>`;
        }
    }

    // Next button
    html += `<button class="page-btn" ${pagination.current_page >= pagination.last_page ? 'disabled' : ''} onclick="goToPage(${pagination.current_page + 1})">›</button>`;

    btns.innerHTML = html;
}

function goToPage(page) {
    if (page < 1 || page > totalPages) return;
    const search = document.getElementById('product-search-input').value;
    loadProducts(search, currentFilter, page);
}

// ========== HELPERS ==========

function formatPrice(price) {
    return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
}

function getStatusLabel(status) {
    const labels = {
        'dang_ban': 'Đang bán',
        'ngung_ban': 'Ngừng bán',
        'het_hang': 'Hết hàng',
    };
    return labels[status] || status;
}

function getStatusClass(status, stock) {
    if (status === 'ngung_ban') return 'status-badge--inactive';
    if (status === 'het_hang' || stock === 0) return 'status-badge--danger';
    if (stock <= 30) return 'status-badge--warning';
    return 'status-badge--active';
}

function getProductColor(id) {
    const colors = [
        '#e0e7ff, #c7d2fe', '#fce7f3, #fbcfe8', '#d1fae5, #a7f3d0',
        '#fef3c7, #fde68a', '#e0f2fe, #bae6fd', '#f3e8ff, #e9d5ff',
        '#fce4ec, #f8bbd0', '#e8eaf6, #c5cae9',
    ];
    return colors[id % colors.length];
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function updateCategorySelect() {
    const select = document.getElementById('ma_danh_muc');
    const options = '<option value="">-- Chọn danh mục --</option>' +
        allCategories.map(cat =>
            `<option value="${cat.ma_danh_muc}">${escapeHtml(cat.ten_danh_muc)}</option>`
        ).join('');
    select.innerHTML = options;
}

function showAlert(message, type = 'info') {
    alert(message);
}

// ========== MODAL ==========

function showModal(title, productId = null) {
    currentEditId = productId;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('product-id').value = productId || '';

    if (productId) {
        const product = allProducts.find(p => p.ma_san_pham === productId);
        if (product) {
            document.getElementById('ten_san_pham').value = product.ten_san_pham;
            document.getElementById('ma_danh_muc').value = product.ma_danh_muc || '';
            document.getElementById('gia').value = product.gia;
            document.getElementById('gia_khuyen_mai').value = product.gia_khuyen_mai || '';
            document.getElementById('so_luong_ton').value = product.so_luong_ton;
            document.getElementById('thuong_hieu').value = product.thuong_hieu || '';
            document.getElementById('xuat_xu').value = product.xuat_xu || '';
            document.getElementById('loai_da_phu_hop').value = product.loai_da_phu_hop || '';
            document.getElementById('mo_ta').value = product.mo_ta || '';
            document.getElementById('thanh_phan').value = product.thanh_phan || '';
            document.getElementById('huong_dan_su_dung').value = product.huong_dan_su_dung || '';
            document.getElementById('trang_thai_form').value = product.trang_thai || 'dang_ban';
        }
    } else {
        document.getElementById('form-product').reset();
    }

    document.getElementById('modal-product').style.display = 'block';
    document.getElementById('modal-overlay').style.display = 'block';
}

function closeModal() {
    document.getElementById('modal-product').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
    document.getElementById('form-product').reset();
    currentEditId = null;
    clearErrors();
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
}

// ========== CRUD ==========

function editProduct(id) {
    showModal('Sửa sản phẩm', id);
}

async function deleteProduct(id) {
    if (!confirm('Bạn chắc chắn muốn xóa sản phẩm này?')) return;

    try {
        const response = await fetch(`${API_BASE_URL}/products/${id}`, {
            method: 'DELETE'
        });
        const result = await response.json();

        if (result.status === 'success') {
            showAlert(result.message || 'Xóa sản phẩm thành công', 'success');
            loadProducts('', currentFilter, currentPage);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting product:', error);
        showAlert('Lỗi khi xóa sản phẩm', 'error');
    }
}

// Form submit
document.getElementById('form-product').addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    const formData = {
        ten_san_pham: document.getElementById('ten_san_pham').value.trim(),
        ma_danh_muc: document.getElementById('ma_danh_muc').value || null,
        gia: document.getElementById('gia').value,
        gia_khuyen_mai: document.getElementById('gia_khuyen_mai').value || null,
        so_luong_ton: document.getElementById('so_luong_ton').value || 0,
        thuong_hieu: document.getElementById('thuong_hieu').value.trim() || null,
        xuat_xu: document.getElementById('xuat_xu').value.trim() || null,
        loai_da_phu_hop: document.getElementById('loai_da_phu_hop').value.trim() || null,
        mo_ta: document.getElementById('mo_ta').value.trim() || null,
        thanh_phan: document.getElementById('thanh_phan').value.trim() || null,
        huong_dan_su_dung: document.getElementById('huong_dan_su_dung').value.trim() || null,
        trang_thai: document.getElementById('trang_thai_form').value,
    };

    // Validate
    if (!formData.ten_san_pham) {
        document.getElementById('error-ten_san_pham').textContent = 'Tên sản phẩm không được để trống';
        return;
    }

    if (!formData.gia || parseFloat(formData.gia) <= 0) {
        document.getElementById('error-gia').textContent = 'Giá phải lớn hơn 0';
        return;
    }

    if (formData.gia_khuyen_mai && parseFloat(formData.gia_khuyen_mai) >= parseFloat(formData.gia)) {
        document.getElementById('error-gia_khuyen_mai').textContent = 'Giá khuyến mãi phải nhỏ hơn giá gốc';
        return;
    }

    const productId = document.getElementById('product-id').value;
    const url = productId
        ? `${API_BASE_URL}/products/${productId}`
        : `${API_BASE_URL}/products`;
    const method = productId ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const result = await response.json();

        if (result.status === 'success') {
            showAlert(result.message || 'Lưu sản phẩm thành công', 'success');
            closeModal();
            loadProducts('', currentFilter, currentPage);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error saving product:', error);
        showAlert('Lỗi khi lưu sản phẩm', 'error');
    }
});

// ========== EVENT LISTENERS ==========

document.getElementById('btn-add-product').addEventListener('click', () => {
    showModal('Thêm sản phẩm');
});

document.getElementById('btn-close-modal').addEventListener('click', closeModal);
document.getElementById('btn-cancel-modal').addEventListener('click', closeModal);
document.getElementById('modal-overlay').addEventListener('click', closeModal);

// Status filter tabs
document.querySelectorAll('#product-status-tabs .role-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('#product-status-tabs .role-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.getAttribute('data-status');
        currentPage = 1;
        const search = document.getElementById('product-search-input').value;
        loadProducts(search, currentFilter, 1);
    });
});

// Search with debounce
document.getElementById('product-search-input').addEventListener('input', (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage = 1;
        loadProducts(e.target.value, currentFilter, 1);
    }, 300);
});

// Initial load
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadCategories();
    loadProducts();
});
</script>

<style>
.modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    min-width: 400px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-content--wide {
    width: 700px;
    max-width: 90vw;
    padding: 20px;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
}

.modal-content {
    padding: 20px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.modal-header h2 {
    margin: 0;
    font-size: 18px;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.modal-close:hover {
    color: #000;
}

.form-group {
    margin-bottom: 15px;
    flex: 1;
}

.form-group--flex {
    flex: 2;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
}

.form-row {
    display: flex;
    gap: 15px;
}

.error-message {
    color: #dc3545;
    font-size: 12px;
    margin-top: 3px;
    display: block;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
}

.btn {
    padding: 8px 16px;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

/* Product table cells */
.product-name-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.product-thumb {
    width: 36px;
    height: 36px;
    border-radius: 6px;
    flex-shrink: 0;
}

.product-info {
    display: flex;
    flex-direction: column;
}

.product-name {
    font-weight: 500;
    font-size: 14px;
}

.product-brand {
    font-size: 12px;
    color: #888;
}

.price-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.price-original {
    text-decoration: line-through;
    color: #999;
    font-size: 12px;
}

.price-sale {
    color: #dc3545;
    font-weight: 600;
}

.text-danger {
    color: #dc3545 !important;
}

.text-warning {
    color: #f59e0b !important;
}

/* Status badges */
.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge--active {
    background: #e8f5e9;
    color: #388e3c;
}

.status-badge--warning {
    background: #fff8e1;
    color: #f57f17;
}

.status-badge--danger {
    background: #ffebee;
    color: #d32f2f;
}

.status-badge--inactive {
    background: #f5f5f5;
    color: #757575;
}

/* Action buttons */
.action-btns {
    display: flex;
    gap: 5px;
}

.icon-action-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 5px;
    display: flex;
    align-items: center;
    color: #007bff;
}

.icon-action-btn:hover {
    color: #0056b3;
}

.icon-action-btn--danger {
    color: #dc3545;
}

.icon-action-btn--danger:hover {
    color: #c82333;
}

/* Stats */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 15px;
}

.stat-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.stat-box--warning {
    border-left-color: #f59e0b;
}

.stat-box--danger {
    border-left-color: #dc3545;
}

.stat-box--success {
    border-left-color: #10b981;
}

.stat-label {
    display: block;
    font-size: 12px;
    color: #666;
    margin-bottom: 5px;
}

.stat-value {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #007bff;
}

/* Filter tabs */
.role-tabs {
    display: flex;
    gap: 5px;
}

.role-tab {
    padding: 8px 12px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
}

.role-tab.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-top: 1px solid #eee;
    margin-top: 10px;
}

.pagination-info {
    font-size: 13px;
    color: #666;
}

.pagination-btns {
    display: flex;
    gap: 4px;
    align-items: center;
}

.page-btn {
    padding: 6px 10px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-size: 13px;
    min-width: 32px;
    text-align: center;
}

.page-btn:hover:not(:disabled):not(.active) {
    background: #f0f0f0;
}

.page-btn.active {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.page-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.page-ellipsis {
    padding: 6px 4px;
    color: #999;
}
</style>
@endsection
