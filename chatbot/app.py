from flask import Flask, request, jsonify
from flask_cors import CORS
import json
import re
import mysql.connector
from datetime import datetime
import uuid
import openai
import os
from typing import List, Dict, Optional

app = Flask(__name__)
CORS(app)

# Set host and port explicitly
PORT = 5001  # Using a different port
HOST = '127.0.0.1'  # localhost

# OpenAI Configuration
OPENAI_API_KEY = os.getenv('OPENAI_API_KEY', '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')
openai.api_key = OPENAI_API_KEY

# Database configuration
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'dream_wear'
}

# Dream Wear specific information for context
DREAM_WEAR_CONTEXT = """
You are an AI customer service representative for Dream Wear, a premium sports jersey retailer. 

Company Information:
- Website: dreamwear.com
- Email: support@dreamwear.com
- Phone: +1 (555) 123-4567
- Business Hours: Monday-Friday 9AM-6PM EST

Product Information:
- Authentic licensed sports jerseys
- Sizes: S-3XL for most jerseys
- Price Range: $29.99 to $199.99
- Sports: Football, Basketball, Baseball, Soccer, Hockey
- Custom jersey services available

Shipping Information:
- Standard Shipping: 3-5 business days
- Express Shipping: 1-2 business days
- Overnight Shipping: Available
- Free shipping on orders over $50

Return Policy:
- 30-day return policy
- Unworn items in original packaging
- Contact support team to initiate returns

Payment Methods:
- All major credit cards
- PayPal
- Apple Pay
- Secure payment processing

Your role is to:
1. Provide helpful, accurate information about Dream Wear products and services
2. Assist with order inquiries and customer support
3. Maintain a friendly, professional tone
4. Direct customers to appropriate resources when needed
5. Keep responses concise but informative
6. Always prioritize customer satisfaction

If you don't know something specific, suggest contacting customer support or checking the website.
"""

def get_db_connection():
    """Create and return a database connection"""
    try:
        # Test if MySQL server is running first
        connection = mysql.connector.connect(
            host=DB_CONFIG['host'],
            user=DB_CONFIG['user'],
            password=DB_CONFIG['password'],
            connection_timeout=5
        )
        
        # Check if database exists, create if it doesn't
        cursor = connection.cursor()
        cursor.execute(f"CREATE DATABASE IF NOT EXISTS {DB_CONFIG['database']}")
        cursor.close()
        connection.close()
        
        # Connect to the specific database
        connection = mysql.connector.connect(**DB_CONFIG)
        
        # Create tables if they don't exist
        cursor = connection.cursor()
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS chat_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id VARCHAR(255),
                session_id VARCHAR(255) NOT NULL,
                message TEXT,
                response TEXT,
                message_type ENUM('user', 'bot') NOT NULL,
                created_at DATETIME NOT NULL
            )
        """)
        connection.commit()
        cursor.close()
        
        return connection
        
    except mysql.connector.Error as err:
        if err.errno == 2003:  # Can't connect to MySQL server
            print("Error: MySQL server is not running. Please start your MySQL server.")
        elif err.errno == 1045:  # Access denied
            print("Error: Invalid database credentials. Please check your username and password.")
        elif err.errno == 1049:  # Unknown database
            print(f"Error: Database '{DB_CONFIG['database']}' does not exist.")
        else:
            print(f"Database Error: {err}")
        return None

def store_chat_log(session_id, user_id, message, response, message_type):
    """Store chat message in database"""
    try:
        connection = get_db_connection()
        if connection:
            cursor = connection.cursor()
            
            # Insert chat log
            query = """
                INSERT INTO chat_logs (user_id, session_id, message, response, message_type, created_at)
                VALUES (%s, %s, %s, %s, %s, %s)
            """
            cursor.execute(query, (user_id, session_id, message, response, message_type, datetime.now()))
            connection.commit()
            cursor.close()
            connection.close()
            return True
        return False
    except Exception as e:
        print(f"Error storing chat log: {e}")
        return False

def get_chat_history(session_id, limit=10):
    """Get recent chat history for a session"""
    try:
        connection = get_db_connection()
        if connection:
            cursor = connection.cursor(dictionary=True)
            
            query = """
                SELECT message, response, message_type, created_at
                FROM chat_logs 
                WHERE session_id = %s 
                ORDER BY created_at ASC
                LIMIT %s
            """
            cursor.execute(query, (session_id, limit))
            history = cursor.fetchall()
            cursor.close()
            connection.close()
            return history
        return []
    except Exception as e:
        print(f"Error getting chat history: {e}")
        return []

def format_chat_history_for_openai(history: List[Dict]) -> str:
    """Format chat history for OpenAI API context"""
    formatted_history = ""
    for entry in history:
        if entry['message_type'] == 'user' and entry['message']:
            formatted_history += f"User: {entry['message']}\n"
        elif entry['message_type'] == 'bot' and entry['response']:
            formatted_history += f"Assistant: {entry['response']}\n"
    return formatted_history

def get_openai_response(user_message: str, chat_history: List[Dict] = None) -> str:
    """Get intelligent response from OpenAI API"""
    try:
        # Format conversation history
        messages = [{"role": "system", "content": DREAM_WEAR_CONTEXT}]
        
        # Add chat history if available
        if chat_history:
            for entry in chat_history:
                if entry['message_type'] == 'user' and entry['message']:
                    messages.append({"role": "user", "content": entry['message']})
                elif entry['message_type'] == 'bot' and entry['response']:
                    messages.append({"role": "assistant", "content": entry['response']})
        
        # Add current message
        messages.append({"role": "user", "content": user_message})
        
        # Call OpenAI API with proper error handling
        try:
            response = openai.ChatCompletion.create(
                model="gpt-3.5-turbo",
                messages=messages,
                max_tokens=300,
                temperature=0.7,
                presence_penalty=0.1,
                frequency_penalty=0.1
            )
            
            if response and response.choices and len(response.choices) > 0:
                return response.choices[0].message.content.strip()
            else:
                print("Empty response from OpenAI")
                return get_fallback_response(user_message)
        except Exception as api_error:
            print(f"OpenAI API call failed: {api_error}")
            return get_fallback_response(user_message)
        
    except openai.error.OpenAIError as e:
        print(f"OpenAI API Error: {e}")
        # Fallback to simple responses if OpenAI fails
        return get_fallback_response(user_message)
    except Exception as e:
        print(f"Error getting OpenAI response: {e}")
        return get_fallback_response(user_message)

def get_fallback_response(message: str) -> str:
    """Fallback response system when OpenAI is unavailable"""
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
        # Validate request
        if not request.is_json:
            return jsonify({'error': 'Content-Type must be application/json'}), 400
            
        data = request.get_json()
        user_message = data.get('message', '').strip()
        session_id = data.get('session_id', str(uuid.uuid4()))
        user_id = data.get('user_id', None)
        
        if not user_message:
            return jsonify({'error': 'Please provide a message.'}), 400
            
        print(f"Received message: {user_message}")  # Debug log
        
        # Check database connection
        if not get_db_connection():
            print("Warning: Database connection failed")
            # Continue without database - fallback to stateless mode
        
        try:
            # Get chat history for context
            chat_history = get_chat_history(session_id, limit=5)
            
            # Get response from OpenAI or fallback
            bot_response = get_openai_response(user_message, chat_history)
            
            if not bot_response:
                bot_response = get_fallback_response(user_message)
            
            # Try to store messages, but don't fail if storage fails
            try:
                store_chat_log(session_id, user_id, user_message, '', 'user')
                store_chat_log(session_id, user_id, '', bot_response, 'bot')
            except Exception as storage_error:
                print(f"Warning: Failed to store chat log: {storage_error}")
            
            return jsonify({
                'reply': bot_response,
                'session_id': session_id,
                'status': 'success'
            })
            
        except Exception as processing_error:
            print(f"Error processing message: {processing_error}")
            return jsonify({
                'reply': 'I apologize, but I encountered an error processing your message. Please try again.',
                'status': 'error',
                'session_id': session_id
            }), 500
        
    except Exception as e:
        return jsonify({
            'reply': 'Sorry, I encountered an error. Please try again.',
            'status': 'error',
            'error': str(e)
        }), 500

@app.route('/chatbot/history', methods=['GET'])
def get_history():
    """Get chat history for a session"""
    try:
        session_id = request.args.get('session_id')
        if not session_id:
            return jsonify({'error': 'Session ID required'}), 400
        
        history = get_chat_history(session_id)
        return jsonify({
            'history': history,
            'status': 'success'
        })
        
    except Exception as e:
        return jsonify({
            'error': 'Failed to retrieve chat history',
            'status': 'error',
            'error': str(e)
        }), 500

@app.route('/health', methods=['GET'])
def health_check():
    return jsonify({'status': 'healthy', 'service': 'dream-wear-chatbot-openai'})

@app.route('/config', methods=['GET'])
def get_config():
    """Get chatbot configuration status"""
    openai_status = "configured" if OPENAI_API_KEY != 'your-openai-api-key-here' else "not_configured"
    return jsonify({
        'openai_status': openai_status,
        'database_connected': get_db_connection() is not None
    })

if __name__ == '__main__':
    try:
        print(f"Starting server on http://{HOST}:{PORT}")
        app.run(host=HOST, port=PORT, debug=True)
    except Exception as e:
        print(f"Error starting server: {e}")