from flask import Flask, request, jsonify
from flask_cors import CORS
import json
import re

app = Flask(__name__)
CORS(app)

# Simple chatbot responses
def get_bot_response(message):
    message = message.lower()
    
    # Greetings
    if any(word in message for word in ['hello', 'hi', 'hey']):
        return "Hello! Welcome to Dream Wear. How can I help you with your jersey needs today?"
    
    # Product inquiries
    elif any(word in message for word in ['jersey', 'product', 'item']):
        return "We have a wide selection of authentic sports jerseys! You can browse our collection at dreamwear.com/products. What team or sport are you interested in?"
    
    # Shipping questions
    elif any(word in message for word in ['shipping', 'delivery', 'when', 'arrive']):
        return "We offer standard shipping (3-5 business days), express shipping (1-2 business days), and overnight shipping. Free shipping on orders over $50!"
    
    # Returns and exchanges
    elif any(word in message for word in ['return', 'exchange', 'refund']):
        return "We have a 30-day return policy for unworn items in original packaging. Contact our support team to initiate a return."
    
    # Size and fit questions
    elif any(word in message for word in ['size', 'fit', 'measurement']):
        return "We carry sizes S-3XL for most jerseys. Check our size guide at dreamwear.com/size-guide for detailed measurements."
    
    # Authenticity questions
    elif any(word in message for word in ['authentic', 'real', 'genuine', 'official']):
        return "Yes, all our jerseys are officially licensed and authentic. We work directly with manufacturers to ensure quality and authenticity."
    
    # Custom jerseys
    elif any(word in message for word in ['custom', 'personalize', 'customize']):
        return "Yes! We offer custom jersey services. Contact us for pricing and options for personalized jerseys with your name and number."
    
    # Price questions
    elif any(word in message for word in ['price', 'cost', 'expensive', 'cheap']):
        return "Our jerseys range from $29.99 to $199.99 depending on the style and team. Check out our products page for current prices!"
    
    # Contact information
    elif any(word in message for word in ['contact', 'help', 'support', 'phone', 'email']):
        return "You can reach us at support@dreamwear.com or call +1 (555) 123-4567. Our customer service team is available Monday-Friday 9AM-6PM EST."
    
    # Order status
    elif any(word in message for word in ['order', 'track', 'status']):
        return "You can check your order status by logging into your account or contacting our support team with your order number."
    
    # Payment questions
    elif any(word in message for word in ['payment', 'pay', 'credit', 'card']):
        return "We accept all major credit cards, PayPal, and Apple Pay. All payments are processed securely."
    
    # General help
    else:
        return "Thanks for your message! I'm here to help with any questions about our jerseys, shipping, returns, or anything else. Feel free to ask!"

@app.route('/chatbot', methods=['POST'])
def chatbot():
    try:
        data = request.get_json()
        user_message = data.get('message', '')
        
        if not user_message:
            return jsonify({'reply': 'Please provide a message.'}), 400
        
        # Get bot response
        bot_response = get_bot_response(user_message)
        
        return jsonify({
            'reply': bot_response,
            'status': 'success'
        })
        
    except Exception as e:
        return jsonify({
            'reply': 'Sorry, I encountered an error. Please try again.',
            'status': 'error',
            'error': str(e)
        }), 500

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({'status': 'healthy', 'service': 'dream-wear-chatbot'})

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000) 