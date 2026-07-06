@extends('layouts.user')
@section('title', 'Trang cá nhân')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-profile.css') }}">
@endsection

@section('content')
<section class="user-section">
    <div class="profile-grid">
        
        {{-- Sidebar --}}
        <div class="profile-sidebar">
            <div class="profile-avatar">
                <div class="profile-avatar__head"></div>
                <div class="profile-avatar__body"></div>
            </div>

            <div id="sidebar-name" class="profile-name">Đang tải...</div>
            
            <button id="btn-info" onclick="switchTab('info')" class="tab-btn profile-tab-btn profile-tab-btn--active">
                Quản lý thông tin
            </button>
            
            <button id="btn-history" onclick="switchTab('history')" class="tab-btn profile-tab-btn">
                Lịch sử mua hàng
            </button>
            
            <button id="btn-orders" onclick="switchTab('orders')" class="tab-btn profile-tab-btn">
                Quản lý đơn hàng
            </button>
        </div>

        {{-- Content Area --}}
        <div class="profile-content">
            
            {{-- Tab: Thông tin --}}
            <div id="tab-info" class="tab-content profile-tab profile-tab--active">
                <h2 class="profile-tab__title">Cập nhật thông tin</h2>
                
                <form id="update-profile-form" class="profile-form">
                    
                    <div class="profile-form__grid">
                        <div class="profile-form__group">
                            <label class="profile-form__label">Họ và tên</label>
                            <input type="text" id="input-name" class="profile-form__input" required>
                        </div>

                        <div class="profile-form__group">
                            <label class="profile-form__label">Email</label>
                            <input type="email" id="input-email" class="profile-form__input profile-form__input--readonly" readonly title="Email không thể thay đổi">
                        </div>

                        <div class="profile-form__group">
                            <label class="profile-form__label">Số điện thoại</label>
                            <input type="text" id="input-phone" class="profile-form__input" placeholder="VD: 0912345678">
                        </div>

                        <div class="profile-form__group">
                            <label class="profile-form__label profile-form__label--pink">Loại da của bạn</label>
                            <select id="input-skin-type" class="profile-form__select profile-form__input--pink-focus">
                                <option value="">-- Chưa xác định --</option>
                                <option value="da_dau">Da dầu</option>
                                <option value="da_kho">Da khô</option>
                                <option value="da_hon_hop">Da hỗn hợp</option>
                                <option value="da_nhay_cam">Da nhạy cảm</option>
                                <option value="da_thuong">Da thường</option>
                            </select>
                        </div>
                    </div>

                    <div class="profile-form__group">
                        <label class="profile-form__label">Địa chỉ giao hàng</label>
                        <input type="text" id="input-address" class="profile-form__input" placeholder="Số nhà, Tên đường, Phường/Xã...">
                    </div>

                    <div class="profile-bank-section">
                        <h3 class="profile-bank-section__title">
                            <span class="profile-bank-section__title-icon">💳</span> Thông tin nhận tiền hoàn
                        </h3>
                        <p class="profile-bank-section__hint">Cập nhật sẵn tài khoản ngân hàng để quá trình hoàn tiền (nếu có) diễn ra nhanh chóng.</p>
                        
                        <div class="profile-form__grid">
                            <div class="profile-form__group">
                                <label class="profile-form__label">Ngân hàng</label>
                                <input type="text" id="input-bank-name" list="bank-list" class="profile-form__input" placeholder="VD: Vietcombank, MB Bank...">
                            </div>
                            <div class="profile-form__group">
                                <label class="profile-form__label">Số tài khoản</label>
                                <input type="text" id="input-bank-account" class="profile-form__input" placeholder="Nhập số tài khoản">
                            </div>
                            <div class="profile-form__group profile-form__group--full">
                                <label class="profile-form__label">Tên chủ tài khoản</label>
                                <input type="text" id="input-bank-owner" class="profile-form__input" style="text-transform: uppercase;" placeholder="VD: NGUYEN VAN A">
                            </div>
                        </div>
                    </div>

                    <div class="profile-password-section" style="margin-bottom: 1rem;">
                        <label class="profile-form__label">Mật khẩu cũ <span class="profile-password-section__hint">(Bắt buộc nếu muốn đổi mật khẩu)</span></label>
                        <input type="password" id="input-old-password" class="profile-form__input" placeholder="Nhập mật khẩu cũ...">
                    </div>

                    <div class="profile-password-section">
                        <label class="profile-form__label">Mật khẩu mới <span class="profile-password-section__hint">(Bỏ trống nếu không muốn đổi)</span></label>
                        <input type="password" id="input-password" class="profile-form__input" placeholder="Nhập mật khẩu mới...">
                    </div>

                    <div class="profile-form__actions">
                        <button type="submit" id="btn-save-profile" class="profile-form__save-btn">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>

            {{-- Tab: Lịch sử --}}
            <div id="tab-history" class="tab-content profile-tab">
                <h2 class="profile-tab__title">Lịch sử mua hàng</h2>
                
                <!-- Filter buttons -->
                <div class="profile-order-filters" id="history-filters" style="display: none;">
                    <button class="profile-filter-btn profile-filter-btn--active" onclick="filterOrders('history', 'all', event)">Tất cả</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'hoan_thanh', event)">Hoàn thành</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'giao_thanh_cong', event)">Giao thành công</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'da_huy', event)">Đã hủy</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'da_tra_hang', event)">Đã trả hàng</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'tu_choi_tra_hang', event)">Từ chối trả hàng</button>
                </div>

                <div id="history-container" class="profile-orders-empty">
                    Chưa có dữ liệu lịch sử mua hàng.
                </div>
            </div>

            {{-- Tab: Đơn hàng --}}
            <div id="tab-orders" class="tab-content profile-tab">
                <h2 class="profile-tab__title">Quản lý đơn hàng</h2>

                <!-- Filter buttons -->
                <div class="profile-order-filters" id="orders-filters" style="display: none;">
                    <button class="profile-filter-btn profile-filter-btn--active" onclick="filterOrders('orders', 'all', event)">Tất cả</button>
                    <button class="profile-filter-btn" onclick="filterOrders('orders', 'cho_xac_nhan', event)">Chờ xác nhận</button>
                    <button class="profile-filter-btn" onclick="filterOrders('orders', 'da_xac_nhan', event)">Đã xác nhận</button>
                    <button class="profile-filter-btn" onclick="filterOrders('orders', 'dang_giao', event)">Đang giao</button>
                    <button class="profile-filter-btn" onclick="filterOrders('orders', 'dang_tra_hang', event)">Đang xử lý trả hàng</button>
                </div>

                <div id="orders-container" class="profile-orders-empty">
                    Bạn chưa có đơn hàng nào đang được xử lý.
                </div>
            </div>
            
        </div>
    </div>
</section>

<script>
    const API_URL = 'http://localhost:3000/api';

    // 1. CHỨC NĂNG CHUYỂN TAB GIAO DIỆN
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.remove('profile-tab--active');
        });

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('profile-tab-btn--active');
        });

        document.getElementById('tab-' + tabName).classList.add('profile-tab--active');
        document.getElementById('btn-' + tabName).classList.add('profile-tab-btn--active');
        
        // Cập nhật URL để khi F5 không bị mất tab
        const url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.replaceState({}, '', url);
    }

    // 2. LẤY DỮ LIỆU ĐỔ VÀO FORM KHI MỞ TRANG
    document.addEventListener('DOMContentLoaded', async function() {
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        try {
            const response = await fetch(`${API_URL}/auth/me`, {
                method: 'GET',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                const user = result.data;
                document.getElementById('sidebar-name').textContent = user.ho_ten;
                document.getElementById('input-name').value = user.ho_ten || '';
                document.getElementById('input-email').value = user.email || '';
                document.getElementById('input-phone').value = user.so_dien_thoai || '';
                document.getElementById('input-address').value = user.dia_chi || '';
                document.getElementById('input-skin-type').value = user.loai_da || '';
                
                // Load dữ liệu ngân hàng
                document.getElementById('input-bank-name').value = user.ngan_hang || '';
                document.getElementById('input-bank-account').value = user.so_tai_khoan || '';
                document.getElementById('input-bank-owner').value = user.chu_tai_khoan || '';

                loadMyOrders();

                // Kiểm tra xem có yêu cầu mở tab cụ thể từ URL không
                const urlParams = new URLSearchParams(window.location.search);
                const requestedTab = urlParams.get('tab');
                if (requestedTab) {
                    switchTab(requestedTab);
                }
            } else {
                logout();
            }
        } catch (error) {
            console.error('Lỗi lấy thông tin:', error);
        }
    });

    // 3. GỬI DỮ LIỆU CẬP NHẬT LÊN SERVER
    document.getElementById('update-profile-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-save-profile');
        btn.textContent = 'Đang lưu...';
        btn.disabled = true;

        const updateData = {
            ho_ten: document.getElementById('input-name').value,
            so_dien_thoai: document.getElementById('input-phone').value,
            dia_chi: document.getElementById('input-address').value,
            loai_da: document.getElementById('input-skin-type').value,
            
            // Gửi dữ liệu ngân hàng lên backend
            ngan_hang: document.getElementById('input-bank-name').value,
            so_tai_khoan: document.getElementById('input-bank-account').value,
            chu_tai_khoan: document.getElementById('input-bank-owner').value
        };

        const oldPassword = document.getElementById('input-old-password').value;
        const newPassword = document.getElementById('input-password').value;
        
        if (newPassword || oldPassword) {
            if (!oldPassword) {
                alert('Vui lòng nhập mật khẩu cũ để xác nhận việc đổi mật khẩu mới!');
                btn.textContent = 'Lưu thay đổi';
                btn.disabled = false;
                return;
            }
            if (!newPassword) {
                alert('Vui lòng nhập mật khẩu mới!');
                btn.textContent = 'Lưu thay đổi';
                btn.disabled = false;
                return;
            }
            if (newPassword === oldPassword) {
                alert('Mật khẩu mới không được trùng với mật khẩu cũ!');
                btn.textContent = 'Lưu thay đổi';
                btn.disabled = false;
                return;
            }
            updateData.mat_khau_cu = oldPassword;
            updateData.mat_khau_moi = newPassword;
        }

        try {
            const token = localStorage.getItem('token');
            const response = await fetch(`${API_URL}/auth/update-profile`, {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(updateData)
            });

            const result = await response.json();
            if (result.success) {
                alert('Cập nhật thông tin thành công!');
                document.getElementById('sidebar-name').textContent = updateData.ho_ten;
                document.getElementById('input-old-password').value = ''; 
                document.getElementById('input-password').value = ''; 
            } else {
                alert(result.message || 'Có lỗi xảy ra khi cập nhật.');
            }
        } catch (error) {
            console.error('Lỗi cập nhật:', error);
            alert('Không thể kết nối đến server Node.js!');
        } finally {
            btn.textContent = 'Lưu thay đổi';
            btn.disabled = false;
        }
    });

    // 4. CHỨC NĂNG ĐĂNG XUẤT
    function logout() {
        localStorage.removeItem('token');
        localStorage.removeItem('user');
        window.location.href = '/login';
    }

    // 5. TẢI VÀ HIỂN THỊ ĐƠN HÀNG
    async function loadMyOrders() {
        const token = localStorage.getItem('token');
        const ordersContainer = document.getElementById('orders-container');
        const historyContainer = document.getElementById('history-container'); 

        try {
            const response = await fetch(`${API_URL}/order/my-orders`, {
                headers: { 'Authorization': 'Bearer ' + token }
            });
            const result = await response.json();

            if (result.success && result.data.length > 0) {
                let htmlOrders = '<div class="space-y-4 text-left">';
                let htmlHistory = '<div class="space-y-4 text-left">';
                let hasOrders = false;
                let hasHistory = false;
                
                result.data.forEach((order, index) => {
                    const orderDateFull = new Date(order.ngay_dat);
                    const timeString = orderDateFull.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                    const dateString = orderDateFull.toLocaleDateString('vi-VN');
                    const date = `${timeString} - ${dateString}`;
                    
                    let paymentMethodText = 'Thanh toán khi nhận hàng (COD)';
                    switch(order.phuong_thuc_thanh_toan) {
                        case 'tien_mat': paymentMethodText = 'Thanh toán khi nhận hàng (COD)'; break;
                        case 'chuyen_khoan': paymentMethodText = 'Chuyển khoản Ngân hàng'; break;
                        case 'vi_dien_tu': paymentMethodText = 'Ví điện tử (Momo)'; break;
                        case 'the_tin_dung': paymentMethodText = 'Thẻ tín dụng / Ghi nợ'; break;
                    }
                    
                    const tongTienHang = parseInt(order.tong_tien_hang);
                    const soTienGiam = parseInt(order.so_tien_giam || 0); 
                    const tongThanhToan = parseInt(order.tong_thanh_toan);

                    let voucherHtml = '';
                    if (soTienGiam > 0) {
                        voucherHtml = `
                            <div class="flex justify-between items-center w-full mb-1 text-green-600 font-medium text-sm">
                                <span>Mã giảm giá:</span>
                                <span>-${soTienGiam.toLocaleString()} đ</span>
                            </div>
                        `;
                    }
                    
                    let productsHtml = '<div class="profile-order-card__products">';
                    if (order.chi_tiet && order.chi_tiet.length > 0) {
                        order.chi_tiet.forEach(item => {
                            const itemPrice = parseInt(item.don_gia).toLocaleString() + ' đ';
                            productsHtml += `
                                <div class="profile-order-card__product-row">
                                    <span><span class="font-bold text-gray-800">${item.so_luong}x</span> ${item.ten_san_pham}</span>
                                    <span class="font-bold text-gray-800">${itemPrice}</span>
                                </div>
                            `;
                        });
                    }
                    productsHtml += '</div>';

                    // TAB LỊCH SỬ
                    if(['giao_thanh_cong', 'da_huy', 'hoan_thanh', 'da_tra_hang', 'tu_choi_tra_hang'].includes(order.trang_thai_don)) {
                        hasHistory = true;
                        let statusColor, statusText, actionBtnHtml = '';

                        if (order.trang_thai_don === 'giao_thanh_cong') {
                            const orderDateObj = new Date(order.ngay_cap_nhat || order.ngay_dat);
                            const diffDays = Math.floor((new Date() - orderDateObj) / (1000 * 60 * 60 * 24));

                            statusColor = 'bg-green-100 text-green-700';
                            statusText = 'Giao thành công (Chờ xác nhận)';
                            
                            if (diffDays <= 3) {
                                actionBtnHtml = `
                                    <div class="flex space-x-3 mt-4 w-full">
                                        <button onclick="updateOrderStatus(${order.ma_don_hang}, 'hoan_thanh')" class="w-1/2 bg-gray-800 hover:bg-black text-white font-bold py-2 px-4 rounded-lg transition text-sm shadow">
                                            Đã nhận được hàng
                                        </button>
                                        <button onclick="updateOrderStatus(${order.ma_don_hang}, 'tra_hang_hoan_tien')" class="w-1/2 bg-white border-2 border-gray-200 hover:border-yellow-500 hover:text-yellow-600 text-gray-600 font-bold py-2 px-4 rounded-lg transition text-sm">
                                            Yêu cầu Trả hàng
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-2 text-center w-full">Đơn hàng sẽ tự động hoàn thành sau 3 ngày (còn lại ${3 - diffDays} ngày)</p>
                                `;
                            } else {
                                actionBtnHtml = `<div class="mt-4 text-center text-gray-500 font-medium w-full bg-gray-50 py-2 rounded-lg border border-gray-200">Đã hết hạn 3 ngày đổi trả. Hệ thống đang tự động hoàn thành.</div>`;
                            }
                        }
                        else if (order.trang_thai_don === 'da_huy') {
                            statusColor = 'bg-red-100 text-red-700';
                            statusText = 'Đã hủy';
                            if (order.ly_do_huy_don) {
                                actionBtnHtml = `<div class="mt-4 text-sm text-left text-red-600 bg-red-50 p-3 rounded-lg w-full border border-red-100"><b>Lý do hủy:</b> ${order.ly_do_huy_don}</div>`;
                            }
                        } 
                        else if (order.trang_thai_don === 'hoan_thanh') {
                            statusColor = 'bg-blue-100 text-blue-700 border border-blue-200';
                            statusText = 'Hoàn thành';
                            actionBtnHtml = `<div class="mt-4 text-center text-green-600 font-bold w-full bg-green-50 py-2 rounded-lg">Cảm ơn bạn đã mua sắm!</div>`;
                        }
                        else if (order.trang_thai_don === 'da_tra_hang') {
                            statusColor = 'bg-gray-100 text-gray-700 border border-gray-300';
                            statusText = 'Đã hoàn tiền / Trả hàng';
                            actionBtnHtml = `<div class="mt-4 text-center text-gray-600 font-bold w-full bg-gray-50 py-2 rounded-lg">Đơn hàng đã được trả thành công</div>`;
                        }
                        else if (order.trang_thai_don === 'tu_choi_tra_hang') {
                            statusColor = 'bg-red-100 text-red-700 border border-red-300';
                            statusText = 'Bị từ chối trả hàng';
                            actionBtnHtml = `<div class="mt-4 text-center text-red-600 font-bold w-full bg-red-50 py-2 rounded-lg">Yêu cầu trả hàng của bạn bị từ chối vì sai quy định hoàn trả.</div>`;
                        }

                        htmlHistory += `
                            <div class="profile-order-card" id="order-${order.ma_don_hang}" data-status="${order.trang_thai_don}">
                                <div class="profile-order-card__header">
                                    <div>
                                        <p class="profile-order-card__id">Đơn hàng #${order.ma_don_hang}</p>
                                        <p class="profile-order-card__date">Ngày đặt: ${date}</p>
                                        <p class="profile-order-card__date mt-1 text-pink-600">Thanh toán: <span class="font-medium">${paymentMethodText}</span></p>
                                    </div>
                                    <span class="profile-order-card__status ${statusColor}">${statusText}</span>
                                </div>
                                ${productsHtml}
                                <div class="profile-order-card__footer">
                                    ${voucherHtml}
                                    <div class="profile-order-card__total-row">
                                        <span class="profile-order-card__total-label">Tổng thanh toán:</span>
                                        <span class="profile-order-card__total-value">${tongThanhToan.toLocaleString()} đ</span>
                                    </div>
                                    <div class="w-full">${actionBtnHtml}</div>
                                </div>
                            </div>`;
                    } 
                    // TAB QUẢN LÝ ĐƠN HÀNG
                    else {
                        hasOrders = true;
                        let activeStatusText = 'Chờ xác nhận';
                        let activeStatusColor = 'bg-yellow-100 text-yellow-700';
                        let actionBtnHtml = '';

                        if (order.trang_thai_don === 'cho_xac_nhan' || order.trang_thai_don === 'da_xac_nhan') {
                            const orderDateObj = new Date(order.ngay_dat);
                            const diffMinutes = Math.floor((new Date() - orderDateObj) / (1000 * 60));
                            
                            if (order.trang_thai_don === 'da_xac_nhan') {
                                activeStatusText = 'Đã xác nhận';
                                activeStatusColor = 'bg-purple-100 text-purple-700';
                            }
                            
                            if (diffMinutes <= 30) {
                                actionBtnHtml = `
                                    <button onclick="updateOrderStatus(${order.ma_don_hang}, 'da_huy')" class="mt-4 w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition text-sm shadow">
                                        Hủy đơn hàng
                                    </button>
                                    <p class="text-xs text-center text-gray-400 mt-2">Bạn có thể hủy đơn trong vòng 30 phút (còn lại ${30 - diffMinutes} phút)</p>`;
                            } else {
                                if (order.trang_thai_don === 'cho_xac_nhan') {
                                    actionBtnHtml = `<div class="mt-4 text-sm text-center text-gray-500 bg-gray-50 border border-gray-200 py-2 rounded-lg w-full">Đã quá 30 phút kể từ lúc đặt hàng, không thể tự hủy đơn.</div>`;
                                }
                            }
                        } else if (order.trang_thai_don === 'dang_giao') {
                            activeStatusText = 'Đang giao hàng';
                            activeStatusColor = 'bg-blue-100 text-blue-700';
                        } else if (order.trang_thai_don === 'dang_tra_hang' || order.trang_thai_don === 'tra_hang_hoan_tien') {
                            activeStatusText = 'Đang xử lý trả hàng';
                            activeStatusColor = 'bg-orange-100 text-orange-700 border border-orange-300'; 
                            actionBtnHtml = `<div class="mt-4 text-sm text-left text-orange-600 bg-orange-50 p-3 rounded-lg w-full">Shop đang xử lý yêu cầu trả hàng của bạn.</div>`;
                        }
                        // Thêm đoạn này vào:
                        else if (order.trang_thai_don === 'tu_choi_tra_hang') {
                            activeStatusText = 'Bị từ chối trả hàng';
                            activeStatusColor = 'bg-red-100 text-red-700 border border-red-300';
                            actionBtnHtml = `<div class="mt-4 text-sm text-left text-red-600 bg-red-50 p-3 rounded-lg w-full border border-red-200">Yêu cầu trả hàng của bạn đã bị từ chối vì lý do sai quy định hoàn trả.</div>`;
                        }
                            htmlOrders += `
                            <div class="profile-order-card" id="order-${order.ma_don_hang}" data-status="${order.trang_thai_don}">
                                <div class="profile-order-card__header" style="align-items: center; margin-bottom: 1rem;">
                                    <div>
                                        <p class="profile-order-card__id">Đơn hàng #${order.ma_don_hang}</p>
                                        <p class="profile-order-card__date">Ngày đặt: ${date}</p>
                                        <p class="profile-order-card__date mt-1 text-pink-600">Thanh toán: <span class="font-medium">${paymentMethodText}</span></p>
                                    </div>
                                    <span class="profile-order-card__status ${activeStatusColor}">${activeStatusText}</span>
                                </div>
                                ${productsHtml}
                                <div class="profile-order-card__footer">
                                    ${voucherHtml}
                                    <div class="profile-order-card__total-row">
                                        <span class="profile-order-card__total-label">Tổng thanh toán:</span>
                                        <span class="profile-order-card__total-value">${tongThanhToan.toLocaleString()} đ</span>
                                    </div>
                                    <div class="w-full">${actionBtnHtml}</div>
                                </div>
                            </div>`;
                    }
                });
                
                htmlOrders += '</div>';
                htmlHistory += '</div>';
                
                if (hasOrders) {
                    document.getElementById('orders-filters').style.display = 'flex';
                    ordersContainer.innerHTML = htmlOrders;
                    ordersContainer.classList.remove('profile-orders-empty');
                }
                if (hasHistory) {
                    document.getElementById('history-filters').style.display = 'flex';
                    historyContainer.innerHTML = htmlHistory;
                    historyContainer.classList.remove('profile-orders-empty');
                }
                
                // Cuộn đến đơn hàng nếu có hash trên URL
                if (window.location.hash) {
                    setTimeout(() => {
                        const target = document.querySelector(window.location.hash);
                        if (target) {
                            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            target.style.boxShadow = '0 0 0 2px #f472b6'; // pink-400 highlight
                            setTimeout(() => {
                                target.style.boxShadow = '';
                            }, 2000);
                        }
                    }, 300);
                }
            }
        } catch (error) {
            console.error('Lỗi tải đơn hàng:', error);
        }
    }

   // Thay thế đoạn xử lý trạng thái 'tra_hang_hoan_tien' trong hàm updateOrderStatus cũ
async function updateOrderStatus(maDonHang, trangThaiMoi) {
    if (trangThaiMoi === 'tra_hang_hoan_tien') {
        // Mở Modal lên thay vì dùng prompt
        document.getElementById('refund-order-id').value = maDonHang;
        document.getElementById('refund-modal').classList.remove('hidden');
        return; // Dừng tại đây, chờ người dùng bấm "Xác nhận gửi" trong Modal
    }

    // Các trạng thái khác (hủy đơn, hoàn thành) giữ nguyên logic cũ của ông
    let lyDoHuyDon = null;
    if (trangThaiMoi === 'da_huy') {
        lyDoHuyDon = prompt("Vui lòng nhập lý do hủy đơn hàng (Bắt buộc):");
        if (!lyDoHuyDon || lyDoHuyDon.trim() === "") {
            alert("Bạn phải nhập lý do thì hệ thống mới xử lý hủy đơn!");
            return;
        }
    } else if (trangThaiMoi === 'hoan_thanh') {
        if (!confirm("Xác nhận bạn đã nhận được hàng và sản phẩm không có vấn đề gì?")) return;
    }

    // Gọi API cho Hủy và Hoàn thành
    callUpdateStatusAPI(maDonHang, trangThaiMoi, null, lyDoHuyDon);
}

// HÀM MỚI: Đóng Modal
function closeRefundModal() {
    document.getElementById('refund-modal').classList.add('hidden');
}

// HÀM MỚI: Xử lý nút "Xác nhận gửi" trong Modal
function submitRefundRequest() {
    const isAgree = document.getElementById('refund-agree').checked;
    if (!isAgree) {
        alert("Vui lòng đọc Quy định đổi trả và tích vào ô cam kết trước khi gửi yêu cầu!");
        return;
    }
    const maDonHang = document.getElementById('refund-order-id').value;
    const nganHang = document.getElementById('refund-bank').value;
    const soTaiKhoan = document.getElementById('refund-account').value;
    const chuTaiKhoan = document.getElementById('refund-owner').value;
    const lyDo = document.getElementById('refund-reason').value;

    if (!nganHang || !soTaiKhoan || !chuTaiKhoan || !lyDo) {
        alert("Vui lòng nhập đầy đủ thông tin bảo mật để đối chiếu!");
        return;
    }

    // Đóng modal sau khi lấy đủ data
    closeRefundModal();

    // Gọi API với đầy đủ thông tin đối chiếu
    callUpdateStatusAPI(maDonHang, 'tra_hang_hoan_tien', lyDo, null, nganHang, soTaiKhoan, chuTaiKhoan);
}

// HÀM CHUNG: Gọi API lên Node.js Backend
async function callUpdateStatusAPI(maDonHang, trangThaiMoi, lyDoTra, lyDoHuy, nganHang = null, soTk = null, chuTk = null) {
    const token = localStorage.getItem('token');
    try {
        const response = await fetch(`${API_URL}/order/update-status`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token 
            },
            body: JSON.stringify({ 
                ma_don_hang: maDonHang, 
                trang_thai: trangThaiMoi,
                ly_do_tra_hang: lyDoTra,
                ly_do_huy_don: lyDoHuy,
                ngan_hang_hoan_tien: nganHang,
                stk_hoan_tien: soTk,
                chu_tk_hoan_tien: chuTk
            })
        });

        const result = await response.json();
        if (result.success) {
            alert(result.message);
            location.reload(); 
        } else {
            alert("Lỗi: " + result.message);
        }
    } catch (error) {
        alert("Có lỗi kết nối đến server Node.js!");
    }
}

// 6. CHỨC NĂNG LỌC ĐƠN HÀNG
function filterOrders(tab, status, event) {
    // Cập nhật trạng thái active cho nút filter
    const filterContainer = document.getElementById(tab + '-filters');
    if (filterContainer) {
        filterContainer.querySelectorAll('.profile-filter-btn').forEach(btn => {
            btn.classList.remove('profile-filter-btn--active');
        });
        if (event && event.currentTarget) {
            event.currentTarget.classList.add('profile-filter-btn--active');
        }
    }

    // Ẩn/hiện các đơn hàng
    const containerId = tab === 'history' ? 'history-container' : 'orders-container';
    const container = document.getElementById(containerId);
    
    if (container) {
        const orderCards = container.querySelectorAll('.profile-order-card');
        let visibleCount = 0;

        orderCards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            
            let isMatch = false;
            if (status === 'all') {
                isMatch = true;
            } else if (status === 'dang_tra_hang' && (cardStatus === 'dang_tra_hang' || cardStatus === 'tra_hang_hoan_tien')) {
                isMatch = true;
            } else if (cardStatus === status) {
                isMatch = true;
            }
            
            if (isMatch) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // Nếu không có đơn hàng nào thỏa mãn filter
        let emptyMsgEl = container.querySelector('.filter-empty-msg');
        if (visibleCount === 0 && orderCards.length > 0) {
            if (!emptyMsgEl) {
                emptyMsgEl = document.createElement('div');
                emptyMsgEl.className = 'filter-empty-msg profile-orders-empty';
                emptyMsgEl.textContent = 'Không có đơn hàng nào ở trạng thái này.';
                container.appendChild(emptyMsgEl);
            }
            emptyMsgEl.style.display = 'block';
        } else if (emptyMsgEl) {
            emptyMsgEl.style.display = 'none';
        }
    }
}
</script>

{{-- Refund Modal --}}
<div id="refund-modal" class="refund-modal-overlay hidden">
    <div class="refund-modal">
        <h3 class="refund-modal__title">Yêu cầu hoàn trả</h3>
        <p class="refund-modal__warning">* Vui lòng nhập thông tin để Admin đối chiếu với hồ sơ gốc nhằm bảo vệ tài sản của bạn.</p>

        <input type="hidden" id="refund-order-id">

        <div class="refund-modal__fields">
            <div>
                <label class="refund-modal__label">Ngân hàng & Số tài khoản:</label>
                <div class="refund-modal__input-group">
                    <input type="text" id="refund-bank" list="bank-list" class="refund-modal__input refund-modal__input--1-3" placeholder="Ngân hàng">
                    <input type="number" id="refund-account" class="refund-modal__input refund-modal__input--2-3" placeholder="Số tài khoản">
                </div>
            </div>
            <div>
                <label class="refund-modal__label">Chủ tài khoản:</label>
                <input type="text" id="refund-owner" class="refund-modal__input" style="text-transform: uppercase;" placeholder="NGUYEN VAN A">
            </div>
            <div>
                <label class="refund-modal__label">Lý do trả hàng:</label>
                <textarea id="refund-reason" class="refund-modal__textarea" rows="2" placeholder="Ví dụ: Sản phẩm bị tràn, vỡ..."></textarea>
            </div>

            <div class="refund-modal__policy">
                <p class="refund-modal__policy-title">📜 QUY ĐỊNH ĐỔI TRẢ CỦA SHOP:</p>
                <ul class="refund-modal__policy-list" style="margin-bottom: 15px;">
                    <li>Sản phẩm còn nguyên bao bì, chưa qua sử dụng.</li>
                    <li>Không bị nứt vỡ do khách hàng.</li>
                    <li>Chỉ đổi trả nếu lỗi do NSX hoặc giao sai.</li>
                    <li>Yêu cầu tạo trong vòng 3 ngày kể từ khi nhận hàng.</li>
                    <li><b>Shop có quyền từ chối</b> nếu sai điều kiện!</li>
                    <li style="color: #d81b60; font-weight: bold;">Lưu ý: Chúng tôi chỉ hoàn trả tiền bằng hình thức bank và không hoàn trả bằng tiền mặt</li>
                </ul>
                <label class="refund-modal__agree-label">
                    <input type="checkbox" id="refund-agree" class="refund-modal__agree-checkbox">
                    <span class="refund-modal__agree-text">Tôi đã đọc và cam kết đáp ứng đủ điều kiện</span>
                </label>
            </div>
        </div>
        
        <datalist id="bank-list">
            <option value="Vietcombank (Ngân hàng TMCP Ngoại thương)"></option>
            <option value="VietinBank (Ngân hàng TMCP Công Thương)"></option>
            <option value="BIDV (Ngân hàng Đầu tư và Phát triển)"></option>
            <option value="Agribank (Ngân hàng NN & PTNT)"></option>
            <option value="Techcombank (Ngân hàng Kỹ thương)"></option>
            <option value="MB Bank (Ngân hàng Quân đội)"></option>
            <option value="VPBank (Ngân hàng Việt Nam Thịnh Vượng)"></option>
            <option value="ACB (Ngân hàng Á Châu)"></option>
            <option value="Sacombank (Ngân hàng Sài Gòn Thương Tín)"></option>
            <option value="TPBank (Ngân hàng Tiên Phong)"></option>
            <option value="VIB (Ngân hàng Quốc tế)"></option>
            <option value="HDBank (Ngân hàng Phát triển TPHCM)"></option>
            <option value="SHB (Ngân hàng Sài Gòn - Hà Nội)"></option>
        </datalist>

        <div class="refund-modal__actions">
            <button onclick="closeRefundModal()" class="refund-modal__btn-cancel">Hủy</button>
            <button onclick="submitRefundRequest()" class="refund-modal__btn-submit">Xác nhận gửi</button>
        </div>
    </div>
</div>
@endsection