# 3.3. Phân hệ Quản trị (Admin)

Phân hệ Quản trị (Admin) đóng vai trò là trung tâm điều hành của toàn bộ hệ thống, cho phép chủ cửa hàng hoặc nhân viên quản lý kiểm soát dữ liệu, theo dõi doanh thu và cấu hình các thông số quan trọng.

---

## 3.3.1. Các màn hình chức năng (Dashboard Tổng quan)
*(Chèn ảnh chụp màn hình trang Dashboard vào đây)*

**Mô tả chức năng:**
Trang Dashboard cung cấp cái nhìn tổng quan về tình hình kinh doanh của cửa hàng. Giao diện hiển thị các thẻ thống kê tóm tắt như: Tổng doanh thu, số lượng đơn hàng mới, số lượng khách hàng, và biểu đồ doanh thu theo thời gian. Giúp người quản trị nắm bắt nhanh chóng hiệu quả hoạt động của shop mỹ phẩm.

**Mã nguồn xử lý:**
- **Route Frontend:**
```php
Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
```
- **Backend (Node.js API):** Xử lý lấy số liệu tổng quát
```javascript
// Trích xuất thống kê cơ bản cho Dashboard
exports.getDashboardStats = async (req, res) => {
    try {
        const totalRevenue = await DonHang.sum('tong_tien', { where: { trang_thai_don: 'da_giao' } });
        const newOrders = await DonHang.count({ where: { trang_thai_don: 'cho_xac_nhan' } });
        const totalUsers = await NguoiDung.count({ where: { vai_tro: 'khach_hang' } });
        
        res.json({ status: 'success', data: { totalRevenue, newOrders, totalUsers } });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
```

---

## 3.3.2. Các màn hình chức năng (Quản lý Danh mục)
*(Chèn ảnh chụp màn hình trang Quản lý danh mục vào đây)*

**Mô tả chức năng:**
Trang Quản lý danh mục cho phép admin tạo, sửa, xóa các nhóm sản phẩm (Ví dụ: Son môi, Kem dưỡng da, Sữa rửa mặt). Hệ thống hỗ trợ quản lý danh mục theo dạng cây (Danh mục cha - con) để tổ chức sản phẩm một cách logic.

**Mã nguồn xử lý:**
- **Route Frontend:**
```php
Route::get('/categories', function () { return view('admin.categories'); })->name('admin.categories');
```
- **Backend (Node.js API):** Xử lý thêm mới danh mục
```javascript
exports.createCategory = async (req, res) => {
    try {
        const { ten_danh_muc, mo_ta, ma_danh_muc_cha, thu_tu_hien_thi } = req.body;
        const newCategory = await DanhMuc.create({
            ten_danh_muc, mo_ta, ma_danh_muc_cha, thu_tu_hien_thi
        });
        res.json({ status: 'success', data: newCategory });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
```

---

## 3.3.3. Các màn hình chức năng (Quản lý Sản phẩm)
*(Chèn ảnh chụp màn hình trang Quản lý sản phẩm vào đây)*

**Mô tả chức năng:**
Trang Quản lý sản phẩm hiển thị danh sách toàn bộ mặt hàng mỹ phẩm. Chức năng bao gồm thêm, sửa, xóa, tìm kiếm sản phẩm và quản lý số lượng tồn kho. Khi tồn kho của một sản phẩm mỹ phẩm xuống thấp, hệ thống sẽ hiển thị cảnh báo.

**Mã nguồn xử lý:**
- **Route Frontend:**
```php
Route::get('/products', function () { return view('admin.products'); })->name('admin.products');
```
- **Backend (Node.js API):** Xử lý lấy danh sách phân trang và lọc
```javascript
exports.getAllProducts = async (req, res) => {
    const { search, trang_thai, per_page = 15, page = 1 } = req.query;
    let where = {};
    
    if (search) where.ten_san_pham = { [Op.like]: `%${search}%` };
    if (trang_thai) where.trang_thai = trang_thai;

    const offset = (page - 1) * per_page;
    const { count, rows } = await SanPham.findAndCountAll({
        where, limit: parseInt(per_page), offset, order: [['ngay_tao', 'DESC']]
    });
    res.json({ status: 'success', data: rows, total: count });
};
```

---

## 3.3.4. Các màn hình chức năng (Quản lý Đơn hàng)
*(Chèn ảnh chụp màn hình trang Quản lý đơn hàng vào đây)*

**Mô tả chức năng:**
Trang Quản lý đơn hàng giúp admin theo dõi toàn bộ luồng giao dịch. Admin có thể xem chi tiết người đặt, địa chỉ giao hàng, danh sách mỹ phẩm đã mua và cập nhật trạng thái đơn (Từ "Chờ xác nhận" -> "Đang giao" -> "Hoàn thành").

**Mã nguồn xử lý:**
- **Route Frontend:**
```php
Route::get('/orders', function () { return view('admin.orders'); })->name('admin.orders');
```
- **Backend (Node.js API):** Cập nhật trạng thái đơn hàng
```javascript
exports.updateOrderStatus = async (req, res) => {
    try {
        const { id } = req.params;
        const { trang_thai_don } = req.body;
        
        await DonHang.update({ trang_thai_don }, { where: { ma_don_hang: id } });
        res.json({ status: 'success', message: 'Cập nhật trạng thái thành công' });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
```

---

## 3.3.5. Các màn hình chức năng (Quản lý Người dùng)
*(Chèn ảnh chụp màn hình trang Quản lý người dùng vào đây)*

**Mô tả chức năng:**
Nơi quản trị viên kiểm soát danh sách thành viên (Khách hàng và Admin khác). Có khả năng khóa/mở khóa tài khoản nếu phát hiện dấu hiệu gian lận hoặc vi phạm quy định mua hàng.

**Mã nguồn xử lý:**
- **Route Frontend:**
```php
Route::get('/users', function () { return view('admin.users'); })->name('admin.users');
```
- **Backend (Node.js API):**
```javascript
exports.getAllUsers = async (req, res) => {
    try {
        const users = await NguoiDung.findAll({
            attributes: ['ma_nguoi_dung', 'ho_ten', 'email', 'vai_tro', 'trang_thai', 'ngay_tao'],
            order: [['ngay_tao', 'DESC']]
        });
        res.json({ status: 'success', data: users });
    } catch (error) {
        res.status(500).json({ status: 'error', message: error.message });
    }
};
```
