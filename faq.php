<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Dream Wear</title>
    
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
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h1>
            <p class="text-xl text-gray-600">
                Find answers to common questions about our products, shipping, returns, and more.
            </p>
        </div>

        <!-- FAQ Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Ordering & Products -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-shopping-cart text-primary mr-3"></i>
                    Ordering & Products
                </h2>
                
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Are your jerseys authentic?</h3>
                        <p class="text-gray-600 text-sm">Yes, all our jerseys are officially licensed and authentic. We work directly with manufacturers to ensure quality and authenticity.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Do you offer custom jerseys?</h3>
                        <p class="text-gray-600 text-sm">Yes! We offer custom jersey services. Contact us for pricing and options for personalized jerseys with your name and number.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">What sizes do you carry?</h3>
                        <p class="text-gray-600 text-sm">We carry sizes S-3XL for most jerseys. Check our <a href="size-guide.php" class="text-primary hover:underline">size guide</a> for detailed measurements.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Do you have youth sizes?</h3>
                        <p class="text-gray-600 text-sm">Yes, we offer youth sizes for many popular teams. Look for "Youth" in the product title.</p>
                    </div>
                </div>
            </div>

            <!-- Shipping & Delivery -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-shipping-fast text-primary mr-3"></i>
                    Shipping & Delivery
                </h2>
                
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">How long does shipping take?</h3>
                        <p class="text-gray-600 text-sm">Most orders ship within 24 hours and arrive in 3-5 business days. Express shipping options are available for faster delivery.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Do you ship internationally?</h3>
                        <p class="text-gray-600 text-sm">Currently, we ship to the United States and Canada. International shipping may be available for select items.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Is shipping free?</h3>
                        <p class="text-gray-600 text-sm">Free shipping is available on orders over $50. Standard shipping costs $5.99 for orders under $50.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">How do I track my order?</h3>
                        <p class="text-gray-600 text-sm">You'll receive a tracking number via email once your order ships. You can also track your order in your account dashboard.</p>
                    </div>
                </div>
            </div>

            <!-- Returns & Support -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-headset text-primary mr-3"></i>
                    Returns & Support
                </h2>
                
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">What is your return policy?</h3>
                        <p class="text-gray-600 text-sm">We offer a 30-day return policy for unworn items in original packaging. Contact our support team to initiate a return.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">How do I contact customer service?</h3>
                        <p class="text-gray-600 text-sm">You can reach us via <a href="contact.php" class="text-primary hover:underline">contact form</a>, email at support@dreamwear.com, or call us at +1 (555) 123-4567.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">What if my jersey doesn't fit?</h3>
                        <p class="text-gray-600 text-sm">If your jersey doesn't fit, you can exchange it for a different size within 30 days, as long as it's unworn and in original packaging.</p>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-4">
                        <h3 class="font-semibold text-gray-900 mb-2">Do you offer gift wrapping?</h3>
                        <p class="text-gray-600 text-sm">Yes! Gift wrapping is available for an additional $3.99. You can select this option during checkout.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Help Section -->
        <div class="mt-12 bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Still Need Help?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Contact Us</h3>
                    <p class="text-gray-600 text-sm mb-4">Send us a message and we'll get back to you within 24 hours.</p>
                    <a href="contact.php" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Contact Support
                    </a>
                </div>
                
                <div class="text-center">
                    <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-phone text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Call Us</h3>
                    <p class="text-gray-600 text-sm mb-4">Speak with our customer service team directly.</p>
                    <a href="tel:+15551234567" class="bg-secondary text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        +1 (555) 123-4567
                    </a>
                </div>
                
                <div class="text-center">
                    <div class="bg-accent text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-comments text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Live Chat</h3>
                    <p class="text-gray-600 text-sm mb-4">Chat with our AI assistant for instant help.</p>
                    <button onclick="openChat()" class="bg-accent text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                        Start Chat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function openChat() {
            // This would open the chat widget
            const chatToggle = document.getElementById('chat-toggle');
            if (chatToggle) {
                chatToggle.click();
            } else {
                alert('Chat feature coming soon! Please contact us via email or phone.');
            }
        }
    </script>
</body>
</html> 