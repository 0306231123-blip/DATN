import re

with open('resources/views/page_user/profileuser.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Add styles
styles = """@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-profile.css') }}">
<style>
/* Shopee Style Overrides */
.profile-grid { max-width: 1200px !important; display: flex !important; gap: 2rem !important; align-items: flex-start; }
.profile-sidebar { width: 220px !important; flex-shrink: 0 !important; background: transparent !important; display: block !important; }
.profile-content { flex-grow: 1 !important; background: #fff !important; box-shadow: 0 1px 2px 0 rgba(0,0,0,.13) !important; border-radius: 2px !important; border: none !important; min-height: 400px; padding: 0 !important; margin-bottom: 2rem; }

/* Sidebar */
.shopee-user-info { display: flex; align-items: center; gap: 15px; padding: 15px 0; margin-bottom: 15px; border-bottom: 1px solid #efefef; }
.shopee-avatar { width: 50px; height: 50px; border-radius: 50%; background: #f5f5f5; border: 1px solid #efefef; display: flex; align-items: center; justify-content: center; color: #ccc; overflow: hidden; }
.shopee-avatar svg { width: 30px; height: 30px; fill: #d1d5db; }
.shopee-username { font-weight: 600; font-size: 14px; color: #333; max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

.profile-tab-btn { background: transparent !important; border: none !important; box-shadow: none !important; text-align: left !important; padding: 8px 0 !important; color: #757575 !important; font-size: 14px !important; font-weight: 500 !important; display: flex !important; align-items: center !important; gap: 12px !important; transition: color 0.2s !important; border-radius: 0 !important; margin-bottom: 8px !important; width: 100% !important; }
.profile-tab-btn:hover { color: #ee4d2d !important; background: transparent !important; }
.profile-tab-btn--active { color: #ee4d2d !important; font-weight: 600 !important; }
.shopee-icon { width: 20px; text-align: center; color: #0055aa; font-size: 18px; }
.shopee-icon.history { color: #ee4d2d; }
.shopee-icon.orders { color: #ee4d2d; }

/* Content Header */
.shopee-header { padding: 18px 30px; border-bottom: 1px solid #efefef; }
.shopee-header__title { font-size: 1.125rem; font-weight: 500; color: #333; margin: 0; text-transform: none; border: none; padding: 0; }
.shopee-header__subtitle { font-size: 14px; color: #555; margin-top: 4px; }

/* Form */
.profile-form { background: transparent !important; box-shadow: none !important; border: none !important; padding: 30px !important; display: block !important; }
.shopee-row { display: flex; align-items: center; margin-bottom: 30px; }
.shopee-label { width: 25%; text-align: right; padding-right: 20px; color: rgba(85,85,85,.8); font-size: 14px; font-weight: 400 !important; margin: 0 !important; }
.shopee-control { width: 75%; display: flex; align-items: center; gap: 10px; }
.shopee-input { width: 100%; max-width: 400px; height: 40px; padding: 0 10px !important; border: 1px solid #ccc !important; border-radius: 2px !important; font-size: 14px; color: #333; box-shadow: none !important; background: #fff; }
.shopee-input:focus { border-color: #555 !important; }
.shopee-input--readonly { border: none !important; padding: 0 !important; background: transparent !important; color: #333 !important; cursor: default !important; }

.shopee-address-wrapper { display: flex; flex-direction: column; width: 100%; max-width: 500px; }
.shopee-address-text { font-size: 14px; color: #333; margin-bottom: 10px; width: 100%; }
.shopee-address-inputs { display: flex; gap: 10px; flex-wrap: wrap; width: 100%; }

.shopee-section-title { font-size: 14px; font-weight: 500; color: #333; margin: 30px 0 20px 25%; }
.shopee-section-subtitle { font-weight: 400; color: #999; font-size: 12px; margin-left: 5px; }

/* Save button */
.profile-form__save-btn { background: #ee4d2d !important; color: #fff !important; height: 40px !important; padding: 0 20px !important; border-radius: 2px !important; font-weight: 400 !important; min-width: 100px; margin-left: 25%; width: auto !important; }
.profile-form__save-btn:hover { background: #f05d40 !important; }
.profile-form__actions { justify-content: flex-start !important; padding-top: 0 !important; display: block !important; margin-top: 40px !important; }

/* Hide old elements to avoid conflict */
.profile-avatar, .profile-name { display: none !important; }
.profile-tab__title { display: none !important; }
</style>
@endsection"""

content = content.replace(
    "@section('styles')\n<link rel=\"stylesheet\" href=\"{{ asset('css/user-profile.css') }}\">\n@endsection",
    styles
)

new_sidebar_html = """        {{-- Sidebar --}}
        <div class="profile-sidebar">
            <div class="shopee-user-info">
                <div class="shopee-avatar">
                    <svg viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                </div>
                <div id="sidebar-name" class="shopee-username">Đang tải...</div>
            </div>
            
            <button id="btn-info" onclick="switchTab('info')" class="tab-btn profile-tab-btn profile-tab-btn--active">
                <span class="shopee-icon">👤</span> Quản lý thông tin
            </button>
            
            <button id="btn-history" onclick="switchTab('history')" class="tab-btn profile-tab-btn">
                <span class="shopee-icon history">🛒</span> Lịch sử mua hàng
            </button>
            
            <button id="btn-orders" onclick="switchTab('orders')" class="tab-btn profile-tab-btn">
                <span class="shopee-icon orders">📦</span> Quản lý đơn hàng
            </button>
        </div>"""

content = re.sub(r'\{\{-- Sidebar --\}\}.*?<\/div>\s*\{\{-- Content Area --\}\}', new_sidebar_html + '\n\n        {{-- Content Area --}}', content, flags=re.DOTALL)

new_tab_info_html = """            {{-- Tab: Thông tin --}}
            <div id="tab-info" class="tab-content profile-tab profile-tab--active">
                <div class="shopee-header">
                    <h2 class="shopee-header__title">Hồ sơ của tôi</h2>
                    <p class="shopee-header__subtitle">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>
                </div>
                
                <form id="update-profile-form" class="profile-form">
                    
                    <div class="shopee-row">
                        <label class="shopee-label">Họ và tên</label>
                        <div class="shopee-control">
                            <input type="text" id="input-name" class="shopee-input" required>
                        </div>
                    </div>

                    <div class="shopee-row">
                        <label class="shopee-label">Email</label>
                        <div class="shopee-control">
                            <input type="email" id="input-email" class="shopee-input shopee-input--readonly" readonly title="Email không thể thay đổi">
                        </div>
                    </div>

                    <div class="shopee-row">
                        <label class="shopee-label">Số điện thoại</label>
                        <div class="shopee-control">
                            <input type="text" id="input-phone" class="shopee-input" placeholder="VD: 0912345678">
                        </div>
                    </div>

                    <div class="shopee-row">
                        <label class="shopee-label">Loại da của bạn</label>
                        <div class="shopee-control">
                            <select id="input-skin-type" class="shopee-input" style="max-width: 200px;">
                                <option value="">-- Chưa xác định --</option>
                                <option value="da_dau">Da dầu</option>
                                <option value="da_kho">Da khô</option>
                                <option value="da_hon_hop">Da hỗn hợp</option>
                                <option value="da_nhay_cam">Da nhạy cảm</option>
                                <option value="da_thuong">Da thường</option>
                            </select>
                        </div>
                    </div>

                    <div class="shopee-row">
                        <label class="shopee-label">Địa chỉ giao hàng</label>
                        <div class="shopee-control">
                            <div class="shopee-address-wrapper">
                                <div class="shopee-address-text">Địa chỉ hiện tại: <span id="current-address-display" style="font-weight: 700;">Chưa có</span></div>
                                <input type="hidden" id="input-address">
                                <div class="shopee-address-inputs">
                                    <select id="province" class="shopee-input" style="max-width: 245px;">
                                        <option value="" selected>-- Chọn Tỉnh/TP mới --</option>
                                    </select>
                                    <select id="ward" class="shopee-input cursor-pointer" style="max-width: 245px;">
                                        <option value="" selected>-- Chọn Phường/Xã --</option>
                                    </select>
                                    <input type="text" id="street" class="shopee-input" style="max-width: 500px;" placeholder="Nhập số nhà, tên đường mới...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="shopee-section-title">
                        Thông tin nhận tiền hoàn
                        <span class="shopee-section-subtitle">Cập nhật sẵn tài khoản để quá trình hoàn tiền diễn ra nhanh chóng.</span>
                    </div>

                    <div class="shopee-row">
                        <label class="shopee-label">Ngân hàng</label>
                        <div class="shopee-control">
                            <input type="text" id="input-bank-name" list="bank-list" class="shopee-input" placeholder="VD: Vietcombank, MB Bank...">
                        </div>
                    </div>

                    <div class="shopee-row">
                        <label class="shopee-label">Số tài khoản</label>
                        <div class="shopee-control">
                            <input type="text" id="input-bank-account" class="shopee-input" placeholder="Nhập số tài khoản">
                        </div>
                    </div>

                    <div class="shopee-row">
                        <label class="shopee-label">Tên chủ tài khoản</label>
                        <div class="shopee-control">
                            <input type="text" id="input-bank-owner" class="shopee-input" style="text-transform: uppercase;" placeholder="VD: NGUYEN VAN A">
                        </div>
                    </div>

                    <div class="shopee-section-title">
                        Thay đổi mật khẩu
                        <span class="shopee-section-subtitle">Để trống nếu không muốn đổi.</span>
                    </div>

                    <div class="shopee-row">
                        <label class="shopee-label">Mật khẩu cũ</label>
                        <div class="shopee-control">
                            <input type="password" id="input-old-password" class="shopee-input" placeholder="Nhập mật khẩu cũ...">
                        </div>
                    </div>

                    <div class="shopee-row">
                        <label class="shopee-label">Mật khẩu mới</label>
                        <div class="shopee-control">
                            <input type="password" id="input-password" class="shopee-input" placeholder="Nhập mật khẩu mới...">
                        </div>
                    </div>

                    <div class="profile-form__actions">
                        <button type="submit" id="btn-save-profile" class="profile-form__save-btn">
                            Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>"""

content = re.sub(r'\{\{-- Tab: Thông tin --\}\}.*?\{\{-- Tab: Lịch sử --\}\}', new_tab_info_html + '\n\n            {{-- Tab: Lịch sử --}}', content, flags=re.DOTALL)

with open('resources/views/page_user/profileuser.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated profileuser.blade.php successfully")
