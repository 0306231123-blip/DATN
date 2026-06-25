@extends('layouts.admin')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Quản lý người dùng')
@section('page-subtitle', 'Đang tải...')

@section('content')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-users.css') }}">
@endsection
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
        let url = `${API_BASE_URL}/users?per_page=100`;
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
            document.getElementById('page-subtitle').textContent =
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

@endsection
