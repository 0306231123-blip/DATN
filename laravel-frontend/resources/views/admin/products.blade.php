@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Quản lý sản phẩm')
@section('page-subtitle', 'Đang tải...')

@section('content')
<!-- Action Bar -->
<div class="view-tabs" style="margin-bottom: 20px; border-bottom: 1px solid #e2e8f0; display: flex; gap: 20px;">
    <button class="view-tab active" data-view="products" onclick="switchView('products')" style="padding: 10px 20px; font-weight: bold; background: none; border: none; border-bottom: 2px solid var(--primary-color); color: var(--primary-color); cursor: pointer;">Sản Phẩm</button>
    <button class="view-tab" data-view="inventory" onclick="switchView('inventory')" style="padding: 10px 20px; font-weight: bold; background: none; border: none; border-bottom: 2px solid transparent; color: #64748b; cursor: pointer;">Kho Hàng</button>
    <button class="view-tab" data-view="vouchers" onclick="switchView('vouchers')" style="padding: 10px 20px; font-weight: bold; background: none; border: none; border-bottom: 2px solid transparent; color: #64748b; cursor: pointer;">Khuyến Mãi</button>
</div>

<div id="view-products">
<div class="page-action-bar" id="products-action-bar">
    <div class="action-bar-left">
        <button class="btn btn-primary" id="btn-publish-product" onclick="openPublishModal()">
            <i data-lucide="plus" class="icon-xs"></i>
            <span>Đăng bán từ Kho</span>
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
            <button class="role-tab" data-status="sap_het_hang">Sắp hết</button>
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
                    <th>Giá bán</th>
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
    <div id="products-pagination" class="pagination-container" style="display: none;"></div>
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
</div> <!-- End view-products -->

<!-- Bắt đầu view-inventory -->
<div id="view-inventory" style="display: none;">
    <div class="page-action-bar">
        <div class="action-bar-left">
            <h3>Quản lý Tồn Kho</h3>
            <button class="btn btn-primary" onclick="showModal('Thêm sản phẩm gốc')" style="margin-left: 15px;">
                <i data-lucide="plus" class="icon-xs"></i> Thêm sản phẩm mới
            </button>
            <button class="btn" onclick="document.getElementById('excel-upload').click()" style="margin-left: 10px; background-color: #10b981; color: white; border-color: #10b981;">
                <i data-lucide="file-spreadsheet" class="icon-xs"></i>
                <span>Nhập từ Excel</span>
            </button>
            <input type="file" id="excel-upload" accept=".xlsx, .xls, .csv" style="display: none;" onchange="handleExcelUpload(event)">
            <button class="btn" onclick="downloadSampleExcel()" style="margin-left: 10px; background-color: #6366f1; color: white; border-color: #6366f1;">
                <i data-lucide="download" class="icon-xs"></i>
                <span>Tải file mẫu</span>
            </button>
        </div>
        <div class="action-bar-right">
            <button class="btn btn-secondary" onclick="viewAllLogs()" style="margin-right: 10px;">
                <i data-lucide="clock" class="icon-xs"></i> Lịch sử chung
            </button>
            <button class="btn btn-primary" onclick="openBulkImportModal()" style="margin-right: 10px;">
                <i data-lucide="download" class="icon-xs"></i> Nhập hàng loạt
            </button>
            <button class="btn btn-secondary" onclick="loadInventory()">
                <i data-lucide="refresh-cw" class="icon-xs"></i> Tải lại
            </button>
        </div>
    </div>
    <div class="data-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Sản phẩm / Biến thể</th>
                    <th>Tồn kho</th>
                    <th>Giá nhập</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="inventory-tbody">
            </tbody>
        </table>
    </div>
    <div id="inventory-pagination" class="pagination-container" style="display: none;"></div>
</div>

<!-- Bắt đầu view-vouchers -->
<div id="view-vouchers" style="display: none;">
    <div class="page-action-bar">
        <div class="action-bar-left">
            <button class="btn btn-primary" onclick="openVoucherModal()">
                <i data-lucide="plus" class="icon-xs"></i> Thêm khuyến mãi
            </button>
        </div>
        <div class="action-bar-right">
            <div class="search-box">
                <i data-lucide="search" class="icon-xs search-icon"></i>
                <input type="text" placeholder="Tìm kiếm voucher..." class="search-input" id="voucher-search-input" onkeyup="if(event.key === 'Enter') loadVouchers()">
            </div>
            <button class="btn btn-secondary" onclick="loadVouchers()" style="margin-left: 10px;">
                <i data-lucide="refresh-cw" class="icon-xs"></i>
            </button>
        </div>
    </div>
    
    <div class="data-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã Code</th>
                    <th>Loại / Giá trị</th>
                    <th>Áp dụng cho</th>
                    <th>Số lượng</th>
                    <th>Hạn sử dụng</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody id="vouchers-tbody">
                <tr><td colspan="7" style="text-align: center;">Đang tải...</td></tr>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div id="vouchers-pagination" class="pagination-container" style="display: none;"></div>
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
                <input type="hidden" id="anh_san_pham" name="anh_san_pham" value="">

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
                    <input type="text" id="thuong_hieu" name="thuong_hieu" class="form-control" list="danh_sach_thuong_hieu">
                    <datalist id="danh_sach_thuong_hieu"></datalist>
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

            <div class="form-group" style="margin-top: 20px; background: #f8fafc; padding: 15px; border-radius: 6px;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin: 0;">
                    <input type="checkbox" id="co_bien_the" name="co_bien_the" onchange="toggleVariants()" style="width: 18px; height: 18px;"> 
                    <strong>Sản phẩm có nhiều phân loại (màu sắc, dung tích...)</strong>
                </label>
            </div>

            <div id="variants-section" style="display: none; margin-top: 15px; border: 1px solid #e2e8f0; padding: 15px; border-radius: 8px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                    <h3 style="margin: 0; font-size: 15px;">Danh sách biến thể</h3>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="addVariantRow()">+ Thêm phân loại</button>
                </div>
                <div class="table-wrapper" style="max-height: 300px; overflow-y: auto;">
                    <table class="admin-table" id="variants-table">
                        <thead>
                            <tr>
                                <th>Phân loại</th>
                                <th>SKU</th>
                                <th>Giá *</th>
                                <th>Giá KM</th>
                                <th>Tồn kho</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="variants-tbody">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-cancel-modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Publish Product (Multi-select) -->
<div class="modal" id="modal-publish" style="display: none; z-index: 1002;">
    <div class="modal-content modal-content--extra-wide">
        <div class="modal-header">
            <h2>Đăng bán sản phẩm từ Kho</h2>
            <button class="modal-close" type="button" onclick="closePublishModal()">&times;</button>
        </div>
        <form id="form-publish" class="form">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-size: 14px; color: var(--text-muted);">Chọn sản phẩm và điền giá bán để đăng lên trang web.</span>
                <label style="display: flex; align-items: center; gap: 6px; cursor: pointer; font-size: 13px; color: var(--primary-color);">
                    <input type="checkbox" id="publish-select-all" onchange="togglePublishSelectAll(this.checked)" style="width: 16px; height: 16px;">
                    Chọn tất cả
                </label>
            </div>
            <div class="table-wrapper" style="max-height: 65vh; overflow-y: auto; margin-bottom: 15px;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 40px;"></th>
                            <th>Sản phẩm</th>
                            <th>Tồn kho</th>
                            <th style="width: 130px;">Giá nhập</th>
                            <th style="width: 160px;">Giá bán (VNĐ) *</th>
                            <th style="width: 160px;">Giá KM (Tùy chọn)</th>
                        </tr>
                    </thead>
                    <tbody id="publish-tbody">
                        <tr><td colspan="6" style="text-align: center;">Đang tải...</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closePublishModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Xác nhận đăng bán</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Overlay -->
<div class="modal-overlay" id="modal-overlay" style="display: none;" onclick="closeAllModals()"></div>

<!-- Modal Nhập/Xuất Kho Đơn Lẻ -->
<div class="modal" id="modal-inv-single" style="display: none; z-index: 1002;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="inv-single-title">Nhập kho</h2>
            <button class="modal-close" type="button" onclick="closeInvModal()">&times;</button>
        </div>
        <form id="form-inv-single" class="form">
            <input type="hidden" id="inv-single-sp-id">
            <input type="hidden" id="inv-single-bt-id">
            <input type="hidden" id="inv-single-type">
            <div class="form-group">
                <label>Sản phẩm</label>
                <input type="text" id="inv-single-name" class="form-control" readonly style="background: #f8fafc;">
            </div>
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label>Số lượng *</label>
                    <input type="number" id="inv-single-sl" class="form-control" min="1" placeholder="Nhập số lượng...">
                </div>
                <div class="form-group" id="inv-single-gia-group" style="flex: 1;">
                    <label>Giá nhập (VNĐ)</label>
                    <input type="number" id="inv-single-gia" class="form-control" min="0" placeholder="Tùy chọn...">
                </div>
            </div>
            <div class="form-group">
                <label>Ghi chú</label>
                <input type="text" id="inv-single-note" class="form-control" placeholder="Ví dụ: Nhập hàng từ nhà cung cấp X...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeInvModal()">Hủy</button>
                <button type="submit" class="btn btn-primary" id="inv-single-submit-btn">Xác nhận Nhập Kho</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="modal-bulk-import" style="display: none; z-index: 1002;">
    <div class="modal-content modal-content--extra-wide">
        <div class="modal-header">
            <h2>Nhập kho hàng loạt</h2>
            <button class="modal-close" type="button" onclick="closeBulkImportModal()">&times;</button>
        </div>
        <form id="form-bulk-import" class="form">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-size: 14px; color: var(--text-muted);">Vui lòng chọn sản phẩm và điền số lượng.</span>
            </div>
            <div class="table-wrapper" style="max-height: 65vh; overflow-y: auto; margin-bottom: 20px;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm / Phân loại</th>
                            <th>SKU</th>
                            <th>Tồn kho</th>
                            <th style="width: 100px; min-width: 80px;">SL nhập</th>
                            <th style="width: 200px; min-width: 170px;">Giá nhập (VNĐ)</th>
                        </tr>
                    </thead>
                    <tbody id="bulk-import-tbody">
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                <label>Ghi chú</label>
                <input type="text" id="bulk-note" class="form-control" placeholder="Ví dụ: Nhập hàng tháng 10...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeBulkImportModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Xác nhận Nhập Kho</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Thẻ Kho -->
<div class="modal" id="modal-logs" style="display: none; z-index: 1002;">
    <div class="modal-content modal-content--extra-wide">
        <div class="modal-header">
            <h2>Lịch sử biến động kho</h2>
            <button class="modal-close" onclick="closeLogsModal()">&times;</button>
        </div>
        <div class="table-wrapper" style="max-height: 65vh; overflow-y: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Ngày</th>
                        <th>Sản phẩm / Phân loại</th>
                        <th>Thao tác</th>
                        <th>Thay đổi</th>
                        <th>Tồn cuối</th>
                        <th>Giá nhập</th>
                        <th>Ghi chú</th>
                    </tr>
                </thead>
                <tbody id="logs-tbody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Thêm/Sửa Khuyến Mãi -->
<div class="modal" id="modal-voucher" style="display: none; z-index: 1002;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="voucher-modal-title">Thêm Khuyến Mãi</h2>
            <button class="modal-close" onclick="closeVoucherModal()">&times;</button>
        </div>
        <form id="form-voucher" class="form" onsubmit="saveVoucher(event)">
            <input type="hidden" id="voucher-id">
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label>Mã Code *</label>
                    <input type="text" id="voucher-code" class="form-control" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Loại Giảm *</label>
                    <select id="voucher-type" class="form-control" required>
                        <option value="tien_mat">Tiền mặt</option>
                        <option value="phan_tram">Phần trăm</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label>Giá trị giảm *</label>
                    <input type="number" id="voucher-value" class="form-control" required min="1">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Đơn tối thiểu</label>
                    <input type="number" id="voucher-min-order" class="form-control" value="0" min="0">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label>Giảm tối đa (Nếu theo %)</label>
                    <input type="number" id="voucher-max-discount" class="form-control" min="0">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Số lượng</label>
                    <input type="number" id="voucher-quantity" class="form-control" value="0" min="0">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label>Áp dụng cho Danh Mục</label>
                    <select id="voucher-category" class="form-control">
                        <option value="">-- Tất cả danh mục --</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Áp dụng cho Sản Phẩm</label>
                    <select id="voucher-product" class="form-control">
                        <option value="">-- Tất cả sản phẩm --</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label>Ngày bắt đầu</label>
                    <input type="datetime-local" id="voucher-start" class="form-control">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Ngày kết thúc *</label>
                    <input type="datetime-local" id="voucher-end" class="form-control" required>
                </div>
            </div>
            
            <div class="form-group">
                <label>Trạng thái</label>
                <select id="voucher-status" class="form-control">
                    <option value="hoat_dong">Hoạt động</option>
                    <option value="tam_dung">Tạm dừng</option>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeVoucherModal()">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu Voucher</button>
            </div>
        </form>
    </div>
</div>
@endsection


@section('scripts')
@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-products.css') }}">
@endsection

<!-- Cài đặt thư viện SheetJS để đọc file Excel -->
<script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>

<script>
const API_BASE_URL = 'http://localhost:3000/api';

let products = [];
let categories = [];
let allBrands = [];
let currentEditId = null;
let galleryImages = []; // cached gallery images

let currentProductsPage = 1;
let currentInventoryPage = 1;
let currentVouchersPage = 1;

// ========== SHARED PAGINATION ==========
function renderPagination(pagination, containerId, pageChangeCallbackName) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const currentPage = pagination.current_page || pagination.page || 1;
    const totalPages = pagination.last_page || pagination.pages || 1;
    const total = pagination.total || 0;
    
    container.style.display = 'flex';
    let html = `<div class="pagination-info">Hiển thị trang ${currentPage} / ${totalPages} (Tổng: ${total})</div>`;
    
    html += '<div class="pagination">';
    html += `<button class="page-btn" ${currentPage <= 1 ? 'disabled' : ''} onclick="${pageChangeCallbackName}(${currentPage - 1})">‹</button>`;
    
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || Math.abs(i - currentPage) <= 1) {
            html += `<button class="page-btn ${i === currentPage ? 'active' : ''}" onclick="${pageChangeCallbackName}(${i})">${i}</button>`;
        } else if (i === currentPage - 2 || i === currentPage + 2) {
            html += `<span style="padding: 0 4px; color: var(--text-muted);">...</span>`;
        }
    }
    
    html += `<button class="page-btn" ${currentPage >= totalPages ? 'disabled' : ''} onclick="${pageChangeCallbackName}(${currentPage + 1})">›</button>`;
    html += '</div>';
    
    container.innerHTML = html;
}

// ========== LOAD DATA ==========

async function loadCategories() {
    try {
        const response = await fetch(`${API_BASE_URL}/categories?per_page=100`);
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

async function loadBrandsList() {
    try {
        const response = await fetch(`${API_BASE_URL}/products/brands`);
        const result = await response.json();
        if (result.status === 'success' || result.success) {
            allBrands = result.data;
            updateBrandDatalist();
        }
    } catch (error) {
        console.error('Error loading brands:', error);
    }
}

function updateBrandDatalist() {
    const datalist = document.getElementById('danh_sach_thuong_hieu');
    if (!datalist) return;
    const options = allBrands.map(brand => 
        `<option value="${escapeHtml(brand)}"></option>`
    ).join('');
    datalist.innerHTML = options;
}

async function loadProducts(search = '', status = 'all', page = 1) {
    try {
        const settings = window.getGlobalSettings ? window.getGlobalSettings() : { perPage: 15, lowStockThreshold: 20 };
        let url = `${API_BASE_URL}/products?per_page=${settings.perPage}&page=${page}&low_stock_threshold=${settings.lowStockThreshold}`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        if (status && status !== 'all') url += `&trang_thai=${status}`;

        const response = await fetch(url);
        const result = await response.json();

        if (result.status === 'success' || result.success) {
            products = result.data;
            renderTable();
            if (result.pagination) {
                renderPagination(result.pagination, 'products-pagination', 'changeProductsPage');
            }
            loadStats();
        }
    } catch (error) {
        console.error('Error loading products:', error);
        showAlert('Lỗi khi tải sản phẩm', 'error');
    }
}

function changeProductsPage(page) {
    currentProductsPage = page;
    const search = document.getElementById('product-search-input').value;
    const status = document.querySelector('.role-tab.active')?.dataset.status || 'all';
    loadProducts(search, status, page);
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
        
        let primaryImage = product.anh_san_pham;
        if (!primaryImage && product.danh_sach_anh && product.danh_sach_anh.length > 0) {
            primaryImage = product.danh_sach_anh[0].duong_dan_anh;
        }
        const imgPath = getImageUrl(primaryImage);

        const settings = window.getGlobalSettings ? window.getGlobalSettings() : { lowStockThreshold: 20 };
        let statusBadge = '';
        if (product.trang_thai === 'ngung_ban') {
            statusBadge = '<span class="status-badge status-badge--inactive">Ngừng bán</span>';
        } else if (product.so_luong_ton === 0) {
            statusBadge = '<span class="status-badge status-badge--danger">Hết hàng</span>';
        } else if (product.so_luong_ton < settings.lowStockThreshold) {
            statusBadge = '<span class="status-badge" style="background-color: #fff7ed; color: #c2410c;">Sắp hết hàng</span>';
        } else {
            statusBadge = '<span class="status-badge status-badge--active">Đang bán</span>';
        }

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
                    ${product.co_bien_the ? `<span>${Number(product.gia).toLocaleString('vi-VN')} VNĐ - ${Number(product.gia_max).toLocaleString('vi-VN')} VNĐ</span>` : (product.gia_khuyen_mai ? `<span class="price-original">${Number(product.gia).toLocaleString('vi-VN')} VNĐ</span><span class="price-sale">${Number(product.gia_khuyen_mai).toLocaleString('vi-VN')} VNĐ</span>` : `<span>${Number(product.gia).toLocaleString('vi-VN')} VNĐ</span>`)}
                </div>
            </td>
            <td>${product.so_luong_ton}</td>
            <td>${statusBadge}</td>
            <td>
                <div class="action-btns">
                    <button class="icon-action-btn" title="Ngừng hiển thị" onclick="unpublishProduct(${product.ma_san_pham})">
                        <i data-lucide="eye-off" class="icon-xs"></i>
                    </button>
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
        const settings = window.getGlobalSettings ? window.getGlobalSettings() : { lowStockThreshold: 20 };
        const response = await fetch(`${API_BASE_URL}/products/stats?low_stock_threshold=${settings.lowStockThreshold}`);
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

function closeAllModals() {
    if (typeof closeModal === 'function') closeModal();
    if (typeof closeBulkImportModal === 'function') closeBulkImportModal();
    if (typeof closeLogsModal === 'function') closeLogsModal();
    if (typeof closeInvModal === 'function') closeInvModal();
}

function escapeHtml(text) {
    if (!text && text !== 0) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getImageUrl(path) {
    if (!path) return '/images/logo.jpg';
    
    // Nếu là ảnh data: base64 thì giữ nguyên
    if (path.startsWith('data:')) return path;

    // Loại bỏ hostname nếu path là URL tuyệt đối (vd: http://localhost:3000/images/...)
    if (path.startsWith('http://') || path.startsWith('https://')) {
        try {
            path = new URL(path).pathname;
        } catch(e) {}
    }
    
    // Nếu path đã có /images/ hoặc images/ thì chỉ cần thêm / nếu thiếu
    if (path.startsWith('/images/')) return path;
    if (path.startsWith('images/')) return '/' + path;
    
    // Nếu chỉ lưu tên file (vd: cerave.jpg), cần thêm /images/
    return '/images/' + (path.startsWith('/') ? path.substring(1) : path);
}

// ========== IMAGE PICKER ==========

// Set selected image URL into hidden input & show preview
function setSelectedImage(url, filename) {
    document.getElementById('anh_san_pham').value = url || '';

    const section = document.getElementById('image-preview-section');
    const img = document.getElementById('image-preview-img');
    const name = document.getElementById('image-preview-name');

    if (url) {
        img.src = getImageUrl(url);
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
    const currentUrl = document.getElementById('anh_san_pham').value;

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
        const fullUrl = getImageUrl(img.url);
        const selected = currentUrl === fullUrl ? 'selected' : '';
        return `<div class="gallery-item ${selected}" data-url="${escapeHtml(fullUrl)}" data-filename="${escapeHtml(img.filename)}" onclick="selectGalleryImage(this)">
            <img src="${escapeHtml(fullUrl)}" alt="${escapeHtml(img.filename)}" loading="lazy" onerror="this.parentElement.style.display='none'">
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
            if (product.anh_san_pham) {
                setSelectedImage(product.anh_san_pham, product.anh_san_pham.split('/').pop());
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

// Delete Product
async function deleteProduct(id) {
    const confirmDelete = await window.showCustomDialog({
        title: 'Xóa sản phẩm',
        message: 'Bạn chắc chắn muốn xóa sản phẩm này?',
        isPrompt: false
    });
    if (!confirmDelete) return;
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
            anh_san_pham: document.getElementById('anh_san_pham').value.trim() || null,
            gia: document.getElementById('gia').value,
            gia_khuyen_mai: document.getElementById('gia_khuyen_mai').value || null,
            so_luong_ton: document.getElementById('so_luong_ton').value || 0,
            thuong_hieu: document.getElementById('thuong_hieu') ? document.getElementById('thuong_hieu').value.trim() : null,
            xuat_xu: document.getElementById('xuat_xu') ? document.getElementById('xuat_xu').value.trim() : null,
            loai_da_phu_hop: document.getElementById('loai_da_phu_hop') ? document.getElementById('loai_da_phu_hop').value.trim() : null,
            mo_ta: document.getElementById('mo_ta') ? document.getElementById('mo_ta').value.trim() : null,
            thanh_phan: document.getElementById('thanh_phan') ? document.getElementById('thanh_phan').value.trim() : null,
            huong_dan_su_dung: document.getElementById('huong_dan_su_dung') ? document.getElementById('huong_dan_su_dung').value.trim() : null,
            trang_thai: document.getElementById('trang_thai_form') ? document.getElementById('trang_thai_form').value : 'dang_ban',
            variants: []
        };

        const cbBienThe = document.getElementById('co_bien_the');
        if (cbBienThe && cbBienThe.checked) {
            const rows = document.querySelectorAll('.variant-row');
            rows.forEach(r => {
                const ma_bien_the = r.dataset.id || null;
                const ten_bien_the = r.querySelector('.v-ten').value;
                const sku = r.querySelector('.v-sku').value;
                const gia = r.querySelector('.v-gia').value;
                const gia_khuyen_mai = r.querySelector('.v-giakm').value;
                const so_luong_ton = r.querySelector('.v-ton').value;
                
                formData.variants.push({
                    ma_bien_the, ten_bien_the, sku, gia, gia_khuyen_mai, so_luong_ton,
                    thuoc_tinh: { ten: ten_bien_the }
                });
            });
            formData.gia = formData.variants[0]?.gia || 0;
            formData.so_luong_ton = 0;
        }

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
    await loadBrandsList();
    await loadProducts();
});
// ==================== NEW JS FOR VARIANTS & INVENTORY ====================

function switchView(view) {
    document.querySelectorAll('.view-tab').forEach(t => {
        t.style.borderBottomColor = 'transparent';
        t.style.color = '#64748b';
    });
    const activeTab = document.querySelector(`.view-tab[data-view="${view}"]`);
    if(activeTab) {
        activeTab.style.borderBottomColor = 'var(--primary-color)';
        activeTab.style.color = 'var(--primary-color)';
    }

    document.getElementById('view-products').style.display = view === 'products' ? 'block' : 'none';
    document.getElementById('view-inventory').style.display = view === 'inventory' ? 'block' : 'none';
    document.getElementById('view-vouchers').style.display = view === 'vouchers' ? 'block' : 'none';

    if (view === 'inventory') {
        loadInventory(currentInventoryPage);
    } else if (view === 'vouchers') {
        loadVouchers(currentVouchersPage);
    }
}

// -- Variants Logic --
function toggleVariants() {
    const checked = document.getElementById('co_bien_the').checked;
    document.getElementById('variants-section').style.display = checked ? 'block' : 'none';
    
    // Disable main inputs if variants enabled
    document.getElementById('gia').disabled = checked;
    document.getElementById('gia_khuyen_mai').disabled = checked;
    document.getElementById('so_luong_ton').disabled = checked;
}

function addVariantRow(data = {}) {
    const tbody = document.getElementById('variants-tbody');
    const tr = document.createElement('tr');
    tr.className = 'variant-row';
    tr.dataset.id = data.ma_bien_the || '';
    tr.innerHTML = `
        <td><input type="text" class="form-control v-ten" placeholder="Màu đỏ..." value="${escapeHtml(data.ten_bien_the || '')}"></td>
        <td><input type="text" class="form-control v-sku" placeholder="SKU..." value="${escapeHtml(data.sku || '')}"></td>
        <td><input type="number" class="form-control v-gia" required min="0" value="${data.gia || ''}"></td>
        <td><input type="number" class="form-control v-giakm" min="0" value="${data.gia_khuyen_mai || ''}"></td>
        <td><input type="number" class="form-control v-ton" min="0" value="${data.so_luong_ton || 0}"></td>
        <td><button type="button" class="btn btn-sm btn-secondary" onclick="this.closest('tr').remove()">Xóa</button></td>
    `;
    tbody.appendChild(tr);
}

// Hook into showModal to load variants
const originalShowModal = showModal;
showModal = function(title, productId = null) {
    originalShowModal(title, productId);
    document.getElementById('variants-tbody').innerHTML = '';
    const cb = document.getElementById('co_bien_the');
    cb.checked = false;
    
    if (productId) {
        const product = products.find(p => p.ma_san_pham === productId);
        if (product && product.co_bien_the && product.bien_the) {
            cb.checked = true;
            product.bien_the.forEach(v => addVariantRow(v));
        }
    }
    toggleVariants();
};

// -- Inventory Logic --
async function loadInventory(page = 1) {
    const tbody = document.getElementById('inventory-tbody');
    tbody.innerHTML = '<tr><td colspan="4" style="text-align: center;">Đang tải...</td></tr>';
    try {
        const settings = window.getGlobalSettings ? window.getGlobalSettings() : { perPage: 15 };
        const response = await fetch(`${API_BASE_URL}/products?per_page=${settings.perPage}&page=${page}&is_inventory=true`);
        const result = await response.json();
        if (result.status === 'success' || result.success) {
            let html = '';
            if (result.pagination) {
                renderPagination(result.pagination, 'inventory-pagination', 'changeInventoryPage');
            }
            result.data.forEach(p => {
                if (p.co_bien_the && p.bien_the && p.bien_the.length > 0) {
                    p.bien_the.forEach(v => {
                        html += `<tr>
                            <td>${escapeHtml(v.sku || '--')}</td>
                            <td>${escapeHtml(p.ten_san_pham)} - <strong>${escapeHtml(v.ten_bien_the)}</strong></td>
                            <td>${v.so_luong_ton}</td>
                            <td>${v.gia_nhap ? Number(v.gia_nhap).toLocaleString('vi-VN') + ' VNĐ' : '--'}</td>
                            <td>
                                <div class="action-btns">
                                    <button class="btn btn-sm btn-primary" onclick="openInvModal(${p.ma_san_pham}, ${v.ma_bien_the}, 'import', '${escapeHtml(p.ten_san_pham)} - ${escapeHtml(v.ten_bien_the)}')" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="package-plus" style="width:14px;height:14px;"></i> Nhập kho</button>
                                    <button class="icon-action-btn icon-action-btn--danger" title="Xóa" onclick="deleteInventoryProduct(${p.ma_san_pham})">
                                        <i data-lucide="trash-2" class="icon-xs"></i>
                                    </button>
                                    <button class="icon-action-btn" title="Lịch sử" onclick="viewLogs(${p.ma_san_pham}, ${v.ma_bien_the})">
                                        <i data-lucide="clock" class="icon-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                    });
                } else {
                    html += `<tr>
                        <td>${escapeHtml(p.sku || '--')}</td>
                        <td>${escapeHtml(p.ten_san_pham)}</td>
                        <td>${p.so_luong_ton}</td>
                        <td>${p.gia_nhap ? Number(p.gia_nhap).toLocaleString('vi-VN') + ' VNĐ' : '--'}</td>
                        <td>
                            <div class="action-btns">
                                <button class="btn btn-sm btn-primary" onclick="openInvModal(${p.ma_san_pham}, null, 'import', '${escapeHtml(p.ten_san_pham)}')" style="display:inline-flex;align-items:center;gap:4px;"><i data-lucide="package-plus" style="width:14px;height:14px;"></i> Nhập kho</button>
                                <button class="icon-action-btn icon-action-btn--danger" title="Xóa" onclick="deleteInventoryProduct(${p.ma_san_pham})">
                                    <i data-lucide="trash-2" class="icon-xs"></i>
                                </button>
                                <button class="icon-action-btn" title="Lịch sử" onclick="viewLogs(${p.ma_san_pham}, null)">
                                    <i data-lucide="clock" class="icon-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>`;
                }
            });
            tbody.innerHTML = html || '<tr><td colspan="5">Không có dữ liệu</td></tr>';
            if (window.lucide) lucide.createIcons();
        }
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="4">Lỗi tải dữ liệu</td></tr>';
    }
}
// Bulk Import Logic
async function openBulkImportModal() {
    document.getElementById('modal-bulk-import').style.display = 'block';
    if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'block';
    
    document.getElementById('bulk-note').value = '';
    
    const tbody = document.getElementById('bulk-import-tbody');
    tbody.innerHTML = '<tr><td colspan="5" style="text-align: center;">Đang tải...</td></tr>';
    
    try {
        const response = await fetch(`${API_BASE_URL}/products?per_page=1000`);
        const result = await response.json();
        if (result.status === 'success' || result.success) {
            let html = '';
            result.data.forEach(p => {
                if (p.co_bien_the && p.bien_the && p.bien_the.length > 0) {
                    p.bien_the.forEach(v => {
                        // Tính giá nhập tự động (khoảng 65% giá bán lẻ)
                        const giaNhap = Math.round(((v.gia || 0) * 0.65) / 1000) * 1000;
                        html += `<tr class="bulk-item" data-sp="${p.ma_san_pham}" data-bt="${v.ma_bien_the}">
                            <td>${escapeHtml(p.ten_san_pham)} - <strong>${escapeHtml(v.ten_bien_the)}</strong></td>
                            <td>${escapeHtml(v.sku || '--')}</td>
                            <td>${v.so_luong_ton}</td>
                            <td><input type="number" class="form-control b-sl" min="1" placeholder="SL" style="min-width: 70px;"></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <input type="number" class="form-control b-gia" min="0" placeholder="Giá nhập" value="${giaNhap > 0 ? giaNhap : ''}" style="min-width: 100px; flex: 1;">
                                    <span style="font-weight: 500; color: #64748b;">VNĐ</span>
                                </div>
                            </td>
                        </tr>`;
                    });
                } else {
                    // Tính giá nhập tự động (khoảng 65% giá bán lẻ)
                    const giaNhap = Math.round(((p.gia || 0) * 0.65) / 1000) * 1000;
                    html += `<tr class="bulk-item" data-sp="${p.ma_san_pham}" data-bt="">
                        <td>${escapeHtml(p.ten_san_pham)}</td>
                        <td>${escapeHtml(p.sku || '--')}</td>
                        <td>${p.so_luong_ton}</td>
                        <td><input type="number" class="form-control b-sl" min="1" placeholder="SL" style="min-width: 70px;"></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <input type="number" class="form-control b-gia" min="0" placeholder="Giá nhập" value="${giaNhap > 0 ? giaNhap : ''}" style="min-width: 100px; flex: 1;">
                                <span style="font-weight: 500; color: #64748b;">VNĐ</span>
                            </div>
                        </td>
                    </tr>`;
                }
            });
            tbody.innerHTML = html || '<tr><td colspan="5">Không có dữ liệu</td></tr>';
        }
    } catch (e) {
        tbody.innerHTML = '<tr><td colspan="5">Lỗi tải dữ liệu</td></tr>';
    }
}

function changeInventoryPage(page) {
    currentInventoryPage = page;
    loadInventory(page);
}

function closeBulkImportModal() {
    document.getElementById('modal-bulk-import').style.display = 'none';
    if(document.getElementById('modal-overlay') && document.getElementById('modal-product').style.display !== 'block') {
        document.getElementById('modal-overlay').style.display = 'none';
    }
}

const formBulkImport = document.getElementById('form-bulk-import');
if (formBulkImport) {
    formBulkImport.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const items = [];
        document.querySelectorAll('.bulk-item').forEach(tr => {
            const sl = tr.querySelector('.b-sl').value;
            const gia = tr.querySelector('.b-gia').value;
            
            if (sl && parseInt(sl) > 0) {
                items.push({
                    ma_san_pham: tr.dataset.sp,
                    ma_bien_the: tr.dataset.bt || null,
                    so_luong: parseInt(sl),
                    gia_nhap: gia ? parseInt(gia) : undefined
                });
            }
        });
        
        if (items.length === 0) {
            showAlert('Vui lòng nhập số lượng cho ít nhất 1 sản phẩm', 'error');
            return;
        }
        
        const payload = {
            items: items,
            ghi_chu: document.getElementById('bulk-note').value,
            ma_nha_cung_cap: document.getElementById('bulk-supplier') ? document.getElementById('bulk-supplier').value : null
        };
        
        try {
            const res = await fetch(`${API_BASE_URL}/inventory/import`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.status === 'success') {
                showAlert('Nhập kho hàng loạt thành công!', 'success');
                closeBulkImportModal();
                loadInventory();
                loadProducts(); 
            } else {
                showAlert(result.message, 'error');
            }
        } catch (err) {
            showAlert('Lỗi kết nối', 'error');
        }
    });
}

async function viewLogs(ma_sp, ma_bt) {
    document.getElementById('modal-logs').style.display = 'block';
    if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'block';
    const tbody = document.getElementById('logs-tbody');
    tbody.innerHTML = '<tr><td colspan="7">Đang tải...</td></tr>';
    try {
        let url = `${API_BASE_URL}/inventory/logs?ma_san_pham=${ma_sp}`;
        if (ma_bt) url += `&ma_bien_the=${ma_bt}`;
        const res = await fetch(url);
        const result = await res.json();
        if (result.status === 'success') {
            tbody.innerHTML = result.data.map(log => {
                const spName = log.san_pham ? log.san_pham.ten_san_pham : '';
                const btName = log.bien_the ? ' - ' + log.bien_the.ten_bien_the : '';
                return `
                <tr>
                    <td>${new Date(log.ngay_tao).toLocaleString('vi-VN')}</td>
                    <td>${escapeHtml(spName + btName)}</td>
                    <td>${log.loai_thao_tac === 'nhap_kho' ? '<span style="color:green">Nhập</span>' : (log.loai_thao_tac === 'xuat_kho' ? '<span style="color:red">Xuất</span>' : log.loai_thao_tac)}</td>
                    <td>${log.so_luong_thay_doi > 0 ? '+'+log.so_luong_thay_doi : log.so_luong_thay_doi}</td>
                    <td>${log.ton_kho_cuoi}</td>
                    <td>${log.gia_nhap ? Number(log.gia_nhap).toLocaleString('vi-VN') + 'đ' : '--'}</td>
                    <td>${escapeHtml(log.ghi_chu || '')}</td>
                </tr>
                `;
            }).join('') || '<tr><td colspan="7">Chưa có lịch sử</td></tr>';
        }
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="7">Lỗi tải dữ liệu</td></tr>';
    }
}

async function viewAllLogs() {
    document.getElementById('modal-logs').style.display = 'block';
    if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'block';
    const tbody = document.getElementById('logs-tbody');
    tbody.innerHTML = '<tr><td colspan="7">Đang tải...</td></tr>';
    try {
        let url = `${API_BASE_URL}/inventory/logs`; // Lấy tất cả, đã sort DESC từ DB
        const res = await fetch(url);
        const result = await res.json();
        if (result.status === 'success') {
            tbody.innerHTML = result.data.map(log => {
                const spName = log.san_pham ? log.san_pham.ten_san_pham : '';
                const btName = log.bien_the ? ' - ' + log.bien_the.ten_bien_the : '';
                return `
                <tr>
                    <td>${new Date(log.ngay_tao).toLocaleString('vi-VN')}</td>
                    <td>${escapeHtml(spName + btName)}</td>
                    <td>${log.loai_thao_tac === 'nhap_kho' ? '<span style="color:green">Nhập</span>' : (log.loai_thao_tac === 'xuat_kho' ? '<span style="color:red">Xuất</span>' : log.loai_thao_tac)}</td>
                    <td>${log.so_luong_thay_doi > 0 ? '+'+log.so_luong_thay_doi : log.so_luong_thay_doi}</td>
                    <td>${log.ton_kho_cuoi}</td>
                    <td>${log.gia_nhap ? Number(log.gia_nhap).toLocaleString('vi-VN') + 'đ' : '--'}</td>
                    <td>${escapeHtml(log.ghi_chu || '')}</td>
                </tr>
                `;
            }).join('') || '<tr><td colspan="7">Chưa có lịch sử</td></tr>';
        }
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="7">Lỗi tải dữ liệu</td></tr>';
    }
}
function closeLogsModal() {
    document.getElementById('modal-logs').style.display = 'none';
    if(document.getElementById('modal-overlay') && document.getElementById('modal-product').style.display !== 'block') {
        document.getElementById('modal-overlay').style.display = 'none';
    }
}

// ========== VOUCHERS ==========
let vouchers = [];

async function loadVouchers(page = 1) {
    try {
        const search = document.getElementById('voucher-search-input').value;
        let url = `${API_BASE_URL}/voucher?page=${page}&limit=15`;
        if (search) url += `&search=${encodeURIComponent(search)}`;
        
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.status === 'success') {
            vouchers = result.data;
            renderVouchersTable();
            if (result.pagination) {
                renderPagination(result.pagination, 'vouchers-pagination', 'changeVouchersPage');
            }
        }
    } catch (error) {
        showAlert('Lỗi tải danh sách voucher', 'error');
    }
}

function changeVouchersPage(page) {
    currentVouchersPage = page;
    loadVouchers(page);
}

function renderVouchersTable() {
    const tbody = document.getElementById('vouchers-tbody');
    if (!tbody) return;
    
    if (vouchers.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="text-align: center;">Không có voucher nào</td></tr>';
        return;
    }
    
    tbody.innerHTML = vouchers.map(v => {
        const typeText = v.loai_giam === 'phan_tram' ? '%' : 'VNĐ';
        const valueText = v.loai_giam === 'phan_tram' ? v.gia_tri + '%' : Number(v.gia_tri).toLocaleString('vi-VN') + 'đ';
        const applyTo = v.san_pham ? `SP: ${escapeHtml(v.san_pham.ten_san_pham)}` : (v.danh_muc ? `DM: ${escapeHtml(v.danh_muc.ten_danh_muc)}` : 'Tất cả');
        const endDate = new Date(v.ngay_ket_thuc).toLocaleString('vi-VN');
        const statusBadge = v.trang_thai === 'hoat_dong' ? '<span class="status-badge status-badge--active">Hoạt động</span>' : '<span class="status-badge status-badge--inactive">Tạm dừng</span>';
        
        return `
            <tr>
                <td><strong>${escapeHtml(v.ma_code)}</strong></td>
                <td>${valueText}</td>
                <td>${applyTo}</td>
                <td>${v.so_luong}</td>
                <td>${endDate}</td>
                <td>${statusBadge}</td>
                <td>
                    <div class="action-btns">
                        <button class="icon-action-btn" title="Sửa" onclick="editVoucher(${v.ma_khuyen_mai})">
                            <i data-lucide="pencil" class="icon-xs"></i>
                        </button>
                        <button class="icon-action-btn icon-action-btn--danger" title="Xóa" onclick="deleteVoucher(${v.ma_khuyen_mai})">
                            <i data-lucide="trash-2" class="icon-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    if (window.lucide) lucide.createIcons();
}

function openVoucherModal() {
    document.getElementById('form-voucher').reset();
    document.getElementById('voucher-id').value = '';
    document.getElementById('voucher-modal-title').textContent = 'Thêm Khuyến Mãi';
    
    // Load options for products and categories
    const catSelect = document.getElementById('voucher-category');
    catSelect.innerHTML = '<option value="">-- Tất cả danh mục --</option>' + categories.map(c => `<option value="${c.ma_danh_muc}">${escapeHtml(c.ten_danh_muc)}</option>`).join('');
    
    const prodSelect = document.getElementById('voucher-product');
    prodSelect.innerHTML = '<option value="">-- Tất cả sản phẩm --</option>' + products.map(p => `<option value="${p.ma_san_pham}">${escapeHtml(p.ten_san_pham)}</option>`).join('');
    
    document.getElementById('modal-voucher').style.display = 'block';
    if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'block';
}

function closeVoucherModal() {
    document.getElementById('modal-voucher').style.display = 'none';
    if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'none';
}

function editVoucher(id) {
    const v = vouchers.find(x => x.ma_khuyen_mai === id);
    if (!v) return;
    
    openVoucherModal();
    document.getElementById('voucher-modal-title').textContent = 'Sửa Khuyến Mãi';
    document.getElementById('voucher-id').value = v.ma_khuyen_mai;
    document.getElementById('voucher-code').value = v.ma_code;
    document.getElementById('voucher-type').value = v.loai_giam;
    document.getElementById('voucher-value').value = v.gia_tri;
    document.getElementById('voucher-min-order').value = v.don_toi_thieu;
    document.getElementById('voucher-max-discount').value = v.giam_toi_da || '';
    document.getElementById('voucher-quantity').value = v.so_luong;
    document.getElementById('voucher-category').value = v.ma_danh_muc || '';
    document.getElementById('voucher-product').value = v.ma_san_pham || '';
    document.getElementById('voucher-status').value = v.trang_thai;
    
    if (v.ngay_bat_dau) {
        const d = new Date(v.ngay_bat_dau);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        document.getElementById('voucher-start').value = d.toISOString().slice(0, 16);
    }
    if (v.ngay_ket_thuc) {
        const d = new Date(v.ngay_ket_thuc);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        document.getElementById('voucher-end').value = d.toISOString().slice(0, 16);
    }
}

async function saveVoucher(e) {
    e.preventDefault();
    const id = document.getElementById('voucher-id').value;
    const payload = {
        ma_code: document.getElementById('voucher-code').value,
        loai_giam: document.getElementById('voucher-type').value,
        gia_tri: document.getElementById('voucher-value').value,
        don_toi_thieu: document.getElementById('voucher-min-order').value || 0,
        giam_toi_da: document.getElementById('voucher-max-discount').value || null,
        so_luong: document.getElementById('voucher-quantity').value || 0,
        ma_danh_muc: document.getElementById('voucher-category').value || null,
        ma_san_pham: document.getElementById('voucher-product').value || null,
        ngay_bat_dau: document.getElementById('voucher-start').value || null,
        ngay_ket_thuc: document.getElementById('voucher-end').value,
        trang_thai: document.getElementById('voucher-status').value
    };
    
    const method = id ? 'PUT' : 'POST';
    const url = id ? `${API_BASE_URL}/voucher/${id}` : `${API_BASE_URL}/voucher`;
    
    try {
        const res = await fetch(url, {
            method,
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(payload)
        });
        const result = await res.json();
        
        if (result.status === 'success') {
            showAlert(result.message, 'success');
            closeVoucherModal();
            loadVouchers();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Lỗi kết nối', 'error');
    }
}

async function deleteVoucher(id) {
    if (!confirm('Bạn có chắc muốn xóa voucher này?')) return;
    try {
        const res = await fetch(`${API_BASE_URL}/voucher/${id}`, { method: 'DELETE' });
        const result = await res.json();
        if (result.status === 'success') {
            showAlert('Xóa thành công', 'success');
            loadVouchers();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Lỗi kết nối', 'error');
    }
}

// ========== EXCEL IMPORT & MARKET PRICE ==========

// 0. Tải file Excel mẫu
function downloadSampleExcel() {
    const headers = ['Tên sản phẩm', 'SKU', 'Danh mục', 'Giá bán', 'Giá nhập', 'Số lượng tồn', 'Thương hiệu'];
    const sampleData = [{}];
    headers.forEach(h => sampleData[0][h] = '');

    const ws = XLSX.utils.json_to_sheet(sampleData);

    // Đặt độ rộng cột cho dễ đọc
    ws['!cols'] = [
        { wch: 40 },  // Tên sản phẩm
        { wch: 12 },  // SKU
        { wch: 20 },  // Danh mục
        { wch: 15 },  // Giá bán
        { wch: 15 },  // Giá nhập
        { wch: 15 },  // Số lượng tồn
        { wch: 20 },  // Thương hiệu
    ];

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Danh sách sản phẩm');

    XLSX.writeFile(wb, 'mau_nhap_san_pham.xlsx');
    showAlert('Tải file mẫu thành công!', 'success');
}

// 1. Nhập từ Excel (Thêm sản phẩm hàng loạt)
async function handleExcelUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = async function (e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            
            // Đọc sheet đầu tiên
            const firstSheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[firstSheetName];
            
            // Chuyển về JSON
            const jsonData = XLSX.utils.sheet_to_json(worksheet, { defval: '' });
            
            if (!jsonData || jsonData.length === 0) {
                showAlert('File Excel trống hoặc sai định dạng', 'error');
                event.target.value = '';
                return;
            }

            // Hiển thị loading
            showAlert('Đang xử lý dữ liệu...', 'info');

            // Gọi API Bulk Create
            const res = await fetch(`${API_BASE_URL}/products/bulk`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ products: jsonData })
            });

            const result = await res.json();
            
            if (result.success || result.status === 'success') {
                showAlert(`Đã nhập thành công ${result.data?.length || jsonData.length} sản phẩm từ Excel`, 'success');
                loadProducts(document.getElementById('product-search-input')?.value || '', document.querySelector('.role-tab.active')?.getAttribute('data-status') || 'all');
            } else {
                showAlert('Lỗi: ' + (result.message || 'Không thể nhập từ Excel'), 'error');
            }

        } catch (err) {
            console.error('Lỗi khi đọc file Excel:', err);
            showAlert('Đã xảy ra lỗi khi đọc file Excel', 'error');
        }
        
        event.target.value = ''; // Reset file input
    };
    reader.readAsArrayBuffer(file);
}
// ========== PUBLISH MODAL LOGIC ==========
let unlistedProducts = [];

// ========== SINGLE INV MODAL LOGIC ==========
function openInvModal(ma_san_pham, ma_bien_the, type, productName) {
    document.getElementById('inv-single-sp-id').value = ma_san_pham;
    document.getElementById('inv-single-bt-id').value = ma_bien_the || '';
    document.getElementById('inv-single-type').value = type;
    document.getElementById('inv-single-name').value = productName;
    document.getElementById('inv-single-sl').value = '';
    document.getElementById('inv-single-gia').value = '';
    document.getElementById('inv-single-note').value = '';

    const title = type === 'import' ? 'Nhập kho' : 'Xuất kho';
    document.getElementById('inv-single-title').textContent = title + ' — ' + productName;
    document.getElementById('inv-single-submit-btn').textContent = 'Xác nhận ' + title;

    // Ẩn trường Giá nhập nếu là Xuất kho
    document.getElementById('inv-single-gia-group').style.display = type === 'import' ? 'block' : 'none';

    document.getElementById('modal-inv-single').style.display = 'block';
    if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'block';
    setTimeout(() => document.getElementById('inv-single-sl').focus(), 100);
}

function closeInvModal() {
    document.getElementById('modal-inv-single').style.display = 'none';
    if(document.getElementById('modal-overlay') &&
       document.getElementById('modal-product').style.display !== 'block' &&
       document.getElementById('modal-bulk-import').style.display !== 'block' &&
       document.getElementById('modal-logs').style.display !== 'block') {
        document.getElementById('modal-overlay').style.display = 'none';
    }
}

const formInvSingle = document.getElementById('form-inv-single');
if (formInvSingle) {
    formInvSingle.addEventListener('submit', async (e) => {
        e.preventDefault();
        const ma_san_pham = document.getElementById('inv-single-sp-id').value;
        const ma_bien_the = document.getElementById('inv-single-bt-id').value || null;
        const type = document.getElementById('inv-single-type').value;
        const sl = parseInt(document.getElementById('inv-single-sl').value);
        const gia = document.getElementById('inv-single-gia').value;
        const ghi_chu = document.getElementById('inv-single-note').value;

        if (!sl || sl < 1) {
            showAlert('Vui lòng nhập số lượng hợp lệ (tối thiểu 1)', 'error');
            return;
        }

        const endpoint = type === 'import' ? `${API_BASE_URL}/inventory/import` : `${API_BASE_URL}/inventory/export`;
        const payload = {
            items: [{ ma_san_pham, ma_bien_the, so_luong: sl, gia_nhap: gia ? parseInt(gia) : undefined }],
            ghi_chu
        };

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.status === 'success') {
                showAlert(type === 'import' ? 'Nhập kho thành công!' : 'Xuất kho thành công!', 'success');
                closeInvModal();
                loadInventory(currentInventoryPage);
                loadProducts();
            } else {
                showAlert(result.message || 'Lỗi thao tác kho', 'error');
            }
        } catch(err) {
            showAlert('Lỗi kết nối', 'error');
        }
    });
}

async function deleteInventoryProduct(id) {
    const confirmDelete = await window.showCustomDialog({
        title: 'Xóa sản phẩm',
        message: 'Bạn chắc chắn muốn xóa sản phẩm này khỏi kho hàng?',
        isPrompt: false
    });
    if (!confirmDelete) return;
    try {
        const response = await fetch(`${API_BASE_URL}/products/${id}`, { method: 'DELETE' });
        const result = await response.json();
        if (result.status === 'success' || result.success) {
            showAlert('Xóa sản phẩm thành công', 'success');
            loadInventory(currentInventoryPage);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Lỗi khi xóa sản phẩm', 'error');
    }
}

async function openPublishModal() {
    document.getElementById('modal-publish').style.display = 'block';
    if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'block';
    
    document.getElementById('publish-select-all').checked = false;
    const tbody = document.getElementById('publish-tbody');
    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Đang tải...</td></tr>';
    
    try {
        const response = await fetch(`${API_BASE_URL}/products?per_page=1000&is_unlisted=true`);
        const result = await response.json();
        if (result.status === 'success' || result.success) {
            unlistedProducts = result.data;
            if (unlistedProducts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Không có sản phẩm nào trong kho chưa đăng bán</td></tr>';
                return;
            }
            
            let html = '';
            unlistedProducts.forEach(p => {
                html += `
                    <tr class="publish-item" data-id="${p.ma_san_pham}">
                        <td><input type="checkbox" class="p-check" style="width: 16px; height: 16px;" onchange="updatePublishSelectAllState()"></td>
                        <td>
                            <div style="font-weight: 500;">${escapeHtml(p.ten_san_pham)}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">SKU: ${escapeHtml(p.sku || '--')}</div>
                        </td>
                        <td>${p.so_luong_ton}</td>
                        <td style="color: var(--text-muted);">${p.gia_nhap ? Number(p.gia_nhap).toLocaleString('vi-VN') + 'đ' : '--'}</td>
                        <td>
                            <input type="number" class="form-control p-gia" min="1000" placeholder="Giá bán" value="${p.gia > 0 ? p.gia : ''}" style="width: 130px;">
                        </td>
                        <td>
                            <input type="number" class="form-control p-giakm" min="1000" placeholder="Giá KM" value="${p.gia_khuyen_mai || ''}" style="width: 130px;">
                        </td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }
    } catch(e) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Lỗi tải danh sách</td></tr>';
    }
}

function togglePublishSelectAll(checked) {
    document.querySelectorAll('.p-check').forEach(cb => {
        cb.checked = checked;
    });
}

function updatePublishSelectAllState() {
    const all = document.querySelectorAll('.p-check');
    const checked = document.querySelectorAll('.p-check:checked');
    document.getElementById('publish-select-all').checked = all.length > 0 && all.length === checked.length;
}

function closePublishModal() {
    document.getElementById('modal-publish').style.display = 'none';
    if(document.getElementById('modal-overlay') && document.getElementById('modal-product').style.display !== 'block' && document.getElementById('modal-bulk-import').style.display !== 'block' && document.getElementById('modal-logs').style.display !== 'block' && document.getElementById('modal-voucher').style.display !== 'block' && document.getElementById('modal-inv-single').style.display !== 'block') {
        document.getElementById('modal-overlay').style.display = 'none';
    }
    document.getElementById('form-publish').reset();
}

const formPublish = document.getElementById('form-publish');
if (formPublish) {
    formPublish.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const selectedRows = Array.from(document.querySelectorAll('.publish-item')).filter(tr => tr.querySelector('.p-check').checked);
        if (selectedRows.length === 0) {
            showAlert('Vui lòng chọn ít nhất một sản phẩm để đăng bán', 'error');
            return;
        }

        const items = [];
        let hasError = false;

        selectedRows.forEach(tr => {
            const id = tr.dataset.id;
            const gia = tr.querySelector('.p-gia').value;
            const giakm = tr.querySelector('.p-giakm').value;

            if (!gia || parseInt(gia) < 1000) {
                tr.querySelector('.p-gia').style.borderColor = 'red';
                hasError = true;
            } else {
                tr.querySelector('.p-gia').style.borderColor = '#cbd5e1';
                items.push({
                    id: id,
                    gia: parseInt(gia),
                    gia_khuyen_mai: giakm ? parseInt(giakm) : null,
                    trang_thai: 'dang_ban',
                    hien_thi_web: true
                });
            }
        });

        if (hasError) {
            showAlert('Vui lòng nhập giá bán hợp lệ (>= 1000) cho các sản phẩm đã chọn', 'error');
            return;
        }

        const submitBtn = formPublish.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang xử lý...';

        try {
            for (const item of items) {
                const { id, ...payload } = item;
                const response = await fetch(`${API_BASE_URL}/products/${id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await response.json();
                if (result.status !== 'success' && !result.success) {
                    throw new Error(result.message || 'Lỗi cập nhật sản phẩm');
                }
            }
            
            showAlert(`Đã đăng bán ${items.length} sản phẩm thành công`, 'success');
            closePublishModal();
            loadProducts();
            if(document.getElementById('view-inventory').style.display === 'block') {
                loadInventory(currentInventoryPage);
            }
        } catch(err) {
            showAlert('Lỗi khi đăng bán sản phẩm', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });
}

async function unpublishProduct(id) {
    if(!confirm('Bạn có chắc muốn gỡ sản phẩm này khỏi trang web (chỉ giữ lại trong kho)?')) return;
    try {
        const payload = { hien_thi_web: false };
        const response = await fetch(`${API_BASE_URL}/products/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (result.status === 'success' || result.success) {
            showAlert('Gỡ khỏi trang web thành công', 'success');
            loadProducts();
        } else {
            showAlert(result.message || 'Lỗi', 'error');
        }
    } catch(err) {
        showAlert('Lỗi kết nối', 'error');
    }
}
</script>
@endsection



