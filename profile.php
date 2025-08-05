<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user = new User();
$userData = $user->findById($_SESSION['user_id']);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } else {
        switch ($action) {
            case 'update_profile':
                $first_name = sanitizeInput($_POST['first_name'] ?? '');
                $last_name = sanitizeInput($_POST['last_name'] ?? '');
                $phone = sanitizeInput($_POST['phone'] ?? '');
                $address = sanitizeInput($_POST['address'] ?? '');
                $city = sanitizeInput($_POST['city'] ?? '');
                $state = sanitizeInput($_POST['state'] ?? '');
                $zip_code = sanitizeInput($_POST['zip_code'] ?? '');
                $country = sanitizeInput($_POST['country'] ?? 'USA');

                if (empty($first_name) || empty($last_name)) {
                    $error = 'First name and last name are required.';
                } else {
                    $updateData = [
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'phone' => $phone,
                        'address' => $address,
                        'city' => $city,
                        'state' => $state,
                        'zip_code' => $zip_code,
                        'country' => $country
                    ];

                    if ($user->update($_SESSION['user_id'], $updateData)) {
                        $success = 'Profile updated successfully!';
                        // Update session data
                        $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                        $userData = $user->findById($_SESSION['user_id']);
                    } else {
                        $error = 'Failed to update profile. Please try again.';
                    }
                }
                break;

            case 'change_password':
                $current_password = $_POST['current_password'] ?? '';
                $new_password = $_POST['new_password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';

                if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                    $error = 'All password fields are required.';
                } elseif (!$user->verifyPassword($current_password, $userData['password_hash'])) {
                    $error = 'Current password is incorrect.';
                } elseif (strlen($new_password) < 8) {
                    $error = 'New password must be at least 8 characters long.';
                } elseif ($new_password !== $confirm_password) {
                    $error = 'New passwords do not match.';
                } else {
                    if ($user->update($_SESSION['user_id'], ['password' => $new_password])) {
                        $success = 'Password changed successfully!';
                    } else {
                        $error = 'Failed to change password. Please try again.';
                    }
                }
                break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Dream Wear</title>
    
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
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="index.php" class="flex items-center space-x-2">
                        <img src="logo.jpg" alt="Dream Wear" class="h-8 w-auto">
                        <span class="text-xl font-bold text-gray-900">Dream Wear</span>
                    </a>
                </div>

                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                    <a href="products.php" class="text-gray-700 hover:text-primary transition-colors">Products</a>
                    <a href="about.php" class="text-gray-700 hover:text-primary transition-colors">About</a>
                    <a href="contact.php" class="text-gray-700 hover:text-primary transition-colors">Contact</a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="relative text-gray-700 hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </a>
                    
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-primary font-semibold">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 bg-gray-50">Profile</a>
                            <a href="orders.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">My Profile</h1>
            <p class="text-gray-600">Manage your account information</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['error']); ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

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
            <!-- Profile Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Profile Information</h2>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" id="first_name" name="first_name" required 
                                   value="<?php echo htmlspecialchars($userData['first_name']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" required 
                                   value="<?php echo htmlspecialchars($userData['last_name']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($userData['email']); ?>" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50" disabled>
                        <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <textarea id="address" name="address" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" id="city" name="city" 
                                   value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                            <input type="text" id="state" name="state" 
                                   value="<?php echo htmlspecialchars($userData['state'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-1">ZIP Code</label>
                            <input type="text" id="zip_code" name="zip_code" 
                                   value="<?php echo htmlspecialchars($userData['zip_code'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        </div>
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                        <input type="text" id="country" name="country" 
                               value="<?php echo htmlspecialchars($userData['country'] ?? 'USA'); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <button type="submit" 
                            class="w-full bg-primary text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                        Update Profile
                    </button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Change Password</h2>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="change_password">
                    
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password *</label>
                        <input type="password" id="current_password" name="current_password" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password *</label>
                        <input type="password" id="new_password" name="new_password" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                        <p class="text-xs text-gray-500 mt-1">Minimum 8 characters</p>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password *</label>
                        <input type="password" id="confirm_password" name="confirm_password" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <button type="submit" 
                            class="w-full bg-secondary text-white py-2 px-4 rounded-md hover:bg-red-700 transition-colors">
                        Change Password
                    </button>
                </form>

                <!-- Account Information -->
                <div class="mt-8 pt-6 border-t">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Username:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($userData['username']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Member Since:</span>
                            <span class="font-medium"><?php echo date('M j, Y', strtotime($userData['created_at'])); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Account Status:</span>
                            <span class="font-medium text-green-600">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 