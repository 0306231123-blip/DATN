@extends('layouts.admin')

@section('title', 'Quản lý danh mục')
@section('page-title', 'Quản lý danh mục')
@section('page-subtitle', 'Đang tải...')

@section('content')
<div class="categories-layout" id="categories-layout">
    <!-- Left: Categories Table -->
    <div class="categories-table-section">
        <!-- Action Bar -->
        <div class="page-action-bar" id="categories-action-bar">
            <div class="action-bar-left">
                <button class="btn btn-primary" id="btn-add-category">
                    <i data-lucide="plus" class="icon-xs"></i>
                    <span>Thêm danh mục</span>
                </button>
                <div class="search-box" id="search-categories">
                    <i data-lucide="search" class="icon-xs search-icon"></i>
                    <input type="text" placeholder="Tìm kiếm..." class="search-input" id="category-search-input">
                </div>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="data-card" id="categories-table-card">
            <div class="table-wrapper">
                <table class="admin-table" id="categories-table">
                    <thead>
                        <tr>
                            <th>Tên danh mục</th>
                            <th>Mô tả</th>
                            <th>Danh mục cha</th>
                            <th>Thứ tự</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="categories-tbody">
                        <tr class="loading-row">
                            <td colspan="5" style="text-align: center; padding: 20px;">
                                <span>Đang tải dữ liệu...</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Category Stats -->
    <div class="categories-chart-section">
        <div class="data-card" id="category-stats-card">
            <h3 class="card-section-title">Thống kê danh mục</h3>
            <div class="category-stats-list" id="category-stats-list">
                <div class="stat-item">
                    <span class="stat-label">Tổng danh mục:</span>
                    <span class="stat-value" id="total-categories">0</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add/Edit Category -->
<div class="modal" id="modal-category" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-title">Thêm danh mục</h2>
            <button class="modal-close" id="btn-close-modal">&times;</button>
        </div>
        <form id="form-category" class="form">
            <input type="hidden" id="category-id" value="">

            <div class="form-group">
                <label for="ten_danh_muc">Tên danh mục *</label>
                <input type="text" id="ten_danh_muc" name="ten_danh_muc" required class="form-control">
                <span class="error-message" id="error-ten_danh_muc"></span>
            </div>

            <div class="form-group">
                <label for="mo_ta">Mô tả</label>
                <textarea id="mo_ta" name="mo_ta" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="ma_danh_muc_cha">Danh mục cha</label>
                <select id="ma_danh_muc_cha" name="ma_danh_muc_cha" class="form-control">
                    <option value="">-- Không --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="thu_tu_hien_thi">Thứ tự hiển thị</label>
                <input type="number" id="thu_tu_hien_thi" name="thu_tu_hien_thi" class="form-control" min="1">
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

let categories = [];
let currentEditId = null;

// Load categories
async function loadCategories(search = '') {
    try {
        let url = `${API_BASE_URL}/categories?limit=100`;
        if (search) {
            url += `&search=${encodeURIComponent(search)}`;
        }

        const response = await fetch(url);
        const result = await response.json();

        if (result.status === 'success') {
            categories = result.data;
            renderTable();
            loadStats();
            updateParentCategorySelect();
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        showAlert('Lỗi khi tải danh mục', 'error');
    }
}

// Render table
function renderTable() {
    const tbody = document.getElementById('categories-tbody');

    if (categories.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Không có danh mục nào</td></tr>';
        return;
    }

    tbody.innerHTML = categories.map(cat => `
        <tr>
            <td><span class="text-bold">${escapeHtml(cat.ten_danh_muc)}</span></td>
            <td><span class="text-secondary text-truncate">${escapeHtml(cat.mo_ta || '--')}</span></td>
            <td><span class="text-secondary">${getParentCategoryName(cat.ma_danh_muc_cha)}</span></td>
            <td><span class="text-secondary">${cat.thu_tu_hien_thi || '--'}</span></td>
            <td>
                <div class="action-btns">
                    <button class="icon-action-btn" title="Sửa" onclick="editCategory(${cat.ma_danh_muc})">
                        <i data-lucide="pencil" class="icon-xs"></i>
                    </button>
                    <button class="icon-action-btn icon-action-btn--danger" title="Xóa" onclick="deleteCategory(${cat.ma_danh_muc})">
                        <i data-lucide="trash-2" class="icon-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

    lucide.createIcons();
}

// Load stats
async function loadStats() {
    try {
        const response = await fetch(`${API_BASE_URL}/categories/stats`);
        const result = await response.json();

        if (result.status === 'success') {
            document.getElementById('total-categories').textContent = result.data.total;
            document.getElementById('page-subtitle').textContent =
                `${result.data.total} danh mục`;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Update parent category select
function updateParentCategorySelect() {
    const select = document.getElementById('ma_danh_muc_cha');
    const parentCategories = categories.filter(cat => !cat.ma_danh_muc_cha);

    const options = '<option value="">-- Không --</option>' +
        parentCategories.map(cat =>
            `<option value="${cat.ma_danh_muc}">${escapeHtml(cat.ten_danh_muc)}</option>`
        ).join('');

    select.innerHTML = options;
}

// Get parent category name
function getParentCategoryName(id) {
    if (!id) return '--';
    const parent = categories.find(cat => cat.ma_danh_muc === id);
    return parent ? parent.ten_danh_muc : '--';
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Show modal
function showModal(title, categoryId = null) {
    currentEditId = categoryId;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('category-id').value = categoryId || '';

    if (categoryId) {
        const category = categories.find(c => c.ma_danh_muc === categoryId);
        if (category) {
            document.getElementById('ten_danh_muc').value = category.ten_danh_muc;
            document.getElementById('mo_ta').value = category.mo_ta || '';
            document.getElementById('ma_danh_muc_cha').value = category.ma_danh_muc_cha || '';
            document.getElementById('thu_tu_hien_thi').value = category.thu_tu_hien_thi || '';
        }
    } else {
        document.getElementById('form-category').reset();
    }

    document.getElementById('modal-category').style.display = 'block';
    document.getElementById('modal-overlay').style.display = 'block';
}

// Close modal
function closeModal() {
    document.getElementById('modal-category').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
    document.getElementById('form-category').reset();
    currentEditId = null;
    clearErrors();
}

// Clear errors
function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
}

// Edit category
function editCategory(id) {
    showModal('Sửa danh mục', id);
}

// Delete category
async function deleteCategory(id) {
    if (!confirm('Bạn chắc chắn muốn xóa danh mục này?')) return;

    try {
        const response = await fetch(`${API_BASE_URL}/categories/${id}`, {
            method: 'DELETE'
        });
        const result = await response.json();

        if (result.status === 'success') {
            showAlert('Xóa danh mục thành công', 'success');
            loadCategories();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting category:', error);
        showAlert('Lỗi khi xóa danh mục', 'error');
    }
}

// Show alert
function showAlert(message, type = 'info') {
    alert(message); // Simple alert, có thể thay bằng toast notification
}

// Form submit
document.getElementById('form-category').addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    const formData = {
        ten_danh_muc: document.getElementById('ten_danh_muc').value.trim(),
        mo_ta: document.getElementById('mo_ta').value.trim() || null,
        ma_danh_muc_cha: document.getElementById('ma_danh_muc_cha').value || null,
        thu_tu_hien_thi: document.getElementById('thu_tu_hien_thi').value || null,
    };

    if (!formData.ten_danh_muc) {
        document.getElementById('error-ten_danh_muc').textContent = 'Tên danh mục không được để trống';
        return;
    }

    const categoryId = document.getElementById('category-id').value;
    const url = categoryId
        ? `${API_BASE_URL}/categories/${categoryId}`
        : `${API_BASE_URL}/categories`;
    const method = categoryId ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const result = await response.json();

        if (result.status === 'success') {
            showAlert(result.message, 'success');
            closeModal();
            loadCategories();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error saving category:', error);
        showAlert('Lỗi khi lưu danh mục', 'error');
    }
});

// Event listeners
document.getElementById('btn-add-category').addEventListener('click', () => {
    showModal('Thêm danh mục');
});

document.getElementById('btn-close-modal').addEventListener('click', closeModal);
document.getElementById('btn-cancel-modal').addEventListener('click', closeModal);

document.getElementById('modal-overlay').addEventListener('click', closeModal);

document.getElementById('category-search-input').addEventListener('input', (e) => {
    loadCategories(e.target.value);
});

// Initial load
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadCategories();
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

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    max-width: 200px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.stat-label {
    font-weight: 500;
}

.stat-value {
    color: #007bff;
    font-weight: bold;
}
</style>
@endsection
