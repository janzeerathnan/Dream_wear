<?php
require_once 'includes/config.php';
require_once 'includes/database.php';

$productId = intval($_GET['id'] ?? 0);
if (!$productId) {
    redirect('products.php');
}

$product = new Product();
$productData = $product->findById($productId);

if (!$productData || !$productData['is_active']) {
    redirect('products.php');
}

// Parse JSON data
$colors = json_decode($productData['colors'] ?? '[]', true) ?: [];
$sizes = json_decode($productData['sizes'] ?? '[]', true) ?: [];
$additionalImages = json_decode($productData['additional_images'] ?? '[]', true) ?: [];

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    $size = sanitizeInput($_POST['size'] ?? '');
    $color = sanitizeInput($_POST['color'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);
    $customization_notes = sanitizeInput($_POST['customization_notes'] ?? '');
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validateCSRFToken($csrf_token)) {
        $error = 'Invalid request. Please try again.';
    } elseif (empty($size) || empty($color)) {
        $error = 'Please select both size and color.';
    } elseif ($quantity < 1 || $quantity > 10) {
        $error = 'Quantity must be between 1 and 10.';
    } else {
        $cart = new Cart();
        $cartData = [
            'user_id' => $_SESSION['user_id'],
            'product_id' => $productId,
            'quantity' => $quantity,
            'size' => $size,
            'color' => $color,
            'customization_notes' => $customization_notes
        ];

        try {
            $cart->addItem($cartData);
            $success = 'Product added to cart successfully!';
        } catch (Exception $e) {
            $error = 'Failed to add product to cart. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($productData['name']); ?> - Dream Wear</title>
    
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
                        <a href="login.php" class="text-gray-700 hover:text-primary transition-colors">Login</a>
                        <a href="register.php" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">

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
            <!-- Product Images -->
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <img src="<?php echo htmlspecialchars($productData['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($productData['name']); ?>"
                         class="w-full h-96 object-cover rounded-lg">
                </div>
                
                <?php if (!empty($additionalImages)): ?>
                <div class="grid grid-cols-4 gap-2">
                    <?php foreach ($additionalImages as $image): ?>
                    <div class="bg-white rounded-lg shadow-md p-2">
                        <img src="<?php echo htmlspecialchars($image); ?>" 
                             alt="Product image"
                             class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 transition-opacity">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Product Details -->
            <div class="space-y-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($productData['name']); ?></h1>
                                            <?php if (!empty($productData['team_name'])): ?>
                        <p class="text-lg text-gray-600 mb-4"><?php echo htmlspecialchars($productData['team_name']); ?></p>
                        <?php endif; ?>
                    
                    <div class="flex items-center space-x-4 mb-4">
                        <?php if ($productData['sale_price']): ?>
                        <span class="text-3xl font-bold text-red-500">$<?php echo number_format($productData['sale_price'], 2); ?></span>
                        <span class="text-xl text-gray-400 line-through">$<?php echo number_format($productData['price'], 2); ?></span>
                        <?php else: ?>
                        <span class="text-3xl font-bold text-primary">$<?php echo number_format($productData['price'], 2); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center space-x-4 text-sm text-gray-600">
                        <span class="flex items-center">
                            <i class="fas fa-tshirt mr-2"></i>
                            <?php echo htmlspecialchars($productData['sport_type']); ?>
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-tag mr-2"></i>
                            <?php echo htmlspecialchars($productData['category']); ?>
                        </span>
                        <?php if ($productData['stock_quantity'] > 0): ?>
                        <span class="flex items-center text-green-600">
                            <i class="fas fa-check-circle mr-2"></i>
                            In Stock
                        </span>
                        <?php else: ?>
                        <span class="flex items-center text-red-600">
                            <i class="fas fa-times-circle mr-2"></i>
                            Out of Stock
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-600"><?php echo htmlspecialchars($productData['description']); ?></p>
                </div>

                <?php if (isLoggedIn() && $productData['stock_quantity'] > 0): ?>
                <!-- Add to Cart Form -->
                <form method="POST" action="product.php?id=<?php echo $productId; ?>" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <!-- Size Selection -->
                    <div>
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-2">Size *</label>
                        <select id="size" name="size" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <option value="">Select Size</option>
                            <?php foreach ($sizes as $size): ?>
                            <option value="<?php echo htmlspecialchars($size); ?>"><?php echo htmlspecialchars($size); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Color Selection -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">Color *</label>
                        <select id="color" name="color" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <option value="">Select Color</option>
                            <?php foreach ($colors as $color): ?>
                            <option value="<?php echo htmlspecialchars($color); ?>"><?php echo htmlspecialchars($color); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <div class="flex items-center space-x-2">
                            <button type="button" onclick="updateQuantity(-1)" 
                                    class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-50">
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="10" 
                                   class="w-16 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                            <button type="button" onclick="updateQuantity(1)" 
                                    class="w-8 h-8 border border-gray-300 rounded flex items-center justify-center hover:bg-gray-50">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Customization Notes -->
                    <div>
                        <label for="customization_notes" class="block text-sm font-medium text-gray-700 mb-2">Customization Notes</label>
                        <textarea id="customization_notes" name="customization_notes" rows="3" 
                                  placeholder="Any special requests or customization notes..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full bg-primary text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        <i class="fas fa-cart-plus mr-2"></i>
                        Add to Cart
                    </button>
                </form>
                <?php elseif (!isLoggedIn()): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <p class="text-yellow-800">Please <a href="login.php" class="font-semibold underline">log in</a> to add this item to your cart.</p>
                </div>
                <?php else: ?>
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-800">This product is currently out of stock.</p>
                </div>
                <?php endif; ?>

                <!-- Product Features -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Features</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Authentic team design and colors
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Premium quality fabric
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Official team logos and branding
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Available in multiple sizes
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-2"></i>
                            Fast shipping available
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php 
                $relatedProducts = $product->getAll(['category' => $productData['category']], 4);
                foreach ($relatedProducts as $relatedProduct):
                    if ($relatedProduct['id'] == $productId) continue;
                ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="relative">
                        <img src="<?php echo htmlspecialchars($relatedProduct['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>"
                             class="w-full h-48 object-cover">
                    </div>
                    <div class="p-4">
                        <h3 class="text-lg font-semibold mb-2"><?php echo htmlspecialchars($relatedProduct['name']); ?></h3>
                        <?php if (!empty($relatedProduct['team_name'])): ?>
                        <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars($relatedProduct['team_name']); ?></p>
                        <?php endif; ?>
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-primary">$<?php echo number_format($relatedProduct['price'], 2); ?></span>
                            <a href="product.php?id=<?php echo $relatedProduct['id']; ?>" 
                               class="bg-primary text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                                View
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function updateQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            let currentValue = parseInt(quantityInput.value) || 1;
            let newValue = currentValue + change;
            
            // Ensure value is between 1 and 10
            newValue = Math.max(1, Math.min(10, newValue));
            quantityInput.value = newValue;
        }

        // Form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const size = document.getElementById('size').value;
                const color = document.getElementById('color').value;
                
                if (!size || !color) {
                    e.preventDefault();
                    alert('Please select both size and color.');
                }
            });
        }
    </script>
</body>
</html> 