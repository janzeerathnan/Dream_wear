<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

$product = new Product();
$featuredProducts = $product->getFeatured();
$categories = $product->getCategories();
$sportTypes = $product->getSportTypes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Wear - Premium Jersey Design & Sales</title>
    <meta name="description" content="Design and buy custom sports jerseys for all your favorite teams and players. Premium quality, authentic designs, and fast shipping.">
    
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
    
    <!-- Custom CSS -->
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #1e40af 0%, #dc2626 100%);
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .chat-bubble {
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-10px); }
            60% { transform: translateY(-5px); }
        }
        .hero-slide {
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        .hero-slide.active {
            opacity: 1;
        }
        .category-nav-item {
            transition: all 0.3s ease;
        }
        .category-nav-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.3);
        }
    </style>
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
                        <div class="relative" id="user-dropdown">
                            <button id="user-dropdown-btn" class="flex items-center space-x-2 text-gray-700 hover:text-primary transition-colors">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
                                <i class="fas fa-chevron-down text-sm"></i>
                            </button>
                            <div id="user-dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                                <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a>
                                <a href="orders.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                                <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="text-gray-700 hover:text-primary transition-colors">Login</a>
                        <a href="register.php" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section with Dynamic Images -->
    <section class="relative h-96 md:h-[600px] overflow-hidden">
        <!-- Dynamic Hero Slides -->
        <div class="hero-slide active absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');">
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        </div>
        <div class="hero-slide absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1544966503-7cc5ac882d5f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');">
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        </div>
        <div class="hero-slide absolute inset-0 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');">
            <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        </div>
        
        <!-- Hero Content -->
        <div class="relative z-10 flex items-center justify-center h-full">
            <div class="text-center text-white px-4">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Where Dreams Meet <span class="text-yellow-300">Style</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                    Design and buy custom sports jerseys for all your favorite teams and players. 
                    Premium quality, authentic designs, and fast shipping.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="products.php" class="bg-white text-primary px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        Shop Now
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Hero Navigation Dots -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3">
            <button class="hero-dot w-3 h-3 bg-white rounded-full opacity-100" data-slide="0"></button>
            <button class="hero-dot w-3 h-3 bg-white rounded-full opacity-50" data-slide="1"></button>
            <button class="hero-dot w-3 h-3 bg-white rounded-full opacity-50" data-slide="2"></button>
        </div>
    </section>

    <!-- Categories Navigation Section -->
    <section class="py-12 bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-8">Shop by Category</h2>
            <div class="flex flex-wrap justify-center gap-4 md:gap-6">
                <?php foreach ($categories as $category): ?>
                <a href="products.php?category=<?php echo urlencode($category['category']); ?>" 
                   class="category-nav-item flex items-center space-x-3 bg-gray-50 hover:bg-primary hover:text-white px-6 py-3 rounded-full font-medium transition-all duration-300">
                    <i class="fas fa-tshirt text-lg"></i>
                    <span><?php echo htmlspecialchars($category['category']); ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Featured Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card bg-white rounded-lg shadow-md overflow-hidden transition-all duration-300">
                    <div class="relative">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="w-full h-64 object-cover">
                        <?php if ($product['sale_price']): ?>
                        <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-semibold">
                            SALE
                        </div>
                        <?php endif; ?>
                        <?php if ($product['stock_quantity'] <= 5 && $product['stock_quantity'] > 0): ?>
                        <div class="absolute top-2 left-2 bg-yellow-500 text-white px-2 py-1 rounded text-sm font-semibold">
                            LOW STOCK
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <?php if (!empty($product['team_name'])): ?>
                        <p class="text-gray-600 text-sm mb-2"><?php echo htmlspecialchars($product['team_name']); ?></p>
                        <?php endif; ?>
                        <p class="text-gray-500 text-xs mb-3"><?php echo htmlspecialchars($product['sport_type'] ?? ''); ?></p>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <?php if ($product['sale_price']): ?>
                                <span class="text-lg font-bold text-red-500">$<?php echo number_format($product['sale_price'], 2); ?></span>
                                <span class="text-gray-400 line-through text-sm">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php else: ?>
                                <span class="text-lg font-bold text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php endif; ?>
                            </div>
                            <a href="product.php?id=<?php echo $product['id']; ?>" 
                               class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                                View Details
                            </a>
                        </div>
                        <div class="mt-3 text-xs text-gray-500">
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <span class="text-green-600">✓ In Stock (<?php echo $product['stock_quantity']; ?>)</span>
                            <?php else: ?>
                                <span class="text-red-600">✗ Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-8">
                <a href="products.php" class="bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    View All Products
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center mb-12">Why Choose Dream Wear?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="bg-primary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-medal text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Premium Quality</h3>
                    <p class="text-gray-600">Authentic materials and professional craftsmanship for the best jerseys.</p>
                </div>
                <div class="text-center">
                    <div class="bg-secondary text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shipping-fast text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Fast Shipping</h3>
                    <p class="text-gray-600">Quick delivery to get your jersey to you as soon as possible.</p>
                </div>
                <div class="text-center">
                    <div class="bg-accent text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-headset text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">24/7 Support</h3>
                    <p class="text-gray-600">Our AI chatbot and customer service team are always here to help.</p>
                </div>
            </div>
        </div>
    </section>



    <?php include 'includes/footer.php'; ?>

    <?php include 'includes/chat-widget.php'; ?>

    <!-- JavaScript -->
    <script>
        // Hero Image Slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.hero-dot');
        const totalSlides = slides.length;

        function showSlide(index) {
            // Hide all slides
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.style.opacity = '0.5');
            
            // Show current slide
            slides[index].classList.add('active');
            dots[index].style.opacity = '1';
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            showSlide(currentSlide);
        }

        // Auto-advance slides every 5 seconds
        setInterval(nextSlide, 5000);

        // Manual navigation with dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });

        // User Dropdown Functionality
        const userDropdownBtn = document.getElementById('user-dropdown-btn');
        const userDropdownMenu = document.getElementById('user-dropdown-menu');
        
        if (userDropdownBtn && userDropdownMenu) {
            userDropdownBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdownMenu.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!userDropdownBtn.contains(e.target) && !userDropdownMenu.contains(e.target)) {
                    userDropdownMenu.classList.add('hidden');
                }
            });
            
            // Close dropdown when pressing Escape
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    userDropdownMenu.classList.add('hidden');
                }
            });
        }



        // Cart Counter Update (placeholder)
        function updateCartCounter() {
            // This would be updated with actual cart data
            const counter = document.querySelector('.fa-shopping-cart + span');
            // counter.textContent = cartItemCount;
        }

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