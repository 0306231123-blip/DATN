const fs = require('fs');
let content = fs.readFileSync('d:/DATN/laravel-frontend/resources/views/page_user/profileuser.blade.php', 'utf8');

// 1. Replace the inner logic of `forEach`
const jsOld = `                    // TAB LỊCH SỬ`;
const idxStart = content.indexOf(jsOld);
const idxEnd = content.indexOf(`                });\n                \n                htmlOrders += '</div>';`);

if(idxStart > -1 && idxEnd > -1) {
    const newJS = `                    hasOrders = true;
                    let statusColor = '', statusText = '', actionBtnHtml = '';

                    if (order.trang_thai_don === 'da_huy') {
                        statusColor = 'bg-red-100 text-red-700';
                        statusText = 'Đã hủy';
                        if (order.ly_do_huy_don) {
                            actionBtnHtml = \`<div class="mt-4 text-sm text-left text-red-600 bg-red-50 p-3 rounded-lg w-full border border-red-100"><b>Lý do hủy:</b> \${order.ly_do_huy_don}</div>\`;
                        }
                    } 
                    else if (order.trang_thai_don === 'hoan_thanh') {
                        statusColor = 'bg-blue-100 text-blue-700 border border-blue-200';
                        statusText = 'Hoàn thành';
                        actionBtnHtml = \`<div class="mt-4 text-center text-green-600 font-bold w-full bg-green-50 py-2 rounded-lg">Cảm ơn bạn đã mua sắm!</div>\`;
                    }
                    else if (order.trang_thai_don === 'da_tra_hang') {
                        statusColor = 'bg-gray-100 text-gray-700 border border-gray-300';
                        statusText = 'Đã hoàn tiền / Trả hàng';
                        actionBtnHtml = \`<div class="mt-4 text-center text-gray-600 font-bold w-full bg-gray-50 py-2 rounded-lg">Đơn hàng đã được trả thành công</div>\`;
                    }
                    else if (order.trang_thai_don === 'tu_choi_tra_hang') {
                        statusColor = 'bg-red-100 text-red-700 border border-red-300';
                        statusText = 'Bị từ chối trả hàng';
                        actionBtnHtml = \`<div class="mt-4 text-sm text-left text-red-600 bg-red-50 p-3 rounded-lg w-full border border-red-200">Yêu cầu trả hàng của bạn bị từ chối vì sai quy định hoàn trả.</div>\`;
                    }
                    else if (order.trang_thai_don === 'khong_du_dieu_kien') {
                        statusColor = 'bg-gray-200 text-gray-500 border border-gray-300';
                        statusText = 'Không đủ điều kiện';
                        actionBtnHtml = \`<div class="mt-4 text-center text-gray-500 font-bold w-full bg-gray-100 py-2 rounded-lg">Đơn hàng không đủ điều kiện xử lý.</div>\`;
                    }
                    else if (order.trang_thai_don === 'giao_thanh_cong') {
                        const orderDateObj = new Date(order.ngay_cap_nhat || order.ngay_dat);
                        const diffDays = Math.floor((new Date() - orderDateObj) / (1000 * 60 * 60 * 24));

                        statusColor = 'bg-green-100 text-green-700';
                        statusText = 'Giao thành công (Chờ xác nhận)';
                        
                        if (diffDays <= 3) {
                            actionBtnHtml = \`
                                <div class="flex space-x-3 mt-4 w-full">
                                    <button onclick="updateOrderStatus('\${order.ma_don_hang}', 'hoan_thanh')" class="w-1/2 bg-gray-800 hover:bg-black text-white font-bold py-2 px-4 rounded-lg transition text-sm shadow">
                                        Đã nhận được hàng
                                    </button>
                                    <button onclick="updateOrderStatus('\${order.ma_don_hang}', 'tra_hang_hoan_tien')" class="w-1/2 bg-white border-2 border-gray-200 hover:border-yellow-500 hover:text-yellow-600 text-gray-600 font-bold py-2 px-4 rounded-lg transition text-sm">
                                        Yêu cầu Trả hàng
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-2 text-center w-full">Đơn hàng sẽ tự động hoàn thành sau 3 ngày (còn lại \${3 - diffDays} ngày)</p>
                            \`;
                        } else {
                            actionBtnHtml = \`<div class="mt-4 text-center text-gray-500 font-medium w-full bg-gray-50 py-2 rounded-lg border border-gray-200">Đã hết hạn 3 ngày đổi trả. Hệ thống đang tự động hoàn thành.</div>\`;
                        }
                    }
                    else if (order.trang_thai_don === 'cho_xac_nhan' || order.trang_thai_don === 'da_xac_nhan') {
                        const orderDateObj = new Date(order.ngay_dat);
                        const diffMinutes = Math.floor((new Date() - orderDateObj) / (1000 * 60));
                        
                        if (order.trang_thai_don === 'da_xac_nhan') {
                            statusText = 'Đã xác nhận';
                            statusColor = 'bg-purple-100 text-purple-700';
                        } else {
                            statusText = 'Chờ xác nhận';
                            statusColor = 'bg-yellow-100 text-yellow-700';
                        }
                        
                        if (diffMinutes <= 30) {
                            actionBtnHtml = \`
                                <button onclick="updateOrderStatus('\${order.ma_don_hang}', 'da_huy', '\${order.phuong_thuc_thanh_toan}')" class="mt-4 w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition text-sm shadow">
                                    Hủy đơn hàng
                                </button>
                                <p class="text-xs text-center text-gray-400 mt-2">Bạn có thể hủy đơn trong vòng 30 phút (còn lại \${30 - diffMinutes} phút)</p>\`;
                        } else {
                            if (order.trang_thai_don === 'cho_xac_nhan') {
                                actionBtnHtml = \`<div class="mt-4 text-sm text-center text-gray-500 bg-gray-50 border border-gray-200 py-2 rounded-lg w-full">Đã quá 30 phút kể từ lúc đặt hàng, không thể tự hủy đơn.</div>\`;
                            }
                        }
                    } else if (order.trang_thai_don === 'dang_giao') {
                        statusText = 'Đang giao hàng';
                        statusColor = 'bg-blue-100 text-blue-700';
                    } else if (order.trang_thai_don === 'dang_tra_hang' || order.trang_thai_don === 'tra_hang_hoan_tien') {
                        statusText = 'Đang xử lý đổi/trả';
                        statusColor = 'bg-orange-100 text-orange-700 border border-orange-300'; 
                        actionBtnHtml = \`<div class="mt-4 text-sm text-left text-orange-600 bg-orange-50 p-3 rounded-lg w-full">Shop đang xử lý yêu cầu đổi/trả của bạn.</div>\`;
                    }

                    let filterDataStatus = order.trang_thai_don;
                    if (filterDataStatus === 'tra_hang_hoan_tien') filterDataStatus = 'dang_tra_hang';

                    htmlOrders += \`
                        <div class="profile-order-card" id="order-\${order.ma_don_hang}" data-status="\${filterDataStatus}" data-date="\${dateISO}" data-products="\${productNames}" onclick="showOrderDetails('\${order.ma_don_hang}', event)" style="cursor: pointer;">
                            <div class="profile-order-card__header">
                                <div>
                                    <p class="profile-order-card__id">Đơn hàng \${order.ma_don_hang}</p>
                                    <p class="profile-order-card__date">Ngày đặt: \${date}</p>
                                    <p class="profile-order-card__date mt-1 text-pink-600">Thanh toán: <span class="font-medium">\${paymentMethodText}</span></p>
                                </div>
                                <span class="profile-order-card__status \${statusColor}">\${statusText}</span>
                            </div>
                            \${productsHtml}
                            <div class="profile-order-card__footer">
                                \${voucherHtml}
                                <div class="profile-order-card__total-row">
                                    <span class="profile-order-card__total-label">Tổng thanh toán:</span>
                                    <span class="profile-order-card__total-value">\${tongThanhToan.toLocaleString()} VNĐ</span>
                                </div>
                                <div class="w-full">\${actionBtnHtml}</div>
                            </div>
                        </div>\`;
`;
    content = content.substring(0, idxStart) + newJS + content.substring(idxEnd);
}

// 2. Remove history container logic
const logicRemove1 = `                htmlHistory += '</div>';`;
content = content.replace(logicRemove1, '');

const logicRemove2 = `                if (hasHistory) {
                    document.getElementById('history-filters').style.display = 'flex';
                    document.getElementById('history-search-bar').style.display = 'flex';
                    historyContainer.innerHTML = htmlHistory;
                    historyContainer.classList.remove('profile-orders-empty');
                } else {
                    historyContainer.innerHTML = '<p style="text-align:center;color:#999;padding:50px 0;">Bạn chưa có lịch sử mua hàng nào.</p>';
                    historyContainer.classList.add('profile-orders-empty');
                }`;
content = content.replace(logicRemove2, '');

const logicRemove3 = `ordersContainer.innerHTML = '<p style="text-align:center;color:#999;padding:50px 0;">Bạn chưa có đơn hàng nào đang xử lý.</p>';
                historyContainer.innerHTML = '<p style="text-align:center;color:#999;padding:50px 0;">Bạn chưa có lịch sử mua hàng nào.</p>';`;
const logicAdd3 = `ordersContainer.innerHTML = '<p style="text-align:center;color:#999;padding:50px 0;">Bạn chưa có đơn hàng nào đang xử lý.</p>';`;
content = content.replace(logicRemove3, logicAdd3);

content = content.replace(`let htmlHistory = '<div class="space-y-4 text-left">';\n`, '');
content = content.replace(`let hasHistory = false;\n`, '');

fs.writeFileSync('d:/DATN/laravel-frontend/resources/views/page_user/profileuser.blade.php', content, 'utf8');
console.log('done');
