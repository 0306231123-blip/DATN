@extends('layouts.admin')

@section('page-title', 'Cảnh báo hệ thống')
@section('page-subtitle', 'Theo dõi các hoạt động bất thường hoặc nghi ngờ spam')

@section('content')
<div class="content-container" style="max-width: 1000px; margin: 0 auto; background: var(--surface-color); border-radius: 12px; padding: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="font-size: 1.2rem; color: var(--text-color); font-weight: 600;">Danh sách cảnh báo</h2>
        <button class="btn btn-primary" onclick="loadAlerts()" style="display: flex; align-items: center; gap: 8px;">
            <i data-lucide="refresh-cw" class="icon-sm"></i> Làm mới
        </button>
    </div>

    <div class="table-container">
        <table class="table" style="width: 100%; text-align: left; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid var(--border-color); color: var(--text-muted);">
                    <th style="padding: 12px 16px;">Thời gian</th>
                    <th style="padding: 12px 16px;">Loại cảnh báo</th>
                    <th style="padding: 12px 16px;">Nội dung</th>
                    <th style="padding: 12px 16px; width: 120px;">Hành động</th>
                </tr>
            </thead>
            <tbody id="alerts-tbody">
                <tr>
                    <td colspan="4" style="text-align: center; padding: 30px; color: var(--text-muted);">
                        Đang tải dữ liệu...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const API_ALERTS_URL = typeof API_BASE_URL !== 'undefined' ? `${API_BASE_URL}/alerts/system` : 'http://localhost:3000/api/alerts/system';
    
    async function loadAlerts() {
        try {
            const token = localStorage.getItem('admin_token');
            if(!token) return;

            const res = await fetch(API_ALERTS_URL, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const json = await res.json();
            
            const tbody = document.getElementById('alerts-tbody');
            if (json.status === 'success') {
                if (json.data.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 30px; color: var(--text-muted);">Chưa có cảnh báo hệ thống nào.</td></tr>`;
                    return;
                }

                tbody.innerHTML = json.data.map(alert => {
                    const date = new Date(alert.ngay_tao).toLocaleString('vi-VN');
                    const isUnread = !alert.da_doc;
                    const rowStyle = isUnread ? 'background-color: #fef2f2; font-weight: 500;' : '';
                    
                    let typeBadge = '';
                    if (alert.loai_canh_bao === 'MUA_SO_LUONG_LON') {
                        typeBadge = '<span style="background: #fee2e2; color: #ef4444; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">Mua SL lớn</span>';
                    } else if (alert.loai_canh_bao === 'TAN_SUAT_MUA_CAO') {
                        typeBadge = '<span style="background: #ffedd5; color: #f97316; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">Tần suất cao</span>';
                    } else {
                        typeBadge = `<span style="background: #f1f5f9; color: #64748b; padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">${alert.loai_canh_bao}</span>`;
                    }

                    return `
                        <tr style="border-bottom: 1px solid var(--border-color); ${rowStyle}">
                            <td style="padding: 12px 16px;">
                                ${isUnread ? '<span style="display: inline-block; width: 8px; height: 8px; background: #ef4444; border-radius: 50%; margin-right: 6px;"></span>' : ''}
                                ${date}
                            </td>
                            <td style="padding: 12px 16px;">${typeBadge}</td>
                            <td style="padding: 12px 16px; max-width: 400px; line-height: 1.5;">${alert.noi_dung}</td>
                            <td style="padding: 12px 16px;">
                                ${isUnread ? `
                                    <button class="btn btn-outline" onclick="markAsRead(${alert.ma_canh_bao})" style="padding: 4px 8px; font-size: 0.85rem;">
                                        Đã xử lý
                                    </button>
                                ` : '<span style="color: var(--text-muted); font-size: 0.85rem;">Đã đọc</span>'}
                            </td>
                        </tr>
                    `;
                }).join('');
                
                lucide.createIcons();
            } else {
                tbody.innerHTML = `<tr><td colspan="4" style="text-align: center; padding: 30px; color: #ef4444;">Lỗi: ${json.message}</td></tr>`;
            }
        } catch(e) {
            console.error(e);
        }
    }

    async function markAsRead(id) {
        try {
            const token = localStorage.getItem('admin_token');
            const res = await fetch(`${API_ALERTS_URL}/${id}/read`, {
                method: 'PUT',
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const json = await res.json();
            if (json.status === 'success') {
                loadAlerts();
                if(typeof window.fetchAlerts === 'function') {
                    window.fetchAlerts(); // Cập nhật lại số chấm đỏ ở sidebar
                }
            } else {
                alert('Có lỗi xảy ra: ' + json.message);
            }
        } catch(e) {
            console.error(e);
            alert('Không thể kết nối đến máy chủ.');
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        loadAlerts();
    });
</script>
@endsection
