<!-- Floating Chat Widget -->
<div id="ecommerce-chatbot-container" class="fixed bottom-5 right-5 z-50 font-sans">
    <!-- Toggle Button -->
    <button onclick="toggleChat()" class="bg-indigo-600 hover:bg-indigo-700 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-transform hover:scale-105 focus:outline-none">
        <i id="chatbot-icon" class="fa-solid fa-comments text-2xl"></i>
    </button>

    <!-- Chat Window -->
    <div id="chatbot-window" class="hidden absolute bottom-20 right-0 w-96 max-w-[calc(100vw-2rem)] bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col overflow-hidden transition-all duration-300" style="height: 500px;">
        <!-- Header -->
        <div class="bg-indigo-600 text-white p-4 flex items-center justify-between">
            <h3 class="font-bold text-sm">ShopEase Assistant</h3>
            <button onclick="toggleChat()" class="text-white hover:text-slate-200"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <!-- Messages Area -->
        <div id="chat-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50 text-sm">
            <div class="flex items-start space-x-2">
                <div class="bg-indigo-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs font-bold">AI</div>
                <div class="bg-white border p-3 rounded-2xl rounded-tl-sm text-slate-700 shadow-sm max-w-[80%]">
                    Hello! Welcome to our store. How can I assist you today?
                </div>
            </div>
        </div>

        <!-- Input Form -->
        <form id="chat-form" onsubmit="sendChatMessage(event)" class="p-3 bg-white border-t flex items-center space-x-2">
            @csrf
            <input type="text" id="chat-input" placeholder="Type a message..." class="flex-1 bg-slate-100 border rounded-full px-4 py-2 text-sm focus:outline-none focus:border-indigo-500">
            <button type="submit" class="bg-indigo-600 text-white w-9 h-9 rounded-full flex items-center justify-center"><i class="fa-solid fa-paper-plane text-xs"></i></button>
        </form>
    </div>
</div>

<script>
    let isOpen = false;
    function toggleChat() {
        isOpen = !isOpen;
        const win = document.getElementById('chatbot-window');
        win.classList.toggle('hidden', !isOpen);
    }

    async function sendChatMessage(e) {
        e.preventDefault();
        const input = document.getElementById('chat-input');
        const text = input.value.trim();
        if (!text) return;

        // Append user message
        appendMsg(text, 'user');
        input.value = '';

        try {
            const response = await fetch('/api/chatbot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: text })
            });
            const data = await response.json();
            if (data.success) {
                appendMsg(data.reply, 'bot');
            }
        } catch (error) {
            appendMsg("Sorry, something went wrong. Please try again.", 'bot');
        }
    }

    function appendMsg(text, sender) {
        const container = document.getElementById('chat-messages');
        const div = document.createElement('div');
        if (sender === 'user') {
            div.className = "flex items-start justify-end space-x-2";
            div.innerHTML = `<div class="bg-indigo-600 text-white p-3 rounded-2xl rounded-tr-sm max-w-[80%]">${text}</div>`;
        } else {
            div.className = "flex items-start space-x-2";
            div.innerHTML = `<div class="bg-indigo-600 text-white rounded-full w-7 h-7 flex items-center justify-center text-xs font-bold">AI</div><div class="bg-white border p-3 rounded-2xl rounded-tl-sm text-slate-700 shadow-sm max-w-[80%]">${text}</div>`;
        }
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }
</script>