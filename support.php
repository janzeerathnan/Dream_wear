<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - Dream Wear</title>
    
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
                <li class="text-gray-900">Support</li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Support Center</h1>
            <p class="text-xl text-gray-600">
                We're here to help! Find answers to your questions and get the support you need.
            </p>
        </div>

        <!-- Support Options -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-12">
            <!-- Live Chat -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Live Chat</h3>
                <p class="text-gray-600 text-sm mb-4">Chat with our AI assistant for instant help with common questions.</p>
                <button onclick="openChat()" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Start Chat
                </button>
            </div>

            <!-- Email Support -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Email Support</h3>
                <p class="text-gray-600 text-sm mb-4">Send us a detailed message and get a response within 24 hours.</p>
                <a href="contact.php" class="bg-secondary text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    Send Email
                </a>
            </div>

            <!-- Phone Support -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="bg-accent text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Phone Support</h3>
                <p class="text-gray-600 text-sm mb-4">Call us directly for immediate assistance with urgent issues.</p>
                <a href="tel:+15551234567" class="bg-accent text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                    Call Now
                </a>
            </div>

            <!-- FAQ -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="bg-green-500 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-question-circle text-xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">FAQ</h3>
                <p class="text-gray-600 text-sm mb-4">Find quick answers to frequently asked questions.</p>
                <a href="faq.php" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition-colors">
                    View FAQ
                </a>
            </div>
        </div>

        <!-- Support Categories -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <!-- Order Support -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-shopping-cart text-primary mr-3"></i>
                    Order Support
                </h3>
                
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Track Your Order</h4>
                        <p class="text-gray-600 text-sm">Get real-time updates on your order status and delivery.</p>
                        <a href="shipping.php" class="text-primary hover:underline text-sm">Track Order →</a>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Order Changes</h4>
                        <p class="text-gray-600 text-sm">Modify or cancel your order before it ships.</p>
                        <a href="contact.php" class="text-primary hover:underline text-sm">Contact Support →</a>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Payment Issues</h4>
                        <p class="text-gray-600 text-sm">Resolve payment problems and billing questions.</p>
                        <a href="contact.php" class="text-primary hover:underline text-sm">Get Help →</a>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Missing Items</h4>
                        <p class="text-gray-600 text-sm">Report missing or incorrect items in your order.</p>
                        <a href="contact.php" class="text-primary hover:underline text-sm">Report Issue →</a>
                    </div>
                </div>
            </div>

            <!-- Product Support -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-tshirt text-primary mr-3"></i>
                    Product Support
                </h3>
                
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Size & Fit</h4>
                        <p class="text-gray-600 text-sm">Get help finding the right size and fit for your jersey.</p>
                        <a href="size-guide.php" class="text-primary hover:underline text-sm">Size Guide →</a>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Product Quality</h4>
                        <p class="text-gray-600 text-sm">Report quality issues or manufacturing defects.</p>
                        <a href="contact.php" class="text-primary hover:underline text-sm">Report Issue →</a>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Care Instructions</h4>
                        <p class="text-gray-600 text-sm">Learn how to properly care for your jerseys.</p>
                        <a href="faq.php" class="text-primary hover:underline text-sm">Care Guide →</a>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Custom Orders</h4>
                        <p class="text-gray-600 text-sm">Get help with custom jersey orders and personalization.</p>
                        <a href="contact.php" class="text-primary hover:underline text-sm">Custom Support →</a>
                    </div>
                </div>
            </div>

            <!-- Account Support -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-user-circle text-primary mr-3"></i>
                    Account Support
                </h3>
                
                <div class="space-y-4">
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Password Reset</h4>
                        <p class="text-gray-600 text-sm">Reset your password or recover your account.</p>
                        <a href="login.php" class="text-primary hover:underline text-sm">Reset Password →</a>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Profile Updates</h4>
                        <p class="text-gray-600 text-sm">Update your account information and preferences.</p>
                        <a href="profile.php" class="text-primary hover:underline text-sm">Edit Profile →</a>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Order History</h4>
                        <p class="text-gray-600 text-sm">View your complete order history and details.</p>
                        <a href="orders.php" class="text-primary hover:underline text-sm">View Orders →</a>
                    </div>
                    
                    <div class="border-b border-gray-200 pb-3">
                        <h4 class="font-semibold text-gray-900 mb-1">Account Security</h4>
                        <p class="text-gray-600 text-sm">Secure your account and manage privacy settings.</p>
                        <a href="profile.php" class="text-primary hover:underline text-sm">Security Settings →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Hours & Contact Info -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Business Hours -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Business Hours</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-900">Monday - Friday</span>
                        <span class="text-gray-600">9:00 AM - 6:00 PM EST</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-900">Saturday</span>
                        <span class="text-gray-600">10:00 AM - 4:00 PM EST</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-900">Sunday</span>
                        <span class="text-gray-600">Closed</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-semibold text-gray-900">Holidays</span>
                        <span class="text-gray-600">Limited Hours</span>
                    </div>
                </div>
                
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <h4 class="font-semibold text-gray-900 mb-2">Emergency Support</h4>
                    <p class="text-gray-600 text-sm">For urgent issues outside business hours, email us and we'll respond as soon as possible.</p>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Contact Information</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-phone text-primary mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Phone</h4>
                            <p class="text-gray-600">+1 (555) 123-4567</p>
                            <p class="text-sm text-gray-500">For immediate assistance</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-envelope text-primary mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Email</h4>
                            <p class="text-gray-600">support@dreamwear.com</p>
                            <p class="text-sm text-gray-500">For detailed inquiries</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Address</h4>
                            <p class="text-gray-600">123 Sports Avenue<br>Jersey City, NJ 07302</p>
                            <p class="text-sm text-gray-500">Customer service center</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-clock text-primary mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-gray-900">Response Time</h4>
                            <p class="text-gray-600">Within 24 hours</p>
                            <p class="text-sm text-gray-500">For email inquiries</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Resources -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Additional Resources</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold mb-2">Help Center</h4>
                    <p class="text-gray-600 text-sm mb-4">Comprehensive guides and tutorials for all our services.</p>
                    <a href="faq.php" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Visit Help Center
                    </a>
                </div>
                
                <div class="text-center">
                    <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shipping-fast text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold mb-2">Shipping Info</h4>
                    <p class="text-gray-600 text-sm mb-4">Learn about our shipping options, costs, and delivery times.</p>
                    <a href="shipping.php" class="bg-secondary text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        Shipping Details
                    </a>
                </div>
                
                <div class="text-center">
                    <div class="bg-accent text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-undo text-xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold mb-2">Returns</h4>
                    <p class="text-gray-600 text-sm mb-4">Understand our return policy and how to initiate returns.</p>
                    <a href="returns.php" class="bg-accent text-white px-6 py-2 rounded-lg hover:bg-yellow-600 transition-colors">
                        Return Policy
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/chat-widget.php'; ?>
    <?php include 'includes/footer.php'; ?>

    <script>
        function openChat() {
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