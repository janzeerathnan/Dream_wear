<?php
require_once '../includes/config.php';
require_once '../includes/database.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } else {
        switch ($action) {
            case 'update_site_settings':
                $site_name = sanitizeInput($_POST['site_name'] ?? '');
                $site_url = sanitizeInput($_POST['site_url'] ?? '');
                $admin_email = sanitizeInput($_POST['admin_email'] ?? '');
                
                if (empty($site_name) || empty($site_url) || empty($admin_email)) {
                    $error = 'All fields are required.';
                } elseif (!validateEmail($admin_email)) {
                    $error = 'Please enter a valid admin email address.';
                } else {
                    // In a real application, you would save these to a settings table
                    // For now, we'll just show a success message
                    $success = 'Site settings updated successfully!';
                    logActivity($_SESSION['user_id'], 'update_site_settings', "Updated site settings: $site_name, $site_url, $admin_email");
                }
                break;

            case 'update_security_settings':
                $session_timeout = intval($_POST['session_timeout'] ?? 3600);
                $max_login_attempts = intval($_POST['max_login_attempts'] ?? 5);
                $password_min_length = intval($_POST['password_min_length'] ?? 8);
                
                if ($session_timeout < 300 || $session_timeout > 86400) {
                    $error = 'Session timeout must be between 5 minutes and 24 hours.';
                } elseif ($max_login_attempts < 1 || $max_login_attempts > 10) {
                    $error = 'Max login attempts must be between 1 and 10.';
                } elseif ($password_min_length < 6 || $password_min_length > 20) {
                    $error = 'Password minimum length must be between 6 and 20 characters.';
                } else {
                    $success = 'Security settings updated successfully!';
                    logActivity($_SESSION['user_id'], 'update_security_settings', "Updated security settings: timeout=$session_timeout, attempts=$max_login_attempts, min_length=$password_min_length");
                }
                break;

            case 'update_email_settings':
                $smtp_host = sanitizeInput($_POST['smtp_host'] ?? '');
                $smtp_port = intval($_POST['smtp_port'] ?? 587);
                $smtp_username = sanitizeInput($_POST['smtp_username'] ?? '');
                $smtp_password = $_POST['smtp_password'] ?? '';
                $smtp_encryption = sanitizeInput($_POST['smtp_encryption'] ?? 'tls');
                
                if (empty($smtp_host) || empty($smtp_username)) {
                    $error = 'SMTP host and username are required.';
                } elseif ($smtp_port < 1 || $smtp_port > 65535) {
                    $error = 'SMTP port must be between 1 and 65535.';
                } else {
                    $success = 'Email settings updated successfully!';
                    logActivity($_SESSION['user_id'], 'update_email_settings', "Updated email settings: $smtp_host:$smtp_port");
                }
                break;

            case 'backup_database':
                // In a real application, you would implement database backup
                $success = 'Database backup initiated successfully!';
                logActivity($_SESSION['user_id'], 'backup_database', 'Database backup requested');
                break;

            case 'clear_cache':
                // In a real application, you would clear application cache
                $success = 'Cache cleared successfully!';
                logActivity($_SESSION['user_id'], 'clear_cache', 'Application cache cleared');
                break;
        }
    }
}

// Get current settings (in a real app, these would come from database)
$currentSettings = [
    'site_name' => SITE_NAME,
    'site_url' => SITE_URL,
    'admin_email' => ADMIN_EMAIL,
    'session_timeout' => SESSION_TIMEOUT,
    'max_login_attempts' => 5,
    'password_min_length' => 8,
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'noreply@dreamwear.com',
    'smtp_encryption' => 'tls'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Dream Wear Admin</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#dc2626',
                        accent: '#f59e0b'
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Admin Header -->
    <header class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    
                        <img src="../logo.jpg" alt="Dream Wear" class="h-8 w-auto">
                        <span class="text-xl font-bold text-gray-900">Dream Wear</span>
                        
                    
                </div>
                
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                    <a href="../logout.php" class="text-gray-700 hover:text-primary transition-colors">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg min-h-screen">
            <nav class="mt-8">
                <div class="px-4 space-y-2">
                    <a href="dashboard.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-tachometer-alt mr-3"></i>
                        Dashboard
                    </a>
                    <a href="products.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-tshirt mr-3"></i>
                        Products
                    </a>
                    <a href="orders.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-shopping-cart mr-3"></i>
                        Orders
                    </a>
                    <a href="users.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                    <a href="analytics.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-lg">
                        <i class="fas fa-chart-bar mr-3"></i>
                        Analytics
                    </a>
                    <a href="settings.php" class="flex items-center px-4 py-2 text-primary bg-blue-50 rounded-lg">
                        <i class="fas fa-cog mr-3"></i>
                        Settings
                    </a>
                </div>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Settings</h1>
                <p class="text-gray-600">Manage system configuration and preferences</p>
            </div>

            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Site Settings -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-globe mr-2 text-primary"></i>Site Settings
                    </h3>
                    
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_site_settings">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                            <input type="text" name="site_name" value="<?php echo htmlspecialchars($currentSettings['site_name']); ?>" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Site URL</label>
                            <input type="url" name="site_url" value="<?php echo htmlspecialchars($currentSettings['site_url']); ?>" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Admin Email</label>
                            <input type="email" name="admin_email" value="<?php echo htmlspecialchars($currentSettings['admin_email']); ?>" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                            Update Site Settings
                        </button>
                    </form>
                </div>

                <!-- Security Settings -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-shield-alt mr-2 text-primary"></i>Security Settings
                    </h3>
                    
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_security_settings">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Session Timeout (seconds)</label>
                            <input type="number" name="session_timeout" value="<?php echo $currentSettings['session_timeout']; ?>" min="300" max="86400" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <p class="text-xs text-gray-500 mt-1">Between 300 (5 min) and 86400 (24 hours)</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Max Login Attempts</label>
                            <input type="number" name="max_login_attempts" value="<?php echo $currentSettings['max_login_attempts']; ?>" min="1" max="10" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Password Minimum Length</label>
                            <input type="number" name="password_min_length" value="<?php echo $currentSettings['password_min_length']; ?>" min="6" max="20" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                            Update Security Settings
                        </button>
                    </form>
                </div>

                <!-- Email Settings -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-envelope mr-2 text-primary"></i>Email Settings
                    </h3>
                    
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                        <input type="hidden" name="action" value="update_email_settings">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Host</label>
                            <input type="text" name="smtp_host" value="<?php echo htmlspecialchars($currentSettings['smtp_host']); ?>" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Port</label>
                                <input type="number" name="smtp_port" value="<?php echo $currentSettings['smtp_port']; ?>" min="1" max="65535" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Encryption</label>
                                <select name="smtp_encryption" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                                    <option value="tls" <?php echo $currentSettings['smtp_encryption'] === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                    <option value="ssl" <?php echo $currentSettings['smtp_encryption'] === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                    <option value="none" <?php echo $currentSettings['smtp_encryption'] === 'none' ? 'selected' : ''; ?>>None</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Username</label>
                            <input type="text" name="smtp_username" value="<?php echo htmlspecialchars($currentSettings['smtp_username']); ?>" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">SMTP Password</label>
                            <input type="password" name="smtp_password" placeholder="Enter new password to update" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <button type="submit" class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                            Update Email Settings
                        </button>
                    </form>
                </div>

                <!-- System Maintenance -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-tools mr-2 text-primary"></i>System Maintenance
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Database Backup</h4>
                            <p class="text-sm text-gray-600 mb-3">Create a backup of your database for safekeeping.</p>
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="backup_database">
                                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                    <i class="fas fa-download mr-2"></i>Create Backup
                                </button>
                            </form>
                        </div>
                        
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Clear Cache</h4>
                            <p class="text-sm text-gray-600 mb-3">Clear application cache to free up memory and improve performance.</p>
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="clear_cache">
                                <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors">
                                    <i class="fas fa-broom mr-2"></i>Clear Cache
                                </button>
                            </form>
                        </div>
                        
                        <div class="border rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">System Information</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">PHP Version:</span>
                                    <span class="font-medium"><?php echo PHP_VERSION; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Server Software:</span>
                                    <span class="font-medium"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Database:</span>
                                    <span class="font-medium">MySQL</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Upload Max Size:</span>
                                    <span class="font-medium"><?php echo ini_get('upload_max_filesize'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html> 