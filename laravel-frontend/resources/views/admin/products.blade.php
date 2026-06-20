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

            <!-- Image Picker Section -->
            <div class="form-group">
                <label>Hình ảnh sản phẩm</label>
                <input type="hidden" id="hinh_anh" name="hinh_anh" value="">

                <!-- Tab Navigation -->
                <div class="image-picker">
                    <div class="image-picker-tabs">
                        <button type="button" class="image-picker-tab active" data-tab="upload">
                            <i data-lucide="upload-cloud" class="icon-xs"></i>
                            <span>Upload</span>
                        </button>
                        <button type="button" class="image-picker-tab" data-tab="gallery">
                            <i data-lucide="image" class="icon-xs"></i>
                            <span>Thư viện</span>
                        </button>
                        <button type="button" class="image-picker-tab" data-tab="url">
                            <i data-lucide="link" class="icon-xs"></i>
                            <span>Nhập URL</span>
                        </button>
                    </div>

                    <!-- Tab: Upload -->
                    <div class="image-picker-panel active" id="panel-upload">
                        <div class="drop-zone" id="drop-zone">
                            <div class="drop-zone-content">
                                <i data-lucide="cloud-upload" class="drop-zone-icon"></i>
                                <p class="drop-zone-text">Kéo thả ảnh vào đây</p>
                                <p class="drop-zone-hint">hoặc <span class="drop-zone-browse">click để chọn file</span></p>
                                <p class="drop-zone-formats">JPG, PNG, GIF, WebP — tối đa 5MB</p>
                            </div>
                            <input type="file" id="file-input" accept="image/jpeg,image/png,image/gif,image/webp" style="display: none;">
                        </div>
                        <!-- Upload Progress -->
                        <div class="upload-progress" id="upload-progress" style="display: none;">
                            <div class="upload-progress-bar">
                                <div class="upload-progress-fill" id="upload-progress-fill"></div>
                            </div>
                            <span class="upload-progress-text" id="upload-progress-text">Đang tải lên...</span>
                        </div>
                    </div>

                    <!-- Tab: Gallery -->
                    <div class="image-picker-panel" id="panel-gallery">
                        <div class="gallery-search">
                            <i data-lucide="search" class="icon-xs gallery-search-icon"></i>
                            <input type="text" id="gallery-search-input" placeholder="Tìm ảnh..." class="gallery-search-input">
                        </div>
                        <div class="gallery-grid" id="gallery-grid">
                            <div class="gallery-loading">Đang tải ảnh...</div>
                        </div>
                    </div>

                    <!-- Tab: URL -->
                    <div class="image-picker-panel" id="panel-url">
                        <div class="url-input-group">
                            <input type="text" id="url-input" placeholder="https://example.com/image.jpg hoặc /images/product.jpg" class="form-control">
                            <button type="button" class="btn btn-secondary btn-sm" id="btn-apply-url">Áp dụng</button>
                        </div>
                        <span class="form-hint">Nhập URL ảnh bên ngoài hoặc đường dẫn trong thư mục public</span>
                    </div>
                </div>

                <!-- Preview (shared across all tabs) -->
                <div class="image-preview-section" id="image-preview-section" style="display: none;">
                    <div class="image-preview-card">
                        <img id="image-preview-img" src="" alt="Preview">
                        <div class="image-preview-info">
                            <span class="image-preview-name" id="image-preview-name"></span>
                            <button type="button" class="image-preview-remove" id="btn-remove-image" title="Xóa ảnh">
                                <i data-lucide="x" class="icon-xs"></i>
                                <span>Xóa ảnh</span>
                            </button>
                        </div>
                    </div>
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

.form-hint {
    display: block;
    margin-top: 4px;
    color: #888;
    font-size: 12px;
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

.product-thumb-img {
    object-fit: cover;
    border: 1px solid #eee;
}

.product-info {
    display: flex;
    flex-direction: column;
}

/* ===== Image Picker ===== */
.image-picker {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
}

.image-picker-tabs {
    display: flex;
    border-bottom: 1px solid #e2e8f0;
    background: #f8fafc;
}

.image-picker-tab {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 10px 12px;
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #64748b;
    transition: all 0.2s ease;
    border-bottom: 2px solid transparent;
    margin-bottom: -1px;
}

.image-picker-tab:hover {
    color: #334155;
    background: #f1f5f9;
}

.image-picker-tab.active {
    color: #2563eb;
    border-bottom-color: #2563eb;
    background: #fff;
}

.image-picker-panel {
    display: none;
    padding: 16px;
}

.image-picker-panel.active {
    display: block;
}

/* Drop Zone */
.drop-zone {
    border: 2px dashed #cbd5e1;
    border-radius: 8px;
    padding: 32px 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.25s ease;
    background: #fafbfc;
    position: relative;
}

.drop-zone:hover {
    border-color: #93c5fd;
    background: #eff6ff;
}

.drop-zone.drag-over {
    border-color: #3b82f6;
    background: #dbeafe;
    transform: scale(1.01);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
}

.drop-zone-icon {
    width: 40px;
    height: 40px;
    color: #94a3b8;
    margin-bottom: 8px;
    transition: color 0.2s;
}

.drop-zone:hover .drop-zone-icon,
.drop-zone.drag-over .drop-zone-icon {
    color: #3b82f6;
}

.drop-zone-content {
    pointer-events: none;
}

.drop-zone-text {
    font-size: 15px;
    font-weight: 500;
    color: #475569;
    margin: 0 0 4px;
}

.drop-zone-hint {
    font-size: 13px;
    color: #94a3b8;
    margin: 0 0 8px;
}

.drop-zone-browse {
    color: #2563eb;
    font-weight: 500;
    text-decoration: underline;
    text-underline-offset: 2px;
}

.drop-zone-formats {
    font-size: 11px;
    color: #b0b8c4;
    margin: 0;
}

/* Upload Progress */
.upload-progress {
    margin-top: 12px;
}

.upload-progress-bar {
    width: 100%;
    height: 6px;
    background: #e2e8f0;
    border-radius: 3px;
    overflow: hidden;
}

.upload-progress-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
    border-radius: 3px;
    transition: width 0.3s ease;
}

.upload-progress-text {
    display: block;
    font-size: 12px;
    color: #64748b;
    margin-top: 4px;
}

/* Gallery */
.gallery-search {
    position: relative;
    margin-bottom: 12px;
}

.gallery-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    pointer-events: none;
}

.gallery-search-input {
    width: 100%;
    padding: 8px 12px 8px 32px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    font-size: 13px;
    box-sizing: border-box;
    outline: none;
    transition: border-color 0.2s;
}

.gallery-search-input:focus {
    border-color: #93c5fd;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 8px;
    max-height: 220px;
    overflow-y: auto;
    padding: 2px;
}

.gallery-grid::-webkit-scrollbar {
    width: 5px;
}
.gallery-grid::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}
.gallery-grid::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.gallery-item {
    aspect-ratio: 1;
    border-radius: 6px;
    border: 2px solid #e2e8f0;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
}

.gallery-item:hover {
    border-color: #93c5fd;
    transform: scale(1.04);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.gallery-item.selected {
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
}

.gallery-item.selected::after {
    content: '✓';
    position: absolute;
    top: 4px;
    right: 4px;
    width: 18px;
    height: 18px;
    background: #2563eb;
    color: #fff;
    font-size: 11px;
    font-weight: bold;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.gallery-loading,
.gallery-empty {
    grid-column: 1 / -1;
    text-align: center;
    padding: 24px;
    color: #94a3b8;
    font-size: 13px;
}

/* URL Input */
.url-input-group {
    display: flex;
    gap: 8px;
}

.url-input-group .form-control {
    flex: 1;
}

.btn-sm {
    padding: 6px 14px;
    font-size: 13px;
}

/* Preview Section */
.image-preview-section {
    margin-top: 12px;
}

.image-preview-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 14px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    animation: fadeInUp 0.25s ease;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

.image-preview-card img {
    width: 56px;
    height: 56px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #d1fae5;
    flex-shrink: 0;
}

.image-preview-info {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    min-width: 0;
}

.image-preview-name {
    font-size: 13px;
    color: #334155;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.image-preview-remove {
    display: flex;
    align-items: center;
    gap: 4px;
    background: none;
    border: none;
    color: #ef4444;
    cursor: pointer;
    font-size: 12px;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 4px;
    transition: background 0.15s;
    white-space: nowrap;
    flex-shrink: 0;
}

.image-preview-remove:hover {
    background: #fef2f2;
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

<script>
const API_BASE_URL = 'http://localhost:3000/api';

let products = [];
let categories = [];
let currentEditId = null;
let galleryImages = []; // cached gallery images

// ========== LOAD DATA ==========

async function loadCategories() {
    try {
        const response = await fetch(`${API_BASE_URL}/categories?limit=100`);
        const result = await response.json();
        if (result.status === 'success' || result.success) {
            categories = result.data;
            updateCategorySelects();
        }
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function updateCategorySelects() {
    const select = document.getElementById('ma_danh_muc');
    if (!select) return;
    const options = '<option value="">-- Chọn danh mục --</option>' +
        categories.map(cat =>
            `<option value="${cat.ma_danh_muc}">${escapeHtml(cat.ten_danh_muc)}</option>`
        ).join('');
    select.innerHTML = options;
}

async function loadProducts(search = '', status = 'all') {
    try {
        let url = `${API_BASE_URL}/products?limit=100`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status && status !== 'all') url += `&trang_thai=${status}`;

        const response = await fetch(url);
        const result = await response.json();

        if (result.status === 'success' || result.success) {
            products = result.data;
            renderTable();
            loadStats();
        }
    } catch (error) {
        console.error('Error loading products:', error);
        showAlert('Lỗi khi tải sản phẩm', 'error');
    }
}

// ========== RENDER TABLE ==========

function renderTable() {
    const tbody = document.getElementById('products-tbody');
    if (!tbody) return;

    if (products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Không có sản phẩm nào</td></tr>';
        return;
    }

    tbody.innerHTML = products.map(product => {
        const catName = categories.find(c => c.ma_danh_muc === product.ma_danh_muc)?.ten_danh_muc || '--';
        const imgPath = product.hinh_anh ? (product.hinh_anh.startsWith('http') ? product.hinh_anh : product.hinh_anh) : '/images/logo.jpg';

        let statusBadge = '';
        if (product.trang_thai === 'dang_ban') statusBadge = '<span class="status-badge status-badge--active">Đang bán</span>';
        else if (product.trang_thai === 'ngung_ban') statusBadge = '<span class="status-badge status-badge--inactive">Ngừng bán</span>';
        else statusBadge = '<span class="status-badge status-badge--danger">Hết hàng</span>';

        return `
        <tr>
            <td>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${escapeHtml(imgPath)}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;" onerror="this.src='/images/logo.jpg'">
                    <div>
                        <div class="product-name">${escapeHtml(product.ten_san_pham)}</div>
                        <div class="product-brand">${escapeHtml(product.thuong_hieu || '--')}</div>
                    </div>
                </div>
            </td>
            <td><span class="text-secondary">${escapeHtml(catName)}</span></td>
            <td>
                <div class="price-cell">
                    ${product.gia_khuyen_mai ? `<span class="price-original">${Number(product.gia).toLocaleString('vi-VN')}đ</span><span class="price-sale">${Number(product.gia_khuyen_mai).toLocaleString('vi-VN')}đ</span>` : `<span>${Number(product.gia).toLocaleString('vi-VN')}đ</span>`}
                </div>
            </td>
            <td>${product.so_luong_ton}</td>
            <td>${statusBadge}</td>
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
    `}).join('');

    if (window.lucide) lucide.createIcons();
}

// ========== STATS ==========

async function loadStats() {
    try {
        const response = await fetch(`${API_BASE_URL}/products/stats`);
        const result = await response.json();
        if (result.status === 'success' || result.success) {
            if(document.getElementById('stat-total')) document.getElementById('stat-total').textContent = result.data.total;
            if(document.getElementById('stat-dang-ban')) document.getElementById('stat-dang-ban').textContent = result.data.dang_ban;
            if(document.getElementById('stat-sap-het')) document.getElementById('stat-sap-het').textContent = result.data.sap_het;
            if(document.getElementById('stat-het-hang')) document.getElementById('stat-het-hang').textContent = result.data.het_hang;
            if(document.getElementById('stat-khuyen-mai')) document.getElementById('stat-khuyen-mai').textContent = result.data.khuyen_mai;
            if(document.getElementById('page-subtitle')) document.getElementById('page-subtitle').textContent = result.data.total + ' sản phẩm';
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// ========== HELPERS ==========

function escapeHtml(text) {
    if (!text && text !== 0) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showAlert(message, type = 'info') {
    alert(message);
}

// ========== IMAGE PICKER ==========

// Set selected image URL into hidden input & show preview
function setSelectedImage(url, filename) {
    document.getElementById('hinh_anh').value = url || '';

    const section = document.getElementById('image-preview-section');
    const img = document.getElementById('image-preview-img');
    const name = document.getElementById('image-preview-name');

    if (url) {
        img.src = url;
        name.textContent = filename || url.split('/').pop();
        section.style.display = 'block';
        // highlight gallery item if visible
        highlightGalleryItem(url);
    } else {
        section.style.display = 'none';
        img.src = '';
        name.textContent = '';
        clearGallerySelection();
    }

    if (window.lucide) lucide.createIcons();
}

function clearSelectedImage() {
    setSelectedImage('', '');
    // reset file input
    const fileInput = document.getElementById('file-input');
    if (fileInput) fileInput.value = '';
    // reset url input
    const urlInput = document.getElementById('url-input');
    if (urlInput) urlInput.value = '';
}

// ---- Tab switching ----
function switchImageTab(tabName) {
    document.querySelectorAll('.image-picker-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.image-picker-panel').forEach(p => p.classList.remove('active'));

    document.querySelector(`.image-picker-tab[data-tab="${tabName}"]`)?.classList.add('active');
    document.getElementById(`panel-${tabName}`)?.classList.add('active');

    // Load gallery on first open
    if (tabName === 'gallery' && galleryImages.length === 0) {
        loadGalleryImages();
    }

    if (window.lucide) lucide.createIcons();
}

// ---- Upload ----
async function uploadImage(file) {
    // Validate client-side
    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!allowedTypes.includes(file.type)) {
        showAlert('Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WebP)', 'error');
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        showAlert('File quá lớn. Giới hạn tối đa 5MB.', 'error');
        return;
    }

    const progressEl = document.getElementById('upload-progress');
    const fillEl = document.getElementById('upload-progress-fill');
    const textEl = document.getElementById('upload-progress-text');

    progressEl.style.display = 'block';
    fillEl.style.width = '0%';
    textEl.textContent = 'Đang tải lên...';

    try {
        const formData = new FormData();
        formData.append('image', file);

        // Use XMLHttpRequest for progress tracking
        const result = await new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `${API_BASE_URL}/upload`);

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    fillEl.style.width = pct + '%';
                    textEl.textContent = `Đang tải lên... ${pct}%`;
                }
            });

            xhr.addEventListener('load', () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(JSON.parse(xhr.responseText));
                } else {
                    try {
                        reject(JSON.parse(xhr.responseText));
                    } catch {
                        reject({ message: 'Upload thất bại' });
                    }
                }
            });

            xhr.addEventListener('error', () => reject({ message: 'Lỗi kết nối' }));
            xhr.send(formData);
        });

        if (result.status === 'success') {
            fillEl.style.width = '100%';
            textEl.textContent = '✓ Upload thành công!';

            setSelectedImage(result.data.url, result.data.filename);

            // Add to gallery cache
            galleryImages.unshift({
                filename: result.data.filename,
                url: result.data.url,
                folder: 'products',
            });

            setTimeout(() => {
                progressEl.style.display = 'none';
            }, 1500);
        } else {
            textEl.textContent = '✗ ' + (result.message || 'Upload thất bại');
            fillEl.style.width = '0%';
        }
    } catch (error) {
        textEl.textContent = '✗ ' + (error.message || 'Upload thất bại');
        fillEl.style.width = '0%';
    }
}

// ---- Gallery ----
async function loadGalleryImages() {
    const grid = document.getElementById('gallery-grid');
    grid.innerHTML = '<div class="gallery-loading">Đang tải ảnh...</div>';

    try {
        const response = await fetch(`${API_BASE_URL}/upload/images`);
        const result = await response.json();

        if (result.status === 'success') {
            galleryImages = result.data;
            renderGallery();
        } else {
            grid.innerHTML = '<div class="gallery-empty">Không thể tải ảnh</div>';
        }
    } catch (error) {
        console.error('Error loading gallery:', error);
        grid.innerHTML = '<div class="gallery-empty">Lỗi kết nối server</div>';
    }
}

function renderGallery(filter = '') {
    const grid = document.getElementById('gallery-grid');
    const currentUrl = document.getElementById('hinh_anh').value;

    let filtered = galleryImages;
    if (filter) {
        const lower = filter.toLowerCase();
        filtered = galleryImages.filter(img => img.filename.toLowerCase().includes(lower));
    }

    if (filtered.length === 0) {
        grid.innerHTML = '<div class="gallery-empty">Không tìm thấy ảnh nào</div>';
        return;
    }

    grid.innerHTML = filtered.map(img => {
        const selected = currentUrl === img.url ? 'selected' : '';
        return `<div class="gallery-item ${selected}" data-url="${escapeHtml(img.url)}" data-filename="${escapeHtml(img.filename)}" onclick="selectGalleryImage(this)">
            <img src="${escapeHtml(img.url)}" alt="${escapeHtml(img.filename)}" loading="lazy" onerror="this.parentElement.style.display='none'">
        </div>`;
    }).join('');
}

function selectGalleryImage(el) {
    const url = el.getAttribute('data-url');
    const filename = el.getAttribute('data-filename');
    setSelectedImage(url, filename);
}

function highlightGalleryItem(url) {
    document.querySelectorAll('.gallery-item').forEach(item => {
        item.classList.toggle('selected', item.getAttribute('data-url') === url);
    });
}

function clearGallerySelection() {
    document.querySelectorAll('.gallery-item.selected').forEach(item => {
        item.classList.remove('selected');
    });
}

// ========== MODAL ==========

function showModal(title, productId = null) {
    currentEditId = productId;
    if(document.getElementById('modal-title')) document.getElementById('modal-title').textContent = title;
    if(document.getElementById('product-id')) document.getElementById('product-id').value = productId || '';

    // Reset image picker
    clearSelectedImage();
    switchImageTab('upload');

    if (productId) {
        const product = products.find(p => p.ma_san_pham === productId);
        if (product) {
            document.getElementById('ten_san_pham').value = product.ten_san_pham;
            document.getElementById('ma_danh_muc').value = product.ma_danh_muc || '';
            document.getElementById('gia').value = product.gia || '';
            document.getElementById('gia_khuyen_mai').value = product.gia_khuyen_mai || '';
            document.getElementById('so_luong_ton').value = product.so_luong_ton || 0;
            if(document.getElementById('thuong_hieu')) document.getElementById('thuong_hieu').value = product.thuong_hieu || '';
            if(document.getElementById('xuat_xu')) document.getElementById('xuat_xu').value = product.xuat_xu || '';
            if(document.getElementById('loai_da_phu_hop')) document.getElementById('loai_da_phu_hop').value = product.loai_da_phu_hop || '';
            if(document.getElementById('mo_ta')) document.getElementById('mo_ta').value = product.mo_ta || '';
            if(document.getElementById('thanh_phan')) document.getElementById('thanh_phan').value = product.thanh_phan || '';
            if(document.getElementById('huong_dan_su_dung')) document.getElementById('huong_dan_su_dung').value = product.huong_dan_su_dung || '';
            if(document.getElementById('trang_thai_form')) document.getElementById('trang_thai_form').value = product.trang_thai || 'dang_ban';

            // Set image if exists
            if (product.hinh_anh) {
                setSelectedImage(product.hinh_anh, product.hinh_anh.split('/').pop());
            }
        }
    } else {
        document.getElementById('form-product').reset();
        if(document.getElementById('trang_thai_form')) document.getElementById('trang_thai_form').value = 'dang_ban';
    }

    document.getElementById('modal-product').style.display = 'block';
    if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'block';
}

function closeModal() {
    document.getElementById('modal-product').style.display = 'none';
    if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'none';
    document.getElementById('form-product').reset();
    currentEditId = null;
    clearErrors();
    clearSelectedImage();
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
        const response = await fetch(`${API_BASE_URL}/products/${id}`, { method: 'DELETE' });
        const result = await response.json();
        if (result.status === 'success' || result.success) {
            showAlert('Xóa sản phẩm thành công', 'success');
            loadProducts();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error deleting product:', error);
        showAlert('Lỗi khi xóa sản phẩm', 'error');
    }
}

// Form submit
const formProduct = document.getElementById('form-product');
if (formProduct) {
    formProduct.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearErrors();

        const formData = {
            ten_san_pham: document.getElementById('ten_san_pham').value.trim(),
            ma_danh_muc: document.getElementById('ma_danh_muc').value || null,
            hinh_anh: document.getElementById('hinh_anh').value.trim() || null,
            gia: document.getElementById('gia').value,
            gia_khuyen_mai: document.getElementById('gia_khuyen_mai').value || null,
            so_luong_ton: document.getElementById('so_luong_ton').value || 0,
            thuong_hieu: document.getElementById('thuong_hieu') ? document.getElementById('thuong_hieu').value.trim() : null,
            xuat_xu: document.getElementById('xuat_xu') ? document.getElementById('xuat_xu').value.trim() : null,
            loai_da_phu_hop: document.getElementById('loai_da_phu_hop') ? document.getElementById('loai_da_phu_hop').value.trim() : null,
            mo_ta: document.getElementById('mo_ta') ? document.getElementById('mo_ta').value.trim() : null,
            thanh_phan: document.getElementById('thanh_phan') ? document.getElementById('thanh_phan').value.trim() : null,
            huong_dan_su_dung: document.getElementById('huong_dan_su_dung') ? document.getElementById('huong_dan_su_dung').value.trim() : null,
            trang_thai: document.getElementById('trang_thai_form') ? document.getElementById('trang_thai_form').value : 'dang_ban'
        };

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
        const url = productId ? `${API_BASE_URL}/products/${productId}` : `${API_BASE_URL}/products`;
        const method = productId ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            const result = await response.json();

            if (result.status === 'success' || result.success) {
                showAlert(result.message || 'Lưu thành công', 'success');
                closeModal();
                loadProducts();
            } else {
                showAlert(result.message || 'Lỗi lưu sản phẩm', 'error');
            }
        } catch (error) {
            console.error('Error saving product:', error);
            showAlert('Lỗi khi lưu sản phẩm', 'error');
        }
    });
}

// ========== EVENT LISTENERS ==========

// Modal buttons
if(document.getElementById('btn-add-product')) document.getElementById('btn-add-product').addEventListener('click', () => showModal('Thêm sản phẩm'));
if(document.getElementById('btn-close-modal')) document.getElementById('btn-close-modal').addEventListener('click', closeModal);
if(document.getElementById('btn-cancel-modal')) document.getElementById('btn-cancel-modal').addEventListener('click', closeModal);
if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').addEventListener('click', closeModal);

// Search
const searchInput = document.getElementById('product-search-input');
if(searchInput) {
    searchInput.addEventListener('input', (e) => {
        const status = document.querySelector('.role-tab.active')?.dataset.status || 'all';
        loadProducts(e.target.value, status);
    });
}

// Status tabs
const statusTabs = document.querySelectorAll('.role-tab');
statusTabs.forEach(tab => {
    tab.addEventListener('click', (e) => {
        statusTabs.forEach(t => t.classList.remove('active'));
        e.target.classList.add('active');
        const search = document.getElementById('product-search-input')?.value || '';
        loadProducts(search, e.target.dataset.status);
    });
});

// ---- Image Picker Tab Switching ----
document.querySelectorAll('.image-picker-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        switchImageTab(tab.getAttribute('data-tab'));
    });
});

// ---- Drop Zone ----
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');

if (dropZone && fileInput) {
    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragenter', (e) => {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });

    dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            uploadImage(files[0]);
        }
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length > 0) {
            uploadImage(fileInput.files[0]);
        }
    });
}

// ---- Gallery Search ----
const gallerySearchInput = document.getElementById('gallery-search-input');
if (gallerySearchInput) {
    gallerySearchInput.addEventListener('input', (e) => {
        renderGallery(e.target.value);
    });
}

// ---- URL Apply ----
const btnApplyUrl = document.getElementById('btn-apply-url');
if (btnApplyUrl) {
    btnApplyUrl.addEventListener('click', () => {
        const urlInput = document.getElementById('url-input');
        const url = urlInput?.value.trim();
        if (url) {
            setSelectedImage(url, url.split('/').pop());
        } else {
            showAlert('Vui lòng nhập URL ảnh', 'error');
        }
    });
}

// ---- Remove Image ----
const btnRemoveImage = document.getElementById('btn-remove-image');
if (btnRemoveImage) {
    btnRemoveImage.addEventListener('click', clearSelectedImage);
}

// ========== INIT ==========

document.addEventListener('DOMContentLoaded', async function() {
    if (window.lucide) lucide.createIcons();
    await loadCategories();
    await loadProducts();
});
</script>
@endsection

