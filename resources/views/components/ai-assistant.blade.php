<!-- Floating AI Chat Widget -->
<div id="ai-assistant-container" class="ai-assistant-wrap position-fixed" style="z-index: 9999;">
    <!-- Chat Window -->
    <div id="ai-chat-window" class="card shadow-lg border-0 d-none ai-chat-responsive">
        <div class="card-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="bg-white rounded-circle p-1 me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="ti ti-robot text-primary fs-20"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">Arewa Smart AI</h6>
                    <small class="opacity-75"><i class="ti ti-circle-filled text-success me-1 fs-20"></i> AI Assistant</small>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <button type="button" class="btn btn-link text-white p-0 me-2 opacity-50 hover-opacity-100" onclick="clearAiHistory()" title="Clear Chat History">
                    <i class="ti ti-trash fs-15"></i>
                </button>
                <button type="button" class="btn-close btn-close-white" onclick="toggleAiChat()" aria-label="Close"></button>
            </div>
        </div>
        <div class="card-body p-3 overflow-auto" id="ai-chat-messages" style="background: #f8f9fa;">
            <!-- Welcome Message -->
            <div class="bg-white p-3 rounded-4 shadow-sm mb-3 ai-message">
                Hello! I'm your **Arewa Smart AI Support Assistant**. <br><br>
                I can help you with:
                <ul class="mb-0 mt-2">
                    <li>Integration snippets (PHP, Python, etc.)</li>
                    <li>Live pricing for your account</li>
                    <li>API apply & Best practices</li>
                </ul>
            </div>
        </div>
        <div class="card-footer bg-white p-3 border-0">
            <div class="input-group">
                <input type="text" id="ai-user-input" class="form-control border-light rounded-pill px-3 shadow-none" placeholder="Ask about NIN/BVN prices..." onkeypress="handleAiKeypress(event)">
                <button class="btn btn-primary rounded-circle ms-2 d-flex align-items-center justify-content-center" type="button" onclick="sendAiMessage()" style="width: 45px; height: 45px;">
                    <i class="ti ti-send fs-15"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Floating Button -->
    <button id="ai-chat-toggle" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center position-relative heartbeat-animation ai-toggle-btn" onclick="toggleAiChat()">
        <i class="ti ti-message-chatbot fs-20"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
            AI
        </span>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    function toggleAiChat() {
        const window = document.getElementById('ai-chat-window');
        window.classList.toggle('d-none');
        if (!window.classList.contains('d-none')) {
            document.getElementById('ai-user-input').focus();
        }
    }

    function handleAiKeypress(e) {
        if (e.key === 'Enter') sendAiMessage();
    }

    async function sendAiMessage() {
        const input = document.getElementById('ai-user-input');
        const message = input.value.trim();
        if (!message) return;

        appendMessage('user', message);
        input.value = '';

        const typingIndicator = appendTypingIndicator();

        try {
            const response = await fetch('{{ route("developer.ai.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({ 
                    message: message,
                    current_url: window.location.href 
                })
            });

            const data = await response.json();
            typingIndicator.remove();

            if (data.status === 'success') {
                appendMessage('ai', data.answer);
            } else {
                appendMessage('ai', "I'm sorry, I'm having trouble connecting to the AI brain right now. " + (data.message || ""));
            }
        } catch (e) {
            if (typingIndicator) typingIndicator.remove();
            appendMessage('ai', "A network error occurred. Please check your connection.");
        }
    }

    function appendMessage(role, text, save = true) {
        const container = document.getElementById('ai-chat-messages');
        const msgDiv = document.createElement('div');
        
        if (role === 'user') {
            msgDiv.className = 'bg-primary text-white p-3 rounded-4 shadow-sm mb-3 align-self-end text-end ms-5';
            msgDiv.innerText = text;
        } else {
            msgDiv.className = 'bg-white p-3 rounded-4 shadow-sm mb-3 ai-message me-5';
            // Use marked.js for professional markdown rendering
            msgDiv.innerHTML = marked.parse(text);
        }
        
        container.appendChild(msgDiv);
        container.scrollTop = container.scrollHeight;

        if (save) {
            saveAiHistory(role, text);
        }
    }

    // Persistence Logic
    function saveAiHistory(role, text) {
        let history = JSON.parse(localStorage.getItem('ai_chat_history') || '[]');
        history.push({ role, text });
        // Keep last 20 messages
        if (history.length > 20) history = history.slice(-20);
        localStorage.setItem('ai_chat_history', JSON.stringify(history));
    }

    function loadAiHistory() {
        const history = JSON.parse(localStorage.getItem('ai_chat_history') || '[]');
        if (history.length > 0) {
            // Remove the hardcoded welcome message if we have history
            const messagesContainer = document.getElementById('ai-chat-messages');
            messagesContainer.innerHTML = ''; 
            
            history.forEach(msg => {
                appendMessage(msg.role, msg.text, false);
            });
        }
    }

    function clearAiHistory() {
        Swal.fire({
            title: 'Clear Chat?',
            text: "Your conversation history will be permanently removed.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear it!'
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.removeItem('ai_chat_history');
                location.reload(); // Quickest way to reset the UI
            }
        });
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', loadAiHistory);

    function appendTypingIndicator() {
        const container = document.getElementById('ai-chat-messages');
        const div = document.createElement('div');
        div.className = 'bg-white p-3 rounded-4 shadow-sm mb-3 me-5';
        div.innerHTML = '<span class="spinner-grow spinner-grow-sm text-primary me-1"></span> Processing...';
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
        return div;
    }
</script>

<style>
    .ai-assistant-wrap {
        bottom: 25px;
        right: 25px;
    }

    .ai-chat-responsive {
        width: 380px; 
        height: 520px; 
        border-radius: 1.5rem; 
        overflow: hidden; 
        bottom: 80px; 
        position: absolute; 
        right: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1rem 3rem rgba(0,0,0,0.175) !important;
        backdrop-filter: blur(10px);
    }

    .ai-toggle-btn {
        width: 65px;
        height: 65px;
        transition: transform 0.2s;
    }

    /* Mobile Immersion Mode */
    @media (max-width: 767.98px) {
        .ai-assistant-wrap {
            bottom: 15px;
            right: 15px;
        }

        .ai-chat-responsive {
            position: fixed;
            top: auto;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100% !important;
            height: 85vh !important;
            border-radius: 1.5rem 1.5rem 0 0 !important;
            z-index: 10000;
        }

        .ai-toggle-btn {
            width: 55px;
            height: 55px;
        }
        
        /* Ensure messages have more breathing room on mobile */
        #ai-chat-messages {
            max-height: none !important;
            flex-grow: 1;
        }
    }

    #ai-chat-messages {
        display: flex;
        flex-direction: column;
        max-height: 400px;
        scrollbar-width: thin;
        background: #f8f9fa;
    }

    #ai-chat-messages::-webkit-scrollbar {
        width: 4px;
    }
    #ai-chat-messages::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }
    .ai-message {
        border-left: 4px solid var(--bs-primary);
    }
   
    
    @keyframes heartbeat {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    .heartbeat-animation {
        animation: heartbeat 3s infinite;
    }
    .heartbeat-animation:hover {
        animation: none;
    }
</style>
