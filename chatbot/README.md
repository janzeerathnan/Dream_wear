# Dream Wear Chatbot - OpenAI Powered

A Python Flask-based intelligent chatbot service for the Dream Wear e-commerce website with OpenAI GPT-3.5 integration for natural language processing and database storage for chat history.

## 🚀 Features

- ✅ **OpenAI GPT-3.5 Integration** - Intelligent, context-aware responses
- ✅ **Natural Language Processing** - Understands complex queries and intent
- ✅ **Conversation Memory** - Maintains context across chat sessions
- ✅ **Database storage** for chat history and analytics
- ✅ **Session-based chat tracking** with persistent conversations
- ✅ **User authentication integration** for personalized experiences
- ✅ **RESTful API endpoints** with comprehensive error handling
- ✅ **CORS support** for seamless web integration
- ✅ **Fallback system** when OpenAI is unavailable
- ✅ **Comprehensive testing suite** for quality assurance

## 🧠 AI Capabilities

### **Intelligent Responses**
- **Context Awareness**: Remembers previous conversation context
- **Natural Language**: Understands complex, conversational queries
- **Intent Recognition**: Identifies customer needs and questions
- **Personalized Responses**: Adapts tone and content based on conversation

### **Knowledge Base**
- **Product Information**: Detailed jersey knowledge and recommendations
- **Shipping & Delivery**: Comprehensive shipping policy information
- **Returns & Exchanges**: Clear return policy explanations
- **Pricing & Payment**: Transparent pricing and payment method details
- **Customer Support**: Contact information and support procedures

### **Advanced Features**
- **Multi-turn Conversations**: Handles complex, multi-step inquiries
- **Error Recovery**: Graceful handling of API failures
- **Response Optimization**: Balanced between helpfulness and conciseness
- **Brand Consistency**: Maintains Dream Wear's professional tone

## 🛠️ Prerequisites

- Python 3.7 or higher
- MySQL server running
- Dream Wear database with `chat_logs` table
- OpenAI API key (optional but recommended)

## 📦 Installation

### 1. **Clone and Navigate**
```bash
cd chatbot
```

### 2. **Run the Setup Script**
```bash
python setup.py
```

This will:
- Install required Python packages
- Configure OpenAI API key (optional)
- Test database connection
- Verify the `chat_logs` table exists
- Start the chatbot server

### 3. **Manual Setup (Alternative)**
```bash
# Install dependencies
pip install -r requirements.txt

# Set OpenAI API key (optional)
export OPENAI_API_KEY="your-api-key-here"

# Start the server
python app.py
```

## 🔑 OpenAI Configuration

### **Getting an API Key**
1. Visit [OpenAI Platform](https://platform.openai.com/api-keys)
2. Create a new API key
3. Copy the key and set it as an environment variable

### **Setting the API Key**
```bash
# Method 1: Environment variable
export OPENAI_API_KEY="your-api-key-here"

# Method 2: .env file
echo "OPENAI_API_KEY=your-api-key-here" > .env

# Method 3: Direct in code (not recommended for production)
# Edit config.py and set the API key directly
```

### **API Key Benefits**
- **Intelligent Responses**: Context-aware, natural language processing
- **Conversation Memory**: Remembers previous interactions
- **Complex Queries**: Handles multi-step questions and clarifications
- **Fallback System**: Graceful degradation when API is unavailable

## 🗄️ Database Setup

Make sure your MySQL database has the `chat_logs` table:

```sql
CREATE TABLE chat_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    response TEXT NOT NULL,
    message_type ENUM('user', 'bot') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

## 🔌 API Endpoints

### **POST /chatbot**
Send a message to the chatbot and get an intelligent response.

**Request:**
```json
{
    "message": "I need help finding a football jersey",
    "session_id": "session_1234567890",
    "user_id": 1
}
```

**Response:**
```json
{
    "reply": "I'd be happy to help you find the perfect football jersey! We have a great selection of authentic NFL jerseys. What team are you looking for, or would you like me to show you our most popular options?",
    "session_id": "session_1234567890",
    "status": "success"
}
```

### **GET /chatbot/history?session_id={session_id}**
Get chat history for a session with conversation context.

**Response:**
```json
{
    "history": [
        {
            "message": "Hello",
            "response": "",
            "message_type": "user",
            "created_at": "2024-01-15T10:30:00"
        },
        {
            "message": "",
            "response": "Hello! Welcome to Dream Wear. How can I help you with your jersey needs today?",
            "message_type": "bot",
            "created_at": "2024-01-15T10:30:05"
        }
    ],
    "status": "success"
}
```

### **GET /health**
Health check endpoint.

**Response:**
```json
{
    "status": "healthy",
    "service": "dream-wear-chatbot-openai"
}
```

### **GET /config**
Get chatbot configuration status.

**Response:**
```json
{
    "openai_status": "configured",
    "database_connected": true
}
```

## 🧪 Testing

### **Comprehensive Test Suite**
```bash
python test_chatbot.py
```

This will test:
- Health and configuration endpoints
- Chat responses for various scenarios
- Chat history functionality
- OpenAI integration (if configured)
- Fallback system (if OpenAI unavailable)

### **Interactive Testing**
```bash
python test_chatbot.py
# Choose option 2 for interactive testing
```

### **Manual Testing Examples**

**Basic Greeting:**
```
User: "Hello!"
Bot: "Hello! Welcome to Dream Wear. How can I help you with your jersey needs today?"
```

**Product Inquiry:**
```
User: "Do you have basketball jerseys?"
Bot: "Yes! We have a fantastic selection of authentic basketball jerseys from the NBA and college teams. We carry both current player jerseys and classic throwback styles. What team or player are you interested in?"
```

**Complex Query:**
```
User: "I want to buy a jersey for my son's birthday, but I'm not sure about sizing and shipping time"
Bot: "That's a great gift idea! Let me help you with both sizing and shipping. For sizing, we recommend checking our size guide at dreamwear.com/size-guide - we carry sizes S-3XL. For shipping, we offer standard (3-5 business days), express (1-2 business days), and overnight options. Free shipping on orders over $50! What's your son's age or size, and when is his birthday?"
```

## ⚙️ Configuration

### **Environment Variables**
```bash
# OpenAI Configuration
OPENAI_API_KEY=your-api-key-here
OPENAI_MODEL=gpt-3.5-turbo
OPENAI_MAX_TOKENS=300
OPENAI_TEMPERATURE=0.7

# Database Configuration
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=dream_wear

# Server Configuration
SERVER_HOST=0.0.0.0
SERVER_PORT=5000
DEBUG_MODE=True
```

### **Advanced Configuration**
Edit `config.py` to customize:
- OpenAI model parameters
- Database connection settings
- Server configuration
- Chat history limits

## 🌐 Web Integration

The chat widget in your website (`includes/chat-widget.php`) is configured to:

1. **Connect to the Flask chatbot API**
2. **Store chat sessions** in localStorage
3. **Load chat history** when reopening the chat
4. **Send user ID** if the user is logged in
5. **Handle intelligent responses** from OpenAI
6. **Maintain conversation context** across sessions

## 🔧 Troubleshooting

### **OpenAI API Issues**
1. **Check API Key**: Verify your OpenAI API key is correct
2. **Check Credits**: Ensure you have sufficient OpenAI credits
3. **Check Rate Limits**: Monitor API usage and rate limits
4. **Fallback System**: The chatbot will use fallback responses if OpenAI fails

### **Database Connection Issues**
1. Ensure MySQL server is running
2. Verify database credentials in `config.py`
3. Check if the `dream_wear` database exists
4. Ensure the `chat_logs` table is created

### **Chat Widget Not Working**
1. Make sure the Flask server is running on port 5000
2. Check browser console for CORS errors
3. Verify the API endpoint is accessible: `http://localhost:5000/health`
4. Test with the provided test scripts

### **Performance Issues**
1. **Monitor API Usage**: Check OpenAI API usage and costs
2. **Optimize Responses**: Adjust `max_tokens` and `temperature` in config
3. **Database Optimization**: Add indexes to frequently queried columns
4. **Caching**: Consider implementing response caching for common queries

## 🚀 Deployment

### **Production Considerations**
1. **Environment Variables**: Use proper environment variable management
2. **API Key Security**: Never commit API keys to version control
3. **Rate Limiting**: Implement rate limiting for API endpoints
4. **Monitoring**: Set up logging and monitoring for the chatbot service
5. **SSL/TLS**: Use HTTPS in production
6. **Load Balancing**: Consider load balancing for high traffic

### **Docker Deployment**
```dockerfile
FROM python:3.9-slim
WORKDIR /app
COPY requirements.txt .
RUN pip install -r requirements.txt
COPY . .
EXPOSE 5000
CMD ["python", "app.py"]
```

## 📊 Analytics & Monitoring

### **Trackable Metrics**
- Chat session duration and engagement
- User interaction patterns
- Common inquiry types and topics
- Response effectiveness and satisfaction
- OpenAI API usage and costs
- Error rates and fallback usage

### **Health Monitoring**
- Database connection status
- OpenAI API availability
- Response time monitoring
- Error logging and alerting
- Service uptime tracking

## 🔒 Security Features

- **Input Validation**: Sanitize user inputs before processing
- **API Key Protection**: Secure storage of OpenAI API keys
- **Rate Limiting**: Prevent abuse of chatbot endpoints
- **CORS Configuration**: Proper cross-origin resource sharing
- **Error Handling**: Graceful handling of security-related errors

## 🤝 Support

For issues or questions:
1. Check the troubleshooting section above
2. Run the comprehensive test suite
3. Check the Flask server logs for errors
4. Verify OpenAI API configuration
5. Test with the interactive testing tool

## 📈 Future Enhancements

- **Multi-language Support**: Add support for multiple languages
- **Voice Integration**: Add voice-to-text and text-to-speech
- **Advanced Analytics**: Implement detailed conversation analytics
- **Custom Training**: Fine-tune the model on Dream Wear specific data
- **Integration APIs**: Connect with inventory and order systems
- **Mobile App**: Develop native mobile chatbot interface

---

**Dream Wear Chatbot** - Powered by OpenAI GPT-3.5 for intelligent customer support! 🤖✨ 