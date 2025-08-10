<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Dream Wear</title>
    
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
                        <img src="logo.jpg" alt="<?php echo SITE_NAME; ?>" class="h-8 w-auto">
                    </a>
                </div>

                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="text-gray-700 hover:text-primary transition-colors">Home</a>
                    <a href="products.php" class="text-gray-700 hover:text-primary transition-colors">Products</a>
                    <a href="about.php" class="text-primary font-semibold">About</a>
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
            <h1 class="text-4xl font-bold text-gray-900 mb-4">About <?php echo SITE_NAME; ?></h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                Your premier destination for authentic sports jerseys and custom athletic wear
            </p>
        </div>

        <!-- Mission Section -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <div class="text-center mb-8">
                <i class="fas fa-bullseye text-4xl text-primary mb-4"></i>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Mission</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    At <?php echo SITE_NAME; ?>, we believe every sports fan deserves to wear their passion with pride. 
                    We're dedicated to providing high-quality, authentic jerseys that let you represent your 
                    favorite teams and players with style and confidence.
                </p>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <i class="fas fa-medal text-3xl text-primary mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Premium Quality</h3>
                <p class="text-gray-600">
                    We source only the finest materials and work with licensed manufacturers to ensure 
                    every jersey meets the highest standards of quality and authenticity.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <i class="fas fa-shipping-fast text-3xl text-primary mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Fast Shipping</h3>
                <p class="text-gray-600">
                    Get your jerseys delivered quickly with our reliable shipping service. 
                    Most orders ship within 24 hours and arrive at your door in 3-5 business days.
                </p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <i class="fas fa-headset text-3xl text-primary mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Expert Support</h3>
                <p class="text-gray-600">
                    Our knowledgeable team is here to help you find the perfect jersey. 
                    From sizing questions to customization options, we've got you covered.
                </p>
            </div>
        </div>

        <!-- Sports Coverage -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-8">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-8">Sports We Cover</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <i class="fas fa-football-ball text-3xl text-primary mb-2"></i>
                    <h4 class="font-semibold text-gray-900">NFL</h4>
                    <p class="text-sm text-gray-600">All 32 teams</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-basketball-ball text-3xl text-primary mb-2"></i>
                    <h4 class="font-semibold text-gray-900">NBA</h4>
                    <p class="text-sm text-gray-600">All 30 teams</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-baseball-ball text-3xl text-primary mb-2"></i>
                    <h4 class="font-semibold text-gray-900">MLB</h4>
                    <p class="text-sm text-gray-600">All 30 teams</p>
                </div>
                <div class="text-center">
                    <i class="fas fa-futbol text-3xl text-primary mb-2"></i>
                    <h4 class="font-semibold text-gray-900">Soccer</h4>
                    <p class="text-sm text-gray-600">Major leagues worldwide</p>
                </div>
            </div>
        </div>

        
    </div>
    <?php include 'includes/chat-widget.php'; ?>

    <?php include 'includes/footer.php'; ?>


    
</body>
</html> 