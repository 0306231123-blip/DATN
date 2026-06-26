@extends('layouts.user')
@section('title', 'Trang cá nhân')
@section('content')
<section class="bg-[#fcfdf2] py-12 px-8 min-h-screen">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-10">
        
        <div class="col-span-1 flex flex-col items-center space-y-6">
            <div class="w-32 h-32 bg-transparent border-4 border-gray-800 rounded-full flex flex-col items-center justify-end overflow-hidden">
                <div class="w-12 h-12 bg-gray-800 rounded-full mb-1"></div>
                <div class="w-24 h-12 bg-gray-800 rounded-t-full"></div>
            </div>

            <div id="sidebar-name" class="bg-white px-8 py-3 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full">Đang tải...</div>
            
            <button id="btn-info" onclick="switchTab('info')" class="tab-btn border-2 border-gray-800 bg-white hover:bg-gray-50 px-8 py-3 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full transition">
                Quản lý thông tin
            </button>
            
            <button id="btn-history" onclick="switchTab('history')" class="tab-btn border-2 border-transparent bg-white hover:bg-gray-50 px-8 py-3 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full transition">
                Lịch sử mua hàng
            </button>
            
            <button id="btn-orders" onclick="switchTab('orders')" class="tab-btn border-2 border-transparent bg-white hover:bg-gray-50 px-8 py-3 rounded-xl shadow-sm text-center font-bold text-gray-700 w-full transition">
                Quản lý đơn hàng
            </button>
        </div>

        <div class="col-span-2 bg-gray-200 rounded-3xl shadow-inner min-h-[500px] p-8 relative">
            
            <div id="tab-info" class="tab-content block">
                <h2 class="text-2xl font-black text-gray-800 mb-6 border-b-2 border-gray-300 pb-4 uppercase tracking-wider">Cập nhật thông tin</h2>
                
                <form id="update-profile-form" class="bg-white p-8 rounded-2xl shadow-sm space-y-5 text-gray-700">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex flex-col">
                            <label class="font-bold mb-2">Họ và tên</label>
                            <input type="text" id="input-name" class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-800" required>
                        </div>

                        <div class="flex flex-col">
                            <label class="font-bold mb-2">Email</label>
                            <input type="email" id="input-email" class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-800 bg-gray-50 text-gray-500 cursor-not-allowed" readonly title="Email không thể thay đổi">
                        </div>

                        <div class="flex flex-col">
                            <label class="font-bold mb-2">Số điện thoại</label>
                            <input type="text" id="input-phone" class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-800" placeholder="VD: 0912345678">
                        </div>

                        <div class="flex flex-col">
                            <label class="font-bold mb-2 text-pink-600">Loại da của bạn</label>
                            <select id="input-skin-type" class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-pink-500 text-gray-700">
                                <option value="">-- Chưa xác định --</option>
                                <option value="da_dau">Da dầu</option>
                                <option value="da_kho">Da khô</option>
                                <option value="da_hon_hop">Da hỗn hợp</option>
                                <option value="da_nhay_cam">Da nhạy cảm</option>
                                <option value="da_thuong">Da thường</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <label class="font-bold mb-2">Địa chỉ giao hàng</label>
                        <input type="text" id="input-address" class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-800" placeholder="Số nhà, Tên đường, Phường/Xã...">
                    </div>

                    <div class="flex flex-col pt-4 border-t border-gray-100">
                        <label class="font-bold mb-2">Mật khẩu mới <span class="text-xs font-normal text-gray-400">(Bỏ trống nếu không muốn đổi)</span></label>
                        <input type="password" id="input-password" class="px-4 py-2 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-800" placeholder="Nhập mật khẩu mới...">
                    </div>

                    <div class="pt-4 flex justify-end">
                        <button type="submit" id="btn-save-profile" class="bg-gray-800 hover:bg-black text-white font-bold py-3 px-8 rounded-xl shadow transition w-full md:w-auto">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>

            <div id="tab-history" class="tab-content hidden">
                <h2 class="text-2xl font-black text-gray-800 mb-6 border-b-2 border-gray-300 pb-4 uppercase tracking-wider">Lịch sử mua hàng</h2>
                <div id="history-container" class="bg-white p-8 rounded-2xl shadow-sm text-center text-gray-500 font-medium">
                    Chưa có dữ liệu lịch sử mua hàng.
                </div>
            </div>

            <div id="tab-orders" class="tab-content hidden">
                <h2 class="text-2xl font-black text-gray-800 mb-6 border-b-2 border-gray-300 pb-4 uppercase tracking-wider">Quản lý đơn hàng</h2>
                <div id="orders-container" class="bg-white p-8 rounded-2xl...">
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
            content.classList.remove('block');
            content.classList.add('hidden');
        });

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('border-gray-800');
            btn.classList.add('border-transparent');
        });

        document.getElementById('tab-' + tabName).classList.remove('hidden');
        document.getElementById('tab-' + tabName).classList.add('block');
        document.getElementById('btn-' + tabName).classList.remove('border-transparent');
        document.getElementById('btn-' + tabName).classList.add('border-gray-800');
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
                loadMyOrders();
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
            loai_da: document.getElementById('input-skin-type').value
        };

        const newPassword = document.getElementById('input-password').value;
        if (newPassword) {
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

    // 5. TẢI VÀ HIỂN THỊ ĐƠN HÀNG (CÓ IN LÝ DO HỦY ĐƠN)
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
                
                result.data.forEach(order => {
                    const date = new Date(order.ngay_dat).toLocaleDateString('vi-VN');
                    const total = parseInt(order.tong_thanh_toan).toLocaleString() + ' đ';
                    
                    let productsHtml = '<div class="mt-4 pt-4 border-t border-gray-100 space-y-3">';
                    if (order.chi_tiet && order.chi_tiet.length > 0) {
                        order.chi_tiet.forEach(item => {
                            const itemPrice = parseInt(item.don_gia).toLocaleString() + ' đ';
                            productsHtml += `
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span><span class="font-bold text-gray-800">${item.so_luong}x</span> ${item.ten_san_pham}</span>
                                    <span class="font-bold text-gray-800">${itemPrice}</span>
                                </div>
                            `;
                        });
                    }
                    productsHtml += '</div>';

                    // ==========================================
                    // 1. TAB LỊCH SỬ MUA HÀNG (Giao thành công, Đã hủy, Hoàn thành, Đã trả hàng)
                    // ==========================================
                    if(['giao_thanh_cong', 'da_huy', 'hoan_thanh', 'da_tra_hang'].includes(order.trang_thai_don)) {
                        hasHistory = true;
                        let statusColor, statusText, actionBtnHtml = '';

                        if (order.trang_thai_don === 'giao_thanh_cong') {
                            statusColor = 'bg-green-100 text-green-700';
                            statusText = 'Giao thành công (Chờ xác nhận)';
                            actionBtnHtml = `
                                <div class="flex space-x-3 mt-4 w-full">
                                    <button onclick="updateOrderStatus(${order.ma_don_hang}, 'hoan_thanh')" class="w-1/2 bg-gray-800 hover:bg-black text-white font-bold py-2 px-4 rounded-lg transition text-sm shadow">
                                        Đã nhận được hàng
                                    </button>
                                    <button onclick="updateOrderStatus(${order.ma_don_hang}, 'tra_hang_hoan_tien')" class="w-1/2 bg-white border-2 border-gray-200 hover:border-yellow-500 hover:text-yellow-600 text-gray-600 font-bold py-2 px-4 rounded-lg transition text-sm">
                                        Yêu cầu Trả hàng
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-2 text-center w-full">Đơn hàng sẽ tự động hoàn thành sau 7 ngày</p>
                            `;
                        } 
                        else if (order.trang_thai_don === 'da_huy') {
                            statusColor = 'bg-red-100 text-red-700';
                            statusText = 'Đã hủy';
                            // ĐOẠN NÀY HIỂN THỊ LÝ DO HỦY ĐƠN LÊN MÀN HÌNH
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

                        htmlHistory += `
                            <div class="border border-gray-200 p-6 rounded-2xl flex flex-col bg-white">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <p class="font-black text-gray-800 text-lg">Đơn hàng #${order.ma_don_hang}</p>
                                        <p class="text-sm text-gray-500">Ngày đặt: ${date}</p>
                                    </div>
                                    <span class="px-4 py-1 text-sm font-bold rounded-full ${statusColor}">${statusText}</span>
                                </div>
                                ${productsHtml}
                                <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col justify-end items-end space-y-2">
                                    <div class="flex justify-between items-center w-full">
                                        <span class="font-bold text-gray-700">Tổng thanh toán:</span>
                                        <span class="font-black text-pink-600 text-xl">${total}</span>
                                    </div>
                                    <div class="w-full">${actionBtnHtml}</div>
                                </div>
                            </div>`;
                    } 
                    // ==========================================
                    // 2. TAB QUẢN LÝ ĐƠN HÀNG (Chờ xác nhận, Đã xác nhận, Đang giao, Đang xử lý trả hàng)
                    // ==========================================
                    else {
                        hasOrders = true;
                        let activeStatusText = 'Chờ xác nhận';
                        let activeStatusColor = 'bg-yellow-100 text-yellow-700';
                        let actionBtnHtml = '';

                        if (order.trang_thai_don === 'cho_xac_nhan') {
                            actionBtnHtml = `
                                <button onclick="updateOrderStatus(${order.ma_don_hang}, 'da_huy')" class="mt-4 w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                                    Hủy đơn hàng
                                </button>`;
                        } else if (order.trang_thai_don === 'dang_giao') {
                            activeStatusText = 'Đang giao hàng';
                            activeStatusColor = 'bg-blue-100 text-blue-700';
                        } else if (order.trang_thai_don === 'da_xac_nhan') {
                            activeStatusText = 'Đã xác nhận';
                            activeStatusColor = 'bg-purple-100 text-purple-700';
                        } else if (order.trang_thai_don === 'dang_tra_hang' || order.trang_thai_don === 'tra_hang_hoan_tien') {
                            activeStatusText = 'Đang xử lý trả hàng';
                            activeStatusColor = 'bg-orange-100 text-orange-700 border border-orange-300'; 
                            // In ra ghi chú chờ shop duyệt (nếu muốn)
                            actionBtnHtml = `<div class="mt-4 text-sm text-left text-orange-600 bg-orange-50 p-3 rounded-lg w-full">Shop đang xử lý yêu cầu trả hàng của bạn.</div>`;
                        }

                        htmlOrders += `
                            <div class="border border-gray-200 p-6 rounded-2xl flex flex-col bg-white">
                                <div class="flex justify-between items-center mb-4">
                                    <p class="font-black text-gray-800 text-lg">Đơn hàng #${order.ma_don_hang}</p>
                                    <span class="px-4 py-1 text-sm font-bold rounded-full ${activeStatusColor}">${activeStatusText}</span>
                                </div>
                                ${productsHtml}
                                <div class="mt-4 pt-4 border-t border-gray-100 text-right">
                                    <p class="font-black text-pink-600 text-xl">${total}</p>
                                    ${actionBtnHtml}
                                </div>
                            </div>`;
                    }
                });
                
                htmlOrders += '</div>';
                htmlHistory += '</div>';
                
                if (hasOrders) {
                    ordersContainer.innerHTML = htmlOrders;
                    ordersContainer.classList.remove('text-center', 'text-gray-500', 'font-medium', 'p-8');
                }
                if (hasHistory) {
                    historyContainer.innerHTML = htmlHistory;
                    historyContainer.classList.remove('text-center', 'text-gray-500', 'font-medium', 'p-8');
                }
            }
        } catch (error) {
            console.error('Lỗi tải đơn hàng:', error);
        }
    }

    // 6. HÀM CHUNG ĐỂ CẬP NHẬT TRẠNG THÁI (GỬI KÈM LÝ DO HỦY / LÝ DO TRẢ HÀNG)
    async function updateOrderStatus(maDonHang, trangThaiMoi) {
        let lyDoTraHang = null;
        let lyDoHuyDon = null;

        // KIỂM TRA NẾU LÀ HỦY ĐƠN -> BẬT PROMPT NHẬP LÝ DO
        if (trangThaiMoi === 'da_huy') {
            lyDoHuyDon = prompt("Vui lòng nhập lý do hủy đơn hàng (Bắt buộc):");
            if (!lyDoHuyDon || lyDoHuyDon.trim() === "") {
                alert("Bạn phải nhập lý do thì hệ thống mới xử lý hủy đơn!");
                return; // Dừng lại không gọi API nữa
            }
        } 
        else if (trangThaiMoi === 'hoan_thanh') {
            if (!confirm("Xác nhận bạn đã nhận được hàng và sản phẩm không có vấn đề gì? (Sau khi xác nhận sẽ không thể trả hàng nữa)")) return;
        }
        else if (trangThaiMoi === 'tra_hang_hoan_tien') {
            lyDoTraHang = prompt("Vui lòng nhập chi tiết lý do bạn muốn trả hàng (Bắt buộc):");
            if (!lyDoTraHang || lyDoTraHang.trim() === "") {
                alert("Bạn phải nhập lý do thì Shop mới xử lý được nhé!");
                return;
            }
        }

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
                    ly_do_tra_hang: lyDoTraHang,
                    ly_do_huy_don: lyDoHuyDon // Backend sẽ tự lấy cái này lưu vào Database
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
            alert("Có lỗi xảy ra, vui lòng thử lại sau!");
        }
    }
</script>
@endsection