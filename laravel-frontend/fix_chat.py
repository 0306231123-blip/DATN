import re

with open('resources/views/layouts/user.blade.php', 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Modify HTML tabs
text = re.sub(
    r'<!-- Tabs -->.*?</div>',
    '<!-- Tabs -->\n            <div class=\"flex border-b border-gray-200 bg-white\">\n                <button id=\"tab-cskh\" class=\"w-full py-2 text-sm font-semibold text-pink-500 border-b-2 border-pink-500 focus:outline-none transition\">Chat với Shop</button>\n            </div>',
    text, count=1, flags=re.DOTALL
)

# 2. Remove AI chat messages HTML
text = re.sub(r'<!-- Messages Area AI -->.*?</div>\s*<!-- Image Preview Area -->', '<!-- Image Preview Area -->', text, flags=re.DOTALL)

# 3. Remove tab switching JS
text = re.sub(r'const tabCskh = document\.getElementById\(\'tab-cskh\'\);.*?if \(tabCskh && tabAi\) \{.*?\n            \}', 'let currentChatTab = \'cskh\';', text, flags=re.DOTALL)

# 4. Remove AI chat references in JS
text = text.replace("const aiChatMessages = document.getElementById('ai-chat-messages');", "")

# 5. Remove 'if (currentChatTab === \'ai\')' block entirely in sendMessage
text = re.sub(r'if \(currentChatTab === \'ai\'\) \{.*?\n                \}\s*let uploadedUrls = \[\];', 'let uploadedUrls = [];', text, flags=re.DOTALL)

# 6. Remove DOMContentLoaded AI script
text = re.sub(r'// Load AI Chat History & Restock Suggestion.*?\n            \}\);', '', text, flags=re.DOTALL)

with open('resources/views/layouts/user.blade.php', 'w', encoding='utf-8') as f:
    f.write(text)
print('Fixed!')
