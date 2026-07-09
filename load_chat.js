function loadChatMessages() {
                const token = localStorage.getItem('token');
                if (!token) {
                    chatMessages.innerHTML = '<div class="text-center text-sm text-gray-500 my-4">Vui l├▓ng <a href="/login" class="text-pink-600 underline">─æ─âng nhß║¡p</a> ─æß╗â chat vß╗¢i CSKH.</div>';
                    return;
                }

                try {
                    const res = await fetch('http://localhost:3000/api/chat', {
                        headers: { 'Authorization': `Bearer ${token}` }
                    });
                    const result = await res.json();
                    
                    if (result.success) {
                        const messages = result.data;
                        if (messages.length !== lastMessageCount) {
                            // Nß║┐u ─æang ─æ├│ng chat m├á c├│ tin nhß║»n mß╗¢i th├¼ hiß╗çn chß║Ñm ─æß╗Å
                            if (chatBox.classList.contains('hidden') && lastMessageCount > 0) {
                                document.getElementById('chat-unread-badge')?.classList.remove('hidden');
                            }
                            
                            lastMessageCount = messages.length;
                            chatMessages.innerHTML = '';
                            
                            if (messages.length === 0) {
                                chatMessages.innerHTML = '<div class="text-center text-xs text-gray-400 my-2">H├úy gß╗¡i lß╗¥i ch├áo ─æß║┐n nh├ón vi├¬n hß╗ù trß╗ú!</div>';
                            }

                            messages.forEach(msg => {
                                const isAdmin = msg.is_from_admin;
                                const alignClass = isAdmin ? 'items-start' : 'items-end';
                                const bgClass = isAdmin ? 'bg-white border border-gray-100 text-gray-800' : 'bg-pink-500 text-white';
                                const radiusClass = isAdmin ? 'rounded-br-2xl rounded-tr-2xl rounded-bl-sm rounded-tl-2xl' : 'rounded-bl-2xl rounded-tl-2xl rounded-br-sm rounded-tr-2xl';
                                
                                const imgHtml = msg.hinh_anh ? `<img src="${msg.hinh_anh}" class="max-w-full rounded mt-1 mb-1 border border-gray-200" style="max-height: 150px; object-fit: contain;">` : '';
                                const textHtml = msg.noi_dung ? msg.noi_dung : '';
                                
                                const html = `
                                    <div class="flex flex-col ${alignClass} w-full">
                                        <div class="max-w-[80%] ${bgClass} px-3 py-2 ${radiusClass} text-sm shadow-sm flex flex-col">
                                            ${imgHtml}
                                            ${textHtml}
                                        </div>
                                        <span class="text-[10px] text-gray-400 mt-1">${formatChatTime(msg.ngay_gui)}</span>
                                    </div>
                                `;
                                chatMessages.insertAdjacentHTML('beforeend', html);
                            });
                            
                            // Cuß╗Ön xuß╗æng cuß╗æi c├╣ng
                            chatMessages.scrollTop = chatMessages.scrollHeight;
                        }
                    }
                } catch (err) {
                    console.error('Lß╗ùi tß║úi tin nhß║»n:', err);
                }
            }

            let selectedChatImages = [];

            async 