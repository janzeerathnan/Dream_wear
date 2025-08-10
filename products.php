<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

$product = new Product();

// Get filter parameters
$category = sanitizeInput($_GET['category'] ?? '');
$sport_type = sanitizeInput($_GET['sport_type'] ?? '');
$search = sanitizeInput($_GET['search'] ?? '');
$sort = sanitizeInput($_GET['sort'] ?? 'newest');
$page = max(1, intval($_GET['page'] ?? 1));

// Build filters array
$filters = [];
if ($category) $filters['category'] = $category;
if ($sport_type) $filters['sport_type'] = $sport_type;
if ($search) $filters['search'] = $search;

// Calculate pagination
$offset = ($page - 1) * ITEMS_PER_PAGE;
$products = $product->getAll($filters, ITEMS_PER_PAGE, $offset);
$totalProducts = count($product->getAll($filters)); // This should be optimized in production
$totalPages = ceil($totalProducts / ITEMS_PER_PAGE);

// Get filter options
$categories = $product->getCategories();
$sportTypes = $product->getSportTypes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Products - <?php echo SITE_NAME; ?></title>
    
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
                    <a href="products.php" class="text-primary font-semibold">Products</a>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Our Products</h1>
            <p class="text-gray-600">Discover our collection of premium sports jerseys</p>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" action="products.php" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" id="search" name="search" 
                               value="<?php echo htmlspecialchars($search); ?>"
                               placeholder="Search products..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select id="category" name="category" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['category']); ?>" 
                                    <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['category']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sport Type -->
                    <div>
                        <label for="sport_type" class="block text-sm font-medium text-gray-700 mb-1">Sport</label>
                        <select id="sport_type" name="sport_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <option value="">All Sports</option>
                            <?php foreach ($sportTypes as $sport): ?>
                            <option value="<?php echo htmlspecialchars($sport['sport_type']); ?>" 
                                    <?php echo $sport_type === $sport['sport_type'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($sport['sport_type']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <button type="submit" 
                            class="bg-primary text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Apply Filters
                    </button>
                    
                    <a href="products.php" 
                       class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Results Summary -->
        <div class="flex justify-between items-center mb-6">
            <p class="text-gray-600">
                Showing <?php echo count($products); ?> of <?php echo $totalProducts; ?> products
            </p>
            
            <!-- Sort -->
            <div class="flex items-center space-x-2">
                <label for="sort" class="text-sm font-medium text-gray-700">Sort by:</label>
                <select id="sort" name="sort" 
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"
                        onchange="this.form.submit()">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest</option>
                    <option value="price_low" <?php echo $sort === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="price_high" <?php echo $sort === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Name: A to Z</option>
                </select>
            </div>
        </div>

        <!-- Products Grid -->
        <?php if (empty($products)): ?>
        <div class="text-center py-12">
            <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No products found</h3>
            <p class="text-gray-500">Try adjusting your filters or search terms</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($products as $product): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                <div class="relative">
                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         class="w-full h-64 object-cover">
                    <?php if ($product['sale_price']): ?>
                    <div class="absolute top-2 right-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-semibold">
                        SALE
                    </div>
                    <?php endif; ?>
                    <?php if ($product['is_featured']): ?>
                    <div class="absolute top-2 left-2 bg-yellow-500 text-white px-2 py-1 rounded text-sm font-semibold">
                        FEATURED
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="p-4">
                    <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($product['name']); ?></h3>
                                            <?php if (!empty($product['team_name'])): ?>
                        <p class="text-gray-600 text-sm mb-2"><?php echo htmlspecialchars($product['team_name']); ?></p>
                        <?php endif; ?>
                    <p class="text-gray-500 text-sm mb-3"><?php echo htmlspecialchars($product['sport_type']); ?></p>
                    
                    <div class="flex justify-between items-center mb-3">
                        <div class="flex items-center space-x-2">
                                                    <?php if ($product['sale_price']): ?>
                        <span class="text-lg font-bold text-red-500">Rs. <?php echo number_format($product['sale_price'], 2); ?></span>
                        <span class="text-gray-400 line-through">Rs. <?php echo number_format($product['price'], 2); ?></span>
                        <?php else: ?>
                        <span class="text-lg font-bold text-primary">Rs. <?php echo number_format($product['price'], 2); ?></span>
                        <?php endif; ?>
                        </div>
                        
                        <div class="text-sm text-gray-500">
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <span class="text-green-600">In Stock</span>
                            <?php else: ?>
                                <span class="text-red-600">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="product.php?id=<?php echo $product['id']; ?>" 
                           class="flex-1 bg-primary text-white text-center py-2 rounded hover:bg-blue-700 transition-colors">
                            View Details
                        </a>
                        <?php if ($product['stock_quantity'] > 0): ?>
                        <button onclick="addToCart(<?php echo $product['id']; ?>)" 
                                class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors">
                            <i class="fas fa-cart-plus"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="mt-8 flex justify-center">
            <nav class="flex items-center space-x-2">
                <?php if ($page > 1): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>" 
                   class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Previous
                </a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                   class="px-3 py-2 border border-gray-300 rounded-md <?php echo $i === $page ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-50'; ?> transition-colors">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>" 
                   class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Next
                </a>
                <?php endif; ?>
            </nav>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include 'includes/chat-widget.php'; ?>

    <?php include 'includes/footer.php'; ?>

    <script>
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



        // Add to Cart Functionality
        function addToCart(productId) {
            if (!<?php echo isLoggedIn() ? 'true' : 'false'; ?>) {
                window.location.href = 'login.php';
                return;
            }

            // This would typically make an AJAX call to add the item to cart
            alert('Product added to cart!');
        }

        // Auto-submit sort form
        document.getElementById('sort').addEventListener('change', function() {
            const url = new URL(window.location);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    </script>
</body>
</html> 