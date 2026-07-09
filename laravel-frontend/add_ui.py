with open('resources/views/page_user/home.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Insert HTML section
skin_section_html = """
{{-- AI SKIN TYPE RECOMMENDATION --}}
<section id="ai-skin-type-section" class="hidden mb-12 bg-gradient-to-br from-green-50 to-teal-50 p-6 rounded-sm border border-transparent hover:border-teal-500 shadow-sm relative overflow-hidden">
    <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl">🌿</div>
    <div class="flex items-center mb-4 relative z-10">
        <span class="text-3xl mr-3 animate-pulse">✨</span>
        <h2 class="text-2xl font-black text-teal-600 uppercase tracking-widest">Chăm Sóc Dành Riêng Cho <span id="skin-type-label" class="text-green-600">Làn Da</span> Của Bạn</h2>
    </div>
    <div class="relative z-10 flex flex-col md:flex-row gap-6 items-center">
        <div class="flex-1">
            <div class="bg-white p-4 rounded-sm shadow-sm border border-teal-50 relative">
                <div class="absolute -left-2 -top-2 text-2xl">💧</div>
                <p id="ai-skin-type-reason" class="text-gray-700 italic font-medium leading-relaxed"></p>
            </div>
        </div>
        <div id="ai-skin-type-product" class="w-full md:flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 custom-scrollbar overflow-x-auto">
            <!-- Product cards will be rendered here -->
        </div>
    </div>
</section>
"""

html_target = "</section>\n\n{{-- Featured Products --}}"
if html_target in text and 'id="ai-skin-type-section"' not in text:
    text = text.replace(html_target, "</section>\n" + skin_section_html + "\n{{-- Featured Products --}}")

# 2. Insert JS logic
skin_js = """
                    // --- FETCH SKIN TYPE SUGGESTION ---
                    const skinSection = document.getElementById('ai-skin-type-section');
                    const skinReasonEl = document.getElementById('ai-skin-type-reason');
                    const skinProductEl = document.getElementById('ai-skin-type-product');
                    const skinTypeLabel = document.getElementById('skin-type-label');
                    
                    fetch('http://localhost:5000/api/ai-skin-type-suggest', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ user_id: userId })
                    }).then(res => res.json()).then(result => {
                        if (result.success && result.products && result.products.length > 0) {
                            if (skinTypeLabel) skinTypeLabel.innerText = result.loai_da_text;
                            if (skinReasonEl) skinReasonEl.innerHTML = result.reason;
                            
                            let productsHtml = '';
                            result.products.forEach(sp => {
                                const price = new Intl.NumberFormat('vi-VN').format(sp.gia_khuyen_mai || sp.gia) + ' đ';
                                let img = sp.hinh_anh_url ? sp.hinh_anh_url : 'https://via.placeholder.com/300x300?text=No+Image';
                                if (!img.startsWith('http')) {
                                    img = img.startsWith('/') ? img : '/' + img;
                                }
                                const tagName = sp.ten_danh_muc ? sp.ten_danh_muc : 'Gợi ý cho bạn';

                                productsHtml += `
                                    <a href="/user/detail/${sp.ma_san_pham}" class="bg-white p-2 hover:shadow-md hover:-translate-y-[1px] transition-all duration-200 flex flex-col h-full group relative border border-transparent hover:border-teal-500 flex-1 min-w-[150px]">
                                        <div class="absolute top-0 right-0 z-20 bg-gradient-to-r from-teal-500 to-green-400 text-white text-[10px] sm:text-xs font-bold px-3 py-1 rounded-bl-sm shadow-md">
                                            ${tagName}
                                        </div>
                                        <div class="relative w-full aspect-square mb-3 overflow-hidden rounded-sm bg-gray-50">
                                            <img src="${img}" alt="${sp.ten_san_pham}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                        </div>
                                        <div class="flex-grow flex flex-col justify-between">
                                            <h3 class="font-bold text-gray-800 text-sm line-clamp-2 mb-1 group-hover:text-teal-600 transition">
                                                ${sp.ten_san_pham}
                                            </h3>
                                            <div class="text-teal-600 font-medium text-base">
                                                ${price}
                                            </div>
                                        </div>
                                    </a>
                                `;
                            });
                            
                            if (skinProductEl) skinProductEl.innerHTML = productsHtml;
                            if (skinSection) skinSection.classList.remove('hidden');
                        }
                    }).catch(err => console.error('Lỗi tải AI Skin Type:', err));
"""

js_target = "const response = await fetch('http://localhost:5000/api/ai-next-step-suggest'"
if js_target in text and 'http://localhost:5000/api/ai-skin-type-suggest' not in text:
    text = text.replace(js_target, skin_js + "\n                    " + js_target)

with open('resources/views/page_user/home.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)
print("Done safely.")
