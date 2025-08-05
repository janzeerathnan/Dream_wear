<?php
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found - Dream Wear</title>
    
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

    <!-- Main Content -->
    <div class="flex-1 flex items-center justify-center py-12">
        <div class="max-w-2xl mx-auto text-center px-4">
            <!-- 404 Icon -->
            <div class="mb-8">
                <div class="bg-primary text-white w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-exclamation-triangle text-4xl"></i>
                </div>
            </div>

            <!-- Error Message -->
            <h1 class="text-6xl font-bold text-gray-900 mb-4">404</h1>
            <h2 class="text-3xl font-semibold text-gray-700 mb-4">Page Not Found</h2>
            <p class="text-lg text-gray-600 mb-8">
                Oops! The page you're looking for doesn't exist. It might have been moved, deleted, or you entered the wrong URL.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <a href="index.php" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Go Home
                </a>
                <a href="products.php" class="bg-gray-200 text-gray-800 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                    <i class="fas fa-tshirt mr-2"></i>
                    Browse Products
                </a>
            </div>

            <!-- Search Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Looking for something specific?</h3>
                <form action="products.php" method="GET" class="flex flex-col sm:flex-row gap-2">
                    <input type="text" name="search" placeholder="Search for jerseys, teams, or players..." 
                           class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary">
                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Search
                    </button>
                </form>
            </div>

            <!-- Quick Links -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-blue-100 text-primary w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-tshirt text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Popular Categories</h4>
                    <div class="space-y-1 text-sm text-gray-600">
                        <a href="products.php?category=NFL" class="block hover:text-primary">NFL Jerseys</a>
                        <a href="products.php?category=NBA" class="block hover:text-primary">NBA Jerseys</a>
                        <a href="products.php?category=MLB" class="block hover:text-primary">MLB Jerseys</a>
                    </div>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 text-green-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-headset text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Need Help?</h4>
                    <div class="space-y-1 text-sm text-gray-600">
                        <a href="contact.php" class="block hover:text-primary">Contact Us</a>
                        <a href="faq.php" class="block hover:text-primary">FAQ</a>
                        <a href="size-guide.php" class="block hover:text-primary">Size Guide</a>
                    </div>
                </div>

                <div class="text-center">
                    <div class="bg-yellow-100 text-yellow-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-info-circle text-xl"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">About Dream Wear</h4>
                    <div class="space-y-1 text-sm text-gray-600">
                        <a href="about.php" class="block hover:text-primary">About Us</a>
                        <a href="shipping.php" class="block hover:text-primary">Shipping Info</a>
                        <a href="returns.php" class="block hover:text-primary">Returns</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Auto-focus search input
        document.querySelector('input[name="search"]').focus();
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html> 