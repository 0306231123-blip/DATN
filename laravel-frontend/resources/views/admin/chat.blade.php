@extends('layouts.admin')

@section('title', 'Hỗ trợ Khách hàng')
@section('page-title', 'Hỗ trợ Khách hàng')
@section('page-subtitle', 'Quản lý tin nhắn trực tuyến từ khách hàng')

@section('content')
<style>
    .chat-container {
        display: flex;
        height: calc(100vh - 180px);
        background: #fff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow: hidden;
    }
    
    /* Left sidebar (User list) */
    .chat-sidebar {
        width: 300px;
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        background: #fdfdfd;
    }
    .chat-sidebar-header {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        background: var(--surface-color);
    }
    .chat-sidebar-header h3 {
        font-weight: 600;
        font-size: 1rem;
        color: var(--text-color);
        margin: 0;
    }
    .chat-user-list {
        flex: 1;
        overflow-y: auto;
    }
    .chat-user-item {
        padding: 1rem;
        border-bottom: 1px solid var(--border-color);
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .chat-user-item:hover {
        background: #f8fafc;
    }
    .chat-user-item.active {
        background: #8b5cf6;
        color: white;
        border-color: #8b5cf6;
    }
    .chat-user-item.active .chat-user-name,
    .chat-user-item.active .chat-user-email {
        color: white;
    }
    .chat-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: var(--text-muted);
        flex-shrink: 0;
    }
    .chat-user-item.active .chat-avatar {
        background: rgba(255,255,255,0.2);
        color: white;
    }
    .chat-user-info {
        overflow: hidden;
    }
    .chat-user-name {
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--text-color);
    }
    .chat-user-email {
        font-size: 0.75rem;
        color: var(--text-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Right side (Chat area) */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: var(--bg-color);
    }
    .chat-main-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--border-color);
        background: #fff;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .chat-main-header h3 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-color);
    }
    .chat-messages {
        flex: 1;
        padding: 1.5rem;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    .chat-message {
        max-width: 70%;
        display: flex;
        flex-direction: column;
    }
    .chat-message.customer {
        align-self: flex-start;
    }
    .chat-message.admin {
        align-self: flex-end;
    }
    .message-bubble {
        padding: 12px 16px;
        border-radius: var(--radius-md);
        font-size: 0.95rem;
        line-height: 1.4;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }
    .chat-message.customer .message-bubble {
        background: #fff;
        border: 1px solid var(--border-color);
        color: var(--text-color);
        border-top-left-radius: 4px;
    }
    .chat-message.admin .message-bubble {
        background: #8b5cf6;
        color: white;
        border-top-right-radius: 4px;
    }
    .message-time {
        font-size: 0.7rem;
        color: var(--text-muted);
        margin-top: 4px;
    }
    .chat-message.admin .message-time {
        text-align: right;
    }

    /* Input area */
    .chat-input-area {
        padding: 1rem 1.5rem;
        background: #fff;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .chat-input {
        flex: 1;
        border: 1px solid var(--border-color);
        border-radius: 20px;
        padding: 10px 20px;
        font-size: 0.95rem;
        outline: none;
        transition: border-color 0.2s;
    }
    .chat-input:focus {
        border-color: var(--primary-color);
    }
    .btn-send {
        background: #8b5cf6;
        color: white;
        border: none;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .btn-send:hover {
        background: #7c3aed;
    }
    .unread-badge {
        background: red;
        color: white;
        font-size: 0.7rem;
        padding: 2px 6px;
        border-radius: 10px;
        margin-left: auto;
    }
    
    .empty-chat {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        color: var(--text-muted);
    }
    .empty-chat i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>

<div class="chat-container">
    <!-- Cột danh sách user -->
    <div class="chat-sidebar">
        <div class="chat-sidebar-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">Danh sách trò chuyện</h3>
            <button class="btn btn-primary" onclick="openNewChatModal()" style="padding: 4px 8px; font-size: 12px; display: flex; align-items: center; gap: 4px;" title="Khởi tạo tin nhắn mới">
                <i data-lucide="plus" style="width: 14px; height: 14px;"></i> Tìm
            </button>
        </div>
        <div class="chat-user-list" id="user-list">
            <div style="padding: 20px; text-align: center; color: var(--text-muted);">Đang tải...</div>
        </div>
    </div>

    <!-- Cột chat -->
    <div class="chat-main" id="chat-main" style="display: none;">
        <div class="chat-main-header">
            <div class="chat-avatar" id="current-user-avatar">?</div>
            <div>
                <h3 id="current-user-name">Tên khách hàng</h3>
                <span id="current-user-email" style="font-size: 0.8rem; color: var(--text-muted);">email@example.com</span>
            </div>
        </div>
        <div class="chat-messages" id="chat-messages">
            <!-- Tin nhắn render ở đây -->
        </div>
        <!-- Image Preview Area -->
        <div id="chat-image-preview-container" style="display: none; padding: 10px 24px; background: #f8fafc; border-top: 1px solid var(--border-color); align-items: flex-start; gap: 10px; position: relative;">
            <img id="chat-image-preview" src="" style="height: 64px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e2e8f0;">
            <button id="chat-remove-image-btn" style="position: absolute; top: 5px; left: 80px; width: 20px; height: 20px; background: #94a3b8; border: none; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; cursor: pointer; font-weight: bold;">&times;</button>
        </div>

        <div class="chat-input-area">
            <input type="file" id="chat-image-input" style="display: none;" accept="image/*">
            <button id="chat-attach-btn" style="background: none; border: none; color: #94a3b8; cursor: pointer; padding: 8px; margin-right: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            </button>
            <input type="text" id="chat-input" class="chat-input" placeholder="Nhập tin nhắn hỗ trợ...">
            <button class="btn-send" id="btn-send" title="Gửi tin nhắn">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-left: -2px;"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
            </button>
        </div>
    </div>
    
    <!-- Placeholder khi chưa chọn chat -->
    <div class="chat-main empty-chat" id="chat-placeholder">
        <i data-lucide="message-square"></i>
        <p>Chọn một khách hàng để bắt đầu hỗ trợ</p>
    </div>
</div>

<!-- Modal Bắt đầu chat mới -->
<div class="modal" id="modal-new-chat" style="display: none; z-index: 1002;">
    <div class="modal-content" style="width: 450px;">
        <div class="modal-header">
            <h2>Tìm khách hàng</h2>
            <button class="modal-close" onclick="closeNewChatModal()">&times;</button>
        </div>
        <div style="padding: 20px;">
            <input type="text" id="new-chat-search" class="form-control" placeholder="Tìm tên hoặc email..." style="width: 100%; margin-bottom: 15px;" oninput="searchNewChatUsers()">
            <div id="new-chat-user-list" style="max-height: 350px; overflow-y: auto;">
                <!-- Danh sách user render ở đây -->
                <div style="text-align: center; color: var(--text-muted); padding: 20px;">Gõ để tìm kiếm khách hàng...</div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function escapeHtml(unsafe) {
        if (!unsafe) return '';
        return String(unsafe)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    const API_URL = 'http://localhost:3000/api';
    let currentUserId = null;
    let users = [];
    let pollingInterval = null;
    let lastMessageCount = 0;

    const userListEl = document.getElementById('user-list');
    const chatMainEl = document.getElementById('chat-main');
    const chatPlaceholderEl = document.getElementById('chat-placeholder');
    const chatMessagesEl = document.getElementById('chat-messages');
    const chatInputEl = document.getElementById('chat-input');
    const btnSend = document.getElementById('btn-send');
    
    const chatAttachBtn = document.getElementById('chat-attach-btn');
    const chatImageInput = document.getElementById('chat-image-input');
    const chatImagePreviewContainer = document.getElementById('chat-image-preview-container');
    const chatImagePreview = document.getElementById('chat-image-preview');
    const chatRemoveImageBtn = document.getElementById('chat-remove-image-btn');
    let selectedChatImage = null;

    if (chatAttachBtn) chatAttachBtn.addEventListener('click', () => chatImageInput.click());
    
    if (chatImageInput) {
        chatImageInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                selectedChatImage = file;
                chatImagePreview.src = URL.createObjectURL(file);
                chatImagePreviewContainer.style.display = 'flex';
            }
        });
    }

    if (chatRemoveImageBtn) {
        chatRemoveImageBtn.addEventListener('click', () => {
            selectedChatImage = null;
            chatImageInput.value = '';
            chatImagePreviewContainer.style.display = 'none';
        });
    }

    // Utils
    function formatTime(dateStr) {
        const d = new Date(dateStr);
        return d.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) + ' ' + d.toLocaleDateString('vi-VN');
    }
    
    function getInitials(name) {
        if (!name) return '?';
        return name.charAt(0).toUpperCase();
    }

    // 1. Tải danh sách user
    async function loadUserList() {
        const token = localStorage.getItem('admin_token');
        if (!token) return;

        try {
            const res = await fetch(`${API_URL}/chat/admin/users`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            
            if (data.success) {
                users = data.data;
                renderUserList();
            }
        } catch (error) {
            console.error('Lỗi tải danh sách user:', error);
        }
    }

    function renderUserList() {
        if (users.length === 0) {
            userListEl.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-muted);">Chưa có cuộc trò chuyện nào</div>';
            return;
        }

        userListEl.innerHTML = users.map(user => {
            const lastRead = localStorage.getItem('chat_admin_read_' + user.ma_nguoi_dung) || 0;
            const msgTime = new Date(user.latest_message_date || Date.now()).getTime();
            const isUnread = (msgTime > lastRead) && (currentUserId != user.ma_nguoi_dung);
            
            return `
            <div class="chat-user-item ${currentUserId == user.ma_nguoi_dung ? 'active' : ''}" onclick="selectUser(${user.ma_nguoi_dung}, '${escapeHtml(user.ho_ten)}', '${escapeHtml(user.email)}')">
                <div class="chat-avatar">${getInitials(user.ho_ten)}</div>
                <div class="chat-user-info">
                    <div class="chat-user-name">${escapeHtml(user.ho_ten)}</div>
                    <div class="chat-user-email">${escapeHtml(user.email)}</div>
                </div>
                ${isUnread ? '<div class="unread-badge">Mới</div>' : ''}
            </div>
            `;
        }).join('');
    }

    // ========== TÌM KIẾM VÀ TẠO CHAT MỚI ==========
    function openNewChatModal() {
        document.getElementById('modal-new-chat').style.display = 'block';
        if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'block';
        
        document.getElementById('new-chat-search').value = '';
        document.getElementById('new-chat-user-list').innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 20px;">Gõ để tìm kiếm khách hàng...</div>';
        setTimeout(() => document.getElementById('new-chat-search').focus(), 100);
    }

    function closeNewChatModal() {
        document.getElementById('modal-new-chat').style.display = 'none';
        if(document.getElementById('modal-overlay')) document.getElementById('modal-overlay').style.display = 'none';
    }

    let searchChatTimer = null;
    async function searchNewChatUsers() {
        const query = document.getElementById('new-chat-search').value;
        if (!query.trim()) {
            document.getElementById('new-chat-user-list').innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 20px;">Gõ để tìm kiếm khách hàng...</div>';
            return;
        }

        if (searchChatTimer) clearTimeout(searchChatTimer);
        searchChatTimer = setTimeout(async () => {
            document.getElementById('new-chat-user-list').innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 20px;">Đang tìm...</div>';
            try {
                const token = localStorage.getItem('admin_token');
                const res = await fetch(`${API_URL}/users?search=${encodeURIComponent(query)}&per_page=10`, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const result = await res.json();
                
                if (result.success && result.data && result.data.length > 0) {
                    const html = result.data.map(u => `
                        <div class="chat-user-item" onclick="startNewChat(${u.ma_nguoi_dung}, '${escapeHtml(u.ho_ten)}', '${escapeHtml(u.email)}')">
                            <div class="chat-avatar">${getInitials(u.ho_ten)}</div>
                            <div class="chat-user-info">
                                <div class="chat-user-name">${escapeHtml(u.ho_ten)}</div>
                                <div class="chat-user-email">${escapeHtml(u.email)}</div>
                            </div>
                        </div>
                    `).join('');
                    document.getElementById('new-chat-user-list').innerHTML = html;
                } else {
                    document.getElementById('new-chat-user-list').innerHTML = '<div style="text-align: center; color: var(--text-muted); padding: 20px;">Không tìm thấy khách hàng</div>';
                }
            } catch (e) {
                document.getElementById('new-chat-user-list').innerHTML = '<div style="text-align: center; color: red; padding: 20px;">Lỗi kết nối</div>';
            }
        }, 500);
    }

    function startNewChat(userId, name, email) {
        closeNewChatModal();
        let existingUser = users.find(u => u.ma_nguoi_dung == userId);
        if (existingUser) {
            existingUser.latest_message_date = new Date().toISOString();
        } else {
            users.unshift({
                ma_nguoi_dung: userId,
                ho_ten: name,
                email: email,
                latest_message_date: new Date().toISOString()
            });
        }
        renderUserList();
        selectUser(userId, name, email);
    }
    // ==============================================

    // 2. Chọn user và tải tin nhắn
    window.selectUser = function(userId, name, email) {
        currentUserId = userId;
        lastMessageCount = 0; // reset
        
        // Đánh dấu đã đọc
        localStorage.setItem('chat_admin_read_' + userId, Date.now());
        
        // Update UI
        renderUserList();
        document.getElementById('current-user-name').innerText = name;
        document.getElementById('current-user-email').innerText = email;
        document.getElementById('current-user-avatar').innerText = getInitials(name);
        
        chatPlaceholderEl.style.display = 'none';
        chatMainEl.style.display = 'flex';
        
        chatMessagesEl.innerHTML = '<div style="text-align:center; padding:20px; color:gray;">Đang tải...</div>';
        
        loadMessages();
        
        // Bật polling cho user này
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(loadMessages, 3000);
        
        // Refresh lại danh sách user (để phòng có tin nhắn mới đẩy lên đầu)
        setInterval(loadUserList, 10000);
    }

    async function loadMessages() {
        if (!currentUserId) return;
        const token = localStorage.getItem('admin_token');
        
        try {
            const res = await fetch(`${API_URL}/chat/admin/${currentUserId}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const data = await res.json();
            
            if (data.success) {
                const msgs = data.data;
                if (msgs.length !== lastMessageCount) {
                    lastMessageCount = msgs.length;
                    
                    if (msgs.length === 0) {
                        chatMessagesEl.innerHTML = '<div style="text-align:center; color:gray; font-size: 0.9rem;">Chưa có tin nhắn</div>';
                        return;
                    }

                    chatMessagesEl.innerHTML = msgs.map(msg => {
                        const type = msg.is_from_admin ? 'admin' : 'customer';
                        const imgHtml = msg.hinh_anh ? `<img src="${msg.hinh_anh}" style="max-width: 100%; border-radius: 4px; margin-bottom: 5px; max-height: 200px; object-fit: contain; border: 1px solid #e2e8f0;">` : '';
                        const textHtml = msg.noi_dung ? msg.noi_dung : '';
                        
                        return `
                            <div class="chat-message ${type}">
                                <div class="message-bubble" style="display: flex; flex-direction: column;">
                                    ${imgHtml}
                                    ${textHtml}
                                </div>
                                <div class="message-time">${formatTime(msg.ngay_gui)}</div>
                            </div>
                        `;
                    }).join('');
                    
                    chatMessagesEl.scrollTop = chatMessagesEl.scrollHeight;
                    
                    // Cập nhật last read
                    localStorage.setItem('chat_admin_read_' + currentUserId, Date.now());
                }
            }
        } catch (error) {
            console.error('Lỗi tải tin nhắn chi tiết:', error);
        }
    }

    // 3. Gửi tin nhắn
    async function sendMessage() {
        if (!currentUserId) return;
        const text = chatInputEl.value.trim();
        if (!text && !selectedChatImage) return;

        const token = localStorage.getItem('admin_token');
        chatInputEl.value = '';
        chatImagePreviewContainer.style.display = 'none';

        let hinh_anh = null;

        if (selectedChatImage) {
            const formData = new FormData();
            formData.append('image', selectedChatImage);
            try {
                const uploadRes = await fetch(`${API_URL}/chat/upload`, {
                    method: 'POST',
                    headers: { 'Authorization': `Bearer ${token}` },
                    body: formData
                });
                const uploadData = await uploadRes.json();
                if (uploadData.success) {
                    hinh_anh = 'http://localhost:8000' + uploadData.url; 
                } else {
                    alert('Lỗi upload ảnh: ' + uploadData.message);
                    return;
                }
            } catch (e) {
                console.error('Lỗi upload:', e);
                return;
            }
        }

        selectedChatImage = null;
        chatImageInput.value = '';

        try {
            const res = await fetch(`${API_URL}/chat/admin/${currentUserId}`, {
                method: 'POST',
                headers: { 
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ noi_dung: text, hinh_anh })
            });
            const data = await res.json();
            if (data.success) {
                loadMessages();
            } else {
                alert('Lỗi: ' + data.message);
            }
        } catch (error) {
            console.error('Lỗi gửi tin nhắn:', error);
        }
    }

    // Events
    btnSend.addEventListener('click', sendMessage);
    chatInputEl.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    // Init
    document.addEventListener('DOMContentLoaded', () => {
        loadUserList();
    });
</script>
@endsection
