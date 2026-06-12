@extends('layouts.admin')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Quản lý người dùng')
@section('page-subtitle', '<span id="users-summary">Đang tải...</span>')

@section('content')
<!-- Action Bar -->
<div class="page-action-bar" id="users-action-bar">
    <div class="action-bar-left">
        <div class="search-box search-box--wide" id="search-users">
            <i data-lucide="search" class="icon-xs search-icon"></i>
            <input type="text" placeholder="Tìm theo tên, email..." class="search-input" id="user-search-input">
        </div>

        <!-- Role Filter Tabs -->
        <div class="role-tabs" id="user-role-tabs">
            <button class="role-tab active" data-role="all">Tất cả</button>
            <button class="role-tab" data-role="khach_hang">Khách hàng</button>
            <button class="role-tab" data-role="quan_tri_vien">Admin</button>
        </div>
    </div>
    <div class="action-bar-right">
        <button class="btn btn-primary" id="btn-add-user">
            <i data-lucide="plus" class="icon-xs"></i>
            <span>Thêm người dùng</span>
        </button>
    </div>
</div>

<!-- Users Table -->
<div class="data-card" id="users-table-card">
    <div class="table-wrapper">
        <table class="admin-table" id="users-table">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="users-tbody">
                <tr class="loading-row">
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        <span>Đang tải dữ liệu...</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Stats Card -->
<div class="data-card" style="margin-top: 20px;">
    <h3 class="card-section-title">Thống kê người dùng</h3>
    <div class="stats-grid">
        <div class="stat-box">
            <span class="stat-label">Tổng người dùng</span>
            <span class="stat-value" id="total-users">0</span>
        </div>
        <div class="stat-box">
            <span class="stat-label">Khách hàng</span>
            <span class="stat-value" id="total-customers">0</span>
        </div>
        <div class="stat-box">
            <span class="stat-label">Admin</span>
            <span class="stat-value" id="total-admins">0</span>
        </div>
    </div>
</div>

<!-- Modal Add/Edit User -->
<div class="modal" id="modal-user" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modal-title">Thêm người dùng</h2>
            <button class="modal-close" id="btn-close-modal">&times;</button>
        </div>
        <form id="form-user" class="form">
            <input type="hidden" id="user-id" value="">

            <div class="form-group">
                <label for="ho_ten">Họ tên *</label>
                <input type="text" id="ho_ten" name="ho_ten" required class="form-control">
                <span class="error-message" id="error-ho_ten"></span>
            </div>

            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required class="form-control">
                <span class="error-message" id="error-email"></span>
            </div>

            <div class="form-group">
                <label for="mat_khau">Mật khẩu <span id="password-label">(tối thiểu 6 ký tự) *</span></label>
                <input type="password" id="mat_khau" name="mat_khau" class="form-control">
                <span class="error-message" id="error-mat_khau"></span>
            </div>

            <div class="form-group">
                <label for="so_dien_thoai">Số điện thoại</label>
                <input type="tel" id="so_dien_thoai" name="so_dien_thoai" class="form-control">
            </div>

            <div class="form-group">
                <label for="vai_tro">Vai trò *</label>
                <select id="vai_tro" name="vai_tro" required class="form-control">
                    <option value="khach_hang">Khách hàng</option>
                    <option value="quan_tri_vien">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label for="trang_thai">Trạng thái *</label>
                <select id="trang_thai" name="trang_thai" required class="form-control">
                    <option value="hoat_dong">Hoạt động</option>
                    <option value="bi_khoa">Bị khóa</option>
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
let allUsers = [];
let currentFilter = 'all';
let currentEditId = null;

// Load users
async function loadUsers(search = '', role = 'all') {
    try {
        let url = `${API_BASE_URL}/users?limit=100`;
        if (search) {
            url += `&search=${encodeURIComponent(search)}`;
        }

        const response = await fetch(url);
        const result = await response.json();

        if (result.success || result.status === 'success') {
            allUsers = result.data;

            // Apply role filter
            let filtered = allUsers;
            if (role !== 'all') {
                filtered = allUsers.filter(user => user.vai_tro === role);
            }

            renderTable(filtered);
            loadStats();
        }
    } catch (error) {
        console.error('Error loading users:', error);
        showAlert('Lỗi khi tải người dùng', 'error');
    }
}

// Render table
function renderTable(users) {
    const tbody = document.getElementById('users-tbody');

    if (users.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Không có người dùng nào</td></tr>';
        return;
    }

    tbody.innerHTML = users.map(user => {
        const initials = user.ho_ten.split(' ').map(n => n.charAt(0).toUpperCase()).join('');
        const roleLabel = user.vai_tro === 'quan_tri_vien' ? 'Admin' : 'Khách hàng';
        const roleBadgeClass = user.vai_tro === 'quan_tri_vien' ? 'role-badge--admin' : 'role-badge--customer';
        const statusLabel = user.trang_thai === 'hoat_dong' ? 'Hoạt động' : 'Bị khóa';
        const statusClass = user.trang_thai === 'hoat_dong' ? 'status-badge--active' : 'status-badge--inactive';

        return `
            <tr>
                <td>
                    <div class="user-cell">
                        <div class="user-avatar-sm" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            ${initials}
                        </div>
                        <span class="text-bold">${escapeHtml(user.ho_ten)}</span>
                    </div>
                </td>
                <td><span class="text-secondary">${escapeHtml(user.email)}</span></td>
                <td><span class="text-secondary">${user.so_dien_thoai || '--'}</span></td>
                <td><span class="role-badge ${roleBadgeClass}">${roleLabel}</span></td>
                <td><span class="status-badge ${statusClass}">${statusLabel}</span></td>
                <td>
                    <div class="action-btns">
                        <button class="icon-action-btn" title="Sửa" onclick="editUser(${user.ma_nguoi_dung})">
                            <i data-lucide="pencil" class="icon-xs"></i>
                        </button>
                        <button class="icon-action-btn icon-action-btn--danger" title="Xóa" onclick="deleteUser(${user.ma_nguoi_dung})">
                            <i data-lucide="trash-2" class="icon-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    lucide.createIcons();
}

// Load stats
async function loadStats() {
    try {
        const response = await fetch(`${API_BASE_URL}/users/stats`);
        const result = await response.json();

        if (result.success || result.status === 'success') {
            document.getElementById('total-users').textContent = result.data.total;
            document.getElementById('total-customers').textContent = result.data.customers;
            document.getElementById('total-admins').textContent = result.data.admins;
            document.getElementById('users-summary').textContent =
                `${result.data.total} tài khoản · ${result.data.customers} khách hàng`;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Show modal
function showModal(title, userId = null) {
    currentEditId = userId;
    document.getElementById('modal-title').textContent = title;
    document.getElementById('user-id').value = userId || '';

    if (userId) {
        document.getElementById('password-label').textContent = '(để trống để giữ mật khẩu cũ)';
        document.getElementById('mat_khau').required = false;

        const user = allUsers.find(u => u.ma_nguoi_dung === userId);
        if (user) {
            document.getElementById('ho_ten').value = user.ho_ten;
            document.getElementById('email').value = user.email;
            document.getElementById('so_dien_thoai').value = user.so_dien_thoai || '';
            document.getElementById('vai_tro').value = user.vai_tro;
            document.getElementById('trang_thai').value = user.trang_thai;
        }
    } else {
        document.getElementById('password-label').textContent = '(tối thiểu 6 ký tự) *';
        document.getElementById('mat_khau').required = true;
        document.getElementById('form-user').reset();
    }

    document.getElementById('modal-user').style.display = 'block';
    document.getElementById('modal-overlay').style.display = 'block';
}

// Close modal
function closeModal() {
    document.getElementById('modal-user').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
    document.getElementById('form-user').reset();
    currentEditId = null;
    clearErrors();
}

// Clear errors
function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
}

// Edit user
function editUser(id) {
    showModal('Sửa người dùng', id);
}

// Delete user
async function deleteUser(id) {
    if (!confirm('Bạn chắc chắn muốn xóa người dùng này?')) return;

    try {
        const response = await fetch(`${API_BASE_URL}/users/${id}`, {
            method: 'DELETE'
        });
        const result = await response.json();

        if (result.success || result.status === 'success') {
            showAlert(result.message || 'Xóa người dùng thành công', 'success');
            loadUsers();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        showAlert('Lỗi khi xóa người dùng', 'error');
    }
}

// Show alert
function showAlert(message, type = 'info') {
    alert(message);
}

// Form submit
document.getElementById('form-user').addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();

    const formData = {
        ho_ten: document.getElementById('ho_ten').value.trim(),
        email: document.getElementById('email').value.trim(),
        so_dien_thoai: document.getElementById('so_dien_thoai').value.trim() || null,
        vai_tro: document.getElementById('vai_tro').value,
        trang_thai: document.getElementById('trang_thai').value,
    };

    if (!formData.ho_ten) {
        document.getElementById('error-ho_ten').textContent = 'Họ tên không được để trống';
        return;
    }

    if (!formData.email) {
        document.getElementById('error-email').textContent = 'Email không được để trống';
        return;
    }

    const matKhau = document.getElementById('mat_khau').value.trim();
    if (matKhau) {
        formData.password = matKhau;
    } else if (!currentEditId) {
        document.getElementById('error-mat_khau').textContent = 'Mật khẩu không được để trống';
        return;
    }

    const userId = document.getElementById('user-id').value;
    const url = userId
        ? `${API_BASE_URL}/users/${userId}`
        : `${API_BASE_URL}/users`;
    const method = userId ? 'PUT' : 'POST';

    try {
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const result = await response.json();

        if (result.success || result.status === 'success') {
            showAlert(result.message || 'Lưu thành công', 'success');
            closeModal();
            loadUsers('', currentFilter);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error saving user:', error);
        showAlert('Lỗi khi lưu người dùng', 'error');
    }
});

// Event listeners
document.getElementById('btn-add-user').addEventListener('click', () => {
    showModal('Thêm người dùng');
});

document.getElementById('btn-close-modal').addEventListener('click', closeModal);
document.getElementById('btn-cancel-modal').addEventListener('click', closeModal);
document.getElementById('modal-overlay').addEventListener('click', closeModal);

// Role tabs
document.querySelectorAll('.role-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        currentFilter = this.getAttribute('data-role');
        loadUsers('', currentFilter);
    });
});

// Search
document.getElementById('user-search-input').addEventListener('input', (e) => {
    loadUsers(e.target.value, currentFilter);
});

// Initial load
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    loadUsers();
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

.user-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 12px;
}

.role-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.role-badge--customer {
    background: #e3f2fd;
    color: #1976d2;
}

.role-badge--admin {
    background: #f3e5f5;
    color: #7b1fa2;
}

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

.status-badge--inactive {
    background: #ffebee;
    color: #d32f2f;
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

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.stat-box {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
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
</style>
@endsection
