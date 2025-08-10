<!-- Chat Widget -->
<div id="chat-widget" class="fixed bottom-4 right-4 z-50">
    <!-- Chat Toggle Button -->
    <button id="chat-toggle" class="bg-primary text-white p-4 rounded-full shadow-lg hover:bg-blue-700 transition-colors">
        <i class="fas fa-comments text-xl"></i>
    </button>
    
    <!-- Chat Window -->
    <div id="chat-window" class="hidden absolute bottom-16 right-0 w-80 bg-white rounded-lg shadow-xl border">
        <!-- Chat Header -->
        <div class="bg-primary text-white p-4 rounded-t-lg flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-headset text-lg"></i>
                <span class="font-semibold"><?php echo SITE_NAME; ?> Support</span>
            </div>
            <button id="chat-close" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Chat Messages -->
        <div id="chat-messages" class="h-64 overflow-y-auto p-4 space-y-4">
            <div class="flex items-start space-x-2">
                <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                    <p class="text-sm">Hello! I'm your <?php echo SITE_NAME; ?> assistant. How can I help you today?</p>
                </div>
            </div>
        </div>
        
        <!-- Chat Input -->
        <div class="p-4 border-t">
            <div class="flex space-x-2">
                <input type="text" id="chat-input" placeholder="Type your message..." 
                       class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                <button id="chat-send" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Chat Widget Functionality
document.addEventListener('DOMContentLoaded', function() {
    const chatToggle = document.getElementById('chat-toggle');
    const chatWindow = document.getElementById('chat-window');
    const chatClose = document.getElementById('chat-close');
    const chatInput = document.getElementById('chat-input');
    const chatSend = document.getElementById('chat-send');
    const chatMessages = document.getElementById('chat-messages');
    
    // Generate session ID for this chat session
    let sessionId = localStorage.getItem('chat_session_id');
    if (!sessionId) {
        sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        localStorage.setItem('chat_session_id', sessionId);
    }
    
    // Get user ID if logged in
    const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;
    
    // Toggle chat window
    chatToggle.addEventListener('click', function() {
        chatWindow.classList.toggle('hidden');
        if (!chatWindow.classList.contains('hidden')) {
            chatInput.focus();
            loadChatHistory();
        }
    });
    
    // Close chat window
    chatClose.addEventListener('click', function() {
        chatWindow.classList.add('hidden');
    });
    
    // Load chat history from API
    async function loadChatHistory() {
        try {
            const response = await fetch(`http://localhost:5000/chatbot/history?session_id=${sessionId}`);
            const data = await response.json();
            
            if (data.status === 'success' && data.history.length > 0) {
                // Clear existing messages except the welcome message
                const welcomeMessage = chatMessages.querySelector('.bg-gray-100');
                chatMessages.innerHTML = '';
                if (welcomeMessage) {
                    chatMessages.appendChild(welcomeMessage);
                }
                
                // Add history messages
                data.history.reverse().forEach(log => {
                    if (log.message_type === 'user' && log.message) {
                        addMessage(log.message, 'user');
                    }
                    if (log.message_type === 'bot' && log.response) {
                        addMessage(log.response, 'bot');
                    }
                });
            }
        } catch (error) {
            console.error('Error loading chat history:', error);
        }
    }
    
    // Send message function
    async function sendMessage() {
        const message = chatInput.value.trim();
        if (message) {
            // Add user message immediately
            addMessage(message, 'user');
            chatInput.value = '';
            
            // Show typing indicator
            const typingDiv = document.createElement('div');
            typingDiv.className = 'flex items-start space-x-2';
            typingDiv.innerHTML = `
                <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                    <p class="text-sm">Typing...</p>
                </div>
            `;
            chatMessages.appendChild(typingDiv);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            try {
                // Send to chatbot API
                const response = await fetch('http://localhost:5000/chatbot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        message: message,
                        session_id: sessionId,
                        user_id: userId
                    })
                });
                
                const data = await response.json();
                
                // Remove typing indicator
                chatMessages.removeChild(typingDiv);
                
                if (data.status === 'success') {
                    addMessage(data.reply, 'bot');
                } else {
                    addMessage('Sorry, I encountered an error. Please try again.', 'bot');
                }
            } catch (error) {
                // Remove typing indicator
                chatMessages.removeChild(typingDiv);
                addMessage('Sorry, I\'m having trouble connecting. Please try again later.', 'bot');
                console.error('Error sending message:', error);
            }
        }
    }
    
    // Add message to chat
    function addMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'flex items-start space-x-2';
        
        if (sender === 'user') {
            messageDiv.innerHTML = `
                <div class="ml-auto">
                    <div class="bg-primary text-white rounded-lg p-3 max-w-xs">
                        <p class="text-sm">${text}</p>
                    </div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                    <p class="text-sm">${text}</p>
                </div>
            `;
        }
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Send message on Enter key
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    // Send message on button click
    chatSend.addEventListener('click', sendMessage);
    
    // Close chat when clicking outside
    document.addEventListener('click', function(e) {
        if (!chatToggle.contains(e.target) && !chatWindow.contains(e.target)) {
            chatWindow.classList.add('hidden');
        }
    });
});
</script> 