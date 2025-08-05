<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = sanitizeInput($_POST['first_name'] ?? '');
    $last_name = sanitizeInput($_POST['last_name'] ?? '');
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } elseif (empty($first_name) || empty($last_name) || empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!validateEmail($email)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
        $error = 'Username must be 3-20 characters and contain only letters, numbers, and underscores.';
    } else {
        $user = new User();
        
        // Check if email already exists
        if ($user->findByEmail($email)) {
            $error = 'An account with this email already exists.';
        } elseif ($user->findByUsername($username)) {
            $error = 'This username is already taken.';
        } else {
            try {
                $userData = [
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'phone' => $phone,
                    'role' => 'customer',
                    'is_active' => 1
                ];

                $userId = $user->create($userData);

                if ($userId) {
                    $success = 'Account created successfully! You can now log in.';
                    
                    // Log activity
                    logActivity($userId, 'registration', 'New user registered');
                    
                    // Clear form data
                    $first_name = $last_name = $username = $email = $phone = '';
                } else {
                    $error = 'Failed to create account. Please try again.';
                }
            } catch (Exception $e) {
                $error = 'An error occurred. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Dream Wear</title>
    
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
<body class="bg-gray-50 min-h-screen flex flex-col">
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
                    <a href="contact.php" class="text-primary font-semibold">Contact</a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <a href="cart.php" class="relative text-gray-700 hover:text-primary transition-colors">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-primary transition-colors">
                            <i class="fas fa-user-circle text-xl"></i>
                            <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                            <a href="orders.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                            <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="flex items-center space-x-4">
                        <a href="login.php" class="text-gray-700 hover:text-primary transition-colors">Login</a>
                        <a href="register.php" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Register</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="flex-1">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Create your account</h2>
        </div>

        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline"><?php echo htmlspecialchars($success); ?></span>
            <div class="mt-2">
                <a href="login.php" class="font-medium underline">Click here to log in</a>
            </div>
        </div>
        <?php endif; ?>

        <div class="bg-white shadow-lg rounded-lg p-8">
            <form method="POST" action="register.php" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <!-- Name Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">
                            First Name *
                        </label>
                        <input id="first_name" name="first_name" type="text" required 
                               value="<?php echo htmlspecialchars($first_name ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                               placeholder="Enter your first name">
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">
                            Last Name *
                        </label>
                        <input id="last_name" name="last_name" type="text" required 
                               value="<?php echo htmlspecialchars($last_name ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                               placeholder="Enter your last name">
                    </div>
                </div>

                <!-- Username and Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">
                            Username *
                        </label>
                        <input id="username" name="username" type="text" required 
                               value="<?php echo htmlspecialchars($username ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                               placeholder="Choose a username">
                        <p class="mt-1 text-xs text-gray-500">3-20 characters, letters, numbers, and underscores only</p>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email Address *
                        </label>
                        <input id="email" name="email" type="email" required 
                               value="<?php echo htmlspecialchars($email ?? ''); ?>"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                               placeholder="Enter your email">
                    </div>
                </div>

                <!-- Password Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password *
                        </label>
                        <input id="password" name="password" type="password" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                               placeholder="Create a password">
                        <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">
                            Confirm Password *
                        </label>
                        <input id="confirm_password" name="confirm_password" type="password" required 
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                               placeholder="Confirm your password">
                    </div>
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">
                        Phone Number
                    </label>
                    <input id="phone" name="phone" type="tel" 
                           value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm"
                           placeholder="Enter your phone number (optional)">
                </div>

                <!-- Terms and Conditions -->
                <div class="flex items-center">
                    <input id="terms" name="terms" type="checkbox" required 
                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="terms" class="ml-2 block text-sm text-gray-900">
                        I agree to the 
                        <a href="terms.php" class="text-primary hover:text-blue-700">Terms of Service</a>
                        and 
                        <a href="privacy.php" class="text-primary hover:text-blue-700">Privacy Policy</a>
                    </label>
                </div>

                <!-- Newsletter Subscription -->
                <div class="flex items-center">
                    <input id="newsletter" name="newsletter" type="checkbox" 
                           class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                    <label for="newsletter" class="ml-2 block text-sm text-gray-900">
                        Subscribe to our newsletter for exclusive offers and updates
                    </label>
                </div>

                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        Create Account
                    </button>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account? 
                        <a href="login.php" class="font-medium text-primary hover:text-blue-700">
                            Sign in here
                        </a>
                    </p>
                </div>
            </form>
        </div>

        
    </div>
    </div>

 

    <script>
        // Form validation
        const form = document.querySelector('form');
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');

        // Password strength indicator
        function checkPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            return strength;
        }

        passwordInput.addEventListener('input', function() {
            const strength = checkPasswordStrength(this.value);
            const strengthBar = document.getElementById('password-strength');
            if (!strengthBar) {
                const bar = document.createElement('div');
                bar.id = 'password-strength';
                bar.className = 'mt-1 h-2 bg-gray-200 rounded';
                this.parentNode.appendChild(bar);
            }
            
            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-blue-500', 'bg-green-500'];
            const width = (strength / 5) * 100;
            
            strengthBar.innerHTML = `<div class="h-full rounded transition-all duration-300 ${colors[strength-1] || 'bg-gray-200'}" style="width: ${width}%"></div>`;
        });

        // Confirm password validation
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
            }
        });

        // Username validation
        usernameInput.addEventListener('input', function() {
            const username = this.value;
            if (username.length < 3 || username.length > 20 || !/^[a-zA-Z0-9_]+$/.test(username)) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
            }
        });

        // Email validation
        emailInput.addEventListener('blur', function() {
            if (this.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
                this.classList.add('border-red-500');
            } else {
                this.classList.remove('border-red-500');
            }
        });

        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Check required fields
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('border-red-500');
                    isValid = false;
                } else {
                    field.classList.remove('border-red-500');
                }
            });
            
            // Check password match
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.classList.add('border-red-500');
                isValid = false;
            }
            
            // Check password length
            if (passwordInput.value.length < 8) {
                passwordInput.classList.add('border-red-500');
                isValid = false;
            }
            
            // Check username format
            if (!/^[a-zA-Z0-9_]{3,20}$/.test(usernameInput.value)) {
                usernameInput.classList.add('border-red-500');
                isValid = false;
            }
            
            // Check email format
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
                emailInput.classList.add('border-red-500');
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please correct the errors before submitting.');
            }
        });
    </script>
</body>
</html> 