import os
from typing import Optional

class ChatbotConfig:
    """Configuration class for Dream Wear Chatbot"""
    
    # OpenAI Configuration
    OPENAI_API_KEY = os.getenv('OPENAI_API_KEY', 'your-openai-api-key-here')
    OPENAI_MODEL = os.getenv('OPENAI_MODEL', 'gpt-3.5-turbo')
    OPENAI_MAX_TOKENS = int(os.getenv('OPENAI_MAX_TOKENS', '300'))
    OPENAI_TEMPERATURE = float(os.getenv('OPENAI_TEMPERATURE', '0.7'))
    
    # Database Configuration
    DB_HOST = os.getenv('DB_HOST', 'localhost')
    DB_USER = os.getenv('DB_USER', 'root')
    DB_PASSWORD = os.getenv('DB_PASSWORD', '')
    DB_NAME = os.getenv('DB_NAME', 'dream_wear')
    
    # Server Configuration
    SERVER_HOST = os.getenv('SERVER_HOST', '0.0.0.0')
    SERVER_PORT = int(os.getenv('SERVER_PORT', '5000'))
    DEBUG_MODE = os.getenv('DEBUG_MODE', 'True').lower() == 'true'
    
    # Chat Configuration
    MAX_HISTORY_LIMIT = int(os.getenv('MAX_HISTORY_LIMIT', '10'))
    SESSION_TIMEOUT = int(os.getenv('SESSION_TIMEOUT', '3600'))  # 1 hour
    
    @classmethod
    def get_database_config(cls) -> dict:
        """Get database configuration dictionary"""
        return {
            'host': cls.DB_HOST,
            'user': cls.DB_USER,
            'password': cls.DB_PASSWORD,
            'database': cls.DB_NAME
        }
    
    @classmethod
    def is_openai_configured(cls) -> bool:
        """Check if OpenAI API is properly configured"""
        return cls.OPENAI_API_KEY != 'your-openai-api-key-here' and cls.OPENAI_API_KEY != ''
    
    @classmethod
    def validate_config(cls) -> dict:
        """Validate all configuration settings"""
        validation_results = {
            'openai_configured': cls.is_openai_configured(),
            'database_configured': True,  # Will be validated at runtime
            'server_configured': True,
            'warnings': []
        }
        
        if not cls.is_openai_configured():
            validation_results['warnings'].append(
                "OpenAI API key not configured. Chatbot will use fallback responses."
            )
        
        return validation_results 