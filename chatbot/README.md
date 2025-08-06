# Dream Wear Chatbot

A Python Flask-based chatbot service for the Dream Wear e-commerce website.

## Features

- **Intelligent Responses**: Handles common customer inquiries about products, shipping, returns, and more
- **RESTful API**: Provides a simple JSON API for integration with the main website
- **CORS Support**: Configured to work with web applications
- **Error Handling**: Robust error handling and logging

## Setup

1. **Install Python dependencies:**
   ```bash
   pip install -r requirements.txt
   ```

2. **Run the chatbot service:**
   ```bash
   python app.py
   ```

3. **The service will be available at:**
   - Main endpoint: `http://localhost:5000/chatbot`
   - Health check: `http://localhost:5000/health`

## API Usage

### Send a message to the chatbot:

**POST** `/chatbot`

**Request Body:**
```json
{
    "message": "Hello, I need help with shipping"
}
```

**Response:**
```json
{
    "reply": "We offer standard shipping (3-5 business days), express shipping (1-2 business days), and overnight shipping. Free shipping on orders over $50!",
    "status": "success"
}
```

## Integration with Website

The chatbot is integrated into the main website via JavaScript in the chat widget component (`includes/chat-widget.php`). The widget communicates with this Flask service to provide real-time customer support.

## Supported Topics

- Product inquiries
- Shipping information
- Returns and exchanges
- Size and fit questions
- Authenticity questions
- Custom jersey services
- Pricing information
- Contact information
- Order status
- Payment methods

## Development

To modify the chatbot responses, edit the `get_bot_response()` function in `app.py`. The function uses keyword matching to provide appropriate responses to customer inquiries. 