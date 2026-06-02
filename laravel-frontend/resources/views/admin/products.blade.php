@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Quản lý sản phẩm')
@section('page-subtitle', '119 sản phẩm · cập nhật hôm nay')

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
        <button class="btn btn-outline" id="btn-filter-products">
            <i data-lucide="sliders-horizontal" class="icon-xs"></i>
            <span>Lọc</span>
        </button>
        <button class="btn btn-outline" id="btn-export-products">
            <i data-lucide="download" class="icon-xs"></i>
            <span>Xuất</span>
        </button>
    </div>
</div>

<!-- Products Table -->
<div class="data-card" id="products-table-card">
    <div class="table-wrapper">
        <table class="admin-table" id="products-table">
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá</th>
                    <th>Kho</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="product-name-cell">
                            <div class="product-thumb" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe);"></div>
                            <span class="product-name">Kem dưỡng ẩm SPF50</span>
                        </div>
                    </td>
                    <td><span class="text-secondary">Chăm sóc da</span></td>
                    <td><span class="text-bold">320.000đ</span></td>
                    <td><span class="text-bold">142</span></td>
                    <td><span class="status-badge status-badge--active">Đang bán</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem" aria-label="Xem sản phẩm"><i data-lucide="eye" class="icon-xs"></i></button>
                            <button class="icon-action-btn" title="Sửa" aria-label="Sửa sản phẩm"><i data-lucide="pencil" class="icon-xs"></i></button>
                            <button class="icon-action-btn icon-action-btn--danger" title="Xóa" aria-label="Xóa sản phẩm"><i data-lucide="trash-2" class="icon-xs"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="product-name-cell">
                            <div class="product-thumb" style="background: linear-gradient(135deg, #fce7f3, #fbcfe8);"></div>
                            <span class="product-name">Son môi lì #12</span>
                        </div>
                    </td>
                    <td><span class="text-secondary">Trang điểm</span></td>
                    <td><span class="text-bold">185.000đ</span></td>
                    <td><span class="text-bold">58</span></td>
                    <td><span class="status-badge status-badge--active">Đang bán</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem" aria-label="Xem sản phẩm"><i data-lucide="eye" class="icon-xs"></i></button>
                            <button class="icon-action-btn" title="Sửa" aria-label="Sửa sản phẩm"><i data-lucide="pencil" class="icon-xs"></i></button>
                            <button class="icon-action-btn icon-action-btn--danger" title="Xóa" aria-label="Xóa sản phẩm"><i data-lucide="trash-2" class="icon-xs"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="product-name-cell">
                            <div class="product-thumb" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);"></div>
                            <span class="product-name">Dầu gội phục hồi</span>
                        </div>
                    </td>
                    <td><span class="text-secondary">Dưỡng tóc</span></td>
                    <td><span class="text-bold">210.000đ</span></td>
                    <td><span class="text-bold">0</span></td>
                    <td><span class="status-badge status-badge--danger">Hết hàng</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem" aria-label="Xem sản phẩm"><i data-lucide="eye" class="icon-xs"></i></button>
                            <button class="icon-action-btn" title="Sửa" aria-label="Sửa sản phẩm"><i data-lucide="pencil" class="icon-xs"></i></button>
                            <button class="icon-action-btn icon-action-btn--danger" title="Xóa" aria-label="Xóa sản phẩm"><i data-lucide="trash-2" class="icon-xs"></i></button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="product-name-cell">
                            <div class="product-thumb" style="background: linear-gradient(135deg, #fef3c7, #fde68a);"></div>
                            <span class="product-name">Serum vitamin C</span>
                        </div>
                    </td>
                    <td><span class="text-secondary">Chăm sóc da</span></td>
                    <td><span class="text-bold">450.000đ</span></td>
                    <td><span class="text-bold">27</span></td>
                    <td><span class="status-badge status-badge--warning">Sắp hết</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="icon-action-btn" title="Xem" aria-label="Xem sản phẩm"><i data-lucide="eye" class="icon-xs"></i></button>
                            <button class="icon-action-btn" title="Sửa" aria-label="Sửa sản phẩm"><i data-lucide="pencil" class="icon-xs"></i></button>
                            <button class="icon-action-btn icon-action-btn--danger" title="Xóa" aria-label="Xóa sản phẩm"><i data-lucide="trash-2" class="icon-xs"></i></button>
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
});
</script>
@endsection
