@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')
@section('page-title', 'Quản lý đơn hàng')
@section('page-subtitle', '213 đơn · tháng 5/2026')

@section('content')
<!-- Action Bar -->
<div class="page-action-bar" id="orders-action-bar">
    <div class="action-bar-left">
        <!-- Status Tabs -->
        <div class="status-tabs" id="order-status-tabs">
            <button class="status-tab active" data-status="all" id="tab-all">
                <span>Tất cả</span>
                <span class="tab-count">213</span>
            </button>
            <button class="status-tab" data-status="pending" id="tab-pending">
                <span>Chờ xác nhận</span>
                <span class="tab-count">18</span>
            </button>
            <button class="status-tab" data-status="shipping" id="tab-shipping">
                <span>Đang giao</span>
                <span class="tab-count">42</span>
            </button>
            <button class="status-tab" data-status="completed" id="tab-completed">
                <span>Hoàn thành</span>
                <span class="tab-count">144</span>
            </button>
            <button class="status-tab" data-status="cancelled" id="tab-cancelled">
                <span>Đã hủy</span>
                <span class="tab-count">9</span>
            </button>
        </div>
    </div>
    <div class="action-bar-right">
        <div class="search-box" id="search-orders">
            <i data-lucide="search" class="icon-xs search-icon"></i>
            <input type="text" placeholder="Mã đơn..." class="search-input search-input--sm" id="order-search-input">
        </div>
        <button class="btn btn-outline" id="btn-export-orders">
            <i data-lucide="download" class="icon-xs"></i>
            <span>Xuất Excel</span>
        </button>
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
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="order-id-link">#DH0091</span></td>
                    <td><span class="text-bold">Nguyễn Lan Anh</span></td>
                    <td><span class="text-secondary">3 sản phẩm</span></td>
                    <td><span class="text-bold">855.000đ</span></td>
                    <td><span class="status-badge status-badge--warning">Chờ xác nhận</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem chi tiết" aria-label="Xem đơn hàng"><i data-lucide="eye" class="icon-xs"></i></button>
                            <button class="icon-action-btn icon-action-btn--success" title="Duyệt" aria-label="Duyệt đơn hàng"><i data-lucide="check" class="icon-xs"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="order-id-link">#DH0090</span></td>
                    <td><span class="text-bold">Trần Minh Châu</span></td>
                    <td><span class="text-secondary">1 sản phẩm</span></td>
                    <td><span class="text-bold">320.000đ</span></td>
                    <td><span class="status-badge status-badge--info">Đang giao</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem chi tiết" aria-label="Xem đơn hàng"><i data-lucide="eye" class="icon-xs"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="order-id-link">#DH0089</span></td>
                    <td><span class="text-bold">Lê Thùy Dung</span></td>
                    <td><span class="text-secondary">2 sản phẩm</span></td>
                    <td><span class="text-bold">635.000đ</span></td>
                    <td><span class="status-badge status-badge--success">Hoàn thành</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem chi tiết" aria-label="Xem đơn hàng"><i data-lucide="eye" class="icon-xs"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td><span class="order-id-link">#DH0088</span></td>
                    <td><span class="text-bold">Phạm Hải Yến</span></td>
                    <td><span class="text-secondary">5 sản phẩm</span></td>
                    <td><span class="text-bold">1.420.000đ</span></td>
                    <td><span class="status-badge status-badge--danger">Đã hủy</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem chi tiết" aria-label="Xem đơn hàng"><i data-lucide="eye" class="icon-xs"></i></button>
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

    // Tab switching
    const tabs = document.querySelectorAll('.status-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>
@endsection
