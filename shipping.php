<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipping Information - Dream Wear</title>
    
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="index.php" class="hover:text-primary">Home</a></li>
                <li><i class="fas fa-chevron-right text-xs"></i></li>
                <li class="text-gray-900">Shipping Information</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Shipping Information</h1>
            <p class="text-xl text-gray-600">
                Fast, reliable shipping to get your jerseys to you as quickly as possible.
            </p>
        </div>

        <!-- Shipping Methods -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Standard Shipping -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Standard Shipping</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Cost:</span>
                        <span class="font-semibold">$5.99</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Free on orders over:</span>
                        <span class="font-semibold">$50</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Delivery time:</span>
                        <span class="font-semibold">3-5 business days</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Tracking:</span>
                        <span class="font-semibold text-green-600">Included</span>
                    </div>
                </div>
            </div>

            <!-- Express Shipping -->
            <div class="bg-white rounded-lg shadow-md p-6 border-2 border-primary">
                <div class="text-center mb-6">
                    <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shipping-fast text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Express Shipping</h2>
                    <span class="bg-secondary text-white px-3 py-1 rounded-full text-sm font-semibold">Most Popular</span>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Cost:</span>
                        <span class="font-semibold">$12.99</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Free on orders over:</span>
                        <span class="font-semibold">$100</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Delivery time:</span>
                        <span class="font-semibold">1-2 business days</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Tracking:</span>
                        <span class="font-semibold text-green-600">Included</span>
                    </div>
                </div>
            </div>

            <!-- Overnight Shipping -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="bg-accent text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-rocket text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Overnight Shipping</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Cost:</span>
                        <span class="font-semibold">$24.99</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Free on orders over:</span>
                        <span class="font-semibold">$200</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Delivery time:</span>
                        <span class="font-semibold">Next business day</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Tracking:</span>
                        <span class="font-semibold text-green-600">Included</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Processing & Delivery -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Processing & Delivery</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="bg-primary text-white w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold">1</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Order Processing</h4>
                            <p class="text-gray-600 text-sm">Orders are processed within 24 hours of placement (excluding weekends and holidays).</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="bg-primary text-white w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold">2</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Shipping Notification</h4>
                            <p class="text-gray-600 text-sm">You'll receive an email with tracking information once your order ships.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="bg-primary text-white w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold">3</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Delivery</h4>
                            <p class="text-gray-600 text-sm">Your package will be delivered to your specified address during business hours.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping Policies -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Shipping Policies</h3>
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Shipping Destinations</h4>
                        <p class="text-gray-600 text-sm">We currently ship to all 50 US states and Canada. International shipping available for select items.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Holiday Shipping</h4>
                        <p class="text-gray-600 text-sm">Processing times may be extended during holidays. Check our holiday schedule for specific dates.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Signature Required</h4>
                        <p class="text-gray-600 text-sm">Orders over $200 require signature upon delivery for security purposes.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Address Accuracy</h4>
                        <p class="text-gray-600 text-sm">Please ensure your shipping address is correct. We're not responsible for packages sent to incorrect addresses.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tracking & Support -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Track Your Order</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="text-center">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-search text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold mb-2">Track by Order Number</h4>
                    <p class="text-gray-600 text-sm mb-4">Enter your order number to get real-time tracking updates.</p>
                    <form class="flex space-x-2">
                        <input type="text" placeholder="Enter order number" 
                               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Track
                        </button>
                    </form>
                </div>
                
                <div class="text-center">
                    <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold mb-2">Need Help?</h4>
                    <p class="text-gray-600 text-sm mb-4">Contact our shipping support team for assistance with your order.</p>
                    <div class="space-y-2">
                        <a href="contact.php" class="block bg-secondary text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            Contact Support
                        </a>
                        <a href="tel:+15551234567" class="block bg-gray-200 text-gray-800 px-6 py-2 rounded-lg hover:bg-gray-300 transition-colors">
                            Call: +1 (555) 123-4567
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html> 