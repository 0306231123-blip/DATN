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

                    <div class="profile-form__group profile-form__group--full">
                        <label class="profile-form__label">Địa chỉ giao hàng</label>
                        <div class="text-sm mb-2" style="color: #4b5563;">Địa chỉ hiện tại: <span id="current-address-display" style="font-weight: 700; color: #1f2937;">Chưa có</span></div>
                        <input type="hidden" id="input-address">
                        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                            <select id="province" class="profile-form__input" style="flex: 1; min-width: 200px;">
                                <option value="" selected>-- Giữ nguyên hoặc Chọn Tỉnh/TP mới --</option>
                            </select>
                            <select id="ward" class="profile-form__input cursor-pointer" style="flex: 1; min-width: 200px;">
                                <option value="" selected>-- Chọn Phường / Xã --</option>
                            </select>
                            <input type="text" id="street" class="profile-form__input" style="flex: 2; min-width: 300px;" placeholder="Nhập số nhà, tên đường mới...">
                        </div>
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
                
                <div class="profile-search-bar" id="history-search-bar" style="display: none;">
                    <div class="profile-search-group">
                        <input type="text" id="search-history-input" class="profile-form__input" style="width: 100%" placeholder="Nhập tên sản phẩm cần tìm..." onkeypress="if(event.key === 'Enter') applyFilters('history')">
                    </div>
                    <div class="profile-search-group profile-search-group--date">
                        <input type="date" id="date-from-history" class="profile-form__input" title="Từ ngày" onchange="document.getElementById('date-to-history').min = this.value">
                        <span>-</span>
                        <input type="date" id="date-to-history" class="profile-form__input" title="Đến ngày">
                    </div>
                    <button type="button" class="profile-form__save-btn" style="padding: 0.75rem 1.5rem;" onclick="applyFilters('history')">Tìm kiếm</button>
                </div>

                <!-- Filter buttons -->
                <div class="profile-order-filters" id="history-filters" style="display: none;">
                    <button class="profile-filter-btn profile-filter-btn--active" onclick="filterOrders('history', 'all', event)">Tất cả</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'hoan_thanh', event)">Hoàn thành</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'giao_thanh_cong', event)">Giao thành công</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'da_huy', event)">Đã hủy</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'da_tra_hang', event)">Đã trả hàng</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'tu_choi_tra_hang', event)">Từ chối trả hàng</button>
                    <button class="profile-filter-btn" onclick="filterOrders('history', 'khong_du_dieu_kien', event)">Không đủ điều kiện</button>
                </div>

                <div id="history-container" class="profile-orders-empty">
                    Chưa có dữ liệu lịch sử mua hàng.
                </div>
            </div>

            {{-- Tab: Đơn hàng --}}
            <div id="tab-orders" class="tab-content profile-tab">
                <h2 class="profile-tab__title">Quản lý đơn hàng</h2>

                <div class="profile-search-bar" id="orders-search-bar" style="display: none;">
                    <div class="profile-search-group">
                        <input type="text" id="search-orders-input" class="profile-form__input" style="width: 100%" placeholder="Nhập tên sản phẩm cần tìm..." onkeypress="if(event.key === 'Enter') applyFilters('orders')">
                    </div>
                    <div class="profile-search-group profile-search-group--date">
                        <input type="date" id="date-from-orders" class="profile-form__input" title="Từ ngày" onchange="document.getElementById('date-to-orders').min = this.value">
                        <span>-</span>
                        <input type="date" id="date-to-orders" class="profile-form__input" title="Đến ngày">
                    </div>
                    <button type="button" class="profile-form__save-btn" style="padding: 0.75rem 1.5rem;" onclick="applyFilters('orders')">Tìm kiếm</button>
                </div>

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
    let originalData = {};

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
                document.getElementById('current-address-display').textContent = user.dia_chi || 'Chưa có';
                document.getElementById('input-skin-type').value = user.loai_da || '';
                
                // Load dữ liệu ngân hàng
                document.getElementById('input-bank-name').value = user.ngan_hang || '';
                document.getElementById('input-bank-account').value = user.so_tai_khoan || '';
                document.getElementById('input-bank-owner').value = user.chu_tai_khoan || '';

                // Lưu lại dữ liệu ban đầu
                originalData = {
                    ho_ten: user.ho_ten || '',
                    so_dien_thoai: user.so_dien_thoai || '',
                    dia_chi: user.dia_chi || '',
                    loai_da: user.loai_da || '',
                    ngan_hang: user.ngan_hang || '',
                    so_tai_khoan: user.so_tai_khoan || '',
                    chu_tai_khoan: user.chu_tai_khoan || ''
                };

                // Auto-fill dropdown địa chỉ
                if (user.dia_chi) {
                    let parts = user.dia_chi.split(',').map(s => s.trim());
                    if (parts.length >= 3) {
                        let pName = parts[parts.length - 1];
                        let wName = parts[parts.length - 2];
                        let sName = parts.slice(0, parts.length - 2).join(', ');

                        let checkExist = setInterval(function() {
                            const provinceSelect = document.getElementById('province');
                            const wardSelect = document.getElementById('ward');
                            const streetInput = document.getElementById('street');
                            if (provinceSelect && provinceSelect.options.length > 1) {
                                clearInterval(checkExist);
                                let foundProvince = Array.from(provinceSelect.options).find(opt => opt.getAttribute('data-name') === pName);
                                if (foundProvince) {
                                    provinceSelect.value = foundProvince.value;
                                    provinceSelect.dispatchEvent(new Event('change'));

                                    let checkWardExist = setInterval(function() {
                                        if (wardSelect.options.length > 1) {
                                            clearInterval(checkWardExist);
                                            let foundWard = Array.from(wardSelect.options).find(opt => opt.getAttribute('data-name') === wName);
                                            if (foundWard) {
                                                wardSelect.value = foundWard.value;
                                            }
                                            streetInput.value = sName;
                                        }
                                    }, 100);
                                }
                            }
                        }, 100);
                    }
                }

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

        // Tải API Tỉnh/Thành phố cho Form cập nhật địa chỉ
        const provinceSelect = document.getElementById('province');
        const wardSelect = document.getElementById('ward');
        
        fetch('https://provinces.open-api.vn/api/v2/p/')
            .then(res => res.json())
            .then(data => {
                data.forEach(province => {
                    let option = document.createElement('option');
                    option.value = province.code; 
                    option.text = province.name;
                    option.setAttribute('data-name', province.name);
                    provinceSelect.appendChild(option);
                });
            });

        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            wardSelect.innerHTML = '<option value="" selected>-- Chọn Phường / Xã --</option>';
            if (!provinceId) return;
            
            fetch(`https://provinces.open-api.vn/api/v2/p/${provinceId}?depth=2`)
                .then(res => res.json())
                .then(data => {
                    if (data.wards) {
                        data.wards.forEach(ward => {
                            let option = document.createElement('option');
                            option.value = ward.code;
                            option.text = ward.name;
                            option.setAttribute('data-name', ward.name);
                            wardSelect.appendChild(option);
                        });
                    }
                });
        });
    });

    // 3. GỬI DỮ LIỆU CẬP NHẬT LÊN SERVER
    document.getElementById('update-profile-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = document.getElementById('btn-save-profile');
        btn.textContent = 'Đang lưu...';
        btn.disabled = true;

        const phoneInput = document.getElementById('input-phone').value.trim();
        const phoneRegex = /^0\d{9}$/;
        if (phoneInput && !phoneRegex.test(phoneInput)) {
            alert('🛑 Số điện thoại không hợp lệ! Vui lòng nhập đủ 10 số và bắt đầu bằng số 0.');
            document.getElementById('input-phone').focus();
            btn.textContent = 'Lưu thay đổi';
            btn.disabled = false;
            return;
        }

        let diaChiMoi = document.getElementById('input-address').value;
        const provinceOption = document.getElementById('province').options[document.getElementById('province').selectedIndex];
        const wardOption = document.getElementById('ward').options[document.getElementById('ward').selectedIndex];
        const provinceName = (provinceOption && provinceOption.value !== "") ? provinceOption.getAttribute('data-name') : null;
        const wardName = (wardOption && wardOption.value !== "") ? wardOption.getAttribute('data-name') : null;
        const street = document.getElementById('street').value.trim();

        if (provinceName || wardName || street) {
            if (!provinceName) { alert('🛑 Vui lòng chọn Tỉnh / Thành phố!'); document.getElementById('province').focus(); btn.textContent = 'Lưu thay đổi'; btn.disabled = false; return; }
            if (!wardName) { alert('🛑 Vui lòng chọn Phường / Xã!'); document.getElementById('ward').focus(); btn.textContent = 'Lưu thay đổi'; btn.disabled = false; return; }
            if (!street || street.length < 5) { alert('🛑 Vui lòng nhập chi tiết Số nhà, tên đường (tối thiểu 5 ký tự)!'); document.getElementById('street').focus(); btn.textContent = 'Lưu thay đổi'; btn.disabled = false; return; }
            diaChiMoi = `${street}, ${wardName}, ${provinceName}`;
        }

        const oldPassword = document.getElementById('input-old-password').value;
        const newPassword = document.getElementById('input-password').value;
        const currentName = document.getElementById('input-name').value;
        const currentSkinType = document.getElementById('input-skin-type').value;
        const currentBankName = document.getElementById('input-bank-name').value;
        const currentBankAccount = document.getElementById('input-bank-account').value;
        const currentBankOwner = document.getElementById('input-bank-owner').value;

        // KIỂM TRA XEM CÓ THAY ĐỔI GÌ KHÔNG
        if (
            currentName === originalData.ho_ten &&
            phoneInput === originalData.so_dien_thoai &&
            diaChiMoi === originalData.dia_chi &&
            currentSkinType === originalData.loai_da &&
            currentBankName === originalData.ngan_hang &&
            currentBankAccount === originalData.so_tai_khoan &&
            currentBankOwner === originalData.chu_tai_khoan &&
            !oldPassword && !newPassword
        ) {
            alert('Chưa có thông tin mới để thay đổi.');
            btn.textContent = 'Lưu thay đổi';
            btn.disabled = false;
            return;
        }

        const updateData = {
            ho_ten: currentName,
            so_dien_thoai: phoneInput,
            dia_chi: diaChiMoi,
            loai_da: currentSkinType,
            
            // Gửi dữ liệu ngân hàng lên backend
            ngan_hang: currentBankName,
            so_tai_khoan: currentBankAccount,
            chu_tai_khoan: currentBankOwner
        };

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
                
                // Cập nhật lại originalData để check cho lần sau
                originalData = {
                    ho_ten: updateData.ho_ten,
                    so_dien_thoai: updateData.so_dien_thoai,
                    dia_chi: updateData.dia_chi,
                    loai_da: updateData.loai_da,
                    ngan_hang: updateData.ngan_hang,
                    so_tai_khoan: updateData.so_tai_khoan,
                    chu_tai_khoan: updateData.chu_tai_khoan
                };

                document.getElementById('sidebar-name').textContent = updateData.ho_ten;
                document.getElementById('input-old-password').value = ''; 
                document.getElementById('input-password').value = ''; 
                document.getElementById('input-address').value = diaChiMoi;
                document.getElementById('current-address-display').textContent = diaChiMoi;
                document.getElementById('province').value = "";
                document.getElementById('ward').innerHTML = '<option value="" selected>-- Chọn Phường / Xã --</option>';
                document.getElementById('street').value = "";
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
                window.ordersMap = {};
                
                result.data.forEach((order, index) => {
                    window.ordersMap[order.ma_don_hang] = order;
                    const orderDateFull = new Date(order.ngay_dat);
                    const timeString = orderDateFull.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });
                    const dateString = orderDateFull.toLocaleDateString('vi-VN');
                    const date = `${timeString} - ${dateString}`;
                    
                    const y = orderDateFull.getFullYear();
                    const m = String(orderDateFull.getMonth() + 1).padStart(2, '0');
                    const d = String(orderDateFull.getDate()).padStart(2, '0');
                    const dateISO = `${y}-${m}-${d}`;
                    const productNames = order.chi_tiet ? order.chi_tiet.map(item => item.ten_san_pham.toLowerCase()).join(' | ') : '';
                    
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
                                <span>-${soTienGiam.toLocaleString()} VNĐ</span>
                            </div>
                        `;
                    }
                    
                    let productsHtml = '<div class="profile-order-card__products">';
                    if (order.chi_tiet && order.chi_tiet.length > 0) {
                        order.chi_tiet.forEach(item => {
                            const itemPrice = parseInt(item.don_gia).toLocaleString() + ' VNĐ';
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
                    if(['giao_thanh_cong', 'da_huy', 'hoan_thanh', 'da_tra_hang', 'tu_choi_tra_hang', 'khong_du_dieu_kien'].includes(order.trang_thai_don)) {
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
                        else if (order.trang_thai_don === 'khong_du_dieu_kien') {
                            statusColor = 'bg-gray-200 text-gray-500 border border-gray-300';
                            statusText = 'Không đủ điều kiện';
                            actionBtnHtml = `<div class="mt-4 text-center text-gray-500 font-bold w-full bg-gray-100 py-2 rounded-lg">Đơn hàng không đủ điều kiện xử lý.</div>`;
                        }

                        htmlHistory += `
                            <div class="profile-order-card" id="order-${order.ma_don_hang}" data-status="${order.trang_thai_don}" data-date="${dateISO}" data-products="${productNames}" onclick="showOrderDetails(${order.ma_don_hang}, event)" style="cursor: pointer;">
                                <div class="profile-order-card__header">
                                    <div>
                                        <p class="profile-order-card__id">Đơn hàng ${order.ma_don_hang_custom || '#' + order.ma_don_hang}</p>
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
                                        <span class="profile-order-card__total-value">${tongThanhToan.toLocaleString()} VNĐ</span>
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
                            <div class="profile-order-card" id="order-${order.ma_don_hang}" data-status="${order.trang_thai_don}" data-date="${dateISO}" data-products="${productNames}" onclick="showOrderDetails(${order.ma_don_hang}, event)" style="cursor: pointer;">
                                <div class="profile-order-card__header" style="align-items: center; margin-bottom: 1rem;">
                                    <div>
                                        <p class="profile-order-card__id">Đơn hàng ${order.ma_don_hang_custom || '#' + order.ma_don_hang}</p>
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
                                        <span class="profile-order-card__total-value">${tongThanhToan.toLocaleString()} VNĐ</span>
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
                    document.getElementById('orders-search-bar').style.display = 'flex';
                    ordersContainer.innerHTML = htmlOrders;
                    ordersContainer.classList.remove('profile-orders-empty');
                }
                if (hasHistory) {
                    document.getElementById('history-filters').style.display = 'flex';
                    document.getElementById('history-search-bar').style.display = 'flex';
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

function showOrderDetails(orderId, event) {
    if (event && event.target && event.target.closest('button')) return;
    
    const order = window.ordersMap[orderId];
    if(!order) return;

    document.getElementById('modal-order-id').textContent = order.ma_don_hang_custom || '#' + order.ma_don_hang;
    
    const orderDateFull = new Date(order.ngay_dat);
    document.getElementById('modal-order-date').textContent = orderDateFull.toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }) + ' - ' + orderDateFull.toLocaleDateString('vi-VN');

    let paymentMethodText = 'Thanh toán khi nhận hàng (COD)';
    switch(order.phuong_thuc_thanh_toan) {
        case 'tien_mat': paymentMethodText = 'Thanh toán khi nhận hàng (COD)'; break;
        case 'chuyen_khoan': paymentMethodText = 'Chuyển khoản Ngân hàng'; break;
        case 'vi_dien_tu': paymentMethodText = 'Ví điện tử (Momo)'; break;
        case 'the_tin_dung': paymentMethodText = 'Thẻ tín dụng / Ghi nợ'; break;
    }
    document.getElementById('modal-order-payment').textContent = paymentMethodText;

    let statusText = order.trang_thai_don;
    switch(order.trang_thai_don) {
        case 'cho_xac_nhan': statusText = 'Chờ xác nhận'; break;
        case 'da_xac_nhan': statusText = 'Đã xác nhận'; break;
        case 'dang_giao': statusText = 'Đang giao'; break;
        case 'giao_thanh_cong': statusText = 'Giao thành công'; break;
        case 'hoan_thanh': statusText = 'Hoàn thành'; break;
        case 'da_huy': statusText = 'Đã hủy'; break;
        case 'dang_tra_hang': statusText = 'Đang xử lý trả hàng'; break;
        case 'da_tra_hang': statusText = 'Đã trả hàng/Hoàn tiền'; break;
        case 'tu_choi_tra_hang': statusText = 'Bị từ chối trả hàng'; break;
        case 'tra_hang_hoan_tien': statusText = 'Yêu cầu trả hàng'; break;
        case 'khong_du_dieu_kien': statusText = 'Không đủ điều kiện'; break;
    }
    document.getElementById('modal-order-status').textContent = statusText;

    document.getElementById('modal-customer-name').textContent = order.ho_ten_nguoi_nhan || 'Không có';
    document.getElementById('modal-customer-phone').textContent = order.so_dien_thoai_nhan || 'Không có';
    document.getElementById('modal-customer-address').textContent = order.dia_chi_giao || 'Không có';

    const subtotal = parseInt(order.tong_tien_hang || 0);
    const shipping = parseInt(order.phi_van_chuyen || 0);
    const discount = parseInt(order.so_tien_giam || 0);
    const total = parseInt(order.tong_thanh_toan || 0);

    document.getElementById('modal-order-subtotal').textContent = subtotal.toLocaleString() + ' VNĐ';
    document.getElementById('modal-order-shipping').textContent = (shipping > 0 ? shipping.toLocaleString() + ' VNĐ' : 'Miễn phí');
    document.getElementById('modal-order-discount').textContent = '-' + discount.toLocaleString() + ' VNĐ';
    document.getElementById('modal-order-total').textContent = total.toLocaleString() + ' VNĐ';

    let productsHtml = '';
    if (order.chi_tiet && order.chi_tiet.length > 0) {
        order.chi_tiet.forEach(item => {
            const itemPrice = parseInt(item.don_gia).toLocaleString() + ' VNĐ';
            const itemTotal = (parseInt(item.don_gia) * item.so_luong).toLocaleString() + ' VNĐ';
            productsHtml += `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.75rem; border-bottom: 1px dashed #eee;">
                    <div style="flex: 1;">
                        <p style="font-weight: bold; margin: 0; font-size: 0.95rem; color: #374151;">${item.ten_san_pham}</p>
                        <p style="margin: 0; font-size: 0.85rem; color: #6b7280; margin-top: 4px;">Số lượng: ${item.so_luong} x ${itemPrice}</p>
                    </div>
                    <div style="font-weight: bold; color: #111827;">${itemTotal}</div>
                </div>
            `;
        });
    }
    document.getElementById('modal-order-products').innerHTML = productsHtml;

    document.getElementById('order-details-modal').style.display = 'flex';
}

function closeOrderDetailsModal() {
    document.getElementById('order-details-modal').style.display = 'none';
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
let currentHistoryStatus = 'all';
let currentOrdersStatus = 'all';

function filterOrders(tab, status, event) {
    if (tab === 'history') currentHistoryStatus = status;
    else currentOrdersStatus = status;

    const filterContainer = document.getElementById(tab + '-filters');
    if (filterContainer && event && event.currentTarget) {
        filterContainer.querySelectorAll('.profile-filter-btn').forEach(btn => {
            btn.classList.remove('profile-filter-btn--active');
        });
        event.currentTarget.classList.add('profile-filter-btn--active');
    }
    
    applyFilters(tab);
}

function applyFilters(tab) {
    const containerId = tab === 'history' ? 'history-container' : 'orders-container';
    const container = document.getElementById(containerId);
    if (!container) return;

    const searchInput = (document.getElementById('search-' + tab + '-input')?.value || '').toLowerCase();
    const dateFrom = document.getElementById('date-from-' + tab)?.value || '';
    const dateTo = document.getElementById('date-to-' + tab)?.value || '';
    const currentStatus = tab === 'history' ? currentHistoryStatus : currentOrdersStatus;

    if (dateFrom && dateTo && dateFrom > dateTo) {
        alert("Ngày giới hạn phía sau không được nhỏ hơn ngày phía trước!");
        return;
    }

    const orderCards = container.querySelectorAll('.profile-order-card');
    let visibleCount = 0;

    orderCards.forEach(card => {
        const cardStatus = card.getAttribute('data-status');
        const cardDate = card.getAttribute('data-date'); 
        const cardProducts = card.getAttribute('data-products') || '';

        // Status check
        let matchStatus = false;
        if (currentStatus === 'all') matchStatus = true;
        else if (currentStatus === 'dang_tra_hang' && (cardStatus === 'dang_tra_hang' || cardStatus === 'tra_hang_hoan_tien')) matchStatus = true;
        else if (cardStatus === currentStatus) matchStatus = true;

        // Search check
        let matchSearch = true;
        if (searchInput && !cardProducts.includes(searchInput)) {
            matchSearch = false;
        }

        // Date check
        let matchDate = true;
        if (dateFrom && cardDate < dateFrom) matchDate = false;
        if (dateTo && cardDate > dateTo) matchDate = false;

        if (matchStatus && matchSearch && matchDate) {
            card.style.display = 'flex';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });

    let emptyMsgEl = container.querySelector('.filter-empty-msg');
    if (visibleCount === 0 && orderCards.length > 0) {
        if (!emptyMsgEl) {
            emptyMsgEl = document.createElement('div');
            emptyMsgEl.className = 'filter-empty-msg profile-orders-empty';
            emptyMsgEl.textContent = 'Không tìm thấy đơn hàng nào phù hợp với bộ lọc.';
            container.appendChild(emptyMsgEl);
        }
        emptyMsgEl.style.display = 'block';
    } else if (emptyMsgEl) {
        emptyMsgEl.style.display = 'none';
    }
}

// Lắng nghe sự kiện đổi hash (VD: bấm thông báo khi đang ở cùng tab)
window.addEventListener('hashchange', function() {
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
        }, 100); // Đợi 1 chút phòng khi trang chưa render xong
    }
});
</script>

{{-- Order Details Modal --}}
<div id="order-details-modal" class="refund-modal-overlay hidden" style="z-index: 1000; display: none; justify-content: center; align-items: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
    <div class="refund-modal" style="position: relative; max-height: 90vh; overflow-y: auto; width: 600px; max-width: 95%; background: white; padding: 2rem; border-radius: 1rem;">
        <button onclick="closeOrderDetailsModal()" style="position: absolute; top: 1rem; right: 1rem; background: transparent; border: none; font-size: 2rem; cursor: pointer; color: #9ca3af; line-height: 1; padding: 0.25rem 0.75rem;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#9ca3af'" title="Đóng cửa sổ">
            &times;
        </button>
        <h3 class="refund-modal__title" style="margin-bottom: 1.5rem; text-align: center; margin-top: 0;">Chi tiết đơn hàng <span id="modal-order-id"></span></h3>
        
        <div style="margin-bottom: 1.5rem; background: #fdf2f8; padding: 1rem; border-radius: 0.75rem;">
            <p style="margin-bottom: 0.5rem; color: #4b5563;"><b>Trạng thái:</b> <span id="modal-order-status" style="font-weight: bold; color: var(--user-pink-600); text-transform: uppercase;"></span></p>
            <p style="margin-bottom: 0.5rem; color: #4b5563;"><b>Ngày đặt:</b> <span id="modal-order-date" style="color: #1f2937;"></span></p>
            <p style="margin-bottom: 0; color: #4b5563;"><b>Thanh toán:</b> <span id="modal-order-payment" style="color: #1f2937;"></span></p>
        </div>

        <h4 style="font-weight: bold; color: #111827; border-bottom: 2px solid #f3f4f6; padding-bottom: 0.5rem; margin-bottom: 1rem; font-size: 1.1rem;">Thông tin khách hàng</h4>
        <div style="margin-bottom: 1.5rem; font-size: 0.95rem; color: #374151;">
            <p style="margin-bottom: 0.5rem;"><b>Họ tên:</b> <span id="modal-customer-name"></span></p>
            <p style="margin-bottom: 0.5rem;"><b>Số điện thoại:</b> <span id="modal-customer-phone"></span></p>
            <p style="margin-bottom: 0;"><b>Địa chỉ giao hàng:</b> <span id="modal-customer-address"></span></p>
        </div>

        <h4 style="font-weight: bold; color: #111827; border-bottom: 2px solid #f3f4f6; padding-bottom: 0.5rem; margin-bottom: 1rem; font-size: 1.1rem;">Sản phẩm</h4>
        <div id="modal-order-products" style="margin-bottom: 1.5rem;">
            <!-- Products will be injected here -->
        </div>

        <div style="text-align: right; border-top: 2px solid #f3f4f6; padding-top: 1rem;">
            <p style="margin-bottom: 0.5rem; color: #4b5563;">Tạm tính: <span id="modal-order-subtotal" style="color: #111827; font-weight: 500;"></span></p>
            <p style="margin-bottom: 0.5rem; color: #4b5563;">Phí vận chuyển: <span id="modal-order-shipping" style="color: #111827; font-weight: 500;"></span></p>
            <p style="margin-bottom: 0.5rem; color: #4b5563;">Giảm giá: <span id="modal-order-discount" style="color: #10b981; font-weight: 500;"></span></p>
            <h4 style="font-size: 1.25rem; font-weight: 900; color: var(--user-pink-600); margin-top: 1rem;">Tổng cộng: <span id="modal-order-total"></span></h4>
        </div>

        <div class="refund-modal__actions" style="margin-top: 2rem;">
            <button onclick="closeOrderDetailsModal()" class="refund-modal__btn-cancel" style="width: 100%; padding: 0.75rem; border-radius: 0.5rem; font-weight: bold;">Đóng cửa sổ</button>
        </div>
    </div>
</div>

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