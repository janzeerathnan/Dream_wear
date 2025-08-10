<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Returns & Exchanges - Dream Wear</title>
    
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
                    <a href="orders.php" class="relative text-gray-700 hover:text-primary transition-colors">
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
        

        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Returns & Exchanges</h1>
            <p class="text-xl text-gray-600">
                We want you to be completely satisfied with your purchase. Learn about our return policy and how to initiate a return.
            </p>
        </div>

        <!-- Return Policy Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Return Window -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">30-Day Return Window</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Return period:</span>
                        <span class="font-semibold">30 days</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">From date of:</span>
                        <span class="font-semibold">Delivery</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Condition required:</span>
                        <span class="font-semibold">Unworn</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Packaging:</span>
                        <span class="font-semibold">Original</span>
                    </div>
                </div>
            </div>

            <!-- Return Process -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-undo text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Easy Return Process</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="bg-secondary text-white w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">1</div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Contact Support</h4>
                            <p class="text-gray-600 text-sm">Reach out to our customer service team to initiate your return.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="bg-secondary text-white w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">2</div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Get Return Label</h4>
                            <p class="text-gray-600 text-sm">We'll provide you with a prepaid shipping label.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="bg-secondary text-white w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">3</div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Ship Back</h4>
                            <p class="text-gray-600 text-sm">Package your item and drop it off at any shipping location.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <div class="bg-secondary text-white w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-sm font-bold">4</div>
                        <div>
                            <h4 class="font-semibold text-gray-900">Get Refund</h4>
                            <p class="text-gray-600 text-sm">Receive your refund within 5-7 business days.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exchange Options -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="bg-accent text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exchange-alt text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Exchange Options</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Size Exchange</h4>
                        <p class="text-gray-600 text-sm">Exchange for a different size within 30 days.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Style Exchange</h4>
                        <p class="text-gray-600 text-sm">Exchange for a different style or color.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Team Exchange</h4>
                        <p class="text-gray-600 text-sm">Exchange for a different team jersey.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Player Exchange</h4>
                        <p class="text-gray-600 text-sm">Exchange for a different player jersey.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Return Policy Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- What Can Be Returned -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">What Can Be Returned</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Unworn Items</h4>
                            <p class="text-gray-600 text-sm">Items that have not been worn, washed, or altered.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Original Packaging</h4>
                            <p class="text-gray-600 text-sm">Items with all original tags, labels, and packaging intact.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Within 30 Days</h4>
                            <p class="text-gray-600 text-sm">Returns must be initiated within 30 days of delivery.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-check text-green-500 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Defective Items</h4>
                            <p class="text-gray-600 text-sm">Items with manufacturing defects or quality issues.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- What Cannot Be Returned -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">What Cannot Be Returned</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-times text-red-500 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Worn Items</h4>
                            <p class="text-gray-600 text-sm">Items that have been worn, washed, or show signs of use.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-times text-red-500 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Missing Tags</h4>
                            <p class="text-gray-600 text-sm">Items without original tags, labels, or packaging.</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-times text-red-500 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Custom Items</h4>
                            <p class="text-gray-600 text-sm">Personalized or custom-made jerseys (unless defective).</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-times text-red-500 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Sale Items</h4>
                            <p class="text-gray-600 text-sm">Final sale items marked as non-returnable.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Refund Information -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-12">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Refund Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-credit-card text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold mb-2">Refund Method</h4>
                    <p class="text-gray-600 text-sm">Refunds are processed to the original payment method used for the purchase.</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold mb-2">Processing Time</h4>
                    <p class="text-gray-600 text-sm">Refunds are processed within 5-7 business days after we receive your return.</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-accent text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shipping-fast text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold mb-2">Shipping Costs</h4>
                    <p class="text-gray-600 text-sm">Return shipping is free for defective items. Standard returns may incur shipping fees.</p>
                </div>
            </div>
        </div>

        <!-- Contact Support -->
        
    </div>

    <?php include 'includes/chat-widget.php'; ?>
    <?php include 'includes/footer.php'; ?>
</body>
</html> 