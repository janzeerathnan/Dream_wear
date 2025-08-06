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
                <span class="font-semibold">Dream Wear Support</span>
            </div>
            <button id="chat-close" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Chat Messages -->
        <div id="chat-messages" class="h-64 overflow-y-auto p-4 space-y-4">
            <div class="flex items-start space-x-2">
                <div class="bg-gray-100 rounded-lg p-3 max-w-xs">
                    <p class="text-sm">Hello! I'm your Dream Wear assistant. How can I help you today?</p>
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
    
    // Toggle chat window
    chatToggle.addEventListener('click', function() {
        chatWindow.classList.toggle('hidden');
        if (!chatWindow.classList.contains('hidden')) {
            chatInput.focus();
        }
    });
    
    // Close chat window
    chatClose.addEventListener('click', function() {
        chatWindow.classList.add('hidden');
    });
    
    // Send message function
    function sendMessage() {
        const message = chatInput.value.trim();
        if (message) {
            // Add user message
            addMessage(message, 'user');
            chatInput.value = '';
            
            // Simulate bot response
            setTimeout(() => {
                const response = getBotResponse(message);
                addMessage(response, 'bot');
            }, 1000);
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
    
    // Bot response logic
    function getBotResponse(message) {
        const lowerMessage = message.toLowerCase();
        
        if (lowerMessage.includes('hello') || lowerMessage.includes('hi')) {
            return "Hi there! How can I help you with your jersey needs today?";
        } else if (lowerMessage.includes('shipping') || lowerMessage.includes('delivery')) {
            return "We offer standard shipping (3-5 days), express shipping (1-2 days), and overnight shipping. Free shipping on orders over $50!";
        } else if (lowerMessage.includes('return') || lowerMessage.includes('refund')) {
            return "We have a 30-day return policy for unworn items in original packaging. Contact our support team to initiate a return.";
        } else if (lowerMessage.includes('size') || lowerMessage.includes('fit')) {
            return "We carry sizes S-3XL for most jerseys. Check our size guide for detailed measurements. If you need help finding the right size, just let me know!";
        } else if (lowerMessage.includes('authentic') || lowerMessage.includes('real')) {
            return "Yes, all our jerseys are officially licensed and authentic. We work directly with manufacturers to ensure quality and authenticity.";
        } else if (lowerMessage.includes('custom') || lowerMessage.includes('personalize')) {
            return "Yes! We offer custom jersey services. Contact us for pricing and options for personalized jerseys with your name and number.";
        } else if (lowerMessage.includes('price') || lowerMessage.includes('cost')) {
            return "Our jerseys range from $29.99 to $199.99 depending on the style and team. Check out our products page for current prices!";
        } else if (lowerMessage.includes('contact') || lowerMessage.includes('help')) {
            return "You can reach us at support@dreamwear.com or call +1 (555) 123-4567. Our customer service team is available Monday-Friday 9AM-6PM EST.";
        } else {
            return "Thanks for your message! I'm here to help with any questions about our jerseys, shipping, returns, or anything else. Feel free to ask!";
        }
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