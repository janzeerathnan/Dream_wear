<?php
// Dream Wear Configuration File

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'dream_wear');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application Configuration
define('SITE_NAME', 'Dream Wear');
define('SITE_URL', 'http://localhost/Dream%20Wear');
define('ADMIN_EMAIL', 'admin@dreamwear.com');

// Security Configuration
define('SECRET_KEY', 'dream_wear_secret_key_2024');
define('SESSION_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_NAME', 'dream_wear_csrf');

// File Upload Configuration
define('UPLOAD_DIR', 'assets/images/products/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Chatbot Configuration
define('CHATBOT_URL', 'http://localhost:5000/chatbot');

// Pagination
define('ITEMS_PER_PAGE', 12);

// Error Reporting (set to 0 for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone
date_default_timezone_set('America/New_York');

// Security headers (only if headers haven't been sent)
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}

// CSRF Protection
if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
}

// Helper function to generate CSRF token
function generateCSRFToken() {
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Helper function to validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Helper function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Helper function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Helper function to check if user is admin
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Helper function to get current URL
function getCurrentURL() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
           "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

// Helper function to format price
function formatPrice($price) {
    return 'Rs. ' . number_format($price, 2);
}

// Helper function to generate order number
function generateOrderNumber() {
    return 'DW-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8));
}

// Helper function to log activity
function logActivity($user_id, $action, $details = '') {
    // In a real application, you would log to a file or database
    error_log("User $user_id performed action: $action - $details");
}

// Helper function to check if user has complete address
function hasCompleteAddress($userData) {
    return !empty($userData['address']) && 
           !empty($userData['city']) && 
           !empty($userData['state']) && 
           !empty($userData['zip_code']) && 
           !empty($userData['country']);
}

// Helper function to validate payment method
function validatePaymentMethod($method) {
    $validMethods = ['cash_on_delivery', 'card_payment'];
    return in_array($method, $validMethods);
}

// Helper function to validate card details
function validateCardDetails($cardNumber, $expiryMonth, $expiryYear, $cvv) {
    // Basic validation - in production, use proper payment gateway validation
    if (empty($cardNumber) || empty($expiryMonth) || empty($expiryYear) || empty($cvv)) {
        return false;
    }
    
    // Check card number length (basic validation)
    if (strlen($cardNumber) < 13 || strlen($cardNumber) > 19) {
        return false;
    }
    
    // Check CVV length
    if (strlen($cvv) < 3 || strlen($cvv) > 4) {
        return false;
    }
    
    // Check expiry date
    $currentYear = date('Y');
    $currentMonth = date('m');
    
    if ($expiryYear < $currentYear || ($expiryYear == $currentYear && $expiryMonth < $currentMonth)) {
        return false;
    }
    
    return true;
}

// Auto-load classes (if using classes)
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
?> 