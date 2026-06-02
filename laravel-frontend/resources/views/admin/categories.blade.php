@extends('layouts.admin')

@section('title', 'Quản lý danh mục')
@section('page-title', 'Quản lý danh mục')
@section('page-subtitle', '4 danh mục · 119 sản phẩm')

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
                            <th>Sản phẩm</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="text-bold">Chăm sóc da</span></td>
                            <td><span class="text-secondary text-truncate">Kem, serum, toner, mặt nạ...</span></td>
                            <td><span class="count-badge">48</span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="icon-action-btn" title="Sửa" aria-label="Sửa danh mục"><i data-lucide="pencil" class="icon-xs"></i></button>
                                    <button class="icon-action-btn icon-action-btn--danger" title="Xóa" aria-label="Xóa danh mục"><i data-lucide="trash-2" class="icon-xs"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="text-bold">Trang điểm</span></td>
                            <td><span class="text-secondary text-truncate">Son, phấn, mascara...</span></td>
                            <td><span class="count-badge">35</span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="icon-action-btn" title="Sửa" aria-label="Sửa danh mục"><i data-lucide="pencil" class="icon-xs"></i></button>
                                    <button class="icon-action-btn icon-action-btn--danger" title="Xóa" aria-label="Xóa danh mục"><i data-lucide="trash-2" class="icon-xs"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="text-bold">Dưỡng tóc</span></td>
                            <td><span class="text-secondary text-truncate">Dầu gội, xả, ủ tóc...</span></td>
                            <td><span class="count-badge">22</span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="icon-action-btn" title="Sửa" aria-label="Sửa danh mục"><i data-lucide="pencil" class="icon-xs"></i></button>
                                    <button class="icon-action-btn icon-action-btn--danger" title="Xóa" aria-label="Xóa danh mục"><i data-lucide="trash-2" class="icon-xs"></i></button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="text-bold">Nước hoa</span></td>
                            <td><span class="text-secondary text-truncate">Nữ, nam, unisex...</span></td>
                            <td><span class="count-badge">14</span></td>
                            <td>
                                <div class="action-btns">
                                    <button class="icon-action-btn" title="Sửa" aria-label="Sửa danh mục"><i data-lucide="pencil" class="icon-xs"></i></button>
                                    <button class="icon-action-btn icon-action-btn--danger" title="Xóa" aria-label="Xóa danh mục"><i data-lucide="trash-2" class="icon-xs"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right: Category Chart -->
    <div class="categories-chart-section">
        <div class="data-card" id="category-distribution-card">
            <h3 class="card-section-title">Tỉ lệ sản phẩm theo danh mục</h3>
            <div class="category-bar-list" id="category-bar-list">
                <div class="category-bar-item">
                    <div class="category-bar-label">
                        <span class="category-bar-name">Chăm sóc da</span>
                        <span class="category-bar-value">68%</span>
                    </div>
                    <div class="category-bar-track">
                        <div class="category-bar-fill" style="width: 68%; background: var(--color-primary);"></div>
                    </div>
                </div>
                <div class="category-bar-item">
                    <div class="category-bar-label">
                        <span class="category-bar-name">Trang điểm</span>
                        <span class="category-bar-value">20%</span>
                    </div>
                    <div class="category-bar-track">
                        <div class="category-bar-fill" style="width: 20%; background: var(--color-secondary);"></div>
                    </div>
                </div>
                <div class="category-bar-item">
                    <div class="category-bar-label">
                        <span class="category-bar-name">Dưỡng tóc</span>
                        <span class="category-bar-value">9%</span>
                    </div>
                    <div class="category-bar-track">
                        <div class="category-bar-fill" style="width: 9%; background: var(--color-green);"></div>
                    </div>
                </div>
                <div class="category-bar-item">
                    <div class="category-bar-label">
                        <span class="category-bar-name">Nước hoa</span>
                        <span class="category-bar-value">3%</span>
                    </div>
                    <div class="category-bar-track">
                        <div class="category-bar-fill" style="width: 3%; background: var(--color-yellow);"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();

    // Animate progress bars on load
    const fills = document.querySelectorAll('.category-bar-fill');
    fills.forEach((fill, index) => {
        const targetWidth = fill.style.width;
        fill.style.width = '0%';
        setTimeout(() => {
            fill.style.width = targetWidth;
        }, 100 + index * 150);
    });
});
</script>
@endsection
