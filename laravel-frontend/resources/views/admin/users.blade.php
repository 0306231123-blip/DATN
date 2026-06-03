@extends('layouts.admin')

@section('title', 'Quản lý người dùng')
@section('page-title', 'Quản lý người dùng')
@section('page-subtitle', '284 tài khoản · 67 mới tháng này')

@section('content')
<!-- Action Bar -->
<div class="page-action-bar" id="users-action-bar">
    <div class="action-bar-left">
        <div class="search-box search-box--wide" id="search-users">
            <i data-lucide="search" class="icon-xs search-icon"></i>
            <input type="text" placeholder="Tìm theo tên, email..." class="search-input" id="user-search-input">
        </div>
        <button class="btn btn-outline" id="btn-filter-users">
            <i data-lucide="sliders-horizontal" class="icon-xs"></i>
            <span>Lọc</span>
        </button>
        <!-- Role Filter Tabs -->
        <div class="role-tabs" id="user-role-tabs">
            <button class="role-tab active" data-role="all" id="role-all">Tất cả</button>
            <button class="role-tab" data-role="customer" id="role-customer">Khách hàng</button>
            <button class="role-tab" data-role="admin" id="role-admin">Admin</button>
        </div>
    </div>
    <div class="action-bar-right">
        <button class="btn btn-outline" id="btn-export-users">
            <i data-lucide="download" class="icon-xs"></i>
            <span>Xuất danh sách</span>
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
                    <th>Đơn</th>
                    <th>Tổng chi</th>
                    <th>Vai trò</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm" style="background: linear-gradient(135deg, #c084fc, #a855f7);">LA</div>
                            <span class="text-bold">Lan Anh</span>
                        </div>
                    </td>
                    <td><span class="text-secondary">lananh@gmail.com</span></td>
                    <td><span class="text-bold">12</span></td>
                    <td><span class="text-bold">4.2tr đ</span></td>
                    <td><span class="role-badge role-badge--customer">Khách hàng</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem" aria-label="Xem người dùng"><i data-lucide="eye" class="icon-xs"></i></button>
                            <button class="icon-action-btn" title="Sửa" aria-label="Sửa người dùng"><i data-lucide="pencil" class="icon-xs"></i></button>
                            <button class="icon-action-btn icon-action-btn--danger" title="Xóa" aria-label="Xóa người dùng"><i data-lucide="trash-2" class="icon-xs"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm" style="background: linear-gradient(135deg, #f472b6, #ec4899);">MC</div>
                            <span class="text-bold">Minh Châu</span>
                        </div>
                    </td>
                    <td><span class="text-secondary">mchau@gmail.com</span></td>
                    <td><span class="text-bold">5</span></td>
                    <td><span class="text-bold">1.6tr đ</span></td>
                    <td><span class="role-badge role-badge--customer">Khách hàng</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem" aria-label="Xem người dùng"><i data-lucide="eye" class="icon-xs"></i></button>
                            <button class="icon-action-btn" title="Sửa" aria-label="Sửa người dùng"><i data-lucide="pencil" class="icon-xs"></i></button>
                            <button class="icon-action-btn icon-action-btn--danger" title="Xóa" aria-label="Xóa người dùng"><i data-lucide="trash-2" class="icon-xs"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar-sm" style="background: linear-gradient(135deg, var(--color-primary), #a78bfa);">AD</div>
                            <span class="text-bold">Admin</span>
                        </div>
                    </td>
                    <td><span class="text-secondary">admin@beauty.vn</span></td>
                    <td><span class="text-muted">—</span></td>
                    <td><span class="text-muted">—</span></td>
                    <td><span class="role-badge role-badge--admin">Admin</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem" aria-label="Xem người dùng"><i data-lucide="eye" class="icon-xs"></i></button>
                            <button class="icon-action-btn" title="Sửa" aria-label="Sửa người dùng"><i data-lucide="pencil" class="icon-xs"></i></button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    // Role tab switching
    const roleTabs = document.querySelectorAll('.role-tab');
    roleTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            roleTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
@endsection
